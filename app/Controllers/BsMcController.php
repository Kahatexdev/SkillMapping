<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\BsmcModel;
use App\Models\BatchModel;
use CodeIgniter\HTTP\ResponseInterface;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Style\Border;


use App\Models\KaryawanModel;

class BsMcController extends BaseController
{
    protected $bsmcModel;
    protected $karyawanModel;
    protected $batchModel;

    public function __construct()
    {

        $this->bsmcModel = new BsmcModel();
        $this->karyawanModel = new KaryawanModel();
        $this->batchModel = new BatchModel();
    }

    public function downloadTemplate()
    {
        // Membuat spreadsheet baru
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Menyusun header kolom
        $sheet->setCellValue('A1', 'KODE KARTU');
        $sheet->setCellValue('B1', 'NAMA LENGKAP');
        $sheet->setCellValue('C1', 'L/P');
        $sheet->setCellValue('D1', 'TGL. MASUK KERJA');
        $sheet->setCellValue('E1', 'BAGIAN');
        $sheet->setCellValue('F1', 'RATA-RATA PRODUKSI');
        $sheet->setCellValue('G1', 'RATA-RATA BS');

        // Mengatur lebar kolom
        $sheet->getColumnDimension('A')->setWidth(20);
        $sheet->getColumnDimension('B')->setWidth(20);
        $sheet->getColumnDimension('C')->setWidth(20);
        $sheet->getColumnDimension('D')->setWidth(20);
        $sheet->getColumnDimension('E')->setWidth(20);
        $sheet->getColumnDimension('F')->setWidth(20);
        $sheet->getColumnDimension('G')->setWidth(20);



        // Mengatur style header
        $sheet->getStyle('A1:G1')->getFont()->setBold(true);
        $sheet->getStyle('A1:G1')->getFill()->setFillType('solid')->getStartColor()->setARGB('FFA0A0A0');
        $sheet->getStyle('A1:G1')->getAlignment()->setHorizontal('center');

        // isi data
        $sheet->setCellValue('A2', 'KK001');
        $sheet->setCellValue('B2', 'John Doe');
        $sheet->setCellValue('C2', 'L');
        $sheet->setCellValue('D2', '24/05/2023');
        $sheet->setCellValue('E2', 'KNITTER');
        $sheet->setCellValue('F2', '515');
        $sheet->setCellValue('G2', '2');

        // 
        // Menentukan nama file
        $fileName = 'Template_Summary_Bs_Mesin.xlsx';

        // Set header untuk unduhan file
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header("Content-Disposition: attachment; filename=\"{$fileName}\"");
        header('Cache-Control: max-age=0');

        // Buat file Excel dan kirim sebagai unduhan
        $writer = new Xlsx($spreadsheet);
        $writer->save('php://output');
        exit;
    }

    // public function upload()
    // {
    //     $file = $this->request->getFile('file');
    //     if ($file && $file->isValid() && !$file->hasMoved()) {
    //         $fileType = $file->getClientMimeType();
    //         if (!in_array($fileType, ['application/vnd.ms-excel', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'])) {
    //             return redirect()->to(base_url('monitoring/dataBsmc'))->with('error', 'Invalid file type. Please upload an Excel file.');
    //         }

    //         $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($file->getTempName());
    //         $dataSheet = $spreadsheet->getActiveSheet();
    //         $startRow = 34;

    //         $karyawanModel = new \App\Models\KaryawanModel();
    //         $bsmcModel = new \App\Models\BsmcModel();

    //         $successCount = 0;
    //         $errorCount = 0;
    //         $errorMessages = [];

    //         $nameSheet = $spreadsheet->getSheetNames();
    //         // dd ($nameSheet);
    //         $sheet = $spreadsheet->getSheetByName($nameSheet[2]);
    //         $highestRow = $sheet->getHighestRow();
    //         $highestColumn = $sheet->getHighestColumn();

    //         for ($row = $startRow; $row <= $highestRow; $row++) {
    //             $rowData = $sheet->rangeToArray('A' . $row . ':' . $highestColumn . $row, NULL, TRUE, FALSE)[0];
    //             $namaKaryawan = $rowData[4];
    //             $tanggal = \date('Y-m-d', strtotime($rowData[6]));
    //             $noModel = $rowData[10];
    //             $inisial = $rowData[11];
    //             $qtyProdMc = $rowData[5];
    //             $qtyBs = $rowData[20];

