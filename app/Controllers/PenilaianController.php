<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\BagianModel;
use App\Models\JobroleModel;
use App\Models\PenilaianModel;
use App\Models\BatchModel;
use App\Models\KaryawanModel;
use CodeIgniter\HTTP\ResponseInterface;

class PenilaianController extends BaseController
{
    protected $penilaianmodel;
    protected $jobrolemodel;
    protected $bagianmodel;
    protected $batchmodel;
    protected $karyawanmodel;

    const bobot_nilai = [
        1 => 15,
        2 => 30,
        3 => 45,
        4 => 60,
        5 => 85,
        6 => 100
    ];


    public function __construct()
    {
        $this->penilaianmodel = new PenilaianModel();
        $this->jobrolemodel = new JobroleModel();
        $this->bagianmodel = new BagianModel();
        $this->batchmodel = new BatchModel();
        $this->karyawanmodel = new KaryawanModel();
    }

    public function getAreaUtama()
    {
        if ($this->request->isAJAX()) {
            $nama_bagian = $this->request->getPost('nama_bagian');
            // group by area_utama
            $areaUtama = $this->bagianmodel
                ->select('area_utama')
                ->where('nama_bagian', $nama_bagian)
                ->groupBy('area_utama')
                ->findAll();

            // Debug: Pastikan query berhasil
            // dd($areaUtama);

            return $this->response->setJSON($areaUtama);
        }

        return $this->response->setStatusCode(404);
    }

    public function getArea()
    {
        if ($this->request->isAJAX()) {
            $area_utama = $this->request->getPost('area_utama');
            $nama_bagian = $this->request->getPost('nama_bagian');

            $areaData = $this->bagianmodel
                ->where('area_utama', $area_utama)
                ->where('nama_bagian', $nama_bagian)
                ->findAll();

            return $this->response->setJSON($areaData);
        }

        return $this->response->setStatusCode(404);
    }

    public function getJobRole()
    {
        $nama_bagian = $this->request->getPost('nama_bagian');
        $area_utama = $this->request->getPost('area_utama');
        $area = $this->request->getPost('area');

        // Ambil ID Bagian berdasarkan Nama Bagian, Area Utama, dan Area
        $id_bagian = $this->bagianmodel->getIdBagian($nama_bagian, $area_utama, $area);

        // Ambil data Job Role berdasarkan ID Bagian
        $jobRole = $this->jobrolemodel->getJobRoleByBagianId($id_bagian['id_bagian']);

        return $this->response->setJSON($jobRole);
    }

    public function cekPenilaian()
    {
        $shift = $this->request->getPost('shift');
        $bulan = $this->request->getPost('bulan');
        $tahun = $this->request->getPost('tahun');
        // dd($shift, $bulan, $tahun);
        $id_batch = $this->batchmodel->getIdBatch($shift, $bulan, $tahun);
        // dd($id_batch);
        $nama_bagian = $this->request->getPost('nama_bagian');
        $area_utama = $this->request->getPost('area_utama');
        $area = $this->request->getPost('area');

        $id_bagian = $this->bagianmodel->getIdBagian($nama_bagian, $area_utama, $area);
        // dd($id_bagian);

        $id_jobrole = $this->jobrolemodel->getIdJobrole($id_bagian['id_bagian']);
        // dd($id_jobrole);

        $karyawan_id = 1; // Dummy data
        // dd($karyawan_id);

        $id_user = 1; // Dummy data

        $datauntukinputnilai = [
            'id_batch' => $id_batch['id_batch'],
            'id_jobrole' => $id_jobrole['id_jobrole'],
            'id_karyawan' => $karyawan_id,
            'id_user' => $id_user
        ];

        $json = json_encode($datauntukinputnilai);

        return view('penilaian/create', compact('json'));
    }

    public function index() {
    }

