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



class PenilaianController extends BaseController
{
    protected $penilaianmodel;
    protected $jobrolemodel;
    protected $bagianmodel;
    protected $batchmodel;
    protected $karyawanmodel;
    protected $periodeModel;
    protected $absenmodel;

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
        $karyawan = $this->karyawanmodel->getKaryawanByFilters($namaBagian, $areaUtama, $area);
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
        if (!$id_jobrole) {
            return redirect()->back()->with('error', 'Job role not found.');
        }

        // Decode jobdesc from JSON
        $jobdesc = json_decode($id_jobrole['jobdesc'], true) ?? [];
        if (empty($jobdesc)) {
            return redirect()->back()->with('error', 'Job description not available.');
        }

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
            'temp' => $temp
        ];

        // dd ($data);

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
        $totalColumns = 7 + $jobdescCount + 2; // 7 kolom awal + jobdesc + nilai, rata-rata, grade
        $lastColumn = Coordinate::stringFromColumnIndex($totalColumns);

        // Header utama
        $sheet->mergeCells("A1:{$lastColumn}2");
        $sheet->setCellValue('A1', 'LAPORAN PENILAIAN MANDOR BAGIAN ' . $penilaian[0]['nama_bagian'].' AREA '.$penilaian[0]['area_utama'].'-'.$penilaian[0]['area']);
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
        

        // Header ABSEN
        $starabsenColumn = Coordinate::stringFromColumnIndex(2 + $totalColumns);
        $absenColumn = Coordinate::stringFromColumnIndex(1 + $totalColumns + 9);
        // merge cell untuk absen dari baris $starabsenColumn1 : $absenColumn3
        // dd ($starabsenColumn, $absenColumn);
        $sheet->mergeCells("{$starabsenColumn}1:{$absenColumn}2");
        $sheet->setCellValue($starabsenColumn . '1', 'ABSEN');
        $sheet->getStyle($starabsenColumn . '1')->getFont()->setBold(true)->setSize(12);
        $this->setStyles($sheet, $starabsenColumn . '1', $absenColumn . '2');

        $row = 3;

        // **Header Kolom (Hanya sekali diatur di sini, sebelum iterasi shift)**
        $this->setColumnHeaders($sheet, $row);
        $this->setJobDescHeaders($sheet, $jobdesc, 7, $row);
        $this->setAdditionalHeaders($sheet, 7, $jobdescCount, $row);

        $endHeaderCol = Coordinate::stringFromColumnIndex(7 + $jobdescCount + 2);
        // dd ($endHeaderCol);
        // kurang 1 karena kolom terakhir tidak perlu border kanan
        $this->setStyles($sheet, "A{$row}", "$endHeaderCol{$row}"); // Border untuk header kolom
        
        $row++; // Mulai baris data setelah header

        foreach ($penilaianByShift as $shift => $dataPerShift) {
            // Shift header dengan merge sesuai panjang kolom
            $sheet->mergeCells("A{$row}:{$lastColumn}{$row}");
            $sheet->setCellValue("A{$row}", "SHIFT $shift");
            $sheet->getStyle("A{$row}")->getFont()->setBold(true)->setSize(12);

            $row++;
            $no = 1;
            $startRow = $row;

            foreach ($dataPerShift as $p) {
                $row = $this->setRowData($sheet, $p, $row, $no++, 7, $jobdescCount);
            }

            $endRow = $row - 1;
            // dd ($endRow);
            // Border untuk data shift
            // $this->setStyles($sheet, "A$startRow", "$endHeaderCol$endRow");

            $row++; // Spasi antar shift
        }

        // Auto-size columns
        // foreach (range('A', $endHeaderCol) as $col) {
        //     $sheet->getColumnDimension($col)->setAutoSize(true);
        // }

        // Tambahkan border di seluruh area yang digunakan
        $highestRow = $sheet->getHighestRow();
        $highestColumn = $sheet->getHighestColumn();
        // convert column index to number
        $highestColumn = Coordinate::columnIndexFromString($highestColumn);
        // dd ($highestRow, $highestColumn);
        $highestColumn = $highestColumn - 1; // Kurangi 1 kolom karena kolom terakhir tidak perlu border kanan
        // convert column number to index
        $highestColumn = $this->getColumnName($highestColumn);
        // dd ($highestColumn);
        // $this->setFullBorder($sheet, "A1:{$highestColumn}{$highestRow}");

        $this->outputExcel($spreadsheet);
        exit();
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
        dd (Coordinate::columnIndexFromString('H'), count($this->getJobDesc(1)), $jobdescEndCol, $this->getJobDesc(1));
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
                ->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);

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
