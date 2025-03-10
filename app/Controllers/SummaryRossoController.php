<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;
use App\Models\KaryawanModel;
use App\Models\AbsenModel;
use App\Models\UserModel;
use App\Models\SummaryRossoModel;
use App\Models\PeriodeModel;
use App\Models\PenilaianModel;
use App\Models\BatchModel;
use App\Models\BagianModel;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Style\Border;
use DateTime;

class SummaryRossoController extends BaseController
{
    protected $karyawanmodel;
    protected $absenmodel;
    protected $usermodel;
    protected $summaryRossoModel;
    protected $periodeModel;
    protected $penilaianmodel;
    protected $batchModel;
    protected $bagianModel;

    public function __construct()
    {
        $this->karyawanmodel = new KaryawanModel();
        $this->absenmodel = new AbsenModel();
        $this->usermodel = new UserModel();
        $this->summaryRosso = new SummaryRossoModel();
        $this->periodeModel = new PeriodeModel();
        $this->penilaianmodel = new PenilaianModel();
        $this->batchModel = new BatchModel();
        $this->bagianModel = new BagianModel();
    }

    public function downloadTemplate()
    {
        // Membuat spreadsheet baru
        $spreadsheet = new Spreadsheet();

        $titles = ['KK1', 'KK2', 'KK5', 'KK7', 'KK8', 'KK11'];
        foreach ($titles as $index => $title) {
            $sheet = $index === 0 ? $spreadsheet->getActiveSheet() : $spreadsheet->createSheet();
            $sheet->setTitle($title);

            // Menyusun header kolom
            $sheet->setCellValue('A1', 'TANGGAL');
            $sheet->setCellValue('B1', 'NAMA LENGKAP');
            $sheet->setCellValue('C1', 'KODE KARTU');
            $sheet->setCellValue('D1', 'PRODUKSI');
            $sheet->setCellValue('E1', 'PERBAIKAN');

            // Mengatur lebar kolom
            $sheet->getColumnDimension('A')->setWidth(20);
            $sheet->getColumnDimension('B')->setWidth(20);
            $sheet->getColumnDimension('C')->setWidth(20);
            $sheet->getColumnDimension('D')->setWidth(20);
            $sheet->getColumnDimension('E')->setWidth(20);

            // Mengatur style header
            $sheet->getStyle('A1:E1')->getFont()->setBold(true);
            $sheet->getStyle('A1:E1')->getFill()->setFillType('solid')->getStartColor()->setARGB('FFA0A0A0');
            $sheet->getStyle('A1:E1')->getAlignment()->setHorizontal('center');

            // Isi data contoh pada sheet pertama
            if ($index === 0) {
                $sheet->setCellValue('A2', '2025-01-25');
                $sheet->setCellValue('B2', 'John Doe');
                $sheet->setCellValue('C2', 'KK0001');
                $sheet->setCellValue('D2', '1000');
                $sheet->setCellValue('E2', '100');
            }
        }

        // Menentukan nama file
        $fileName = 'Template_Summary_Rosso.xlsx';

        // Set header untuk unduhan file
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header("Content-Disposition: attachment; filename=\"{$fileName}\"");
        header('Cache-Control: max-age=0');

        // Buat file Excel dan kirim sebagai unduhan
        $writer = new Xlsx($spreadsheet);
        $writer->save('php://output');
        exit;
    }

