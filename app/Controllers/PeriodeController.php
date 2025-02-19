<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;
use App\Models\PeriodeModel;
use App\Models\BatchModel;

class PeriodeController extends BaseController
{
    protected $periodeModel;
    protected $batchModel;

    public function __construct()
    {

        $this->periodeModel = new PeriodeModel();
        $this->batchModel = new BatchModel();
    }
    public function index()
    {
        //
    }

    public function create()
    {
        $batch = $this->batchModel->findAll();
        $data = [
            'role' => session()->get('role'),
            'title' => 'Periode',
            'active1' => '',
            'active2' => '',
            'active3' => '',
            'active4' => '',
            'active5' => 'active',
            'batch' => $batch
        ];

        return view('/Periode/create', $data);
    }

    public function store()
    {
        $namaPeriode = $this->request->getPost('nama_periode');
        $idBatch = $this->request->getPost('nama_batch');
        $startDate = $this->request->getPost('start_date');
        $endDate = $this->request->getPost('end_date');
        $jml_libur = $this->request->getPost('jml_libur');
        // dd($namaPeriode, $idBatch, $startDate, $endDate);
        $errors = [];

        $tempNamaPeriode = $this->periodeModel->where('nama_periode', $namaPeriode)->where('id_batch', $idBatch)->first();
        if ($tempNamaPeriode) {
            $errors['nama_periode'] = 'Nama periode sudah ada';
        }
        if ($startDate > $endDate) {
            $errors['start_date'] = 'Tanggal mulai tidak boleh lebih besar dari tanggal selesai';
        }
        $tempDate = $this->periodeModel->where('start_date <=', $startDate)->where('end_date >=', $startDate)->where('id_batch', $idBatch)->first();
        if ($tempDate) {
            $errors['start_date'] = 'Tanggal mulai tidak boleh beririsan dengan periode lain';
        }
        $tempDate = $this->periodeModel->where('start_date <=', $endDate)->where('end_date >=', $endDate)->where('id_batch', $idBatch)->first();
        if ($tempDate) {
            $errors['end_date'] = 'Tanggal selesai tidak boleh beririsan dengan periode lain';
        }

        if ($errors) {
            session()->setFlashdata('errors', $errors);
            return redirect()->to(base_url('Monitoring/dataPeriode'));
        } else {
            $this->periodeModel->save([
                'nama_periode' => $namaPeriode,
                'id_batch' => $idBatch,
                'start_date' => $startDate,
                'end_date' => $endDate,
                'jml_libur' => $jml_libur
            ]);
            session()->setFlashdata('success', 'Data berhasil ditambahkan');
            return redirect()->to(base_url('Monitoring/dataPeriode'));
        }
    }

    public function edit($id)
    {
        $periode = $this->periodeModel->find($id);
        $batch = $this->batchModel->findAll();
        $data = [
            'role' => session()->get('role'),
            'title' => 'Periode',
            'active1' => '',
            'active2' => '',
            'active3' => '',
            'active4' => '',
            'active5' => 'active',
            'periode' => $periode,
            'batch' => $batch
        ];

        return view('/Periode/edit', $data);
    }

    public function update($id)
    {
        $namaPeriode = $this->request->getPost('nama_periode');
        $idBatch = $this->request->getPost('nama_batch');
        $startDate = $this->request->getPost('start_date');
        $endDate = $this->request->getPost('end_date');
        $jml_libur = $this->request->getPost('jml_libur');

        $errors = [];

        $tempNamaPeriode = $this->periodeModel->where('nama_periode', $namaPeriode)->where('id_batch', $idBatch)->where('id_periode !=', $id)->first();
        if ($tempNamaPeriode) {
            $errors['nama_periode'] = 'Nama periode sudah ada';
        }
        if ($startDate > $endDate) {
            $errors['start_date'] = 'Tanggal mulai tidak boleh lebih besar dari tanggal selesai';
        }
        $tempDate = $this->periodeModel->where('start_date <=', $startDate)->where('end_date >=', $startDate)->where('id_batch', $idBatch)->where('id_periode !=', $id)->first();
        if ($tempDate) {
            $errors['start_date'] = 'Tanggal mulai tidak boleh beririsan dengan periode lain';
        }
        $tempDate = $this->periodeModel->where('start_date <=', $endDate)->where('end_date >=', $endDate)->where('id_batch', $idBatch)->where('id_periode !=', $id)->first();
        if ($tempDate) {
            $errors['end_date'] = 'Tanggal selesai tidak boleh beririsan dengan periode lain';
        }

        if ($errors) {
            session()->setFlashdata('errors', $errors);
            return redirect()->to(base_url('Monitoring/dataPeriode/'));
        } else {
            $this->periodeModel->update($id, [
                'nama_periode' => $namaPeriode,
                'id_batch' => $idBatch,
                'start_date' => $startDate,
                'end_date' => $endDate,
                'jml_libur' => $jml_libur
            ]);
            session()->setFlashdata('success', 'Data berhasil diubah');
            return redirect()->to(base_url('Monitoring/dataPeriode'));
        }
    }

    public function delete($id)
    {
        $this->periodeModel->delete($id);
        session()->setFlashdata('success', 'Data periode berhasil dihapus');
        return redirect()->to(base_url('Monitoring/dataPeriode'));
    }
}
