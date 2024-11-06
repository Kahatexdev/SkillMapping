<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class BagianSeeder extends Seeder
{
    public function run()
    {
        $data = [
            ['nama_bagian' => 'KNITTER', 'area_utama' => 'KK1', 'area' => 'KK1A', 'keterangan' => ''],
            ['nama_bagian' => 'KNITTER', 'area_utama' => 'KK1', 'area' => 'KK1B', 'keterangan' => ''],
            ['nama_bagian' => 'KNITTER', 'area_utama' => 'KK2', 'area' => 'KK2A', 'keterangan' => ''],
            ['nama_bagian' => 'KNITTER', 'area_utama' => 'KK2', 'area' => 'KK2B', 'keterangan' => ''],
            ['nama_bagian' => 'KNITTER', 'area_utama' => 'KK2', 'area' => 'KK2C', 'keterangan' => ''],
            ['nama_bagian' => 'KNITTER', 'area_utama' => 'KK5', 'area' => 'KK5', 'keterangan' => ''],
            ['nama_bagian' => 'KNITTER', 'area_utama' => 'KK7', 'area' => 'KK7K', 'keterangan' => ''],
            ['nama_bagian' => 'KNITTER', 'area_utama' => 'KK7', 'area' => 'KK7L', 'keterangan' => ''],
            ['nama_bagian' => 'KNITTER', 'area_utama' => 'KK8', 'area' => 'KK8D', 'keterangan' => ''],
            ['nama_bagian' => 'KNITTER', 'area_utama' => 'KK8', 'area' => 'KK8F', 'keterangan' => ''],
            ['nama_bagian' => 'KNITTER', 'area_utama' => 'KK8', 'area' => 'KK8J', 'keterangan' => ''],
            ['nama_bagian' => 'KNITTER', 'area_utama' => 'KK8', 'area' => 'KK9', 'keterangan' => ''],
            // MONTIR
            ['nama_bagian' => 'MONTIR', 'area_utama' => 'KK1', 'area' => 'KK1A', 'keterangan' => ''],
            ['nama_bagian' => 'MONTIR', 'area_utama' => 'KK1', 'area' => 'KK1B', 'keterangan' => ''],
            ['nama_bagian' => 'MONTIR', 'area_utama' => 'KK2', 'area' => 'KK2A', 'keterangan' => ''],
            ['nama_bagian' => 'MONTIR', 'area_utama' => 'KK2', 'area' => 'KK2B', 'keterangan' => ''],
            ['nama_bagian' => 'MONTIR', 'area_utama' => 'KK2', 'area' => 'KK2C', 'keterangan' => ''],
            ['nama_bagian' => 'MONTIR', 'area_utama' => 'KK5', 'area' => 'KK5', 'keterangan' => ''],
            ['nama_bagian' => 'MONTIR', 'area_utama' => 'KK7', 'area' => 'KK7K', 'keterangan' => ''],
            ['nama_bagian' => 'MONTIR', 'area_utama' => 'KK7', 'area' => 'KK7L', 'keterangan' => ''],
            ['nama_bagian' => 'MONTIR', 'area_utama' => 'KK8', 'area' => 'KK8D', 'keterangan' => ''],
            ['nama_bagian' => 'MONTIR', 'area_utama' => 'KK8', 'area' => 'KK8F', 'keterangan' => ''],
            ['nama_bagian' => 'MONTIR', 'area_utama' => 'KK8', 'area' => 'KK8J', 'keterangan' => ''],
            ['nama_bagian' => 'MONTIR', 'area_utama' => 'KK8', 'area' => 'KK9', 'keterangan' => ''],
            // ROSSO
            ['nama_bagian' => 'ROSSO', 'area_utama' => 'KK1', 'area' => 'KK1A', 'keterangan' => ''],
            ['nama_bagian' => 'ROSSO', 'area_utama' => 'KK1', 'area' => 'KK1B', 'keterangan' => ''],
            ['nama_bagian' => 'ROSSO', 'area_utama' => 'KK2', 'area' => 'KK2A', 'keterangan' => ''],
            ['nama_bagian' => 'ROSSO', 'area_utama' => 'KK2', 'area' => 'KK2B', 'keterangan' => ''],
            ['nama_bagian' => 'ROSSO', 'area_utama' => 'KK2', 'area' => 'KK2C', 'keterangan' => ''],
            ['nama_bagian' => 'ROSSO', 'area_utama' => 'KK5', 'area' => 'KK5', 'keterangan' => ''],
            ['nama_bagian' => 'ROSSO', 'area_utama' => 'KK7', 'area' => 'KK7K', 'keterangan' => ''],
            ['nama_bagian' => 'ROSSO', 'area_utama' => 'KK7', 'area' => 'KK7L', 'keterangan' => ''],
            ['nama_bagian' => 'ROSSO', 'area_utama' => 'KK8', 'area' => 'KK8D', 'keterangan' => ''],
            ['nama_bagian' => 'ROSSO', 'area_utama' => 'KK8', 'area' => 'KK8F', 'keterangan' => ''],
            ['nama_bagian' => 'ROSSO', 'area_utama' => 'KK8', 'area' => 'KK8J', 'keterangan' => ''],
            ['nama_bagian' => 'ROSSO', 'area_utama' => 'KK8', 'area' => 'KK9', 'keterangan' => ''],
            // JAHIT
            ['nama_bagian' => 'JAHIT', 'area_utama' => 'KK1', 'area' => 'KK1A', 'keterangan' => ''],
            ['nama_bagian' => 'JAHIT', 'area_utama' => 'KK1', 'area' => 'KK1B', 'keterangan' => ''],
            ['nama_bagian' => 'JAHIT', 'area_utama' => 'KK2', 'area' => 'KK2A', 'keterangan' => ''],
            ['nama_bagian' => 'JAHIT', 'area_utama' => 'KK2', 'area' => 'KK2B', 'keterangan' => ''],
            ['nama_bagian' => 'JAHIT', 'area_utama' => 'KK2', 'area' => 'KK2C', 'keterangan' => ''],
            ['nama_bagian' => 'JAHIT', 'area_utama' => 'KK5', 'area' => 'KK5', 'keterangan' => ''],
            ['nama_bagian' => 'JAHIT', 'area_utama' => 'KK7', 'area' => 'KK7K', 'keterangan' => ''],
            ['nama_bagian' => 'JAHIT', 'area_utama' => 'KK7', 'area' => 'KK7L', 'keterangan' => ''],
            ['nama_bagian' => 'JAHIT', 'area_utama' => 'KK8', 'area' => 'KK8D', 'keterangan' => ''],
            ['nama_bagian' => 'JAHIT', 'area_utama' => 'KK8', 'area' => 'KK8F', 'keterangan' => ''],
            ['nama_bagian' => 'JAHIT', 'area_utama' => 'KK8', 'area' => 'KK8J', 'keterangan' => ''],
            ['nama_bagian' => 'JAHIT', 'area_utama' => 'KK8', 'area' => 'KK9', 'keterangan' => ''],
            // OBRAS
            ['nama_bagian' => 'OBRAS', 'area_utama' => 'KK1', 'area' => 'KK1A', 'keterangan' => ''],
            ['nama_bagian' => 'OBRAS', 'area_utama' => 'KK1', 'area' => 'KK1B', 'keterangan' => ''],
            ['nama_bagian' => 'OBRAS', 'area_utama' => 'KK2', 'area' => 'KK2A', 'keterangan' => ''],
            ['nama_bagian' => 'OBRAS', 'area_utama' => 'KK2', 'area' => 'KK2B', 'keterangan' => ''],
            ['nama_bagian' => 'OBRAS', 'area_utama' => 'KK2', 'area' => 'KK2C', 'keterangan' => ''],
            ['nama_bagian' => 'OBRAS', 'area_utama' => 'KK5', 'area' => 'KK5', 'keterangan' => ''],
            ['nama_bagian' => 'OBRAS', 'area_utama' => 'KK7', 'area' => 'KK7K', 'keterangan' => ''],
            ['nama_bagian' => 'OBRAS', 'area_utama' => 'KK7', 'area' => 'KK7L', 'keterangan' => ''],
            ['nama_bagian' => 'OBRAS', 'area_utama' => 'KK8', 'area' => 'KK8D', 'keterangan' => ''],
            ['nama_bagian' => 'OBRAS', 'area_utama' => 'KK8', 'area' => 'KK8F', 'keterangan' => ''],
            ['nama_bagian' => 'OBRAS', 'area_utama' => 'KK8', 'area' => 'KK8J', 'keterangan' => ''],
            ['nama_bagian' => 'OBRAS', 'area_utama' => 'KK8', 'area' => 'KK9', 'keterangan' => ''],
            // SEWING
            ['nama_bagian' => 'SEWING', 'area_utama' => 'KK1', 'area' => 'KK1A', 'keterangan' => ''],
            ['nama_bagian' => 'SEWING', 'area_utama' => 'KK1', 'area' => 'KK1B', 'keterangan' => ''],
            ['nama_bagian' => 'SEWING', 'area_utama' => 'KK2', 'area' => 'KK2A', 'keterangan' => ''],
            ['nama_bagian' => 'SEWING', 'area_utama' => 'KK2', 'area' => 'KK2B', 'keterangan' => ''],
            ['nama_bagian' => 'SEWING', 'area_utama' => 'KK2', 'area' => 'KK2C', 'keterangan' => ''],
            ['nama_bagian' => 'SEWING', 'area_utama' => 'KK5', 'area' => 'KK5', 'keterangan' => ''],
            ['nama_bagian' => 'SEWING', 'area_utama' => 'KK7', 'area' => 'KK7K', 'keterangan' => ''],
            ['nama_bagian' => 'SEWING', 'area_utama' => 'KK7', 'area' => 'KK7L', 'keterangan' => ''],
            ['nama_bagian' => 'SEWING', 'area_utama' => 'KK8', 'area' => 'KK8D', 'keterangan' => ''],
            ['nama_bagian' => 'SEWING', 'area_utama' => 'KK8', 'area' => 'KK8F', 'keterangan' => ''],
            ['nama_bagian' => 'SEWING', 'area_utama' => 'KK8', 'area' => 'KK8J', 'keterangan' => ''],
            ['nama_bagian' => 'SEWING', 'area_utama' => 'KK8', 'area' => 'KK9', 'keterangan' => '']
        ];

        $this->db->table('bagian')->insertBatch($data);
    }
}
