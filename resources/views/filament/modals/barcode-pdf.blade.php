<!DOCTYPE html>
<html>
<head>
<style>
    body {
        font-family: Helvetica, Arial, sans-serif;
        margin: 0; 
        padding: 0;
    }
    .page-container {
        width: 100%;
        border-collapse: separate;
        border-spacing: 15px;
    }
    .sticker-cell {
        padding: 15px;
        width: 50%;
    }
    .sticker {
        border: 2px solid #1e293b;
        border-radius: 8px;
        padding: 8px;
        background-color: #ffffff;
        width: 100%;
        page-break-inside: avoid;
    }
    .sticker-header {
        background-color: #1e293b;
        color: #ffffff;
        text-align: center;
        font-size: 10px;
        font-weight: bold;
        padding: 6px;
        border-radius: 4px 4px 0 0;
        margin-top: -8px;
        margin-left: -8px;
        margin-right: -8px;
        margin-bottom: 8px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
    .sticker-body {
        width: 100%;
        border-collapse: collapse;
    }
    .sticker-body td {
        vertical-align: middle;
    }
    .logo-td {
        width: 25%;
        text-align: center;
    }
    .info-td {
        width: 50%;
        padding-left: 10px;
        padding-right: 5px;
    }
    .qr-td {
        width: 25%;
        text-align: center;
    }
    .asset-code {
        font-weight: bold;
        font-size: 15px;
        color: #0f172a;
    }
    .asset-name {
        font-size: 12px;
        color: #475569;
        margin-top: 4px;
        font-weight: 500;
        line-height: 1.2;
    }
    .asset-divisi {
        font-size: 10px;
        color: #64748b;
        margin-top: 6px;
        background-color: #f1f5f9;
        padding: 3px 6px;
        border-radius: 4px;
        display: inline-block;
    }
    .sticker-footer {
        text-align: center;
        font-size: 9px;
        font-style: italic;
        color: #ef4444;
        margin-top: 10px;
        border-top: 1px dashed #cbd5e1;
        padding-top: 5px;
    }
    .qr-wrapper {
        border: 1px solid #e2e8f0;
        padding: 3px;
        border-radius: 4px;
        display: inline-block;
        background-color: #fff;
    }
</style>
</head>
<body>
<table class="page-container" cellspacing="0" cellpadding="0">
@for ($i = 0; $i < count($records); $i += 2)
    <tr>
        <!-- First Column -->
        <td class="sticker-cell" valign="top">
            <div class="sticker">
                <div class="sticker-header">
                    Inventaris Koperasi Konsumen Pedami
                </div>
                <table class="sticker-body">
                    <tr>
                        <td class="logo-td">
                            @if(file_exists(public_path('img/logo.jpeg')))
                                <img src="{{ public_path('img/logo.jpeg') }}" width="60" height="60">
                            @endif
                        </td>
                        <td class="info-td">
                            <div class="asset-code">{{ $records[$i]->kode_asset }}</div>
                            <div class="asset-name">{{ mb_strimwidth($records[$i]->nama_asset, 0, 30, "...") }}</div>
                            <div class="asset-divisi">
                                {{ optional(optional(optional($records[$i]->penanggung_jawab)->subdivisi)->divisi)->nama_divisi ?? 'Tanpa Divisi' }}
                            </div>
                        </td>
                        <td class="qr-td">
                            <div class="qr-wrapper">
                                {!! DNS2D::getBarcodeHTML("http://92.242.187.223/info-asset/".$records[$i]->id, 'QRCODE', 2.8, 2.8) !!}
                            </div>
                        </td>
                    </tr>
                </table>
                <div class="sticker-footer">
                    Dilarang mencabut/melepas stiker ini!
                </div>
            </div>
        </td>

        <!-- Second Column -->
        @if (isset($records[$i+1]))
        <td class="sticker-cell" valign="top">
            <div class="sticker">
                <div class="sticker-header">
                    Inventaris Koperasi Konsumen Pedami
                </div>
                <table class="sticker-body">
                    <tr>
                        <td class="logo-td">
                            @if(file_exists(public_path('img/logo.jpeg')))
                                <img src="{{ public_path('img/logo.jpeg') }}" width="60" height="60">
                            @endif
                        </td>
                        <td class="info-td">
                            <div class="asset-code">{{ $records[$i+1]->kode_asset }}</div>
                            <div class="asset-name">{{ mb_strimwidth($records[$i+1]->nama_asset, 0, 30, "...") }}</div>
                            <div class="asset-divisi">
                                {{ optional(optional(optional($records[$i+1]->penanggung_jawab)->subdivisi)->divisi)->nama_divisi ?? 'Tanpa Divisi' }}
                            </div>
                        </td>
                        <td class="qr-td">
                            <div class="qr-wrapper">
                                {!! DNS2D::getBarcodeHTML("http://92.242.187.223/info-asset/".$records[$i+1]->id, 'QRCODE', 2.8, 2.8) !!}
                            </div>
                        </td>
                    </tr>
                </table>
                <div class="sticker-footer">
                    Dilarang mencabut/melepas stiker ini!
                </div>
            </div>
        </td>
        @else
        <td class="sticker-cell"></td>
        @endif
    </tr>
@endfor
</table>
</body>
</html>