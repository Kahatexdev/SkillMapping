<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class AbsenSeeder extends Seeder
{
    public function run()
    {
        $data = [
            [
                'id_karyawan' => 1,
                'tanggal' => '2021-09-01',
                'ket_absen' => 'Tidak ada keterangan',
                'id_user' => '1'
            ],
            
            [
                'id_karyawan' => 2,
                'tanggal' => '2021-09-01',
                'ket_absen' => 'Tidak ada keterangan',
                'id_user' => '2'
            ]
        ];
        $this->db->table('absen')->insertBatch($data);
    }


}