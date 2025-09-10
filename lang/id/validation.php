<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Baris Bahasa Validasi
    |--------------------------------------------------------------------------
    |
    | Baris bahasa berikut berisi pesan kesalahan default yang digunakan oleh
    | kelas validator. Beberapa aturan memiliki beberapa versi, seperti aturan
    | ukuran. Silakan sesuaikan pesan-pesan ini sesuai kebutuhan aplikasi Anda.
    |
    */

    'accepted'             => 'Kolom :attribute harus diterima.',
    'accepted_if'          => 'Kolom :attribute harus diterima ketika :other bernilai :value.',
    'active_url'           => 'Kolom :attribute harus berupa URL yang valid.',
    'after'                => 'Kolom :attribute harus berupa tanggal setelah :date.',
    'after_or_equal'       => 'Kolom :attribute harus berupa tanggal setelah atau sama dengan :date.',
    'alpha'                => 'Kolom :attribute hanya boleh berisi huruf.',
    'alpha_dash'           => 'Kolom :attribute hanya boleh berisi huruf, angka, tanda hubung, dan garis bawah.',
    'alpha_num'            => 'Kolom :attribute hanya boleh berisi huruf dan angka.',
    'any_of'               => 'Kolom :attribute tidak valid.',
    'array'                => 'Kolom :attribute harus berupa array.',
    'ascii'                => 'Kolom :attribute hanya boleh berisi karakter alfanumerik dan simbol single-byte.',
    'before'               => 'Kolom :attribute harus berupa tanggal sebelum :date.',
    'before_or_equal'      => 'Kolom :attribute harus berupa tanggal sebelum atau sama dengan :date.',
    'between' => [
        'array'   => 'Kolom :attribute harus memiliki antara :min sampai :max item.',
        'file'    => 'Kolom :attribute harus berukuran antara :min sampai :max kilobita.',
        'numeric' => 'Kolom :attribute harus bernilai antara :min sampai :max.',
        'string'  => 'Kolom :attribute harus berisi antara :min sampai :max karakter.',
    ],
    'boolean'              => 'Kolom :attribute harus bernilai benar atau salah.',
    'can'                  => 'Kolom :attribute mengandung nilai yang tidak diizinkan.',
    'confirmed'            => 'Konfirmasi kolom :attribute tidak cocok.',
    'contains'             => 'Kolom :attribute tidak memiliki nilai yang diperlukan.',
    'current_password'     => 'Kata sandi tidak sesuai.',
    'date'                 => 'Kolom :attribute harus berupa tanggal yang valid.',
    'date_equals'          => 'Kolom :attribute harus berupa tanggal yang sama dengan :date.',
    'date_format'          => 'Kolom :attribute harus sesuai dengan format :format.',
    'decimal'              => 'Kolom :attribute harus memiliki :decimal angka desimal.',
    'declined'             => 'Kolom :attribute harus ditolak.',
    'declined_if'          => 'Kolom :attribute harus ditolak ketika :other bernilai :value.',
    'different'            => 'Kolom :attribute dan :other harus berbeda.',
    'digits'               => 'Kolom :attribute harus terdiri dari :digits digit.',
    'digits_between'       => 'Kolom :attribute harus terdiri dari :min sampai :max digit.',
    'dimensions'           => 'Kolom :attribute memiliki dimensi gambar yang tidak valid.',
    'distinct'             => 'Kolom :attribute memiliki nilai duplikat.',
    'doesnt_contain'       => 'Kolom :attribute tidak boleh berisi salah satu dari: :values.',
    'doesnt_end_with'      => 'Kolom :attribute tidak boleh diakhiri dengan salah satu dari: :values.',
    'doesnt_start_with'    => 'Kolom :attribute tidak boleh diawali dengan salah satu dari: :values.',
    'email'                => 'Kolom :attribute harus berupa alamat email yang valid.',
    'ends_with'            => 'Kolom :attribute harus diakhiri dengan salah satu dari: :values.',
    'enum'                 => 'Pilihan :attribute tidak valid.',
    'exists'               => 'Pilihan :attribute tidak valid.',
    'extensions'           => 'Kolom :attribute harus memiliki ekstensi: :values.',
    'file'                 => 'Kolom :attribute harus berupa berkas.',
    'filled'               => 'Kolom :attribute harus memiliki nilai.',
    'gt' => [
        'array'   => 'Kolom :attribute harus memiliki lebih dari :value item.',
        'file'    => 'Kolom :attribute harus lebih besar dari :value kilobita.',
        'numeric' => 'Kolom :attribute harus lebih besar dari :value.',
        'string'  => 'Kolom :attribute harus lebih panjang dari :value karakter.',
    ],
    'gte' => [
        'array'   => 'Kolom :attribute harus memiliki :value item atau lebih.',
        'file'    => 'Kolom :attribute harus lebih besar atau sama dengan :value kilobita.',
        'numeric' => 'Kolom :attribute harus lebih besar atau sama dengan :value.',
        'string'  => 'Kolom :attribute harus lebih panjang atau sama dengan :value karakter.',
    ],
    'hex_color'            => 'Kolom :attribute harus berupa warna heksadesimal yang valid.',
    'image'                => 'Kolom :attribute harus berupa gambar.',
    'in'                   => 'Pilihan :attribute tidak valid.',
    'in_array'             => 'Kolom :attribute harus ada di dalam :other.',
    'in_array_keys'        => 'Kolom :attribute harus mengandung salah satu kunci berikut: :values.',
    'integer'              => 'Kolom :attribute harus berupa bilangan bulat.',
    'ip'                   => 'Kolom :attribute harus berupa alamat IP yang valid.',
    'ipv4'                 => 'Kolom :attribute harus berupa alamat IPv4 yang valid.',
    'ipv6'                 => 'Kolom :attribute harus berupa alamat IPv6 yang valid.',
    'json'                 => 'Kolom :attribute harus berupa string JSON yang valid.',
    'list'                 => 'Kolom :attribute harus berupa daftar.',
    'lowercase'            => 'Kolom :attribute harus berupa huruf kecil.',
    'lt' => [
        'array'   => 'Kolom :attribute harus memiliki kurang dari :value item.',
        'file'    => 'Kolom :attribute harus lebih kecil dari :value kilobita.',
        'numeric' => 'Kolom :attribute harus lebih kecil dari :value.',
        'string'  => 'Kolom :attribute harus kurang dari :value karakter.',
    ],
    'lte' => [
        'array'   => 'Kolom :attribute tidak boleh memiliki lebih dari :value item.',
        'file'    => 'Kolom :attribute harus lebih kecil atau sama dengan :value kilobita.',
        'numeric' => 'Kolom :attribute harus lebih kecil atau sama dengan :value.',
        'string'  => 'Kolom :attribute harus kurang atau sama dengan :value karakter.',
    ],
    'mac_address'          => 'Kolom :attribute harus berupa alamat MAC yang valid.',
    'max' => [
        'array'   => 'Kolom :attribute tidak boleh memiliki lebih dari :max item.',
        'file'    => 'Kolom :attribute tidak boleh lebih besar dari :max kilobita.',
        'numeric' => 'Kolom :attribute tidak boleh lebih besar dari :max.',
        'string'  => 'Kolom :attribute tidak boleh lebih dari :max karakter.',
    ],
    'max_digits'           => 'Kolom :attribute tidak boleh lebih dari :max digit.',
    'mimes'                => 'Kolom :attribute harus berupa berkas dengan tipe: :values.',
    'mimetypes'            => 'Kolom :attribute harus berupa berkas dengan tipe: :values.',
    'min' => [
        'array'   => 'Kolom :attribute harus memiliki minimal :min item.',
        'file'    => 'Kolom :attribute harus berukuran minimal :min kilobita.',
        'numeric' => 'Kolom :attribute harus bernilai minimal :min.',
        'string'  => 'Kolom :attribute harus berisi minimal :min karakter.',
    ],
    'min_digits'           => 'Kolom :attribute harus memiliki minimal :min digit.',
    'missing'              => 'Kolom :attribute harus tidak ada.',
    'missing_if'           => 'Kolom :attribute harus tidak ada ketika :other bernilai :value.',
    'missing_unless'       => 'Kolom :attribute harus tidak ada kecuali :other bernilai :value.',
    'missing_with'         => 'Kolom :attribute harus tidak ada ketika :values ada.',
    'missing_with_all'     => 'Kolom :attribute harus tidak ada ketika :values ada semua.',
    'multiple_of'          => 'Kolom :attribute harus kelipatan dari :value.',
    'not_in'               => 'Pilihan :attribute tidak valid.',
    'not_regex'            => 'Format kolom :attribute tidak valid.',
    'numeric'              => 'Kolom :attribute harus berupa angka.',
    'password' => [
        'letters'       => 'Kolom :attribute harus mengandung minimal satu huruf.',
        'mixed'         => 'Kolom :attribute harus mengandung minimal satu huruf besar dan satu huruf kecil.',
        'numbers'       => 'Kolom :attribute harus mengandung minimal satu angka.',
        'symbols'       => 'Kolom :attribute harus mengandung minimal satu simbol.',
        'uncompromised' => 'Kolom :attribute yang diberikan muncul dalam kebocoran data. Silakan pilih :attribute lain.',
    ],
    'present'              => 'Kolom :attribute harus ada.',
    'present_if'           => 'Kolom :attribute harus ada ketika :other bernilai :value.',
    'present_unless'       => 'Kolom :attribute harus ada kecuali :other bernilai :value.',
    'present_with'         => 'Kolom :attribute harus ada ketika :values ada.',
    'present_with_all'     => 'Kolom :attribute harus ada ketika :values ada semua.',
    'prohibited'           => 'Kolom :attribute dilarang diisi.',
    'prohibited_if'        => 'Kolom :attribute dilarang diisi ketika :other bernilai :value.',
    'prohibited_if_accepted' => 'Kolom :attribute dilarang diisi ketika :other diterima.',
    'prohibited_if_declined' => 'Kolom :attribute dilarang diisi ketika :other ditolak.',
    'prohibited_unless'    => 'Kolom :attribute dilarang diisi kecuali :other ada di :values.',
    'prohibits'            => 'Kolom :attribute melarang :other untuk ada.',
    'regex'                => 'Format kolom :attribute tidak valid.',
    'required'             => 'Kolom :attribute wajib diisi.',
    'required_array_keys'  => 'Kolom :attribute harus memiliki entri untuk: :values.',
    'required_if'          => 'Kolom :attribute wajib diisi ketika :other bernilai :value.',
    'required_if_accepted' => 'Kolom :attribute wajib diisi ketika :other diterima.',
    'required_if_declined' => 'Kolom :attribute wajib diisi ketika :other ditolak.',
    'required_unless'      => 'Kolom :attribute wajib diisi kecuali :other ada di :values.',
    'required_with'        => 'Kolom :attribute wajib diisi ketika :values ada.',
    'required_with_all'    => 'Kolom :attribute wajib diisi ketika :values ada semua.',
    'required_without'     => 'Kolom :attribute wajib diisi ketika :values tidak ada.',
    'required_without_all' => 'Kolom :attribute wajib diisi ketika tidak ada satupun dari :values ada.',
    'same'                 => 'Kolom :attribute dan :other harus sama.',
    'size' => [
        'array'   => 'Kolom :attribute harus berisi :size item.',
        'file'    => 'Kolom :attribute harus berukuran :size kilobita.',
        'numeric' => 'Kolom :attribute harus bernilai :size.',
        'string'  => 'Kolom :attribute harus berisi :size karakter.',
    ],
    'starts_with'          => 'Kolom :attribute harus diawali dengan salah satu dari: :values.',
    'string'               => 'Kolom :attribute harus berupa teks.',
    'timezone'             => 'Kolom :attribute harus berupa zona waktu yang valid.',
    'unique'               => ':attribute sudah digunakan.',
    'uploaded'             => 'Gagal mengunggah :attribute.',
    'uppercase'            => 'Kolom :attribute harus berupa huruf besar.',
    'url'                  => 'Kolom :attribute harus berupa URL yang valid.',
    'ulid'                 => 'Kolom :attribute harus berupa ULID yang valid.',
    'uuid'                 => 'Kolom :attribute harus berupa UUID yang valid.',

    /*
    |--------------------------------------------------------------------------
    | Pesan Validasi Kustom
    |--------------------------------------------------------------------------
    |
    | Anda dapat menentukan pesan validasi khusus untuk atribut tertentu
    | menggunakan format "attribute.rule" untuk menamainya. Hal ini memudahkan
    | Anda menentukan pesan khusus untuk aturan validasi tertentu.
    |
    */

    'custom' => [
        'attribute-name' => [
            'rule-name' => 'pesan-khusus',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Atribut Validasi Kustom
    |--------------------------------------------------------------------------
    |
    | Baris berikut digunakan untuk mengganti placeholder atribut dengan sesuatu
    | yang lebih ramah pembaca, seperti "Alamat Email" alih-alih "email".
    | Ini membantu membuat pesan validasi lebih jelas.
    |
    */

    'attributes' => [],

];
