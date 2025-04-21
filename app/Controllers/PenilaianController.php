<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\BagianModel;
use App\Models\JobroleModel;
use App\Models\PenilaianModel;
use App\Models\BatchModel;
use App\Models\KaryawanModel;
use App\Models\PeriodeModel;
use App\Models\AbsenModel;
use App\Models\SummaryJarumModel;
use App\Models\SummaryRossoModel;
use App\Models\BsmcModel;
use CodeIgniter\HTTP\ResponseInterface;
use PhpOffice\PhpSpreadsheet\Calculation\DateTimeExcel\Month;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Cell\DataValidation;
use PhpOffice\PhpSpreadsheet\Cell\Hyperlink;
use PhpOffice\PhpSpreadsheet\Cell\ValueBinder;
use PhpOffice\PhpSpreadsheet\Cell\Cell;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\Cell\DefaultValueBinder;
use DateTime;



class PenilaianController extends BaseController
{
    protected $penilaianmodel;
    protected $jobrolemodel;
    protected $bagianmodel;
    protected $batchmodel;
    protected $karyawanmodel;
    protected $periodeModel;
    protected $absenmodel;
    protected $jarumModel;
    protected $rossoModel;
    protected $bsmcModel;
    protected $db;

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

    // fungsi untuk mengubah index_nilai ke tempSkor
    private function indexToSkor($index_nilai)
    {
        $map = ['A' => 4, 'B' => 3, 'C' => 2, 'D' => 1];
        return $map[$index_nilai] ?? 0;
    }


    public function __construct()
    {
        $this->db = \Config\Database::connect();
        $this->penilaianmodel = new PenilaianModel();
        $this->jobrolemodel = new JobroleModel();
        $this->bagianmodel = new BagianModel();
        $this->batchmodel = new BatchModel();
        $this->karyawanmodel = new KaryawanModel();
        $this->periodeModel = new PeriodeModel();
        $this->absenmodel = new AbsenModel();
        $this->jarumModel = new SummaryJarumModel();
        $this->rossoModel = new SummaryRossoModel();
        $this->bsmcModel = new BsmcModel();
    }

    // public function getAreaUtama()
    // {
    //     if ($this->request->isAJAX()) {
    //         $nama_bagian = $this->request->getPost('nama_bagian');
    //         // group by area_utama
    //         $areaUtama = $this->bagianmodel
    //             ->select('area_utama')
    //             ->where('nama_bagian', $nama_bagian)
    //             ->groupBy('area_utama')
    //             ->findAll();

    //         // Debug: Pastikan query berhasil
    //         // dd($areaUtama);

    //         return $this->response->setJSON($areaUtama);
    //     }

    //     return $this->response->setStatusCode(404);
    // }

    // public function getArea()
    // {
    //     if ($this->request->isAJAX()) {
    //         $area_utama = $this->request->getPost('area_utama');
    //         $nama_bagian = $this->request->getPost('nama_bagian');

    //         $areaData = $this->bagianmodel
    //             ->where('area_utama', $area_utama)
    //             ->where('nama_bagian', $nama_bagian)
    //             ->findAll();

    //         return $this->response->setJSON($areaData);
    //     }

    //     return $this->response->setStatusCode(404);
    // }

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

    // public function cekPenilaian()
    // {
    //     $shift = $this->request->getPost('shift');
    //     $bulan = $this->request->getPost('bulan');
    //     $tahun = $this->request->getPost('tahun');
    //     // dd($shift, $bulan, $tahun);
    //     $id_batch = $this->batchmodel->getIdBatch($shift, $bulan, $tahun);
    //     // dd($id_batch);
    //     $nama_bagian = $this->request->getPost('nama_bagian');
    //     $area_utama = $this->request->getPost('area_utama');
    //     $area = $this->request->getPost('area');

    //     $id_bagian = $this->bagianmodel->getIdBagian($nama_bagian, $area_utama, $area);
    //     // dd($id_bagian);

    //     $id_jobrole = $this->jobrolemodel->getIdJobrole($id_bagian['id_bagian']);
    //     // dd($id_jobrole);

    //     $karyawan_id = 1; // Dummy data
    //     // dd($karyawan_id);

    //     $id_user = 1; // Dummy data

    //     $datauntukinputnilai = [
    //         'id_batch' => $id_batch['id_batch'],
    //         'id_jobrole' => $id_jobrole['id_jobrole'],
    //         'id_karyawan' => $karyawan_id,
    //         'id_user' => $id_user
    //     ];

    //     $json = json_encode($datauntukinputnilai);

    //     return view('Penilaian/create', compact('json'));
    // }

    public function index() {}

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
        $role = $this->request->getPost('role');

        $area = null;
        if ($area == 'null') {
            $area = null;
        }

        if ($area != session()->get('area')) {
            return redirect()->back()->with('error', 'Pilih Sesuai' . session()->get('area') . 'Anda!');
        } elseif ($role == 'Monitoring') {
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
            'karyawanCount' => $karyawanCount,
            'temp' => $temp,
            'jobdescWithKet' => $jobdescWithKet // Kirim data gabungan ke view
        ];

