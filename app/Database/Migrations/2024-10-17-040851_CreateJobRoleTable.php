<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateJobRoleTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id_job_role' => [
                'type' => 'INT',
                'constraint' => 11,
                'auto_increment' => true,
            ],
            'nama_job_role' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
            ],
            'keterangan' => [
                'type' => 'TEXT',
                'null' => true,
            ],
        ]);
        $this->forge->addKey('id_job_role', true);
        $this->forge->createTable('job_role');
    }

    public function down()
    {
        $this->forge->dropTable('job_role');
    }
}
