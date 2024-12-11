<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;

class BagianController extends BaseController
{

    public function __construct()
    {
        $this->bagianModel = new \App\Models\BagianModel();
    }
    public function index()
    {

    }

    public function create()
    {
        $data = [
            'role' => session()->get('role'),
            'title' => 'Bagian',
            'active1' => '',
            'active2' => '',
            'active3' => 'active',
            'active4' => '',
            'active5' => '',
            'active6' => ''
        ];
        
        return view('bagian/create', $data);
    }

    public function store()
    {
        $data = [
            'nama_bagian' => $this->request->getPost('nama_bagian'),
            'area_utama' => $this->request->getPost('area_utama'),
            'area' => $this->request->getPost('area'),
            'keterangan' => $this->request->getPost('keterangan')
        ];

        $this->bagianModel->insert($data);
        session()->setFlashdata('success', 'Data Bagian Berhasil Ditambahkan');
        return redirect()->to(base_url('Monitoring/dataBagian'));
    }

    public function edit($id)
    {
        $data = [
            'role' => session()->get('role'),
            'title' => 'Bagian',
            'active1' => '',
            'active2' => '',
            'active3' => 'active',
            'active4' => '',
            'active5' => '',
            'active6' => '',
            'bagian' => $this->bagianModel->find($id)
        ];
        return view('bagian/edit', $data);
    }

    public function update($id)
    {
        $data = [
            'nama_bagian' => $this->request->getPost('nama_bagian'),
            'area_utama' => $this->request->getPost('area_utama'),
            'area' => $this->request->getPost('area'),
            'keterangan' => $this->request->getPost('keterangan')
        ];

        $this->bagianModel->update($id, $data);
        session()->setFlashdata('success', 'Data Bagian Berhasil Diubah');
        return redirect()->to(base_url('Monitoring/dataBagian'));
    }

    public function delete($id){
        $bagianModel = new \App\Models\BagianModel();
        $bagianModel->delete($id);

        return redirect()->to(base_url('Monitoring/dataBagian'))->with('success', 'Data bagian berhasil dihapus.');
    }
}