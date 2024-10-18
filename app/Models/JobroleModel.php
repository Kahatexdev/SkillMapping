<?php

namespace App\Models;

use CodeIgniter\Model;

class JobroleModel extends Model
{
    protected $table            = 'job_role';
    protected $primaryKey       = 'id_jobrole';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = ['id_jobrole', 'id_bagian', 'status', 'jobdesc'];

    protected bool $allowEmptyInserts = false;
    protected bool $updateOnlyChanged = true;

    protected array $casts = [];
    protected array $castHandlers = [];

    // Dates
    protected $useTimestamps = false;
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

    // In your Model or Controller
    public function getJobRolesWithBagian()
    {
        return $this->db->table('job_role')
            ->select('job_role.id_jobrole, job_role.id_bagian, job_role.status, job_role.jobdesc, bagian.nama_bagian')
            ->join('bagian', 'bagian.id_bagian=job_role.id_bagian')
            ->get()
            ->getResultArray();
    }

}