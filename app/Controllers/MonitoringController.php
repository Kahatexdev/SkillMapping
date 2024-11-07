<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Database\Migrations\BsMc;
use CodeIgniter\HTTP\ResponseInterface;
use App\Models\KaryawanModel;
use App\Models\BagianModel;
use App\Models\UserModel;
use App\Models\JobroleModel;
use App\Models\AbsenModel;
use App\Models\BsmcModel;
use PhpOffice\PhpSpreadsheet\IOFactory;

class MonitoringController extends BaseController
{
    protected $karyawanmodel;
    protected $bagianmodel;
    protected $usermodel;
    protected $jobrole;
    protected $absenmodel;
    protected $bsmcmodel;

    public function __construct()
    {

        $this->karyawanmodel = new KaryawanModel();
        $this->bagianmodel = new BagianModel();
        $this->usermodel = new UserModel();
        $this->jobrole = new JobroleModel();
        $this->absenmodel = new AbsenModel();
        $this->bsmcmodel = new BsmcModel();
    }
    public function index()
    {
        $data = [
            'role' => session()->get('role'),
            'title' => 'Dashboard',
            'active1' => 'active',
            'active2' => '',
            'active3' => '',
            'active4' => '',
            'active5' => '',
            'active6' => ''

        ];
        return view('Layout/index', $data);
    }
    public function karyawan()
    {
        $karyawan = $this->karyawanmodel->getBagian();
        // dd($karyawan);
        $data = [
            'role' => session()->get('role'),
            'title' => 'Karyawan',
            'active1' => '',
            'active2' => '',
            'active3' => '',
            'active4' => 'active',
            'active5' => '',
            'active6' => '',
            'karyawan' => $karyawan
        ];
        return view(session()->get('role') . '/karyawan', $data);
    }
    public function user()
    {
        $usermodels = new UserModel();

        $users = $usermodels->findAll();

        $data = [
            'role' => session()->get('role'),
            'title' => 'User',
            'active1' => '',
            'active2' => 'active',
            'active3' => '',
            'active4' => '',
            'active5' => '',
            'active6' => '',
            'users' => $users
        ];


        // dd($users);
        return view(session()->get('role') . '/user', $data);
    }
    public function bagian()
    {
        $bagian = $this->bagianmodel->findAll();
        $data = [
            'role' => session()->get('role'),
            'title' => 'Bagian',
            'active1' => '',
            'active2' => '',
            'active3' => 'active',
            'active4' => '',
            'active5' => '',
            'active6' => '',
            'bagian' => $bagian
        ];
        return view(session()->get('role') . '/bagian', $data);
    }
    public function absen()
    {
        $absen = $this->absenmodel->getdata();

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
            'absen' => $absen
        ];
        // dd($absen);
        return view(session()->get('role') . '/absen', $data, $users);
    }
    public function job()
    {
        $jobrole = $this->jobrole->getJobRolesWithBagian();
        $data = [
            'role' => session()->get('role'),
            'title' => 'Job Role',
            'active1' => '',
            'active2' => '',
            'active3' => '',
            'active4' => '',
            'active5' => '',
            'active6' => 'active',
            'jobrole' => $jobrole
        ];
        return view(session()->get('role') . '/jobrole', $data);
    }
    public function bsmc()
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
