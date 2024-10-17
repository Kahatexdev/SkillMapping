<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class JobroleTable extends Migration
{
    public function up(){
    $this->forge->addField([
        'id_jobrole' => [
            'type' => 'INT',
            'constraint' => 11,
            'auto_increment' => true,
        ],
        'id_bagian' => [
            'type' => 'INT',
            'constraint' => 11,
        ],
        'status' => [
            'type' => 'VARCHAR',
            'constraint' => 50,
        ],
        'jobdesc' => [
            'type' => 'TEXT',
            'null' => true,
        ],
    ]);
    $this->forge->addKey('id_jobrole', true);
    $this->forge->addForeignKey('id_bagian', 'bagian', 'id_bagian', 'CASCADE', 'CASCADE');
    $this->forge->createTable('job_role');
}

public function down()
{
    $this->forge->dropTable('job_role');
}}
