<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;
use App\Models\KaryawanModel;
use App\Models\BagianModel;
use App\Models\UserModel;
use App\Models\JobroleModel;
use App\Models\AbsenModel;
use App\Models\BsmcModel;
use App\Models\SummaryRossoModel;
use App\Models\BatchModel;
use App\Models\PeriodeModel;
use App\Models\PenilaianModel;

class TrainingSchoolController extends BaseController
{
    protected $karyawanmodel;
    protected $bagianmodel;
    protected $usermodel;
    protected $jobrole;
    protected $absenmodel;
    protected $bsmcmodel;
    protected $summaryRosso;
    protected $batchmodel;
    protected $periodeModel;
    protected $penilaianmodel;

    public function __construct()
    {

        $this->karyawanmodel = new KaryawanModel();
        $this->bagianmodel = new BagianModel();
        $this->usermodel = new UserModel();
        $this->jobrole = new JobroleModel();
        $this->absenmodel = new AbsenModel();
        $this->bsmcmodel = new BsmcModel();
        $this->summaryRosso = new SummaryRossoModel();
        $this->batchmodel = new BatchModel();
        $this->periodeModel = new PeriodeModel();
        $this->penilaianmodel = new PenilaianModel();
    }

    public function index()
    {
        //
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
}
