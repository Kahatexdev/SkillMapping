<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;
use App\Models\JobroleModel;
use App\Models\BagianModel;

class JobroleController extends BaseController
{
    public function index()
    {
        //
    }

    public function create()
    {
        $bagianmodel = new BagianModel();

        $bagians = $bagianmodel->findAll();

        return view('jobrole/create', ['bagians' => $bagians]);
    }

    public function store()
    {
        $jobrolemodel = new JobroleModel();

        $data = [
            'id_bagian' => $this->request->getPost('id_bagian'),
            'status' => $this->request->getPost('status'),
            'jobdesc' => $this->request->getPost('jobdesc')
        ];

        if($jobrolemodel->insert($data)){
            session()->setFlashdata('success', 'Data berhasil ditambahkan');    
        } else {
            session()->setFlashdata('error', 'Data gagal ditambahkan');
        }

        return redirect()->to('/monitoring/dataJob');
    }

    public function edit($id)
    {
        $jobrolemodel = new JobroleModel();
        $bagianmodel = new BagianModel();

        $jobrole = $jobrolemodel->find($id);
        $bagians = $bagianmodel->findAll();

        return view('jobrole/edit', ['jobrole' => $jobrole, 'bagians' => $bagians]);
    }

    public function update($id)
    {
        $jobrolemodel = new JobroleModel();

        $data = [
            'id_bagian' => $this->request->getPost('id_bagian'),
            'status' => $this->request->getPost('status'),
            'jobdesc' => $this->request->getPost('jobdesc')
        ];

        if($jobrolemodel->update($id, $data)){
            session()->setFlashdata('success', 'Data berhasil diubah');    
        } else {
            session()->setFlashdata('error', 'Data gagal diubah');
        }

        return redirect()->to('/monitoring/dataJob');
    }

    public function delete($id)
    {
        $jobrolemodel = new JobroleModel();

        if($jobrolemodel->delete($id)){
            session()->setFlashdata('success', 'Data berhasil dihapus');    
        } else {
            session()->setFlashdata('error', 'Data gagal dihapus');
        }

        return redirect()->to('/monitoring/dataJob');
    }
}