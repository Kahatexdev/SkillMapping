<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class SummaryJarum extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id_sj' => [
                'type' => 'INT',
                'constraint' => 11,
                'auto_increment' => true,
            ],
            'id_karyawan' => [
                'type' => 'INT',
                'constraint' => 11,
            ],
            'tanggal' => [
                'type' => 'DATE',
            ],
            'qty_jarum' => [
                'type' => 'INT',
                'constraint' => 11,
            ],
            'created_at' => [
                'type' => 'DATETIME'
            ],
            'updated_at' => [
                'type' => 'DATETIME'
            ],
        ]);
        $this->forge->addKey('id_sj', true);
        $this->forge->addForeignKey('id_karyawan', 'karyawan', 'id_karyawan', 'CASCADE', 'CASCADE');
        $this->forge->createTable('summary_jarum');

    public function down()
    {
        $this->forge->dropTable('summary_jarum');
    }
}