    //             dd($namaKaryawan, $tanggal, $noModel, $inisial, $qtyProdMc, $qtyBs);
    //             $karyawan = $karyawanModel->where('kode_kartu', $kodeKartu)->first();
    //             if ($karyawan) {
    //                 $bsmcModel->insert([
    //                     'id_karyawan' => $karyawan['id_karyawan'],
    //                     'tanggal' => $tanggal,
    //                     'no_model' => $noModel,
    //                     'inisial' => $inisial,
    //                     'qty_prod_mc' => $qtyProdMc,
    //                     'qty_bs' => $qtyBs
    //                 ]);
    //                 $successCount++;
    //             } else {
    //                 $errorCount++;
    //                 $errorMessages[] = "Row $row: Kode Kartu $kodeKartu not found.";
    //             }
    //         }

    //         $message = 'Data uploaded successfully.';
    //         if ($successCount > 0) {
    //             $message .= " $successCount data successfully uploaded.";
    //         }
    //         if ($errorCount > 0) {
    //             $message .= " $errorCount data failed to upload.";
    //         }
    //         if (!empty($errorMessages)) {
    //             $message .= "<br>" . implode("<br>", $errorMessages);
    //         }

    //         return redirect()->to(base_url('monitoring/dataBsmc'))->with('success', $message);
    //     } else {
    //         return redirect()->to(base_url('monitoring/dataBsmc'))->with('error', 'Invalid file.');
    //     }
    // }

