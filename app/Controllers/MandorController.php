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
class MandorController extends BaseController
{
    protected $karyawanmodel;
    protected $bagianmodel;
    protected $usermodel;
    protected $jobrolemodel;
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
        $this->jobrolemodel = new JobroleModel();
        $this->absenmodel = new AbsenModel();
        $this->bsmcmodel = new BsmcModel();
        $this->summaryRosso = new SummaryRossoModel();
        $this->batchmodel = new BatchModel();
        $this->periodeModel = new PeriodeModel();
        $this->penilaianmodel = new PenilaianModel();
    }

    public function getAreaUtama()
    {
        $namaBagian = $this->request->getGet('nama_bagian');
        $areaUtama = $this->bagianmodel->getAreaUtamaByBagian($namaBagian);
        // var_dump ($this->response->setJSON($areaUtama));
        return $this->response->setJSON($areaUtama);
    }

    public function getArea()
    {
        $namaBagian = $this->request->getGet('nama_bagian');
        $areaUtama = $this->request->getGet('area_utama');
        $area = $this->bagianmodel->getAreaByBagianAndUtama($namaBagian, $areaUtama);
        return $this->response->setJSON($area);
    }

    public function getKaryawan()
    {
        $namaBagian = $this->request->getGet('nama_bagian');
        $areaUtama = $this->request->getGet('area_utama');
        $area = $this->request->getGet('area');
        // dd ($namaBagian, $areaUtama, $area);
        if ($area == 'null') {
            $area = null;
        }
        $karyawan = $this->karyawanmodel->getKaryawanByFilters($namaBagian, $areaUtama, $area);
        // dd ($karyawan);  
        return $this->response->setJSON($karyawan);
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


    public function index()
    {
    }

    public function listArea()
    {
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
        }
        // dd($area);
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
            'title' => 'Penilaian',
            'active1' => '',
            'active2' => 'active',
            'active3' => '',
            'batch' => $batch,
            'namabagian' => $namabagian,
            'periode' => $periode,
            'penilaian' => $penilaian,
            'karyawan' => $karyawan // Karyawan yang difilter
        ];
        // dd ($data);
        return view(session()->get('role') . '/penilaian', $data);
    }

    public function create()
    {
        // Get data from URL query parameters
        $id_periode = $this->request->getPost('id_periode');

        if (!$id_periode) {
            return redirect()->back()->with('error', 'Periode not found.');
        }

        $nama_bagian = $this->request->getPost('nama_bagian');
        $area_utama = $this->request->getPost('area_utama');
        $area = $this->request->getPost('area');

        if ($area == 'null') {
            $area = null;
        }

        if ($area != session()->get('area')) {
            return redirect()->back()->with('error', 'Bukan Hak Kamu.');
        }

        $id_bagian = $this->bagianmodel->getIdBagian($nama_bagian, $area_utama, $area);

        if (!$id_bagian) {
            return redirect()->back()->with('error', 'Bagian not found.');
        }

        $id_jobrole = $this->jobrolemodel->getJobRoleByBagianId($id_bagian['id_bagian']);
        // dd($id_jobrole);
        if (!$id_jobrole) {
            return redirect()->back()->with('error', 'Job role not found.');
        }

        // Decode jobdesc from JSON
        $jobdesc = json_decode($id_jobrole['jobdesc'], true) ?? [];
        if (empty($jobdesc)) {
            return redirect()->back()->with('error', 'Job description not available.');
        }

        
        // $jobdesc = json_decode($id_jobrole['jobdesc'], true ) ?? [];
        $keterangan = json_decode($id_jobrole['keterangan'], true) ?? [];
        // Gabungkan jobdesc berdasarkan keterangannya
        $jobdescWithKet = [];
        foreach ($jobdesc as $index => $desc) {
            $jobdescWithKet[$keterangan[$index]][] = $desc; // Kelompokkan berdasarkan keterangan
        }
        // // Gabungkan jobdesc dan keterangan
        // $jobdescWithKet = [];
        // foreach ($jobdesc as $index => $desc) {
        //     $jobdescWithKet[] = [
        //         'deskripsi' => $desc,
        //         'keterangan' => $keterangan[$index] ?? null
        //     ];
        // }
        // $ketJob = $this->jobrolemodel->getJobRolesByJobRoleId($id_jobrole['id_jobrole']);
        // dd($ketJob);
        // Jika data karyawan dipilih dari form multiple select
        $selected_karyawan_ids = $this->request->getPost('karyawan'); // Ambil data karyawan dari form POST

        // Jika tidak ada karyawan yang dipilih, tampilkan pesan error
        if (empty($selected_karyawan_ids)) {
            return redirect()->back()->with('error', 'Pilih minimal satu karyawan.');
        }

        // Ambil detail karyawan berdasarkan ID yang dipilih
        $karyawan = $this->karyawanmodel->whereIn('id_karyawan', $selected_karyawan_ids)->findAll();

        if (empty($karyawan)) {
            return redirect()->back()->with('error', 'Tidak ada data karyawan yang ditemukan.');
        }

        $id_user = session()->get('id_user') ?? 1; // Replace dummy data with session user if available

        // jika karyawan sudah pernah dinilai pada periode dan id_jobrole yang dipilih, tampilkan pesan error
        $existingPenilaian = $this->penilaianmodel->getExistingPenilaian($id_periode, $id_jobrole['id_jobrole'], $selected_karyawan_ids);
        // dd ($existingPenilaian, $id_periode, $id_jobrole['id_jobrole'], $selected_karyawan_ids);
        if (!empty($existingPenilaian)) {
            return redirect()->back()->with('error', 'Karyawan sudah dinilai pada periode ini.');
        }
        // karyawan count
        $karyawanCount = count($karyawan);
        $temp = [
            'id_periode' => $id_periode,
            'id_jobrole' => $id_jobrole['id_jobrole'],
            'id_karyawan' => $karyawan,
            'id_user' => $id_user,
            'id_bagian' => $id_bagian['id_bagian']
        ];

        // dd ($temp);
        $data = [
            'role' => session()->get('role'),
            'title' => 'Penilaian',
            'active1' => '',
            'active2' => '',
            'active3' => '',
            'active4' => '',
            'active5' => '',
            'active6' => '',
            'active7' => '',
            'active8' => 'active',
            'jobrole' => $id_jobrole,
            'jobdesc' => $jobdesc, // Pass jobdesc to view
            'karyawan' => $karyawan,
            'karyawanCount' => $karyawanCount,
            'temp' => $temp,
            'jobdescWithKet' => $jobdescWithKet // Kirim data gabungan ke view
        ];

        // dd ($data);

        return view('Penilaian/create', $data);
    }

    const bobot_nilai = [
        1 => 15,
        2 => 30,
        3 => 45,
        4 => 60,
        5 => 85,
        6 => 100
    ];
    
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
        $periodeId = $this->request->getPost('id_periode');
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
                'id_periode' => $periodeId,
                'id_jobrole' => $jobroleId,
                'karyawan_id' => $karyawanId,
                'bobot_nilai' => json_encode($bobotNilai[$karyawanId]),
                'index_nilai' => $indexNilai[$karyawanId],
                'id_user' => $id_user
            ];
        }

        // dd($data);

        if ($this->penilaianmodel->insertBatch($data)) {
            return redirect()->to('/Mandor')->with('success', 'Penilaian berhasil disimpan.');
        }

        return redirect()->back()->with('error', 'Gagal menyimpan penilaian.');
    }

    public function penilaianPerArea($area_utama)
    {
        $area_utama = session()->get('area');

        // jika karakter diakhir username itu sebuah huruf, maka hapus huruf tersebut
        if (ctype_alpha(substr($area_utama, -1))) {
            $area_utama = substr($area_utama, 0, -1);
        } else {
            $area_utama = $area_utama;
        }

        // dd ($area_utama);
        // dd ($area_utama);
        // dd ($id_periode);
        $penilaian = $this->penilaianmodel->getPenilaianPerArea($area_utama);
        // dd ($penilaian);
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
            'active8' => 'active',
            'penilaian' => $penilaian,
            'area_utama' => $area_utama
        ];
        // dd ($data);

        return view(session()->get('role') . '/reportareaperarea', $data);
    }
    public function penilaianPerPeriode($area_utama, $id_periode)
    {
        $area_utama = urldecode($area_utama);
        // jika karakter diakhir username itu sebuah huruf, maka hapus huruf tersebut
        if (ctype_alpha(substr($area_utama, -1))) {
            $area_utama = substr($area_utama, 0, -1);
        } else {
            $area_utama = $area_utama;
        }
        // dd ($area_utama);
        $id_periode = (int) $id_periode;
        // dd ($id_periode);
        $penilaian = $this->penilaianmodel->getPenilaianPerPeriode($area_utama, $id_periode);
        // dd ($penilaian);
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
            'active8' => 'active',
            'penilaian' => $penilaian
        ];
        // dd ($data);

        return view(session()->get('role') . '/reportareaperperiode', $data);
    }
}