    public function upload()
    {
        $file = $this->request->getFile('file');
        if ($file && $file->isValid() && !$file->hasMoved()) {
            $fileType = $file->getClientMimeType();
            if (!in_array($fileType, ['application/vnd.ms-excel', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'])) {
                return redirect()->to(base_url('Monitoring/dataRosso'))->with('error', 'Invalid file type. Please upload an Excel file.');
            }

            $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($file->getTempName());
            $dataSheet = $spreadsheet->getActiveSheet();
            $startRow = 2;

            $bagianModel = new \App\Models\BagianModel();
            $this->karyawanmodel = new \App\Models\KaryawanModel();
            $this->summaryRosso = new \App\Models\SummaryRossoModel();

            $batch = $this->request->getPost('id_batch');
            $successCount = 0;
            $errorCount = 0;
            $errorMessages = [];

            for ($row = $startRow; $row <= $dataSheet->getHighestRow(); $row++) {
                $isValid = true;
                $errorMessage = "Row {$row}: ";

                $kodeKartu = $dataSheet->getCell('A' . $row)->getValue();
                $namaLengkap = $dataSheet->getCell('B' . $row)->getValue();
                $jenisKelamin = $dataSheet->getCell('C' . $row)->getValue();
                $tglMasukKerja = $dataSheet->getCell('D' . $row)->getFormattedValue();
                $bagian = $dataSheet->getCell('E' . $row)->getValue();
                $averageProduksi = $dataSheet->getCell('F' . $row)->getValue();
                $averageBS = $dataSheet->getCell('G' . $row)->getValue();

                if (empty($kodeKartu)) {
                    $isValid = false;
                    $errorMessage .= "Kode Kartu is required. ";
                } else {
                    $karyawan = $this->karyawanmodel->where('kode_kartu', $kodeKartu)->where('nama_karyawan', $namaLengkap)->first();
                    if (!$karyawan) {
                        $isValid = false;
                        $errorMessage .= "Kode Kartu not found. ";
                    }
                }

                if (empty($jenisKelamin) || !in_array($jenisKelamin, ['L', 'P'])) {
                    $isValid = false;
                    $errorMessage .= "Jenis Kelamin must be L or P. ";
                }

                if ($isValid) {
                    $data = [
                        'id_batch' => $batch,
                        'id_karyawan' => $karyawan['id_karyawan'],
                        'average_produksi' => $averageProduksi,
                        'average_bs' => $averageBS
                    ];
                    // var_dump($data);

                    // dd ($data);
                    $this->summaryRosso->insert($data);

                    $successMessage = "Data Rosso berhasil disimpan.";
                    $successCount++;
                } else {
                    $errorMessages[] = $errorMessage;
                    $errorCount++;
                }
            }
            // Jika ada data yang gagal disimpan
            if ($errorCount > 0) {
                $errorMessages = implode("<br>", $errorMessages);
                return redirect()->to(base_url('Monitoring/dataRosso'))->with('error', "{$errorCount} data gagal disimpan. <br>{$errorMessages}");
            } else {
                return redirect()->to(base_url('Monitoring/dataRosso'))->with('success', "{$successCount} data berhasil disimpan.");
            }
        } else {
            return redirect()->to(base_url('Monitoring/dataRosso'))->with('error', 'Invalid file.');
        }
    }


    public function index()
    {
        //
    }

    public function create()
    {
        $bagianModel = new \App\Models\BagianModel();
        $bagian = $bagianModel->where('nama_bagian', 'ROSSO')->findAll();
        $data = [
            'role' => session()->get('role'),
            'title' => 'Summary Rosso',
            'active1' => '',
            'active2' => '',
            'active3' => '',
            'active4' => '',
            'active5' => '',
            'active6' => '',
            'active7' => '',
            'active8' => 'active',
            'bagian' => $bagian
        ];
        return view('Rosso/create', $data);

    }

    public function store()
    {

        //tambahkan validasi stardate dan end date
        $karyawan = $this->karyawanmodel->where('id_karyawan', $this->request->getPost('id_karyawan'))->first();
        if (!$karyawan) {
            return redirect()->to(base_url('Monitoring/dataRosso'))->with('error', 'Id Karyawan not found.');
        } else {
            $data = [
                'id_periode' => $this->request->getPost('id_periode'),
                'id_karyawan' => $karyawan['id_karyawan'],
                'tgl_prod_rosso' => $this->request->getPost('tgl_prod_rosso'),
                'qty_bs' => $this->request->getPost('qty_bs'),
                'qty_prod_rosso' => $this->request->getPost('qty_prod_rosso')
            ];

            // dd($data);  
            $this->summaryRosso->insert($data);
            return redirect()->to(base_url('Monitoring/dataRosso'))->with('success', 'Data successfully added.');
        }
    }

    public function show($id_periode)
    {
        $summaryRosso = new SummaryRossoModel();
        $SummaryRosso = $summaryRosso->getRossoByPeriode($id_periode);
        $periode = $this->periodeModel->checkPeriode($id_periode);

        // Perhitungan rata-rata berdasarkan rentang hari
        foreach ($SummaryRosso as &$row) {
            $start_date = new DateTime($row['start_date']); // pastikan 'start_date' ada di hasil query
            $end_date = new DateTime($row['end_date']); // pastikan 'end_date' ada di hasil query
            $jml_libur = $row['jml_libur'];
            $days = $start_date->diff($end_date)->days + 1; // Hitung rentang hari (inklusif)
            $days -= $jml_libur; // Kurangi jumlah hari libur
            // dd($days);
            
            // Hitung rata-rata
            $row['avg_qty_prod_rosso'] = $days > 0 ? $row['total_qty_prod_rosso'] / $days : 0;
            $row['avg_qty_bs'] = $days > 0 ? $row['total_qty_bs'] / $days : 0;
        }
        $data = [
            'role' => session()->get('role'),
            'title' => 'Summary Rosso',
            'active1' => '',
            'active2' => '',
            'active3' => '',
            'active4' => '',
            'active5' => '',
            'active6' => '',
            'active7' => '',
            'active8' => 'active',
            'SummaryRosso' => $SummaryRosso,
            'periode' => $periode
        ];
        // dd ($SummaryRosso);
        return view('Rosso/show-detail', $data);
    }


    public function edit($id)
    {
        // $bagianModel = new \App\Models\BagianModel();
        // $bagian = $bagianModel->where('nama_bagian', 'ROSSO')->findAll();
        $summaryRosso = new SummaryRossoModel();
        $SummaryRosso = $summaryRosso->getDataById($id);
        $sr = $summaryRosso->getDataById($id);
        $data = [
            'role' => session()->get('role'),
            'title' => 'Summary Rosso',
            'active1' => '',
            'active2' => '',
            'active3' => '',
            'active4' => '',
            'active5' => '',
            'active6' => '',
            'active7' => '',
            'active8' => 'active',
            // 'bagian' => $bagian,
            'SummaryRosso' => $SummaryRosso,
            'sr' => $sr
        ];
        // dd ($data); 
        return view('summaryRosso/edit', $data);
    }

    public function update($id)
    {
        $karyawan = $this->karyawanmodel->where('id_karyawan', $this->request->getPost('id_karyawan'))->first();
        if (!$karyawan) {
            return redirect()->to(base_url('Monitoring/dataRosso'))->with('error', 'ID Karyawan not found.');
        } else {
            $data = [
                'id_karyawan' => $karyawan['id_karyawan'],
                'tgl_prod_rosso' => $this->request->getPost('tgl_prod_rosso'),
                'qty_bs' => $this->request->getPost('qty_bs'),
                'qty_prod_rosso' => $this->request->getPost('qty_prod_rosso')
            ];
            $this->summaryRosso->update($id, $data);
            return redirect()->to(base_url('Monitoring/dataRosso'))->with('success', 'Data successfully updated.');
        }
    }

    public function delete($id)
    {
        $this->summaryRosso->delete($id);
        return redirect()->to(base_url('Monitoring/dataRosso'))->with('success', 'Data successfully deleted.');
    }

    public function summaryRosso()
    {
        $data = [
            'role' => session()->get('role'),
            'title' => 'Rosso',
            'active1' => '',
            'active2' => '',
            'active3' => '',
            'active4' => '',
            'active5' => '',
            'active6' => '',
            'active7' => '',
            'active8' => 'active'
        ];

        // dd ($summaryRosso);
        return view('Rosso/summaryPerPeriode', $data);
    }

    public function tampilPerBatch($area_utama)
    {
        $summaryRosso = $this->summaryRosso->getDatabyAreaUtama($area_utama);
        $batch = $this->batchModel->getBatch();
        // dd ($summaryRosso);
        $data = [
            'role' => session()->get('role'),
            'title' => 'Rosso',
            'active1' => 'active',
            'active2' => '',
            'active3' => '',
            'active4' => '',
            'active5' => '',
            'active6' => '',
            'active7' => '',
            'active8' => '',
            'area_utama' => $area_utama,
            'batch' => $batch,
            'summaryRosso' => $summaryRosso
        ];

        return view('Rosso/tampilPerBatch', $data);
    }

    // public function excelSummaryRosso($area_utama, $id_batch)
    // {
        
    //     $summaryRosso = $this->summaryRosso->getDatabyAreaUtamaAndPeriodeInBatch($area_utama, $id_batch);
    //     $namaBatch = $this->batchModel->where('id_batch', $id_batch)->first();
    //     // $id_batch = $this->request->getPost('id_batch');
    //     // dd ($summaryRosso);
    //     $spreadsheet = new Spreadsheet();
    //     $sheet = $spreadsheet->getActiveSheet();

    //     $sheet->mergeCells('A1:H2');
    //     $sheet->setCellValue('A1', 'REPORT SUMMARY ROSSO');
    //     $sheet->getStyle('A1')->getAlignment()->setHorizontal('center');
    //     $sheet->getStyle('A1')->getFont()->setBold(true);
    //     $sheet->getStyle('A1')->getFont()->setUnderline(true);
    //     $sheet->getStyle('A1')->getFont()->setName('Times New Roman');
    //     $sheet->getStyle('A1')->getFont()->setSize(16);

    //     $sheet->mergeCells('A3:B3');
    //     $sheet->setCellValue('A3', 'AREA');
    //     $sheet->setCellValue('C3', ': '.$area_utama);
    //     $sheet->getStyle('A3:C3')->getFont()->setBold(true);

    //     $sheet->mergeCells('A4:B4');
    //     $sheet->setCellValue('A4', 'NAMA BATCH');
    //     $sheet->setCellValue('C4', ': '.$namaBatch['nama_batch']);
    //     $sheet->getStyle('A4:C4')->getFont()->setBold(true);
    //     $sheet->getStyle('A3:C4')->getFont()->setName('Times New Roman');
    //     $sheet->getStyle('A3:C4')->getFont()->setSize(12);


    //     $sheet->mergeCells('A6:A7');
    //     $sheet->setCellValue('A6', 'NO');
    //     $sheet->mergeCells('B6:B7');
    //     $sheet->setCellValue('B6', 'KODE KARTU');
    //     $sheet->mergeCells('C6:C7');
    //     $sheet->setCellValue('C6', 'NAMA LENGKAP');
    //     $sheet->mergeCells('D6:D7');
    //     $sheet->setCellValue('D6', 'L/P');
    //     $sheet->mergeCells('E6:E7');
    //     $sheet->setCellValue('E6', 'TGL. MASUK KERJA');
    //     $sheet->mergeCells('F6:F7');
    //     $sheet->setCellValue('F6', 'BAGIAN');
    //     $sheet->mergeCells('G6:G7');
    //     $sheet->setCellValue('G6', 'RATA-RATA PRODUKSI');
    //     $sheet->mergeCells('H6:H7');
    //     $sheet->setCellValue('H6', 'RATA-RATA BS');

    //     $sheet->getStyle('A6:H7')->getFont()->setBold(true);
    //     $sheet->getStyle('A6:H7')->getFont()->setName('Times New Roman');
    //     $sheet->getStyle('A6:H7')->getFont()->setSize(10);
    //     $sheet->getStyle('A6:H7')->getAlignment()->setHorizontal('center');
    //     $sheet->getStyle('A6:H7')->getAlignment()->setVertical('center');
    //     $sheet->getStyle('A6:H7')->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
    //     $sheet->getStyle('A6:H7')->getAlignment()->setWrapText(true);

    //     $sheet->getColumnDimension('A')->setWidth(5);
    //     $sheet->getColumnDimension('B')->setWidth(10);
    //     $sheet->getColumnDimension('C')->setWidth(20);
    //     $sheet->getColumnDimension('D')->setWidth(5);
    //     $sheet->getColumnDimension('E')->setWidth(15);
    //     $sheet->getColumnDimension('F')->setWidth(10);
    //     $sheet->getColumnDimension('G')->setWidth(10);
    //     $sheet->getColumnDimension('H')->setWidth(10);

    //     $startRow = 8;
    //     $no = 1;

    //     foreach ($summaryRosso as $row) {
    //         $sheet->setCellValue('A' . $startRow, $no);
    //         $sheet->setCellValue('B' . $startRow, $row['kode_kartu']);
    //         $sheet->setCellValue('C' . $startRow, $row['nama_karyawan']);
    //         $sheet->setCellValue('D' . $startRow, $row['jenis_kelamin']);
    //         $sheet->setCellValue('E' . $startRow, $row['tgl_masuk']);
    //         $sheet->setCellValue('F' . $startRow, $row['nama_bagian']);
    //         $sheet->setCellValue('G' . $startRow, $row['average_produksi']);
    //         $sheet->setCellValue('H' . $startRow, $row['average_bs']);

    //         $sheet->getStyle('A' . $startRow . ':H' . $startRow)->getFont()->setName('Times New Roman');
    //         $sheet->getStyle('A' . $startRow . ':H' . $startRow)->getFont()->setSize(10);
    //         $sheet->getStyle('A' . $startRow . ':H' . $startRow)->getAlignment()->setHorizontal('center');
    //         $sheet->getStyle('A' . $startRow . ':H' . $startRow)->getAlignment()->setVertical('center');
    //         $sheet->getStyle('A' . $startRow . ':H' . $startRow)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
    //         $sheet->getStyle('A' . $startRow . ':H' . $startRow)->getAlignment()->setWrapText(true);

    //         $no++;
    //         $startRow++;
    //     }

    //     // get 3 karyawan dengan max average produksi dan min average bs
    //     $getTop3 = $this->summaryRosso->getTop3Produksi($area_utama, $id_batch);
    //     // dd($getTop3);
    //     // Header untuk Top 3 Produksi
    //     $sheet->mergeCells('J6:Q6');
    //     $sheet->setCellValue('J6', 'TOP 3 PRODUKSI');
    //     $sheet->getStyle('J6')->getFont()->setBold(true);
    //     $sheet->getStyle('J6')->getFont()->setName('Times New Roman');
    //     $sheet->getStyle('J6')->getFont()->setSize(10);
    //     $sheet->getStyle('J6')->getAlignment()->setHorizontal('center');
    //     $sheet->getStyle('J6')->getAlignment()->setVertical('center');
    //     $sheet->getStyle('J6:Q6')->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);

    //     // Sub-header untuk kolom Top 3 Produksi
    //     $sheet->setCellValue('J7', 'NO');
    //     $sheet->setCellValue('K7', 'KODE KARTU');
    //     $sheet->setCellValue('L7', 'NAMA KARYAWAN');
    //     $sheet->setCellValue('M7', 'L/P');
    //     $sheet->setCellValue('N7', 'TGL MASUK');
    //     $sheet->setCellValue('O7', 'BAGIAN');
    //     $sheet->setCellValue('P7', 'AVG PRODUKSI');
    //     $sheet->setCellValue('Q7', 'AVG BS');

    //     $sheet->getStyle('J7:Q7')->getFont()->setBold(true);
    //     $sheet->getStyle('J7:Q7')->getFont()->setName('Times New Roman');
    //     $sheet->getStyle('J7:Q7')->getFont()->setSize(10);
    //     $sheet->getStyle('J7:Q7')->getAlignment()->setHorizontal('center');
    //     $sheet->getStyle('J7:Q7')->getAlignment()->setVertical('center');
    //     $sheet->getStyle('J7:Q7')->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
    //     $sheet->getStyle('J7:Q7')->getAlignment()->setWrapText(true);

    //     // column dimension
    //     $sheet->getColumnDimension('J')->setWidth(5);
    //     $sheet->getColumnDimension('K')->setWidth(10);
    //     $sheet->getColumnDimension('L')->setWidth(20);
    //     $sheet->getColumnDimension('M')->setWidth(5);
    //     $sheet->getColumnDimension('N')->setWidth(15);
    //     $sheet->getColumnDimension('O')->setWidth(10);
    //     $sheet->getColumnDimension('P')->setWidth(10);
    //     $sheet->getColumnDimension('Q')->setWidth(10);

    //     // Data Top 3 Produksi
    //     $startRow = 8;
    //     $no = 1;
    //     foreach ($getTop3 as $row) {
    //         $sheet->setCellValue('J' . $startRow, $no);
    //         $sheet->setCellValue('K' . $startRow, $row['kode_kartu']);
    //         $sheet->setCellValue('L' . $startRow, $row['nama_karyawan']);
    //         $sheet->setCellValue('M' . $startRow, $row['jenis_kelamin']);
    //         $sheet->setCellValue('N' . $startRow, $row['tgl_masuk']);
    //         $sheet->setCellValue('O' . $startRow, $row['nama_bagian']);
    //         $sheet->setCellValue('P' . $startRow, $row['average_produksi']);
    //         $sheet->setCellValue('Q' . $startRow, $row['average_bs']);

    //         $sheet->getStyle('J' . $startRow . ':Q' . $startRow)->getFont()->setName('Times New Roman');
    //         $sheet->getStyle('J' . $startRow . ':Q' . $startRow)->getFont()->setSize(10);
    //         $sheet->getStyle('J' . $startRow . ':Q' . $startRow)->getAlignment()->setHorizontal('center');
    //         $sheet->getStyle('J' . $startRow . ':Q' . $startRow)->getAlignment()->setVertical('center');
    //         $sheet->getStyle('J' . $startRow . ':Q' . $startRow)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
    //         $sheet->getStyle('J' . $startRow . ':Q' . $startRow)->getAlignment()->setWrapText(true);

    //         $no++;
    //         $startRow++;
    //     }



    //     $spreadsheet->getActiveSheet()->setTitle('REPORT SUMMARY ROSSO');

    //     $filename = 'REPORT SUMMARY ROSSO ' . date('d-m-Y H:i:s') . '.xlsx';

    //     header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    //     header('Content-Disposition: attachment;filename="' . $filename . '"');
    //     header('Cache-Control: max-age=0');

    //     $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
    //     $writer->save('php://output');
    //     exit;
    // }

    public function import()
    {
        $file = $this->request->getFile('file');

        if (!$file->isValid() || $file->getExtension() !== 'xlsx') {
            return redirect()->back()->with('error', 'File tidak valid atau format tidak didukung.');
        }

        try {
            $spreadsheet = IOFactory::load($file->getTempName());
            $sheets = $spreadsheet->getSheetNames();

            // Ambil semua data karyawan untuk mempercepat pencarian (berdasarkan kode_kartu)
            $karyawanData = $this->karyawanmodel->findAll();
            $karyawanMap = [];
            foreach ($karyawanData as $karyawan) {
                $karyawanMap[strtolower(trim($karyawan['kode_kartu']))] = [
                    'id_karyawan' => $karyawan['id_karyawan'],
                    'id_bagian' => $karyawan['id_bagian']
                ];
            }

            // Ambil data area berdasarkan id_bagian
            $bagianData = $this->bagianModel->findAll();
            $areaMap = [];
            foreach ($bagianData as $bagian) {
                $areaMap[$bagian['id_bagian']] = $bagian['area_utama'];
            }

            $dataToInsert = [];
            $duplicateEntries = [];
            $wrongAreaEntries = [];
            $invalidDateEntries = []; // Menyimpan data dengan tanggal melebihi hari ini
            // **Mengecek duplikat di dalam file Excel**
            $processedKodeKartu = []; // Menyimpan kode kartu yang sudah diproses dalam Excel
            $duplicateExcelEntries = []; // Menyimpan kode kartu yang duplikat dalam Excel
            $currentDate = date('Y-m-d'); // Tanggal hari ini

            foreach ($sheets as $sheetName) {
                $worksheet = $spreadsheet->getSheetByName($sheetName);
                $highestRow = $worksheet->getHighestRow();

                for ($row = 2; $row <= $highestRow; $row++) {
                    $tanggal = $worksheet->getCell('A' . $row)->getFormattedValue();
                    // $nama = $worksheet->getCell('B' . $row)->getValue();
                    $kode_kartu = strtolower(trim($worksheet->getCell('C' . $row)->getValue()));
                    $produksi = $worksheet->getCell('D' . $row)->getValue();
                    $perbaikan = $worksheet->getCell('E' . $row)->getValue();
                    $tgl_input = date('Y-m-d', strtotime(str_replace('/', '-', $tanggal)));

                    // **Cek apakah kode kartu sudah muncul sebelumnya dalam file Excel**
                    if (in_array($kode_kartu, $processedKodeKartu)) {
                        $duplicateExcelEntries[] = "Kode Kartu: $kode_kartu";
                        continue; // **Lewati data ini, karena sudah ada dalam file Excel**
                    }

                    // **Validasi tanggal input tidak boleh lebih dari tanggal hari ini**
                    if ($tgl_input > $currentDate) {
                        $invalidDateEntries[] = "Kode Kartu: $kode_kartu, Tanggal Input: $tgl_input (Melebihi hari ini)";
                        continue; // Lewati data jika tanggal lebih dari hari ini
                    }

                    // Validasi kode_kartu (pastikan ada di database)
                    if (!isset($karyawanMap[$kode_kartu])) {
                        continue; // Lewati jika kode kartu tidak ditemukan
                    }

                    $id_karyawan = $karyawanMap[$kode_kartu]['id_karyawan'];
                    $id_bagian = $karyawanMap[$kode_kartu]['id_bagian'];
                    $area = $areaMap[$id_bagian] ?? null;

                    // Validasi area (judul sheet harus sesuai dengan id_bagian)
                    if ($sheetName !== $area) {
                        $wrongAreaEntries[] = "Kode Kartu: $kode_kartu, Seharusnya: $area, Ditemukan di: $sheetName";
                        continue;
                    }

                    // **VALIDASI: Cek apakah data sudah ada di database**
                    $existingData = $this->summaryRosso->validasiKaryawan($tgl_input, $id_karyawan);

                    if ($existingData) {
                        $duplicateEntries[] = "Tanggal: $tgl_input, Kode Kartu: $kode_kartu, Area: $area";
                        continue; // Lewati jika data sudah ada
                    }

                    // Jika data valid, tambahkan ke array untuk insertBatch
                    $dataToInsert[] = [
                        'tgl_input' => $tgl_input,
                        'id_karyawan' => $id_karyawan,
                        'produksi' => is_numeric($produksi) ? $produksi : 0,
                        'perbaikan' => is_numeric($perbaikan) ? $perbaikan : 0,
                        'area' => $area,
                        'created_at' => date('Y-m-d H:i:s'),
                        'updated_at' => date('Y-m-d H:i:s')
                    ];
                    // **Tambahkan kode kartu ke array processedKodeKartu**
                    $processedKodeKartu[] = $kode_kartu;
                }
            }

            // Tampilkan Alert 
            $errorMessage = "";
            $successMessage = "";
            if (!empty($dataToInsert)) {
                $this->summaryRosso->insertBatch($dataToInsert);
                $successMessage .= " ✅" . count($dataToInsert) . " Data Rosso Berhasil Diimport" . "<br><br>";
            }
            if (!empty($duplicateExcelEntries)) {
                $errorMessage .= "⛔ Kode kartu duplikat:<br>" . implode("<br>", $duplicateExcelEntries) . "<br><br>";
            }
            if (!empty($invalidDateEntries)) {
                $errorMessage .= "⛔ Tanggal input tidak boleh lebih dari hari ini dan tidak diinput:<br>" . implode("<br>", $invalidDateEntries) . "<br><br>";
            }
            if (!empty($duplicateEntries)) {
                $errorMessage .= "⛔ Data karyawan sudah ada pada :<br>" . implode("<br>", $duplicateEntries) . "<br><br>";
            }
            if (!empty($wrongAreaEntries)) {
                $errorMessage .= "⚠️ Kode kartu berikut dimasukkan ke area yang salah dan tidak diinput:<br>" . implode("<br>", $wrongAreaEntries) . "<br><br>";
            }
            if (!empty($errorMessage)) {
                return redirect()->back()->with('error', $errorMessage);
            } else {
                return redirect()->back()->with('success', $successMessage);
            }
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Terjadi kesalahan saat membaca file: ' . $e->getMessage());
        }
    }

    public function excelSummaryRosso($area, $id_batch)
    {
        $sumRosso = $this->summaryRosso->getSummaryRosso($area, $id_batch);
        $namaBatch = $this->batchModel->where('id_batch', $id_batch)->first();
        $start_dates = array_column($sumRosso, 'end_date');
        // Konversi setiap start_date menjadi nama bulan
        $bulan = array_unique(array_map(fn($date) => date('F', strtotime($date)), $start_dates));
        // Urutkan bulan
        $month_order = ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'];
        usort($bulan, fn($a, $b) => array_search($a, $month_order) - array_search($b, $month_order));

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $sheet->mergeCells('A1:H2');
        $sheet->setCellValue('A1', 'REPORT SUMMARY BS DAN PRODUKSI ROSSO');
        $sheet->getStyle('A1')->getAlignment()->setHorizontal('center');
        $sheet->getStyle('A1')->getFont()->setBold(true);
        $sheet->getStyle('A1')->getFont()->setUnderline(true);
        $sheet->getStyle('A1')->getFont()->setName('Times New Roman');
        $sheet->getStyle('A1')->getFont()->setSize(16);

        $sheet->mergeCells('A3:B3');
        $sheet->setCellValue('A3', 'AREA');
        $sheet->setCellValue('C3', ': ' . $area);
        $sheet->getStyle('A3:C3')->getFont()->setBold(true);

        $sheet->mergeCells('A4:B4');
        $sheet->setCellValue('A4', 'NAMA BATCH');
        $sheet->setCellValue('C4', ': ' . $namaBatch['nama_batch']);
        $sheet->getStyle('A4:C4')->getFont()->setBold(true);
        $sheet->getStyle('A3:C4')->getFont()->setName('Times New Roman');
        $sheet->getStyle('A3:C4')->getFont()->setSize(12);


        $sheet->mergeCells('A6:A7');
        $sheet->setCellValue('A6', 'NO');
        $sheet->mergeCells('B6:B7');
        $sheet->setCellValue('B6', 'KODE KARTU');
        $sheet->mergeCells('C6:C7');
        $sheet->setCellValue('C6', 'NAMA LENGKAP');
        $sheet->mergeCells('D6:D7');
        $sheet->setCellValue('D6', 'L/P');
        $sheet->mergeCells('E6:E7');
        $sheet->setCellValue('E6', 'TGL. MASUK KERJA');
        $sheet->mergeCells('F6:F7');
        $sheet->setCellValue('F6', 'BAGIAN');
        $sheet->mergeCells('G6:I6');
        $sheet->setCellValue('G6', 'PRODUKSI');
        // Masukkan data bulan ke G7, H7, I7, dst.
        $col = 'G';
        foreach ($bulan as $index => $bln) {
            $sheet->setCellValue($col . '7', substr($bln, 0, 3)); // Gunakan 3 huruf awal bulan
            $col++;
        }
        $sheet->mergeCells('J6:J7');
        $sheet->setCellValue('J6', 'RATA-RATA PRODUKSI');
        $sheet->mergeCells('K6:M6');
        $sheet->setCellValue('K6', 'PERBAIKAN');
        // Masukkan data bulan ke K7, L7, M7, dst.
        $col = 'K';
        foreach ($bulan as $index => $bln) {
            $sheet->setCellValue($col . '7', substr($bln, 0, 3));
            $col++;
        }
        $sheet->mergeCells('N6:N7');
        $sheet->setCellValue('N6', 'RATA-RATA PERBAIKAN');

        $sheet->getStyle('A6:N7')->getFont()->setBold(true);
        $sheet->getStyle('A6:N7')->getFont()->setName('Times New Roman');
        $sheet->getStyle('A6:N7')->getFont()->setSize(10);
        $sheet->getStyle('A6:N7')->getAlignment()->setHorizontal('center');
        $sheet->getStyle('A6:N7')->getAlignment()->setVertical('center');
        $sheet->getStyle('A6:N7')->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
        $sheet->getStyle('A6:N7')->getAlignment()->setWrapText(true);

        $sheet->getColumnDimension('A')->setWidth(5);
        $sheet->getColumnDimension('B')->setWidth(10);
        $sheet->getColumnDimension('C')->setWidth(20);
        $sheet->getColumnDimension('D')->setWidth(5);
        $sheet->getColumnDimension('E')->setWidth(15);
        $sheet->getColumnDimension('F')->setWidth(10);
        $sheet->getColumnDimension('J')->setWidth(15);
        $sheet->getColumnDimension('N')->setWidth(13);

        $startRow = 8;
        $no = 1;

        // Array untuk menyimpan data unik berdasarkan kode kartu
        $groupedData = [];

        // Proses data untuk mengelompokkan berdasarkan kode_kartu
        foreach ($sumRosso as $row) {
            $kode_kartu = $row['kode_kartu'];
            if (!isset($groupedData[$kode_kartu])) {
                // Jika kode kartu belum ada, simpan data awal
                $groupedData[$kode_kartu] = [
                    'kode_kartu'    => $row['kode_kartu'],
                    'nama_karyawan' => $row['nama_karyawan'],
                    'jenis_kelamin' => $row['jenis_kelamin'],
                    'tgl_masuk'     => $row['tgl_masuk'],
                    'nama_bagian'   => $row['nama_bagian'],
                    'produksi'      => array_fill_keys($bulan, 0), // Inisialisasi produksi per bulan
                    'perbaikan'     => array_fill_keys($bulan, 0), // Inisialisasi perbaikan per bulan
                    'hari_kerja'    => array_fill_keys($bulan, 0), // Inisialisasi hari kerja per bulan
                ];
            }
            // Menghitung jumlah hari kerja dalam bulan tersebut
            $startDate = new DateTime($row['start_date']);
            $endDate   = new DateTime($row['end_date']);
            $jumlahHari = $endDate->diff($startDate)->days + 1; // Total hari dalam periode
            $hariKerja = $jumlahHari - (int) $row['jml_libur']; // Hari kerja setelah dikurangi libur

            // Ambil jumlah hari kerja dari tabel periode
            $periode = $this->periodeModel->where('start_date <=', $row['end_date'])
                ->where('end_date >=', $row['end_date'])
                ->first();

            if ($periode) {
                $jumlah_hari_kerja = ((strtotime($periode['end_date']) - strtotime($periode['start_date'])) / (60 * 60 * 24)) + 1 - $periode['jml_libur'];
                if ($jumlah_hari_kerja > 0) {
                    $bulanData = date('F', strtotime($row['end_date']));
                    $groupedData[$kode_kartu]['produksi'][$bulanData] += round($row['total_produksi'] / $jumlah_hari_kerja);
                    $groupedData[$kode_kartu]['perbaikan'][$bulanData] += round($row['total_perbaikan'] / $jumlah_hari_kerja);
                }
            }
        }
        // Loop untuk memasukkan data ke dalam Excel
        foreach ($groupedData as $data) {
            $sheet->setCellValue('A' . $startRow, $no);
            $sheet->setCellValue('B' . $startRow, $data['kode_kartu']);
            $sheet->setCellValue('C' . $startRow, $data['nama_karyawan']);
            $sheet->setCellValue('D' . $startRow, $data['jenis_kelamin']);
            $sheet->setCellValue('E' . $startRow, $data['tgl_masuk']);
            $sheet->setCellValue('F' . $startRow, $data['nama_bagian']);

            // Kolom awal produksi dan Perbaikan
            $colProd = 'G';
            $colBS = 'K';
            $totalProduksi = 0;
            $totalBS = 0;
            $totalHariKerja = 0;
            $jumlahBulan = count($bulan);

            // Loop bulan untuk memasukkan produksi & perbaikan
            foreach ($bulan as $bln) {
                $produksiBulan = $data['produksi'][$bln];
                $bsBulan = $data['perbaikan'][$bln];
                $hariKerjaBulan = $data['hari_kerja'][$bln];

                $totalProduksi += $produksiBulan;
                $totalBS += $bsBulan;
                $totalHariKerja += $hariKerjaBulan;
                $sheet->setCellValue($colProd . $startRow, $produksiBulan);
                $sheet->setCellValue($colBS . $startRow, $bsBulan);

                // Geser ke kolom berikutnya
                $colProd++;
                $colBS++;
            }

            // Hitung rata-rata produksi dan Perbaikan berdasarkan 3 bulan
            $rataProduksiPerBatch = $jumlahBulan > 0 ? round($totalProduksi / $jumlahBulan) : 0;
            $rataBSPerBatch = $jumlahBulan > 0 ? round($totalBS / $jumlahBulan) : 0;

            // Masukkan rata-rata ke kolom yang sesuai
            $sheet->setCellValue('J' . $startRow, $rataProduksiPerBatch);
            $sheet->setCellValue('N' . $startRow, $rataBSPerBatch);

            $sheet->getStyle('A' . $startRow . ':N' . $startRow)->getFont()->setName('Times New Roman');
            $sheet->getStyle('A' . $startRow . ':N' . $startRow)->getFont()->setSize(10);
            $sheet->getStyle('A' . $startRow . ':N' . $startRow)->getAlignment()->setHorizontal('center');
            $sheet->getStyle('A' . $startRow . ':N' . $startRow)->getAlignment()->setVertical('center');
            $sheet->getStyle('A' . $startRow . ':N' . $startRow)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
            $sheet->getStyle('A' . $startRow . ':N' . $startRow)->getAlignment()->setWrapText(true);

            $no++;
            $startRow++;
        }

        // get 3 karyawan dengan max average produksi dan min average perbaikan

        // Header untuk Top 3 Produksi
        $sheet->mergeCells('Q6:X6');
        $sheet->setCellValue('Q6', 'TOP 3 PRODUKSI');
        $sheet->getStyle('Q6')->getFont()->setBold(true);
        $sheet->getStyle('Q6')->getFont()->setName('Times New Roman');
        $sheet->getStyle('Q6')->getFont()->setSize(10);
        $sheet->getStyle('Q6')->getAlignment()->setHorizontal('center');
        $sheet->getStyle('Q6')->getAlignment()->setVertical('center');
        $sheet->getStyle('Q6:X6')->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);

        // Sub-header untuk kolom Top 3 Produksi
        $sheet->setCellValue('Q7', 'NO');
        $sheet->setCellValue('R7', 'KODE KARTU');
        $sheet->setCellValue('S7', 'NAMA KARYAWAN');
        $sheet->setCellValue('T7', 'L/P');
        $sheet->setCellValue('U7', 'TGL MASUK');
        $sheet->setCellValue('V7', 'BAGIAN');
        $sheet->setCellValue('W7', 'AVG PRODUKSI');
        $sheet->setCellValue('X7', 'AVG PERBAIKAN');

        $sheet->getStyle('Q7:X7')->getFont()->setBold(true);
        $sheet->getStyle('Q7:X7')->getFont()->setName('Times New Roman');
        $sheet->getStyle('Q7:X7')->getFont()->setSize(10);
        $sheet->getStyle('Q7:X7')->getAlignment()->setHorizontal('center');
        $sheet->getStyle('Q7:X7')->getAlignment()->setVertical('center');
        $sheet->getStyle('Q7:X7')->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
        $sheet->getStyle('Q7:X7')->getAlignment()->setWrapText(true);

        // column dimension
        $sheet->getColumnDimension('Q')->setWidth(5);
        $sheet->getColumnDimension('R')->setWidth(10);
        $sheet->getColumnDimension('S')->setWidth(20);
        $sheet->getColumnDimension('T')->setWidth(5);
        $sheet->getColumnDimension('U')->setWidth(15);
        $sheet->getColumnDimension('V')->setWidth(10);
        $sheet->getColumnDimension('W')->setWidth(10);
        $sheet->getColumnDimension('X')->setWidth(10);

        // header untuk top 3 min avg perbaikan
        $sheet->mergeCells('Z6:AG6');
        $sheet->setCellValue('Z6', 'TOP 3 MIN AVG PERBAIKAN');
        $sheet->getStyle('Z6')->getFont()->setBold(true);
        $sheet->getStyle('Z6')->getFont()->setName('Times New Roman');
        $sheet->getStyle('Z6')->getFont()->setSize(10);
        $sheet->getStyle('Z6')->getAlignment()->setHorizontal('center');
        $sheet->getStyle('Z6')->getAlignment()->setVertical('center');
        $sheet->getStyle('Z6:AG6')->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);

        // Sub-header untuk kolom Top 3 Min Avg Perbaikan
        $sheet->setCellValue('Z7', 'NO');
        $sheet->setCellValue('AA7', 'KODE KARTU');
        $sheet->setCellValue('AB7', 'NAMA KARYAWAN');
        $sheet->setCellValue('AC7', 'L/P');
        $sheet->setCellValue('AD7', 'TGL MASUK');
        $sheet->setCellValue('AE7', 'BAGIAN');
        $sheet->setCellValue('AF7', 'AVG PRODUKSI');
        $sheet->setCellValue('AG7', 'AVG PERBAIKAN');

        $sheet->getStyle('Z7:AG7')->getFont()->setBold(true);
        $sheet->getStyle('Z7:AG7')->getFont()->setName('Times New Roman');
        $sheet->getStyle('Z7:AG7')->getFont()->setSize(10);
        $sheet->getStyle('Z7:AG7')->getAlignment()->setHorizontal('center');
        $sheet->getStyle('Z7:AG7')->getAlignment()->setVertical('center');
        $sheet->getStyle('Z7:AG7')->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
        $sheet->getStyle('Z7:AG7')->getAlignment()->setWrapText(true);


        // Data Top 3 Produksi
        // Urutkan data berdasarkan rata-rata produksi tertinggi
        usort($groupedData, function ($a, $b) {
            return array_sum($b['produksi']) <=> array_sum($a['produksi']); // Descending
        });
        $top3Produksi = array_slice($groupedData, 0, 3); // Ambil 3 terbesar

        $startRow = 8;
        $no = 1;
        foreach ($top3Produksi as $row) {

            $avgProduksi = array_sum($row['produksi']) / count($bulan);
            $avgBS = array_sum($row['perbaikan']) / count($bulan);

            $sheet->setCellValue('Q' . $startRow, $no);
            $sheet->setCellValue('R' . $startRow, $row['kode_kartu']);
            $sheet->setCellValue('S' . $startRow, $row['nama_karyawan']);
            $sheet->setCellValue('T' . $startRow, $row['jenis_kelamin']);
            $sheet->setCellValue('U' . $startRow, $row['tgl_masuk']);
            $sheet->setCellValue('V' . $startRow, $row['nama_bagian']);
            $sheet->setCellValue('W' . $startRow, round($avgProduksi));
            $sheet->setCellValue('X' . $startRow, round($avgBS));

            $sheet->getStyle('Q' . $startRow . ':X' . $startRow)->getFont()->setName('Times New Roman');
            $sheet->getStyle('Q' . $startRow . ':X' . $startRow)->getFont()->setSize(10);
            $sheet->getStyle('Q' . $startRow . ':X' . $startRow)->getAlignment()->setHorizontal('center');
            $sheet->getStyle('Q' . $startRow . ':X' . $startRow)->getAlignment()->setVertical('center');
            $sheet->getStyle('Q' . $startRow . ':X' . $startRow)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
            $sheet->getStyle('Q' . $startRow . ':X' . $startRow)->getAlignment()->setWrapText(true);

            $no++;
            $startRow++;
        }

        // Data Top 3 Min Avg Perbaikan

        // Urutkan berdasarkan produksi tertinggi
        usort($groupedData, function ($a, $b) {
            return array_sum($b['produksi']) <=> array_sum($a['produksi']); // Descending
        });
        // Ambil 7 data produksi tertinggi
        $top7Produksi = array_slice($groupedData, 0, 7);

        // Urutkan 7 data ini berdasarkan Perbaikan terkecil
        usort($top7Produksi, function ($a, $b) {
            return array_sum($a['perbaikan']) <=> array_sum($b['perbaikan']); // Ascending
        });
        // Ambil 3 data Perbaikan terkecil dari Top 7 Produksi
        $top3BS = array_slice($top7Produksi, 0, 3);

        // $getMinAvgBS = $this->bsmcModel->getTop3LowestBS($area_utama, $id_batch);
        $startRow = 8;
        $no = 1;
        foreach ($top3BS as $row) {

            $avgProduksi = array_sum($row['produksi']) / count($bulan);
            $avgBS = array_sum($row['perbaikan']) / count($bulan);

            $sheet->setCellValue('Z' . $startRow, $no);
            $sheet->setCellValue('AA' . $startRow, $row['kode_kartu']);
            $sheet->setCellValue('AB' . $startRow, $row['nama_karyawan']);
            $sheet->setCellValue('AC' . $startRow, $row['jenis_kelamin']);
            $sheet->setCellValue('AD' . $startRow, $row['tgl_masuk']);
            $sheet->setCellValue('AE' . $startRow, $row['nama_bagian']);
            $sheet->setCellValue('AF' . $startRow, round($avgProduksi));
            $sheet->setCellValue('AG' . $startRow, round($avgBS));

            $sheet->getStyle('Z' . $startRow . ':AG' . $startRow)->getFont()->setName('Times New Roman');
            $sheet->getStyle('Z' . $startRow . ':AG' . $startRow)->getFont()->setSize(10);
            $sheet->getStyle('Z' . $startRow . ':AG' . $startRow)->getAlignment()->setHorizontal('center');
            $sheet->getStyle('Z' . $startRow . ':AG' . $startRow)->getAlignment()->setVertical('center');
            $sheet->getStyle('Z' . $startRow . ':AG' . $startRow)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
            $sheet->getStyle('Z' . $startRow . ':AG' . $startRow)->getAlignment()->setWrapText(true);

            $no++;
            $startRow++;
        }

        $spreadsheet->getActiveSheet()->setTitle('REPORT SUMMARY ROSSO');

        $filename = 'REPORT SUMMARY BS DAN PRODUKSI ROSSO ' . date('d-m-Y H:i:s') . '.xlsx';

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');

        $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
        $writer->save('php://output');
        exit;
    }
}
