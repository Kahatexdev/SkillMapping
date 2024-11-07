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
                'role' => 'monitoring'
            ],
            [
                'username' => 'mandor',
                'password' => 'mandor',
                'role' => 'mandor'
            ]
        ];
        $this->db->table('user')->insertBatch($data);
    }
}
