<?php

namespace App\Models;

use CodeIgniter\Model;

class PenilaianModel extends Model
{
    protected $table            = 'penilaian';
    protected $primaryKey       = 'id_penilaian';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = ['id_penilaian', 'karyawan_id', 'id_periode', 'bobot_nilai', 'index_nilai', 'grade_akhir', 'id_user', 'id_jobrole', 'urutan_periode', 'created_at', 'updated_at'];

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

    // public function cekPenilaian($karyawan_id, $id_batch, $id_jobrole, $id_user)
    // {
    //     return $this->where('karyawan_id', $karyawan_id)
    //         ->where('id_batch', $id_batch)
    //         ->where('id_jobrole', $id_jobrole)
    //         ->where('id_user', $id_user)
    //         ->first();
    // }

    public function getPenilaian()
    {
        return $this->db->table('penilaian')
            ->select('penilaian.id_penilaian, penilaian.karyawan_id, penilaian.id_periode, penilaian.bobot_nilai, penilaian.index_nilai, penilaian.id_user, penilaian.id_jobrole, penilaian.created_at, penilaian.updated_at, karyawan.nama_karyawan, job_role.keterangan, bagian.id_bagian, bagian.nama_bagian, bagian.area, bagian.area_utama, batch.id_batch, batch.nama_batch, periode.nama_periode, periode.start_date, periode.end_date')
            ->join('karyawan', 'karyawan.id_karyawan=penilaian.karyawan_id')
            ->join('job_role', 'job_role.id_jobrole=penilaian.id_jobrole')
            ->join('bagian', 'bagian.id_bagian=job_role.id_bagian')
            ->join('periode', 'periode.id_periode=penilaian.id_periode')
            ->join('batch', 'batch.id_batch=periode.id_batch')
            ->groupBy('bagian.id_bagian')
            // group by batch.id_batch
            ->groupBy('penilaian.id_periode')
            ->get()
            ->getResultArray();
    }

    public function getPenilaianByIdBagian($id_bagian, $id_periode, $id_jobrole)
    {
        return $this->db->table('penilaian')
            ->select('penilaian.id_penilaian, penilaian.karyawan_id, penilaian.id_periode, penilaian.bobot_nilai, penilaian.index_nilai, penilaian.id_user, penilaian.id_jobrole, penilaian.created_at, penilaian.updated_at, karyawan.kode_kartu, karyawan.nama_karyawan, karyawan.shift, karyawan.jenis_kelamin, karyawan.tgl_masuk, job_role.keterangan, bagian.id_bagian, bagian.nama_bagian, bagian.area, bagian.area_utama, periode.id_periode, periode.nama_periode, periode.id_batch, periode.start_date, periode.end_date, batch.nama_batch, job_role.jobdesc, absen.id_absen, absen.id_karyawan, absen.id_periode, absen.sakit, absen.izin, absen.mangkir')
            ->join('karyawan', 'karyawan.id_karyawan=penilaian.karyawan_id')
            ->join('job_role', 'job_role.id_jobrole=penilaian.id_jobrole')
            ->join('bagian', 'bagian.id_bagian=job_role.id_bagian')
            ->join('periode', 'periode.id_periode=penilaian.id_periode')
            ->join('batch', 'batch.id_batch=periode.id_batch')
            ->join('absen', 'absen.id_karyawan=karyawan.id_karyawan')
            ->where('job_role.id_bagian', $id_bagian)
            ->where('penilaian.id_periode', $id_periode)
            ->where('penilaian.id_jobrole', $id_jobrole)
            ->get()
            ->getResultArray();
    }

    public function getPenilaianGroupByBatchAndArea()
    {
        return $this->db->table('penilaian')
            ->select('penilaian.id_penilaian, penilaian.karyawan_id, penilaian.id_periode, penilaian.bobot_nilai, penilaian.index_nilai, penilaian.id_user, penilaian.id_jobrole, penilaian.created_at, penilaian.updated_at, karyawan.nama_karyawan, job_role.keterangan, bagian.id_bagian, bagian.nama_bagian, bagian.area, bagian.area_utama, batch.id_batch, batch.nama_batch, periode.nama_periode, periode.start_date, periode.end_date')
            ->join('karyawan', 'karyawan.id_karyawan=penilaian.karyawan_id')
            ->join('job_role', 'job_role.id_jobrole=penilaian.id_jobrole')
            ->join('bagian', 'bagian.id_bagian=job_role.id_bagian')
            ->join('periode', 'periode.id_periode=penilaian.id_periode')
            ->join('batch', 'batch.id_batch=periode.id_batch')
            ->groupBy('batch.id_batch')
            ->groupBy('bagian.area_utama')
            ->get()
            ->getResultArray();
    }

