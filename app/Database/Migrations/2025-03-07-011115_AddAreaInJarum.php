<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddAreaInJarum extends Migration
{
    public function up()
    {
        $this->forge->addColumn('sum_jarum', [
            'area' => [
                'type' => 'varchar',
                'constraint' => 12,
            ],
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('sum_jarum', 'area');
    }
}
