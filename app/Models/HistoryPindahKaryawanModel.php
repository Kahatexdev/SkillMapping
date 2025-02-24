<?php

namespace App\Models;

use CodeIgniter\Model;

class HistoryPindahKaryawanModel extends Model
{
    protected $table            = 'history_pindah_karyawan';
    protected $primaryKey       = 'id_pindah';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = ['id_pindah', 'id_karyawan', 'id_bagian_asal', 'id_bagian_baru', 'tgl_pindah', 'keterangan', 'created_at', 'updated_by'];

    protected bool $allowEmptyInserts = false;
    protected bool $updateOnlyChanged = true;

    protected array $casts = [];
    protected array $castHandlers = [];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = '';
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

    public function getHistoryPindahKaryawan()
    {
        return $this->db->table('history_pindah_karyawan')
            ->select('history_pindah_karyawan.*, karyawan.kode_kartu, karyawan.nama_karyawan,  bagian_asal.nama_bagian AS bagian_asal, 
                bagian_asal.area_utama AS area_utama_asal, 
                bagian_asal.area AS area_asal, 
                bagian_baru.nama_bagian AS bagian_baru, 
                bagian_baru.area_utama AS area_utama_baru, 
                bagian_baru.area AS area_baru, user.username as updated_by')
            ->join('karyawan', 'karyawan.id_karyawan = history_pindah_karyawan.id_karyawan')
            ->join('bagian AS bagian_asal', 'bagian_asal.id_bagian = history_pindah_karyawan.id_bagian_asal')
            ->join('bagian AS bagian_baru', 'bagian_baru.id_bagian = history_pindah_karyawan.id_bagian_baru')
            ->join('user', 'user.id_user = history_pindah_karyawan.updated_by')
            ->get()
            ->getResultArray();
    }

    // Fungsi untuk mendapatkan data fluktuasi grade
    public function getGradeFluctuation()
    {
        // Contoh data fluktuasi grade (bisa disesuaikan dengan query dari tabel terkait)
        return [
            'labels' => ['Jan', 'Feb', 'Mar', 'Apr', 'May'], // Label bulan
            'grades' => [3, 4, 3, 5, 4], // Nilai grade tiap bulan
        ];
    }

    // Fungsi untuk mendapatkan data karyawan yang dipindah area
    public function getKaryawanDipindah()
    {
        return $this->where('id_bagian_asal !=', 'id_bagian_baru')
            ->orderBy('tgl_pindah', 'DESC')
            ->findAll();
    }

    public function getPindahGroupedByDate()
    {
        // Ambil data pindahan
        $builder = $this->db->table($this->table);
        $builder->select("DATE(tgl_pindah) AS tgl, COUNT(*) AS jumlah");
        $builder->groupBy("DATE(tgl_pindah)");
        $builder->orderBy("tgl", "ASC");
        return $builder->get()->getResultArray();
    }
}