    public function getPenilaianGroupByBatchAndAreaByIdBatch($id_batch, $area_utama)
    {
        return $this->db->table('penilaian')
            ->select('penilaian.id_penilaian, penilaian.karyawan_id, penilaian.id_periode, penilaian.bobot_nilai, penilaian.index_nilai, penilaian.id_user, penilaian.id_jobrole, penilaian.created_at, penilaian.updated_at, karyawan.id_karyawan, karyawan.kode_kartu, karyawan.nama_karyawan, karyawan.jenis_kelamin, karyawan.tgl_masuk, job_role.keterangan, bagian.id_bagian, bagian.nama_bagian, bagian.area, bagian.area_utama, batch.id_batch, batch.nama_batch, periode.nama_periode, periode.start_date, periode.end_date, periode.jml_libur')
            ->join('karyawan', 'karyawan.id_karyawan=penilaian.karyawan_id')
            ->join('job_role', 'job_role.id_jobrole=penilaian.id_jobrole')
            ->join('bagian', 'bagian.id_bagian=job_role.id_bagian')
            ->join('periode', 'periode.id_periode=penilaian.id_periode')
            ->join('batch', 'batch.id_batch=periode.id_batch')
            ->where('batch.id_batch', $id_batch)
            ->where('bagian.area_utama', $area_utama)
            ->notLike('karyawan.kode_kartu', 'KKKK%')
            ->groupBy('penilaian.karyawan_id')
            // ->groupBy('batch.id_batch')
            // ->groupBy('bagian.area_utama')
            // ->groupBy('bagian.area_utama')
            ->get()
            ->getResultArray();
    }

    public function getBatchGroupByBulanPenilaian()
    {
        return $this->db->table('penilaian')
            ->select('penilaian.id_penilaian, penilaian.karyawan_id, penilaian.id_periode, penilaian.bobot_nilai, penilaian.index_nilai, penilaian.id_user, penilaian.id_jobrole, penilaian.created_at, penilaian.updated_at, karyawan.id_karyawan, karyawan.kode_kartu, karyawan.nama_karyawan, karyawan.jenis_kelamin, karyawan.tgl_masuk, job_role.keterangan, bagian.id_bagian, bagian.nama_bagian, bagian.area, bagian.area_utama, batch.id_batch, batch.nama_batch, periode.nama_periode, periode.start_date, periode.end_date, periode.jml_libur, MONTH(periode.end_date) as bulan')
            ->join('karyawan', 'karyawan.id_karyawan=penilaian.karyawan_id')
            ->join('job_role', 'job_role.id_jobrole=penilaian.id_jobrole')
            ->join('bagian', 'bagian.id_bagian=job_role.id_bagian')
            ->join('periode', 'periode.id_periode=penilaian.id_periode')
            ->join('batch', 'batch.id_batch=periode.id_batch')
            ->groupBy('periode.end_date')
            ->get(3)
            ->getResultArray();
    }

    public function getPenilaianGroupByBatch()
    {
        return $this->db->table('penilaian')
            ->select('penilaian.id_penilaian, penilaian.karyawan_id, penilaian.id_periode, penilaian.bobot_nilai, penilaian.index_nilai, penilaian.id_user, penilaian.id_jobrole, penilaian.created_at, penilaian.updated_at, karyawan.nama_karyawan, job_role.keterangan, bagian.id_bagian, bagian.nama_bagian, bagian.area, bagian.area_utama, batch.id_batch, batch.nama_batch, periode.nama_periode, periode.start_date, periode.end_date')
            ->join('karyawan', 'karyawan.id_karyawan=penilaian.karyawan_id')
            ->join('job_role', 'job_role.id_jobrole=penilaian.id_jobrole')
            ->join('bagian', 'bagian.id_bagian=job_role.id_bagian')
            ->join('periode', 'periode.id_periode=penilaian.id_periode')
            ->join('batch', 'batch.id_batch=periode.id_batch')
            ->groupBy('batch.id_batch')
            ->get()
            ->getResultArray();
    }

    public function getPenilaianGroupByBatchAndAreaByIdBatchAndBulan($id_batch, $area_utama, $bulan)
    {
        return $this->db->table('penilaian')
            ->select('penilaian.id_penilaian, penilaian.karyawan_id, penilaian.id_periode, penilaian.bobot_nilai, penilaian.index_nilai, penilaian.id_user, penilaian.id_jobrole, penilaian.created_at, penilaian.updated_at, karyawan.id_karyawan, karyawan.kode_kartu, karyawan.nama_karyawan, karyawan.jenis_kelamin, karyawan.tgl_masuk, job_role.keterangan, bagian.id_bagian, bagian.nama_bagian, bagian.area, bagian.area_utama, batch.id_batch, batch.nama_batch, periode.nama_periode, periode.start_date, periode.end_date, periode.jml_libur')
            ->join('karyawan', 'karyawan.id_karyawan=penilaian.karyawan_id')
            ->join('job_role', 'job_role.id_jobrole=penilaian.id_jobrole')
            ->join('bagian', 'bagian.id_bagian=job_role.id_bagian')
            ->join('periode', 'periode.id_periode=penilaian.id_periode')
            ->join('batch', 'batch.id_batch=periode.id_batch')
            ->where('batch.id_batch', $id_batch)
            ->where('bagian.area_utama', $area_utama)
            ->where('MONTH(periode.end_date)', $bulan)
            ->groupBy('penilaian.karyawan_id')
            ->get()
            ->getResultArray();
    }

