<table>

@for ($i = 0; $i < count($records); $i += 2)

    <tr>
        <td>

        <table>
                <tr>
                    <td colspan="3"; style align="center">I<b>NVENTARIS KOPERASI KONSUMEN PEDAMI</b></td>
                </tr>
                <tr>
                    <td width="20%">
                       
                        <img src="{{ public_path("img/logo.jpeg") }}" width="65" height="65">
                    </td>
                    <td width="60%">
                        {{ $records[$i]->kode_asset }} ({{ $records[$i]->nama_asset }})
                        <br/>
                        {{ $records[$i]->karyawan->nama_karyawan }}
                        <br/>
                        {{ $records[$i]->karyawan->subdivisi->divisi->nama_divisi }}
                    </td>
                    <td width="20%">
                    {!! DNS2D::getBarcodeHTML($records[$i]->kode_asset, 'QRCODE', 3, 3) !!}
                    </td>
                </tr>
                <tr>
                    <td colspan="3"; style align="center"><i><b>Dilarang mencabut/melepas stiker ini!</b></i></td>
                </tr>
            </table>
        </td>
        @if (isset($records[$i+1]))
        <td>
        <table>
                <tr>
                    <td colspan="3"; style align="center"><b>INVENTARIS KOPERASI KONSUMEN PEDAMI</b></td>
                </tr>
                <tr>
                    <td width="20%">
                    <img src="{{ public_path("img/logo.jpeg") }}" width="65" height="65">
                    </td>
                    <td width="60%">
                        {{ $records[$i+1]->kode_asset }} ({{ $records[$i+1]->nama_asset }})
                        <br/>
                        {{ $records[$i+1]->karyawan->nama_karyawan }}
                        <br/>
                        {{ $records[$i+1]->karyawan->subdivisi->divisi->nama_divisi }}
                    </td>
                    <td width="20%">
                        
                        {!! DNS2D::getBarcodeHTML($records[$i+1]->kode_asset, 'QRCODE', 3, 3) !!}
                    </td>
                </tr>
                <tr>
                    <td colspan="3"; style align="center"><i><b>Dilarang mencabut/melepas stiker ini!</b></i></td>
                </tr>
            </table>
           
        </td>

        @endif
    </tr>

@endfor

</table>