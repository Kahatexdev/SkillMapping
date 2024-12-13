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
    protected $allowedFields    = ['id_penilaian', 'karyawan_id', 'id_periode', 'bobot_nilai', 'index_nilai', 'id_user', 'id_jobrole', 'created_at', 'updated_at'];

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

            ->groupBy('penilaian.karyawan_id')
            // ->groupBy('batch.id_batch')
            // ->groupBy('bagian.area_utama')
            // ->groupBy('bagian.area_utama')
            ->get()
            ->getResultArray();
    }

    public function getBatchGroupByBulanPenilaian(){
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

    public function getPenilaianGroupByBulan($id_karyawan, $id_batch, $id_jobrole)
    {
        return $this->db->table('penilaian')
            ->select('penilaian.id_penilaian, penilaian.karyawan_id, penilaian.id_periode, penilaian.bobot_nilai, penilaian.index_nilai, penilaian.id_user, penilaian.id_jobrole, penilaian.created_at, penilaian.updated_at, karyawan.nama_karyawan, job_role.keterangan, bagian.id_bagian, bagian.nama_bagian, bagian.area, bagian.area_utama, batch.id_batch, batch.nama_batch, periode.nama_periode, periode.start_date, periode.end_date, periode.jml_libur, MONTH(periode.end_date) as bulan')
            ->join('karyawan', 'karyawan.id_karyawan=penilaian.karyawan_id')
            ->join('job_role', 'job_role.id_jobrole=penilaian.id_jobrole')
            ->join('bagian', 'bagian.id_bagian=job_role.id_bagian')
            ->join('periode', 'periode.id_periode=penilaian.id_periode')
            ->join('batch', 'batch.id_batch=periode.id_batch')
            ->where('penilaian.karyawan_id', $id_karyawan)
            ->where('batch.id_batch', $id_batch)
            ->where('job_role.id_jobrole', $id_jobrole)
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

}
