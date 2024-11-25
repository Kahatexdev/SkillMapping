<?php

namespace App\Models;

use CodeIgniter\Model;

class SummaryRossoModel extends Model
{
    protected $table            = 'summary_rosso';
    protected $primaryKey       = 'id_sr';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = ['id_sr', 'id_periode', 'id_karyawan', 'tgl_prod_rosso', 'qty_prod_rosso', 'qty_bs', 'created_at', 'updated_at'];

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
        return $this->select('SUM(qty_prod_rosso) as total_qty_prod_rosso, SUM(qty_bs) as total_qty_bs, karyawan.id_bagian, bagian.nama_bagian, karyawan.kode_kartu, karyawan.nama_karyawan, karyawan.jenis_kelamin, karyawan.tgl_masuk')
            ->join('karyawan', 'karyawan.id_karyawan = summary_rosso.id_karyawan')
            ->join('bagian', 'bagian.id_bagian = karyawan.id_bagian')
            ->where('id_periode', $id_periode)
            ->groupBy('summary_rosso.id_karyawan')
            ->get()->getResultArray();
    }
}