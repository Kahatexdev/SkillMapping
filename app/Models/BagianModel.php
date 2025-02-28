<?php

namespace App\Models;

use CodeIgniter\Model;

class BagianModel extends Model
{
    protected $table            = 'bagian';
    protected $primaryKey       = 'id_bagian';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = ['id_bagian', 'nama_bagian', 'area_utama', 'area', 'keterangan'];

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

    public function checkBagian($data)
    {
        return $this->where('nama_bagian', $data['nama_bagian'])
            ->where('area', $data['area'])
            ->first();
    }
    public function getIdBagian($nama_bagian, $area_utama, $area)
    {
        return $this->select('id_bagian')
            ->where('nama_bagian', $nama_bagian)
            ->where('area_utama', $area_utama)
            ->where('area', $area)
            ->first();
    }

    public function getBagian()
    {
        // get nama bagian group by nama bagian
        return $this->select('nama_bagian')
            ->groupBy('nama_bagian')
            ->findAll();
    }

    public function getAreaUtama($nama_bagian)
    {
        // get area_utama where nama_bagian = $nama_bagian group by area_utama
        return $this->select('area_utama')
            ->where('nama_bagian', $nama_bagian)
            ->groupBy('area_utama')
            ->findAll();
    }

    public function getArea($nama_bagian, $area_utama)
    {
        // get area where nama_bagian = $nama_bagian and area_utama = $area_utama
        return $this->select('area')
            ->where('nama_bagian', $nama_bagian)
            ->where('area_utama', $area_utama)
            ->findAll();
    }

    public function getAreaGroupByAreaUtama()
    {
        // get area where nama_bagian = $nama_bagian and area_utama = $area_utama group by area
        return $this->select('area_utama')
            ->groupBy('area_utama')
            ->findAll();
    }
    public function getAreaOnly()
    {
        return $this->select('GROUP_CONCAT(DISTINCT area_utama) AS area_utama')
            ->groupBy('area_utama')
            ->findAll();
    }

    public function getAreaUtamaByBagian($nama_bagian)
    {
        return $this->select('area_utama')
            ->where('nama_bagian', $nama_bagian)
            ->groupBy('area_utama')
            ->findAll();
    }

    public function getAreaByBagianAndUtama($nama_bagian, $area_utama)
    {
        return $this->select('area')
            ->where('nama_bagian', $nama_bagian)
            ->where('area_utama', $area_utama)
            ->groupBy('area')
            ->findAll();
    }

    public function getAreaByNamaBagian()
    {
        return $this->select('area')
            ->where('nama_bagian', 'Montir')
            ->where('area IS NOT NULL', null, false)
            ->findAll();
    }

    public function getAreaGroupByArea()
    {
        // get area where nama_bagian = $nama_bagian and area_utama = $area_utama group by area
        return $this->select('area')
            ->where('area IS NOT NULL', null, false)
            ->groupBy('area')
            ->findAll();
    }
}
