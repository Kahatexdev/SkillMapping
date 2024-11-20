<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class SummaryRosso extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id_sr' => [
                'type' => 'INT',
                'constraint' => 11,
                'auto_increment' => true,
            ],
            'id_karyawan' => [
                'type' => 'INT',
                'constraint' => 11,
            ],
            'tgl_prod_rosso' => [
                'type' => 'DATE',
            ],
            'qty_prod_rosso' => [
                'type' => 'INT',
                'constraint' => 11,
            ],
            'qty_bs' => [
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
        $this->forge->addKey('id_sr', true);
        $this->forge->addForeignKey('id_karyawan', 'karyawan', 'id_karyawan', 'CASCADE', 'CASCADE');
        $this->forge->createTable('summary_rosso');
    }

    public function down()
    {
        $this->forge->dropTable('summary_rosso');
    }
}
