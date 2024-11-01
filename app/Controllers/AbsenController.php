<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\KaryawanModel;
use CodeIgniter\HTTP\ResponseInterface;
use App\Models\UserModel;
use App\Models\AbsenModel;

class AbsenController extends BaseController
{

    public function index()
    {
        
    }

    public function create()
    {
        $data = new AbsenModel();

        $datas = $data->getAbsenWithKaryawan();

        $usermodel = new UserModel();
        $users = $usermodel->findAll();

        $data = [
            'role' => session()->get('role'),
            'title' => 'Absen',
            'active1' => '',
            'active2' => '',
            'active3' => '',
            'active4' => '',
            'active5' => 'active',
            'active6' => '',
            'datas' => $datas,
            'users' => $users
        ];

        return view('absen/create', $data);
    }

    public function store(){

        $absen = new AbsenModel();

        $data = [
            'id_karyawan' => $this->request->getPost('id_karyawan'),
            'tanggal' => $this->request->getPost('tanggal'),
            'ket_absen' => $this->request->getPost('ket_absen'),
            'id_user' => $this->request->getPost('id_user')
        ];

        if($absen->insert($data)){
            session()->setFlashdata('success', 'Data berhasil ditambahkan');    
        } else {
            session()->setFlashdata('error', 'Data gagal ditambahkan');
        }

        return redirect()->to('/monitoring/dataAbsen');
    }

    public function edit($id)
    {
        $absen = new AbsenModel();
        $datajoin = $absen->getAbsenWithKaryawan();
        $usermodel = new UserModel();

        $users = $usermodel->findAll();
        $data = $absen->find($id);

        $data = [
            'role' => session()->get('role'),
            'title' => 'Absen',
            'active1' => '',
            'active2' => '',
            'active3' => '',
            'active4' => '',
            'active5' => 'active',
            'active6' => '',
            'data' => $data,
            'datajoin' => $datajoin,
            'users' => $users
        ];
        return view('absen/edit', $data);
    }

    public function update($id)
    {
        $absen = new AbsenModel();

        $data = [
            'id_karyawan' => $this->request->getPost('id_karyawan'),
            'tanggal' => $this->request->getPost('tanggal'),
            'ket_absen' => $this->request->getPost('ket_absen'),
            'id_user' => $this->request->getPost('id_user')
        ];

        if($absen->update($id, $data)){
            session()->setFlashdata('success', 'Data berhasil diubah');    
        } else {
            session()->setFlashdata('error', 'Data gagal diubah');
        }

        return redirect()->to('/monitoring/dataAbsen');
    }

    public function delete($id)
    {
        $absen = new AbsenModel();

        if($absen->delete($id)){
            session()->setFlashdata('success', 'Data berhasil dihapus');    
        } else {
            session()->setFlashdata('error', 'Data gagal dihapus');
        }

        return redirect()->to('/monitoring/dataAbsen');
    }

    public function import()
    {
        $data = [
            'role' => session()->get('role'),
            'title' => 'Absen',
            'active1' => '',
            'active2' => '',
            'active3' => '',
            'active4' => '',
            'active5' => 'active',
            'active6' => ''
        ];
        return view('absen/import', $data);
    }
}