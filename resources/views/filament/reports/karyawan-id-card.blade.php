<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        @page {
            margin: 10mm;
        }

        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            padding: 0;
            font-family: Helvetica, Arial, sans-serif;
            color: #1f2937;
            background: #ffffff;
        }

        .sheet {
            width: 100%;
            padding: 0;
            background: #ffffff;
        }

        .card-row {
            width: auto;
            margin: 0 auto;
            border-collapse: collapse;
        }

        .card-cell {
            width: 66mm;
            vertical-align: top;
            padding: 0 4mm;
        }

        .id-card {
            position: relative;
            width: 62mm;
            height: 86mm;
            overflow: hidden;
            border-radius: 8px;
            background: #ffffff;
            border: 1px solid #d1d5db;
        }

        .front .top,
        .back .top {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 31mm;
        }

        .front .top {
            background: #166534;
        }

        .back .top {
            background: #166534;
        }

        .curve {
            position: absolute;
            right: -24mm;
            top: 14mm;
            width: 60mm;
            height: 74mm;
            border-radius: 50%;
            background: #ffffff;
        }

        .curve-soft {
            position: absolute;
            right: -18mm;
            top: 12mm;
            width: 48mm;
            height: 36mm;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.24);
        }

        .logo-area {
            position: absolute;
            top: 6mm;
            left: 6mm;
            color: #ffffff;
            text-transform: uppercase;
            line-height: 1.05;
        }

        .company-small {
            font-size: 7px;
            letter-spacing: 1.2px;
            font-weight: bold;
        }

        .company-name {
            font-size: 12px;
            font-weight: bold;
        }

        .logo-img {
            max-width: 24mm;
            max-height: 14mm;
        }

        .photo {
            position: absolute;
            top: 17mm;
            left: 50%;
            width: 21mm;
            height: 21mm;
            margin-left: -10.5mm;
            border-radius: 50%;
            background: #f9fafb;
            border: 3px solid #ffffff;
            outline: 2px solid #166534;
            text-align: center;
            line-height: 21mm;
            color: #166534;
            font-size: 24px;
            font-weight: bold;
            overflow: hidden;
            z-index: 3;
        }

        .photo img {
            display: block;
            width: 21mm;
            height: 21mm;
            max-width: 21mm;
            max-height: 21mm;
            border-radius: 50%;
        }

        .front-content {
            position: absolute;
            left: 6mm;
            right: 6mm;
            top: 42mm;
            text-align: center;
            z-index: 2;
        }

        .name {
            font-size: 12px;
            font-weight: bold;
            color: #111827;
            line-height: 1.25;
            margin-bottom: 1.2mm;
        }

        .position {
            font-size: 8.5px;
            color: #4b5563;
            line-height: 1.25;
            margin-bottom: 2mm;
        }

        .info-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 7.5px;
            text-align: left;
            line-height: 1.3;
        }

        .info-table td {
            padding: 0.55mm 0.6mm;
            vertical-align: top;
        }

        .label {
            width: 16mm;
            color: #374151;
            font-weight: bold;
            white-space: nowrap;
        }

        .separator {
            width: 2mm;
            color: #6b7280;
            text-align: center;
        }

        .value {
            color: #111827;
            line-height: 1.3;
            word-break: break-word;
        }

        .front-footer {
            position: absolute;
            right: 6mm;
            bottom: 5mm;
            max-width: 33mm;
            text-align: right;
            font-size: 6.5px;
            color: #6b7280;
            line-height: 1.35;
            z-index: 2;
        }

        .qr-code {
            position: absolute;
            left: 7mm;
            bottom: 5mm;
            width: 10mm;
            height: 10mm;
            padding: 0.7mm;
            background: #ffffff;
            border: 1px solid #e5e7eb;
            border-radius: 2px;
            line-height: 0;
            font-size: 0;
            overflow: hidden;
            z-index: 3;
        }

        .qr-code img {
            display: block;
            width: 8.6mm;
            height: 8.6mm;
            max-width: 8.6mm;
            max-height: 8.6mm;
        }

        .back-content {
            position: absolute;
            top: 34mm;
            left: 6mm;
            right: 6mm;
            font-size: 7.5px;
            line-height: 1.55;
            color: #374151;
        }

        .back-title {
            font-size: 10px;
            font-weight: bold;
            color: #166534;
            margin-bottom: 2.5mm;
            text-transform: uppercase;
        }

        .note-list {
            margin: 3mm 0 0 0;
            padding-left: 4mm;
        }

        .note-list li {
            margin-bottom: 1.6mm;
        }

        .back-footer {
            position: absolute;
            left: 6mm;
            right: 6mm;
            bottom: 4mm;
            border-top: 1px solid #e5e7eb;
            padding-top: 2mm;
            font-size: 6.5px;
            color: #6b7280;
            text-align: center;
        }
    </style>
