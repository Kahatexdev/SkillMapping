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

    public function getBagianRosso()
    {
        return $this->select('karyawan.id_karyawan, karyawan.kode_kartu, karyawan.nama_karyawan, karyawan.shift, karyawan.jenis_kelamin, karyawan.libur, karyawan.libur_tambahan, karyawan.warna_baju, karyawan.status_baju, karyawan.tgl_lahir, karyawan.tgl_masuk, karyawan.id_bagian, bagian.nama_bagian, bagian.area_utama, bagian.area, bagian.keterangan, karyawan.status_aktif, karyawan.created_at, karyawan.updated_at')
            ->join('bagian', 'bagian.id_bagian = karyawan.id_bagian')
            // where nama_bagian = rosso
            ->where('bagian.nama_bagian =', 'ROSSO')
            ->findAll();
    }

    public function getIdKaryawan()
    {
        return $this->select('*')
            ->findAll();
    }

    public function findById($id)
    {
        $data = $this->find($id);
        if ($data) {
            return $data;
        }
        return false;
    }
    // get data karyawan by id join bagian dengan parameter nama area_utama
    public function getKaryawanByAreaUtama($area_utama)
    {
        return $this->select('karyawan.id_karyawan, karyawan.kode_kartu, karyawan.nama_karyawan, karyawan.shift, karyawan.jenis_kelamin, karyawan.libur, karyawan.libur_tambahan, karyawan.warna_baju, karyawan.status_baju, karyawan.tgl_lahir, karyawan.tgl_masuk, karyawan.id_bagian, bagian.nama_bagian, bagian.area_utama, bagian.area, bagian.keterangan, karyawan.status_aktif, karyawan.created_at, karyawan.updated_at')
            ->join('bagian', 'bagian.id_bagian = karyawan.id_bagian')
            ->where('bagian.area_utama', $area_utama)
            ->findAll();
    }

    // get data karyawan by id join bagian dengan parameter nama area
    public function getKaryawanByArea($area)
    {
        return $this->select('karyawan.id_karyawan, karyawan.kode_kartu, karyawan.nama_karyawan, karyawan.shift, karyawan.jenis_kelamin, karyawan.libur, karyawan.libur_tambahan, karyawan.warna_baju, karyawan.status_baju, karyawan.tgl_lahir, karyawan.tgl_masuk, karyawan.id_bagian, bagian.nama_bagian, bagian.area_utama, bagian.area, bagian.keterangan, karyawan.status_aktif, karyawan.created_at, karyawan.updated_at')
            ->join('bagian', 'bagian.id_bagian = karyawan.id_bagian')
            ->where('bagian.area', $area)
            ->findAll();
    }

    public function exportKaryawanByArea($area)
    {
        return $this->select('karyawan.id_karyawan, karyawan.kode_kartu, karyawan.nama_karyawan, karyawan.shift, karyawan.jenis_kelamin, karyawan.libur, karyawan.libur_tambahan, karyawan.warna_baju, karyawan.status_baju, karyawan.tgl_lahir, karyawan.tgl_masuk, bagian.nama_bagian, bagian.area_utama, bagian.area, karyawan.status_aktif')
            ->join('bagian', 'bagian.id_bagian = karyawan.id_bagian')
            ->where('bagian.area', $area)
            ->findAll();
    }
    public function getKaryawanTanpaArea()
    {
        return $this->select('karyawan.id_karyawan, karyawan.kode_kartu, karyawan.nama_karyawan, karyawan.shift, karyawan.jenis_kelamin, karyawan.libur, karyawan.libur_tambahan, karyawan.warna_baju, karyawan.status_baju, karyawan.tgl_lahir, karyawan.tgl_masuk, karyawan.id_bagian, bagian.nama_bagian, bagian.area_utama, bagian.area, karyawan.status_aktif, karyawan.created_at, karyawan.updated_at')
            ->join('bagian', 'bagian.id_bagian = karyawan.id_bagian', 'left') // left join untuk menghindari data hilang
            ->where('(bagian.area_utama IS NULL OR bagian.area IS NULL OR bagian.area_utama = "-" OR bagian.area = "-")') // Cek area kosong atau "-"
            ->findAll();
    }

    public function getKaryawanByFilters($nama_bagian, $area_utama, $area)
    {
        return $this->select('karyawan.id_karyawan, karyawan.kode_kartu, karyawan.nama_karyawan, karyawan.shift, karyawan.jenis_kelamin, karyawan.libur, karyawan.libur_tambahan, karyawan.warna_baju, karyawan.status_baju, karyawan.tgl_lahir, karyawan.tgl_masuk, karyawan.id_bagian, bagian.nama_bagian, bagian.area_utama, bagian.area, bagian.keterangan, karyawan.status_aktif, karyawan.created_at, karyawan.updated_at')
            ->join('bagian', 'bagian.id_bagian = karyawan.id_bagian')
            ->where('bagian.nama_bagian', $nama_bagian)
            ->where('bagian.area_utama', $area_utama)
            ->where('bagian.area', $area)
            ->where('karyawan.status_aktif', 'Aktif')
            ->findAll();
    }
}