    public function upload()
    {
        $file = $this->request->getFile('file');
        if ($file && $file->isValid() && !$file->hasMoved()) {
            $fileType = $file->getClientMimeType();
            if (!in_array($fileType, ['application/vnd.ms-excel', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'])) {
                return redirect()->to(base_url('Monitoring/dataBsmc'))->with('error', 'Invalid file type. Please upload an Excel file.');
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
                    $this->bsmcModel->insert($data);

                    $successMessage = "Summary BS Mesin berhasil disimpan.";
                    $successCount++;
                } else {
                    $errorMessages[] = $errorMessage;
                    $errorCount++;
                }
            }
            // Jika ada data yang gagal disimpan
            if ($errorCount > 0) {
                $errorMessages = implode("<br>", $errorMessages);
                return redirect()->to(base_url('Monitoring/dataBsmc'))->with('error', "{$errorCount} data gagal disimpan. <br>{$errorMessages}");
            } else {
                return redirect()->to(base_url('Monitoring/dataBsmc'))->with('success', "{$successCount} data berhasil disimpan.");
            }
        } else {
            return redirect()->to(base_url('Monitoring/dataBsmc'))->with('error', 'Invalid file.');
        }
    }

    public function fetchDataAPI()
    {
        $client = \Config\Services::curlrequest();
        $response = $client->request('GET', 'http://localhost:8080/api/bsmc');
        $data = json_decode($response->getBody(), true);

        $bsmcModel = new \App\Models\BsmcModel();
        $karyawanModel = new \App\Models\KaryawanModel();

        $successCount = 0;
        $errorCount = 0;
        $errorMessages = [];

        foreach ($data as $row) {
            $namaKaryawan = $row['nama_karyawan'];
            $tanggal = date('Y-m-d', strtotime($row['tanggal']));
            $noModel = $row['no_model'];
            $inisial = $row['inisial'];
            $qtyProdMc = $row['qty_prod_mc'];
            $qtyBs = $row['qty_bs'];

            $karyawan = $karyawanModel->where('nama_karyawan', $namaKaryawan)->first();
            if ($karyawan) {
                $bsmcModel->insert([
                    'id_karyawan' => $karyawan['id_karyawan'],
                    'tanggal' => $tanggal,
                    'no_model' => $noModel,
                    'inisial' => $inisial,
                    'qty_prod_mc' => $qtyProdMc,
                    'qty_bs' => $qtyBs
                ]);
                $successCount++;
            } else {
                $errorCount++;
                $errorMessages[] = "Nama Karyawan $namaKaryawan not found.";
            }
        }

        $message = 'Data fetched successfully.';
        if ($successCount > 0) {
            $message .= " $successCount data successfully fetched.";
        }
        if ($errorCount > 0) {
            $message .= " $errorCount data failed to fetch.";
        }
        if (!empty($errorMessages)) {
            $message .= "<br>" . implode("<br>", $errorMessages);
        }

        return redirect()->to(base_url('monitoring/dataBsmc'))->with('success', $message);
    }

    public function index() {}

    public function create()
    {
        $karyawan = $this->karyawanModel->findAll();
        $data = [
            'role' => session()->get('role'),
            'title' => 'Bs Mesin',
            'active1' => '',
            'active2' => '',
            'active3' => '',
            'active4' => '',
            'active5' => '',
            'active6' => '',
            'active7' => 'active',
            'karyawan' => $karyawan
        ];
        return view('Bsmc/create', $data);
    }

    public function edit($id)
    {
        $bsmcModel = new \App\Models\BsmcModel();
        $karyawanModel = new \App\Models\KaryawanModel();
        $bsmc = $bsmcModel->find($id);
        // dd($id);
        $karyawan = $karyawanModel->findAll();
        $data = [
            'role' => session()->get('role'),
            'title' => 'Bs Mesin',
            'active1' => '',
            'active2' => '',
            'active3' => '',
            'active4' => '',
            'active5' => '',
            'active6' => '',
            'active7' => 'active',
            'bsmc' => $bsmc,
            'karyawan' => $karyawan
        ];
        return view('bsmc/edit', $data);
    }

    public function delete($id)
    {
        $bsmcModel = new \App\Models\BsmcModel();
        $bsmcModel->delete($id);

        return redirect()->to(base_url('monitoring/dataBsmc'))->with('success', 'Data karyawan berhasil dihapus.');
    }

    public function tampilPerBatch($area_utama)
    {
        $summaryBsmc = $this->bsmcModel->getDatabyAreaUtama($area_utama);
        $batch = $this->batchModel->getBatch();
        $data = [
            'role' => session()->get('role'),
            'title' => 'Bs Mesin',
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
            'summaryBsmc' => $summaryBsmc

        ];

        return view('Bsmc/tampilPerBatch', $data);
    }

    public function summaryBsmc($area_utama, $id_batch)
    {
        $summaryBsmc = $this->bsmcModel->getDatabyAreaUtamaAndPeriodeInBatch($area_utama, $id_batch);
        $namaBatch = $this->batchModel->where('id_batch', $id_batch)->first();
        // $id_batch = $this->request->getPost('id_batch');
        // dd ($summaryRosso);
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $sheet->mergeCells('A1:H2');
        $sheet->setCellValue('A1', 'REPORT SUMMARY BS MESIN');
        $sheet->getStyle('A1')->getAlignment()->setHorizontal('center');
        $sheet->getStyle('A1')->getFont()->setBold(true);
        $sheet->getStyle('A1')->getFont()->setUnderline(true);
        $sheet->getStyle('A1')->getFont()->setName('Times New Roman');
        $sheet->getStyle('A1')->getFont()->setSize(16);

        $sheet->mergeCells('A3:B3');
        $sheet->setCellValue('A3', 'AREA');
        $sheet->setCellValue('C3', ': ' . $area_utama);
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
        $sheet->mergeCells('G6:G7');
        $sheet->setCellValue('G6', 'RATA-RATA PRODUKSI');
        $sheet->mergeCells('H6:H7');
        $sheet->setCellValue('H6', 'RATA-RATA BS');

        $sheet->getStyle('A6:H7')->getFont()->setBold(true);
        $sheet->getStyle('A6:H7')->getFont()->setName('Times New Roman');
        $sheet->getStyle('A6:H7')->getFont()->setSize(10);
        $sheet->getStyle('A6:H7')->getAlignment()->setHorizontal('center');
        $sheet->getStyle('A6:H7')->getAlignment()->setVertical('center');
        $sheet->getStyle('A6:H7')->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
        $sheet->getStyle('A6:H7')->getAlignment()->setWrapText(true);

        $sheet->getColumnDimension('A')->setWidth(5);
        $sheet->getColumnDimension('B')->setWidth(10);
        $sheet->getColumnDimension('C')->setWidth(20);
        $sheet->getColumnDimension('D')->setWidth(5);
        $sheet->getColumnDimension('E')->setWidth(15);
        $sheet->getColumnDimension('F')->setWidth(10);
        $sheet->getColumnDimension('G')->setWidth(10);
        $sheet->getColumnDimension('H')->setWidth(10);

        $startRow = 8;
        $no = 1;

        foreach ($summaryBsmc as $row) {
            $sheet->setCellValue('A' . $startRow, $no);
            $sheet->setCellValue('B' . $startRow, $row['kode_kartu']);
            $sheet->setCellValue('C' . $startRow, $row['nama_karyawan']);
            $sheet->setCellValue('D' . $startRow, $row['jenis_kelamin']);
            $sheet->setCellValue('E' . $startRow, $row['tgl_masuk']);
            $sheet->setCellValue('F' . $startRow, $row['nama_bagian']);
            $sheet->setCellValue('G' . $startRow, $row['average_produksi']);
            $sheet->setCellValue('H' . $startRow, $row['average_bs']);

            $sheet->getStyle('A' . $startRow . ':H' . $startRow)->getFont()->setName('Times New Roman');
            $sheet->getStyle('A' . $startRow . ':H' . $startRow)->getFont()->setSize(10);
            $sheet->getStyle('A' . $startRow . ':H' . $startRow)->getAlignment()->setHorizontal('center');
            $sheet->getStyle('A' . $startRow . ':H' . $startRow)->getAlignment()->setVertical('center');
            $sheet->getStyle('A' . $startRow . ':H' . $startRow)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
            $sheet->getStyle('A' . $startRow . ':H' . $startRow)->getAlignment()->setWrapText(true);

            $no++;
            $startRow++;
        }

        // get 3 karyawan dengan max average produksi dan min average bs
        $getTop3 = $this->bsmcModel->getTop3Produksi($area_utama, $id_batch);
        // dd($getTop3);
        // Header untuk Top 3 Produksi
        $sheet->mergeCells('J6:Q6');
        $sheet->setCellValue('J6', 'TOP 3 PRODUKSI');
        $sheet->getStyle('J6')->getFont()->setBold(true);
        $sheet->getStyle('J6')->getFont()->setName('Times New Roman');
        $sheet->getStyle('J6')->getFont()->setSize(10);
        $sheet->getStyle('J6')->getAlignment()->setHorizontal('center');
        $sheet->getStyle('J6')->getAlignment()->setVertical('center');
        $sheet->getStyle('J6:Q6')->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);

        // Sub-header untuk kolom Top 3 Produksi
        $sheet->setCellValue('J7', 'NO');
        $sheet->setCellValue('K7', 'KODE KARTU');
        $sheet->setCellValue('L7', 'NAMA KARYAWAN');
        $sheet->setCellValue('M7', 'L/P');
        $sheet->setCellValue('N7', 'TGL MASUK');
        $sheet->setCellValue('O7', 'BAGIAN');
        $sheet->setCellValue('P7', 'AVG PRODUKSI');
        $sheet->setCellValue('Q7', 'AVG BS');

        $sheet->getStyle('J7:Q7')->getFont()->setBold(true);
        $sheet->getStyle('J7:Q7')->getFont()->setName('Times New Roman');
        $sheet->getStyle('J7:Q7')->getFont()->setSize(10);
        $sheet->getStyle('J7:Q7')->getAlignment()->setHorizontal('center');
        $sheet->getStyle('J7:Q7')->getAlignment()->setVertical('center');
        $sheet->getStyle('J7:Q7')->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
        $sheet->getStyle('J7:Q7')->getAlignment()->setWrapText(true);

        // column dimension
        $sheet->getColumnDimension('J')->setWidth(5);
        $sheet->getColumnDimension('K')->setWidth(10);
        $sheet->getColumnDimension('L')->setWidth(20);
        $sheet->getColumnDimension('M')->setWidth(5);
        $sheet->getColumnDimension('N')->setWidth(15);
        $sheet->getColumnDimension('O')->setWidth(10);
        $sheet->getColumnDimension('P')->setWidth(10);
        $sheet->getColumnDimension('Q')->setWidth(10);

        // header untuk top 3 min avg bs
        $sheet->mergeCells('S6:Z6');
        $sheet->setCellValue('S6', 'TOP 3 MIN AVG BS');
        $sheet->getStyle('S6')->getFont()->setBold(true);
        $sheet->getStyle('S6')->getFont()->setName('Times New Roman');
        $sheet->getStyle('S6')->getFont()->setSize(10);
        $sheet->getStyle('S6')->getAlignment()->setHorizontal('center');
        $sheet->getStyle('S6')->getAlignment()->setVertical('center');
        $sheet->getStyle('S6:Z6')->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);

