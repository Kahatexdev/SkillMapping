<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;
use App\Models\UserModel;

class AuthController extends BaseController
{
    public function index(): string
    {
        return view('Auth/index');
    }

    public function login()
    {
        //Password perlu di hash?
        $username = $this->request->getPost('username');
        $password = $this->request->getPost('password');
        $UserModel = new UserModel;
        $userData = $UserModel->login($username, $password);
        // dd($username);
        if (!$userData) {
            return redirect()->to(base_url('/login'))->withInput()->with('error', 'Invalid username or password');
        }
        session()->set('id_user', $userData['id_user']);
        session()->set('username', $userData['username']);
        session()->set('role', $userData['role']);
        session()->set('area', $userData['area']);
        // dd($userData['role']);
        switch ($userData['role']) {
            case 'Monitoring':
                return redirect()->to(base_url('/Monitoring'));
            case 'Mandor':
                return redirect()->to(base_url('/Mandor'));
            case 'TrainingSchool':
                return redirect()->to(base_url('/TrainingSchool'));
            default:
                return redirect()->to(base_url('/login'))->withInput()->with('error', 'Invalid username or password');
                break;
        }
    }

    public function logout()
    {
        session()->destroy();
        return redirect()->to(base_url('/login'));
    }
}