<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Support\Monitoring;
use Illuminate\Http\Request;

class BantuanController extends Controller
{
    public function dashboard()
    {
        $data = Monitoring::data(\App\Support\Akses::bisa(auth()->user(), 'kelompok'));
        $data['exportUrl'] = route('admin.monitoring.export');
        return view('admin.bantuan.dashboard', compact('data'));
    }

    /** Ekspor Excel mengikuti filter di dashboard; formatnya sama dengan format impor. */
    public function export(Request $r)
    {
        $d = Monitoring::data();
        return Monitoring::stream('monitoring-bantuan-kelompok-tani-'.date('Ymd').'.xlsx',
            Monitoring::filter($d['groups'], $r->only('q', 'kecamatan', 'status', 'tahun')), $d['years']);
    }
}
