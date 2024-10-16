<?php

namespace App\Models;

use CodeIgniter\Model;

class KaryawanModel extends Model
{
    protected $table            = 'karyawan';
    protected $primaryKey       = 'nik';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = ['nik','kode_kartu','nama_karyawan','jenis_kelamin', 'tgl_masuk', 'shift','id_bagian', 'status'];

    protected bool $allowEmptyInserts = false;
    protected bool $updateOnlyChanged = true;

    protected array $casts = [];
    protected array $castHandlers = [];

    // Dates
    protected $useTimestamps = false;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';

    // Validation
    protected $validationRules      = [];
    protected $validationMessages   = [];
    protected $skipValidation       = false;
    protected $cleanValidationRules = true;

    // Callbacks
    protected $allowCallbacks = true;
    protected $beforeInsert   = [];
    protected $afterInsert    = [];
    protected $beforeUpdate   = [];
    protected $afterUpdate    = [];
    protected $beforeFind     = [];
    protected $afterFind      = [];
    protected $beforeDelete   = [];
    protected $afterDelete    = [];

   
    public function cek_karyawan($nik)
    {
        return $this->select('nik')
            ->where('nik', $nik)
            ->first();
    }
    public function getdata()
    {
        return $this->select('karyawan.nik, karyawan.kode_kartu, karyawan.nama_karyawan, karyawan.jenis_kelamin, karyawan.tgl_masuk, karyawan.shift, karyawan.id_bagian, bagian.nama_bagian')
            ->join('bagian', 'bagian.id_bagian=bagian.id_bagian')
            ->findAll();
    }
}