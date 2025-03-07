<?php

namespace App\Models;

use CodeIgniter\Model;

class SummaryJarumModel extends Model
{
    protected $table            = 'sum_jarum';
    protected $primaryKey       = 'id_sj';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = ['id_sj', 'id_karyawan', 'tgl_input', 'used_needle', 'created_at', 'updated_at'];

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

    public function getDatabyAreaUtama($area_utama)
    {
        return $this->db->table('summary_jarum')
            ->join('karyawan', 'karyawan.id_karyawan = summary_jarum.id_karyawan')
            ->join('bagian', 'bagian.id_bagian = karyawan.id_bagian')
            ->where('bagian.area_utama', $area_utama)
            ->get()->getResultArray();
    }

    public function getDatabyAreaUtamaAndPeriodeInBatch($area_utama, $id_batch)
    {
        return $this->db->table('summary_jarum')
            ->join('karyawan', 'karyawan.id_karyawan = summary_jarum.id_karyawan')
            ->join('bagian', 'bagian.id_bagian = karyawan.id_bagian')
            ->join('batch', 'batch.id_batch = summary_jarum.id_batch')
            ->where('batch.id_batch', $id_batch)
            ->where('bagian.area_utama', $area_utama)
            ->get()->getResultArray();
    }

    public function getTop3Produksi($area_utama, $id_batch)
    {
        return $this->select('summary_jarum.avg_used_needle, karyawan.id_karyawan, karyawan.nama_karyawan, karyawan.kode_kartu, karyawan.jenis_kelamin, karyawan.tgl_masuk, bagian.nama_bagian, batch.nama_batch')
            ->join('karyawan', 'karyawan.id_karyawan = summary_jarum.id_karyawan')
            ->join('bagian', 'bagian.id_bagian = karyawan.id_bagian')
            ->join('batch', 'batch.id_batch = summary_jarum.id_batch')
            ->where('bagian.area_utama', $area_utama)
            ->where('batch.id_batch', $id_batch)
            ->orderBy('summary_jarum.avg_used_needle', 'ASC') // Order by highest production
            ->limit(3) // Limit to top 3
            ->get()->getResultArray();
    }

    public function getDatabyArea($area)
    {
        return $this->db->table('bs_mc')
            ->join('karyawan', 'karyawan.id_karyawan = bs_mc.id_karyawan')
            ->join('bagian', 'bagian.id_bagian = karyawan.id_bagian')
            ->where('bagian.area', $area)
            ->get()->getResultArray();
    }

    public function getSummaryJarum($area, $id_batch)
    {
        return $this->select('sum_jarum.id_sj, sum_jarum.id_karyawan, sum_jarum.tgl_input, SUM(sum_jarum.used_needle) AS total_jarum, sum_jarum.created_at, sum_jarum.updated_at, sum_jarum.area, karyawan.kode_kartu, karyawan.nama_karyawan, karyawan.jenis_kelamin, karyawan.tgl_masuk,  periode.start_date, periode.end_date, periode.nama_periode, periode.id_batch, periode.jml_libur, bagian.nama_bagian')
            ->join('periode', 'sum_jarum.tgl_input BETWEEN periode.start_date AND periode.end_date', 'inner')
            ->join('karyawan', 'karyawan.id_karyawan = sum_jarum.id_karyawan', 'inner')
            ->join('bagian', 'bagian.id_bagian = karyawan.id_bagian', 'inner')
            ->where('sum_jarum.area', $area)
            ->where('periode.id_batch', $id_batch)
            ->groupBy('karyawan.kode_kartu, periode.start_date, periode.end_date')
            ->findAll();
    }
}
