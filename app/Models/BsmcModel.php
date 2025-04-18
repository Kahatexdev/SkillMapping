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
        'area'
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
        return $this->select('bs_mc.id_bsmc, bs_mc.id_karyawan, karyawan.nama_karyawan, karyawan.kode_kartu, karyawan.shift, bs_mc.tanggal, bs_mc.no_model, bs_mc.inisial, bs_mc.qty_bs, bs_mc.qty_prod_mc, bs_mc.created_at, bs_mc.updated_at')
            ->join('karyawan', 'karyawan.id_karyawan = bs_mc.id_karyawan')
            ->findAll();
    }

    public function getDatabyAreaUtama($area_utama)
    {
        return $this->db->table('bs_mc')
            ->join('karyawan', 'karyawan.id_karyawan = bs_mc.id_karyawan')
            ->join('bagian', 'bagian.id_bagian = karyawan.id_bagian')
            ->where('bagian.area_utama', $area_utama)
            ->get()->getResultArray();
    }

    // public function getDatabyAreaUtamaAndPeriodeInBatch($area_utama, $id_batch)
    // {
    //     return $this->db->table('bs_mc')
    //         ->join('karyawan', 'karyawan.id_karyawan = bs_mc.id_karyawan')
    //         ->join('bagian', 'bagian.id_bagian = karyawan.id_bagian')
    //         ->join('batch', 'batch.id_batch = bs_mc.id_batch')
    //         ->where('batch.id_batch', $id_batch)
    //         ->where('bagian.area_utama', $area_utama)
    //         ->get()->getResultArray();
    // }

    public function getDatabyAreaUtamaAndPeriodeInBatch($area_utama, $id_batch)
    {
        return $this->db->table('bs_mc')
            ->join('karyawan', 'karyawan.id_karyawan = bs_mc.id_karyawan')
            ->join('bagian', 'bagian.id_bagian = karyawan.id_bagian')
            ->join('periode', 'bs_mc.tgl_input BETWEEN periode.start_date AND periode.end_date') // Hubungkan berdasarkan tgl_input
            ->join('batch', 'batch.id_batch = periode.id_batch') // Hubungkan batch dengan periode
            ->where('batch.id_batch', $id_batch)
            ->where('bagian.area_utama', $area_utama)
            ->get()->getResultArray();
    }

    public function getTop3Produksi($area_utama, $id_batch)
    {
        return $this->select('AVG(bs_mc.produksi) AS average_produksi, AVG(bs_mc.bs_mc) AS average_bs,karyawan.id_karyawan, karyawan.nama_karyawan, karyawan.kode_kartu, karyawan.jenis_kelamin, karyawan.tgl_masuk, bagian.nama_bagian, batch.id_batch')
            ->join('karyawan', 'karyawan.id_karyawan = bs_mc.id_karyawan')
            ->join('bagian', 'bagian.id_bagian = karyawan.id_bagian')
            ->join('periode', 'bs_mc.tgl_input BETWEEN periode.start_date AND periode.end_date') // Hubungkan berdasarkan tgl_input
            ->join('batch', 'batch.id_batch = periode.id_batch') // Hubungkan batch dengan periode
            ->where('bagian.area_utama', $area_utama)
            ->where('batch.id_batch', $id_batch)
            ->groupBy('bs_mc.id_karyawan')
            ->orderBy('average_produksi', 'DESC') // Order by highest production
            // ->orderBy('bs_mc.average_bs', 'ASC') // Order by lowest defect
            ->limit(3) // Limit to top 3
            ->get()->getResultArray();
    }

    public function getMinAvgBS($area_utama, $id_batch)
    {
        return $this->select('bs_mc.produksi, bs_mc.bs_mc, karyawan.id_karyawan, karyawan.nama_karyawan, karyawan.kode_kartu, karyawan.jenis_kelamin, karyawan.tgl_masuk, bagian.nama_bagian, batch.nama_batch')
            ->join('karyawan', 'karyawan.id_karyawan = bs_mc.id_karyawan')
            ->join('bagian', 'bagian.id_bagian = karyawan.id_bagian')
            ->join('periode', 'bs_mc.tgl_input BETWEEN periode.start_date AND periode.end_date') // Hubungkan berdasarkan tgl_input
            ->join('batch', 'batch.id_batch = periode.id_batch') // Hubungkan batch dengan periode
            ->where('bagian.area_utama', $area_utama)
            ->where('batch.id_batch', $id_batch)
            ->orderBy('bs_mc.produksi', 'DESC') // First, order by highest production
            ->limit(7) // Limit to top 7 results based on production
            ->get()->getResultArray();
    }
    public function getTop3LowestBS($area_utama, $id_batch)
    {
        // Step 1: Get the top 7 data based on highest production
        $top7Data = $this->select('AVG(bs_mc.produksi) AS average_produksi, AVG(bs_mc.bs_mc) AS average_bs,karyawan.id_karyawan, karyawan.nama_karyawan, karyawan.kode_kartu, karyawan.jenis_kelamin, karyawan.tgl_masuk, bagian.nama_bagian, batch.nama_batch')
            ->join('karyawan', 'karyawan.id_karyawan = bs_mc.id_karyawan')
            ->join('bagian', 'bagian.id_bagian = karyawan.id_bagian')
            ->join('periode', 'bs_mc.tgl_input BETWEEN periode.start_date AND periode.end_date') // Hubungkan berdasarkan tgl_input
            ->join('batch', 'batch.id_batch = periode.id_batch') // Hubungkan batch dengan periode
            ->where('bagian.area_utama', $area_utama)
            ->where('batch.id_batch', $id_batch)
            ->groupBy('bs_mc.id_karyawan')
            ->orderBy('average_produksi', 'DESC') // Order by highest production
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
        return $this->select('bs_mc.id_bsmc, bs_mc.id_karyawan, karyawan.nama_karyawan, karyawan.kode_kartu, karyawan.shift, bs_mc.tgl_input, bs_mc.produksi, bs_mc.bs_mc, bs_mc.created_at, bs_mc.updated_at')
            ->join('karyawan', 'karyawan.id_karyawan = bs_mc.id_karyawan')
            ->join('periode', 'bs_mc.tgl_input BETWEEN periode.start_date AND periode.end_date') // Hubungkan berdasarkan tgl_input
            ->join('batch', 'batch.id_batch = periode.id_batch') // Hubungkan batch dengan periode
            ->where('batch.id_batch', $id_batch)
            ->findAll();
    }
    public function getBsmcByIdKaryawan($id_karyawan)
    {
        return $this->select('bs_mc.id_bsmc, bs_mc.id_karyawan, karyawan.nama_karyawan, karyawan.kode_kartu, karyawan.shift, AVG(bs_mc.produksi) AS average_produksi, AVG(bs_mc.bs_mc) AS average_bs, bs_mc.created_at, bs_mc.updated_at')
            ->join('karyawan', 'karyawan.id_karyawan = bs_mc.id_karyawan')
            ->join('periode', 'bs_mc.tgl_input BETWEEN periode.start_date AND periode.end_date') // Hubungkan berdasarkan tgl_input
            ->join('batch', 'batch.id_batch = periode.id_batch') // Hubungkan batch dengan periode
            ->where('bs_mc.id_karyawan', $id_karyawan)
            ->findAll();
    }
    public function validasiKaryawan($tgl_input, $id_karyawan)
    {
        return $this->select('bs_mc.id_bsmc, bs_mc.tgl_input, bs_mc.id_karyawan, karyawan.kode_kartu, karyawan.nama_karyawan, karyawan.tgl_lahir, bagian.area')
            ->join('karyawan', 'karyawan.id_karyawan = bs_mc.id_karyawan')
            ->join('bagian', 'bagian.id_bagian = karyawan.id_bagian')
            ->where('bs_mc.tgl_input', $tgl_input)
            ->where('bs_mc.id_karyawan', $id_karyawan)
            ->first();
    }

    public function getSummaryBSMesin($id_batch, $area)
    {
        return $this->select('bs_mc.id_karyawan, karyawan.kode_kartu, karyawan.nama_karyawan, karyawan.jenis_kelamin, karyawan.tgl_masuk,  SUM(bs_mc.produksi) AS total_produksi, SUM(bs_mc.bs_mc) AS total_bs, periode.nama_periode, periode.id_batch, bs_mc.area, periode.start_date, periode.end_date, periode.jml_libur, bagian.nama_bagian')
            ->join('periode', 'bs_mc.tgl_input BETWEEN periode.start_date AND periode.end_date', 'inner')
            ->join('karyawan', 'karyawan.id_karyawan = bs_mc.id_karyawan', 'inner')
            ->join('bagian', 'bagian.id_bagian = karyawan.id_bagian', 'inner')
            ->where('periode.id_batch', $id_batch)
            ->where('bs_mc.area', $area)
            ->groupBy('karyawan.kode_kartu, periode.start_date, periode.end_date') // Grouping berdasarkan kode_kartu dan periode
            ->findAll();
    }

    public function getDatabyArea($area)
    {
        return $this->db->table('bs_mc')
            ->join('karyawan', 'karyawan.id_karyawan = bs_mc.id_karyawan')
            ->join('bagian', 'bagian.id_bagian = karyawan.id_bagian')
            ->where('bagian.area', $area)
            ->get()->getResultArray();
    }

    public function getFilteredData($area, $startDate, $endDate)
    {
        return $this->select('bs_mc.*, karyawan.kode_kartu, karyawan.nama_karyawan')
            ->join('karyawan', 'karyawan.id_karyawan = bs_mc.id_karyawan')
            ->where('area', $area)
            ->where('tgl_input >=', $startDate)
            ->where('tgl_input <=', $endDate)
            ->orderBy('tgl_input', 'ASC')
            ->findAll();
    }

    public function getCurrentInput()
    {
        return $this->select('bs_mc.tgl_input')
            ->orderBy('bs_mc.tgl_input', 'DESC')
            ->limit(1)
            ->first();
    }

    public function getTopProduksiOperator($area, $id_batch, $limit = 7)
    {
        $db = \Config\Database::connect();

        $sql = "
        SELECT 
            produksi_per_periode.id_karyawan,
            karyawan.nama_karyawan,
            karyawan.kode_kartu,
            karyawan.jenis_kelamin,
            karyawan.tgl_masuk,
            bagian.nama_bagian,
            batch.id_batch,
            -- Ganti AVG(...) dengan SUM(...)/3 agar selalu dibagi 3
            (SUM(produksi_per_periode.avg_produksi) / 3) AS rata_rata_produksi
        FROM (
            SELECT 
                bs_mc.id_karyawan,
                periode.nama_periode,
                SUM(bs_mc.produksi) AS total_produksi,
                (DATEDIFF(periode.end_date, periode.start_date) + 1 - periode.jml_libur) AS hari_kerja,
                -- rata-rata produksi per periode
                (SUM(bs_mc.produksi) / (DATEDIFF(periode.end_date, periode.start_date) + 1 - periode.jml_libur))
                  AS avg_produksi
            FROM bs_mc
            JOIN periode 
              ON bs_mc.tgl_input BETWEEN periode.start_date AND periode.end_date
            WHERE periode.id_batch = ?
            GROUP BY bs_mc.id_karyawan, periode.nama_periode
        ) AS produksi_per_periode
        JOIN karyawan 
          ON karyawan.id_karyawan = produksi_per_periode.id_karyawan
        JOIN bagian 
          ON bagian.id_bagian   = karyawan.id_bagian
        JOIN batch 
          ON batch.id_batch     = ?
        WHERE bagian.area = ?
        GROUP BY produksi_per_periode.id_karyawan
        ORDER BY rata_rata_produksi DESC
        LIMIT ?
    ";

        return $db->query($sql, [$id_batch, $id_batch, $area, (int)$limit])
            ->getResultArray();
    }
    public function getTop3BsMcFromList(array $ids, $id_batch)
    {
        if (empty($ids)) return [];

        $db = \Config\Database::connect();
        // ubah array ke comma-separated
        $in = implode(',', array_map('intval', $ids));

        $sql = "
        SELECT 
          id_karyawan,
          (SUM(bs_mc.bs_mc) / (DATEDIFF(p.end_date,p.start_date)+1 - p.jml_libur)) AS avg_bs
        FROM bs_mc
        JOIN periode p ON bs_mc.tgl_input BETWEEN p.start_date AND p.end_date
        WHERE p.id_batch = ? 
          AND bs_mc.id_karyawan IN ($in)
        GROUP BY id_karyawan
        ORDER BY avg_bs ASC
        LIMIT 3
    ";
        return $db->query($sql, [$id_batch])->getResultArray();
    }
}
