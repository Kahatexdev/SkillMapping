<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddFieldInPenilaianTable extends Migration
{
    public function up()
    {
        $this->forge->addColumn('penilaian', [
            'grade_akhir' => [
                'type' => 'VARCHAR',
                'constraint' => 20,
                'after' => 'index_nilai',
            ],
        ]);
    }

    public function down()
    {
        //
    }
}
