<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class PeriodeTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id_periode' => [
                'type' => 'INT',
                'constraint' => 11,
                'auto_increment' => true,
            ],
            'nama_periode' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
            ],
            'id_batch' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
            ],
            'start_date' => [
                'type' => 'DATE',
            ],
            'end_date' => [
                'type' => 'DATE',
            ],
        ]);

        // Tambah Primary Key
        $this->forge->addKey('id_periode', true);

        // Tambah Foreign Key
        $this->forge->addForeignKey('id_batch', 'batch', 'id_batch', 'CASCADE', 'CASCADE');

        // Buat tabel 'periode'
        $this->forge->createTable('periode');
    }

    public function down()
    {
        // Drop tabel jika dihapus
        $this->forge->dropTable('periode');
    }
}