        // Sub-header untuk kolom Top 3 Min Avg BS
        $sheet->setCellValue('S7', 'NO');
        $sheet->setCellValue('T7', 'KODE KARTU');
        $sheet->setCellValue('U7', 'NAMA KARYAWAN');
        $sheet->setCellValue('V7', 'L/P');
        $sheet->setCellValue('W7', 'TGL MASUK');
        $sheet->setCellValue('X7', 'BAGIAN');
        $sheet->setCellValue('Y7', 'AVG PRODUKSI');
        $sheet->setCellValue('Z7', 'AVG BS');

        $sheet->getStyle('S7:Z7')->getFont()->setBold(true);
        $sheet->getStyle('S7:Z7')->getFont()->setName('Times New Roman');
        $sheet->getStyle('S7:Z7')->getFont()->setSize(10);
        $sheet->getStyle('S7:Z7')->getAlignment()->setHorizontal('center');
        $sheet->getStyle('S7:Z7')->getAlignment()->setVertical('center');
        $sheet->getStyle('S7:Z7')->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
        // $sheet->getStyle('S7:Z7')->getAlignment()->setWrapText(true);


        // Data Top 3 Produksi
        $startRow = 8;
        $no = 1;
        foreach ($getTop3 as $row) {
            $sheet->setCellValue('J' . $startRow, $no);
            $sheet->setCellValue('K' . $startRow, $row['kode_kartu']);
            $sheet->setCellValue('L' . $startRow, $row['nama_karyawan']);
            $sheet->setCellValue('M' . $startRow, $row['jenis_kelamin']);
            $sheet->setCellValue('N' . $startRow, $row['tgl_masuk']);
            $sheet->setCellValue('O' . $startRow, $row['nama_bagian']);
            $sheet->setCellValue('P' . $startRow, $row['average_produksi']);
            $sheet->setCellValue('Q' . $startRow, $row['average_bs']);

            $sheet->getStyle('J' . $startRow . ':Q' . $startRow)->getFont()->setName('Times New Roman');
            $sheet->getStyle('J' . $startRow . ':Q' . $startRow)->getFont()->setSize(10);
            $sheet->getStyle('J' . $startRow . ':Q' . $startRow)->getAlignment()->setHorizontal('center');
            $sheet->getStyle('J' . $startRow . ':Q' . $startRow)->getAlignment()->setVertical('center');
            $sheet->getStyle('J' . $startRow . ':Q' . $startRow)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
            $sheet->getStyle('J' . $startRow . ':Q' . $startRow)->getAlignment()->setWrapText(true);

            $no++;
            $startRow++;
        }

