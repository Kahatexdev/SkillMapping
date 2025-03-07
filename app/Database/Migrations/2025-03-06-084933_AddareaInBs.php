<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddareaInBs extends Migration
{
    public function up()
    {
        $this->forge->addColumn('bs_mc', [
            'area' => [
                'type' => 'varchar',
                'constraint' => 12,
           
            ],
        ]);
    }

    public function down()
    {

        $this->forge->dropColumn('bs_mc','area');
      
    }
}
