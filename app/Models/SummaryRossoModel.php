<?php

namespace App\Models;

use CodeIgniter\Model;

class SummaryRossoModel extends Model
{
    protected $table            = 'sum_rosso';
    protected $primaryKey       = 'id_sr';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = ['id_sr', 'id_karyawan', 'tgl_input', 'produksi', 'perbaikan', 'created_at', 'updated_at', 'area'];

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

    public function getData()
    {
        return $this->db->table('summary_rosso')
            ->join('karyawan', 'karyawan.id_karyawan = summary_rosso.id_karyawan')
            ->join('bagian', 'bagian.id_bagian = karyawan.id_bagian')
            ->get()->getResultArray();
    }

    public function getDatabyAreaUtama($area_utama)
    {
        return $this->db->table('summary_rosso')
            ->join('karyawan', 'karyawan.id_karyawan = summary_rosso.id_karyawan')
            ->join('bagian', 'bagian.id_bagian = karyawan.id_bagian')
            ->where('bagian.area_utama', $area_utama)
            ->get()->getResultArray();
    }
    public function getDatabyAreaUtamaAndPeriodeInBatch($area_utama, $id_batch)
    {
        return $this->db->table('summary_rosso')
            ->join('karyawan', 'karyawan.id_karyawan = summary_rosso.id_karyawan')
            ->join('bagian', 'bagian.id_bagian = karyawan.id_bagian')
            -> join('batch', 'batch.id_batch = summary_rosso.id_batch')
            ->where('batch.id_batch', $id_batch)
            ->where('bagian.area_utama', $area_utama)
            ->get()->getResultArray();
    }


    public function getDataById($id)
    {
        return $this->db->table('summary_rosso')
            ->join('karyawan', 'karyawan.id_karyawan = summary_rosso.id_karyawan')
            ->where('id_sr', $id)
            ->get()->getRowArray();
    }

    public function sumRosso()
    {
        return $this->select('SUM(qty_prod_rosso) as total_qty_prod_rosso, SUM(qty_bs) as total_qty_bs, karyawan.id_bagian, bagian.nama_bagian, karyawan.kode_kartu, karyawan.nama_karyawan, karyawan.jenis_kelamin, karyawan.tgl_masuk')
            ->join('karyawan', 'karyawan.id_karyawan = summary_rosso.id_karyawan')
            ->join('bagian', 'bagian.id_bagian = karyawan.id_bagian')
            ->groupBy('summary_rosso.id_karyawan')
            ->get()->getResultArray();
    }

    public function getRossoGroupByPeriode()
    {
        return $this->select('SUM(qty_prod_rosso) as total_qty_prod_rosso, SUM(qty_bs) as total_qty_bs, karyawan.id_bagian, bagian.nama_bagian, karyawan.kode_kartu, karyawan.nama_karyawan, karyawan.jenis_kelamin, karyawan.tgl_masuk, periode.start_date, periode.end_date, periode.id_periode, periode.nama_periode, batch.nama_batch, batch.id_batch')
            ->join('karyawan', 'karyawan.id_karyawan = summary_rosso.id_karyawan')
            ->join('bagian', 'bagian.id_bagian = karyawan.id_bagian')
            ->join('periode', 'periode.id_periode = summary_rosso.id_periode')
            ->join('batch', 'batch.id_batch = periode.id_batch')
            ->groupBy('summary_rosso.id_periode')
            ->get()->getResultArray();
    }

    public function getRossoByPeriode($id_periode)
    {
        return $this->select(
            'karyawan.id_bagian, 
             bagian.nama_bagian, 
             karyawan.kode_kartu, 
             karyawan.nama_karyawan, 
             karyawan.jenis_kelamin, 
             karyawan.tgl_masuk, 
             periode.start_date, 
             periode.end_date,
             periode.jml_libur,
             SUM(qty_prod_rosso) as total_qty_prod_rosso, 
             SUM(qty_bs) as total_qty_bs'
        )
            ->join('karyawan', 'karyawan.id_karyawan = summary_rosso.id_karyawan')
            ->join('bagian', 'bagian.id_bagian = karyawan.id_bagian')
            ->join('periode', 'periode.id_periode = summary_rosso.id_periode')
            ->where('summary_rosso.id_periode', $id_periode)
            ->groupBy('summary_rosso.id_karyawan')
            ->get()->getResultArray();
    }

    public function getTop3Produksi($area_utama, $id_batch)
    {
        return $this->select('summary_rosso.average_produksi, summary_rosso.average_bs,karyawan.id_karyawan, karyawan.nama_karyawan, karyawan.kode_kartu, karyawan.jenis_kelamin, karyawan.tgl_masuk, bagian.nama_bagian, batch.nama_batch')
        ->join('karyawan', 'karyawan.id_karyawan = summary_rosso.id_karyawan')
        ->join('bagian', 'bagian.id_bagian = karyawan.id_bagian')
        ->join('batch', 'batch.id_batch = summary_rosso.id_batch')
        ->where('bagian.area_utama', $area_utama)
            ->where('batch.id_batch', $id_batch)
            ->orderBy('summary_rosso.average_produksi', 'DESC') // Order by highest production
            ->orderBy('summary_rosso.average_bs', 'ASC') // Order by lowest defect
            ->limit(3) // Limit to top 3
            ->get()->getResultArray();
    }


