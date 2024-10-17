<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class UserTableModify extends Migration
{
    public function up()
    {
        $this->forge->dropColumn('user', 'email');
        $fields = [
            'role' => [
                'type' => 'Enum',
                'type' => 'ENUM',
                'constraint' => ['monitoring', 'mandor'],
                'default' => 'mandor',
            ]
        ];
        $this->forge->addColumn('user', $fields);
    }

    public function down()
    {
        $this->forge->dropColumn('user', 'role');
    }
}
