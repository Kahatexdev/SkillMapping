<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\BsmcModel;
use CodeIgniter\HTTP\ResponseInterface;

use App\Models\KaryawanModel;

class BsMcController extends BaseController
{
    protected $bsmcmodel;

    public function __construct()
    {

        $this->bsmcmodel = new BsmcModel();
    }
    public function index()
    {
        $bsmc = $this->bsmcmodel->getKaryawan();
        // dd($bsmc);
        $data = [
            'role' => session()->get('role'),
            'title' => 'Bs Mesin',
            'active1' => 'active',
            'active2' => '',
            'active3' => '',
            'active4' => '',
            'active5' => '',
            'active6' => '',
            'active7' => 'active',
            'bsmc' => $bsmc

        ];
        return view(session()->get('role') . '/bsmc', $data);
    }
}
