<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddAreaInRosso extends Migration
{
    public function up()
    {
        $this->forge->addColumn('sum_rosso', [
            'area' => [
                'type' => 'varchar',
                'constraint' => 12,
            ],
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('sum_rosso', 'area');
    }
}
