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
    protected $allowedFields    = ['id_sr', 'id_karyawan', 'tgl_prod_rosso', 'qty_prod_rosso', 'qty_bs', 'created_at', 'updated_at'];

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
}
