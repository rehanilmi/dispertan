<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Support\{Akses, Html, Img};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\{Hash, Storage};

class CrudController extends Controller
{
    private function cfg(string $res): array
    {
        $c = config("crud.$res") ?? abort(404);
        abort_unless(Akses::bisa(auth()->user(), $res), 403, __('Anda tidak memiliki akses ke menu ini.'));
        return $c;
    }

    public function index(string $res)
    {
        $c = $this->cfg($res);
        $rows = $c['model']::orderBy(...$c['order'])->paginate(15);
        return view('admin.index', compact('res', 'c', 'rows'));
    }

    public function create(string $res)
    {
        $c = $this->cfg($res);
        abort_if($c['no_create'] ?? false, 404);
        return view('admin.form', ['res' => $res, 'c' => $c, 'row' => new $c['model']]);
    }

    public function store(Request $r, string $res)
    {
        $c = $this->cfg($res);
        abort_if($c['no_create'] ?? false, 404);
        return $this->save($r, $res, $c, new $c['model']);
    }

    public function edit(string $res, int $id)
    {
        $c = $this->cfg($res);
        abort_if($c['no_edit'] ?? false, 404);
        return view('admin.form', ['res' => $res, 'c' => $c, 'row' => $c['model']::findOrFail($id)]);
    }

    public function update(Request $r, string $res, int $id)
    {
        $c = $this->cfg($res);
        abort_if($c['no_edit'] ?? false, 404);
        return $this->save($r, $res, $c, $c['model']::findOrFail($id));
    }

    public function destroy(string $res, int $id)
    {
        $c = $this->cfg($res);
        abort_if($c['no_delete'] ?? false, 404);
        abort_unless(Akses::bisaHapus(auth()->user()), 403, __('Operator tidak dapat menghapus data.'));
        abort_if($res === 'user' && $id === auth()->id(), 422, 'Tidak bisa menghapus akun sendiri.');
        $m = $c['model']::findOrFail($id);
        foreach ($c['fields'] as $k => $f) {
            if (in_array($f['type'], ['image', 'file']) && $m->$k) Storage::disk('public')->delete($m->$k);
        }
        $m->delete();
        $this->log('hapus', $c, $id);
        return back()->with('ok', __('Data dihapus.'));
    }

    private function save(Request $r, string $res, array $c, $m)
    {
        $isNew = ! $m->exists;
        $fields = array_filter($c['fields'], fn ($f) => empty($f['ro']));
        $rules = array_map(fn ($f) => str_replace('{id}', $m->id ?? 'NULL', $f['rules']), $fields);
        foreach ($fields as $k => $f) {
            if ($f['type'] === 'password' && $isNew) $rules[$k] = 'required|'.$rules[$k];
        }
        $nama = array_map(fn ($f) => mb_strtolower(trim(preg_replace('/\s*\(.*$/s', '', __($f['label'])))), $fields);
        $data = $r->validate($rules, [], $nama);
        foreach ($fields as $k => $f) {
            $t = $f['type'];
            if ($t === 'image' || $t === 'file') {
                if ($r->hasFile($k)) {
                    if ($m->$k) Storage::disk('public')->delete($m->$k);
                    $data[$k] = $t === 'image' ? Img::store($r->file($k), $res, $f['max'] ?? 1600) : $r->file($k)->store($res, 'public');
                } else unset($data[$k]);
            } elseif ($t === 'password') {
                if (blank($data[$k] ?? null)) unset($data[$k]); else $data[$k] = Hash::make($data[$k]);
            } elseif ($t === 'richtext') {
                $data[$k] = Html::clean($data[$k] ?? '');
            }
        }
        $m->forceFill($data)->save();
        $this->log($isNew ? 'tambah' : 'ubah', $c, $m->id);
        return redirect()->route('admin.index', $res)->with('ok', __('Data disimpan.'));
    }

    private function log(string $aksi, array $c, $id): void
    {
        ActivityLog::create(['user_id' => auth()->id(), 'aksi' => $aksi, 'deskripsi' => auth()->user()->name.' - '.$c['title'].' #'.$id]);
    }
}
