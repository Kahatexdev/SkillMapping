<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateMessage extends Migration
{
    public function up()
    {
        // Create messages table
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'sender_id' => [
                'type'       => 'INT',
                'constraint' => 11,
            ],
            'receiver_id' => [
                'type'       => 'INT',
                'constraint' => 11,
            ],
            'message' => [
                'type'       => 'TEXT',
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('sender_id', 'user', 'id_user', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('receiver_id', 'user', 'id_user', 'CASCADE', 'CASCADE');
        $this->forge->createTable('messages');
    }

    public function down()
    {
        $this->forge->dropTable('messages');
    }
}
