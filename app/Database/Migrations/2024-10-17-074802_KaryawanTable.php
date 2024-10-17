<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class KaryawanTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id_karyawan' => [
                'type' => 'INT',
                'constraint' => 11,
                'auto_increment' => true,
            ],
            'kode_kartu' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
            ],
            'nama_karyawan' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
            ],
           
            'tanggal_masuk' => [
                'type' => 'DATE',
            ],
            'jenis_kelamin' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
            ],
            'shift' => [
                'type' => 'varchar',
                'constraint' => 50,
            ],
            'id_bagian' => [
                'type' => 'INT',
                'constraint' => 11,
            ],
        ]);
        $this->forge->addKey('id_karyawan', true);
        $this->forge->addForeignKey('id_bagian', 'bagian', 'id_bagian', 'CASCADE', 'CASCADE');
        $this->forge->createTable('karyawan');
    }

    public function down()
    {
        $this->forge->dropTable('karyawan');
    }
}
