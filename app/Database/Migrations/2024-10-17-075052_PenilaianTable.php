<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class PenilaianTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id_penilaian' => [
                'type' => 'INT',
                'constraint' => 11,
                'auto_increment' => true,
            ],
            'karyawan_id' => [
                'type' => 'INT',
                'constraint' => 11,
            ],
            'id_batch' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
            ],
            'bobot_nilai' => [
                'type' => 'INT',
                'constraint' => 11,
            ],
            'index_nilai' => [
                'type' => 'varchar',
                'constraint' => 11,
            ],
            'tanggal_penilaian' => [
                'type' => 'DATE',
            ],
            'keterangan' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'id_user' => [
                'type' => 'INT',
                'constraint' => 11,
            ],
            'id_jobrole' => [
                'type' => 'INT',
                'constraint' => 11,
            ]
        ]);
        $this->forge->addKey('id_penilaian', true);
        $this->forge->addForeignKey('karyawan_id', 'karyawan', 'id_karyawan', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('id_batch', 'batch', 'id_batch', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('id_user', 'user', 'id_user', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('id_jobrole', 'job_role', 'id_jobrole', 'CASCADE', 'CASCADE');
        $this->forge->createTable('penilaian');
    }

    public function down()
    {
        $this->forge->dropTable('penilaian');
    }
}
