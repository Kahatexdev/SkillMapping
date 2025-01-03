<?php

namespace App\Models;

use CodeIgniter\Model;

class SummaryJarumModel extends Model
{
    protected $table            = 'summary_jarum';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = ['id_sj', 'id_karyawan', 'id_periode', 'avg_used_needle'];

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
        ->join('periode', 'periode.id_periode = summary_jarum.id_periode')
        ->where('periode.id_batch', $id_batch)
            ->where('bagian.area_utama', $area_utama)
            ->get()->getResultArray();
    }

    public function getTop3Produksi($area_utama, $id_batch)
    {
        return $this->select('summary_jarum.avg_used_needle, karyawan.nama_karyawan, karyawan.kode_kartu, karyawan.jenis_kelamin, karyawan.tgl_masuk, bagian.nama_bagian, periode.nama_periode, batch.nama_batch')
        ->join('karyawan', 'karyawan.id_karyawan = summary_jarum.id_karyawan')
        ->join('bagian', 'bagian.id_bagian = karyawan.id_bagian')
        ->join('periode', 'periode.id_periode = summary_jarum.id_periode')
        ->join('batch', 'batch.id_batch = periode.id_batch')
        ->where('bagian.area_utama', $area_utama)
            ->where('periode.id_batch', $id_batch)
            ->orderBy('summary_jarum.avg_used_needle', 'ASC') // Order by highest production
            ->limit(3) // Limit to top 3
            ->get()->getResultArray();
    }
}
