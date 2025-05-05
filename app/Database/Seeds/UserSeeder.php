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
                'username' => 'Alfa',
                'password' => 'monitoring',
                'role' => 'Monitoring'
            ],
            [
                'username' => 'Ferawati',
                'password' => 'monitoring',
                'role' => 'Monitoring'
            ],
            [
                'username' => 'Jasi',
                'password' => 'trainingschool',
                'role' => 'TrainingSchool'
            ],
            [
                'username' => 'KK1A',
                'password' => 'mandor',
                'role' => 'Mandor'
            ],
            [
                'username' => 'KK1B',
                'password' => 'mandor',
                'role' => 'Mandor'
            ],
            [
                'username' => 'KK2A',
                'password' => 'mandor',
                'role' => 'Mandor'
            ],
            [
                'username' => 'KK2B',
                'password' => 'mandor',
                'role' => 'Mandor'
            ],
            [
                'username' => 'KK2C',
                'password' => 'mandor',
                'role' => 'Mandor'
            ],
            [
                'username' => 'KK5',
                'password' => 'mandor',
                'role' => 'Mandor'
            ],
            [
                'username' => 'KK7K',
                'password' => 'mandor',
                'role' => 'Mandor'
            ],
            [
                'username' => 'KK7L',
                'password' => 'mandor',
                'role' => 'Mandor'
            ],
            [
                'username' => 'KK8D',
                'password' => 'mandor',
                'role' => 'Mandor'
            ],
            [
                'username' => 'KK8F',
                'password' => 'mandor',
                'role' => 'Mandor'
            ],
            [
                'username' => 'KK8J',
                'password' => 'mandor',
                'role' => 'Mandor'
            ],
            [
                'username' => 'KK9',
                'password' => 'mandor',
                'role' => 'Mandor'
            ],
            [
                'username' => 'KK10',
                'password' => 'mandor',
                'role' => 'Mandor'
            ],
            [
                'username' => 'KK11',
                'password' => 'mandor',
                'role' => 'Mandor'
            ]
        ];
        $this->db->table('user')->insertBatch($data);
    }
}
