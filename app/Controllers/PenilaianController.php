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
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Cell\DataValidation;
use PhpOffice\PhpSpreadsheet\Cell\Hyperlink;
use PhpOffice\PhpSpreadsheet\Cell\ValueBinder;
use PhpOffice\PhpSpreadsheet\Cell\Cell;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\Cell\DefaultValueBinder;




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

        if ($area == 'null') {
            $area = null;
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
        $batch = $this->penilaianmodel->getPenilaianWhereAreautamaGroupByBatch($area_utama);
        // dd ($batch);
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

        // Ambil data penilaian
        $reportbatch = $this->penilaianmodel->getPenilaianGroupByBatchAndAreaByIdBatch($id_batch, $area_utama);
        // getPenilaianGroupByBatchAndAreaByIdBatch
        $getBulan = $this->penilaianmodel->getBatchGroupByBulanPenilaian();
        $getAreaUtama = $this->bagianmodel->getAreaGroupByAreaUtama();
        
        // Ambil daftar bagian unik
        $bagianList = array_unique(array_column($reportbatch, 'nama_bagian'));
        $nama_batch = $this->batchmodel->find($id_batch);
        // dd ($nama_batch);

        // Membuat file Excel
        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();

        foreach ($bagianList as $bagian) {
            $dataBagian = array_filter($reportbatch, function ($item) use ($bagian) {
                return $item['nama_bagian'] === $bagian;
            });

            $sheet = $spreadsheet->createSheet();
            $sheet->setTitle($bagian);

            // Header utama
            $sheet->mergeCells('A1:Q1');
            $sheet->setCellValue('A1', 'REPORT PENILAIAN ' . $bagian . ' AREA ' . $area_utama . ' ' . $nama_batch['nama_batch']);
            $sheet->getStyle('A1')->getFont()->setName('Times New Roman');
            $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(16);
            $sheet->getStyle('A1')->getAlignment()
                ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

            // Header kolom tetap
            $sheet->mergeCells('A3:A4'); // NO
            $sheet->setCellValue('A3', 'NO');
            $sheet->mergeCells('B3:B4');
            $sheet->setCellValue('B3', 'KODE KARTU BARU');
            $sheet->mergeCells('C3:C4');
            $sheet->setCellValue('C3', 'NAMA LENGKAP');
            $sheet->mergeCells('D3:D4');
            $sheet->setCellValue('D3', 'L/P');
            $sheet->mergeCells('E3:E4');
            $sheet->setCellValue('E3', 'TGL. MASUK KERJA');
            $sheet->mergeCells('F3:F4');
            $sheet->setCellValue('F3', 'BAGIAN');

            // Header kolom bulan
            $headerStartCol = 'G';
            $bulanHeaders = [];
            foreach ($getBulan as $b) {
                $bulanHeaders[] = date('M', strtotime($b['end_date']));
            }
            $endColBulan = chr(ord($headerStartCol) + count($bulanHeaders) - 1);

            $sheet->mergeCells($headerStartCol . '3:' . $endColBulan . '3');
            $sheet->setCellValue($headerStartCol . '3', 'BULAN');
            foreach ($bulanHeaders as $index => $bulan) {
                $sheet->setCellValue(chr(ord($headerStartCol) + $index) . '4', $bulan);
            }

            // Tambahan kolom untuk "POINT OBSERVASI", "RATA-RATA POINT AREA", dan "GRADE"
            $sheet->mergeCells(chr(ord($endColBulan) + 1) . '3:' . chr(ord($endColBulan) + 1) . '4');
            $sheet->setCellValue(chr(ord($endColBulan) + 1) . '3', 'POINT OBSERVASI');
            $sheet->mergeCells(chr(ord($endColBulan) + 2) . '3:' . chr(ord($endColBulan) + 2) . '4');
            $sheet->setCellValue(chr(ord($endColBulan) + 2) . '3', 'RATA-RATA POINT AREA');
            $sheet->mergeCells(chr(ord($endColBulan) + 3) . '3:' . chr(ord($endColBulan) + 3) . '4');
            $sheet->setCellValue(chr(ord($endColBulan) + 3) . '3', 'GRADE');
            $sheet->mergeCells(chr(ord($endColBulan) + 4) . '3:' . chr(ord($endColBulan) + 4) . '4');
            $sheet->setCellValue(chr(ord($endColBulan) + 4) . '3', 'PROD');
            $sheet->mergeCells(chr(ord($endColBulan) + 5) . '3:' . chr(ord($endColBulan) + 5) . '4');
            $sheet->setCellValue(chr(ord($endColBulan) + 5) . '3', 'BS');
            $sheet->mergeCells(chr(ord($endColBulan) + 6) . '3:' . chr(ord($endColBulan) + 6) . '4');
            $sheet->setCellValue(chr(ord($endColBulan) + 6) . '3', 'USED NEEDLE');
            $sheet->mergeCells(chr(ord($endColBulan) + 7) . '3:' . chr(ord($endColBulan) + 7) . '4');
            $sheet->setCellValue(chr(ord($endColBulan) + 7) . '3', 'POINT');
            $sheet->mergeCells(chr(ord($endColBulan) + 8) . '3:' . chr(ord($endColBulan) + 8) . '4');
            $sheet->setCellValue(chr(ord($endColBulan) + 8) . '3', 'GRADE AKHIR');

            $sheet->getStyle('A3:' . chr(ord($endColBulan) + 8) . '4')
                ->getBorders()
                ->getAllBorders()
                ->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);

            // Set style untuk header kolom
            $sheet->getStyle('A3:' . chr(ord($endColBulan) + 8) . '4')
                ->getFont()
                ->setName('Times New Roman');
            $sheet->getStyle('A3:' . chr(ord($endColBulan) + 8) . '4')
                ->getFont()
                ->setBold(true)
                ->setSize(12);
            // Tulis data
            $row = 5;
            $no = 1;

            foreach ($dataBagian as $p) {
                $data = [
                    $no,
                    $p['kode_kartu'],
                    $p['nama_karyawan'],
                    $p['jenis_kelamin'],
                    $p['tgl_masuk'],
                    $p['nama_bagian']
                ];

                $total = 0;
                $count = 0;

                // Get bulan untuk masing-masing karyawan
                // Get bulan untuk masing-masing karyawan
                $getBulanData = $this->penilaianmodel->getPenilaianGroupByBulan($p['id_karyawan'], $p['id_batch'], $p['id_jobrole']);

                // Set nilai default untuk kolom G, H, dan I
                $periodeAwal = $periodeTengah = $periodeAkhir = '';

                // Proses data untuk setiap periode
                foreach ($getBulanData as $b) {
                    $hurufNilai = $b['index_nilai'] ?? ''; // Pastikan nilai default jika tidak ada
                    $angkaNilai = $this->convertHurufToAngka($hurufNilai);

                    // Tentukan kolom sesuai dengan periode
                    if ($b['nama_periode'] == 'Awal') {
                        $periodeAwal = ($angkaNilai !== null && $angkaNilai != 0) ? $angkaNilai : '';
                    } elseif ($b['nama_periode'] == 'Tengah') {
                        $periodeTengah = ($angkaNilai !== null && $angkaNilai != 0) ? $angkaNilai : '';
                    } elseif ($b['nama_periode'] == 'Akhir') {
                        $periodeAkhir = ($angkaNilai !== null && $angkaNilai != 0) ? $angkaNilai : '';
                    }

                    // Hitung total dan jumlah untuk rata-rata
                    if ($angkaNilai > 0) {
                        $total += $angkaNilai;
                        $count++;
                    }
                }

                // Tambahkan nilai periode ke data dalam urutan tetap
                $data[] = $periodeAwal;  // Kolom G
                $data[] = $periodeTengah; // Kolom H
                $data[] = $periodeAkhir; // Kolom I

                // dd ($data);
                // Hitung rata-rata dan grade
                $average = round(($total + 1) / ($count + 1), 2);
                $grade = $this->calculateGradeBatch($average);
                $bsmc = $this->bsmcModel->getBsmcByIdKaryawan($p['id_karyawan']);
                $getTop3 = $this->bsmcModel->getTop3Produksi($area_utama, $id_batch);
                $getMinAvgBS = $this->bsmcModel->getTop3LowestBS($area_utama, $id_batch);
                $getTop3Rosso = $this->rossoModel->getTop3Produksi($area_utama, $id_batch);
                $getMinAvgBSRosso = $this->rossoModel->getTop3LowestBS($area_utama, $id_batch);
                $getTop3UsedNeedle = $this->jarumModel->getTop3Produksi($area_utama, $id_batch);


                // Tambahkan kolom rata-rata dan grade ke data
                $data[] = 1; // Kolom tambahan (bisa untuk catatan)
                $data[] = $average;
                $data[] = $grade;


                // jika karayawan di $getTop3 maka berikan angka 1/2/3 sesuai urutan
                
                $activesheet = $spreadsheet->getActiveSheet();

                if ("OPERATOR" == $activesheet->getTitle()) {
                    if ($getTop3) {
                        $noTop3 = 1;
                        foreach ($getTop3 as $top3) {
                            if ($top3['id_karyawan'] == $p['id_karyawan']) {
                                $data[] = $noTop3;
                            }
                            $noTop3++;
                        }
                    } else {
                        $data[] = NULL;
                    }
                    // jika karayawan di $getMinAvgBS maka berikan angka 1/2/3 sesuai urutan
                    if ($getMinAvgBS) {
                        $noMinAvgBS = 1;
                        foreach ($getMinAvgBS as $minAvgBS) {
                            if ($minAvgBS['id_karyawan'] == $p['id_karyawan']) {
                                $data[] = $noMinAvgBS;
                            }
                            $noMinAvgBS++;
                        }
                    } else {
                        $data[] = NULL;
                    }
                    $data[] = "";
                } elseif("ROSSO" == $activesheet->getTitle()) {
                    if ($getTop3Rosso) {
                        $noTop3Rosso = 1;
                        foreach ($getTop3Rosso as $top3Rosso) {
                            if ($top3Rosso['id_karyawan'] == $p['id_karyawan']) {
                                $data[] = $noTop3Rosso;
                            }
                            $noTop3Rosso++;
                        }
                    } else {
                        $data[] = NULL;
                    }
                    // jika karyawan di $getMinAvgBSRosso maka berikan angka 1/2/3 sesuai urutan
                    if ($getMinAvgBSRosso) {
                        $noMinAvgBSRosso = 1;
                        foreach ($getMinAvgBSRosso as $minAvgBSRosso) {
                            if ($minAvgBSRosso['id_karyawan'] == $p['id_karyawan']) {
                                $data[] = $noMinAvgBSRosso;
                            }
                            $noMinAvgBSRosso++;
                        }
                    } else {
                        $data[] = NULL;
                    }
                    $data[] = "";
                } else{
                    $data[] = "";
                    $data[] = "";
                    if ($getTop3UsedNeedle) {
                        $noTop3UsedNeedle = 1;
                        foreach ($getTop3UsedNeedle as $top3UsedNeedle) {
                            if ($top3UsedNeedle['id_karyawan'] == $p['id_karyawan']) {
                                $data[] = $noTop3UsedNeedle;
                            }
                            $noTop3UsedNeedle++;
                        }
                    } else {
                        $data[] = NULL;
                    }
                }


                
                // set rumus excel untuk point =K5+IF(M5<>"";1;0)+IF(N5<>"";1;0)+IF(O5<>"";1;0)
                $sum = "=SUM(K" . $row . ",(IF(M" . $row . "<>\"\",1,0)),(IF(N" . $row . "<>\"\",1,0)),(IF(O" . $row . "<>\"\",1,0)))";
                $sheet->setCellValue("P" . $row, $sum);

                
                // set rumus excel untuk gradeakhir =IF(P5>3,5;"A";IF(P5>2,5;"B";IF(P5>1,75;"C";IF(P5<1,75;"D";""))))
                $konversigradeakhir = "=IF(P" . $row . ">3.5,\"A\",IF(P" . $row . ">2.5,\"B\",IF(P" . $row . ">1.75,\"C\",IF(P" . $row . "<1.75,\"D\",\"D\"))))";
                // $konvesigradeakhir = "=IF(P" . $row . ">3,5;\"A\";IF(P" . $row . ">2,5;\"B\";IF(P" . $row . ">1,75;\"C\";IF(P" . $row . "<1,75;\"D\";\"\"))))";
                $sheet->setCellValue("Q" . $row, $konversigradeakhir);
                // dd ($data, $total, $count);
                $sheet->fromArray($data, null, 'A' . $row);
                $sheet->getStyle('A' . $row . ':' . chr(ord($endColBulan) + 8) . $row)
                    ->getBorders()
                    ->getAllBorders()
                    ->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);

                // set font data
                $sheet->getStyle('A' . $row . ':' . chr(ord($endColBulan) + 8) . $row)->getFont()->setName('Times New Roman');
                // Set font size data
                $sheet->getStyle('A' . $row . ':' . chr(ord($endColBulan) + 8) . $row)->getFont()->setSize(10);
                $row++;
                $no++;
            }
      
            // Tambahkan total karyawan
            $sheet->mergeCells('A' . $row . ':B' . $row);
            $sheet->setCellValue('A' . $row, 'TOTAL KARYAWAN');
            $sheet->setCellValue('C' . $row, $no - 1);
            $sheet->setCellValue('D' . $row, 'org');
            $sheet->getStyle('A' . $row . ':D' . $row)
                ->getFont()
                ->setName('Times New Roman')
                ->setBold(true)
                ->setSize(10);
            $sheet->getStyle('A' . $row . ':' . chr(ord($endColBulan) + 8) . $row)
                ->getBorders()
                ->getAllBorders()
                ->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);

            // Atur kolom yang harus di tengah
            $columnsCenter = ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q'];
            foreach ($columnsCenter as $col) {
                $sheet->getStyle($col . '1:' . $col . $row)
                    ->getAlignment()
                    ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER)
                    ->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);
            }

            // Atur ukuran kolom
            $sheet->getColumnDimension('A')->setWidth(7);
            $sheet->getColumnDimension('B')->setWidth(10);
            $sheet->getColumnDimension('C')->setWidth(30);
            $sheet->getColumnDimension('D')->setWidth(5);
            $sheet->getColumnDimension('E')->setWidth(15);
            $sheet->getColumnDimension('J')->setWidth(10);
            $sheet->getColumnDimension('K')->setWidth(10);
            $sheet->getColumnDimension('L')->setWidth(10);
            $sheet->getColumnDimension('M')->setWidth(10);
            $sheet->getColumnDimension('N')->setWidth(10);
            $sheet->getColumnDimension('O')->setWidth(10);
            $sheet->getColumnDimension('P')->setWidth(10);
            $sheet->getColumnDimension('Q')->setWidth(10);

            // Wrap text
            $sheet->getStyle('B3:B4')->getAlignment()->setWrapText(true);
            $sheet->getStyle('C3:C4')->getAlignment()->setWrapText(true);
            $sheet->getStyle('E3:E4')->getAlignment()->setWrapText(true);
            $sheet->getStyle('J3:J4')->getAlignment()->setWrapText(true);
            $sheet->getStyle('K3:K4')->getAlignment()->setWrapText(true);
            $sheet->getStyle('O3:O4')->getAlignment()->setWrapText(true);
            $sheet->getStyle('Q3:Q4')->getAlignment()->setWrapText(true);
        }

        // Hapus sheet default
        $spreadsheet->removeSheetByIndex(0);

        $grades = ['A', 'B', 'C', 'D']; // Grades
        $dataByGrade = []; // Data berdasarkan grade
        $sortedData = []; // Temporary array to hold sorted data

        // Iterasi sheet asli
        foreach ($spreadsheet->getAllSheets() as $sheet) {
            $sheetName = $sheet->getTitle();
            $data = $sheet->toArray(); // Data sheet

            // Iterasi setiap baris untuk filter grade
            foreach ($data as $row) {
                // Ambil grade dari kolom ke-16 (misal Q)
                $grade = $row[16] ?? '';

                if (in_array($grade, $grades)) {
                    // Ambil kolom 0-5 dan 15-16
                    $filteredRow = array_merge(
                        array_slice($row, 1, 5),    // Kolom 0 sampai 5
                        array_slice($row, 15, 2)   // Kolom 15 sampai 16
                    );

                    // Simpan ke data berdasarkan grade
                    $dataByGrade[$grade][] = $filteredRow;
                }
            }
        }
        // dd ($dataByGrade);
        // Sort Order
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

        // Flatten data by grade for sorting
        foreach ($dataByGrade as $grade => $employees) {
            foreach ($employees as $index => $employee) {
                $sortedData[] = [
                    'grade' => $grade,
                    'employee_data' => $employee,
                    'kode_kartu' => $employee[0] // assuming kode_kartu is in column 1
                ];
            }
        }
        // dd ($sortedData);
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
        // dd ($sortedData);
        // Reorganize sorted data back by grade
        $dataByGrade = [];
        foreach ($sortedData as $sortedEmployee) {
            $dataByGrade[$sortedEmployee['grade']][] = $sortedEmployee['employee_data'];
        }

        // dd ($dataByGrade);

        foreach ($grades as $grade) {
            if (!empty($dataByGrade[$grade])) {
                // Buat sheet baru untuk grade
                $newSheet = $spreadsheet->createSheet();
                $newSheet->setTitle("GRADE " . $grade);

                // Tambahkan header ke sheet
                $header = [
                    ['NO', 'KODE KARTU', 'NAMA KARYAWAN', 'L/P', 'TGL. MASUK KERJA', 'BAGIAN', 'POINT', 'GRADE AKHIR']
                ];
                $newSheet->fromArray($header, null, 'A1');

                // Style untuk header
                $newSheet->getStyle('A1:H1')->getFont()->setBold(true); // Bold font for header
                $newSheet->getStyle('A1:H1')->getFont()->setName('Times New Roman'); // Set font name
                $newSheet->getStyle('A1:H1')->getFont()->setSize(12); // Set font size
                // width kolom
                $newSheet->getColumnDimension('A')->setWidth(5);  // NO
                $newSheet->getColumnDimension('B')->setWidth(10); // KODE KARTU
                $newSheet->getColumnDimension('C')->setWidth(25); // NAMA KARYAWAN
                $newSheet->getColumnDimension('D')->setWidth(5);  // L/P
                $newSheet->getColumnDimension('E')->setWidth(15); // TGL. MASUK KERJA
                $newSheet->getColumnDimension('F')->setWidth(15); // BAGIAN
                $newSheet->getColumnDimension('G')->setWidth(10); // POINT
                $newSheet->getColumnDimension('H')->setWidth(10); // GRADE AKHIR

                // wrap text
                $newSheet->getStyle('A1:H1')->getAlignment()->setWrapText(true);
                $newSheet->getStyle('A1:H1')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER); // Center align header
                $newSheet->getStyle('A1:H1')->getBorders()->getAllBorders()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN); // Add borders to header
                $newSheet->getStyle('A1:H1')->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setRGB('B0C4DE'); // Light Blue background for header

                // Tambahkan nomor urut di kolom "NO" untuk setiap data
                $rowIndex = 2;  // Baris dimulai dari 2 karena header di baris 1
                $dataWithNo = [];  // Array baru untuk menampung data dengan nomor urut

                foreach ($dataByGrade[$grade] as $index => $data) {
                    // Menambahkan nomor urut (NO) pada setiap baris
                    $dataWithNo[] = array_merge([$index + 1], $data);  // Menambahkan nomor urut di depan data
                }

                // Tambahkan data yang sudah ada nomor urutnya
                $newSheet->fromArray($dataWithNo, null, 'A2');

                // Style untuk data rows
                $newSheet->getStyle('A2:H' . (count($dataWithNo) + 1))->getBorders()->getAllBorders()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN); // Add borders to data rows
                $newSheet->getStyle('A2:H' . (count($dataWithNo) + 1))->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER); // Center align data
                $newSheet->getStyle('A2:H' . (count($dataWithNo) + 1))->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER); // Vertical center for data

                // Alternating row color for better readability
                $rowCount = count($dataWithNo);
                for ($i = 2; $i <= $rowCount + 1; $i++) {
                    if ($i % 2 == 0) {
                        // Apply light grey background for even rows
                        $newSheet->getStyle('A' . $i . ':H' . $i)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setRGB('F2F2F2');
                    }
                }

                // Set font for data rows
                $newSheet->getStyle('A2:H' . (count($dataWithNo) + 1))->getFont()->setName('Times New Roman');
                $newSheet->getStyle('A2:H' . (count($dataWithNo) + 1))->getFont()->setSize(10);

                // total karyawan
                $newSheet->mergeCells('A' . ($rowCount + 2) . ':C' . ($rowCount + 2));
                $newSheet->setCellValue('A' . ($rowCount + 2), 'TOTAL KARYAWAN');
                $newSheet->setCellValue('D' . ($rowCount + 2), $rowCount);
                $newSheet->getStyle('A' . ($rowCount + 2) . ':H' . ($rowCount + 2))
                    ->getFont()
                    ->setName('Times New Roman')
                    ->setBold(true)
                    ->setSize(10);
                $newSheet->getStyle('A' . ($rowCount + 2) . ':H' . ($rowCount + 2))
                    ->getBorders()
                    ->getAllBorders()
                    ->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);

                // center
                $newSheet->getStyle('A' . ($rowCount + 2) . ':H' . ($rowCount + 2))
                    ->getAlignment()
                    ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER)
                    ->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);

                // Set wrap text
                $newSheet->getStyle('A' . ($rowCount + 2) . ':H' . ($rowCount + 2))->getAlignment()->setWrapText(true);
            }
        }





        // Simpan file Excel
        $filename = 'Report_Penilaian-' . $area_utama . '-' . date('m-d-Y') . '.xlsx';
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

    public function excelReportPerPeriode($area_utama, $nama_batch, $nama_periode) {
    
    $reportbatch = $this->penilaianmodel->getPenilaianByAreaByNamaBatchByNamaPeriode($area_utama, $nama_batch, $nama_periode);
        // dd($reportbatch);
    $bulan = $this->periodeModel->getPeriodeByNamaBatchAndNamaPeriode($nama_batch, $nama_periode);
        // dd(date('M', strtotime($bulan)));
    $uniqueSheets = [];
    foreach ($reportbatch as $item) {
        $key = $item['area'] . ' - ' . $item['nama_bagian'];
        if (!in_array($key, $uniqueSheets)) {
            $uniqueSheets[] = $key;
        }
    }

    $spreadsheet = new Spreadsheet();
    $spreadsheet->removeSheetByIndex(0); // Hapus sheet default

    foreach ($uniqueSheets as $sheetName) {
        $sheet = $spreadsheet->createSheet();
        $sheet->setTitle(substr($sheetName, 0, 31)); // Nama sheet sesuai area dan bagian

        // Pisahkan area dan nama bagian
        list($currentArea, $currentBagian) = explode(' - ', $sheetName, 2);

        // Filter data untuk sheet ini
        $dataFiltered = array_filter($reportbatch, function ($item) use ($currentArea, $currentBagian) {
            return $item['area'] === $currentArea && $item['nama_bagian'] === $currentBagian;
        });

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
                    $kehadiran = 100 - $totalAbsen;
                    // =IF(BW9<0.94,"-1",IF(BW9>0.93,"0"))
                    $accumulasi = $kehadiran < 94 ? -1 : 0;

                    // hasil akhir = skor + accumulasi
                    $hasil_akhir = $skor + $accumulasi;
                    $grade_akhir = $this->calculateGradeBatch($hasil_akhir);
                    // dd ($grade_akhir);
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
                    $sheet->setCellValue(Coordinate::stringFromColumnIndex($colIndex) . $row, $kehadiran);
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

    // Simpan file Excel
    $filename = 'Report_Penilaian-' . $area_utama . '-' . date('m-d-Y') . '.xlsx';
    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header('Content-Disposition: attachment;filename="' . $filename . '"');
    header('Cache-Control: max-age=0');

    $writer = new Xlsx($spreadsheet);
    $writer->save('php://output');
    exit();
}

}
