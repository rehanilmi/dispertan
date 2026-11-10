<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Support\Img;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\{Hash, Storage};

/** Profil Saya: setiap pegawai dapat mengubah biodatanya sendiri. */
class ProfilController extends Controller
{
    public function edit() { return view('admin.profil', ['u' => auth()->user()]); }

    public function update(Request $r)
    {
        $u = auth()->user();
        $d = $r->validate([
            'name' => 'required|max:100', 'nip' => 'nullable|max:30', 'jabatan' => 'nullable|max:100',
            'no_hp' => ['nullable', 'regex:/^[0-9+\s-]{8,20}$/'], 'alamat' => 'nullable|max:500', 'foto' => 'nullable|image|max:3072',
        ], [], ['name' => 'nama lengkap', 'nip' => 'NIP', 'jabatan' => 'jabatan', 'no_hp' => 'nomor HP', 'alamat' => 'alamat', 'foto' => 'foto profil']);

        if ($r->hasFile('foto')) {
            if ($u->foto) Storage::disk('public')->delete($u->foto);
            $d['foto'] = Img::store($r->file('foto'), 'profil', 600);
        } elseif ($r->boolean('hapus_foto')) {
            if ($u->foto) Storage::disk('public')->delete($u->foto);
            $d['foto'] = null;
        } else {
            unset($d['foto']);
        }
        $u->forceFill($d)->save();
        return back()->with('ok', __('Profil diperbarui.'));
    }

    public function password(Request $r)
    {
        $d = $r->validate(['password_lama' => 'required|current_password', 'password' => 'required|min:10|max:100|confirmed'], [], ['password_lama' => 'password saat ini', 'password' => 'password baru']);
        auth()->user()->forceFill(['password' => Hash::make($d['password'])])->save();
        return back()->with('ok', __('Password diubah.'));
    }
}
