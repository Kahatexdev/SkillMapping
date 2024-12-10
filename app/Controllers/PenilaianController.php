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
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;



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

    // fungsi untuk mengubah index_nilai ke tempSkor
    private function indexToSkor($index_nilai)
    {
        $map = ['A' => 4, 'B' => 3, 'C' => 2, 'D' => 1];
        return $map[$index_nilai] ?? 0;
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

    public function reportExcel(int $id_bagian, int $id_batch, int $id_jobrole)
    {
        $penilaian = $this->penilaianmodel->getPenilaianByIdBagian($id_bagian, $id_batch, $id_jobrole);
        $penilaianByShift = $this->groupByShift($penilaian);

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Hitung panjang kolom (jumlah kolom total, termasuk tambahan jobdesc dan lainnya)
        $jobdesc = $this->getJobDesc($id_jobrole);
        $jobdescCount = count($jobdesc);
        $totalColumns = 6 + $jobdescCount + 3; // 6 kolom awal + jobdesc + nilai, rata-rata, grade
        $lastColumn = Coordinate::stringFromColumnIndex($totalColumns);

        // Header utama dengan border dan teks rata kiri
        $sheet->mergeCells("A1:{$lastColumn}1");
        $sheet->setCellValue('A1', 'LAPORAN PENILAIAN MANDOR');
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
        $sheet->getStyle('A1')->getAlignment()
            ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT)
            ->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);
        $this->setStyles($sheet, 'A1', "{$lastColumn}1");

        $row = 3;

        foreach ($penilaianByShift as $shift => $dataPerShift) {
            // Shift header dengan merge sesuai panjang kolom dan teks rata kiri
            $sheet->mergeCells("A{$row}:{$lastColumn}{$row}");
            $sheet->setCellValue("A{$row}", "SHIFT $shift");
            $sheet->getStyle("A{$row}")->getFont()->setBold(true)->setSize(12);
            $sheet->getStyle("A{$row}")->getAlignment()
                ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT)
                ->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);
            $this->setStyles($sheet, "A{$row}", "{$lastColumn}{$row}");

            $row++;
            $startHeaderRow = $row;

            // Header kolom
            $this->setColumnHeaders($sheet, $row);
            $this->setJobDescHeaders($sheet, $jobdesc, 6, $row);
            $this->setAdditionalHeaders($sheet, 6, $jobdescCount, $row);

            $endHeaderCol = Coordinate::stringFromColumnIndex(6 + $jobdescCount + 3);
            $this->setStyles($sheet, "A{$row}", "$endHeaderCol{$row}"); // Border untuk header kolom

            $row++;
            $no = 1;
            $startRow = $row;

            foreach ($dataPerShift as $p) {
                $row = $this->setRowData($sheet, $p, $row, $no++, 6, $jobdescCount);
            }

            $endRow = $row - 1;

            // Border untuk data shift
            $this->setStyles($sheet, "A$startRow", "$endHeaderCol$endRow");

            $row++; // Spasi antar shift
        }

        // Auto-size columns
        foreach (range('A', $endHeaderCol) as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        $this->outputExcel($spreadsheet);
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
        ]);

        $sheet->getStyle("$startCell:$endCell")->getAlignment()
            ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT)
            ->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);
    }

    private function groupByShift(array $penilaian): array
    {
        $penilaianByShift = [];
        foreach ($penilaian as $p) {
            $penilaianByShift[$p['shift']][] = $p;
        }
        return $penilaianByShift;
    }

    private function setMainHeader($sheet)
    {
        $sheet->mergeCells('A1:J1');
        $sheet->setCellValue('A1', 'LAPORAN PENILAIAN MANDOR');
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
        $sheet->getStyle('A1')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
    }

    private function setShiftHeader($sheet, string $shift, int $row): int
    {
        $sheet->mergeCells("A{$row}:J{$row}");
        $sheet->setCellValue("A{$row}", "SHIFT $shift");
        $sheet->getStyle("A{$row}")->getFont()->setBold(true)->setSize(12);
        $sheet->getStyle("A{$row}")->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
        return ++$row;
    }

    private function setColumnHeaders($sheet, int $row)
    {
        $sheet->setCellValue("A{$row}", 'NO');
        $sheet->setCellValue("B{$row}", 'KODE KARTU');
        $sheet->setCellValue("C{$row}", 'NAMA KARYAWAN');
        $sheet->setCellValue("D{$row}", 'L/P');
        $sheet->setCellValue("E{$row}", 'TGL. MASUK KERJA');
        $sheet->setCellValue("F{$row}", 'BAGIAN');
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
                ->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);

            // Set auto-size agar kolom pas untuk teks vertikal
            $sheet->getColumnDimension($colLetter)->setWidth(5); // Atur manual lebar kolom
        }

        // Tambahkan border pada header jobdesc
        $lastJobdescCol = Coordinate::stringFromColumnIndex($jobdescStartCol + count($jobdesc));
        $this->setStyles($sheet, "{$colLetter}{$row}", "$lastJobdescCol{$row}");
    }



    private function setAdditionalHeaders($sheet, int $jobdescStartCol, int $jobdescCount, int $row)
    {
        $nilaiCol = Coordinate::stringFromColumnIndex($jobdescStartCol + $jobdescCount);
        $sheet->setCellValue($nilaiCol . $row, 'NILAI');

        $averageCol = Coordinate::stringFromColumnIndex($jobdescStartCol + $jobdescCount + 1);
        $sheet->setCellValue($averageCol . $row, 'RATA-RATA');

        $gradeCol = Coordinate::stringFromColumnIndex($jobdescStartCol + $jobdescCount + 2);
        $sheet->setCellValue($gradeCol . $row, 'GRADE');
    }

    private function setRowData($sheet, array $p, int $row, int $no, int $jobdescStartCol, int $jobdescCount): int
    {
        $sheet->setCellValue("A{$row}", $no);
        $sheet->setCellValue("B{$row}", $p['kode_kartu']);
        $sheet->setCellValue("C{$row}", $p['nama_karyawan']);
        $sheet->setCellValue("D{$row}", $p['jenis_kelamin']);
        $sheet->setCellValue("E{$row}", $p['tgl_masuk']);
        $sheet->setCellValue("F{$row}", $p['nama_bagian']);

        $nilai = json_decode($p['bobot_nilai'] ?? '[]', true);
        $totalNilai = 0;
        $totalBobot = 0;

        if (is_array($nilai)) {
            foreach ($nilai as $value) {
                $totalNilai += $value;
                $totalBobot += self::bobot_nilai[$value];
            }

            $average = $totalBobot / count($nilai);
            $grade = $this->calculateGrade($average);

            $colIndex = $jobdescStartCol + 1;
            foreach ($nilai as $value) {
                $colLetter = Coordinate::stringFromColumnIndex($colIndex++);
                $sheet->setCellValue($colLetter . $row, $value);
            }

            $colLetter = Coordinate::stringFromColumnIndex($colIndex++);
            $sheet->setCellValue($colLetter . $row, $average);

            $colLetter = Coordinate::stringFromColumnIndex($colIndex);
            $sheet->setCellValue($colLetter . $row, $grade);
        }

        return ++$row;
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

    public function exelReportBatch($id_batch, $area_utama)
    {
        $id_batch = (int)$id_batch;

        // Ambil data penilaian
        $reportbatch = $this->penilaianmodel->getPenilaianGroupByBatchAndAreaByIdBatch($id_batch, $area_utama);
        $getBulan = $this->penilaianmodel->getBatchGroupByBulanPenilaian();
        $getAreaUtama = $this->bagianmodel->getAreaGroupByAreaUtama();

        // Ambil daftar bagian unik
        $bagianList = array_unique(array_column($reportbatch, 'nama_bagian'));

        // Membuat file Excel
        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();

        foreach ($bagianList as $bagian) {
            $dataBagian = array_filter($reportbatch, function ($item) use ($bagian) {
                return $item['nama_bagian'] === $bagian;
            });

            $sheet = $spreadsheet->createSheet();
            $sheet->setTitle($bagian);

            // Header utama
            $sheet->mergeCells('A1:L1');
            $sheet->setCellValue('A1', 'REPORT PENILAIAN ' . $bagian . ' AREA ' . $area_utama);
            $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
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

            $sheet->getStyle('A3:' . chr(ord($endColBulan) + 3) . '4')
                ->getBorders()
                ->getAllBorders()
                ->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);

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
                $average = round($total / $count, 2);
                $grade = $this->calculateGradeBatch($average);

                // Tambahkan kolom rata-rata dan grade ke data
                $data[] = ''; // Kolom tambahan (bisa untuk catatan)
                $data[] = $average;
                $data[] = $grade;

                $sheet->fromArray($data, null, 'A' . $row);
                $sheet->getStyle('A' . $row . ':' . chr(ord($endColBulan) + 3) . $row)
                    ->getBorders()
                    ->getAllBorders()
                    ->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
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

            $sheet->getStyle('A' . $row . ':' . chr(ord($endColBulan) + 3) . $row)
                ->getBorders()
                ->getAllBorders()
                ->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);

            // Atur kolom yang harus di tengah
            $columnsCenter = ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L'];
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

            // Wrap text
            $sheet->getStyle('B3:B4')->getAlignment()->setWrapText(true);
            $sheet->getStyle('C3:C4')->getAlignment()->setWrapText(true);
            $sheet->getStyle('E3:E4')->getAlignment()->setWrapText(true);
            $sheet->getStyle('J3:J4')->getAlignment()->setWrapText(true);
            $sheet->getStyle('K3:K4')->getAlignment()->setWrapText(true);
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
}
