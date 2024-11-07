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
            'tanggal' => [
                'type' => 'DATE',
            ],
            'no_model' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
            ],
            'inisial' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
            ],
            'qty_prod_mc' => [
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
        $this->forge->addKey('id_bsmc', true);
        $this->forge->addForeignKey('id_karyawan', 'karyawan', 'id_karyawan', 'CASCADE', 'CASCADE');
        $this->forge->createTable('bsmc');
    }

    public function down()
    {
        $this->forge->dropTable('bsmc');
    }
}
