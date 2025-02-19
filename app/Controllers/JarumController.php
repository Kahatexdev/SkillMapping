<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use App\Models\SummaryJarumModel;
use App\Models\BatchModel;

class JarumController extends BaseController
{
    protected $summaryJarum;
    protected $batchModel;

    public function __construct()
    {
        $this->summaryJarum = new SummaryJarumModel();
        $this->batchModel = new BatchModel();
    }
    public function index()
    {
        //
    }

    public function tampilPerBatch($area_utama)
    {
        $summaryJarum = $this->summaryJarum->getDatabyAreaUtama($area_utama);
        $batch = $this->batchModel->getBatch();
        $data = [
            'role' => session()->get('role'),
            'title' => 'Jarum',
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
            'summaryJarum' => $summaryJarum

        ];

        return view('Jarum/tampilPerBatch', $data);
    }

    public function summaryJarum()
    {
        $data = [
            'role' => session()->get('role'),
            'title' => 'Jarum',
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
        return view('Jarum/summaryPerPeriode', $data);
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
        $sheet->setCellValue('F1', 'RATA-RATA PEMAKAIAN JARUM');

        // Mengatur lebar kolom
        $sheet->getColumnDimension('A')->setWidth(20);
        $sheet->getColumnDimension('B')->setWidth(20);
        $sheet->getColumnDimension('C')->setWidth(20);
        $sheet->getColumnDimension('D')->setWidth(20);
        $sheet->getColumnDimension('E')->setWidth(20);
        $sheet->getColumnDimension('F')->setWidth(20);



        // Mengatur style header
        $sheet->getStyle('A1:F1')->getFont()->setBold(true);
        $sheet->getStyle('A1:F1')->getFill()->setFillType('solid')->getStartColor()->setARGB('FFA0A0A0');
        $sheet->getStyle('A1:F1')->getAlignment()->setHorizontal('center');

        // isi data
        $sheet->setCellValue('A2', 'KK001');
        $sheet->setCellValue('B2', 'John Doe');
        $sheet->setCellValue('C2', 'L');
        $sheet->setCellValue('D2', '24/05/2023');
        $sheet->setCellValue('E2', 'KNITTER');
        $sheet->setCellValue('F2', '4');

        // 
        // Menentukan nama file
        $fileName = 'Template_Summary_Jarum.xlsx';

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
                return redirect()->to(base_url('Monitoring/dataJarum'))->with('error', 'Invalid file type. Please upload an Excel file.');
            }

            $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($file->getTempName());
            $dataSheet = $spreadsheet->getActiveSheet();
            $startRow = 2;

            $bagianModel = new \App\Models\BagianModel();
            $this->karyawanmodel = new \App\Models\KaryawanModel();
            // $this->summaryRosso = new \App\Models\SummaryRossoModel();

            $batch = $this->request->getPost('id_batch');
            // dd ($batch);
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
                $avgUsedNeedle = $dataSheet->getCell('F' . $row)->getValue();

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
                        'avg_used_needle' => $avgUsedNeedle
                    ];
                    // var_dump($data);

                    // dd ($data);
                    $this->summaryJarum->insert($data);

