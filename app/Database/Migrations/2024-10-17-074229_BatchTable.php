<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class BatchTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id_batch' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true, // Tambah 'unsigned' untuk mendukung auto_increment
                'auto_increment' => true,
            ],
            'shift' => [
                'type' => 'VARCHAR',
                'constraint' => 10,
            ],
            'bulan' => [
                'type' => 'ENUM', // Untuk menyimpan tanggal
                'constraint' => ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'],
            ],
            'tahun' => [
                'type' => 'YEAR',
            ],
            'created_at' => [
                'type' => 'DATETIME',
            ],
            'updated_at' => [
                'type' => 'DATETIME',
            ],
        ]);

        // Tambah Primary Key
        $this->forge->addKey('id_batch', true);

        // Buat tabel 'batch'
        $this->forge->createTable('batch');
    }

    public function down()
    {
        // Drop tabel jika dihapus
        $this->forge->dropTable('batch');
    }
}
