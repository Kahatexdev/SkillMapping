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
use App\Models\BatchModel;
use App\Models\PenilaianModel;
use PhpOffice\PhpSpreadsheet\IOFactory;

class MonitoringController extends BaseController
{
    protected $karyawanmodel;
    protected $bagianmodel;
    protected $usermodel;
    protected $jobrole;
    protected $absenmodel;
    protected $bsmcmodel;
    protected $batchmodel;
    protected $penilaianmodel;

    public function __construct()
    {

        $this->karyawanmodel = new KaryawanModel();
        $this->bagianmodel = new BagianModel();
        $this->usermodel = new UserModel();
        $this->jobrole = new JobroleModel();
        $this->absenmodel = new AbsenModel();
        $this->bsmcmodel = new BsmcModel();
        $this->batchmodel = new BatchModel();
        $this->penilaianmodel = new PenilaianModel();
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
        return view(session()->get('role') . '/index', $data);
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
        $model = new JobRoleModel();
        $rawData = $model->getAllData();

        // Mengubah data JSON ke array
        $dataArray = [];
        foreach ($rawData as $data) {
            $dataArray[$data['id_jobrole']][] = [
                'keterangan' => json_decode($data['keterangan']),
                'jobdesc' => json_decode($data['jobdesc']),
            ];
        }

        // Mengelompokkan data berdasarkan 'keterangan' (JOB dan 6S) dalam setiap id_jobrole
        $groupedData = [];

        foreach ($dataArray as $id_jobrole => $jobData) {
            // Mengelompokkan berdasarkan 'keterangan' di dalam setiap id_jobrole
            $groupedData[$id_jobrole] = [
                'KNITTER' => [],
                'C.O' => [],
                'Ringan' => [],
                'Standar' => [],
                'Sulit' => [],
                'JOB' => [],
                'ROSSO' => [],
                'SETTING' => [],
                'Potong Manual' => [],
                'Overdeck' => [],
                'Obras' => [],
                'Single Needle' => [],
                'Mc Lipat' => [],
                'Mc Kancing' => [],
                'Mc Press' => [],
                '6S' => []
            ];

            foreach ($jobData as $data) {
                // Mengiterasi array 'jobdesc' dan 'keterangan' berdasarkan indeks yang sama
                foreach ($data['jobdesc'] as $index => $jobdesc) {
                    $keterangan = $data['keterangan'][$index];

                    // Menambahkan jobdesc ke kategori yang sesuai berdasarkan 'keterangan'
                    if ($keterangan === 'JOB') {
                        $groupedData[$id_jobrole]['JOB'][] = $jobdesc;
                    } elseif ($keterangan === '6S') {
                        $groupedData[$id_jobrole]['6S'][] = $jobdesc;
                    } elseif ($keterangan === 'KNITTER') {
                        $groupedData[$id_jobrole]['KNITTER'][] = $jobdesc;
                    } elseif ($keterangan === 'C.O') {
                        $groupedData[$id_jobrole]['C.O'][] = $jobdesc;
                    } elseif ($keterangan === 'Ringan') {
                        $groupedData[$id_jobrole]['Ringan'][] = $jobdesc;
                    } elseif ($keterangan === 'Standar') {
                        $groupedData[$id_jobrole]['Standar'][] = $jobdesc;
                    } elseif ($keterangan === 'Sulit') {
                        $groupedData[$id_jobrole]['Sulit'][] = $jobdesc;
                    } elseif ($keterangan === 'ROSSO') {
                        $groupedData[$id_jobrole]['ROSSO'][] = $jobdesc;
                    } elseif ($keterangan === 'SETTING') {
                        $groupedData[$id_jobrole]['SETTING'][] = $jobdesc;
                    } elseif ($keterangan === 'Potong Manual') {
                        $groupedData[$id_jobrole]['Potong Manual'][] = $jobdesc;
                    } elseif ($keterangan === 'Overdeck') {
                        $groupedData[$id_jobrole]['Overdeck'][] = $jobdesc;
                    } elseif ($keterangan === 'Obras') {
                        $groupedData[$id_jobrole]['Obras'][] = $jobdesc;
                    } elseif ($keterangan === 'Single Needle') {
                        $groupedData[$id_jobrole]['Single Needle'][] = $jobdesc;
                    } elseif ($keterangan === 'Mc Lipat') {
                        $groupedData[$id_jobrole]['Mc Lipat'][] = $jobdesc;
                    } elseif ($keterangan === 'Mc Kancing') {
                        $groupedData[$id_jobrole]['Mc Kancing'][] = $jobdesc;
                    } elseif ($keterangan === 'Mc Press') {
                        $groupedData[$id_jobrole]['Mc Press'][] = $jobdesc;
                    }
                }
            }
        }

        // Pastikan kunci JOB dan 6S ada meskipun kosong
        foreach ($groupedData as $id_jobrole => $data) {
            if (!isset($groupedData[$id_jobrole]['JOB'])) {
                $groupedData[$id_jobrole]['JOB'] = [];
            }
            if (!isset($groupedData[$id_jobrole]['6S'])) {
                $groupedData[$id_jobrole]['6S'] = [];
            }
            if (!isset($groupedData[$id_jobrole]['KNITTER'])) {
                $groupedData[$id_jobrole]['KNITTER'] = [];
            }
            if (!isset($groupedData[$id_jobrole]['C.O'])) {
                $groupedData[$id_jobrole]['C.O'] = [];
            }
            if (!isset($groupedData[$id_jobrole]['Ringan'])) {
                $groupedData[$id_jobrole]['Ringan'] = [];
            }
            if (!isset($groupedData[$id_jobrole]['Standar'])) {
                $groupedData[$id_jobrole]['Standar'] = [];
            }
            if (!isset($groupedData[$id_jobrole]['Sulit'])) {
                $groupedData[$id_jobrole]['Sulit'] = [];
            }
            if (!isset($groupedData[$id_jobrole]['ROSSO'])) {
                $groupedData[$id_jobrole]['ROSSO'] = [];
            }
            if (!isset($groupedData[$id_jobrole]['SETTING'])) {
                $groupedData[$id_jobrole]['SETTING'] = [];
            }
            if (!isset($groupedData[$id_jobrole]['Potong Manual'])) {
                $groupedData[$id_jobrole]['Potong Manual'] = [];
            }
            if (!isset($groupedData[$id_jobrole]['Overdeck'])) {
                $groupedData[$id_jobrole]['Overdeck'] = [];
            }
            if (!isset($groupedData[$id_jobrole]['Obras'])) {
                $groupedData[$id_jobrole]['Obras'] = [];
            }
            if (!isset($groupedData[$id_jobrole]['Single Needle'])) {
                $groupedData[$id_jobrole]['Single Needle'] = [];
            }
            if (!isset($groupedData[$id_jobrole]['Mc Lipat'])) {
                $groupedData[$id_jobrole]['Mc Lipat'] = [];
            }
            if (!isset($groupedData[$id_jobrole]['Mc Kancing'])) {
                $groupedData[$id_jobrole]['Mc Kancing'] = [];
            }
            if (!isset($groupedData[$id_jobrole]['Mc Press'])) {
                $groupedData[$id_jobrole]['Mc Press'] = [];
            }

        }

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
            'jobrole' => $jobrole,
            'groupedData' => $groupedData
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
    public function penilaian()
    {
        $batch = $this->batchmodel->findAll();
        $namabagian = $this->bagianmodel->getBagian();
        $penilaian = $this->penilaianmodel->getPenilaian();

        // dd($area);
        $data = [
            'role' => session()->get('role'),
            'title' => 'Penilaian Mandor',
            'active1' => '',
            'active2' => '',
            'active3' => '',
            'active4' => '',
            'active5' => '',
            'active6' => '',
            'active7' => '',
            'active8' => 'active',
            'batch' => $batch,
            'namabagian' => $namabagian,
            'penilaian' => $penilaian
            // 'area' => $area

        ];
        // dd($penilaian);
        return view(session()->get('role') . '/penilaian', $data);
    }
}
