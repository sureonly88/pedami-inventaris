<?php

namespace App\Exports;

use App\Models\RiwayatPembayaranR2r4;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class RiwayatPembayaranExport implements FromView, ShouldAutoSize, WithStyles
{
    protected $query;
    protected $start_date;
    protected $end_date;
    protected $kategori;

    public function __construct($query, $start_date = null, $end_date = null, $kategori = 'all')
    {
        $this->query = $query;
        $this->start_date = $start_date;
        $this->end_date = $end_date;
        $this->kategori = $kategori;
    }

    public function view(): View
    {
        return view('exports.riwayat-pembayaran', [
            'records' => $this->query->get(),
            'start_date' => $this->start_date,
            'end_date' => $this->end_date,
            'kategori' => $this->kategori,
        ]);
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true, 'size' => 14]],
            // styling for headers will be handled by blade or basic worksheet methods if needed
        ];
    }
}
