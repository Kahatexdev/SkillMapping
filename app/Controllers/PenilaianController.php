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
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;


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

    // Fungsi untuk menghitung grade
    private function calculateGrade($average)
    {
        if ($average < 101) return 'A';
        if ($average < 85) return 'B';
        if ($average < 75) return 'C';
        return 'D';
    }

    // Fungsi untuk menghitung skor
    private function calculateSkor($grade)
    {
        $map = ['A' => 4, 'B' => 3, 'C' => 2, 'D' => 1];
        return $map[$grade] ?? 0;
    }

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

    public function index() {}

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

        if ($area == 'null') {
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
            return redirect()->to('/Monitoring/dataPenilaian')->with('success', 'Penilaian berhasil disimpan.');
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



    /**
     * Menghitung nama kolom Excel berdasarkan indeks (1-based).
     *
     * @param int $columnIndex Indeks kolom (1 untuk A, 27 untuk AA, dll.)
     * @return string Nama kolom Excel
     */
    function getColumnName($columnIndex)
    {
        $columnName = '';
        while ($columnIndex > 0) {
            $mod = ($columnIndex - 1) % 26;
            $columnName = chr(65 + $mod) . $columnName;
            $columnIndex = (int)(($columnIndex - $mod) / 26);
        }
        return $columnName;
    }

    public function reportExcel($id_bagian, $id_batch, $id_jobrole)
    {
        $id_bagian = (int) $id_bagian;
        $id_batch = (int) $id_batch;
        $id_jobrole = (int) $id_jobrole;

        // Ambil data penilaian
        $penilaian = $this->penilaianmodel->getPenilaianByIdBagian($id_bagian, $id_batch, $id_jobrole);

        // groub by shift
        $shift = $penilaian[0]['shift'];
        // dd($shift);
        // ambil data penilaian groupby bagian
        $bagian = $this->penilaianmodel->getPenilaian();

        // Ambil data job_roles dari database
        $job_roles = $this->jobrolemodel->getJobRolesByJobRoleId($id_jobrole);

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
                foreach ($data['jobdesc'] as $index => $jobdesc) {
                    $keterangan = $data['keterangan'][$index];
                    $groupedData[$id_jobrole][$keterangan][] = $jobdesc;
                }
            }
        }

        // Mapping bobot_nilai
        $bobot_nilai_map = [
            1 => 15,
            2 => 30,
            3 => 45,
            4 => 60,
            5 => 85,
            6 => 100
        ];

        // Membuat file Excel
        $filename = 'Penilaian-' . date('m-d-Y') . '.xlsx';
        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Header utama
        $sheet->mergeCells('A1:Z1');
        if ($bagian[0]['area_utama'] == 'KK1') {
            $sheet->setCellValue('A1', 'AREA KK1');
        } else {
            $sheet->setCellValue('A1', 'AREA KK2');
        }

        // Header kolom utama
        $headers = ['NO', 'KODE KARTU BARU', 'NAMA LENGKAP', 'L/P', 'TGL. MASUK KERJA', 'BAGIAN', 'BEFORE'];
        $startColumn = 'A'; // Kolom awal

        // Tambahkan kolom header berdasarkan jobdesc
        $verticalStartColumn = chr(ord($startColumn) + count($headers)); // Kolom pertama untuk jobdesc
        foreach ($groupedData[$id_jobrole] as $category => $tasks) {
            foreach ($tasks as $task) {
                $headers[] = $task; // Menambahkan jobdesc ke header
            }
        }
        $totalTasks = count(array_merge(...array_values($groupedData[$id_jobrole])));

        // Tambahkan kolom tambahan untuk Rata-Rata, Total, Grade, Skor
        array_push($headers, 'RATA-RATA', 'TOTAL', 'GRADE', 'SKOR');

        // Tulis header ke Excel
        $sheet->fromArray($headers, null, 'A3');

        // Format hanya jobdesc agar tampil vertikal
        $verticalStartIndex = ord($verticalStartColumn) - 64; // Mengubah huruf kolom awal ke indeks (A=1, B=2, ...)
        $verticalEndIndex = $verticalStartIndex + $totalTasks - 1; // Indeks kolom akhir
        $verticalEndColumn = $this->getColumnName($verticalEndIndex); // Mengubah indeks ke huruf kolom
        $verticalHeaderRange = $verticalStartColumn . '3:' . $verticalEndColumn . '3';


        // Terapkan rotasi teks untuk jobdesc agar vertikal
        $sheet->getStyle($verticalHeaderRange)->getAlignment()->setTextRotation(90);
        $sheet->getStyle($verticalHeaderRange)->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);
        // terapkan text wrap agar teks jobdesc bisa tampil semua
        $sheet->getStyle($verticalHeaderRange)->getAlignment()->setWrapText(true);
        // terapkan agar text jobdesc rata tengah
        $sheet->getStyle($verticalHeaderRange)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

        // Format kolom tambahan (Rata-rata, Total, Grade, Skor)
        $extraHeadersStartColumn = chr(ord($verticalEndColumn) + 3);
        // dd($extraHeadersStartColumn);
        $tempheader = $this->getColumnName(ord($extraHeadersStartColumn) + 4);
        // dd ($tempheader);
        $extraHeadersRange = $extraHeadersStartColumn . '3:' . $tempheader . '3';

        // Format untuk semua header (center align, bold)
        $sheet->getStyle('A3:' . $tempheader . '3')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('A3:' . $tempheader. '3')->getFont()->setBold(true);
        // text rata tengah
        $sheet->getStyle('A3:' . $tempheader. '3')->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);


        $row = 4; // Mulai dari baris ke-4
        $no = 1; // Nomor urut

        // Urutkan data berdasarkan shift
        usort($penilaian, function ($a, $b) {
            return strcmp($a['shift'], $b['shift']);
        });

        $shift = null; // Variabel untuk menyimpan shift sebelumnya

        foreach ($penilaian as $p) {
            // Tambahkan header SHIFT jika berganti shift
            if ($p['shift'] !== $shift) {
                $shift = $p['shift'];
                $sheet->mergeCells('A' . $row . ':F' . $row); // Sesuaikan jumlah kolom akhir jika lebih dari Z
                $sheet->setCellValue('A' . $row, 'SHIFT ' . $shift);
                $sheet->getStyle('A' . $row)->getFont()->setBold(true);
                $sheet->getStyle('A' . $row)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
                $row++;
            }

            // Ambil nilai penilaian
            $bobot_nilai = json_decode($p['bobot_nilai'], true) ?? [];
            // konversi nilai ke bobot_nilai_map
            
            $nilai_total = 0;
            $count_total = 0;

            // Data awal untuk setiap karyawan
            $data = [
                    $no,
                    $p['kode_kartu'],
                    $p['nama_karyawan'],
                    $p['jenis_kelamin'],
                    $p['tgl_masuk'],
                    $p['nama_bagian'],
                    '' // Before kosong
                ];

            // Tambahkan nilai yang sudah dikonversikan ke bobot_nilai_map untuk setiap task berdasarkan groupedData
            foreach ($groupedData[$id_jobrole] as $category => $tasks) {
                foreach ($tasks as $task) {
                    $nilai = $bobot_nilai[$task] ?? 0;
                    $data[] = $nilai;
                    $nilai_total += $nilai;
                    $count_total++;
                }
            }

            // Perhitungan rata-rata, total, grade, skor
            // rata-rata = nilai dihitung berdasarkan bobot_nilai_map 
            $bobot_nilai = array_map(function ($nilai) use ($bobot_nilai_map) {
                return $bobot_nilai_map[$nilai] ?? 0;
            }, $bobot_nilai);

            $rata_rata = array_sum($bobot_nilai) / count($bobot_nilai);
            $index_nilai = $p['index_nilai'];
            $skor = $this->calculateSkor($index_nilai);

            // Tambahkan data hasil perhitungan
            array_push($data, round($rata_rata, 2), $nilai_total, $index_nilai, $skor);

            // Tulis data ke baris Excel
            $sheet->fromArray($data, null, 'A' . $row);

            // Format border untuk setiap baris
            $sheet->getStyle('A' . $row . ':' . $tempheader . $row) // Sesuaikan jumlah kolom
            ->getBorders()
            ->getAllBorders()
            ->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);

            // Tinggi baris otomatis
            $sheet->getRowDimension($row)->setRowHeight(-1);

            $row++; // Pindah ke baris berikutnya
            $no++; // Increment nomor urut
        }

        $row++; // Baris kosong sebelum menulis total karyawan

        // Menambahkan total karyawan
        $sheet->mergeCells('A' . $row . ':B' . $row); // Kolom dari A sampai F
        $sheet->setCellValue('A' . $row, 'TOTAL KARYAWAN');
        $sheet->mergeCells('C'. $row . ':F' . $row);
        $sheet->setCellValue('C' . $row, $no - 1); // Jumlah total karyawan
        $sheet->getStyle('A' . $row . ':C' . $row)->getFont()->setBold(true);
        $sheet->getStyle('A' . $row . ':C' . $row)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

        // Format border untuk baris total
        $sheet->getStyle('A' . $row . ':' . $tempheader . $row) // Sesuaikan jumlah kolom
        ->getBorders()
        ->getAllBorders()
        ->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);

        // Format tabel (judul dan isi)
        $sheet->getStyle('A3:' . chr(ord($extraHeadersStartColumn) + 3) . $row) // Sesuaikan jumlah kolom
        ->getAlignment()
        ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER)
        ->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);

        $sheet->getStyle('A1:' . chr(ord($extraHeadersStartColumn) + 3) . '1') // Sesuaikan jumlah kolom
        ->getFont()
        ->setBold(true);

        // Simpan file Excel
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');

        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        $writer->save('php://output');
        exit();
    }
}
