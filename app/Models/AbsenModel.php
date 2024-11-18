<?php

namespace App\Models;

use CodeIgniter\Model;

class AbsenModel extends Model
{
    protected $table            = 'absen';
    protected $primaryKey       = 'id_absen';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = ['id_absen', 'id_karyawan', 'bulan', 'izin', 'sakit', 'mangkir', 'cuti', 'id_user', 'created_at', 'updated_at'];

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

    // public function getAbsenWithKaryawan()
    // {
    //     return $this->db->table('absen')
    //         ->select('absen.*, karyawan.nama_karyawan')
    //         ->join('karyawan', 'karyawan.id_karyawan = absen.id_karyawan')
    //         ->get()->getResultArray();
    // }

    public function getdata()
    {
        return $this->select('absen.*, karyawan.nama_karyawan, user.username')
            ->join('karyawan', 'karyawan.id_karyawan = absen.id_karyawan')
            ->join('user', 'user.id_user = absen.id_user')
            ->findAll();
    }
}
