<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\{ActivityLog, Unit, User};
use App\Support\Akses;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\{Rule, ValidationException};

/** Khusus superadmin: buat akun admin/operator bidang & UPTD beserta menu yang boleh diakses. */
class UserController extends Controller
{
    public function index(Request $r)
    {
        $rows = User::query()
            ->when($r->q, fn ($x, $s) => $x->where(fn ($w) => $w->where('name', 'like', "%$s%")->orWhere('email', 'like', "%$s%")))
            ->when($r->peran, fn ($x, $v) => $x->where('role', $v))
            ->orderByRaw("field(role,'superadmin','admin','operator')")->orderBy('name')->paginate(20)->withQueryString();
        return view('admin.users.index', ['rows' => $rows, 'units' => Unit::pluck('nama', 'id')]);
    }

    public function create()
    {
        $baru = new User(); $baru->role = 'operator';   // 'role' tidak mass-assignable, set langsung
        return view('admin.users.form', ['u' => $baru, 'units' => Unit::orderBy('tipe')->orderBy('urutan')->get(), 'terpilih' => []]);
    }

    public function edit(User $user)
    {
        return view('admin.users.form', ['u' => $user, 'units' => Unit::orderBy('tipe')->orderBy('urutan')->get(), 'terpilih' => Akses::menus($user)]);
    }

    public function store(Request $r)
    {
        $d = $this->data($r, null);
        $u = new User();
        $u->forceFill($d)->save();
        $this->log('tambah', $u);
        return redirect()->route('admin.users.index')->with('ok', __('Pengguna dibuat.'));
    }

    public function update(Request $r, User $user)
    {
        $d = $this->data($r, $user);
        $user->forceFill($d)->save();
        $this->log('ubah', $user);
        return redirect()->route('admin.users.index')->with('ok', __('Pengguna diperbarui.'));
    }

    public function destroy(User $user)
    {
        abort_if($user->id === auth()->id(), 422, __('Tidak bisa menghapus akun sendiri.'));
        abort_if($user->role === 'superadmin' && User::where('role', 'superadmin')->count() <= 1, 422, __('Tidak bisa menghapus superadmin terakhir.'));
        $this->log('hapus', $user);
        $user->delete();
        return back()->with('ok', __('Pengguna dihapus.'));
    }

    private function data(Request $r, ?User $u): array
    {
        $d = $r->validate([
            'name' => 'required|max:100', 'email' => ['required', 'email', 'max:150', Rule::unique('users', 'email')->ignore($u?->id)],
            'role' => ['required', Rule::in(array_keys(config('akses.peran')))],
            'unit_id' => 'nullable|exists:units,id', 'password' => [$u ? 'nullable' : 'required', 'min:10', 'max:100'],
            'menus' => 'nullable|array', 'menus.*' => [Rule::in(array_keys(config('akses.menu')))],
        ], [], ['name' => 'nama', 'unit_id' => 'bidang/UPTD', 'role' => 'peran', 'menus' => 'hak akses menu']);

        if ($d['role'] !== 'superadmin' && empty($d['unit_id'])) {
            throw ValidationException::withMessages(['unit_id' => __('Pilih bidang/UPTD untuk admin dan operator.')]);
        }
        if ($u && $u->role === 'superadmin' && $d['role'] !== 'superadmin') {   // jangan sampai tidak ada superadmin / mengunci diri sendiri
            if ($u->id === auth()->id()) throw ValidationException::withMessages(['role' => __('Anda tidak bisa menurunkan peran akun Anda sendiri.')]);
            if (User::where('role', 'superadmin')->count() <= 1) throw ValidationException::withMessages(['role' => __('Harus ada minimal satu superadmin.')]);
        }
        if ($d['role'] === 'superadmin') { $d['unit_id'] = null; $d['menus'] = []; }
        $d['menus'] = json_encode(array_values(array_unique($d['menus'] ?? [])));
        if (blank($d['password'] ?? null)) unset($d['password']); else $d['password'] = Hash::make($d['password']);
        return $d;
    }

    private function log(string $aksi, User $u): void
    {
        ActivityLog::create(['user_id' => auth()->id(), 'aksi' => $aksi, 'deskripsi' => mb_substr(auth()->user()->name.' - Pengguna '.$u->email.' ('.$u->role.')', 0, 250)]);
    }
}