    public function create()
    {
        // Get data from URL query parameters
        $shift = $this->request->getGet('shift');
        $bulan = $this->request->getGet('bulan');
        $tahun = $this->request->getGet('tahun');

        $id_batch = $this->batchmodel->getIdBatch($shift, $bulan, $tahun);
        if (!$id_batch) {
            return redirect()->back()->with('error', 'Batch not found.');
        } 

        $nama_bagian = $this->request->getGet('nama_bagian');
        $area_utama = $this->request->getGet('area_utama');
        $area = $this->request->getGet('area');

        $id_bagian = $this->bagianmodel->getIdBagian($nama_bagian, $area_utama, $area);
        if (!$id_bagian) {
            return redirect()->back()->with('error', 'Bagian not found.');
        }

        $id_jobrole = $this->jobrolemodel->getJobRoleByBagianId($id_bagian['id_bagian']);
        if (!$id_jobrole) {
            return redirect()->back()->with('error', 'Job role not found.');
        }

        // Decode jobdesc from JSON
        $jobdesc = json_decode($id_jobrole['jobdesc'], true) ?? [];
        if (empty($jobdesc)) {
            return redirect()->back()->with('error', 'Job description not available.');
        }

        // Filter karyawan based on area and shift by joining 'karyawan' and 'bagian' tables
        $karyawanQuery = $this->karyawanmodel->select('karyawan.*')
            ->join('bagian', 'karyawan.id_bagian = bagian.id_bagian', 'left'); // Join with bagian table

        // Filter by area if available
        if ($area) {
            $karyawanQuery->where('bagian.area', $area);  // Use area from the 'bagian' table
        }

        // Filter by shift if available
        if ($shift) {
            $karyawanQuery->where('karyawan.shift', $shift);  // Use shift from the 'karyawan' table
        }

        // Filter by bagian if available
        if ($id_bagian) {
            $karyawanQuery->where('karyawan.id_bagian', $id_bagian['id_bagian']);  // Use id_bagian from the 'karyawan' table
        }

        // Fetch the filtered karyawan data
        $karyawan = $karyawanQuery->findAll();

        if (!$karyawan) {
            return redirect()->back()->with('error', 'No employees found.');
        }


        $id_user = session()->get('id_user') ?? 1; // Replace dummy data with session user if available

        if ($penilaian = $this->penilaianmodel->cekPenilaian($karyawan[0]['id_karyawan'], $id_batch['id_batch'], $id_jobrole['id_jobrole'], $id_user)) {
            return redirect()->back()->with('error', 'Penilaian sudah ada.');
        }
        $temp = [
            'id_batch' => $id_batch['id_batch'],
            'id_jobrole' => $id_jobrole['id_jobrole'],
            'id_karyawan' => $karyawan,
            'id_user' => $id_user,
            'id_bagian' => $id_bagian['id_bagian']
        ];

       
        // dd($temp);

        $data = [
            'role' => session()->get('role'),
            'title' => 'Penilaian',
            'active1' => '',
            'active2' => '',
            'active3' => '',
            'active4' => '',
            'active5' => 'active',
            'active6' => '',
            'jobrole' => $id_jobrole,
            'jobdesc' => $jobdesc, // Pass jobdesc to view
            'karyawan' => $karyawan,
            'temp' => $temp
        ];

        return view('penilaian/create', $data);
    }

    // Controller method to handle AJAX request
    public function updateIndexNilai()
    {
        // Get POST data
        $karyawanId = $this->request->getPost('karyawan_id');
        $totalNilai = $this->request->getPost('total_nilai');
        $average = $this->request->getPost('average');

        // Determine the index_nilai based on the average
        $indexNilai = 'A'; // Default to 'A'
        if ($average < 59) {
            $indexNilai = 'D';
        } elseif ($average < 75) {
            $indexNilai = 'C';
        } elseif ($average < 85) {
            $indexNilai = 'B';
        }

        // Return the index_nilai in a response
        return $this->response->setJSON([
            'index_nilai' => $indexNilai
        ]);
    }




    public function store()
    {
        // Dump all POST data to verify inputs
        // dd($this->request->getPost());

        // Retrieve the posted data
        $batchId = $this->request->getPost('id_batch');
        $jobroleId = $this->request->getPost('id_jobrole');
        $karyawanIds = $this->request->getPost('id_karyawan');
        $bobotNilai = $this->request->getPost('nilai');
        // $indexNilai = $this->request->getPost('index_nilai');  // Should now contain data
        $id_user = session()->get('id_user');

        // hitung nilai rata-rata dari bobot nilai dengan constanta bobot_nilai
        $indexNilai = [];

        foreach ($bobotNilai as $karyawanId => $nilai) {
            $totalNilai = 0;
            $totalBobot = 0;
            foreach ($nilai as $jobdesc => $value) {
                $totalNilai += $value;
                $totalBobot += self::bobot_nilai[$value];
            }
            $average = $totalBobot / count($nilai);
            // dd($average);
            $indexNilai[$karyawanId] = $average;
        }

        // dd($indexNilai);

        // ubah nilai rata-rata menjadi grade
        foreach ($indexNilai as $karyawanId => $average) {
            $indexNilai[$karyawanId] = 'A'; // Default to 'A'
            if ($average < 59) {
                $indexNilai[$karyawanId] = 'D';
            } elseif ($average < 75) {
                $indexNilai[$karyawanId] = 'C';
            } elseif ($average < 85) {
                $indexNilai[$karyawanId] = 'B';
            } elseif ($average < 101) {
                $indexNilai[$karyawanId] = 'A';
            }
        }

        // dd($indexNilai);

        // Prepare the data to be inserted
        $data = [];
        foreach ($karyawanIds as $karyawanId) {
            $data[] = [
                'id_batch' => $batchId,
                'id_jobrole' => $jobroleId,
                'karyawan_id' => $karyawanId,
                'bobot_nilai' => json_encode($bobotNilai[$karyawanId]),
                'index_nilai' => $indexNilai[$karyawanId],
                'id_user' => $id_user
            ];
        }

        // dd($data);

        if ($this->penilaianmodel->insertBatch($data)) {
            return redirect()->to('/monitoring/dataPenilaian')->with('success', 'Penilaian berhasil disimpan.');
        }

        return redirect()->back()->with('error', 'Gagal menyimpan penilaian.');
    }
}
