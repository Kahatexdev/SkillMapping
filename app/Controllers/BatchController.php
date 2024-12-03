<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;
use App\Models\BatchModel;

class BatchController extends BaseController
{
    protected $batchModel;

    public function __construct()
    {

        $this->batchModel = new BatchModel();
    }

    public function index()
    {
        //
    }

    public function create()
    {
        $data = [
            'role' => session()->get('role'),
            'title' => 'Batch',
            'active1' => '',
            'active2' => '',
            'active3' => '',
            'active4' => '',
            'active5' => 'active'
        ];

        return view('/Batch/create', $data);
    }

    public function store()
    {
        if ($this->batchModel->save([
            'nama_batch' => $this->request->getVar('nama_batch')
        ])) {
            session()->setFlashdata('success', 'Data berhasil ditambahkan');
            return redirect()->to(base_url('Monitoring/dataBatch'));
        } else {
            session()->setFlashdata('error', 'Data gagal ditambahkan');
            return redirect()->to(base_url('Monitoring/dataBatch'));
        }
    }

    public function edit($id)
    {
        $data = [
            'role' => session()->get('role'),
            'title' => 'Batch',
            'active1' => '',
            'active2' => '',
            'active3' => '',
            'active4' => '',
            'active5' => 'active',
            'batch' => $this->batchModel->find($id)
        ];

        return view('/Batch/edit', $data);
    }
    
    public function update($id)
    {
        $data = [
            'nama_batch' => $this->request->getPost('nama_batch')
        ];

        $this->batchModel->update($id, $data);
        session()->setFlashdata('success', 'Data Batch Berhasil diubah');
        return redirect()->to(base_url('Monitoring/dataBatch'));
    }

    public function delete($id)
    {
        if ($this->batchModel->delete($id)) {
            session()->setFlashdata('success', 'Data berhasil dihapus');
            return redirect()->to(base_url('Monitoring/dataBatch'));
        } else {
            session()->setFlashdata('error', 'Data gagal dihapus');
            return redirect()->to(base_url('Monitoring/dataBatch'));
        }
    }
}
