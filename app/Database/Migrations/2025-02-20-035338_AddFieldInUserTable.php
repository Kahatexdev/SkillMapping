<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddFieldInUserTable extends Migration
{
    public function up()
    {
        $this->forge->addColumn('user', [
            'area' => [
                'type' => 'VARCHAR',
                'constraint' => 20,
                'after' => 'role',
            ],
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('user', 'area');
    }
}