    public function getPenilaianGroupByBulan($id_karyawan, $id_batch)
    {
        return $this->db->table('penilaian')
            ->select('penilaian.id_penilaian, penilaian.karyawan_id, penilaian.id_periode, penilaian.bobot_nilai, penilaian.index_nilai, penilaian.grade_akhir, penilaian.id_user, penilaian.id_jobrole, penilaian.created_at, penilaian.updated_at, karyawan.nama_karyawan, job_role.keterangan, bagian.id_bagian, bagian.nama_bagian, bagian.area, bagian.area_utama, batch.id_batch, batch.nama_batch, periode.nama_periode, periode.start_date, periode.end_date, periode.jml_libur, MONTH(periode.end_date) as bulan')
            ->join('karyawan', 'karyawan.id_karyawan=penilaian.karyawan_id')
            ->join('job_role', 'job_role.id_jobrole=penilaian.id_jobrole')
            ->join('bagian', 'bagian.id_bagian=job_role.id_bagian')
            ->join('periode', 'periode.id_periode=penilaian.id_periode')
            ->join('batch', 'batch.id_batch=periode.id_batch')
            ->where('penilaian.karyawan_id', $id_karyawan)
            ->where('batch.id_batch', $id_batch)
            // ->where('job_role.id_jobrole', $id_jobrole)
            ->groupBy('periode.end_date')
            ->get()
            ->getResultArray();
    }

    public function getPenilaianWhereAreautamaGroupByBatch($area_utama)
    {
        return $this->table('penilaian')
            ->select('penilaian.id_penilaian, penilaian.karyawan_id, penilaian.id_periode, penilaian.bobot_nilai, penilaian.index_nilai, penilaian.id_user, penilaian.id_jobrole, penilaian.created_at, penilaian.updated_at, karyawan.id_karyawan, karyawan.kode_kartu, karyawan.nama_karyawan, karyawan.jenis_kelamin, karyawan.tgl_masuk, job_role.keterangan, bagian.id_bagian, bagian.nama_bagian, bagian.area, bagian.area_utama, batch.id_batch, batch.nama_batch, periode.nama_periode, periode.start_date, periode.end_date, periode.jml_libur')
            ->join('karyawan', 'karyawan.id_karyawan=penilaian.karyawan_id')
            ->join('job_role', 'job_role.id_jobrole=penilaian.id_jobrole')
            ->join('bagian', 'bagian.id_bagian=job_role.id_bagian')
            ->join('periode', 'periode.id_periode=penilaian.id_periode')
            ->join('batch', 'batch.id_batch=periode.id_batch')
            ->where('bagian.area_utama', $area_utama)
            ->groupBy('batch.id_batch')
            ->get()
            ->getResultArray();
    }

    public function getExistingPenilaian($id_periode, $id_jobrole, $id_karyawan)
    {
        // $cek =
        // dd ($id_karyawan, $id_periode, $id_jobrole);
        return $this->table('penilaian')
            ->select('penilaian.id_penilaian, penilaian.karyawan_id, penilaian.id_periode, penilaian.bobot_nilai, penilaian.index_nilai, penilaian.id_user, penilaian.id_jobrole, penilaian.created_at, penilaian.updated_at, karyawan.id_karyawan, karyawan.kode_kartu, karyawan.nama_karyawan, karyawan.jenis_kelamin, karyawan.tgl_masuk, job_role.keterangan, bagian.id_bagian, bagian.nama_bagian, bagian.area, bagian.area_utama, batch.id_batch, batch.nama_batch, periode.nama_periode, periode.start_date, periode.end_date, periode.jml_libur')
            ->join('karyawan', 'karyawan.id_karyawan=penilaian.karyawan_id')
            ->join('job_role', 'job_role.id_jobrole=penilaian.id_jobrole')
            ->join('bagian', 'bagian.id_bagian=job_role.id_bagian')
            ->join('periode', 'periode.id_periode=penilaian.id_periode')
            ->join('batch', 'batch.id_batch=periode.id_batch')
            ->whereIn('penilaian.karyawan_id', $id_karyawan)
            ->where('penilaian.id_periode', $id_periode)
            ->where('penilaian.id_jobrole', $id_jobrole)
            ->get()
            ->getResultArray();

        // dd ($cek);
    }

