<?php

/*
|--------------------------------------------------------------------------
| Konfigurasi Form Dinamis LegalFlow
|--------------------------------------------------------------------------
|
| Field form untuk masing-masing jenis pengajuan. Nilai disimpan pada
| tabel application_fields (field_name / field_value).
|
| Tipe field yang didukung view: text, textarea, date, number, select.
| Untuk select, sertakan key "options" => [nilai => label].
|
*/

return [

    'permit_fields' => [
        ['name' => 'nama_pemohon', 'label' => 'Nama Pemohon', 'type' => 'text', 'required' => true],
        ['name' => 'kontak', 'label' => 'No. Telepon / Email', 'type' => 'text', 'required' => true],
        ['name' => 'tujuan', 'label' => 'Tujuan Pengajuan', 'type' => 'textarea', 'required' => true],
        ['name' => 'lokasi', 'label' => 'Lokasi / Objek Izin', 'type' => 'text', 'required' => false],
        ['name' => 'target_selesai', 'label' => 'Target Tanggal Selesai (opsional)', 'type' => 'date', 'required' => false],
        ['name' => 'catatan_legal', 'label' => 'Catatan untuk Legal (opsional)', 'type' => 'textarea', 'required' => false],
    ],

    'agreement_fields' => [
        ['name' => 'jenis_agreement', 'label' => 'Jenis Agreement', 'type' => 'select', 'required' => true, 'options' => [
            'Pengadaan' => 'Pengadaan Barang/Jasa',
            'Kerja Sama' => 'Kerja Sama / Partnership',
            'Kerahasiaan' => 'Kerahasiaan (NDA)',
            'Kerja (Karyawan)' => 'Perjanjian Kerja',
            'Sewa' => 'Sewa / Lease',
            'Lainnya' => 'Lainnya',
        ]],
        ['name' => 'jenis_pihak', 'label' => 'Jenis Pihak (Counterparty)', 'type' => 'select', 'required' => true, 'options' => [
            'Vendor' => 'Vendor / Supplier',
            'Perusahaan' => 'Perusahaan',
            'Perorangan' => 'Perorangan',
            'Lembaga' => 'Lembaga / Instansi',
        ]],
        ['name' => 'nama_counterparty', 'label' => 'Nama Counterparty', 'type' => 'text', 'required' => true],
        ['name' => 'nilai_kontrak', 'label' => 'Nilai Agreement', 'type' => 'text', 'required' => true],
        ['name' => 'mata_uang', 'label' => 'Mata Uang', 'type' => 'select', 'required' => true, 'options' => [
            'IDR' => 'IDR (Rupiah)', 'USD' => 'USD (Dolar AS)', 'EUR' => 'EUR (Euro)', 'SGD' => 'SGD (Dolar Singapura)',
        ]],
        ['name' => 'tanggal_mulai', 'label' => 'Tanggal Mulai', 'type' => 'date', 'required' => true],
        ['name' => 'tanggal_selesai', 'label' => 'Tanggal Berakhir', 'type' => 'date', 'required' => true],
        ['name' => 'pihak_pertama', 'label' => 'Pihak Pertama (Perusahaan)', 'type' => 'text', 'required' => false],
        ['name' => 'ruang_lingkup', 'label' => 'Ruang Lingkup / Ringkasan', 'type' => 'textarea', 'required' => true],
        ['name' => 'catatan_legal', 'label' => 'Catatan untuk Legal (opsional)', 'type' => 'textarea', 'required' => false],
    ],

    // Ambang notifikasi "akan kadaluarsa" (hari)
    'expiring_soon_days' => 30,

];
