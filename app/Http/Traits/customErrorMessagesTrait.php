<?php

namespace App\Http\Traits;


trait customErrorMessagesTrait
{
    public static function messages()
    {
        $messages = array(
            'required' => 'Data tidak boleh kosong.', //data tdak boleh kosong
            'digits' => 'Data harus berisi :digits digit.', //untuk angka harus berisi a length
            'digits_between' => 'Data harus berisi antara :min-:max digit.', //untuk angka  antara a,b length
            'min' => ':attribute Data berisi minimal :min digit.', //untuk angka /character min length
            'max' => 'Data berisi maximl :max digit.', //untuk angka /character max length
            'exists' => 'Data tidak ada didalam data master', // mencari referensi pada table tertentu
            'date_format' => 'Format tgl dd/mm/yyyy' //format tgl dd/mm/yyyy
        );

        return $messages;
    }
}
