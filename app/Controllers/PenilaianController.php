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
use CodeIgniter\HTTP\ResponseInterface;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Cell\DataValidation;
use PhpOffice\PhpSpreadsheet\Cell\Hyperlink;
use PhpOffice\PhpSpreadsheet\Cell\ValueBinder;




class PenilaianController extends BaseController
{
    protected $penilaianmodel;
    protected $jobrolemodel;
    protected $bagianmodel;
    protected $batchmodel;
    protected $karyawanmodel;
    protected $periodeModel;
    protected $absenmodel;
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

    //     return view('penilaian/create', compact('json'));
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

        return view('penilaian/reportareaperbatch', $data);
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

        // Membuat file Excel
        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();

        $spreadsheet->getActiveSheet()->setTitle('REPORT BATCH');

        // set header
        $spreadsheet->getActiveSheet()->mergeCells('A1:I1');
        $spreadsheet->getActiveSheet()->setCellValue('A1', 'REPORT PENILAIAN BATCH ' . $area_utama);
        $spreadsheet->getActiveSheet()->mergeCells('A2:I2');
        $spreadsheet->getActiveSheet()->setCellValue('A2', 'DEPARTEMEN KAOS KAKI');

        $bulan = '';
        foreach ($getBulan as $b) {
            $bulan .= date('M', strtotime($b['end_date'])) . ' ';
        }
        $spreadsheet->getActiveSheet()->mergeCells('A3:I3');
        $spreadsheet->getActiveSheet()->setCellValue('A3', '(PERIODE ' . trim($bulan) . ')');
        // Set font header
        $spreadsheet->getActiveSheet()->getStyle('A1:A3')->getFont()->setName('Times New Roman');
        $spreadsheet->getActiveSheet()->getStyle('A1:A3')->getFont()->setBold(true)->setSize(16);
        $spreadsheet->getActiveSheet()->getStyle('A1:A3')->getAlignment()
            ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

        // Set header kolom
        $spreadsheet->getActiveSheet()->setCellValue('A4', 'NO');
        $spreadsheet->getActiveSheet()->setCellValue('B4', 'KODE KARTU');
        $spreadsheet->getActiveSheet()->setCellValue('C4', 'NAMA KARYAWAN');
        $spreadsheet->getActiveSheet()->setCellValue('D4', 'L/P');
        $spreadsheet->getActiveSheet()->setCellValue('E4', 'TGL. MASUK KERJA');
        $spreadsheet->getActiveSheet()->setCellValue('F4', 'BAGIAN');
        $spreadsheet->getActiveSheet()->setCellValue('G4', 'POINT');
        $spreadsheet->getActiveSheet()->setCellValue('H4', 'GRADE');
        $spreadsheet->getActiveSheet()->setCellValue('I4', 'AREA');

        // set font header kolom
        $spreadsheet->getActiveSheet()->getStyle('A4:I4')->getFont()->setName('Times New Roman');
        // Set font size header kolom
        $spreadsheet->getActiveSheet()->getStyle('A4:I4')->getFont()->setSize(12);
        // Set style header kolom
        $spreadsheet->getActiveSheet()->getStyle('A4:I4')->getFont()->setBold(true);
        $spreadsheet->getActiveSheet()->getStyle('A4:I4')->getAlignment()
            ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

