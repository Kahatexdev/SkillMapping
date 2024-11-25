<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\BagianModel;
use App\Models\JobroleModel;
use App\Models\PenilaianModel;
use App\Models\BatchModel;
use App\Models\KaryawanModel;
use App\Models\PeriodeModel;
use CodeIgniter\HTTP\ResponseInterface;
use PhpOffice\PhpSpreadsheet\Spreadsheet;

class PenilaianController extends BaseController
{
    protected $penilaianmodel;
    protected $jobrolemodel;
    protected $bagianmodel;
    protected $batchmodel;
    protected $karyawanmodel;
    protected $periodeModel;

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
        $this->periodeModel = new PeriodeModel();
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
        $id_periode = $this->request->getGet('id_periode');

        if (!$id_periode) {
            return redirect()->back()->with('error', 'Periode not found.');
        } 

        $nama_bagian = $this->request->getGet('nama_bagian');
        $area_utama = $this->request->getGet('area_utama');
        $area = $this->request->getGet('area');

        if($area == 'null') {
            $area = null;
        } 
        

        $id_bagian = $this->bagianmodel->getIdBagian($nama_bagian, $area_utama, $area);
        // dd ($id_bagian, $nama_bagian, $area_utama, $area);
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
        // if ($shift) {
        //     $karyawanQuery->where('karyawan.shift', $shift);  // Use shift from the 'karyawan' table
        // }

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

        // if ($penilaian = $this->penilaianmodel->cekPenilaian($karyawan[0]['id_karyawan'], $id_periode['id_periode'], $id_jobrole['id_jobrole'], $id_user)) {
        //     return redirect()->back()->with('error', 'Penilaian sudah ada.');
        // }
        $temp = [
            'id_periode' => $id_periode,
            'id_jobrole' => $id_jobrole['id_jobrole'],
            'id_karyawan' => $karyawan,
            'id_user' => $id_user,
            'id_bagian' => $id_bagian['id_bagian']
        ];

       
        // dd($temp);

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
            return redirect()->to('/monitoring/dataPenilaian')->with('success', 'Penilaian berhasil disimpan.');
        }

        return redirect()->back()->with('error', 'Gagal menyimpan penilaian.');
    }

    public function show($id_bagian, $id_periode, $id_jobrole)
    {
        $id_bagian = (int) $id_bagian;
        $id_periode = (int) $id_periode;
        $id_jobrole = (int) $id_jobrole;
        // dd ($id_bagian, $id_periode, $id_jobrole);
        $penilaian = $this->penilaianmodel->getPenilaianByIdBagian($id_bagian, $id_periode, $id_jobrole);
        // dd ($penilaian[0]['bobot_nilai']);

        $bobotNilai = [];
        foreach ($penilaian as $p) {
            $bobotNilai[$p['karyawan_id']] = json_decode($p['bobot_nilai'], true);
        }

        // dd($bobotNilai);

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
            'penilaian' => $penilaian,
            'bobotNilai' => $bobotNilai
        ];

        return view('penilaian/show', $data);
    }

    public function reportExcel ($id_bagian, $id_batch, $id_jobrole)
    {
        $id_bagian = (int) $id_bagian;
        $id_batch = (int) $id_batch;
        $id_jobrole = (int) $id_jobrole;

        $penilaian = $this->penilaianmodel->getPenilaianByIdBagian($id_bagian, $id_batch, $id_jobrole);

        $bobotNilai = [];
        foreach ($penilaian as $p) {
            $bobotNilai[$p['karyawan_id']] = json_decode($p['bobot_nilai'], true);
        }

        // format nama file excel
        $filename = 'Penilaian-' . date('Y-m-d') . '.xlsx';

        // Load the Excel library
        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Set the column headers
        $sheet->setCellValue('A1', 'No');
        $sheet->setCellValue('B1', 'Nama Karyawan');
        $sheet->setCellValue('C1', 'Jobdesk');
        $sheet->setCellValue('D1', 'Bobot Nilai');
        $sheet->setCellValue('E1', 'Grade');

        // Set the data
        $no = 1;
        $row = 2;

        foreach ($penilaian as $p) {
            $jobdesc = json_decode($p['jobdesc'], true) ?? [];
            $keterangan = json_decode($p['keterangan'], true) ?? [];
            $index_nilai = json_decode($p['index_nilai'], true) ?? [];
            $bobot_nilai = json_decode($p['bobot_nilai'], true) ?? [];

            $total_nilai = 0;
            $total_bobot = 0;

            if (!empty($bobot_nilai) && !empty($index_nilai)) {
                foreach ($bobot_nilai as $key => $value) {
                    $indexVal = $index_nilai[$key] ?? 0;
                    $total_nilai += $indexVal * $value;
                    $total_bobot += $value;
                }
            }

            foreach ($jobdesc as $key => $desc) {
                $sheet->setCellValue('A' . $row, $no);
                $sheet->setCellValue('B' . $row, $p['nama_karyawan']);
                $sheet->setCellValue('C' . $row, $desc);
                $sheet->setCellValue('D' . $row, $p['bobot_nilai']);
                $sheet->setCellValue('E' . $row, $p['index_nilai']);

                $row++;
            }

            $no++;
        }

        // Set the header
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');

        // Save the file to the output
        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        $writer->save('php://output');

        exit();

    }
}
