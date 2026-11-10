<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\{ActivityLog, BankDokumen, Kelompok, Pendaftaran, Pesan, User};
use App\Support\Akses;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function form() { return view('admin.login'); }

    public function login(Request $r)
    {
        $cred = $r->validate(['email' => 'required|email', 'password' => 'required']);
        if (Auth::attempt($cred, $r->boolean('remember'))) {
            $r->session()->regenerate();
            ActivityLog::create(['user_id' => Auth::id(), 'aksi' => 'login', 'deskripsi' => Auth::user()->name.' login']);
            return redirect()->intended(route('admin.dashboard'));
        }
        return back()->withErrors(['email' => 'Email atau password salah.'])->onlyInput('email');
    }

    public function logout(Request $r)
    {
        Auth::logout();
        $r->session()->invalidate();
        $r->session()->regenerateToken();
        return redirect()->route('login');
    }

    public function dashboard()
    {
        $u = Auth::user();
        $kartu = collect(Akses::sidebar($u))->flatten(1)->map(function ($m) use ($u) {
            $k = $m['key']; $c = config("crud.$k");
            $n = match (true) {
                $k === 'kelompok' => Kelompok::count(),
                $k === 'simbadok' => BankDokumen::visibleTo($u)->count(),
                $k === 'pesan' => Pesan::where('status', 'baru')->count(),
                $k === 'sipandai_rekap' => Pendaftaran::whereDate('tanggal', now(config('sipandai.timezone'))->toDateString())->count(),
                $k === 'user' => User::count(),
                (bool) $c => $c['model']::count(),
                default => null,
            };
            return $m + ['jumlah' => $n];
        })->all();
        return view('admin.dashboard', ['kartu' => $kartu, 'peran' => Akses::label($u)]);
    }
}
