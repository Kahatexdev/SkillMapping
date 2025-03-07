<?php

namespace App\Models;

use CodeIgniter\Model;

class UserModel extends Model
{
    protected $table            = 'user';
    protected $primaryKey       = 'id_user';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = ['id_user', 'username', 'password', 'role', 'area'];

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


    public function login($username, $password)
    {
        $user = $this->where(['username' => $username, 'password' => $password])->first();

        if (!$user) {
            return null;
        }

        return [
            'id_user' => $user['id_user'],
            'role' => $user['role'],
            'username' => $user['username'],
            'area' => $user['area']
        ];
    }

    public function getdata()
    {
        return $this->select('karyawan.id_karyawan, karyawan.kode_kartu, karyawan.nama_karyawan, karyawan.tanggal_masuk, karyawan.jenis_kelamin, karyawan.shift, bagian.nama_bagian')
            ->join('bagian', 'bagian.id_bagian = karyawan.id_bagian')
            ->findAll();
    }

    public function findIdByRole($role)
    {
        return $this->select('id_user, username, area')
            ->where('role', $role)
            ->findAll();
    }
}
