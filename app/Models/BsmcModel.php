<?php

namespace App\Models;

use CodeIgniter\Model;

class BsmcModel extends Model
{
    protected $table            = 'bs_mesin';
    protected $primaryKey       = 'id_bsmc';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'id_bsmc',
        'id_karyawan',
        'id_batch',
        'average_produksi',
        'average_bs',
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

    public function getKaryawan()
    {
        return $this->select('bs_mesin.id_bsmc, bs_mesin.id_karyawan, karyawan.nama_karyawan, karyawan.kode_kartu, karyawan.shift, bs_mesin.tanggal, bs_mesin.no_model, bs_mesin.inisial, bs_mesin.qty_bs, bs_mesin.qty_prod_mc, bs_mesin.created_at, bs_mesin.updated_at')
            ->join('karyawan', 'karyawan.id_karyawan = bs_mesin.id_karyawan')
            ->findAll();
    }

    public function getDatabyAreaUtama($area_utama)
    {
        return $this->db->table('bs_mesin')
        ->join('karyawan', 'karyawan.id_karyawan = bs_mesin.id_karyawan')
        ->join('bagian', 'bagian.id_bagian = karyawan.id_bagian')
        ->where('bagian.area_utama', $area_utama)
            ->get()->getResultArray();
    }

    public function getDatabyAreaUtamaAndPeriodeInBatch($area_utama, $id_batch)
    {
        return $this->db->table('bs_mesin')
        ->join('karyawan', 'karyawan.id_karyawan = bs_mesin.id_karyawan')
        ->join('bagian', 'bagian.id_bagian = karyawan.id_bagian')
        ->join('batch', 'batch.id_batch = bs_mesin.id_batch')
        ->where('batch.id_batch', $id_batch)
            ->where('bagian.area_utama', $area_utama)
            ->get()->getResultArray();
    }

    public function getTop3Produksi($area_utama, $id_batch)
    {
        return $this->select('bs_mesin.average_produksi, bs_mesin.average_bs, karyawan.nama_karyawan, karyawan.kode_kartu, karyawan.jenis_kelamin, karyawan.tgl_masuk, bagian.nama_bagian, batch.id_batch')
        ->join('karyawan', 'karyawan.id_karyawan = bs_mesin.id_karyawan')
        ->join('bagian', 'bagian.id_bagian = karyawan.id_bagian')
        ->join('batch', 'batch.id_batch = bs_mesin.id_batch')
        ->where('bagian.area_utama', $area_utama)
            ->where('batch.id_batch', $id_batch)
            ->orderBy('bs_mesin.average_produksi', 'DESC') // Order by highest production
            ->orderBy('bs_mesin.average_bs', 'ASC') // Order by lowest defect
            ->limit(3) // Limit to top 3
            ->get()->getResultArray();
    }

    public function getBsmcByIdKaryawan($id_karyawan)
    {
        return $this->select('bs_mesin.id_bsmc, bs_mesin.id_karyawan, karyawan.nama_karyawan, karyawan.kode_kartu, karyawan.shift, bs_mesin.id_periode, bs_mesin.average_produksi, bs_mesin.average_bs, bs_mesin.created_at, bs_mesin.updated_at')
            ->join('karyawan', 'karyawan.id_karyawan = bs_mesin.id_karyawan')
            ->where('bs_mesin.id_karyawan', $id_karyawan)
            ->findAll();
    }
}
