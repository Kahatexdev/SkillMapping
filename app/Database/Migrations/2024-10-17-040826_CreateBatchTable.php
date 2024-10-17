<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateBatchTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id_batch' => [
                'type' => 'INT',
                'constraint' => 11,
                'auto_increment' => true,
            ],
            'nama_batch' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
            ],
            'tahun' => [
                'type' => 'YEAR',
            ],
            'keterangan' => [
                'type' => 'TEXT',
                'null' => true,
            ],
        ]);
        $this->forge->addKey('id_batch', true);
        $this->forge->createTable('batch');
    }

    public function down()
    {
        $this->forge->dropTable('batch');
    }
}
