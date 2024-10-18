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
        $usermodels->insert($data);

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

        $usermodels->update($id, $data);

        return redirect()->to('/monitoring/dataUser');
    }

    public function delete($id)
    {
        $usermodels = new UserModel();

        $usermodels->delete($id);

        return redirect()->to('/monitoring/dataUser');
    }
}