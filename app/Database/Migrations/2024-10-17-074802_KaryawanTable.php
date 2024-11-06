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
            'shift' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
            ],
            'jenis_kelamin' => [
                'type' => 'ENUM',
                'constraint' => ['P','L'],
            ],
            'libur' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
            ],
            'libur_tambahan' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
            ],
            'warna_baju' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
            ],
            'status_baju' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
            ],
            'tgl_lahir' => [
                'type' => 'DATE'
            ],
            'tgl_masuk' => [
                'type' => 'DATE'
            ],
            'id_bagian' => [
                'type' => 'INT',
                'constraint' => 11,
            ],
            'status_aktif' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
            ],
            'created_at' => [
                'type' => 'DATETIME'
            ],
            'updated_at' => [
                'type' => 'DATETIME'
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
