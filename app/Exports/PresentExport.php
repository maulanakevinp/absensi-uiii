<?php

namespace App\Exports;

use App\Present;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class PresentExport implements FromView
{

    private $user_id, $bulan;

    public function __construct($user_id, $bulan)
    {
        $this->user_id = $user_id;
        $this->bulan = $bulan;
    }

    public function view(): View
    {
        $data = explode('-', $this->bulan);
        $presents = Present::whereUserId($this->user_id)->whereMonth('tanggal', $data[1])->whereYear('tanggal', $data[0])->orderBy('tanggal', 'desc')->get();
        $kehadiran = Present::whereUserId($this->user_id)->whereMonth('tanggal', $data[1])->whereYear('tanggal', $data[0])->whereKeterangan('telat')->get();
        $totalJamTelat = 0;
        foreach ($kehadiran as $present) {
            $totalJamTelat = $totalJamTelat + (\Carbon\Carbon::parse($present->jam_masuk)->diffInHours(\Carbon\Carbon::parse(config('absensi.jam_masuk'))));
        }
        return view('presents.excel-user', compact('presents', 'totalJamTelat'));
    }
}
