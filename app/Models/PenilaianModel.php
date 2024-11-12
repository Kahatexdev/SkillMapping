<?php

namespace App\Models;

use CodeIgniter\Model;

class PenilaianModel extends Model
{
    protected $table            = 'penilaian';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = ['id_penilaian', 'karyawan_id', 'id_batch', 'bobot_nilai', 'index_nilai', 'id_user', 'id_jobrole', 'created_at', 'updated_at'];

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

    public function cekPenilaian($karyawan_id, $id_batch, $id_jobrole, $id_user)
    {
        return $this->where('karyawan_id', $karyawan_id)
            ->where('id_batch', $id_batch)
            ->where('id_jobrole', $id_jobrole)
            ->where('id_user', $id_user)
            ->first();
    }

    public function getPenilaian()
    {
        return $this->db->table('penilaian')
            ->select('penilaian.id_penilaian, penilaian.karyawan_id, penilaian.id_batch, penilaian.bobot_nilai, penilaian.index_nilai, penilaian.id_user, penilaian.id_jobrole, penilaian.created_at, penilaian.updated_at, karyawan.nama_karyawan, job_role.keterangan, bagian.nama_bagian, bagian.area, batch.shift, batch.bulan, batch.tahun')
            ->join('karyawan', 'karyawan.id_karyawan=penilaian.karyawan_id')
            ->join('job_role', 'job_role.id_jobrole=penilaian.id_jobrole')
            ->join('bagian', 'bagian.id_bagian=job_role.id_bagian')
            ->join('batch', 'batch.id_batch=penilaian.id_batch')
            // group by batch.id_batch
            ->groupBy('penilaian.id_batch')
            ->get()
            ->getResultArray();
    }
}
