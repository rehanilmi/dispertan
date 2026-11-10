<?php
return [
    'array' => ':attribute harus berupa daftar.', 'date' => ':attribute bukan tanggal yang valid.', 'digits' => ':attribute harus :digits digit.',
    'email' => ':attribute harus berupa alamat email yang valid.', 'exists' => 'Pilihan :attribute tidak valid.', 'file' => ':attribute harus berupa berkas.',
    'image' => ':attribute harus berupa gambar (jpg, png, webp, atau gif).', 'in' => 'Pilihan :attribute tidak valid.', 'integer' => ':attribute harus berupa angka bulat.',
    'mimes' => ':attribute harus berformat: :values.', 'numeric' => ':attribute harus berupa angka.', 'regex' => 'Format :attribute tidak valid.',
    'required' => ':attribute wajib diisi.', 'starts_with' => ':attribute harus diawali: :values.', 'string' => ':attribute harus berupa teks.',
    'unique' => ':attribute sudah digunakan.', 'url' => 'Format :attribute tidak valid (harus diawali https://).',
    'uploaded' => ':attribute gagal diunggah. Kemungkinan ukuran berkas melebihi batas server (upload_max_filesize); kecilkan ukuran atau naikkan batas di php.ini.',
    'current_password' => 'Password saat ini salah.', 'confirmed' => 'Konfirmasi :attribute tidak cocok.',
    'between' => ['numeric' => ':attribute harus di antara :min dan :max.', 'file' => ':attribute harus berukuran :min–:max KB.', 'string' => ':attribute harus :min–:max karakter.', 'array' => ':attribute harus berisi :min–:max item.'],
    'max' => ['numeric' => ':attribute tidak boleh lebih dari :max.', 'file' => 'Ukuran :attribute tidak boleh lebih dari :max KB.', 'string' => ':attribute tidak boleh lebih dari :max karakter.', 'array' => ':attribute tidak boleh lebih dari :max item.'],
    'min' => ['numeric' => ':attribute minimal :min.', 'file' => 'Ukuran :attribute minimal :min KB.', 'string' => ':attribute minimal :min karakter.', 'array' => ':attribute minimal :min item.'],
    'attributes' => [],
];
