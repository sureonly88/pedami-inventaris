
        <table>
                <tr>
                    <td colspan="3"; style align="center"><b>INVENTARIS KOPERASI KONSUMEN PEDAMI</b></td>
                </tr>
                <tr>
                    <td width="20%">
                       
                        <img src="/img/logo.jpeg" width="65" height="65">
                    </td>
                    <td width="50%">
                        {{ $record->kode_asset }} ({{ $record->nama_asset }})
                        <!-- {{ $record->karyawan->nama_karyawan }} -->
                        <br/>
                        {{ $record->karyawan->subdivisi->divisi->nama_divisi }}
                    </td>
                    <td width="20%">
                    {!! DNS2D::getBarcodeHTML($record->kode_asset, 'QRCODE', 3, 3) !!}
                    </td>
                </tr>
                <tr>
                    <td colspan="3"; style align="center"><i><b>Dilarang mencabut/melepas stiker ini!</b></i></td>
                </tr>
            </table>
        
