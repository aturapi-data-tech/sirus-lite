<!DOCTYPE html>
<html>

<head>
    <style>
        @page {
            size: 6cm 4cm;
            margin: 2px;
        }
    </style>


    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">



    <link href="build/assets/sirus.css" rel="stylesheet">
</head>

<body>
    <table class="w-full">

        <tr>
            <td class="mr-2 w-44">
                <img src="madinahlogo.png" class="object-fill h-8">
            </td>

            <td>
                <p class="ml-2 text-base font-bold text-start"><span></span></p>
                <p class="ml-2 text-base font-bold text-start"><span></span></p>
            </td>

        </tr>

        {{-- <tr style="background-color : rgb(42, 194, 37)">
            <td ></td>
            <td></td>
        </tr>
        <tr style="background-color : rgb(110, 50, 160)">
            <td ></td>
            <td></td>
        </tr> --}}
    </table>




    <table style="font-size: 10px">
        <tr>
            <td>
                <p><span>RegNo</span></p>
            </td>
            <td>
                <p>
                    <span>:
                        {{ isset($data['regNo']) ? ($data['regNo'] ? $data['regNo'] : '-') : '--' }}
                    </span>
                    <span class="ml-16">

                    </span>
                </p>
            </td>
            <td colspan="2"></td>
            <td></td>
        </tr>
        <tr>
            <td>
                <p><span>Pasien</span></p>
            </td>
            <td>
                <p>
                    <span>:
                        {{ isset($data['regName']) ? ($data['regName'] ? $data['regName'] : '-') : '--' }}
                    </span>
                    <span>/
                        {{ isset($data['jenisKelamin']['jenisKelaminDesc']) ? ($data['jenisKelamin']['jenisKelaminDesc'] ? $data['jenisKelamin']['jenisKelaminDesc'] : '-') : '--' }}
                    </span>
                    <span>/
                        {{ isset($data['thn']) ? ($data['thn'] ? $data['thn'] : '-') : '--' }}
                    </span>
                </p>
            </td>

            <td></td>
        </tr>
        <tr>
            <td>
                <p><span>Alamat</span></p>
            </td>
            <td>
                <p>
                    <span>:
                        {{ isset($data['identitas']['alamat']) ? ($data['identitas']['alamat'] ? $data['identitas']['alamat'] : '-') : '--' }}
                    </span>
                </p>
            </td>
            <td></td>
            <td></td>
            <td></td>
        </tr>

        <tr>
            <td></td>
            <td></td>
            <td></td>
        </tr>
        <tr>
            <td></td>
            <td></td>
            <td></td>
        </tr>
    </table>
</body>

</html>
