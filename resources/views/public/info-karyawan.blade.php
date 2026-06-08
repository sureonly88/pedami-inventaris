<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Informasi Karyawan</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f9fafb;
            margin: 0;
            padding: 24px;
            color: #1f2937;
        }
        .container {
            max-width: 900px;
            margin: 0 auto;
            background: #fff;
            border-radius: 16px;
            box-shadow: 0 10px 30px rgba(0,0,0,.08);
            overflow: hidden;
        }
        .header {
            background: linear-gradient(135deg, #b91c1c, #ef4444);
            color: white;
            padding: 24px;
        }
        .content {
            display: grid;
            grid-template-columns: 260px 1fr;
            gap: 24px;
            padding: 24px;
        }
        .photo-box {
            background: #f3f4f6;
            border-radius: 16px;
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 260px;
            overflow: hidden;
        }
        .photo-box img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        .initial {
            width: 120px;
            height: 120px;
            border-radius: 999px;
            background: #fee2e2;
            color: #b91c1c;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 56px;
            font-weight: 700;
        }
        .details {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 16px;
        }
        .item {
            background: #f9fafb;
            border: 1px solid #e5e7eb;
            border-radius: 12px;
            padding: 14px;
        }
        .label {
            font-size: 12px;
            text-transform: uppercase;
            color: #6b7280;
            margin-bottom: 6px;
        }
        .value {
            font-weight: 600;
        }
        .full {
            grid-column: 1 / -1;
        }
        @media (max-width: 768px) {
            body {
                padding: 12px;
            }
            .content,
            .details {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1 style="margin:0; font-size: 28px;">Informasi Karyawan</h1>
            <p style="margin:8px 0 0; opacity:.95;">Koperasi Konsumen PEDAMI</p>
        </div>

        <div class="content">
            <div class="photo-box">
                @if ($urlFoto)
                    <img src="{{ $urlFoto }}" alt="Foto {{ $record->nama_karyawan }}">
                @else
                    <div class="initial">{{ strtoupper(mb_substr($record->nama_karyawan ?? 'K', 0, 1)) }}</div>
                @endif
            </div>

            <div class="details">
                <div class="item"><div class="label">ID Karyawan</div><div class="value">{{ $record->nik ?? $record->id }}</div></div>
                <div class="item"><div class="label">Status</div><div class="value">{{ $record->status_karyawan ?? '-' }}</div></div>
                <div class="item full"><div class="label">Nama Karyawan</div><div class="value">{{ $record->nama_karyawan ?? '-' }}</div></div>
                <div class="item"><div class="label">Jabatan</div><div class="value">{{ $record->jabatan ?? '-' }}</div></div>
                <div class="item"><div class="label">Jenis Kelamin</div><div class="value">{{ $record->jkel ?? '-' }}</div></div>
                <div class="item"><div class="label">Divisi</div><div class="value">{{ $record->subdivisi?->divisi?->nama_divisi ?? '-' }}</div></div>
                <div class="item"><div class="label">Subdivisi</div><div class="value">{{ $record->subdivisi?->nama_sub ?? '-' }}</div></div>
                <div class="item"><div class="label">Tanggal Masuk</div><div class="value">{{ $record->tanggal_masuk_kerja ? $record->tanggal_masuk_kerja->format('d/m/Y') : '-' }}</div></div>
                <div class="item"><div class="label">Masa Kerja</div><div class="value">{{ $record->masa_kerja ?? '-' }}</div></div>
                <div class="item"><div class="label">Email</div><div class="value">{{ $record->user?->email ?? '-' }}</div></div>
                <div class="item"><div class="label">No HP</div><div class="value">{{ $record->no_hp ?? '-' }}</div></div>
            </div>
        </div>
    </div>
</body>
</html>