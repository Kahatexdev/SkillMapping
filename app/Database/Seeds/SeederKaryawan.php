<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class SeederKaryawan extends Seeder
{
    public function run()
    {
        $data = [
            [
                'id_karyawan' => 'K001',
                'kode_kartu' => 'KKA01',
                'nama_karyawan' => 'Budi',
                'shift' => 'A',
                'jenis_kelamin' => 'L',
                'libur' => 'SABTU',
                'libur_tambahan' => 'MINGGU',
                'warna_baju' => 'BIRU',
                'status_baju' => 'KARYAWAN',
                'tgl_lahir' => '2000-01-01',
                'tgl_masuk' => '2021-01-01',
                'id_bagian' => '1',
                'status_aktif' => 'Aktif',
            ],

            [
                'id_karyawan' => 'K002',
                'kode_kartu' => 'KKA02',
                'nama_karyawan' => 'Mas Alek',
                'shift' => 'B',
                'jenis_kelamin' => 'L',
                'libur' => 'SENIN',
                'libur_tambahan' => 'SELASA',
                'warna_baju' => 'BIRU',
                'status_baju' => 'KARYAWAN',
                'tgl_lahir' => '1995-01-01',
                'tgl_masuk' => '2020-01-01',
                'id_bagian' => '2',
                'status_aktif' => 'Aktif',
            ]
        ];

        $this->db->table('karyawan')->insertBatch($data);
    }
}