</head>
<body>
@php
    use Illuminate\Support\Facades\Storage;

    $division = $record->subdivisi?->divisi?->nama_divisi ?? '-';
    $subdivision = $record->subdivisi?->nama_sub ?? '-';
    $employeeId = data_get($record, 'nip') ?: ($record->nik ?? '-');
    $photoPath = data_get($record, 'foto') ?: data_get($record, 'photo') ?: data_get($record, 'gambar');
    $photoUrl = null;
    $employeeInfoUrl = route('info-karyawan.public', $record);

    if ($photoPath) {
        if (str_starts_with($photoPath, 'http')) {
            $photoUrl = $photoPath;
        } else {
            $photoPath = ltrim($photoPath, '/');

            try {
                $photoUrl = Storage::disk('minio')->url($photoPath);
            } catch (\Throwable $exception) {
                if (file_exists(public_path($photoPath))) {
                    $photoUrl = public_path($photoPath);
                } elseif (file_exists(public_path('storage/' . $photoPath))) {
                    $photoUrl = public_path('storage/' . $photoPath);
                }
            }
        }
    }

    $displayName = mb_strimwidth($record->nama_karyawan ?? '-', 0, 34, '...');
    $displayPosition = mb_strimwidth($record->jabatan ?? '-', 0, 32, '...');
    $displaySubdivision = mb_strimwidth($subdivision, 0, 30, '...');
    $displayPhone = mb_strimwidth($record->no_hp ?? '-', 0, 22, '...');
    $dob = $record->tanggal_lahir ? $record->tanggal_lahir->timezone(config('app.timezone'))->locale('id')->translatedFormat('d F Y') : '-';
    $joinDate = $record->tanggal_masuk_kerja ? $record->tanggal_masuk_kerja->timezone(config('app.timezone'))->locale('id')->translatedFormat('d F Y') : '-';
@endphp

<div class="sheet">
    <table class="card-row">
        <tr>
            <td class="card-cell">
                <div class="id-card front">
                    <div class="top"></div>
                    <div class="curve-soft"></div>
                    <div class="curve"></div>

                    <div class="logo-area">
                        @if(file_exists(public_path('images/logo.png')))
                            <img src="{{ public_path('images/logo.png') }}" class="logo-img" alt="Logo">
                        @elseif(file_exists(public_path('img/logo.jpeg')))
                            <img src="{{ public_path('img/logo.jpeg') }}" class="logo-img" alt="Logo">
                        @else
                            <div class="company-small">Koperasi Konsumen</div>
                            <div class="company-name">PEDAMI</div>
                        @endif
                    </div>

                    <div class="photo">
                        @if($photoUrl)
                            <img src="{{ $photoUrl }}" width="79" height="79" style="display: block; width: 21mm; height: 21mm; max-width: 21mm; max-height: 21mm;" alt="Foto {{ $record->nama_karyawan }}">
                        @else
                            {{ strtoupper(mb_substr($record->nama_karyawan ?? 'K', 0, 1)) }}
                        @endif
                    </div>

                    <div class="front-content">
                        <div class="name">{{ $displayName }}</div>
                        <div class="position">{{ $displayPosition }}</div>

                        <table class="info-table">
                            <tr>
                                <td class="label">ID No</td>
                                <td class="separator">:</td>
                                <td class="value">{{ $employeeId }}</td>
                            </tr>
                            <tr>
                                <td class="label">DOB</td>
                                <td class="separator">:</td>
                                <td class="value">{{ $dob }}</td>
                            </tr>
                            <tr>
                                <td class="label">Phone</td>
                                <td class="separator">:</td>
                                <td class="value">{{ $displayPhone }}</td>
                            </tr>
                        </table>
                    </div>

                    <div class="front-footer">
                        Kartu identitas resmi<br>
                        Koperasi Konsumen Pedami
                    </div>

                    <div class="qr-code">
                        <img src="data:image/png;base64,{{ DNS2D::getBarcodePNG($employeeInfoUrl, 'QRCODE', 3, 3) }}" width="33" height="33" style="display: block; width: 8.6mm; height: 8.6mm; max-width: 8.6mm; max-height: 8.6mm;" alt="QR Data Karyawan">
                    </div>
                </div>
            </td>

            <td class="card-cell">
                <div class="id-card back">
                    <div class="top"></div>
                    <div class="curve-soft"></div>
                    <div class="curve"></div>

                    <div class="logo-area">
                        <div class="company-small">Koperasi Konsumen</div>
                        <div class="company-name">PEDAMI</div>
                    </div>

                    <div class="back-content">
                        <div class="back-title">Informasi Karyawan</div>
                        <table class="info-table">
                            <tr>
                                <td class="label">Joined Date</td>
                                <td class="separator">:</td>
                                <td class="value">{{ $joinDate }}</td>
                            </tr>
                            <tr>
                                <td class="label">Status</td>
                                <td class="separator">:</td>
                                <td class="value">{{ $record->status_karyawan ?? '-' }}</td>
                            </tr>
                            <tr>
                                <td class="label">Divisi</td>
                                <td class="separator">:</td>
                                <td class="value">{{ $division }}</td>
                            </tr>
                            <tr>
                                <td class="label">Subdivisi</td>
                                <td class="separator">:</td>
                                <td class="value">{{ $displaySubdivision }}</td>
                            </tr>
                        </table>

                        <ul class="note-list">
                            <li>Kartu ini hanya berlaku selama pemegang terdaftar sebagai karyawan/pengurus aktif.</li>
                            <li>Apabila kartu ditemukan, mohon dikembalikan ke Koperasi Konsumen Pedami.</li>
                        </ul>
                    </div>

                    <div class="back-footer">
                        Dicetak pada {{ \Carbon\Carbon::now('Asia/Makassar')->locale('id')->translatedFormat('d F Y') }}
                    </div>
                </div>
            </td>
        </tr>
    </table>
</div>
</body>
</html>