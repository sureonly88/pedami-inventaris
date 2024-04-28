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
                        {{ $records[$i]->kode_brg }} ({{ $records[$i]->plat}})
                        <br/>
                        {{ $records[$i]->departemen}} ({{ $records[$i]->pemegang }})
                    <td width="20%">
                    {!! DNS2D::getBarcodeHTML($records[$i]->kode_brg, 'QRCODE', 3, 3) !!}
                    </td>
                </tr>
                <tr>
                    <td colspan="3"; style align="center"><i><b>Dilarang mencabut/melepas stiker ini!</b></i></td>
                </tr>
            </table>
        </td>
        @if (isset($records[$i + 1]))
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
                        {{ $records[$i + 1]->kode_brg}} ({{ $records[$i + 1]->plat }})
                        <br/>
                        {{ $records[$i + 1]->departemen}} ({{ $records[$i]->pemegang }})
                       
                    </td>
                    <td width="20%">
                        
                        {!! DNS2D::getBarcodeHTML($records[$i + 1]->kode_brg, 'QRCODE', 3, 3) !!}
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