        // dd ($data);
        $this->db->getLastQuery();
        return view('Penilaian/create', $data);
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
        $judul = $this->penilaianmodel->getPenilaianTitle($id_bagian, $id_periode, $id_jobrole);
        // dd ($judul);
        $id_karyawan = 308;
        $id_periode_sekarang = 2;
        // dd($this->penilaianmodel->getPreviousIndexNilai($id_karyawan, $id_periode_sekarang));
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
            'bobotNilai' => $bobotNilai,
            'judul' => $judul[0]['nama_bagian'] . ' - ' . $judul[0]['area_utama'] . ' - ' . $judul[0]['area'] . ' - ' . $judul[0]['nama_batch'] . ' Periode ' . $judul[0]['nama_periode'] . ' (' . $judul[0]['start_date'] . ' s/d ' . $judul[0]['end_date'] . ')'
        ];

        // dd ($title);
        return view('Penilaian/show', $data);
    }
    public function penilaianPerArea($area_utama)
    {
        $area_utama = urldecode($area_utama);
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

        return view('Penilaian/reportareaperarea', $data);
    }
    public function penilaianPerPeriode($area_utama, $id_periode)
    {
        $area_utama = urldecode($area_utama);
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

        return view('Penilaian/reportareaperperiode', $data);
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

    public function reportExcel($area_utama, $nama_batch, $nama_periode)
    {
        $penilaian = $this->penilaianmodel->getPenilaianByAreaByNamaBatchByNamaPeriode($area_utama, $nama_batch, $nama_periode);

        // Kelompokkan data berdasarkan id_bagian dan jobrole
        $penilaianByGroup = $this->groupByBagianAndJobrole($penilaian);

        $spreadsheet = new Spreadsheet();

        foreach ($penilaianByGroup as $groupKey => $penilaianGroup) {
            // Buat sheet baru untuk setiap grup
            $sheet = $spreadsheet->createSheet();
            $sheet->setTitle($groupKey);

            // Hitung panjang kolom
            $id_jobrole = $penilaianGroup[0]['id_jobrole'];
            $jobdesc = $this->getJobDesc($id_jobrole);
            $jobdescCount = count($jobdesc);
            $totalColumns = 7 + $jobdescCount + 2;
            $lastColumn = Coordinate::stringFromColumnIndex($totalColumns);

            // Header utama
            $sheet->mergeCells("A1:{$lastColumn}2");
            $sheet->setCellValue('A1', 'LAPORAN PENILAIAN MANDOR BAGIAN ' . $penilaianGroup[0]['nama_bagian'] . ' AREA ' . $penilaianGroup[0]['area_utama'] . '-' . $penilaianGroup[0]['area']);
            $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);

            $row = 3;

            // Header Kolom
            $this->setColumnHeaders($sheet, $row);
            $this->setJobDescHeaders($sheet, $jobdesc, 7, $row);
            $this->setAdditionalHeaders($sheet, 7, $jobdescCount, $row);

            $row++; // Mulai baris data setelah header
            $no = 1;

            foreach ($penilaianGroup as $p) {
                $row = $this->setRowData($sheet, $p, $row, $no++, 7, $jobdescCount);
            }

            // Auto-size columns
            foreach (range('A', $lastColumn) as $col) {
                $sheet->getColumnDimension($col)->setAutoSize(true);
            }
        }

        // Hapus sheet kosong pertama yang dibuat secara default
        $spreadsheet->removeSheetByIndex(0);

        // Output spreadsheet
        $this->outputExcel($spreadsheet);
    }

    private function groupByBagianAndJobrole(array $penilaian): array
    {
        $penilaianByGroup = [];

        foreach ($penilaian as $p) {
            $groupKey = $p['id_bagian'] . '-' . $p['id_jobrole'];
            $penilaianByGroup[$groupKey][] = $p;
        }

        return $penilaianByGroup;
    }

    private function setFullBorder($sheet, string $range)
    {
        // $sheet->getStyle($range)->applyFromArray([
        //     'borders' => [
        //         'allBorders' => [
        //             'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
        //             'color' => ['argb' => '000000'], // Warna hitam
        //         ],
        //     ],
        // ]);
    }

    private function setStyles($sheet, string $startCell, string $endCell)
    {
        $sheet->getStyle("$startCell:$endCell")->applyFromArray([
            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                    'color' => ['argb' => '000000'],
                ],
            ],

            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
            ],

            'font' => [
                'size' => 12,
            ],

        ]);

        // Set auto-size for columns
        $sheet->getColumnDimension('A')->setWidth(5); // NO
        $sheet->getColumnDimension('B')->setWidth(15); // KODE KARTU
        $sheet->getColumnDimension('C')->setWidth(25); // NAMA KARYAWAN
        $sheet->getColumnDimension('D')->setWidth(5); // L/P
        $sheet->getColumnDimension('E')->setWidth(10); // TGL. MASUK KERJA
        $sheet->getColumnDimension('F')->setWidth(15); // BAGIAN
        $sheet->getColumnDimension('G')->setWidth(10); // BEFORE

        // Set auto-size for jobdesc columns
        $jobdescStartCol = Coordinate::columnIndexFromString('H');
        $jobdescEndCol = Coordinate::columnIndexFromString('H') + count($this->getJobDesc(1)) - 1;
        // dd (Coordinate::columnIndexFromString('H'), count($this->getJobDesc(1)), $jobdescEndCol, $this->getJobDesc(1));
        $jobdescEndCol = $this->getColumnName($jobdescEndCol);
        $sheet->getColumnDimension('H')->setWidth(5); // JOBDESC
        $sheet->getColumnDimension($jobdescEndCol)->setWidth(5); // JOBDESC

    }

    private function groupByShift(array $penilaian): array
    {
        $penilaianByShift = [];
        // sort by shift
        foreach ($penilaian as $p) {
            $penilaianByShift[$p['shift']][] = $p;
        }
        // Sort groups by shift key in ascending order
        ksort($penilaianByShift);

        return $penilaianByShift;
    }

    private function setColumnHeaders($sheet, int $row)
    {
        $sheet->setCellValue("A{$row}", 'NO');
        $sheet->setCellValue("B{$row}", 'KODE KARTU');
        $sheet->setCellValue("C{$row}", 'NAMA KARYAWAN');
        $sheet->setCellValue("D{$row}", 'L/P');
        $sheet->setCellValue("E{$row}", 'TGL. MASUK KERJA');
        $sheet->setCellValue("F{$row}", 'BAGIAN');
        $sheet->setCellValue("G{$row}", 'BEFORE');

        // Set styles for header columns
        $this->setStyles($sheet, 'A' . $row, 'G' . $row);
    }

    private function getJobDesc(int $id_jobrole): array
    {
        // Ambil data dari database menggunakan model
        $model = new JobRoleModel();
        $jobRoleData = $model->getJobRolesByJobRoleId($id_jobrole);

        if (isset($jobRoleData['jobdesc'])) {
            return json_decode($jobRoleData['jobdesc'], true);
        }

        return []; // Kembalikan array kosong jika tidak ada data
    }


    private function setJobDescHeaders($sheet, array $jobdesc, int $jobdescStartCol, int $row)
    {
        foreach ($jobdesc as $index => $desc) {
            $colLetter = Coordinate::stringFromColumnIndex($jobdescStartCol + $index + 1);
            $sheet->setCellValue($colLetter . $row, $desc);

            // Set text orientation menjadi vertikal
            $sheet->getStyle($colLetter . $row)->getAlignment()
                ->setTextRotation(90) // Rotasi teks 90 derajat
                ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER)
                ->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER)
                ->setWrapText(true); // Aktifkan wrap text
        }

        // Tambahkan border pada header jobdesc
        $lastJobdescCol = Coordinate::stringFromColumnIndex($jobdescStartCol + count($jobdesc) - 1);
        // dd ($lastJobdescCol);
        // $this->setStyles($sheet, "{$colLetter}{$row}", "$lastJobdescCol{$row}");
    }



    private function setAdditionalHeaders($sheet, int $jobdescStartCol, int $jobdescCount, int $row): void
    {
        $gradeCol = Coordinate::stringFromColumnIndex($jobdescStartCol + $jobdescCount + 1);
        $sheet->setCellValue("{$gradeCol}{$row}", 'GRADE');

        $skorCol = Coordinate::stringFromColumnIndex($jobdescStartCol + $jobdescCount + 2);
        $sheet->setCellValue("{$skorCol}{$row}", 'SKOR');

        // Tambahkan header ABSEN
        $absenStartCol = $jobdescStartCol + $jobdescCount + 4;
        $indexAbsen = Coordinate::stringFromColumnIndex($absenStartCol);
        $sheet->setCellValue(Coordinate::stringFromColumnIndex($absenStartCol) . "$row", 'SI');
        $sheet->setCellValue(Coordinate::stringFromColumnIndex($absenStartCol + 1) . "$row", 'MI');
        $sheet->setCellValue(Coordinate::stringFromColumnIndex($absenStartCol + 2) . "$row", 'M');
        // style M background warna merah
        $sheet->getStyle(Coordinate::stringFromColumnIndex($absenStartCol + 2) . "$row")->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('FFFF0000');

        $sheet->setCellValue(Coordinate::stringFromColumnIndex($absenStartCol + 3) . "$row", 'TOTAL');
        $sheet->setCellValue(Coordinate::stringFromColumnIndex($absenStartCol + 4) . "$row", 'KEHADIRAN');
        $sheet->setCellValue(Coordinate::stringFromColumnIndex($absenStartCol + 5) . "$row", 'ACCUMULASI');

        // Tambahkan header HASIL AKHIR, GRADE, dan TRACKING
        $resultStartCol = $absenStartCol + 6;
        $sheet->setCellValue(Coordinate::stringFromColumnIndex($resultStartCol) . "$row", 'HASIL AKHIR');
        $sheet->setCellValue(Coordinate::stringFromColumnIndex($resultStartCol + 1) . "$row", 'GRADE');
        $sheet->setCellValue(Coordinate::stringFromColumnIndex($resultStartCol + 2) . "$row", 'TRACKING');

        // Tambahkan border pada header tambahan
        $lastAdditionalCol = Coordinate::stringFromColumnIndex($resultStartCol + 2);
        // dd ($indexAbsen, $lastAdditionalCol);

        $this->setStyles($sheet, "{$indexAbsen}{$row}", "$lastAdditionalCol{$row}");
        $sheet->getStyle("{$indexAbsen}{$row}:{$lastAdditionalCol}{$row}")->getAlignment()->setWrapText(true);
    }

    private function setRowData($sheet, array $p, int $row, int $no, int $jobdescStartCol, int $jobdescCount): int
    {
        // Set static columns
        $sheet->setCellValue("A{$row}", $no);
        $sheet->setCellValue("B{$row}", $p['kode_kartu']);
        $sheet->setCellValue("C{$row}", $p['nama_karyawan']);
        $sheet->setCellValue("D{$row}", $p['jenis_kelamin']);
        $sheet->setCellValue("E{$row}", $p['tgl_masuk']);
        $sheet->setCellValue("F{$row}", $p['nama_bagian']); // Perbaiki duplikasi
        $sheet->setCellValue("G{$row}", 0); // Pindahkan kolom kedua ke G

        // Decode and calculate scores
        $nilai = json_decode($p['bobot_nilai'] ?? '[]', true);
        $totalNilai = 0;
        $totalBobot = 0;

        if (is_array($nilai) && count($nilai) > 0) {
            foreach ($nilai as $value) {
                $totalNilai += $value;
                $totalBobot += self::bobot_nilai[$value] ?? 0; // Pastikan nilai default jika key tidak ditemukan
            }

            $average = $totalBobot / count($nilai);
            $grade = $p['index_nilai'] ?? '-'; // Default grade jika tidak ada
            $skor = $this->calculateSkor($grade);

            // Set job description and additional columns
            $colIndex = $jobdescStartCol + 1;
            foreach ($nilai as $value) {
                $colLetter = Coordinate::stringFromColumnIndex($colIndex++);
                $sheet->setCellValue("{$colLetter}{$row}", $value);
            }

            // Set grade and score columns
            $colLetter = Coordinate::stringFromColumnIndex($colIndex++);
            $sheet->setCellValue("{$colLetter}{$row}", $grade);

            $colLetter = Coordinate::stringFromColumnIndex($colIndex);
            $sheet->setCellValue("{$colLetter}{$row}", $skor);
        }
        // Data Kehadiran
        // dd($absen);
        $sakit = $p['sakit'] ?? 0;
        $izin = $p['izin'] ?? 0;
        $mangkir = $p['mangkir'] ?? 0;
        // sakit * 1, izin * 2, mangkir * 3
        $totalAbsen = ($sakit * 1) + ($izin * 2) + ($mangkir * 3);
        $kehadiran = 100 - $totalAbsen;
        // =IF(BW9<0.94,"-1",IF(BW9>0.93,"0"))
        $accumulasi = $kehadiran < 94 ? -1 : 0;

        // hasil akhir = skor + accumulasi
        $hasil_akhir = $skor + $accumulasi;
        $grade_akhir = $this->calculateGradeBatch($hasil_akhir);
        // dd ($grade_akhir);
        $trakcing = $grade . $grade_akhir;
        // dd ($sakit);
        $absenStartCol = $jobdescStartCol + $jobdescCount + 4;
        $sheet->setCellValue(Coordinate::stringFromColumnIndex($absenStartCol) . $row, $sakit);
        $sheet->setCellValue(Coordinate::stringFromColumnIndex($absenStartCol + 1) . $row, $izin);
        $sheet->setCellValue(Coordinate::stringFromColumnIndex($absenStartCol + 2) . $row, $mangkir);

        // Data Kehadiran 
        $absenStartCol = $jobdescStartCol + $jobdescCount + 7;
        $sheet->setCellValue(Coordinate::stringFromColumnIndex($absenStartCol) . $row, $totalAbsen);
        $sheet->setCellValue(Coordinate::stringFromColumnIndex($absenStartCol + 1) . $row, $kehadiran);
        $sheet->setCellValue(Coordinate::stringFromColumnIndex($absenStartCol + 2) . $row, $accumulasi);

        // Data Hasil Akhir 

        $resultStartCol = $absenStartCol + 3;
        $sheet->setCellValue(Coordinate::stringFromColumnIndex($resultStartCol) . $row, $hasil_akhir);
        $sheet->setCellValue(Coordinate::stringFromColumnIndex($resultStartCol + 1) . $row, $grade_akhir);
        $sheet->setCellValue(Coordinate::stringFromColumnIndex($resultStartCol + 2) . $row, $trakcing);

        // Set styles for data columns
        $this->setStyles($sheet, "A{$row}", Coordinate::stringFromColumnIndex($resultStartCol + 2) . $row);


        return $row + 1; // Pindah ke baris berikutnya
    }


    private function outputExcel($spreadsheet)
    {
        $filename = 'Laporan Penilaian Mandor ' . date('m-d-y') . '.xlsx';

        // Redirect output to a client’s web browser (Xlsx)
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');

        $writer = new Xlsx($spreadsheet);
        $writer->save('php://output');
        exit();
    }

    public function reportAreaperBatch($area_utama)
    {
        if ($area_utama === 'all') {
            $batch = $this->penilaianmodel->getPenilaianGroupByBatchAllArea();

            // Tambahkan informasi bahwa ini dari all area
            foreach ($batch as &$b) {
                $b['area_utama'] = 'all';
            }
        } else {
            $batch = $this->penilaianmodel->getPenilaianWhereAreautamaGroupByBatch($area_utama);
        }

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
            'reportbatch' => $batch
        ];

        return view('Penilaian/reportareaperbatch', $data);
    }

    public function exelReportBatch($id_batch, $area_utama)
    {
        $id_batch = (int)$id_batch;

        // Ambil data karyawan sesuai area
        if ($area_utama === 'all') {
            $reportbatch = $this->penilaianmodel->getPenilaianByBatchAllArea($id_batch);
        } else {
            $reportbatch = $this->penilaianmodel->getPenilaianGroupByBatchAndAreaByIdBatch($id_batch, $area_utama);
        }
        $resultTop7ProdOp = [];
        $resultTop3BsOp = [];
        $resultTop7ProdRos = [];
        $resultTop3BsRos = [];
        $resultTop3UsedNeedle = [];

        foreach ($reportbatch as $areaData) {
            $area = $areaData['area'];
            // Panggil fungsi model getTop3ProduksiOperator berdasarkan area (bukan area_utama)
            $top7Prod = $this->bsmcModel->getTopProduksiOperator($area, $id_batch, 7);
            // Dari 7 karyawan tadi, cari Top-3 BS_MC
            $ids = array_column($top7Prod, 'id_karyawan');
            $top3Bs = $this->bsmcModel->getTop3BsMcFromList($ids, $id_batch);

            // Top 7 Produksi Rosso
            $areaRosso = 'ROSSO' . $area_utama;
            $top7ProdRos = $this->rossoModel->getTopProduksiRosso($areaRosso, $id_batch, 7);
            $id_bs_rosso = array_column($top7ProdRos, 'id_karyawan');
            $top3BsRos = $this->rossoModel->getTop3BsRossoFromList($id_bs_rosso, $id_batch);
            //Used Needle
            $top3UsedNeedle = $this->jarumModel->getTopUsedNeedle($area, $id_batch, 3);

            $resultTop7ProdOp[$area] = $top7Prod;
            $resultTop3BsOp[$area] = $top3Bs;
            $resultTop7ProdRos[$areaRosso] = $top7ProdRos;
            $resultTop3BsRos[$areaRosso] = $top3BsRos;
            $resultTop3UsedNeedle[$area] = $top3UsedNeedle;
        }

        if ($area_utama === 'all') {
            // ambil list uniq area_utama dari reportbatch
            $areaUtamaList = array_unique(array_column($reportbatch, 'area_utama'));
        } else {
            $areaUtamaList = [$area_utama];
        }

        foreach ($areaUtamaList as $au) {
            $keyRosso = 'ROSSO' . $au;
            // Top 7 Produksi Rosso per ROSSOKKx
            $t7r = $this->rossoModel->getTopProduksiRosso($keyRosso, $id_batch, 7);
            $idsR = array_column($t7r, 'id_karyawan');
            $b3r = $this->rossoModel->getTop3BsRossoFromList($idsR, $id_batch);

            $resultTop7ProdRos[$keyRosso] = $t7r;
            $resultTop3BsRos[$keyRosso] = $b3r;
        }
        // dd($resultTop3BsRos);
        // Anda bisa mengirimkan $result ke view atau melakukan dd($result) untuk debugging
        $namaBulan = array_map(function ($angka) {
            return date('M', mktime(0, 0, 0, $angka, 10));
        }, array_map(function ($item) {
            return (int)$item['bulan'];
        }, $this->penilaianmodel->getBatchGroupByBulanPenilaianRev($id_batch)));

        // Ambil data batch
        $nama_batch = $this->batchmodel->find($id_batch);

        // Kumpulan data semua karyawan (tanpa pengelompokan dulu)
        $allData = [];

        foreach ($reportbatch as $data) {
            // Ambil nilai per periode untuk tiap karyawan dari model
            $nilaiBulanan = $this->penilaianmodel->getPenilaianGroupByBulan(
                $data['id_karyawan'],
                $id_batch,
                $data['id_jobrole']
            );

            // Inisialisasi array dengan key berupa nama bulan (dari $namaBulan) dengan default '-'
            $nilaiPerPeriode = array_fill_keys($namaBulan, '-');

            // Proses tiap nilai yang ada di model
            foreach ($nilaiBulanan as $nilai) {
                // Konversi nilai field 'nama_periode' (diharapkan berupa int) ke nama bulan (misalnya 1 menjadi "Jan")
                $periodeKey = date('M', mktime(0, 0, 0, (int)$nilai['bulan'], 10));
                if (in_array($periodeKey, $namaBulan)) {
                    // Konversi grade huruf ke angka
                    $nilaiPerPeriode[$periodeKey] = $this->convertHurufToAngka($nilai['grade_akhir']);
                }
            }

            // Simpan array nilai per periode (nama bulan) ke data karyawan
            $data['nilai_bulanan'] = $nilaiPerPeriode;

            // rata-rata = (nilai_Bulan1 + nilai_Bulan2 + nilai_Bulan3 + POINT OBSERVASI(default 1)) / 4
            $total = 0;
            foreach ($namaBulan as $periode) {
                $nilai = is_numeric($nilaiPerPeriode[$periode]) ? $nilaiPerPeriode[$periode] : 0;
                $total += $nilai;
            }
            // Tambahkan nilai POINT OBSERVASI default (1)
            $total += 1;
            $rataRataCell = $total / 4;

            // Tentukan grade berdasarkan rata-rata yang dihitung
            $gradeCell = $this->calculateGradeBatch($rataRataCell);

            // b) Tentukan rankingFound untuk Prod
            $prodOk = false;
            if (strtolower($data['nama_bagian']) === 'operator') {
                foreach ($resultTop7ProdOp[$data['area']] as $r => $rowP) {
                    if ($r < 3 && $rowP['id_karyawan'] === $data['id_karyawan']) {
                        $prodRank = $r + 1;
                        $prodOk = true;
                        break;
                    }
                }
            } elseif (strpos($data['area'], 'ROSSO') === 0) {
                $rossoArea = $data['area'];              // <-- langsung pakai data['area']
                if (isset($resultTop7ProdRos[$rossoArea])) {
                    foreach ($resultTop7ProdRos[$rossoArea] as $r => $rowR) {
                        if ($r < 3 && $rowR['id_karyawan'] === $data['id_karyawan']) {
                            $prodRank = $r + 1;
                            $prodOk   = true;
                            break;
                        }
                    }
                }
            }
            if (!($prodOk ?? false)) $prodRank = null;

            // c) Ranking BS
            $bsOk = false;
            if (strtolower($data['nama_bagian']) === 'operator') {
                foreach ($resultTop3BsOp[$data['area']] as $r => $rowB) {
                    if ($r < 3 && $rowB['id_karyawan'] === $data['id_karyawan']) {
                        $bsRank = $r + 1;
                        $bsOk = true;
                        break;
                    }
                }
            } elseif (strpos($data['area'], 'ROSSO') === 0) {
                $rossoArea = $data['area'];
                if (isset($resultTop3BsRos[$rossoArea])) {
                    foreach ($resultTop3BsRos[$rossoArea] as $r => $rowBR) {
                        if ($r < 3 && $rowBR['id_karyawan'] === $data['id_karyawan']) {
                            $bsRank = $r + 1;
                            $bsOk   = true;
                            break;
                        }
                    }
                }
            }
            if (!($bsOk ?? false)) $bsRank = null;

            // d) Ranking Used Needle (Montir)
            $needleOk = false;
            if (strtolower($data['nama_bagian']) === 'montir') {
                foreach ($resultTop3UsedNeedle[$data['area']] as $r => $rowN) {
                    if ($r < 3 && $rowN['id_karyawan'] === $data['id_karyawan']) {
                        $needleRank = $r + 1;
                        $needleOk = true;
                        break;
                    }
                }
            }
            if (!($needleOk ?? false)) $needleRank = null;

            // e) Hitung bonus point: +1 tiap TRUE prodOk/bsOk/needleOk
            $bonus = 0;
            $bonus += $prodOk ? 1 : 0;
            $bonus += $bsOk   ? 1 : 0;
            $bonus += $needleOk ? 1 : 0;

            $pointAkhir = $rataRataCell + $bonus;
            $gradeAkhir = $this->calculateGradeBatch($pointAkhir);

            // f) Tambahkan ke allData
            $allData[] = [
                'id_karyawan'   => $data['id_karyawan'],
                'kode_kartu'    => $data['kode_kartu'],
                'nama_karyawan' => $data['nama_karyawan'],
                'jenis_kelamin' => $data['jenis_kelamin'],
                'tgl_masuk'     => $data['tgl_masuk'],
                'nilai_bulanan' => $nilaiPerPeriode,
                'nama_bagian'   => $data['nama_bagian'],
                'area'          => $data['area'],
                'area_utama'    => $data['area_utama'],
                'rata_rata'     => $rataRataCell,
                'grade_awal'    => $gradeCell,
                'rank_prod'     => $prodRank,
                'rank_bs'       => $bsRank,
                'rank_needle'   => $needleRank,
                'point_akhir'   => $pointAkhir,
                'grade_akhir'   => $gradeAkhir,
            ];
        }

        // Kelompokkan data berdasarkan grade akhir
        $grouped = ['A' => [], 'B' => [], 'C' => [], 'D' => []];
        foreach ($allData as $item) {
            $g = $item['grade_akhir'];
            $grouped[$g][] = $item;
        }

        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheetIndex = 0;

        foreach ($grouped as $grade => $dataGroup) {

            $dataGroup = $grouped[$grade];
            if (empty($dataGroup)) continue;
            $sheet = $sheetIndex === 0 ? $spreadsheet->getActiveSheet() : $spreadsheet->createSheet($sheetIndex);
            $sheet->setTitle("Grade $grade");
            $sheetIndex++;

            // Judul Laporan (baris 1)
            $judul = "REPORT PENILAIAN $area_utama - " . $nama_batch['nama_batch'];
            $sheet->mergeCells('A1:L1');  // Kolom A sampai L (12 kolom)
            $sheet->setCellValue('A1', $judul);
            $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
            $sheet->getStyle('A1')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

            // Header tetap: NO, KODE KARTU, NAMA KARYAWAN, L/P, TGL. MASUK KERJA, BAGIAN (baris 3-4)
            $headers = ['NO', 'KODE KARTU', 'NAMA KARYAWAN', 'L/P', 'TGL. MASUK KERJA', 'BAGIAN'];
            $startColAscii = ord('A');
            foreach ($headers as $i => $header) {
                $col = chr($startColAscii + $i);
                $sheet->mergeCells("{$col}3:{$col}4");
                $sheet->setCellValue("{$col}3", $header);
                $sheet->getStyle("{$col}3")->getAlignment()->setHorizontal('center');
                $sheet->getStyle("{$col}3")->getAlignment()->setVertical('center');
            }

            $col = chr($startColAscii + count($headers));
            $mergeEnd = chr(ord($col) + count($namaBulan) - 1);
            $sheet->mergeCells("{$col}3:{$mergeEnd}3");
            $sheet->setCellValue("{$col}3", 'Bulan');
            $sheet->getStyle("{$col}3")->getAlignment()->setHorizontal('center');
            $sheet->getStyle("{$col}3")->getAlignment()->setVertical('center');
            // Isi sub-header di baris 4 dengan nama-nama bulan
            foreach ($namaBulan as $bulan) {
                $sheet->setCellValue("{$col}4", $bulan);
                $sheet->getStyle("{$col}4")->getAlignment()->setHorizontal('center');
                $col++;
            }

            // POINT OBSERVASI (default 1)
            $sheet->mergeCells("{$col}3:{$col}4");
            $sheet->setCellValue("{$col}3", "POINT OBSERVASI");
            $sheet->getStyle("{$col}3")->getAlignment()->setHorizontal('center');
            $col++;
            // RATA-RATA POINT AREA (hasil perhitungan manual dari nilai periode + observasi)
            $sheet->mergeCells("{$col}3:{$col}4");
            $sheet->setCellValue("{$col}3", "RATA-RATA POINT AREA");
            $sheet->getStyle("{$col}3")->getAlignment()->setHorizontal('center');
            $col++;
            // GRADE (hasil perhitungan manual dari rata-rata)
            $sheet->mergeCells("{$col}3:{$col}4");
            $sheet->setCellValue("{$col}3", "GRADE");
            $sheet->getStyle("{$col}3")->getAlignment()->setHorizontal('center');
            $col++;

            // RANKING: PROD, BS, USED NEEDLE
            $colRankingStart = $col;
            $sheet->mergeCells("{$colRankingStart}3:" . chr(ord($colRankingStart) + 2) . "3");
            $sheet->setCellValue("{$colRankingStart}3", "RANKING");
            $sheet->getStyle("{$colRankingStart}3")->getAlignment()->setHorizontal('center');
            $sheet->getStyle("{$colRankingStart}3")->getAlignment()->setVertical('center');

            // Sub-header untuk RANKING
            $subRanking = ['PROD', 'BS', 'USED NEEDLE'];
            foreach ($subRanking as $sub) {
                $sheet->setCellValue("{$col}4", $sub);
                $sheet->getStyle("{$col}4")->getAlignment()->setHorizontal('center');
                $sheet->getStyle("{$col}4")->getAlignment()->setVertical('center');
                $col++;
            }

            //POINT 
            $sheet->mergeCells("{$col}3:{$col}4");
            $sheet->setCellValue("{$col}3", "POINT");
            $sheet->getStyle("{$col}3")->getAlignment()->setHorizontal('center');
            $col++;

            //GRADE AKHIR 
            $sheet->mergeCells("{$col}3:{$col}4");
            $sheet->setCellValue("{$col}3", "GRADE AKHIR");
            $sheet->getStyle("{$col}3")->getAlignment()->setHorizontal('center');
            $col++;

            // Isi data mulai baris 5
            $row = 5;
            $no = 1;

            foreach ($dataGroup as $d) {
                $col = 'A';
                $sheet->setCellValue($col++ . $row, $no++);
                $sheet->setCellValue($col++ . $row, $d['kode_kartu']);
                $sheet->setCellValue($col++ . $row, $d['nama_karyawan']);
                $sheet->setCellValue($col++ . $row, $d['jenis_kelamin']);
                $sheet->setCellValue($col++ . $row, date('d-m-Y', strtotime($d['tgl_masuk'])));
                $sheet->setCellValue($col++ . $row, $d['nama_bagian']);
                $bulanKeys = array_keys($d['nilai_bulanan']); // Contoh: ['Jan', 'Feb', 'Mar']
                foreach ($bulanKeys as $bulan) {
                    $sheet->setCellValue($col++ . $row, $d['nilai_bulanan'][$bulan]);
                }
                $sheet->setCellValue($col++ . $row, 1); // Isi POINT OBSERVASI (default 1)
                $sheet->setCellValue($col++ . $row, $d['rata_rata']);
                $sheet->setCellValue($col++ . $row, $d['grade_awal']);
                $sheet->setCellValue($col++ . $row, $d['rank_prod']   ?? '-');
                $sheet->setCellValue($col++ . $row, $d['rank_bs']     ?? '-');
                $sheet->setCellValue($col++ . $row, $d['rank_needle'] ?? '-');
                $sheet->setCellValue($col++ . $row, $d['point_akhir']);
                $sheet->setCellValue($col++ . $row, $d['grade_akhir']);
                $row++;
            }
            // Tambahkan border untuk seluruh area dari A3 sampai Q baris terakhir
            $lastRow = $row - 1;
            $sheet->getStyle("A3:Q{$lastRow}")->applyFromArray([
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                        'color' => ['argb' => 'FF000000'],
                    ],
                ],
            ]);

            // Auto-size kolom A sampai Q
            foreach (range('A', 'Q') as $columnID) {
                $sheet->getColumnDimension($columnID)->setAutoSize(true);
            }
        }

        // Buat sheet “REPORT GRADE”
        $sheetGrade = $spreadsheet->createSheet();
        $sheetGrade->setTitle('REPORT GRADE');

        $title = "LAPORAN PREMI SKILL MATRIX";
        $sheetGrade->mergeCells('A1:D1');
        $sheetGrade->setCellValue('A1', $title);

        //Header kolom di baris 2
        $sheetGrade->mergeCells('A2:D2');
        $sheetGrade->setCellValue('A2', $nama_batch['nama_batch']);
        $sheetGrade->getStyle('A1:D2')->getFont()->setBold(true)->setSize(14);
        $sheetGrade->getStyle('A1:D2')->getAlignment()
            ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER)
            ->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);

        // b) Header kolom di baris 3
        $sheetGrade->setCellValue('A3', 'Grade');
        $sheetGrade->setCellValue('B3', 'Premi');
        $sheetGrade->setCellValue('C3', 'Jumlah Orang');
        $sheetGrade->setCellValue('D3', 'Total');
        $sheetGrade->getStyle('A3:D3')->getFont()->setBold(true);
        $sheetGrade->getStyle('A3:D3')->getAlignment()
            ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

        // Data premi
        $totalSeluruhOrang = 0;
        $totalSeluruhRupiah = 0;

        // c) Isi data premi
        $premiMap = ['A' => 25000, 'B' => 20000, 'C' => 15000, 'D' => 0];
        $row = 4;
        foreach ($premiMap as $gr => $pr) {
            $jumlahOrang = isset($grouped[$gr]) ? count($grouped[$gr]) : 0;
            $total       = $pr * $jumlahOrang;

            $sheetGrade->setCellValue("A{$row}", $gr);
            $sheetGrade->setCellValue("B{$row}", $pr);
            $sheetGrade->setCellValue("C{$row}", $jumlahOrang);
            $sheetGrade->setCellValue("D{$row}", $total);
            $totalSeluruhOrang += $jumlahOrang;
            $totalSeluruhRupiah += $total;
            $row++;
        }

        // Total baris
        $sheetGrade->mergeCells("A{$row}:B{$row}");
        $sheetGrade->setCellValue("A{$row}", 'TOTAL');
        $sheetGrade->setCellValue("C{$row}", $totalSeluruhOrang);
        $sheetGrade->setCellValue("D{$row}", $totalSeluruhRupiah);

        // Bold untuk baris total
        $sheetGrade->getStyle("A{$row}:D{$row}")->getFont()->setBold(true);
        // Tambahkan border ke baris total
        $sheetGrade->getStyle("A{$row}:D{$row}")->applyFromArray([
            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                ],
            ],
        ]);

        // 6c) Style header (baris 3)
        $sheetGrade->getStyle('A3:D3')->getFont()->setBold(true);
        $sheetGrade->getStyle('A3:D3')
            ->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

        // 6d) Format kolom Premi dan Total sebagai currency
        $sheetGrade->getStyle('B4:B' . ($row - 1))
            ->getNumberFormat()
            ->setFormatCode('"Rp"#,##0');

        $sheetGrade->getStyle('D4:D' . ($row))
            ->getNumberFormat()
            ->setFormatCode('"Rp"#,##0');

        $lastRow = $row - 1;
        $sheetGrade->getStyle("A3:D{$lastRow}") // border header + data premi
            ->applyFromArray([
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                    ],
                ],
            ]);

        // 6e) Autosize kolom
        foreach (['A', 'B', 'C', 'D'] as $col) {
            $sheetGrade->getColumnDimension($col)->setAutoSize(true);
        }

        // Output file Excel
        $areaLabel = ($area_utama === 'all') ? 'ALL_AREA' : strtoupper($area_utama);
        $filename = 'Report Penilaian-' . $areaLabel . '-' . $nama_batch['nama_batch'] . '.xlsx';
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');

        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        $writer->save('php://output');
        exit();
    }

    // Fungsi untuk konversi huruf ke angka
    private function convertHurufToAngka($huruf)
    {
        $konversi = [
            'A' => 4,
            'B' => 3,
            'C' => 2,
            'D' => 1
        ];
        return $konversi[$huruf] ?? 0; // Jika huruf tidak dikenali, kembalikan 0
    }

    // Fungsi untuk menghitung grade
    private function calculateGradeBatch($average)
    {
        if (!is_numeric($average)) return '-';
        if ($average >= 3.5) return 'A';
        if ($average >= 2.5) return 'B';
        if ($average >= 1.5) return 'C';
        return 'D';
    }

    public function excelReportPerPeriode($area_utama, $nama_batch, $nama_periode)
    {
        $reportbatch = $this->penilaianmodel->getPenilaianByAreaByNamaBatchByNamaPeriode($area_utama, $nama_batch, $nama_periode);
        // dd($reportbatch);
        $bulan = $this->periodeModel->getPeriodeByNamaBatchAndNamaPeriode($nama_batch, $nama_periode);

        $uniqueSheets = [];
        foreach ($reportbatch as $item) {
            $key = $item['nama_bagian'];
            if (!in_array($key, $uniqueSheets)) {
                $uniqueSheets[] = $key;
            }
        }
        // dd($uniqueSheets);

        $spreadsheet = new Spreadsheet();
        $spreadsheet->removeSheetByIndex(0); // Hapus sheet default

        foreach ($uniqueSheets as $sheetName) {
            $sheet = $spreadsheet->createSheet();
            $sheet->setTitle(substr($sheetName, 0, 31)); // Nama sheet sesuai area dan bagian

            // Pisahkan area dan nama bagian
            list($currentBagian) = explode(' - ', $sheetName, 2);

            // Filter data untuk sheet ini
            $dataFiltered = array_filter($reportbatch, function ($item) use ($currentBagian) {
                return $item['nama_bagian'] === $currentBagian;
            });
            // dd($dataFiltered);
            // Ambil nama bulan dari end_date
            $namaBulan = isset($bulan['nama_bulan']) ? strtoupper($bulan['nama_bulan']) : '';
            // Kelompokkan berdasarkan shift
            $dataByShift = $this->groupByShift($dataFiltered);

            // Header Utama
            $sheet->mergeCells('A1:G1')->setCellValue('A1', 'REPORT PENILAIAN - ' . strtoupper($sheetName));
            $sheet->mergeCells('A2:G2')->setCellValue('A2', 'DEPARTEMEN KAOS KAKI');
            $sheet->mergeCells('A3:G3')->setCellValue('A3', '(PERIODE ' . $namaBulan . ' ' . strtoupper($nama_batch) . ')');
            $sheet->getStyle('A1:A3')->applyFromArray([
                'font' => ['bold' => true, 'size' => 16, 'name' => 'Times New Roman'],
                'alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER],
            ]);

            // Header Kolom Statis
            $headers = ['NO', 'KODE KARTU', 'NAMA KARYAWAN', 'SHIFT', 'L/P', 'TGL. MASUK KERJA', 'BAGIAN', 'PREVIOUS GRADE'];
            $startCol = 1; // Kolom A
            foreach ($headers as $header) {
                $colLetter = Coordinate::stringFromColumnIndex($startCol);
                $sheet->getStyle($colLetter . '5')->getAlignment()->setWrapText(true);
                $sheet->mergeCells($colLetter . '5:' . $colLetter . '6')->setCellValue($colLetter . '5', $header);
                $sheet->getStyle($colLetter . '5:' . $colLetter . '6')->applyFromArray([
                    'font' => ['bold' => true],
                    'alignment' => [
                        'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                        'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
                    ],
                    'borders' => ['allBorders' => ['borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN]],
                ]);
                $startCol++;
            }

            // Header Dinamis untuk keterangan dan jobdesc
            $currentCol = 9; // Dimulai dari kolom I
            $jobdescGrouped = [];
            foreach ($dataFiltered as $p) {
                $keterangan = json_decode($p['keterangan'], true);
                $jobdesc = json_decode($p['jobdesc'], true);

                if (is_array($keterangan) && is_array($jobdesc)) {
                    foreach ($keterangan as $index => $ket) {
                        $job = $jobdesc[$index] ?? '';
                        $jobdescGrouped[$ket][] = $job;
                    }
                }
            }

            // Hilangkan duplikasi dalam setiap keterangan
            foreach ($jobdescGrouped as $keterangan => &$jobs) {
                $jobs = array_unique($jobs);
            }
            unset($jobs); // Lepaskan referensi

            // Tulis header keterangan dan jobdesc
            foreach ($jobdescGrouped as $keterangan => $jobs) {
                $startColLetter = Coordinate::stringFromColumnIndex($currentCol);
                $endColLetter = Coordinate::stringFromColumnIndex($currentCol + count($jobs) - 1);

                // Header keterangan
                $sheet->mergeCells($startColLetter . '5:' . $endColLetter . '5');
                $sheet->setCellValue($startColLetter . '5', $keterangan);
                $sheet->getStyle($startColLetter . '5:' . $endColLetter . '5')->applyFromArray([
                    'font' => ['bold' => true],
                    'alignment' => [
                        'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                        'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
                    ],
                    'borders' => ['allBorders' => ['borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN]],
                ]);

                // Header jobdesc
                foreach ($jobs as $job) {
                    $sheet->setCellValue(Coordinate::stringFromColumnIndex($currentCol) . '6', $job);
                    $sheet->getStyle(Coordinate::stringFromColumnIndex($currentCol) . '6')->applyFromArray([
                        'font' => ['bold' => true],
                        'alignment' => [
                            'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                            'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
                        ],
                        'borders' => ['allBorders' => ['borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN]],
                    ]);
                    $currentCol++;
                }
            }

            // Header absen
            $sheet->mergeCells(Coordinate::stringFromColumnIndex($currentCol + 2) . '4:' . Coordinate::stringFromColumnIndex($currentCol + 4) . '4');
            $sheet->setCellValue(Coordinate::stringFromColumnIndex($currentCol + 2) . '4', 'KEHADIRAN');
            $sheet->getStyle(Coordinate::stringFromColumnIndex($currentCol + 2) . '4:' . Coordinate::stringFromColumnIndex($currentCol + 4) . '4')->applyFromArray([
                'font' => ['bold' => true],
                'alignment' => [
                    'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                    'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
                ],
                'borders' => ['allBorders' => ['borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN]],
            ]);
            // Tambahkan Header SAKIT, IZIN, MANGKIR, CUTI
            $additionalHeaders = ['GRADE', 'SKOR', 'SI', 'MI', 'M', 'JML HARI TIDAK MASUK KERJA', 'PERSENTASE KEHADIRAN', 'AKUMULASI ABSENSI', 'GRADE AKHIR', 'TRACKING'];
            foreach ($additionalHeaders as $header) {
                $sheet->mergeCells(Coordinate::stringFromColumnIndex($currentCol) . '5:' . Coordinate::stringFromColumnIndex($currentCol) . '6');
                $sheet->setCellValue(Coordinate::stringFromColumnIndex($currentCol) . '5', $header);
                $sheet->getStyle(Coordinate::stringFromColumnIndex($currentCol) . '5:' . Coordinate::stringFromColumnIndex($currentCol) . '6')->applyFromArray([
                    'font' => ['bold' => true],
                    'alignment' => [
                        'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                        'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
                    ],
                    'borders' => ['allBorders' => ['borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN]],
                ]);
                $currentCol++;
            }
            // Tulis Data Karyawan Berdasarkan Shift
            $row = 7;
            foreach ($dataByShift as $shift => $karyawan) {
                // Tulis Data Karyawan
                $no = 1;
                foreach ($karyawan as $p) {
                    $sheet->setCellValue('A' . $row, $no++);
                    $sheet->setCellValue('B' . $row, $p['kode_kartu']);
                    $sheet->setCellValue('C' . $row, $p['nama_karyawan']);
                    $sheet->setCellValue('D' . $row, $p['shift']);
                    $sheet->setCellValue('E' . $row, $p['jenis_kelamin']);
                    $sheet->setCellValue('F' . $row, $p['tgl_masuk']);
                    $sheet->setCellValue('G' . $row, $p['nama_bagian']);
                    $sheet->setCellValue('H' . $row, $p['previous_grade'] ?? '-');
                    // Decode nilai
                    $nilai = json_decode($p['bobot_nilai'] ?? '[]', true);
                    // dd($nilai);
                    $colIndex = 9; // Dimulai dari kolom I
                    $totalNilai = 0;
                    $totalBobot = 0;

                    if (is_array($nilai) && count($nilai) > 0) {
                        foreach ($nilai as $value) {
                            $totalNilai += $value;
                            $totalBobot += self::bobot_nilai[$value] ?? 0; // Pastikan nilai default jika key tidak ditemukan
                        }

                        $average = $totalBobot / count($nilai);
                        $previous_grade = $p['previous_grade'] ?? '-';
                        $grade = $p['index_nilai'] ?? '-'; // Default grade jika tidak ada
                        $skor = $this->calculateSkor($grade);

                        // Set job description and additional columns
                        foreach ($nilai as $value) {
                            $sheet->setCellValue(Coordinate::stringFromColumnIndex($colIndex) . $row, $value);
                            $colIndex++;
                        }

                        // set grade
                        $sheet->setCellValue(Coordinate::stringFromColumnIndex($colIndex) . $row, $grade);
                        $colIndex++;
                        // set skor
                        $sheet->setCellValue(Coordinate::stringFromColumnIndex($colIndex) . $row, $skor);
                        $colIndex++;
                    }

                    // Set absen
                    $izin = $p['izin'] ?? 0;
                    $sakit = $p['sakit'] ?? 0;
                    $mangkir = $p['mangkir'] ?? 0;
                    $cuti = $p['cuti'] ?? 0;
                    $totalAbsen = ($sakit * 1) + ($izin * 2) + ($mangkir * 3);
                    // $kehadiran = 100 - $totalAbsen;

                    //Set Persentase Kehadiran
                    $start_date = new \DateTime($bulan['start_date']);
                    $end_date = new \DateTime($bulan['end_date']);
                    $selisih = $start_date->diff($end_date);
                    $totalHari = $selisih->days + 1; // +1 untuk menyertakan hari pertama
                    $jmlLibur = $bulan['jml_libur'];
                    $persentaseKehadiran = (($totalHari - $jmlLibur - $totalAbsen) / ($totalHari - $jmlLibur)) * 100;

                    // =IF(BW9<0.94,"-1",IF(BW9>0.93,"0"))
                    $accumulasi = $persentaseKehadiran < 94 ? -1 : 0;

                    // hasil akhir = skor + accumulasi
                    $hasil_akhir = $skor + $accumulasi;
                    $grade_akhir = $this->calculateGradeBatch($hasil_akhir);

                    // Update grade akhir ke database
                    $this->penilaianmodel->updateGradeAkhir($p['karyawan_id'], $p['id_periode'], $grade_akhir);

                    $tracking = $previous_grade . $grade_akhir;

                    $sheet->setCellValue(Coordinate::stringFromColumnIndex($colIndex) . $row, $sakit);
                    $colIndex++;
                    $sheet->setCellValue(Coordinate::stringFromColumnIndex($colIndex) . $row, $izin);
                    $colIndex++;
                    $sheet->setCellValue(Coordinate::stringFromColumnIndex($colIndex) . $row, $mangkir);
                    $colIndex++;

                    //jml hari tidak masuk kerja
                    $sheet->setCellValue(Coordinate::stringFromColumnIndex($colIndex) . $row, $totalAbsen);
                    $colIndex++;
                    //persentase kehadiran
                    $sheet->setCellValue(Coordinate::stringFromColumnIndex($colIndex) . $row, round($persentaseKehadiran) . '%'); // Tambahkan , 2 jika ingin menampilkan desimal
                    $colIndex++;
                    //accumulasi absensi
                    $sheet->setCellValue(Coordinate::stringFromColumnIndex($colIndex) . $row, $accumulasi);
                    $colIndex++;
                    //grade akhir
                    $sheet->setCellValue(Coordinate::stringFromColumnIndex($colIndex) . $row, $grade_akhir);
                    $colIndex++;
                    //tracking
                    $sheet->setCellValue(Coordinate::stringFromColumnIndex($colIndex) . $row, $tracking);
                    // $colIndex++;

                    //style from array
                    $sheet->getStyle('A' . $row . ':' . Coordinate::stringFromColumnIndex($colIndex) . $row)->applyFromArray([
                        'font' => ['name' => 'Times New Roman', 'size' => 10],
                        'alignment' => [
                            'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                            'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
                        ],
                        'borders' => ['allBorders' => ['borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN]],
                    ]);
                    $row++;
                }

                // Tulis Total Karyawan
                // $sheet->setCellValue('B' . $row, 'TOTAL' . $shift);
                $sheet->setCellValue('B' . $row, 'TOTAL');
                $sheet->mergeCells('B' . $row . ':C' . $row);
                $sheet->setCellValue('D' . $row, count($karyawan));
                $sheet->getStyle('B' . $row . ':D' . $row)->applyFromArray([
                    'font' => ['bold' => false],
                    'alignment' => [
                        'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                        'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
                    ],
                    'borders' => ['allBorders' => ['borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN]],
                ]);
                $row++;
            }

            // Set auto-size untuk semua kolom
            foreach ($sheet->getColumnIterator() as $column) {
                $sheet->getColumnDimension($column->getColumnIndex())->setAutoSize(true);
            }
        }

        // sheet baru untuk report tracking
        $sheet = $spreadsheet->createSheet();
        $sheet->setTitle('TRACKING');

        // Header Utama
        $sheet->mergeCells('A1:G1')->setCellValue('A1', 'REPORT TRACKING - ' . strtoupper($area_utama));
        $sheet->mergeCells('A2:G2')->setCellValue('A2', 'DEPARTEMEN KAOS KAKI');
        $sheet->mergeCells('A3:G3')->setCellValue('A3', '(PERIODE ' . strtoupper($nama_periode)  . ' ' . strtoupper($nama_batch) . ')');
        $sheet->getStyle('A1:A3')->applyFromArray([
            'font' => ['bold' => true, 'size' => 16, 'name' => 'Times New Roman'],
            'alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER],
        ]);

        // Header Kolom Statis
        $headers = ['NO', 'KODE KARTU', 'NAMA KARYAWAN', 'SHIFT', 'L/P', 'TGL. MASUK KERJA', 'BAGIAN', 'AREA', 'TRACKING'];
        $startCol = 1; // Kolom A
        foreach ($headers as $header) {
            $colLetter = Coordinate::stringFromColumnIndex($startCol);
            $sheet->getStyle($colLetter . '5')->getAlignment()->setWrapText(true);
            $sheet->mergeCells($colLetter . '5:' . $colLetter . '6')->setCellValue($colLetter . '5', $header);
            $sheet->getStyle($colLetter . '5:' . $colLetter . '6')->applyFromArray([
                'font' => [
                    'name' => 'Times New Roman',
                    'size' => 10,
                    'bold' => true,
                ],
                'alignment' => [
                    'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                    'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
                ],
                'borders' => ['allBorders' => ['borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN]],
            ]);
            $startCol++;
        }

        // sort data by kode kartu
        $sortOrders = [
            'KKMA',
            'KKMB',
            'KKMC',
            'KKMNS',
            'KKSA',
            'KKSB',
            'KKSC',
            'KKJHA',
            'KKJHB',
            'KKJHC',
            'KK2MA',
            'KK2MB',
            'KK2MC',
            'KK2MNS',
            'KK2SA',
            'KK2SB',
            'KK2SC',
            'KK5A',
            'KK5B',
            'KK5C',
            'KK5NS',
            'KK7A',
            'KK7B',
            'KK7C',
            'KK7NS',
            'KK8MA',
            'KK8MB',
            'KK8MC',
            'KK8MNS',
            'KK8SA',
            'KK8SB',
            'KK8SC',
            'KK9A',
            'KK9B',
            'KK9C',
            'KK9NS',
            'KK10A',
            'KK10B',
            'KK10C',
            'KK10NS',
            'KK11A',
            'KK11B',
            'KK11C',
            'KK11NS'
        ];

        $sortedData = [];
        foreach ($reportbatch as $p) {
            $sortedData[] = [
                'kode_kartu' => $p['kode_kartu'],
                'nama_karyawan' => $p['nama_karyawan'],
                'shift' => $p['shift'],
                'jenis_kelamin' => $p['jenis_kelamin'],
                'tgl_masuk' => $p['tgl_masuk'],
                'nama_bagian' => $p['nama_bagian'],
                'area' => $p['area'],
                'index_nilai' => $p['index_nilai'],
                'grade_akhir' => $p['grade_akhir'],
                'previous_grade' => $p['previous_grade'],
                'keterangan' => json_decode($p['keterangan'], true),
                'jobdesc' => json_decode($p['jobdesc'], true),
            ];
        }

        // Sort the flattened array by kode_kartu
        usort($sortedData, function ($a, $b) use ($sortOrders) {
            // Ekstrak prefix kode kartu
            preg_match('/^[A-Z]+/', $a['kode_kartu'], $matchA);
            preg_match('/^[A-Z]+/', $b['kode_kartu'], $matchB);

            $prefixA = $matchA[0] ?? '';
            $prefixB = $matchB[0] ?? '';

            // Cari posisi prefix di array $sortOrders
            $posA = array_search($prefixA, $sortOrders);
            $posB = array_search($prefixB, $sortOrders);

            // Jika tidak ditemukan, posisikan di akhir
            $posA = ($posA === false) ? PHP_INT_MAX : $posA;
            $posB = ($posB === false) ? PHP_INT_MAX : $posB;

            // Bandingkan berdasarkan posisi prefix
            if ($posA !== $posB) {
                return $posA <=> $posB;
            }

            // Jika prefix sama, bandingkan berdasarkan angka di kode kartu
            preg_match('/\d+/', $a['kode_kartu'], $numberA);
            preg_match('/\d+/', $b['kode_kartu'], $numberB);

            $numA = (int)($numberA[0] ?? PHP_INT_MAX); // Default jika tidak ada angka
            $numB = (int)($numberB[0] ?? PHP_INT_MAX);

            return $numA <=> $numB;
        });

        // Reorganize sorted data back by grade
        $dataByGrade = [];
        foreach ($sortedData as $sortedEmployee) {
            $dataByGrade[$sortedEmployee['kode_kartu']] = $sortedEmployee;
        }
        // dd ($dataByGrade);
        // Tulis Data Karyawan Berdasarkan Shift
        $row = 7;

        foreach ($dataByGrade as $p) {
            $sheet->setCellValue('A' . $row, $row - 6);
            $sheet->setCellValue('B' . $row, $p['kode_kartu']);
            $sheet->setCellValue('C' . $row, $p['nama_karyawan']);
            $sheet->setCellValue('D' . $row, $p['shift']);
            $sheet->setCellValue('E' . $row, $p['jenis_kelamin']);
            $sheet->setCellValue('F' . $row, $p['tgl_masuk']);
            $sheet->setCellValue('G' . $row, $p['nama_bagian']);
            if ($p['area']) {
                $sheet->setCellValue('H' . $row, $p['area']);
            } else {
                $sheet->setCellValue('H' . $row, '-');
            }

            // set tracking
            $tracking = $p['previous_grade'] . $p['grade_akhir'];
            $sheet->setCellValue('I' . $row, $tracking);

            //style from array
            $sheet->getStyle('A' . $row . ':I' . $row)->applyFromArray([
                'font' => ['name' => 'Times New Roman', 'size' => 10],
                'alignment' => [
                    'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                    'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
                ],
                'borders' => ['allBorders' => ['borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN]],
            ]);

            $row++;
        }
        // total karyawan
        $sheet->mergeCells('A' . $row . ':G' . $row);
        $sheet->setCellValue('A' . $row, 'TOTAL KARYAWAN');
        $sheet->setCellValue('H' . $row, count($dataByGrade));
        $sheet->getStyle('A' . $row . ':I' . $row)
            ->getFont()
            ->setName('Times New Roman')
            ->setBold(true)
            ->setSize(10);
        $sheet->getStyle('A' . $row . ':I' . $row)
            ->getBorders()
            ->getAllBorders()
            ->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);

        // center
        $sheet->getStyle('A' . $row . ':I' . $row)
            ->getAlignment()
            ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER)
            ->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);

        // Set wrap text
        $sheet->getStyle('A' . $row . ':I' . $row)->getAlignment()->setWrapText(true);

        // Simpan file Excel
        $filename = 'Report_Penilaian-' . $area_utama . '-' . date('m-d-Y') . '.xlsx';
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');

        $writer = new Xlsx($spreadsheet);
        $writer->save('php://output');
        exit();
    }

    public function updateGradeAkhirPerPeriode($area_utama, $nama_batch, $nama_periode)
    {
        $reportbatch = $this->penilaianmodel->getPenilaianByAreaByNamaBatchByNamaPeriode($area_utama, $nama_batch, $nama_periode);
        dd($reportbatch);
        $bulan = $this->periodeModel->getPeriodeByNamaBatchAndNamaPeriode($nama_batch, $nama_periode);

        foreach ($reportbatch as $p) {
            $grade = $p['index_nilai'] ?? '-';
            $skor = $this->calculateSkor($grade);

            $izin = $p['izin'] ?? 0;
            $sakit = $p['sakit'] ?? 0;
            $mangkir = $p['mangkir'] ?? 0;
            $cuti = $p['cuti'] ?? 0;
            $totalAbsen = ($sakit * 1) + ($izin * 2) + ($mangkir * 3);

            $start_date = new \DateTime($bulan['start_date']);
            $end_date = new \DateTime($bulan['end_date']);
            $selisih = $start_date->diff($end_date);
            $totalHari = $selisih->days + 1;
            $jmlLibur = $bulan['jml_libur'];
            $persentaseKehadiran = (($totalHari - $jmlLibur - $totalAbsen) / ($totalHari - $jmlLibur)) * 100;

            $accumulasi = $persentaseKehadiran < 94 ? -1 : 0;
            $hasil_akhir = $skor + $accumulasi;
            $grade_akhir = $this->calculateGradeBatch($hasil_akhir);

            $this->penilaianmodel->updateGradeAkhir($p['karyawan_id'], $p['id_periode'], $grade_akhir);
        }

        return redirect()->back()->with('success', 'Grade akhir berhasil diperbarui tanpa download excel.');
    }
}
