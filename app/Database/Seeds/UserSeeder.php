<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class UserSeeder extends Seeder
{
    public function run()
    {
        $data = [
            [
                'username' => 'monitoring',
                'password' => 'monitoring',
                'role' => 'Monitoring'
            ],
            [
                'username' => 'mandor',
                'password' => 'mandor',
                'role' => 'Mandor'
            ],
            [
                'username' => 'trainingschool',
                'password' => 'trainingschool',
                'role' => 'TrainingSchool'
            ],
        ];
        $this->db->table('user')->insertBatch($data);
    }
}
