<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\{ActivityLog, Setting};
use App\Support\Img;
use Illuminate\Http\Request;

class SettingController extends Controller
{
    public function edit()
    {
        abort_unless(\App\Support\Akses::isSuper(auth()->user()), 403);
        return view('admin.setting', ['fields' => config('settings')]);
    }

    public function update(Request $r)
    {
        abort_unless(\App\Support\Akses::isSuper(auth()->user()), 403);
        $rules = ['maps_embed' => 'nullable|url|starts_with:https://www.google.com/maps/embed'];
        foreach (['instagram', 'facebook', 'youtube'] as $k) $rules[$k] = 'nullable|url|max:255';
        $r->validate($rules + ['logo' => 'nullable|image|max:2048']);
        foreach (config('settings') as $k => $f) {
            if ($f['type'] === 'image') {
                if ($r->hasFile($k)) Setting::put($k, Img::store($r->file($k), 'setting'));
            } else Setting::put($k, $r->input($k));
        }
        ActivityLog::create(['user_id' => auth()->id(), 'aksi' => 'ubah', 'deskripsi' => auth()->user()->name.' - Pengaturan Situs']);
        return back()->with('ok', 'Pengaturan disimpan.');
    }
}