    public function getMinAvgBS($area_utama, $id_batch)
    {
        return $this->select('MIN(summary_rosso.average_bs) as min_avg_bs,summary_rosso.average_bs, summary_rosso.average_produksi,karyawan.id_karyawan, karyawan.nama_karyawan, karyawan.kode_kartu, karyawan.jenis_kelamin, karyawan.tgl_masuk, bagian.nama_bagian, periode.nama_periode, batch.nama_batch')
            ->join('karyawan', 'karyawan.id_karyawan = summary_rosso.id_karyawan')
            ->join('bagian', 'bagian.id_bagian = karyawan.id_bagian')
            ->join('batch', 'batch.id_batch = periode.id_batch')
            ->where('bagian.area_utama', $area_utama)
            ->where('periode.id_batch', $id_batch)
            ->get()->getRowArray();
    }

    public function getTop3LowestBS($area_utama, $id_batch)
    {
        // Step 1: Get the top 7 data based on highest production
        $top7Data = $this->select('summary_rosso.average_produksi, summary_rosso.average_bs,karyawan.id_karyawan, karyawan.nama_karyawan, karyawan.kode_kartu, karyawan.jenis_kelamin, karyawan.tgl_masuk, bagian.nama_bagian, batch.nama_batch')
        ->join('karyawan', 'karyawan.id_karyawan = summary_rosso.id_karyawan')
        ->join('bagian', 'bagian.id_bagian = karyawan.id_bagian')
        ->join('batch', 'batch.id_batch = summary_rosso.id_batch')
        ->where('bagian.area_utama', $area_utama)
            ->where('batch.id_batch', $id_batch)
            ->orderBy('summary_rosso.average_produksi', 'DESC') // Order by highest production
            ->limit(7) // Limit to top 7 results based on production
            ->get()->getResultArray();

        // Step 2: Sort these 7 results by average_bs in ascending order
        usort($top7Data, function ($a, $b) {
            return $a['average_bs'] <=> $b['average_bs']; // Sort by average_bs (lowest first)
        });

        // Step 3: Return the first 3 with the lowest average_bs
        return array_slice($top7Data, 0, 3); // Return the top 3 with the lowest defects
    }

    public function validasiKaryawan($tgl_input, $id_karyawan)
    {
        return $this->select('sum_rosso.id_sr, sum_rosso.tgl_input, sum_rosso.id_karyawan, karyawan.kode_kartu, karyawan.nama_karyawan, karyawan.tgl_lahir, bagian.area')
            ->join('karyawan', 'karyawan.id_karyawan = sum_rosso.id_karyawan')
            ->join('bagian', 'bagian.id_bagian = karyawan.id_bagian')
            ->where('sum_rosso.tgl_input', $tgl_input)
            ->where('sum_rosso.id_karyawan', $id_karyawan)
            ->first();
    }

    public function getSummaryRosso($area, $id_batch)
    {
        return $this->select('sum_rosso.id_karyawan, karyawan.kode_kartu, karyawan.nama_karyawan, karyawan.jenis_kelamin, karyawan.tgl_masuk,  SUM(sum_rosso.produksi) AS total_produksi, SUM(sum_rosso.perbaikan) AS total_perbaikan, periode.nama_periode, periode.id_batch, sum_rosso.area, periode.start_date, periode.end_date, periode.jml_libur, bagian.nama_bagian')
            ->join('periode', 'sum_rosso.tgl_input BETWEEN periode.start_date AND periode.end_date', 'inner')
            ->join('karyawan', 'karyawan.id_karyawan = sum_rosso.id_karyawan', 'inner')
            ->join('bagian', 'bagian.id_bagian = karyawan.id_bagian', 'inner')
            ->where('sum_rosso.area', $area)
            ->where('periode.id_batch', $id_batch)
            ->groupBy('karyawan.kode_kartu, periode.start_date, periode.end_date') // Grouping berdasarkan kode_kartu dan periode
            ->findAll();
    }

    public function getFilteredData($area_utama, $startDate, $endDate)
    {
        return $this->select('sum_rosso.*, karyawan.kode_kartu, karyawan.nama_karyawan')
            ->join('karyawan', 'karyawan.id_karyawan = sum_rosso.id_karyawan')
            ->where('area', $area_utama)
            ->where('tgl_input >=', $startDate)
            ->where('tgl_input <=', $endDate)
            ->orderBy('tgl_input', 'ASC')
            ->findAll();
    }
}
