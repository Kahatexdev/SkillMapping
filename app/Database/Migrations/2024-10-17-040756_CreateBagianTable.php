<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateBagianTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id_bagian' => [
                'type' => 'INT',
                'constraint' => 11,
                'auto_increment' => true,
            ],
            'nama_bagian' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
            ],
            'area_utama' => [
                'type' => 'VARCHAR',
                'constraint' => 20,
            ],
            'area' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
            ],
            'keterangan' => [
                'type' => 'TEXT',
            ],
        ]);
        $this->forge->addKey('id_bagian', true);
        $this->forge->createTable('bagian');
    }

    public function down()
    {
        $this->forge->dropTable('bagian');
    }
}
