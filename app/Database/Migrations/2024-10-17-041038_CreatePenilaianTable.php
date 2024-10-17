<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreatePenilaianTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id_penilaian' => [
                'type' => 'INT',
                'constraint' => 11,
                'auto_increment' => true,
            ],
            'karyawan_id' => [
                'type' => 'INT',
                'constraint' => 11,
            ],
            'nilai' => [
                'type' => 'FLOAT',
            ],
            'tanggal_penilaian' => [
                'type' => 'DATE',
            ],
            'keterangan' => [
                'type' => 'TEXT',
                'null' => true,
            ],
        ]);
        $this->forge->addKey('id_penilaian', true);
        $this->forge->addForeignKey('karyawan_id', 'karyawan', 'id_karyawan', 'CASCADE', 'CASCADE');
        $this->forge->createTable('penilaian');
    }

    public function down()
    {
        $this->forge->dropTable('penilaian');
    }
}
