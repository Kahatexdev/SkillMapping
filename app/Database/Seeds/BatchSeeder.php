<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class BatchSeeder extends Seeder
{
    public function run()
    {
        // Define sample data for the batches
        $data = [
            [
                'nama_batch' => 'BATCH 1 2024',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'nama_batch' => 'BATCH 2 2024',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'nama_batch' => 'BATCH 3 2024',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
        ];

        // Insert data into the database
        $this->db->table('batch')->insertBatch($data);
    }
}
