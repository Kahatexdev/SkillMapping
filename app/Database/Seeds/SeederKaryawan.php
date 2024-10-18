<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class SeederKaryawan extends Seeder
{
    public function run()
    {
        $data = [
            ['id_karyawan' => 'K001',
            'kode_kartu' => 'KKA01',
            'nama_karyawan' => 'Budi',
            'tanggal_masuk' => '2021-01-01',
            'jenis_kelamin' => 'L',
            'shift' => 'A',
            'id_bagian' => '1'],
            
            [
            'id_karyawan' => 'K002',
            'kode_kartu' => 'KKA02',
            'nama_karyawan' => 'tes',
            'tanggal_masuk' => '2021-01-02',
            'jenis_kelamin' => 'P',
            'shift' => 'B',
            'id_bagian' => '2',]
        ];
            
        $this->db->table('karyawan')->insertBatch($data);
    }
}