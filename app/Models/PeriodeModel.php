<?php

namespace App\Models;

use CodeIgniter\Model;

class PeriodeModel extends Model
{
    protected $table            = 'periode';
    protected $primaryKey       = 'id_periode';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = ['id_periode', 'nama_periode', 'id_batch', 'start_date', 'end_date', 'jml_libur'];

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

    public function getPeriode()
    {
        return $this->select('periode.id_periode, periode.nama_periode, batch.id_batch, batch.nama_batch, periode.start_date, periode.end_date, periode.jml_libur')
            ->join('batch', 'batch.id_batch = periode.id_batch')
            ->findAll();
    }

    public function checkPeriode($id_periode)
    {
        return $this->select('*, batch.id_batch, batch.nama_batch')
            ->join('batch', 'batch.id_batch = periode.id_batch')
            ->where('id_periode', $id_periode)
            ->first();
    }
    public function getPeriodeByNamaBatchAndNamaPeriode($nama_batch, $nama_periode)
    {
        $result = $this->select('periode.id_periode, periode.nama_periode, batch.id_batch, batch.nama_batch, periode.start_date, periode.end_date, periode.jml_libur')
        ->join('batch', 'batch.id_batch = periode.id_batch')
        ->where('batch.nama_batch', $nama_batch)
            ->where('periode.nama_periode', $nama_periode)
            ->first();

        // Jika hasil ditemukan, tambahkan format nama bulan
        if ($result) {
            $formatter = new \IntlDateFormatter(
                'id_ID', // Locale untuk Bahasa Indonesia
                \IntlDateFormatter::LONG,
                \IntlDateFormatter::NONE,
                null,
                \IntlDateFormatter::GREGORIAN,
                'MMMM' // Format untuk nama bulan penuh
            );

            $result['nama_bulan'] = $formatter->format(new \DateTime($result['end_date']));
        }

        return $result;
    }


}