                    $successMessage = "Summary Jarum berhasil disimpan.";
                    $successCount++;
                } else {
                    $errorMessages[] = $errorMessage;
                    $errorCount++;
                }
            }
            // Jika ada data yang gagal disimpan
            if ($errorCount > 0) {
                $errorMessages = implode("<br>", $errorMessages);
                return redirect()->to(base_url('Monitoring/dataJarum'))->with('error', "{$errorCount} data gagal disimpan. <br>{$errorMessages}");
            } else {
                return redirect()->to(base_url('Monitoring/dataJarum'))->with('success', "{$successCount} data berhasil disimpan.");
            }
        } else {
            return redirect()->to(base_url('Monitoring/dataJarum'))->with('error', 'Invalid file.');
        }
    }

    public function excelSummaryJarum($area_utama, $id_batch)
    {
        $summaryJarum = $this->summaryJarum->getDatabyAreaUtamaAndPeriodeInBatch($area_utama, $id_batch);
        $namaBatch = $this->batchModel->where('id_batch', $id_batch)->first();
        // $id_batch = $this->request->getPost('id_batch');
        // dd ($summaryRosso);
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $sheet->mergeCells('A1:H2');
        $sheet->setCellValue('A1', 'REPORT SUMMARY JARUM');
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
        $sheet->setCellValue('G6', 'AVG USED NEEDLE');

        $sheet->getStyle('A6:G7')->getFont()->setBold(true);
        $sheet->getStyle('A6:G7')->getFont()->setName('Times New Roman');
        $sheet->getStyle('A6:G7')->getFont()->setSize(10);
        $sheet->getStyle('A6:G7')->getAlignment()->setHorizontal('center');
        $sheet->getStyle('A6:G7')->getAlignment()->setVertical('center');
        $sheet->getStyle('A6:G7')->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
        $sheet->getStyle('A6:G7')->getAlignment()->setWrapText(true);

        $sheet->getColumnDimension('A')->setWidth(5);
        $sheet->getColumnDimension('B')->setWidth(10);
        $sheet->getColumnDimension('C')->setWidth(20);
        $sheet->getColumnDimension('D')->setWidth(5);
        $sheet->getColumnDimension('E')->setWidth(15);
        $sheet->getColumnDimension('F')->setWidth(10);
        $sheet->getColumnDimension('G')->setWidth(10);

        $startRow = 8;
        $no = 1;

        foreach ($summaryJarum as $row) {
            $sheet->setCellValue('A' . $startRow, $no);
            $sheet->setCellValue('B' . $startRow, $row['kode_kartu']);
            $sheet->setCellValue('C' . $startRow, $row['nama_karyawan']);
            $sheet->setCellValue('D' . $startRow, $row['jenis_kelamin']);
            $sheet->setCellValue('E' . $startRow, $row['tgl_masuk']);
            $sheet->setCellValue('F' . $startRow, $row['nama_bagian']);
            $sheet->setCellValue('G' . $startRow, $row['avg_used_needle']);

            $sheet->getStyle('A' . $startRow . ':G' . $startRow)->getFont()->setName('Times New Roman');
            $sheet->getStyle('A' . $startRow . ':G' . $startRow)->getFont()->setSize(10);
            $sheet->getStyle('A' . $startRow . ':G' . $startRow)->getAlignment()->setHorizontal('center');
            $sheet->getStyle('A' . $startRow . ':G' . $startRow)->getAlignment()->setVertical('center');
            $sheet->getStyle('A' . $startRow . ':G' . $startRow)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
            $sheet->getStyle('A' . $startRow . ':G' . $startRow)->getAlignment()->setWrapText(true);

            $no++;
            $startRow++;
        }

        // get 3 karyawan dengan max average produksi dan min average bs
        $getTop3 = $this->summaryJarum->getTop3Produksi($area_utama, $id_batch);
        // dd($getTop3);
        // Header untuk Top 3 Produksi
        $sheet->mergeCells('J6:Q6');
        $sheet->setCellValue('J6', 'TOP 3 AVG USED NEEDLE');
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
        $sheet->setCellValue('P7', 'AVG USED NEEDLE');

        $sheet->getStyle('J7:P7')->getFont()->setBold(true);
        $sheet->getStyle('J7:P7')->getFont()->setName('Times New Roman');
        $sheet->getStyle('J7:P7')->getFont()->setSize(10);
        $sheet->getStyle('J7:P7')->getAlignment()->setHorizontal('center');
        $sheet->getStyle('J7:P7')->getAlignment()->setVertical('center');
        $sheet->getStyle('J7:P7')->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
        $sheet->getStyle('J7:P7')->getAlignment()->setWrapText(true);

        // column dimension
        $sheet->getColumnDimension('J')->setWidth(5);
        $sheet->getColumnDimension('K')->setWidth(10);
        $sheet->getColumnDimension('L')->setWidth(20);
        $sheet->getColumnDimension('M')->setWidth(5);
        $sheet->getColumnDimension('N')->setWidth(15);
        $sheet->getColumnDimension('O')->setWidth(10);
        $sheet->getColumnDimension('P')->setWidth(10);

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
            $sheet->setCellValue('P' . $startRow, $row['avg_used_needle']);

            $sheet->getStyle('J' . $startRow . ':P' . $startRow)->getFont()->setName('Times New Roman');
            $sheet->getStyle('J' . $startRow . ':P' . $startRow)->getFont()->setSize(10);
            $sheet->getStyle('J' . $startRow . ':P' . $startRow)->getAlignment()->setHorizontal('center');
            $sheet->getStyle('J' . $startRow . ':P' . $startRow)->getAlignment()->setVertical('center');
            $sheet->getStyle('J' . $startRow . ':P' . $startRow)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
            $sheet->getStyle('J' . $startRow . ':P' . $startRow)->getAlignment()->setWrapText(true);

            $no++;
            $startRow++;
        }



        $spreadsheet->getActiveSheet()->setTitle('REPORT SUMMARY JARUM');

        $filename = 'REPORT SUMMARY JARUM ' . date('d-m-Y H:i:s') . '.xlsx';

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');

        $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
        $writer->save('php://output');
        exit;
    }
}