        $spreadsheet->getActiveSheet()->getStyle('A4:I4')->getAlignment()
            ->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);
        // Set style border header kolom
        $spreadsheet->getActiveSheet()->getStyle('A4:I4')
            ->getBorders()
            ->getAllBorders()
            ->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);

        // set column dimension manual
        $spreadsheet->getActiveSheet()->getColumnDimension('A')->setWidth(5); // NO
        $spreadsheet->getActiveSheet()->getColumnDimension('B')->setWidth(10); // KODE KARTU
        $spreadsheet->getActiveSheet()->getColumnDimension('C')->setWidth(25); // NAMA KARYAWAN
        $spreadsheet->getActiveSheet()->getColumnDimension('D')->setWidth(5); // L/P
        $spreadsheet->getActiveSheet()->getColumnDimension('E')->setWidth(10); // TGL. MASUK KERJA
        $spreadsheet->getActiveSheet()->getColumnDimension('F')->setWidth(15); // BAGIAN
        $spreadsheet->getActiveSheet()->getColumnDimension('G')->setWidth(10); // POINT
        $spreadsheet->getActiveSheet()->getColumnDimension('H')->setWidth(10); // GRADE
        $spreadsheet->getActiveSheet()->getColumnDimension('I')->setWidth(10); // AREA

        // wrap text
        $spreadsheet->getActiveSheet()->getStyle('A4:I4')->getAlignment()->setWrapText(true);
        // Set data
        $row = 5;
        $no = 1;

        foreach ($reportbatch as $p) {
            $spreadsheet->getActiveSheet()->setCellValue('A' . $row, $no);
            $spreadsheet->getActiveSheet()->setCellValue('B' . $row, $p['kode_kartu']);
            $spreadsheet->getActiveSheet()->setCellValue('C' . $row, $p['nama_karyawan']);
            $spreadsheet->getActiveSheet()->setCellValue('D' . $row, $p['jenis_kelamin']);
            $spreadsheet->getActiveSheet()->setCellValue('E' . $row, $p['tgl_masuk']);
            $spreadsheet->getActiveSheet()->setCellValue('F' . $row, $p['nama_bagian']);
            $spreadsheet->getActiveSheet()->setCellValue('G' . $row, 1);
            $spreadsheet->getActiveSheet()->setCellValue('H' . $row, $p['index_nilai']);
            $spreadsheet->getActiveSheet()->setCellValue('I' . $row, $p['area_utama']);

            $row++;
            $no++;
        }

        // set font data
        $spreadsheet->getActiveSheet()->getStyle('A5:I' . ($row - 1))->getFont()->setName('Times New Roman');

        // set font size data
        $spreadsheet->getActiveSheet()->getStyle('A5:I' . ($row - 1))->getFont()->setSize(10);

        // Set style border data
        $spreadsheet->getActiveSheet()->getStyle('A5:I' . ($row - 1))
            ->getBorders()
            ->getAllBorders()
            ->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);

        // Set auto-size columns
        // foreach (range('A', 'I') as $col) {
        //     $spreadsheet->getActiveSheet()->getColumnDimension($col)->setAutoSize(true);
        // }

        // wrap text
        $spreadsheet->getActiveSheet()->getStyle('A1:I' . ($row - 1))->getAlignment()->setWrapText(true);

        // text center
        $spreadsheet->getActiveSheet()->getStyle('A1:I' . ($row - 1))->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);


        foreach ($bagianList as $bagian) {
            $dataBagian = array_filter($reportbatch, function ($item) use ($bagian) {
                return $item['nama_bagian'] === $bagian;
            });

            $sheet = $spreadsheet->createSheet();
            $sheet->setTitle($bagian);

            // Header utama
            $sheet->mergeCells('A1:Q1');
            $sheet->setCellValue('A1', 'REPORT PENILAIAN ' . $bagian . ' AREA ' . $area_utama);
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

                // Tambahkan kolom rata-rata dan grade ke data
                $data[] = 1; // Kolom tambahan (bisa untuk catatan)
                $data[] = $average;
                $data[] = $grade;
                $data[] = "";
                $data[] = "";
                $data[] = "";
                $data[] = $average;
                $data[] = $grade;
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
            // var_dump($data[$no]);
            // print_r($data[$no]);
            // dd($data);
            // Tambahkan total karyawan
            $sheet->mergeCells('A' . $row . ':B' . $row);
            $sheet->setCellValue('A' . $row, 'TOTAL KARYAWAN');
            $sheet->setCellValue('C' . $row, $no - 1);
            $sheet->setCellValue('D' . $row, 'org');

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

    public function excelReportPerPeriode($area_utama, $nama_batch, $nama_periode)
    {
        $reportbatch = $this->penilaianmodel->getPenilaianByAreaByNamaBatchByNamaPeriode($area_utama, $nama_batch, $nama_periode);

        // Ambil daftar kombinasi area_utama dan nama_bagian
        $uniqueSheets = [];
        foreach ($reportbatch as $item) {
            $key = $item['area'] . ' - ' . $item['nama_bagian'];
            if (!in_array($key, $uniqueSheets)) {
                $uniqueSheets[] = $key;
            }
        }
        // dd ($uniqueSheets);
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

            // Header Utama
            $sheet->mergeCells('A1:G1')->setCellValue('A1', 'REPORT PENILAIAN - ' . strtoupper($sheetName));
            $sheet->mergeCells('A2:G2')->setCellValue('A2', 'DEPARTEMEN KAOS KAKI');
            $sheet->mergeCells('A3:G3')->setCellValue('A3', '(PERIODE ' . strtoupper(date('M', strtotime($nama_periode))) . ' ' . strtoupper($nama_batch) . ')');
            $sheet->getStyle('A1:A3')->applyFromArray([
                'font' => ['bold' => true, 'size' => 16, 'name' => 'Times New Roman'],
                'alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER]
            ]);

            // Header Kolom Statis
            $headers = ['NO', 'KODE KARTU', 'NAMA KARYAWAN', 'L/P', 'TGL. MASUK KERJA', 'BAGIAN', 'BEFORE'];
            $startCol = 1; // Kolom A
            foreach ($headers as $header) {
                $colLetter = Coordinate::stringFromColumnIndex($startCol);
                $sheet->mergeCells($colLetter . '4:' . $colLetter . '5')->setCellValue($colLetter . '4', $header);
                $sheet->getStyle($colLetter . '4:' . $colLetter . '5')->applyFromArray([
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
            $currentCol = 8; // Dimulai dari kolom H
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
                $sheet->mergeCells($startColLetter . '4:' . $endColLetter . '4');
                $sheet->setCellValue($startColLetter . '4', $keterangan);
                $sheet->getStyle($startColLetter . '4:' . $endColLetter . '4')->applyFromArray([
                    'font' => ['bold' => true],
                    'alignment' => [
                        'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                        'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
                    ],
                    'borders' => ['allBorders' => ['borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN]],
                ]);

                // Header jobdesc
                foreach ($jobs as $job) {
                    $sheet->setCellValue(Coordinate::stringFromColumnIndex($currentCol) . '5', $job);
                    $sheet->getStyle(Coordinate::stringFromColumnIndex($currentCol) . '5')->applyFromArray([
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
            $sheet->mergeCells(Coordinate::stringFromColumnIndex($currentCol + 2) . '3:' . Coordinate::stringFromColumnIndex($currentCol + 4) . '3');
            $sheet->setCellValue(Coordinate::stringFromColumnIndex($currentCol + 2) . '3', 'KEHADIRAN');
            $sheet->getStyle(Coordinate::stringFromColumnIndex($currentCol + 2) . '3:' . Coordinate::stringFromColumnIndex($currentCol + 4) . '3')->applyFromArray([
                'font' => ['bold' => true],
                'alignment' => [
                    'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                    'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
                ],
                'borders' => ['allBorders' => ['borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN]],
            ]);
            // Tambahkan Header SAKIT, IZIN, MANGKIR, CUTI
            $additionalHeaders = ['GRADE', 'SKOR', 'SI', 'MI', 'M', 'JML HARI TIDAK MASUK KERJA', 'PERSENTASE KEHADIRAN', 'ACCUMULASI ABSENSI', 'GRADE AKHIR', 'TRACKING'];
            foreach ($additionalHeaders as $header) {
                $sheet->mergeCells(Coordinate::stringFromColumnIndex($currentCol) . '4:' . Coordinate::stringFromColumnIndex($currentCol) . '5');
                $sheet->setCellValue(Coordinate::stringFromColumnIndex($currentCol) . '4', $header);
                $sheet->getStyle(Coordinate::stringFromColumnIndex($currentCol) . '4:' . Coordinate::stringFromColumnIndex($currentCol) . '5')->applyFromArray([
                    'font' => ['bold' => true],
                    'alignment' => [
                        'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                        'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
                    ],
                    'borders' => ['allBorders' => ['borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN]],
                ]);
                $currentCol++;
            }


            // Tulis Data Karyawan
            $row = 6;
            $no = 1;
            foreach ($dataFiltered as $p) {
                $sheet->setCellValue('A' . $row, $no++);
                $sheet->setCellValue('B' . $row, $p['kode_kartu']);
                $sheet->setCellValue('C' . $row, $p['nama_karyawan']);
                $sheet->setCellValue('D' . $row, $p['jenis_kelamin']);
                $sheet->setCellValue('E' . $row, $p['tgl_masuk']);
                $sheet->setCellValue('F' . $row, $p['nama_bagian']);
                $sheet->setCellValue('G' . $row, 1); // Placeholder "BEFORE"
                // Decode nilai
                $nilai = json_decode($p['bobot_nilai'] ?? '[]', true);
                // dd($nilai);
                $colIndex = 8; // Dimulai dari kolom H
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
                $trakcing = $grade . $grade_akhir;

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
                $sheet->setCellValue(Coordinate::stringFromColumnIndex($colIndex) . $row, $trakcing);
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