    public function getPenilaianTitle($id_bagian, $id_periode, $id_jobrole)
    {
        return $this->table('penilaian')
            ->select('penilaian.id_penilaian, penilaian.karyawan_id, penilaian.id_periode, penilaian.bobot_nilai, penilaian.index_nilai, penilaian.id_user, penilaian.id_jobrole, penilaian.created_at, penilaian.updated_at, karyawan.nama_karyawan, job_role.keterangan, bagian.id_bagian, bagian.nama_bagian, bagian.area, bagian.area_utama, batch.id_batch, batch.nama_batch, periode.nama_periode, periode.start_date, periode.end_date')
            ->join('karyawan', 'karyawan.id_karyawan=penilaian.karyawan_id')
            ->join('job_role', 'job_role.id_jobrole=penilaian.id_jobrole')
            ->join('bagian', 'bagian.id_bagian=job_role.id_bagian')
            ->join('periode', 'periode.id_periode=penilaian.id_periode')
            ->join('batch', 'batch.id_batch=periode.id_batch')
            ->where('job_role.id_bagian', $id_bagian)
            ->where('penilaian.id_periode', $id_periode)
            ->where('penilaian.id_jobrole', $id_jobrole)
            ->get(1)
            ->getResultArray();
    }

    // public function getPreviousIndexNilai($id_bagian, $id_periode_sekarang, id_jobrole)
    // {
    //     $tes =  $this->table('penilaian')
    //         ->select('index_nilai')
    //         ->where('id_bagian', $id_bagian) // Cari job role yang sama
    //         ->where('id_periode <', $id_periode_sekarang) // Cari periode sebelumnya
    //         ->orderBy('id_periode', 'DESC') // Ambil periode paling terakhir sebelum periode saat ini
    //         ->limit(1)
    //         ->get()
    //         ->getRowArray();

    // dd ($tes);
    // }

