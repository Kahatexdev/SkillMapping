<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class SumJarum extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id_sj'        => [
                'type' => 'INT',
                'constraint' => 11,
                'auto_increment' => true
            ],
            'id_karyawan'  => [
                'type' => 'INT',
                'constraint' => 11
            ],
            'tgl_input'    => [
                'type' => 'DATE'
            ],
            'used_needle'  => [
                'type' => 'INT',
                'constraint' => 11,
            ],
            'created_at'   => ['type' => 'DATETIME', 'null' => true],
            'updated_at'   => ['type' => 'DATETIME', 'null' => true],
        ]);

        $this->forge->addKey('id_sj', true);
        $this->forge->addForeignKey('id_karyawan', 'karyawan', 'id_karyawan', 'CASCADE', 'CASCADE');

        $this->forge->createTable('sum_jarum');
    }

    public function down()
    {
        $this->forge->dropTable('sum_jarum');
    }
}
