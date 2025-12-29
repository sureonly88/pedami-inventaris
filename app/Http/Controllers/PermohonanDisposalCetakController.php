<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PermohonanDisposal;
use Barryvdh\DomPDF\Facade\Pdf;

class PermohonanDisposalCetakController extends Controller
{
    public function cetak(PermohonanDisposal $record)
    {
        $pdf = Pdf::loadView(
            'pdf.permohonan-disposal',
            compact('record')
        )->setPaper('A4');

        return $pdf->stream(
            'Berita_Acara_Disposal_' . $record->asset->kode_asset . '.pdf'
        );
    }
}
