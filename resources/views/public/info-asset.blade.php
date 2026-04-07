<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Informasi Asset</title>
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
            background: linear-gradient(135deg, #ec4899, #f9a8d4);
            color: white;
            padding: 24px;
        }
        .content {
            display: grid;
            grid-template-columns: 320px 1fr;
            gap: 24px;
            padding: 24px;
        }
        .image-box {
            background: #f3f4f6;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 280px;
            overflow: hidden;
        }
        .image-box img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        .placeholder {
            color: #6b7280;
            font-size: 14px;
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
            <h1 style="margin:0; font-size: 28px;">Informasi Inventaris Asset</h1>
            <p style="margin:8px 0 0; opacity:.95;">Koperasi Konsumen PEDAMI</p>
        </div>

        <div class="content">
            <div class="image-box">
                @if ($urlGambar)
                    <img src="{{ $urlGambar }}" alt="Gambar Asset">
                @else
                    <div class="placeholder">Gambar asset tidak tersedia</div>
                @endif
            </div>

            <div class="details">
                <div class="item"><div class="label">ID Asset</div><div class="value">{{ $record->id }}</div></div>
                <div class="item"><div class="label">Kode Asset</div><div class="value">{{ $record->kode_asset ?? '-' }}</div></div>
                <div class="item full"><div class="label">Nama Asset</div><div class="value">{{ $record->nama_asset ?? '-' }}</div></div>
                <div class="item"><div class="label">Tanggal Pembelian</div><div class="value">{{ $record->tgl_beli ? \Carbon\Carbon::parse($record->tgl_beli)->format('d/m/Y') : '-' }}</div></div>
                <div class="item"><div class="label">Harga Beli</div><div class="value">Rp {{ number_format((float) ($record->hrg_beli ?? 0), 0, ',', '.') }}</div></div>
                <div class="item"><div class="label">Lokasi</div><div class="value">{{ $record->ruangan->lokasi ?? '-' }}</div></div>
                <div class="item"><div class="label">Ruangan</div><div class="value">{{ $record->ruangan->ruangan ?? '-' }}</div></div>
                <div class="item"><div class="label">Divisi</div><div class="value">{{ $record->divisi->nama_divisi ?? '-' }}</div></div>
                <div class="item"><div class="label">Status Barang</div><div class="value">{{ $record->status_barang ?? '-' }}</div></div>
                <div class="item"><div class="label">Karyawan</div><div class="value">{{ $record->karyawan->nama_karyawan ?? '-' }}</div></div>
                <div class="item full"><div class="label">Penanggung Jawab</div><div class="value">{{ $record->penanggung_jawab->nama_karyawan ?? '-' }}</div></div>
                <div class="item full"><div class="label">Deskripsi</div><div class="value">{{ $record->deskripsi ?? '-' }}</div></div>
            </div>
        </div>
    </div>
</body>
</html>