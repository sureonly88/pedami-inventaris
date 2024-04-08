<div>
<table cellpadding="5">
<tr>
<td><img src="/img/logo.jpeg" width="60" height="60"></td>
<td style="text-align: center; vertical-align: top;">BARANG INVENTARIS<br/> KOPERASI KONSUMEN PEDAMI</td>
</tr>
<tr>
    <td colspan="2"><hr/></td>
</tr>
<tr>
    <td>{!! DNS2D::getBarcodeHTML($record->kode_asset, 'QRCODE', 3, 3) !!} <b>{!! $record->kode_asset!!}</b></td>
    <td style="text-align: top; vertical-align: top;">Nama Barang : {!! $record->nama_asset !!}
    <br/>Pemakai : {!! $record->karyawan->nama_karyawan!!}
    <br/>Divisi : {!! $record->karyawan->subdivisi->divisi->nama_divisi!!}
    </td>
</tr>
<tr>
    <td></td>
</tr>
<tr>
    <td style="text-align: center;" colspan="2">&nbsp;&nbsp;<i>"Dilarang mencabut/melepas stiker ini!"</i></td>
</tr>
</table>

</div>

