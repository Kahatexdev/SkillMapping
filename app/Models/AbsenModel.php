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
        return $this->select('absen.*, karyawan.nama_karyawan, user.username, 
        (absen.sakit * 1) + (absen.izin * 2) + (absen.mangkir * 3) as jml_hari_tidak_masuk_kerja, 
        ((31 - ((absen.sakit * 1) + (absen.izin * 2) + (absen.mangkir * 3))) / 31) * 100 as persentase_kehadiran,
        CASE 
            WHEN (((31 - ((absen.sakit * 1) + (absen.izin * 2) + (absen.mangkir * 3))) / 31) * 100) < 94 THEN -1
            ELSE 0
        END as accumulasi_absensi')
        ->join('karyawan', 'karyawan.id_karyawan = absen.id_karyawan')
        ->join('user', 'user.id_user = absen.id_user')
        ->findAll();
    }

    public function getReportPenilaian()
    {
        return $this->select('
        absen.*, 
        karyawan.nama_karyawan, 
        karyawan.kode_kartu, 
        penilaian.index_nilai,
        CAST(COALESCE(absen.mangkir, 0) * 3 AS INT) AS jml_mangkir,
        CAST(COALESCE(absen.izin, 0) * 2 AS INT) AS jml_izin,
        CAST(COALESCE(absen.sakit, 0) * 1 AS INT) AS jml_sakit,
        CAST((COALESCE(absen.mangkir, 0) * 3) + (COALESCE(absen.izin, 0) * 2) + (COALESCE(absen.sakit, 0) * 1) AS INT) AS jml_hari_tidak_masuk_kerja,
        -- Menghitung persentase kehadiran
        CAST(((31 - (COALESCE(absen.sakit, 0) + COALESCE(absen.izin, 0) * 2 + COALESCE(absen.mangkir, 0) * 3)) / 31) * 100 AS INT) AS persentase_kehadiran,

        -- Menghitung akumulasi absensi (nilai negatif jika kehadiran < 94)
        CASE 
            WHEN CAST(((31 - ((COALESCE(absen.sakit, 0) * 1) + (COALESCE(absen.izin, 0) * 2) + (COALESCE(absen.mangkir, 0) * 3))) / 31) * 100 AS INT) < 94 THEN -1
            ELSE 0
        END AS accumulasi_absensi,

        -- Menjumlahkan akumulasi absensi dan nilai index untuk menentukan grade
        CASE 
            WHEN (
                -- Hitung nilai total dengan akumulasi absensi dan index_nilai
                CASE 
                    WHEN CAST(((31 - ((COALESCE(absen.sakit, 0) * 1) + (COALESCE(absen.izin, 0) * 2) + (COALESCE(absen.mangkir, 0) * 3))) / 31) * 100 AS INT) < 94 THEN -1
                    ELSE 0
                END
                +
                CASE 
                    WHEN penilaian.index_nilai = "A" THEN 4
                    WHEN penilaian.index_nilai = "B" THEN 3
                    WHEN penilaian.index_nilai = "C" THEN 2
                    WHEN penilaian.index_nilai = "D" THEN 1
                    ELSE 0
                END
            ) >= 4 THEN "A"
            WHEN (
                CASE 
                    WHEN CAST(((31 - ((COALESCE(absen.sakit, 0) * 1) + (COALESCE(absen.izin, 0) * 2) + (COALESCE(absen.mangkir, 0) * 3))) / 31) * 100 AS INT) < 94 THEN -1
                    ELSE 0
                END
                +
                CASE 
                    WHEN penilaian.index_nilai = "A" THEN 4
                    WHEN penilaian.index_nilai = "B" THEN 3
                    WHEN penilaian.index_nilai = "C" THEN 2
                    WHEN penilaian.index_nilai = "D" THEN 1
                    ELSE 0
                END
            ) = 3 THEN "B"
            WHEN (
                CASE 
                    WHEN CAST(((31 - ((COALESCE(absen.sakit, 0) * 1) + (COALESCE(absen.izin, 0) * 2) + (COALESCE(absen.mangkir, 0) * 3))) / 31) * 100 AS INT) < 94 THEN -1
                    ELSE 0
                END
                +
                CASE 
                    WHEN penilaian.index_nilai = "A" THEN 4
                    WHEN penilaian.index_nilai = "B" THEN 3
                    WHEN penilaian.index_nilai = "C" THEN 2
                    WHEN penilaian.index_nilai = "D" THEN 1
                    ELSE 0
                END
            ) = 2 THEN "C"
            WHEN (
                CASE 
                    WHEN CAST(((31 - ((COALESCE(absen.sakit, 0) * 1) + (COALESCE(absen.izin, 0) * 2) + (COALESCE(absen.mangkir, 0) * 3))) / 31) * 100 AS INT) < 94 THEN -1
                    ELSE 0
                END
                +
                CASE 
                    WHEN penilaian.index_nilai = "A" THEN 4
                    WHEN penilaian.index_nilai = "B" THEN 3
                    WHEN penilaian.index_nilai = "C" THEN 2
                    WHEN penilaian.index_nilai = "D" THEN 1
                    ELSE 0
                END
            ) = 1 THEN "D"
            ELSE "E"
        END AS grade_penilaian
    ')
        ->join('karyawan', 'karyawan.id_karyawan = absen.id_karyawan')
        ->join('penilaian', 'penilaian.karyawan_id = karyawan.id_karyawan')
        ->findAll();
    }



}
