<?php

namespace App\Models;

use CodeIgniter\Model;

class KaryawanModel extends Model
{
    protected $table            = 'karyawan';
    protected $primaryKey       = 'id_karyawan';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'id_karyawan',
        'kode_kartu',
        'nama_karyawan',
        'shift',
        'jenis_kelamin',
        'libur',
        'libur_tambahan',
        'warna_baju',
        'status_baju',
        'tgl_lahir',
        'tgl_masuk',
        'id_bagian',
        'status_aktif',
        'created_at',
        'updated_at'
    ];

    protected bool $allowEmptyInserts = false;
    protected bool $updateOnlyChanged = true;

    protected array $casts = [];
    protected array $castHandlers = [];

    // Dates
    protected $useTimestamps = true;
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

    public function getBagian()
    {
        return $this->select('karyawan.id_karyawan, karyawan.kode_kartu, karyawan.nama_karyawan, karyawan.shift, karyawan.jenis_kelamin, karyawan.libur, karyawan.libur_tambahan, karyawan.warna_baju, karyawan.status_baju, karyawan.tgl_lahir, karyawan.tgl_masuk, karyawan.id_bagian, bagian.nama_bagian, bagian.area_utama, bagian.area, bagian.keterangan, karyawan.status_aktif, karyawan.created_at, karyawan.updated_at')
            ->join('bagian', 'bagian.id_bagian = karyawan.id_bagian')
            ->findAll();
    }

    public function getIdKaryawan()
    {
        return $this->select('*')
            ->findAll();
    }
}
