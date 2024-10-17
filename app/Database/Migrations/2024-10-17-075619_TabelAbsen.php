<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class TabelAbsen extends Migration
{
    
    public function up()
    {
        
        $this->forge->addField([
        'id_absen' => [
            'type' => 'INT',
            'constraint' => 11,
            'auto_increment' => true,
        ],
          'id_karyawan' => [
                'type' => 'INT',
                'constraint' => 11,
            ],
        'tanggal' => [
            'type' => 'date',
        ],
        'ket_absen' => [
            'type' => 'VARCHAR',
            'constraint' => 50,
        ],
       'id_user' => [
                'type' => 'INT',
                'constraint' => 11,]
    ]);
    $this->forge->addKey('id_absen', true);
    $this->forge->addForeignKey('id_karyawan', 'karyawan', 'id_karyawan', 'CASCADE', 'CASCADE');
    $this->forge->addForeignKey('id_user', 'user', 'id_user', 'CASCADE', 'CASCADE');

    $this->forge->createTable('absen');
}

public function down()
{
   $this->forge->dropTable('absen');
}
}
