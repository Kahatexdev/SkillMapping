<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;
use App\Models\UserModel;

class UserController extends BaseController
{
    public function index()
    {
        $usermodels = new UserModel();

        $users = $usermodels->findAll();

        dd($users);
    }
}
