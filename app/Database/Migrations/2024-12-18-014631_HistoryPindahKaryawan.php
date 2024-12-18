<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class HistoryPindahKaryawan extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id_pindah' => [
                'type' => 'INT',
                'constraint' => 11,
                'auto_increment' => true,
            ],
            'id_karyawan' => [
                'type' => 'INT',
                'constraint' => 11,
            ],
            'id_bagian_asal' => [
                'type' => 'INT',
                'constraint' => 11,
            ],
            'id_bagian_baru' => [
                'type' => 'INT',
                'constraint' => 11,
            ],
            'tgl_pindah' => [
                'type' => 'DATETIME',
            ],
            'keterangan' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'updated_by' => [
                'type' => 'INT',
                'constraint' => 11,
            ]
        ]);

        // Primary Key
        $this->forge->addKey('id_pindah', true);

        // Foreign Key ke tabel karyawan
        $this->forge->addForeignKey(
            'id_karyawan',       // Kolom di tabel ini
            'karyawan',          // Tabel tujuan
            'id_karyawan',       // Kolom di tabel tujuan
            'RESTRICT',          // ON DELETE RESTRICT
            'CASCADE'            // ON UPDATE CASCADE
        );

        // Foreign Key untuk id_bagian_asal
        $this->forge->addForeignKey(
            'id_bagian_asal',
            'bagian',
            'id_bagian',
            'RESTRICT',          // ON DELETE RESTRICT
            'NO ACTION'          // ON UPDATE NO ACTION
        );

        // Foreign Key untuk id_bagian_baru
        $this->forge->addForeignKey(
            'id_bagian_baru',
            'bagian',
            'id_bagian',
            'RESTRICT',          // ON DELETE RESTRICT
            'NO ACTION'          // ON UPDATE NO ACTION
        );

        // Foreign Key untuk updated_by
        $this->forge->addForeignKey(
            'updated_by',
            'user',
            'id_user',
            'RESTRICT',          // ON DELETE RESTRICT
            'NO ACTION'          // ON UPDATE NO ACTION
        );

        // Membuat tabel
        $this->forge->createTable('history_pindah_karyawan');
    }

    public function down()
    {
        $this->forge->dropTable('history_pindah_karyawan');
    }
}
