<?php

namespace App\Models;

use CodeIgniter\Model;

class BsmcModel extends Model
{
    protected $table            = 'bs_mc';
    protected $primaryKey       = 'id_bsmc';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'id_bsmc',
        'id_karyawan',
        'tgl_input',
        'produksi',
        'bs_mc',
        'created_at',
        'updated_at',
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
        return $this->select('bs_mesin.average_produksi, bs_mesin.average_bs,karyawan.id_karyawan, karyawan.nama_karyawan, karyawan.kode_kartu, karyawan.jenis_kelamin, karyawan.tgl_masuk, bagian.nama_bagian, batch.id_batch')
            ->join('karyawan', 'karyawan.id_karyawan = bs_mesin.id_karyawan')
            ->join('bagian', 'bagian.id_bagian = karyawan.id_bagian')
            ->join('batch', 'batch.id_batch = bs_mesin.id_batch')
            ->where('bagian.area_utama', $area_utama)
            ->where('batch.id_batch', $id_batch)
            ->orderBy('bs_mesin.average_produksi', 'DESC') // Order by highest production
            // ->orderBy('bs_mesin.average_bs', 'ASC') // Order by lowest defect
            ->limit(3) // Limit to top 3
            ->get()->getResultArray();
    }

    public function getMinAvgBS($area_utama, $id_batch)
    {
        return $this->select('bs_mesin.average_produksi, bs_mesin.average_bs, karyawan.id_karyawan, karyawan.nama_karyawan, karyawan.kode_kartu, karyawan.jenis_kelamin, karyawan.tgl_masuk, bagian.nama_bagian, batch.nama_batch')
            ->join('karyawan', 'karyawan.id_karyawan = bs_mesin.id_karyawan')
            ->join('bagian', 'bagian.id_bagian = karyawan.id_bagian')
            ->join('batch', 'batch.id_batch = bs_mesin.id_batch')
            ->where('bagian.area_utama', $area_utama)
            ->where('batch.id_batch', $id_batch)
            ->orderBy('bs_mesin.average_produksi', 'DESC') // First, order by highest production
            ->limit(7) // Limit to top 7 results based on production
            ->get()->getResultArray();
    }
    public function getTop3LowestBS($area_utama, $id_batch)
    {
        // Step 1: Get the top 7 data based on highest production
        $top7Data = $this->select('bs_mesin.average_produksi, bs_mesin.average_bs,karyawan.id_karyawan, karyawan.nama_karyawan, karyawan.kode_kartu, karyawan.jenis_kelamin, karyawan.tgl_masuk, bagian.nama_bagian, batch.nama_batch')
            ->join('karyawan', 'karyawan.id_karyawan = bs_mesin.id_karyawan')
            ->join('bagian', 'bagian.id_bagian = karyawan.id_bagian')
            ->join('batch', 'batch.id_batch = bs_mesin.id_batch')
            ->where('bagian.area_utama', $area_utama)
            ->where('batch.id_batch', $id_batch)
            ->orderBy('bs_mesin.average_produksi', 'DESC') // Order by highest production
            ->limit(7) // Limit to top 7 results based on production
            ->get()->getResultArray();

        // Step 2: Sort these 7 results by average_bs in ascending order
        usort($top7Data, function ($a, $b) {
            return $a['average_bs'] <=> $b['average_bs']; // Sort by average_bs (lowest first)
        });

        // Step 3: Return the first 3 with the lowest average_bs
        return array_slice($top7Data, 0, 3); // Return the top 3 with the lowest defects
    }

    public function getBsmcByIdBatch($id_batch)
    {
        return $this->select('bs_mesin.id_bsmc, bs_mesin.id_karyawan, karyawan.nama_karyawan, karyawan.kode_kartu, karyawan.shift, bs_mesin.id_batch, bs_mesin.average_produksi, bs_mesin.average_bs, bs_mesin.created_at, bs_mesin.updated_at')
            ->join('karyawan', 'karyawan.id_karyawan = bs_mesin.id_karyawan')
            ->where('bs_mesin.id_batch', $id_batch)
            ->findAll();
    }
    public function getBsmcByIdKaryawan($id_karyawan)
    {
        return $this->select('bs_mesin.id_bsmc, bs_mesin.id_karyawan, karyawan.nama_karyawan, karyawan.kode_kartu, karyawan.shift, bs_mesin.id_batch, bs_mesin.average_produksi, bs_mesin.average_bs, bs_mesin.created_at, bs_mesin.updated_at')
            ->join('karyawan', 'karyawan.id_karyawan = bs_mesin.id_karyawan')
            ->where('bs_mesin.id_karyawan', $id_karyawan)
            ->findAll();
    }
    public function validasiKaryawan($id_karyawan)
    {
        return $this->select('bs_mc.id_bsmc, bs_mc.tgl_input, bs_mc.id_karyawan, karyawan.kode_kartu, karyawan.nama_karyawan, karyawan.tgl_lahir, bagian.area')
            ->join('karyawan', 'karyawan.id_karyawan = bs_mc.id_karyawan')
            ->join('bagian', 'bagian.id_bagian = karyawan.id_bagian')
            ->where('bs_mc.id_karyawan', $id_karyawan)
            ->first();
    }
}