        // Data Top 3 Min Avg BS
        $getMinAvgBS = $this->bsmcModel->getTop3LowestBS($area_utama, $id_batch);
        $startRow = 8;
        $no = 1;
        foreach ($getMinAvgBS as $row) {
            $sheet->setCellValue('S' . $startRow, $no);
            $sheet->setCellValue('T' . $startRow, $row['kode_kartu']);
            $sheet->setCellValue('U' . $startRow, $row['nama_karyawan']);
            $sheet->setCellValue('V' . $startRow, $row['jenis_kelamin']);
            $sheet->setCellValue('W' . $startRow, $row['tgl_masuk']);
            $sheet->setCellValue('X' . $startRow, $row['nama_bagian']);
            $sheet->setCellValue('Y' . $startRow, $row['average_produksi']);
            $sheet->setCellValue('Z' . $startRow, $row['average_bs']);

            $sheet->getStyle('S' . $startRow . ':Z' . $startRow)->getFont()->setName('Times New Roman');
            $sheet->getStyle('S' . $startRow . ':Z' . $startRow)->getFont()->setSize(10);
            $sheet->getStyle('S' . $startRow . ':Z' . $startRow)->getAlignment()->setHorizontal('center');
            $sheet->getStyle('S' . $startRow . ':Z' . $startRow)->getAlignment()->setVertical('center');
            $sheet->getStyle('S' . $startRow . ':Z' . $startRow)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
            $sheet->getStyle('S' . $startRow . ':Z' . $startRow)->getAlignment()->setWrapText(true);

            $no++;
            $startRow++;
        }

        $spreadsheet->getActiveSheet()->setTitle('REPORT SUMMARY BS MESIN');

        $filename = 'REPORT SUMMARY BS MESIN ' . date('d-m-Y H:i:s') . '.xlsx';

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');

        $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
        $writer->save('php://output');
        exit;
    }
}
