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
use App\Models\SummaryRossoModel;
use App\Models\BatchModel;
use App\Models\PeriodeModel;
use App\Models\PenilaianModel;
use App\Models\HistoryPindahKaryawanModel;
use PhpOffice\PhpSpreadsheet\IOFactory;

class MonitoringController extends BaseController
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
    protected $historyPindahKaryawanModel;

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
        $this->historyPindahKaryawanModel = new HistoryPindahKaryawanModel();
    }

    public function index()
    {
        $TtlKaryawan = $this->karyawanmodel->where('status', 'Aktif')->countAll();
        $PerpindahanBulanIni = $this->historyPindahKaryawanModel->where('MONTH(tgl_pindah)', date('m'))->countAllResults();
        $dataKaryawan = $this->karyawanmodel->getActiveKaryawanByBagian();
        // dd($dataKaryawan);
        $periodeAktif = $this->periodeModel->getActivePeriode();
        // dd($dataKaryawan, $TtlKaryawan, $PerpindahanBulanIni, $cekPenilaian, $id_periode, $current_periode, $start_date, $end_date);

        // Default values jika tidak ada periode aktif
        $id_periode = null;
        $current_periode = 'Tidak Ada Periode Aktif';
        $start_date = '-';
        $end_date = '-';
        $cekPenilaian = null;

        if ($periodeAktif) {
            $id_periode = $periodeAktif['id_periode'];
            $current_periode = $periodeAktif['nama_periode'];
            $start_date = $periodeAktif['start_date'];
            $end_date = $periodeAktif['end_date'];
            $cekPenilaian = $this->penilaianmodel->getMandorEvaluationStatus($id_periode);
        }
        $RatarataGrade = 0;
        $SkillGap = 0;

        // Hitung total karyawan (jika diperlukan)
        $totalKaryawan = 0;
        foreach ($dataKaryawan as $row) {
            $totalKaryawan += $row['jumlah_karyawan'];
        }

        $RatarataGrade = $this->penilaianmodel->getRataRataGrade();

        $dataPindah = $this->historyPindahKaryawanModel->getPindahGroupedByDate();
        // dd ($makan);
        // Siapkan data untuk grafik line
        $labelsKar = [];
        $valuesKar = [];
        foreach ($dataPindah as $row) {
            $labelsKar[] = $row['tgl'];
            $valuesKar[] = (int)$row['jumlah'];
        }

        return view('Monitoring/index', [
            'role' => session()->get('role'),
            'title' => 'Dashboard',
            'active1' => 'active',
            'active2' => '',
            'active3' => '',
            'TtlKaryawan' => $TtlKaryawan,
            'PerpindahanBulanIni' => $PerpindahanBulanIni,
            'RataRataGrade' => $RatarataGrade['average_grade_letter'],
            'SkillGap' => $SkillGap,
            'karyawanByBagian' => $dataKaryawan,
            'labelsKar' => $labelsKar,
            'valuesKar' => $valuesKar,
            'cekPenilaian' => $cekPenilaian,
            'id_periode' => $id_periode,
            'current_periode' => $current_periode,
            'start_date' => $start_date,
            'end_date' => $end_date
        ]);
    }

    // public function index()
    // {
    //     $employeeModel = $this->karyawanmodel;
    //     $attendanceModel = $this->absenmodel;
    //     $jobRoleModel = $this->jobrole;
    //     $evaluationModel = $this->penilaianmodel;

    //     $data = [
    //         'role' => session()->get('role'),
    //         'title' => 'Dashboard',
    //         'active1' => 'active',
    //         'active2' => '',
    //         'active3' => '',
    //         'active4' => '',
    //         'active5' => '',
    //         'active6' => '',
    //         'active7' => '',
    //         'active8' => '',
    //         'totalEmployees' => $employeeModel->countAll(),
    //         'totalAttendance' => $attendanceModel->countAll(),
    //         'totalJobRoles' => $jobRoleModel->countAll(),
    //         'totalEvaluations' => $evaluationModel->countAll(),
    //         // Data for charts
    //         'monthlyEvaluations' => [10, 30, 50, 20, 1], // Dummy, replace with dynamic data
    //         'gradeDistribution' => [30, 50, 10, 10], // Dummy, replace with dynamic data
    //     ];
    //     // dd($employeeModel);
    //     // dd($data['totalEmployees']);
    //     return view(session()->get('role') . '/index', $data);
    // }
    public function karyawan()
    {
        $bagianModel = new \App\Models\BagianModel();
        $bagian = $bagianModel->findAll();
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
            'karyawan' => $karyawan,
            'bagian' => $bagian
        ];

        // dd ($karyawan, $bagian);    
        return view(session()->get('role') . '/karyawan', $data);
    }
    public function batch()
    {
        $batch = $this->batchmodel->findAll();
        $data = [
            'role' => session()->get('role'),
            'title' => 'Batch',
            'active1' => '',
            'active2' => '',
            'active3' => '',
            'active4' => '',
            'active5' => 'active',
            'active6' => '',
            'batch' => $batch
        ];
        return view(session()->get('role') . '/batch', $data);
    }
    public function periode()
    {
        $periode = $this->periodeModel->getPeriode();
        $batch = $this->batchmodel->findAll();
        $data = [
            'role' => session()->get('role'),
            'title' => 'Periode',
            'active1' => '',
            'active2' => '',
            'active3' => '',
            'active4' => '',
            'active5' => 'active',
            'active6' => '',
            'periode' => $periode,
            'batch' => $batch
        ];
        return view(session()->get('role') . '/periode', $data);
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
        $karyawan = $this->karyawanmodel->getBagian();
        $periode = $this->periodeModel->getPeriode();

        $data = [
            'role' => session()->get('role'),
            'title' => 'Absen',
            'active1' => '',
            'active2' => '',
            'active3' => '',
            'active4' => '',
            'active5' => 'active',
            'active6' => '',
            'absen' => $absen,
            'users' => $users,
            'karyawan' => $karyawan,
            'periode' => $periode
        ];
        // dd($absen);
        return view(session()->get('role') . '/absen', $data);
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
                'OPERATOR' => [],
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
                    } elseif ($keterangan === 'OPERATOR') {
                        $groupedData[$id_jobrole]['OPERATOR'][] = $jobdesc;
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
            if (!isset($groupedData[$id_jobrole]['OPERATOR'])) {
                $groupedData[$id_jobrole]['OPERATOR'] = [];
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

        // dd ($jobrole);
        return view(session()->get('role') . '/jobrole', $data);
    }
    public function bsmc()
    {
        $tampilperarea = $this->bagianmodel->getAreaGroupByArea();
        $periode = $this->periodeModel->getPeriode();
        $getBatch = $this->batchmodel->getBatch();
        $getCurrentInput = $this->bsmcmodel->getCurrentInput();
        $sort = [
            'KK1A',
            'KK1B',
            'KK2A',
            'KK2B',
            'KK2C',
            'KK5',
            'KK7K',
            'KK7L',
            'KK8D',
            'KK8F',
            'KK8J',
            'KK9',
            'KK10',
            'KK11',
        ];
        // Fungsi untuk mengurutkan berdasarkan array urutan yang ditentukan
        // Urutkan data menggunakan usort
        usort($tampilperarea, function ($a, $b) use ($sort) {
            $pos_a = array_search($a['area'], $sort);
            $pos_b = array_search($b['area'], $sort);

            // Jika tidak ditemukan, letakkan di akhir
            $pos_a = ($pos_a === false) ? PHP_INT_MAX : $pos_a;
            $pos_b = ($pos_b === false) ? PHP_INT_MAX : $pos_b;

            return $pos_a - $pos_b;
        });

        $data = [
            'role' => session()->get('role'),
            'title' => 'Bs Mesin',
            'active1' => '',
            'active2' => '',
            'active3' => '',
            'active4' => '',
            'active5' => '',
            'active6' => '',
            'active7' => '',
            'active8' => '',
            'active9' => 'active',
            'getBatch' => $getBatch,
            'tampilperarea' => $tampilperarea,
            'periode' => $periode,
            'getCurrentInput' => $getCurrentInput
        ];
        return view(session()->get('role') . '/bsmc', $data);
    }
    public function rosso()
    {
        $tampilperarea = $this->bagianmodel->getAreaGroupByAreaUtama();
        $getBatch = $this->batchmodel->getBatch();
        $periode = $this->periodeModel->getPeriode();
        $getCurrentInput = $this->summaryRosso->getCurrentInput();

        $sort = [
            'KK1',
            'KK2',
            'KK5',
            'KK7',
            'KK8',
            'KK9',
            'KK10',
            'KK11'
        ];
        // dd($tampilperarea);
        // Fungsi untuk mengurutkan berdasarkan array urutan yang ditentukan
        // Urutkan data menggunakan usort
        usort($tampilperarea, function ($a, $b) use ($sort) {
            $pos_a = array_search($a['area_utama'], $sort);
            $pos_b = array_search($b['area_utama'], $sort);

            // Jika tidak ditemukan, letakkan di akhir
            $pos_a = ($pos_a === false) ? PHP_INT_MAX : $pos_a;
            $pos_b = ($pos_b === false) ? PHP_INT_MAX : $pos_b;

            return $pos_a - $pos_b;
        });

        // $reportbatch = $this->penilaianmodel->getPenilaianGroupByBatchAndArea();
        $getArea = $this->bagianmodel->getAreaGroupByAreaUtama();
        // $getBatch = $this->penilaianmodel->getPenilaianGroupByBatch();
        // dd($reportbatch);
        $data = [
            'role' => session()->get('role'),
            'title' => 'Rosso',
            'active1' => '',
            'active2' => '',
            'active3' => '',
            'active4' => '',
            'active5' => '',
            'active6' => '',
            'active7' => '',
            'active8' => '',
            'active9' => 'active',
            // 'reportbatch' => $reportbatch,
            // 'getArea' => $getArea,
            'getBatch' => $getBatch,
            'tampilperarea' => $tampilperarea,
            'periode' => $periode,
            'getCurrentInput' => $getCurrentInput
        ];
        // dd ($getBatch);
        return view(session()->get('role') . '/rosso', $data);
    }
    public function jarum()
    {
        $getBatch = $this->batchmodel->getBatch();
        $periode = $this->periodeModel->getPeriode();
        $getArea = $this->bagianmodel->getAreaByNamaBagian();
        $getCurrentInput = $this->summaryRosso->getCurrentInput();

        // dd($getArea);
        $sort = [
            'KK1A',
            'KK1B',
            'KK2A',
            'KK2B',
            'KK2C',
            'KK5',
            'KK7K',
            'KK7L',
            'KK8D',
            'KK8F',
            'KK8J',
            'KK9',
            'KK10',
            'KK11',
        ];

        // Urutkan data menggunakan usort
        usort($getArea, function ($a, $b) use ($sort) {
            $pos_a = array_search($a['area'], $sort);
            $pos_b = array_search($b['area'], $sort);

            // Jika tidak ditemukan, letakkan di akhir
            $pos_a = ($pos_a === false) ? PHP_INT_MAX : $pos_a;
            $pos_b = ($pos_b === false) ? PHP_INT_MAX : $pos_b;

            return $pos_a - $pos_b;
        });

        $data = [
            'role' => session()->get('role'),
            'title' => 'Jarum',
            'active1' => '',
            'active2' => '',
            'active3' => '',
            'active4' => '',
            'active5' => '',
            'active6' => '',
            'active7' => '',
            'active8' => '',
            'active9' => 'active',
            'getBatch' => $getBatch,
            'periode' => $periode,
            'getArea' => $getArea,
            'getCurrentInput' => $getCurrentInput
        ];
        // dd ($getBatch);
        return view(session()->get('role') . '/jarum', $data);
    }
    public function penilaian()
    {
        // Ambil data filter dari request
        $nama_bagian = $this->request->getGet('nama_bagian');
        $area_utama = $this->request->getGet('area_utama');
        $area = $this->request->getGet('area');

        // Default filter untuk karyawan
        $karyawanQuery = $this->karyawanmodel->select('karyawan.*')
            ->join('bagian', 'karyawan.id_bagian = bagian.id_bagian', 'left'); // Join dengan tabel bagian

        // Tambahkan filter berdasarkan nama_bagian
        if ($nama_bagian) {
            $karyawanQuery->where('bagian.nama_bagian', $nama_bagian);
        }

        // Tambahkan filter berdasarkan area_utama
        if ($area_utama) {
            $karyawanQuery->where('bagian.area_utama', $area_utama);
        }

        // Tambahkan filter berdasarkan area
        if ($area) {
            $karyawanQuery->where('bagian.area', $area);
        }

        // Ambil data karyawan yang difilter
        $karyawan = $karyawanQuery->findAll();

        // Data lainnya
        $batch = $this->batchmodel->findAll();
        $namabagian = $this->bagianmodel->getBagian();
        $penilaian = $this->penilaianmodel->getPenilaian();
        $periode = $this->periodeModel->getPeriode();

        // Siapkan data untuk view
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
            'active8' => '',
            'active9' => 'active',
            'batch' => $batch,
            'namabagian' => $namabagian,
            'periode' => $periode,
            'penilaian' => $penilaian,
            'karyawan' => $karyawan // Karyawan yang difilter
        ];

        return view(session()->get('role') . '/penilaian', $data);
    }

    public function reportpenilaian()
    {
        $tampilperarea = $this->bagianmodel->getAreaGroupByAreaUtama();
        $sort = [
            'KK1',
            'KK2',
            'KK5',
            'KK7',
            'KK8',
            'KK9',
            'KK10',
            'KK11'
        ];
        // dd($tampilperarea);
        // Fungsi untuk mengurutkan berdasarkan array urutan yang ditentukan
        // Urutkan data menggunakan usort
        usort($tampilperarea, function ($a, $b) use ($sort) {
            $pos_a = array_search($a['area_utama'], $sort);
            $pos_b = array_search($b['area_utama'], $sort);

            // Jika tidak ditemukan, letakkan di akhir
            $pos_a = ($pos_a === false) ? PHP_INT_MAX : $pos_a;
            $pos_b = ($pos_b === false) ? PHP_INT_MAX : $pos_b;

            return $pos_a - $pos_b;
        });

        $getArea = $this->bagianmodel->getAreaGroupByAreaUtama();
        // dd($reportbatch);
        $data = [
            'role' => session()->get('role'),
            'title' => 'Report Penilaian',
            'active1' => '',
            'active2' => '',
            'active3' => '',
            'active4' => '',
            'active5' => '',
            'active6' => '',
            'active7' => '',
            'active8' => '',
            'active9' => 'active',
            'getArea' => $getArea,
            'tampilperarea' => $tampilperarea
            // 'area' => $area
        ];
        // dd ($getBatch);
        return view(session()->get('role') . '/reportpenilaian', $data);
    }

    public function reportbatch()
    {
        $tampilperarea = $this->bagianmodel->getAreaGroupByAreaUtama();
        array_unshift($tampilperarea, ['area_utama' => 'all']); //Menambahkan data All Area
        $sort = [
            'KK1',
            'KK2',
            'KK5',
            'KK7',
            'KK8',
            'KK9',
            'KK10',
            'KK11',
            'all'
        ];

        // Urutkan data menggunakan usort
        usort($tampilperarea, function ($a, $b) use ($sort) {
            $pos_a = array_search($a['area_utama'], $sort);
            $pos_b = array_search($b['area_utama'], $sort);

            // Jika tidak ditemukan, letakkan di akhir
            $pos_a = ($pos_a === false) ? PHP_INT_MAX : $pos_a;
            $pos_b = ($pos_b === false) ? PHP_INT_MAX : $pos_b;

            return $pos_a - $pos_b;
        });
        // dd($tampilperarea);
        $getBulan = $this->penilaianmodel->getBatchGroupByBulanPenilaian();
        $getBagian = $this->bagianmodel->getBagian();
        $getBatch = $this->penilaianmodel->getPenilaianGroupByBatch();

        $data = [
            'role' => session()->get('role'),
            'title' => 'Report Batch',
            'active1' => '',
            'active2' => '',
            'active3' => '',
            'active4' => '',
            'active5' => '',
            'active6' => '',
            'active7' => '',
            'active8' => '',
            'active9' => 'active',
            'getBulan' => $getBulan,
            'getBagian' => $getBagian,
            'getArea' => $tampilperarea,
            'getBatch' => $getBatch,
            'tampilperarea' => $tampilperarea,
        ];
        // dd ($getBatch);
        return view(session()->get('role') . '/reportbatch', $data);
    }
    public function reportSummaryRosso()
    {
        $summaryRosso = $this->summaryRosso->getRossoGroupByPeriode();
        $periode = $this->periodeModel->getPeriode();

        $data = [
            'role' => session()->get('role'),
            'title' => 'Report Summary Rosso',
            'active1' => '',
            'active2' => '',
            'active3' => '',
            'active4' => '',
            'active5' => '',
            'active6' => '',
            'active7' => '',
            'active8' => 'active',
            'summaryRosso' => $summaryRosso,
            'periode' => $periode

        ];

        // dd ($summaryRosso);
        return view(session()->get('role') . '/reportsummaryrosso', $data);
    }

    public function cekPenilaian()
    {
        $periode = $this->periodeModel->getPeriode();
        $cekPenilaian = $this->penilaianmodel->getMandorEvaluationStatus();
        $data = [
            'role' => session()->get('role'),
            'title' => 'Cek Penilaian',
            'active1' => '',
            'active2' => '',
            'active3' => '',
            'active4' => '',
            'active5' => '',
            'active6' => '',
            'active7' => '',
            'active8' => 'active',
            'periode' => $periode,
            'cekPenilaian' => $cekPenilaian
        ];
        // dd ($data);
        return view(session()->get('role') . '/cekpenilaian', $data);
    }

    public function historyPindahKaryawan()
    {
        $historyPindahKaryawan = $this->historyPindahKaryawanModel->getHistoryPindahKaryawan();
        $data = [
            'role' => session()->get('role'),
            'title' => 'History Pindah Karyawan',
            'active1' => '',
            'active2' => '',
            'active3' => 'active',
            'historyPindahKaryawan' => $historyPindahKaryawan
        ];
        return view(session()->get('role') . '/perpindahan', $data);
    }

    public function getMontirByArea()
    {
        $montir = [];

        if ($this->request->isAJAX()) {
            $area = $this->request->getPost('area');
            $tgl_input = $this->request->getPost('tgl_input');

            // Ambil id_bagian berdasarkan area
            $bagian = $this->bagianmodel->getMontirByArea($area);

            // Ambil periode berdasarkan tanggal
            $periode = $this->periodeModel->getPeriodeByTanggal($tgl_input); // pastikan fungsi ini sudah ada

            if ($periode) {
                foreach ($bagian as $row) {
                    $kary = $this->karyawanmodel->getMontirByArea($row['id_bagian'], $periode['id_periode']);
                    $montir = array_merge($montir, $kary);
                }
            }
        }

        return $this->response->setJSON($montir);
    }
}
