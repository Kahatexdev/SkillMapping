<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class AbsenSeeder extends Seeder
{
    public function run()
    {
        $data = [
            [
                'id_karyawan' => 7,
                'tanggal' => '2021-09-01',
                'ket_absen' => 'Tidak ada keterangan',
                'id_user' => '3'
            ],
            
            [
                'id_karyawan' => 8,
                'tanggal' => '2021-09-01',
                'ket_absen' => 'Tidak ada keterangan',
                'id_user' => '4'
            ]
        ];
        $this->db->table('absen')->insertBatch($data);
    }


}