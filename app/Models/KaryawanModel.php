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
            // ->where('bagian.area_utama', substr($area, 0, -1))
            ->where('bagian.area_utama', $area)
            ->findAll();
    }

    public function exportKaryawanAll()
    {
        // Buat query builder untuk mengambil seluruh data karyawan
        return $this->select('karyawan.id_karyawan, karyawan.kode_kartu, karyawan.nama_karyawan, karyawan.shift, karyawan.jenis_kelamin, karyawan.libur, karyawan.libur_tambahan, karyawan.warna_baju, karyawan.status_baju, karyawan.tgl_lahir, karyawan.tgl_masuk, bagian.nama_bagian, bagian.area_utama, bagian.area, karyawan.status_aktif')
            ->join('bagian', 'bagian.id_bagian = karyawan.id_bagian')
            ->findAll(); // Ambil semua data tanpa filter
    }

    public function exportKaryawanByArea($area)
    {
        // Buat query builder
        $builder = $this->select('karyawan.id_karyawan, karyawan.kode_kartu, karyawan.nama_karyawan, karyawan.shift, karyawan.jenis_kelamin, karyawan.libur, karyawan.libur_tambahan, karyawan.warna_baju, karyawan.status_baju, karyawan.tgl_lahir, karyawan.tgl_masuk, bagian.nama_bagian, bagian.area_utama, bagian.area, karyawan.status_aktif')
            ->join('bagian', 'bagian.id_bagian = karyawan.id_bagian')
            ->where('bagian.area', $area);
        return $builder->findAll();
    }

    public function getKaryawanTanpaArea()
    {
        return $this->select('karyawan.id_karyawan, karyawan.kode_kartu, karyawan.nama_karyawan, karyawan.shift, karyawan.jenis_kelamin, karyawan.libur, karyawan.libur_tambahan, karyawan.warna_baju, karyawan.status_baju, karyawan.tgl_lahir, karyawan.tgl_masuk, karyawan.id_bagian, bagian.nama_bagian, bagian.area_utama, bagian.area, karyawan.status_aktif, karyawan.created_at, karyawan.updated_at')
            ->join('bagian', 'bagian.id_bagian = karyawan.id_bagian', 'left') // left join untuk menghindari data hilang
            ->where('(bagian.area_utama IS NULL OR bagian.area_utama = "-")') // Cek area kosong atau "-"
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

    public function exportPindahKaryawanAll()
    {
        // Buat query builder untuk mengambil seluruh data karyawan
        return $this->select('karyawan.id_karyawan, karyawan.kode_kartu, karyawan.nama_karyawan, karyawan.shift, karyawan.jenis_kelamin, karyawan.libur, karyawan.libur_tambahan, karyawan.warna_baju, karyawan.status_baju, karyawan.tgl_lahir, karyawan.tgl_masuk, bagian.nama_bagian, bagian.area_utama, bagian.area, karyawan.status_aktif, history_pindah_karyawan.id_pindah, history_pindah_karyawan.id_karyawan, history_pindah_karyawan.id_bagian_asal, bagian_asal.nama_bagian AS nama_bagian_asal, bagian_asal.area_utama AS area_utama_asal, bagian_asal.area AS area_asal, history_pindah_karyawan.id_bagian_baru, bagian_baru.nama_bagian AS bagian_aktual, bagian_baru.area_utama AS area_utama_aktual, bagian_baru.area AS area_aktual, history_pindah_karyawan.tgl_pindah, history_pindah_karyawan.keterangan, history_pindah_karyawan.updated_by, user.username')
        ->join('bagian', 'bagian.id_bagian = karyawan.id_bagian')
        ->join('history_pindah_karyawan', 'history_pindah_karyawan.id_karyawan = karyawan.id_karyawan')
        ->join('bagian as bagian_asal', 'bagian_asal.id_bagian = history_pindah_karyawan.id_bagian_asal')
        ->join('bagian as bagian_baru', 'bagian_baru.id_bagian = history_pindah_karyawan.id_bagian_baru')
        ->join('user', 'user.id_user = history_pindah_karyawan.updated_by')
        ->findAll(); // Ambil semua data tanpa filter
    }

    public function getActiveKaryawanByBagian()
    {
        $builder = $this->db->table($this->table);
        $builder->select('COUNT(karyawan.nama_karyawan) AS jumlah_karyawan, bagian.nama_bagian');
        $builder->join('bagian', 'karyawan.id_bagian = bagian.id_bagian');
        $builder->where('karyawan.status_aktif', 'Aktif');
        $builder->groupBy('bagian.nama_bagian');
        return $builder->get()->getResultArray();
    }
}
