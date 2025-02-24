<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class BsMesin extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id_bsmc' => [
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
            'bs_mc' => [
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

        $this->forge->addKey('id_bsmc', true); // Primary Key
        $this->forge->addForeignKey('id_karyawan', 'karyawan', 'id_karyawan', 'CASCADE', 'CASCADE'); // Foreign Key
        $this->forge->createTable('bs_mc');
    }

    public function down()
    {
        $this->forge->dropTable('bs_mc');
    }
}
