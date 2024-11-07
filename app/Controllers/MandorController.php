<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;
use App\Models\KaryawanModel;
use App\Models\AbsenModel;
use App\Models\UserModel;
use App\Models\PenilaianModel;

class MandorController extends BaseController
{
    protected $karyawanmodel;
    protected $absenmodel;
    protected $usermodel;
    protected $penilaianmodel;

    public function __construct()
    {
        $this->karyawanmodel = new KaryawanModel();
        $this->absenmodel = new AbsenModel();
        $this->usermodel = new UserModel();
        $this->penilaianmodel = new PenilaianModel();
    }

    public function index()
    {
    }

    public function karyawan()
    {
        $karyawan = $this->karyawanmodel->getBagian();
        // dd($karyawan);
        $data = [
            'role' => session()->get('role'),
            'title' => 'Karyawan',
            'active1' => 'active',
            'active2' => '',
            'active3' => '',
            'karyawan' => $karyawan
        ];
        return view(session()->get('role') . '/karyawan', $data);
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
            'active2' => 'active',
            'active3' => '',
            'absen' => $absen
        ];
        // dd($absen);
        return view(session()->get('role') . '/absen', $data, $users);
    }

    public function penilaian()
    {
        // $penilaian = $this->penilaianmodel->;
        $data = [
            'role' => session()->get('role'),
            'title' => 'Penilaian',
            'active1' => '',
            'active2' => '',
            'active3' => 'active',
            'active4' => '',
            'active5' => '',
            'active6' => ''
            // 'penilaian' => $penilaian
        ];
        // dd($absen);
        return view(session()->get('role') . '/penilaian', $data);
    }
}
