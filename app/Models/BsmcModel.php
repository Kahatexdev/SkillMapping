<?php

namespace App\Models;

use CodeIgniter\Model;

class BsmcModel extends Model
{
    protected $table            = 'bs_mesin';
    protected $primaryKey       = 'id_bsmc';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'id_bsmc',
        'id_karyawan',
        'tanggal',
        'no_model',
        'inisial',
        'qty_prod_mc',
        'qty_bs',
        'created_at',
        'updated_at'
    ];

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

    public function getKaryawan()
    {
        return $this->select('bs_mesin.id_bsmc, bs_mesin.id_karyawan, karyawan.nama_karyawan, karyawan.kode_kartu, karyawan.shift, bs_mesin.tanggal, bs_mesin.no_model, bs_mesin.inisial, bs_mesin.qty_bs, bs_mesin.qty_prod_mc, bs_mesin.created_at, bs_mesin.updated_at')
            ->join('karyawan', 'karyawan.id_karyawan = bs_mesin.id_karyawan')
            ->findAll();
    }
}
