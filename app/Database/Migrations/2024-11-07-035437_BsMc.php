<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class BsMc extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id_bsmc' => [
                'type' => 'INT',
                'constraint' => 11,
                'auto_increment' => true,
            ],
            'id_karyawan' => [
                'type' => 'INT',
                'constraint' => 11,
            ],
            'id_periode' => [
                'type' => 'INT',
                'constraint' => 11,
            ],
            'average_produksi' => [
                'type' => 'INT',
                'constraint' => 11,
            ],
            'average_bs' => [
                'type' => 'INT',
                'constraint' => 11,
            ],
            'created_at' => [
                'type' => 'DATETIME'
            ],
            'updated_at' => [
                'type' => 'DATETIME'
            ],

        ]);
        $this->forge->addKey('id_bsmc', true);
        $this->forge->addForeignKey('id_karyawan', 'karyawan', 'id_karyawan', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('id_periode', 'periode', 'id_periode', 'CASCADE', 'CASCADE');
        $this->forge->createTable('bs_mesin');
    }

    public function down()
    {
        $this->forge->dropTable('bs_mesin');
    }
}
