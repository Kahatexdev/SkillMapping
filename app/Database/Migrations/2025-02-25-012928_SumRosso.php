<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class SumRosso extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id_sr' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'id_karyawan' => [
                'type'       => 'INT',
                'constraint' => 11,
            ],
            'tgl_input' => [
                'type' => 'DATE',
            ],
            'produksi' => [
                'type'       => 'FLOAT',
            ],
            'perbaikan' => [
                'type'       => 'FLOAT',
            ],
            'created_at' => [
                'type'    => 'DATETIME',
                'null'    => true,
            ],
            'updated_at' => [
                'type'    => 'DATETIME',
                'null'    => true,
            ],
        ]);

        $this->forge->addPrimaryKey('id_sr');
        $this->forge->addForeignKey('id_karyawan', 'karyawan', 'id_karyawan', 'CASCADE', 'CASCADE');
        $this->forge->createTable('sum_rosso');
    }

    public function down()
    {
        $this->forge->dropTable('sum_rosso');
    }
}
