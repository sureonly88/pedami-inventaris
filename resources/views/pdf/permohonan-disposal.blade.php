@php
function terbilang($angka)
{
    $angka = abs($angka);
    $huruf = [
        "", "Satu", "Dua", "Tiga", "Empat", "Lima",
        "Enam", "Tujuh", "Delapan", "Sembilan", "Sepuluh", "Sebelas"
    ];

    if ($angka < 12) {
        return $huruf[$angka];
    } elseif ($angka < 20) {
        return terbilang($angka - 10) . " Belas";
    } elseif ($angka < 100) {
        return terbilang(intval($angka / 10)) . " Puluh " . terbilang($angka % 10);
    } elseif ($angka < 200) {
        return "Seratus " . terbilang($angka - 100);
    } elseif ($angka < 1000) {
        return terbilang(intval($angka / 100)) . " Ratus " . terbilang($angka % 100);
    } elseif ($angka < 2000) {
        return "Seribu " . terbilang($angka - 1000);
    } elseif ($angka < 1000000) {
        return terbilang(intval($angka / 1000)) . " Ribu " . terbilang($angka % 1000);
    } else {
        return "";
    }
}
@endphp


<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <style>
        body {
            font-family: "Times New Roman", serif;
            font-size: 12pt;
            line-height: 1.5;
        }
        .title {
            text-align: center;
            font-weight: bold;
            text-transform: uppercase;
            margin-bottom: 10px;
        }
        .nomor {
            text-align: center;
            margin-bottom: 20px;
        }
        table {
            width: 100%;
        }
        td {
            vertical-align: top;
            padding: 4px 0;
        }
        .ttd {
            margin-top: 40px;
            width: 100%;
            text-align: center;
        }
        .ttd td {
            width: 33%;
        }

        .kop {
            width: 100%;
            margin-bottom: 20px;
        }

        .kop img {
            width: 100%;
            height: auto;
        }
    </style>
</head>
<body>

<!-- KOP SURAT -->
    <div class="kop">
        <img src="{{ public_path('img/kop_pedami.png') }}" alt="Kop Surat">
    </div>

<div class="title">
    BERITA ACARA DISPOSAL / PEMUSNAHAN ASET
</div>

<div class="nomor">
    Nomor : {{ $record->nomor ?? '..............................' }}
</div>

@php
    $tgl = \Carbon\Carbon::parse($record->tgl_pengajuan);
@endphp

<p>
    Pada hari ini,
    <i><b>{{ $tgl->translatedFormat('l') }}</b></i>
    tanggal <i><b>{{ terbilang($tgl->day) }}</b></i>
    bulan <i><b>{{ $tgl->translatedFormat('F') }}</b></i>
    tahun <i><b>{{ terbilang($tgl->year) }}</b></i>
    ({{ $tgl->format('d-m-Y') }}),
    kami yang bertanda tangan di bawah ini selaku Pengelola Aset
    telah melakukan pengecekan / penelitian atas aset berupa :
</p>

<table>
    <tr>
        <td width="30%">No. Aset</td>
        <td width="5%">:</td>
        <td>{{ $record->asset->kode_asset }}</td>
    </tr>
    <tr>
        <td>Nama Aset</td>
        <td>:</td>
        <td>{{ $record->asset->nama_asset }}</td>
    </tr>
    <tr>
        <td>Divisi</td>
        <td>:</td>
        <td>{{ $record->asset->penanggung_jawab->subdivisi->divisi->nama_divisi ?? '-' }}</td>
    </tr>
    <tr>
        <td>Pemakai / Penanggung Jawab</td>
        <td>:</td>
        <td>{{ $record->dibuatOleh->nama_karyawan }}</td>
    </tr>
</table>

<br>

<p>
    Adapun hasil pengecekan / penelitian atas aset tersebut
    dalam keadaan <b>{{ $record->kondisi }}</b>
    dan sudah tidak dapat dipergunakan maupun diperbaiki
    untuk menunjang pekerjaan. Adapun barang yang masih bisa diambil dari
    barang tersebut yaitu <b>{{ $record->keterangan }}</b>.
</p>

<p>
    Demikian Berita Acara ini kami buat dengan sebenarnya
    untuk dipergunakan sebagaimana mestinya.
</p>

<p style="text-align: right;">
    {{ $record->lokasi ?? 'Banjarmasin' }},
    {{ \Carbon\Carbon::parse($record->tgl_pengajuan)->translatedFormat('d F Y') }}
</p>

<table class="ttd">
    <tr>
        <td>
            Dibuat Oleh,<br><br>

                <span style="font-size: 10pt;">
                    Tanggal Verifikasi : <i>{{ \Carbon\Carbon::parse($record->tgl_pengajuan)->translatedFormat('d-m-Y') }}</i>
                </span><br>
                <span style="font-size: 10pt;">
                    Waktu Verifikasi : <i>{{ \Carbon\Carbon::parse($record->tgl_pengajuan)->translatedFormat('H:i') }}</i>
                </span><br><br>

            <b>{{ $record->dibuatOleh->nama_karyawan }}</b>
        </td>

        {{-- MENGETAHUI - MANAGER --}}
        <td>
            Mengetahui,<br><br>

            @if ($record->tgl_verif_manager)
                <span style="font-size: 10pt;">
                    Tanggal Verifikasi : <i>{{ \Carbon\Carbon::parse($record->tgl_verif_manager)->translatedFormat('d-m-Y') }}</i>
                </span><br>
                <span style="font-size: 10pt;">
                    Waktu Verifikasi :  <i>{{ \Carbon\Carbon::parse($record->tgl_verif_manager)->translatedFormat('H:i') }}</i>
                </span><br><br>
            @else
                <br><br>
            @endif

            <b>{{ $record->Manager->nama_karyawan ?? 'Manager' }}</b><br>
            Manager
        </td>

        {{-- MENYETUJUI - KETUA --}}
        <td>
            Menyetujui,<br><br>

            @if ($record->tgl_verif_ketua)
                <span style="font-size: 10pt;">
                    Tanggal Verifikasi : <i>{{ \Carbon\Carbon::parse($record->tgl_verif_ketua)->translatedFormat('d-m-Y') }}</i>
                </span><br>
                <span style="font-size: 10pt;">
                    Waktu Verifikasi : <i>{{ \Carbon\Carbon::parse($record->tgl_verif_ketua)->translatedFormat('H:i') }}</i>
                </span><br><br>
            @else
                <br><br>
            @endif

            <b>{{ $record->Ketua->nama_karyawan ?? 'Ketua' }}</b><br>
            Ketua
        </td>

        
    </tr>
</table>


</body>
</html>
