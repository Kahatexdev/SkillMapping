<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateKaryawanTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id_karyawan' => [
                'type' => 'INT',
                'constraint' => 11,
                'auto_increment' => true,
            ],
            'nama_karyawan' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
            ],
            'job_role_id' => [
                'type' => 'INT',
                'constraint' => 11,
            ],
            'batch_id' => [
                'type' => 'INT',
                'constraint' => 11,
            ],
            'tanggal_masuk' => [
                'type' => 'DATE',
            ],
            'keterangan' => [
                'type' => 'TEXT',
                'null' => true,
            ],
        ]);
        $this->forge->addKey('id_karyawan', true);
        $this->forge->addForeignKey('job_role_id', 'job_role', 'id_job_role', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('batch_id', 'batch', 'id_batch', 'CASCADE', 'CASCADE');
        $this->forge->createTable('karyawan');
    }

    public function down()
    {
        $this->forge->dropTable('karyawan');
    }
}
