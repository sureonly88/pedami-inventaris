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

        .bg-layer {
            position: absolute;
            z-index: 1;
            pointer-events: none;
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

        .top-shadow-a {
            top: -7mm;
            left: -10mm;
            width: 46mm;
            height: 24mm;
            border-radius: 50%;
            background: rgba(8, 58, 32, 0.2);
        }

        .top-shadow-b {
            top: -2mm;
            right: -13mm;
            width: 42mm;
            height: 32mm;
            border-radius: 50%;
            background: rgba(8, 58, 32, 0.24);
        }

        .top-shadow-c {
            top: 7mm;
            right: -4mm;
            width: 28mm;
            height: 20mm;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.06);
        }

        .wave-left {
            left: -12mm;
            top: 20mm;
            width: 34mm;
            height: 12mm;
            border-radius: 0 0 20mm 20mm;
            background: rgba(255, 255, 255, 0.24);
        }

        .wave-mid {
            left: 8mm;
            top: 21mm;
            width: 28mm;
            height: 10mm;
            border-radius: 0 0 18mm 18mm;
            background: rgba(110, 231, 183, 0.25);
        }

        .wave-right {
            right: -7mm;
            top: 19mm;
            width: 36mm;
            height: 14mm;
            border-radius: 18mm 18mm 0 0;
            background: #ffffff;
        }

        .wave-right-soft {
            right: -4mm;
            top: 21mm;
            width: 28mm;
            height: 11mm;
            border-radius: 18mm 18mm 0 0;
            background: rgba(255, 255, 255, 0.28);
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

        .curve,
        .curve-soft {
            z-index: 1;
        }

        .dot-grid {
            position: absolute;
            width: 10mm;
            height: 10mm;
            z-index: 1;
            pointer-events: none;
            font-size: 0;
        }

        .dot-grid span {
            display: inline-block;
            width: 1.1mm;
            height: 1.1mm;
            margin: 0.45mm;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.35);
        }

        .front-dots {
            top: 4mm;
            right: 4mm;
        }

        .back-dots {
            bottom: 6mm;
            left: 4mm;
        }

        .bottom-fade {
            position: absolute;
            right: -9mm;
            bottom: -7mm;
            width: 34mm;
            height: 18mm;
            border-radius: 50%;
            background: rgba(22, 101, 52, 0.08);
            z-index: 1;
        }

        .bottom-fade-soft {
            position: absolute;
            right: -2mm;
            bottom: -4mm;
            width: 28mm;
            height: 14mm;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.78);
            z-index: 1;
        }

        .logo-area {
            position: absolute;
            top: 6mm;
            left: 6mm;
            color: #ffffff;
            text-transform: uppercase;
            line-height: 1.05;
            z-index: 3;
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

        .icon-cell {
            width: 6mm;
            text-align: center;
            padding-right: 0;
        }

        .icon-wrap {
            display: inline-block;
            width: 4.3mm;
            height: 4.3mm;
            line-height: 0;
        }

        .icon-wrap svg {
            display: block;
            width: 4.3mm;
            height: 4.3mm;
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
            text-align: left;
            font-size: 6.5px;
            color: #6b7280;
            line-height: 1.35;
            z-index: 2;
        }

        .front-footer-icon {
            position: absolute;
            right: 31mm;
            bottom: 5.2mm;
            width: 8mm;
            height: 8mm;
            z-index: 2;
        }

        .front-footer-icon svg {
            display: block;
            width: 8mm;
            height: 8mm;
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
            top: 31mm;
            left: 6mm;
            right: 6mm;
            font-size: 7.5px;
            line-height: 1.55;
            color: #374151;
            z-index: 2;
        }

        .back-title {
            font-size: 10px;
            font-weight: bold;
            color: #166534;
            margin-bottom: 1.2mm;
            text-transform: uppercase;
        }

        .back-title-wrap {
            margin-bottom: 1.2mm;
        }

        .back-title-icon {
            display: inline-block;
            width: 8mm;
            height: 8mm;
            vertical-align: middle;
            margin-right: 2mm;
        }

        .back-title-icon svg {
            display: block;
            width: 8mm;
            height: 8mm;
        }

        .back-title-text {
            display: inline-block;
            vertical-align: middle;
        }

        .back-content .info-table {
            position: relative;
            top: -0.8mm;
            line-height: 1.15;
        }

        .back-content .info-table td {
            padding-top: 0.25mm;
            padding-bottom: 0.25mm;
        }

        .note-list {
            position: relative;
            top: -1.2mm;
            margin: 0.8mm 0 0 0;
            padding-left: 4mm;
        }

        .note-list li {
            margin-bottom: 1mm;
            line-height: 1.4;
        }

        .back-footer {
            position: absolute;
            left: 6mm;
            right: 6mm;
            bottom: 3mm;
            padding: 1mm 0 0;
            font-size: 6.5px;
            color: #111827;
            text-align: center;
            line-height: 1.25;
            background: transparent;
            text-shadow: 0 0 1px rgba(255, 255, 255, 0.85);
            z-index: 4;
        }

        .signature-name {
            display: block;
            margin-top: 0.4mm;
            font-weight: bold;
        }

        .signature-img {
            display: block;
            width: 15mm;
            max-width: 15mm;
            max-height: 5mm;
            margin: 0.4mm auto 0;
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
                    <div class="bg-layer top-shadow-a"></div>
                    <div class="bg-layer top-shadow-b"></div>
                    <div class="bg-layer top-shadow-c"></div>
                    <div class="bg-layer wave-left"></div>
                    <div class="bg-layer wave-mid"></div>
                    <div class="bg-layer wave-right"></div>
                    <div class="bg-layer wave-right-soft"></div>
                    <div class="dot-grid front-dots">
                        <span></span><span></span><span></span><span></span>
                        <span></span><span></span><span></span><span></span>
                        <span></span><span></span><span></span><span></span>
                        <span></span><span></span><span></span><span></span>
                    </div>
                    <div class="curve-soft"></div>
                    <div class="curve"></div>
                    <div class="bottom-fade"></div>
                    <div class="bottom-fade-soft"></div>

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
                                <td class="icon-cell">
                                    <span class="icon-wrap">
                                        <svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                                            <rect x="3" y="5" width="18" height="14" rx="2" fill="none" stroke="#1f7a3d" stroke-width="1.8"/>
                                            <circle cx="8" cy="12" r="1.8" fill="none" stroke="#1f7a3d" stroke-width="1.5"/>
                                            <path d="M5.8 16c.8-1.7 2.3-2.6 4.2-2.6 1.9 0 3.4.9 4.2 2.6" fill="none" stroke="#1f7a3d" stroke-width="1.5" stroke-linecap="round"/>
                                            <path d="M14.5 10h3.2M14.5 13h3.2" fill="none" stroke="#1f7a3d" stroke-width="1.5" stroke-linecap="round"/>
                                        </svg>
                                    </span>
                                </td>
                                <td class="label">ID No</td>
                                <td class="separator">:</td>
                                <td class="value">{{ $employeeId }}</td>
                            </tr>
                            <tr>
                                <td class="icon-cell">
                                    <span class="icon-wrap">
                                        <svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                                            <rect x="4" y="5" width="16" height="15" rx="2" fill="none" stroke="#1f7a3d" stroke-width="1.8"/>
                                            <path d="M8 3.8v3.4M16 3.8v3.4M4 9h16" fill="none" stroke="#1f7a3d" stroke-width="1.6" stroke-linecap="round"/>
                                            <circle cx="9" cy="13" r="1" fill="#1f7a3d"/>
                                            <circle cx="12.5" cy="13" r="1" fill="#1f7a3d"/>
                                            <circle cx="16" cy="13" r="1" fill="#1f7a3d"/>
                                        </svg>
                                    </span>
                                </td>
                                <td class="label">DOB</td>
                                <td class="separator">:</td>
                                <td class="value">{{ $dob }}</td>
                            </tr>
                            <tr>
                                <td class="icon-cell">
                                    <span class="icon-wrap">
                                        <svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                                            <path d="M8.6 4.8c1 0 2 .4 2.8 1.2l1.4 1.4c.4.4.5 1 .2 1.5l-1 1.9c-.3.5-.2 1.1.2 1.6l1.9 1.9c.4.4 1.1.5 1.6.2l1.9-1c.5-.3 1.1-.2 1.5.2l1.4 1.4c1.5 1.5 1.6 3.8.2 5.1l-.8.8c-.8.8-2 .9-3 .5-2.6-1-5.1-2.8-7.3-5-2.2-2.2-4-4.7-5-7.3-.4-1 .1-2.2.8-3l.8-.8c.7-.7 1.5-1 2.4-1z" fill="none" stroke="#1f7a3d" stroke-width="1.6" stroke-linejoin="round"/>
                                        </svg>
                                    </span>
                                </td>
                                <td class="label">Phone</td>
                                <td class="separator">:</td>
                                <td class="value">{{ $displayPhone }}</td>
                            </tr>
                        </table>
                    </div>

                    <div class="front-footer-icon">
                        <svg viewBox="0 0 32 32" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                            <path d="M16 3c6.1 0 11 4.9 11 11 0 7.7-6.2 14-11 15-4.8-1-11-7.3-11-15C5 7.9 9.9 3 16 3z" fill="none" stroke="#1f7a3d" stroke-width="1.8"/>
                            <path d="M9.5 14.6c1-3.7 3.6-5.8 6.5-5.8 3 0 5.6 2.1 6.5 5.8M11.2 18.5c1.3 2.5 3 3.8 4.8 3.8 1.8 0 3.5-1.3 4.8-3.8M13.2 12.7c.5-.8 1.2-1.2 2-1.2s1.5.4 2 1.2M11.6 16c.6-.7 1.4-1 2.2-1 .8 0 1.6.3 2.2 1M16.2 15c.6-.7 1.4-1 2.2-1 .8 0 1.6.3 2.2 1" fill="none" stroke="#1f7a3d" stroke-width="1.4" stroke-linecap="round"/>
                        </svg>
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
                    <div class="bg-layer top-shadow-a"></div>
                    <div class="bg-layer top-shadow-b"></div>
                    <div class="bg-layer top-shadow-c"></div>
                    <div class="bg-layer wave-mid"></div>
                    <div class="bg-layer wave-right"></div>
                    <div class="bg-layer wave-right-soft"></div>
                    <div class="curve-soft"></div>
                    <div class="curve"></div>
                    <div class="bottom-fade"></div>
                    <div class="bottom-fade-soft"></div>
                    <div class="dot-grid back-dots">
                        <span></span><span></span><span></span><span></span>
                        <span></span><span></span><span></span><span></span>
                        <span></span><span></span><span></span><span></span>
                        <span></span><span></span><span></span><span></span>
                    </div>

                    <div class="logo-area">
                        <div class="company-small">Koperasi Konsumen</div>
                        <div class="company-name">PEDAMI</div>
                    </div>

                    <div class="back-content">
                        <div class="back-title-wrap">
                            <span class="back-title-icon">
                                <svg viewBox="0 0 32 32" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                                    <circle cx="16" cy="16" r="14" fill="#1f7a3d"/>
                                    <circle cx="16" cy="11.2" r="4.1" fill="none" stroke="#ffffff" stroke-width="2"/>
                                    <path d="M8.6 24.6c1.7-3.6 4.4-5.4 7.4-5.4 3 0 5.7 1.8 7.4 5.4" fill="none" stroke="#ffffff" stroke-width="2" stroke-linecap="round"/>
                                </svg>
                            </span>
                            <span class="back-title-text">
                                <div class="back-title">Informasi Karyawan</div>
                            </span>
                        </div>
                        <table class="info-table">
                            <tr>
                                <td class="icon-cell">
                                    <span class="icon-wrap">
                                        <svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                                            <rect x="4" y="5" width="16" height="15" rx="2" fill="none" stroke="#1f7a3d" stroke-width="1.8"/>
                                            <path d="M8 3.8v3.4M16 3.8v3.4M4 9h16" fill="none" stroke="#1f7a3d" stroke-width="1.6" stroke-linecap="round"/>
                                            <circle cx="9" cy="13" r="1" fill="#1f7a3d"/>
                                            <circle cx="12.5" cy="13" r="1" fill="#1f7a3d"/>
                                            <circle cx="16" cy="13" r="1" fill="#1f7a3d"/>
                                        </svg>
                                    </span>
                                </td>
                                <td class="label">Joined Date</td>
                                <td class="separator">:</td>
                                <td class="value">{{ $joinDate }}</td>
                            </tr>
                            <tr>
                                <td class="icon-cell">
                                    <span class="icon-wrap">
                                        <svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                                            <rect x="4" y="8" width="16" height="10" rx="1.8" fill="none" stroke="#1f7a3d" stroke-width="1.8"/>
                                            <path d="M9 8V6.7c0-1.7 1.4-3.2 3-3.2s3 1.5 3 3.2V8" fill="none" stroke="#1f7a3d" stroke-width="1.6" stroke-linecap="round"/>
                                            <path d="M4 11h16" fill="none" stroke="#1f7a3d" stroke-width="1.4"/>
                                        </svg>
                                    </span>
                                </td>
                                <td class="label">Divisi</td>
                                <td class="separator">:</td>
                                <td class="value">{{ $division }}</td>
                            </tr>
                            <tr>
                                <td class="icon-cell">
                                    <span class="icon-wrap">
                                        <svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                                            <rect x="5" y="4" width="5" height="15" fill="none" stroke="#1f7a3d" stroke-width="1.6"/>
                                            <rect x="14" y="8" width="5" height="11" fill="none" stroke="#1f7a3d" stroke-width="1.6"/>
                                            <path d="M3.5 19.5h17" fill="none" stroke="#1f7a3d" stroke-width="1.6" stroke-linecap="round"/>
                                            <path d="M7.5 7h0M7.5 10h0M7.5 13h0M16.5 11h0M16.5 14h0" fill="none" stroke="#1f7a3d" stroke-width="1.8" stroke-linecap="round"/>
                                        </svg>
                                    </span>
                                </td>
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
                        mengetahui,<br>
                        Ketua Koperasi Konsumen PEDAMI<br>
                        @if(file_exists(public_path('img/ttd-ketua2.jpg')))
                            <img src="{{ public_path('img/ttd-ketua2.jpg') }}" class="signature-img" alt="Tanda tangan Ketua">
                        @endif
                        <span class="signature-name">Irwan Firmana, S. Sos</span>
                    </div>
                </div>
            </td>
        </tr>
    </table>
</div>
</body>
</html>