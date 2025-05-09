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
// use App\Models\UserModel;
use App\Models\HistoryPindahKaryawanModel;
use App\Models\MessageModel;

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
    protected $historyPindahKaryawanModel;
    // protected $userModel;
    protected $messageModel;
    // protected $userModel;

    public function __construct()
    {
        // test
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
        $this->messageModel = new MessageModel();

        // $this->userModel = new UserModel();
    }

    public function index()
    {
        $TtlKaryawan = $this->karyawanmodel->where('status', 'Aktif')->countAll();
        $PerpindahanBulanIni = $this->historyPindahKaryawanModel->where('MONTH(tgl_pindah)', date('m'))->countAllResults();
        $dataKaryawan = $this->karyawanmodel->getActiveKaryawanByBagiaAndArea();

        // Group data berdasarkan area_utama
        $groupedData = [];
        foreach ($dataKaryawan as $row) {
            $groupedData[$row['area_utama']][] = $row;
        }

        // Sort berdasarkan angka setelah 'KK'
        uksort($groupedData, function ($a, $b) {
            return (int) filter_var($a, FILTER_SANITIZE_NUMBER_INT) <=> (int) filter_var($b, FILTER_SANITIZE_NUMBER_INT);
        });

        $totalKaryawan = 0;
        foreach ($dataKaryawan as $row) {
            $totalKaryawan += $row['jumlah_karyawan'];
        }

        $dataPindah = $this->historyPindahKaryawanModel->getPindahGroupedByDate();

        $labelsKar = [];
        $valuesKar = [];
        foreach ($dataPindah as $row) {
            $labelsKar[] = $row['tgl'];
            $valuesKar[] = (int)$row['jumlah'];
        }

        return view('trainingschool/index', [
            'role' => session()->get('role'),
            'title' => 'Dashboard',
            'active1' => 'active',
            'active2' => '',
            'active3' => '',
            'TtlKaryawan' => $TtlKaryawan,
            'PerpindahanBulanIni' => $PerpindahanBulanIni,
            'groupedData' => $groupedData,
            'labelsKar' => $labelsKar,
            'valuesKar' => $valuesKar
        ]);
    }

    public function listArea()
    {
        $apiUrl = 'http://172.23.44.14/CapacityApps/public/api/getPlanMesin';
        $response = file_get_contents($apiUrl);
        $plan = json_decode($response, true);  // Decode JSON response dari API
        $tampilperarea = $this->bagianmodel->getAreaOnly();
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

        // Fungsi untuk mengurutkan berdasarkan array urutan yang ditentukan
        usort($tampilperarea, function ($a, $b) use ($sort) {
            $pos_a = array_search($a['area_utama'], $sort);
            $pos_b = array_search($b['area_utama'], $sort);

            // Jika tidak ditemukan, letakkan di akhir
            $pos_a = ($pos_a === false) ? PHP_INT_MAX : $pos_a;
            $pos_b = ($pos_b === false) ? PHP_INT_MAX : $pos_b;

            return $pos_a - $pos_b;
        });

        $data = [
            'role' => session()->get('role'),
            'title' => 'Karyawan',
            'active1' => '',
            'active2' => 'active',
            'active3' => '',
            'active4' => '',
            'active5' => '',
            'active6' => '',
            'active7' => '',
            'tampildata' => $tampilperarea,
            'listplan' => $plan
        ];
        // dd($data);
        return view(session()->get('role') . '/karyawan', $data);
    }
    public function detailKaryawanPerArea($area)
    {
        if ($area === 'EMPTY') {
            $karyawan = $this->karyawanmodel->getKaryawanTanpaArea();
        } else {
            $karyawan = $this->karyawanmodel->getKaryawanByArea($area);
            // dd ($karyawan);
        }
        // dd ($area);
        // dd($karyawan);
        $bagianModel = new \App\Models\BagianModel();
        $bagian = $bagianModel->findAll();
        // dd($karyawan);
        $data = [
            'role' => session()->get('role'),
            'title' => 'Karyawan',
            'active1' => 'active',
            'active2' => '',
            'active3' => '',
            'karyawan' => $karyawan,
            'area' => $area,
            'bagian' => $bagian
        ];
        return view(session()->get('role') . '/detailKaryawan', $data);
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
        return view(session()->get('role') . '/historyPindahKaryawan', $data);
    }

    public function chat()
    {
        $userId = session()->get('id_user'); // ID pengguna yang login
        $contacts = $this->usermodel->findAll(); // Ambil semua kontak dari database (selain pengguna yang login)

        $contactsWithLastMessage = [];

        foreach ($contacts as $contact) {
            if ($contact['id_user'] != $userId) {
                // Ambil pesan terakhir antara pengguna yang login dan kontak ini
                $lastMessage = $this->messageModel
                    ->where("(sender_id = $userId AND receiver_id = {$contact['id_user']}) OR (sender_id = {$contact['id_user']} AND receiver_id = $userId)")
                    ->orderBy('created_at', 'DESC')
                    ->limit(1)
                    ->first();

                $contactsWithLastMessage[] = [
                    'contact' => $contact,
                    'last_message' => $lastMessage
                ];
            }
        }

        $data = [
            'role' => session()->get('role'),
            'title' => 'Chat',
            'active4' => 'active',
            'contacts' => $contactsWithLastMessage // Kirim data kontak beserta pesan terakhir
        ];

        return view('chat/index', $data);
    }
}
