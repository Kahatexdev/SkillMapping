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
    protected $allowedFields    = ['id_sj', 'id_karyawan', 'tgl_input', 'used_needle', 'created_at', 'updated_at', 'area'];

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
        return $this->db->table('sum_jarum')
            ->join('karyawan', 'karyawan.id_karyawan = sum_jarum.id_karyawan')
            ->join('bagian', 'bagian.id_bagian = karyawan.id_bagian')
            ->where('bagian.area_utama', $area_utama)
            ->get()->getResultArray();
    }

    public function getDatabyAreaUtamaAndPeriodeInBatch($area_utama, $id_batch)
    {
        return $this->db->table('sum_jarum')
            ->join('karyawan', 'karyawan.id_karyawan = sum_jarum.id_karyawan')
            ->join('bagian', 'bagian.id_bagian = karyawan.id_bagian')
            ->join('batch', 'batch.id_batch = sum_jarum.id_batch')
            ->where('batch.id_batch', $id_batch)
            ->where('bagian.area_utama', $area_utama)
            ->get()->getResultArray();
    }

    public function getTop3Produksi($area_utama, $id_batch)
    {
        return $this->select('AVG(sum_jarum.used_needle) as avg_used_needle, karyawan.id_karyawan, karyawan.nama_karyawan, karyawan.kode_kartu, karyawan.jenis_kelamin, karyawan.tgl_masuk, bagian.nama_bagian, batch.nama_batch')
            ->join('karyawan', 'karyawan.id_karyawan = sum_jarum.id_karyawan')
            ->join('bagian', 'bagian.id_bagian = karyawan.id_bagian')
            ->join('periode', 'sum_jarum.tgl_input BETWEEN periode.start_date AND periode.end_date') // Hubungkan berdasarkan tgl_input
            ->join('batch', 'batch.id_batch = periode.id_batch') // Hubungkan batch dengan periode
            ->where('bagian.area_utama', $area_utama)
            ->where('batch.id_batch', $id_batch)
            ->groupBy('sum_jarum.id_karyawan')
            ->orderBy('avg_used_needle', 'ASC') // Order by highest production
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

    // public function getSummaryJarum($area, $id_batch)
    // {
    //     return $this->select('sum_jarum.id_sj, sum_jarum.id_karyawan, sum_jarum.tgl_input, SUM(sum_jarum.used_needle) AS total_jarum, sum_jarum.created_at, sum_jarum.updated_at, sum_jarum.area, karyawan.kode_kartu, karyawan.nama_karyawan, karyawan.jenis_kelamin, karyawan.tgl_masuk,  periode.start_date, periode.end_date, periode.nama_periode, periode.id_batch, periode.jml_libur, bagian.nama_bagian')
    //         ->join('periode', 'sum_jarum.tgl_input BETWEEN periode.start_date AND periode.end_date', 'inner')
    //         ->join('karyawan', 'karyawan.id_karyawan = sum_jarum.id_karyawan', 'inner')
    //         ->join('bagian', 'bagian.id_bagian = karyawan.id_bagian', 'inner')
    //         ->where('sum_jarum.area', $area)
    //         ->where('periode.id_batch', $id_batch)
    //         ->groupBy('karyawan.kode_kartu, periode.start_date, periode.end_date')
    //         ->findAll();
    // }

    public function getSummaryJarum($area, $id_batch)
    {
        return $this->select('sum_jarum.id_sj, sum_jarum.id_karyawan, sum_jarum.tgl_input, SUM(sum_jarum.used_needle) AS total_jarum, sum_jarum.area,
            karyawan.kode_kartu, karyawan.nama_karyawan, karyawan.jenis_kelamin, karyawan.tgl_masuk, periode.start_date, periode.end_date, periode.nama_periode, periode.id_batch, periode.jml_libur, bagian.nama_bagian, h.tgl_pindah, h.id_bagian_asal, h.id_bagian_baru')
            ->join('periode', 'sum_jarum.tgl_input BETWEEN periode.start_date AND periode.end_date', 'inner')
            ->join('karyawan', 'karyawan.id_karyawan = sum_jarum.id_karyawan', 'inner')
            ->join('bagian', 'bagian.id_bagian = karyawan.id_bagian', 'inner')
            ->join('history_pindah_karyawan h', 'h.id_karyawan = sum_jarum.id_karyawan', 'left')
            ->where('periode.id_batch', $id_batch)
            // Group kondisi: area saat ini OR data area lama sebelum tanggal pindah
            ->groupStart()
            // 1) Data di area target
            ->where('sum_jarum.area', $area)
            // 2) OR data dari area asal, tapi hanya yg tgl_input < tanggal_pindah
            ->orWhere(
                "sum_jarum.area = (
                    SELECT area 
                    FROM bagian 
                    WHERE id_bagian = h.id_bagian_asal
                )
                AND sum_jarum.tgl_input < h.tgl_pindah",
                null,
                false
            )
            ->groupEnd()
            ->groupBy('karyawan.kode_kartu, periode.start_date, periode.end_date')
            ->findAll();
    }

    public function getFilteredData($area, $startDate, $endDate)
    {
        return $this->select('sum_jarum.*, karyawan.kode_kartu, karyawan.nama_karyawan')
            ->join('karyawan', 'karyawan.id_karyawan = sum_jarum.id_karyawan')
            ->where('area', $area)
            ->where('tgl_input >=', $startDate)
            ->where('tgl_input <=', $endDate)
            ->orderBy('tgl_input', 'ASC')
            ->findAll();
    }

    public function getCurrentInput()
    {
        return $this->select('sum_jarum.tgl_input')
            ->orderBy('sum_jarum.tgl_input', 'DESC')
            ->limit(1)
            ->first();
    }

    public function getTopUsedNeedle($area, $id_batch, $limit = 3)
    {
        $db = \Config\Database::connect();

        $sql = "
    SELECT 
        penggunaan_jarum.id_karyawan,
        karyawan.nama_karyawan,
        karyawan.kode_kartu,
        karyawan.jenis_kelamin,
        karyawan.tgl_masuk,
        bagian.nama_bagian,
        batch.id_batch,
        -- Jumlah periode aktual dipakai untuk pembagian
        (SUM(penggunaan_jarum.avg_needle) / 3) AS rata_rata_jarum
    FROM (
        SELECT 
            sum_jarum.id_karyawan,
            periode.nama_periode,
            SUM(sum_jarum.used_needle) AS total_used_needle,
            (DATEDIFF(periode.end_date, periode.start_date) + 1 - periode.jml_libur) AS hari_kerja,
            (SUM(sum_jarum.used_needle) / (DATEDIFF(periode.end_date, periode.start_date) + 1 - periode.jml_libur)) AS avg_needle
        FROM sum_jarum
        JOIN periode 
            ON sum_jarum.tgl_input BETWEEN periode.start_date AND periode.end_date
        WHERE periode.id_batch = ?
        GROUP BY sum_jarum.id_karyawan, periode.nama_periode
    ) AS penggunaan_jarum
    JOIN karyawan 
        ON karyawan.id_karyawan = penggunaan_jarum.id_karyawan
    JOIN bagian 
        ON bagian.id_bagian = karyawan.id_bagian
    JOIN batch 
        ON batch.id_batch = ?
    WHERE bagian.area = ?
    GROUP BY penggunaan_jarum.id_karyawan
    ORDER BY rata_rata_jarum ASC
    LIMIT ?
    ";

        return $db->query($sql, [$id_batch, $id_batch, $area, (int)$limit])->getResultArray();
    }

    public function getJarumData()
    {
        return $this->db->table('sum_jarum')
            ->join('karyawan', 'karyawan.id_karyawan = sum_jarum.id_karyawan')
            ->join('bagian', 'bagian.id_bagian = karyawan.id_bagian')
            ->join('periode', 'sum_jarum.tgl_input BETWEEN periode.start_date AND periode.end_date')
            ->get()->getResultArray();
    }
}