    public function getPenilaianPerArea($area_utama)
    {
        return $this->table('penilaian')
            ->select('
            penilaian.id_penilaian,
            penilaian.karyawan_id,
            penilaian.id_periode,
            penilaian.bobot_nilai,
            penilaian.index_nilai,
            penilaian.id_user,
            penilaian.id_jobrole,
            penilaian.created_at,
            penilaian.updated_at,
            karyawan.nama_karyawan,
            job_role.keterangan,
            bagian.id_bagian,
            bagian.nama_bagian,
            bagian.area,
            bagian.area_utama,
            batch.id_batch,
            batch.nama_batch,
            periode.nama_periode,
            periode.start_date,
            periode.end_date
        ')
            ->join('karyawan', 'karyawan.id_karyawan = penilaian.karyawan_id')
            ->join('job_role', 'job_role.id_jobrole = penilaian.id_jobrole')
            ->join('bagian', 'bagian.id_bagian = job_role.id_bagian')
            ->join('periode', 'periode.id_periode = penilaian.id_periode')
            ->join('batch', 'batch.id_batch = periode.id_batch')
            ->where('bagian.area_utama', $area_utama)
            ->groupBy('batch.id_batch, penilaian.id_periode, bagian.area_utama')
            // urutkan berdasarkan bulan dari kolom end_date secara ascending
            ->orderBy('periode.start_date', 'ASC')
            // ->orderBy('batch.id_batch', 'ASC') // Urutkan jika diperlukan
            ->get()
            ->getResultArray();
    }


    public function getPenilaianPerPeriode($area_utama, $id_periode)
    {
        return $this->table('penilaian')
            ->select('penilaian.id_penilaian, penilaian.karyawan_id, penilaian.id_periode, penilaian.bobot_nilai, penilaian.index_nilai, penilaian.grade_akhir, penilaian.id_user, penilaian.id_jobrole, penilaian.created_at, penilaian.updated_at, karyawan.nama_karyawan, job_role.keterangan, bagian.id_bagian, bagian.nama_bagian, bagian.area, bagian.area_utama, batch.id_batch, batch.nama_batch, periode.nama_periode, periode.start_date, periode.end_date')
            ->join('karyawan', 'karyawan.id_karyawan=penilaian.karyawan_id')
            ->join('job_role', 'job_role.id_jobrole=penilaian.id_jobrole')
            ->join('bagian', 'bagian.id_bagian=job_role.id_bagian')
            ->join('periode', 'periode.id_periode=penilaian.id_periode')
            ->join('batch', 'batch.id_batch=periode.id_batch')
            ->where('bagian.area_utama', $area_utama)
            ->where('penilaian.id_periode', $id_periode)
            ->get()
            ->getResultArray();
    }

    public function getPenilaianByAreaByNamaBatchByNamaPeriode($area_utama, $nama_batch, $nama_periode)
    {
        return $this->table('penilaian')
            ->select('
            penilaian.id_penilaian,
            penilaian.karyawan_id,
            penilaian.id_periode,
            penilaian.bobot_nilai,
            penilaian.index_nilai,
            penilaian.grade_akhir,
            penilaian.id_user,
            penilaian.id_jobrole,
            penilaian.created_at,
            penilaian.updated_at,
            karyawan.kode_kartu,
            karyawan.nama_karyawan,
            karyawan.jenis_kelamin,
            karyawan.tgl_masuk,
            karyawan.shift,
            job_role.jobdesc,
            job_role.keterangan,
            bagian.id_bagian,
            bagian.nama_bagian,
            bagian.area,
            bagian.area_utama,
            absen.id_absen,
            absen.id_karyawan,
            absen.id_periode,
            absen.sakit,
            absen.izin,
            absen.mangkir,
            batch.id_batch,
            batch.nama_batch,
            periode.nama_periode,
            periode.start_date,
            periode.end_date,
            ROUND(SUM(bs_mc.produksi),2)       AS prod_op,
            ROUND(SUM(bs_mc.bs_mc),2)           AS bs_mc,
            ROUND(SUM(sum_rosso.produksi),2)    AS prod_rosso,
            ROUND(SUM(sum_rosso.perbaikan),2)   AS perb_rosso,
            ROUND(SUM(sum_jarum.used_needle),2) AS used_needle,
            (SELECT grade_akhir 
             FROM penilaian AS prev_penilaian
             JOIN periode AS prev_periode ON prev_penilaian.id_periode = prev_periode.id_periode
             WHERE prev_penilaian.karyawan_id = penilaian.karyawan_id
             AND prev_periode.end_date < periode.start_date
             ORDER BY prev_periode.end_date DESC LIMIT 1
            ) AS previous_grade
        ')
            ->join('karyawan', 'karyawan.id_karyawan = penilaian.karyawan_id')
            ->join('job_role', 'job_role.id_jobrole = penilaian.id_jobrole')
            ->join('bagian', 'bagian.id_bagian = job_role.id_bagian')
            ->join('absen', 'absen.id_karyawan = penilaian.karyawan_id', 'left')
            ->where('absen.id_periode = penilaian.id_periode')
            ->join('periode', 'periode.id_periode = penilaian.id_periode')
            ->join('batch', 'batch.id_batch = periode.id_batch')
            ->join('bs_mc', "bs_mc.id_karyawan = penilaian.karyawan_id
                        AND bs_mc.tgl_input BETWEEN periode.start_date AND periode.end_date", 'left')
            ->join('sum_rosso', "sum_rosso.id_karyawan = penilaian.karyawan_id
                            AND sum_rosso.tgl_input BETWEEN periode.start_date AND periode.end_date", 'left')
            ->join('sum_jarum', "sum_jarum.id_karyawan = penilaian.karyawan_id
                            AND sum_jarum.tgl_input BETWEEN periode.start_date AND periode.end_date", 'left')
            ->where('bagian.area_utama', $area_utama)
            ->where('batch.nama_batch', $nama_batch)
            ->where('periode.nama_periode', $nama_periode)
            ->groupBy('penilaian.id_penilaian')
            ->get()
            ->getResultArray();
    }

    public function getFluktuasiGrade()
    {
        $query = $this->db->query("
        SELECT 
            MONTH(periode.end_date) AS month, 
            AVG(penilaian.index_nilai) AS average_grade
        FROM penilaian
        JOIN periode ON penilaian.id_periode = periode.id_periode
        GROUP BY MONTH(periode.end_date)
        ORDER BY MONTH(periode.end_date)
    ");
        return $query->getResultArray();
    }

    public function updateGradeAkhir($id_karyawan, $id_periode, $data)
    {
        return $this->db->table('penilaian')
            ->where('karyawan_id', $id_karyawan)
            ->where('id_periode', $id_periode)
            ->update(['grade_akhir' => $data]);
    }


    public function getGradeChangeData()
    {
        $db = \Config\Database::connect();

        $query = "
        WITH grade_mapping AS (
            SELECT 'D' AS grade, 1 AS grade_numeric
            UNION ALL SELECT 'C', 2
            UNION ALL SELECT 'B', 3
            UNION ALL SELECT 'A', 4
        ),
        data_with_numeric AS (
            SELECT
                t.karyawan_id,
                t.id_periode,
                t.index_nilai AS grade_awal,
                t.grade_akhir,
                gm1.grade_numeric AS grade_awal_numeric,
                gm2.grade_numeric AS grade_akhir_numeric
            FROM penilaian t
            LEFT JOIN grade_mapping gm1 ON t.index_nilai = gm1.grade
            LEFT JOIN grade_mapping gm2 ON t.grade_akhir = gm2.grade
        ),
        grade_changes AS (
            SELECT
                karyawan_id,
                CONCAT(grade_awal, grade_akhir) AS grade_change,
                grade_awal_numeric,
                grade_akhir_numeric,
                grade_akhir_numeric - grade_awal_numeric AS diff
            FROM data_with_numeric
            WHERE grade_awal_numeric IS NOT NULL
              AND grade_akhir_numeric IS NOT NULL
              AND grade_akhir_numeric > grade_awal_numeric
        )
        SELECT
            grade_change,
            COUNT(*) AS jumlah,
            CASE
                WHEN diff = 1 THEN 'Kenaikan Satu Tingkat'
                ELSE 'Kenaikan Lebih Dari Satu Tingkat'
            END AS kategori
        FROM grade_changes
        GROUP BY grade_change, kategori
        ORDER BY kategori, grade_change;
        ";

        // Execute the query and return the result
        return $db->query($query)->getResultArray();
    }

    public function getRataRataGrade()
    {
        $sql = "SELECT 
                    CASE 
                        WHEN avg_value >= 3.5 THEN 'A'
                        WHEN avg_value >= 2.5 THEN 'B'
                        WHEN avg_value >= 1.5 THEN 'C'
                        ELSE 'D'
                    END AS average_grade_letter
                FROM (
                    SELECT 
                        AVG(
                            CASE 
                                WHEN grade_akhir = 'A' THEN 4
                                WHEN grade_akhir = 'B' THEN 3
                                WHEN grade_akhir = 'C' THEN 2
                                WHEN grade_akhir = 'D' THEN 1
                            END
                        ) AS avg_value
                    FROM penilaian
                ) AS avg_table";

        $query = $this->db->query($sql);

        return $query->getRowArray();
    }

    public function getMandorEvaluationStatus($id_periode)
    {
        $builder = $this->db->table('user');
        $builder->select('
        user.id_user, 
        user.username, 
        user.role,
        user.area,
        COUNT(DISTINCT karyawan.id_karyawan) AS total_karyawan, 
        COUNT(DISTINCT penilaian.id_penilaian) AS total_penilaian,
        penilaian.id_periode
    ');
        // Join tabel bagian, karyawan, dan penilaian
        $builder->join('bagian', 'bagian.area = user.area', 'left');
        $builder->join('karyawan', 'karyawan.id_bagian = bagian.id_bagian', 'left');
        // Menambahkan kondisi id_periode langsung di join penilaian agar record mandor tetap muncul walau belum ada penilaian
        $builder->join('penilaian', "penilaian.karyawan_id = karyawan.id_karyawan 
                                  AND penilaian.id_user = user.id_user 
                                  AND penilaian.id_periode = '$id_periode'", 'left');
        $builder->where('user.role', 'Mandor');
        $builder->groupBy('user.id_user');

        return $builder->get()->getResultArray();
    }


    public function getEmployeeEvaluationStatus($periode, $area)
    {
        // Menggunakan alias "k" untuk tabel karyawan
        $builder = $this->db->table('karyawan as k');
        $builder->select("
        k.id_karyawan,
        k.kode_kartu,
        k.nama_karyawan,
        k.shift,
        bagian.nama_bagian,
        bagian.area,
        IF(p.id_penilaian IS NULL, 'Belum Dinilai', 'Sudah Dinilai') AS status
    ", false);
        $builder->join('bagian', 'bagian.id_bagian = k.id_bagian', 'left');
        $builder->join('penilaian as p', "p.karyawan_id = k.id_karyawan AND p.id_periode = '{$periode}'", 'left');
        $builder->where('bagian.area', $area);
        $builder->groupBy('k.id_karyawan');
        $builder->groupBy('p.id_periode');

        return $builder->get()->getResultArray();
    }

    // public function raportPenilaian($area, $batch = null)
    // {
    //     return $this->db->table('penilaian p') // Pastikan alias digunakan dengan benar
    //         ->select('k.kode_kartu, k.nama_karyawan, k.jenis_kelamin, k.tgl_masuk, k.shift, b.nama_bagian, p.index_nilai, p.grade_akhir, p.id_periode, bc.id_batch')
    //         ->join('karyawan k', 'k.id_karyawan = p.karyawan_id', 'left')
    //         ->join('bagian b', 'b.id_bagian = k.id_bagian', 'left')
    //         ->join('periode per', 'per.id_periode = p.id_periode', 'left')
    //         ->join('batch bc', 'bc.id_batch = per.id_batch', 'left')
    //         ->where('b.area', $area)
    //         ->where('bc.id_batch', $batch)
    //         ->groupBy('p.id_penilaian')
    //         ->get()
    //         ->getResultArray();
    // }

    public function raportPenilaian($area)
    {
        return $this->db->table('penilaian p')
            ->select("
            k.kode_kartu, k.nama_karyawan, k.jenis_kelamin, k.tgl_masuk, k.shift, b.nama_bagian,
            MAX(CASE WHEN MONTH(per.end_date) = 1 THEN CONCAT(COALESCE(p.index_nilai, '0'),  COALESCE(p.grade_akhir, '-')) END) AS nilai_jan,
            MAX(CASE WHEN MONTH(per.end_date) = 2 THEN CONCAT(COALESCE(p.index_nilai, '0'),  COALESCE(p.grade_akhir, '-')) END) AS nilai_feb,
            MAX(CASE WHEN MONTH(per.end_date) = 3 THEN CONCAT(COALESCE(p.index_nilai, '0'), COALESCE(p.grade_akhir, '-')) END) AS nilai_mar,
            MAX(CASE WHEN MONTH(per.end_date) = 4 THEN CONCAT(COALESCE(p.index_nilai, '0'), COALESCE(p.grade_akhir, '-')) END) AS nilai_apr,
            MAX(CASE WHEN MONTH(per.end_date) = 5 THEN CONCAT(COALESCE(p.index_nilai, '0'), COALESCE(p.grade_akhir, '-')) END) AS nilai_mei,
            MAX(CASE WHEN MONTH(per.end_date) = 6 THEN CONCAT(COALESCE(p.index_nilai, '0'), COALESCE(p.grade_akhir, '-')) END) AS nilai_jun,
            MAX(CASE WHEN MONTH(per.end_date) = 7 THEN CONCAT(COALESCE(p.index_nilai, '0'), COALESCE(p.grade_akhir, '-')) END) AS nilai_jul,
            MAX(CASE WHEN MONTH(per.end_date) = 8 THEN CONCAT(COALESCE(p.index_nilai, '0'), COALESCE(p.grade_akhir, '-')) END) AS nilai_agu,
            MAX(CASE WHEN MONTH(per.end_date) = 9 THEN CONCAT(COALESCE(p.index_nilai, '0'), COALESCE(p.grade_akhir, '-')) END) AS nilai_sep,
            MAX(CASE WHEN MONTH(per.end_date) = 10 THEN CONCAT(COALESCE(p.index_nilai, '0'), COALESCE(p.grade_akhir, '-')) END) AS nilai_okt,
            MAX(CASE WHEN MONTH(per.end_date) = 11 THEN CONCAT(COALESCE(p.index_nilai, '0'), COALESCE(p.grade_akhir, '-')) END) AS nilai_nov,
            MAX(CASE WHEN MONTH(per.end_date) = 12 THEN CONCAT(COALESCE(p.index_nilai, '0'), COALESCE(p.grade_akhir, '-')) END) AS nilai_des
        ")
            ->join('karyawan k', 'k.id_karyawan = p.karyawan_id', 'left')
            ->join('bagian b', 'b.id_bagian = k.id_bagian', 'left')
            ->join('periode per', 'per.id_periode = p.id_periode', 'left')
            ->join('batch bc', 'bc.id_batch = per.id_batch', 'left')
            ->where('b.area', $area)
            ->groupBy('k.kode_kartu, k.nama_karyawan, k.jenis_kelamin, k.tgl_masuk, k.shift, b.nama_bagian')
            ->orderBy('k.shift')
            ->get()
            ->getResultArray();
    }

    public function filterReportBatch($area_utama = null, $nama_bagian = null, $batch = null)
    {
        $builder = $this->table('penilaian')
            ->select('penilaian.id_penilaian, penilaian.karyawan_id, penilaian.id_periode, penilaian.bobot_nilai, penilaian.index_nilai, penilaian.id_user, penilaian.id_jobrole, penilaian.created_at, penilaian.updated_at, karyawan.id_karyawan, karyawan.kode_kartu, karyawan.nama_karyawan, karyawan.jenis_kelamin, karyawan.tgl_masuk, job_role.keterangan, bagian.id_bagian, bagian.nama_bagian, bagian.area, bagian.area_utama, batch.id_batch, batch.nama_batch, periode.nama_periode, periode.start_date, periode.end_date, periode.jml_libur')
            ->join('karyawan', 'karyawan.id_karyawan=penilaian.karyawan_id')
            ->join('job_role', 'job_role.id_jobrole=penilaian.id_jobrole')
            ->join('bagian', 'bagian.id_bagian=job_role.id_bagian')
            ->join('periode', 'periode.id_periode=penilaian.id_periode')
            ->join('batch', 'batch.id_batch=periode.id_batch');

        if ($area_utama !== null) {
            $builder->where('bagian.area_utama', $area_utama);
        }

        if ($nama_bagian !== null) {
            $builder->where('bagian.nama_bagian', $nama_bagian);
        }

        if ($batch !== null) {
            $builder->where('batch.nama_batch', $batch);
        }

        return $builder->get()->getResultArray();
    }

    public function getPenilaianGroupByBatchAllArea()
    {
        return $this->table('penilaian')
            ->select('penilaian.id_penilaian, penilaian.karyawan_id, penilaian.id_periode, penilaian.bobot_nilai, penilaian.index_nilai, penilaian.id_user, penilaian.id_jobrole, penilaian.created_at, penilaian.updated_at, karyawan.id_karyawan, karyawan.kode_kartu, karyawan.nama_karyawan, karyawan.jenis_kelamin, karyawan.tgl_masuk, job_role.keterangan, bagian.id_bagian, bagian.nama_bagian, bagian.area, bagian.area_utama, batch.id_batch, batch.nama_batch, periode.nama_periode, periode.start_date, periode.end_date, periode.jml_libur')
            ->join('karyawan', 'karyawan.id_karyawan=penilaian.karyawan_id')
            ->join('job_role', 'job_role.id_jobrole=penilaian.id_jobrole')
            ->join('bagian', 'bagian.id_bagian=job_role.id_bagian')
            ->join('periode', 'periode.id_periode=penilaian.id_periode')
            ->join('batch', 'batch.id_batch=periode.id_batch')
            ->groupBy('batch.id_batch')
            ->get()
            ->getResultArray();
    }

    public function getPenilaianByBatchAllArea($id_batch)
    {
        return $this->db->table('penilaian')
            ->select('penilaian.id_penilaian, penilaian.karyawan_id, penilaian.id_periode, penilaian.bobot_nilai, penilaian.index_nilai, penilaian.id_user, penilaian.id_jobrole, penilaian.created_at, penilaian.updated_at, karyawan.id_karyawan, karyawan.kode_kartu, karyawan.nama_karyawan, karyawan.jenis_kelamin, karyawan.tgl_masuk, job_role.keterangan, bagian.id_bagian, bagian.nama_bagian, bagian.area, bagian.area_utama, batch.id_batch, batch.nama_batch, periode.nama_periode, periode.start_date, periode.end_date, periode.jml_libur')
            ->join('karyawan', 'karyawan.id_karyawan=penilaian.karyawan_id')
            ->join('job_role', 'job_role.id_jobrole=penilaian.id_jobrole')
            ->join('bagian', 'bagian.id_bagian=job_role.id_bagian')
            ->join('periode', 'periode.id_periode=penilaian.id_periode')
            ->join('batch', 'batch.id_batch=periode.id_batch')
            ->where('batch.id_batch', $id_batch)
            ->notLike('karyawan.kode_kartu', 'KKKK%')
            ->groupBy('penilaian.karyawan_id')
            ->get()
            ->getResultArray();
    }

    public function getBatchGroupByBulanPenilaianRev($id_batch)
    {
        return $this->db->table('penilaian')
            ->select('penilaian.id_penilaian, penilaian.karyawan_id, penilaian.id_periode, penilaian.bobot_nilai, penilaian.index_nilai, penilaian.id_user, penilaian.id_jobrole, penilaian.created_at, penilaian.updated_at, karyawan.id_karyawan, karyawan.kode_kartu, karyawan.nama_karyawan, karyawan.jenis_kelamin, karyawan.tgl_masuk, job_role.keterangan, bagian.id_bagian, bagian.nama_bagian, bagian.area, bagian.area_utama, batch.id_batch, batch.nama_batch, periode.nama_periode, periode.start_date, periode.end_date, periode.jml_libur, MONTH(periode.end_date) as bulan')
            ->join('karyawan', 'karyawan.id_karyawan = penilaian.karyawan_id')
            ->join('job_role', 'job_role.id_jobrole = penilaian.id_jobrole')
            ->join('bagian', 'bagian.id_bagian = job_role.id_bagian')
            ->join('periode', 'periode.id_periode = penilaian.id_periode')
            ->join('batch', 'batch.id_batch = periode.id_batch')
            ->where('batch.id_batch', $id_batch)
            ->groupBy('periode.end_date')
            ->get()
            ->getResultArray();
    }

    public function getAreaByIdBatch($id_batch)
    {
        return $this->db->table('batch')
            ->select('area')
            ->where('id_batch', $id_batch)
            ->get()
            ->getRowArray();
    }

    public function getAllPenilaian()
    {
        return $this->table('penilaian')
            ->select('
            penilaian.id_penilaian,
            penilaian.karyawan_id,
            penilaian.id_periode,
            penilaian.bobot_nilai,
            penilaian.index_nilai,
            penilaian.grade_akhir,
            penilaian.id_user,
            penilaian.id_jobrole,
            penilaian.created_at,
            penilaian.updated_at,
            karyawan.kode_kartu,
            karyawan.nama_karyawan,
            karyawan.jenis_kelamin,
            karyawan.tgl_masuk,
            karyawan.shift,
            job_role.jobdesc,
            job_role.keterangan,
            bagian.id_bagian,
            bagian.nama_bagian,
            bagian.area,
            bagian.area_utama,
            absen.id_absen,
            absen.id_karyawan,
            absen.id_periode,
            absen.sakit,
            absen.izin,
            absen.mangkir,
            batch.id_batch,
            batch.nama_batch,
            periode.nama_periode,
            periode.start_date,
            periode.end_date,
            (SELECT grade_akhir 
             FROM penilaian AS prev_penilaian
             JOIN periode AS prev_periode ON prev_penilaian.id_periode = prev_periode.id_periode
             WHERE prev_penilaian.karyawan_id = penilaian.karyawan_id
             AND prev_periode.end_date < periode.start_date
             ORDER BY prev_periode.end_date DESC LIMIT 1
            ) AS previous_grade
        ')
            ->join('karyawan', 'karyawan.id_karyawan = penilaian.karyawan_id')
            ->join('job_role', 'job_role.id_jobrole = penilaian.id_jobrole')
            ->join('bagian', 'bagian.id_bagian = job_role.id_bagian')
            ->join('absen', 'absen.id_karyawan = penilaian.karyawan_id')
            ->where('absen.id_periode = penilaian.id_periode')
            ->join('periode', 'periode.id_periode = penilaian.id_periode')
            ->join('batch', 'batch.id_batch = periode.id_batch')
            ->get()
            ->getResultArray();
    }
}
