<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;
use App\Models\UserModel;

class UserController extends BaseController
{
    public function index()
    {
        // $usermodels = new UserModel();

        // $users = $usermodels->findAll();

        // // dd($users);
        // return view('pengguna/index', ['users' => $users]);
    }

    public function create()
    {
        return view('pengguna/create');
    }

    public function store()
    {
        $usermodels = new UserModel();

        $data = [
            'username' => $this->request->getPost('username'),
            'password' => $this->request->getPost('password'),
            'role' => $this->request->getPost('role')
        ];

        // dd($data);
        if($usermodels->insert($data)){
            // set session flashdata
            session()->setFlashdata('success', 'Data berhasil ditambahkan');    
        } else {
            session()->setFlashdata('error', 'Data gagal ditambahkan');
        }

        return redirect()->to('/monitoring/dataUser');
    }

    public function edit($id)
    {
        $usermodels = new UserModel();

        $user = $usermodels->find($id);

        return view('pengguna/edit', ['user' => $user]);
    }

    public function update($id)
    {
        $usermodels = new UserModel();

        $data = [
            'username' => $this->request->getPost('username'),
            'password' => $this->request->getPost('password'),
            'role' => $this->request->getPost('role')
        ];

        if($usermodels->update($id, $data)){
            // set session flashdata
            session()->setFlashdata('success', 'Data berhasil diubah');    
        } else {
            session()->setFlashdata('error', 'Data gagal diubah');
        }

        return redirect()->to('/monitoring/dataUser');
    }

    public function delete($id)
    {
        $usermodels = new UserModel();

        if($usermodels->delete($id)){
            // set session flashdata
            session()->setFlashdata('success', 'Data berhasil dihapus');    
        } else {
            session()->setFlashdata('error', 'Data gagal dihapus');
        }

        return redirect()->to('/monitoring/dataUser');
    }
}