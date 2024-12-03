<?php

namespace App\Models;

use CodeIgniter\Model;
use PhpParser\Node\Expr\Cast;

class JobroleModel extends Model
{
    protected $table            = 'job_role';
    protected $primaryKey       = 'id_jobrole';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = ['id_jobrole', 'id_bagian', 'status', 'jobdesc', 'keterangan'];

    protected bool $allowEmptyInserts = false;
    protected bool $updateOnlyChanged = true;

    // In JobroleModel.php
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
            ->select('job_role.id_jobrole, job_role.id_bagian, job_role.jobdesc, job_role.keterangan, bagian.nama_bagian, bagian.area')
            ->join('bagian', 'bagian.id_bagian=job_role.id_bagian')
            ->get()
            ->getResultArray();
    }

    public function getIdJobrole($id_bagian)
    {
        return $this->select('id_jobrole')
            ->where('id_bagian', $id_bagian)
            ->first();
    }
    // Mengambil data job role berdasarkan ID bagian
    public function getJobRoleByBagianId($id_bagian)
    {
        return $this->db->table('job_role')
            ->select('job_role.id_jobrole, job_role.id_bagian, job_role.jobdesc, job_role.keterangan, bagian.nama_bagian,bagian.area_utama, bagian.area')
            ->join('bagian', 'bagian.id_bagian = job_role.id_bagian')
            ->where('job_role.id_bagian', $id_bagian)
            ->get()
            ->getRowArray();  // Gunakan getRowArray() jika hanya ingin mengambil satu baris
    }

    // Mengambil data job role berdasarkan ID job role
    public function getJobRolesByJobRoleId($id_jobrole)
    {
        return $this->db->table('job_role')
            ->select('job_role.id_jobrole, job_role.id_bagian, job_role.jobdesc, job_role.keterangan, bagian.nama_bagian,bagian.area_utama, bagian.area')
            ->join('bagian', 'bagian.id_bagian = job_role.id_bagian')
            ->where('job_role.id_jobrole', $id_jobrole)
            ->get()
            ->getRowArray();  // Gunakan getRowArray() jika hanya ingin mengambil satu baris
    }

    public function safeJsonDecode($data = '')
    {
        $decoded = json_decode($data, true);
        return json_last_error() === JSON_ERROR_NONE ? $decoded : []; // Return empty array on failure
    }


    public function find($id = null)
    {
        $result = parent::find($id);

        if ($result) {
            // Decode JSON fields safely to ensure they are arrays
            $result['keterangan'] = $this->safeJsonDecode($result['keterangan'] ?? '[]');
            $result['jobdesc'] = $this->safeJsonDecode($result['jobdesc'] ?? '[]');
        }

        return $result;
    }

    // Optional: You can also ensure to cast back to valid JSON on update if needed
    public function saveJobrole($data)
    {
        // Encode jobdesc and keterangan back to JSON before saving
        $data['jobdesc'] = json_encode($data['jobdesc']);
        $data['keterangan'] = json_encode($data['keterangan']);

        return $this->save($data);
    }

    public function getAllData()
    {
        return $this->findAll();
    }
}
