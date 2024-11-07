<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class PenilaianSeeder extends Seeder
{
    public function run()
    {
        $data = [
            [
                'id_penilaian' => 1,
                'karyawan_id' => 1,
                'id_batch' => 1,
                'bobot_nilai' => 85,
                'index_nilai' => 'A',
                'tanggal_penilaian' => '2024-01-01',
                'keterangan' => 'Excellent performance',
                'id_user' => 1,
                'id_jobrole' => 1,
            ],
            [
                'id_penilaian' => 2,
                'karyawan_id' => 2,
                'id_batch' => 1,
                'bobot_nilai' => 70,
                'index_nilai' => 'B',
                'tanggal_penilaian' => '2024-01-02',
                'keterangan' => 'Good performance',
                'id_user' => 1,
                'id_jobrole' => 2,
            ],
            [
                'id_penilaian' => 3,
                'karyawan_id' => 3,
                'id_batch' => 2,
                'bobot_nilai' => 60,
                'index_nilai' => 'C',
                'tanggal_penilaian' => '2024-01-03',
                'keterangan' => 'Average performance',
                'id_user' => 2,
                'id_jobrole' => 1,
            ],
            // Tambahkan data tambahan sesuai kebutuhan
        ];

        // Insert data ke tabel penilaian
        $this->db->table('penilaian')->insertBatch($data);
    }
}
