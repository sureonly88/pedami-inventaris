<?php

namespace App\Exports;

use App\Models\PenjualanR2r4;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;


class PenjualanDirectExport implements FromCollection, WithHeadings, WithMapping
{
    protected $ids;

    public function __construct($ids = null)
    {
        $this->ids = $ids;
    }

    public function collection()
    {
        return PenjualanR2r4::with('data_r2r4')
            ->when($this->ids, fn ($q) => $q->whereIn('id', $this->ids))
            ->get();
    }

    public function headings(): array
    {
        return [
            'Nopol',
            'Tanggal Penjualan',
            'Harga Penjualan',
            'Nama Pembeli',
            'Nama Barang',
            'Tahun',
            'No Rangka',
            'No Mesin',
            'Warna',
        ];
    }

    public function map($row): array
    {
        return [
            $row->data_r2r4?->plat,
            $row->tgl_jual,
            $row->hrg_jual,
            $row->nm_pembeli,
            $row->data_r2r4?->nm_brg,
            $row->data_r2r4?->thn,
            $row->data_r2r4?->no_rangka,
            $row->data_r2r4?->no_mesin,
            $row->data_r2r4?->warna,
        ];
    }
}
