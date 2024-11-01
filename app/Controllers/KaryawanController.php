<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\IOFactory;

use App\Models\KaryawanModel;

class KaryawanController extends BaseController
{
    protected $request;

    public function __construct()
    {
        $this->request = \Config\Services::request();
        
    }
    public function index()
    {

    }

    public function import()
    {
        $data = [
            'role' => session()->get('role'),
            'title' => 'Karyawan',
            'active1' => '',
            'active2' => '',
            'active3' => '',
            'active4' => 'active',
            'active5' => '',
            'active6' => ''
        ];
        return view('Karyawan/import', $data);
    }

    public function downloadTemplate()
    {
        // Membuat spreadsheet baru
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Menyusun header kolom
        $sheet->setCellValue('A1', 'Kode Kartu');
        $sheet->setCellValue('B1', 'Nama Karyawan');
        $sheet->setCellValue('C1', 'Tanggal Masuk');
        $sheet->setCellValue('D1', 'Jenis Kelamin');
        $sheet->setCellValue('E1', 'Shift');
        $sheet->setCellValue('F1', 'Nama Bagian');

        // Mengatur lebar kolom
        $sheet->getColumnDimension('A')->setWidth(20);
        $sheet->getColumnDimension('B')->setWidth(30);
        $sheet->getColumnDimension('C')->setWidth(15);
        $sheet->getColumnDimension('D')->setWidth(15);
        $sheet->getColumnDimension('E')->setWidth(15);
        $sheet->getColumnDimension('F')->setWidth(30);


        // Mengatur style header
        $sheet->getStyle('A1:F1')->getFont()->setBold(true);
        $sheet->getStyle('A1:F1')->getFill()->setFillType('solid')->getStartColor()->setARGB('FFA0A0A0');
        $sheet->getStyle('A1:F1')->getAlignment()->setHorizontal('center');

        // isi data
        $sheet->setCellValue('A2', 'KK001');
        $sheet->setCellValue('B2', 'John Doe');
        $sheet->setCellValue('C2', '2021-01-01');
        $sheet->setCellValue('D2', 'L');
        $sheet->setCellValue('E2', 'A');
        $sheet->setCellValue('F2', 'KNITTER');

        
        // Menentukan nama file
        $fileName = 'Template_Data_Karyawan.xlsx';

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
        // dd ($file);
        if ($file && $file->isValid() && !$file->hasMoved()) {
            $fileType = $file->getClientMimeType();
            if (!in_array($fileType, ['application/vnd.ms-excel', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'])) {
                return redirect()->to(base_url('monitoring/karyawanImport'))->with('error', 'Invalid file type. Please upload an Excel file.');
            }

            $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($file->getTempName());
            $dataSheet = $spreadsheet->getActiveSheet();
            $startRow = 2;

            $karyawanModel = new \App\Models\KaryawanModel();
            $bagianModel = new \App\Models\BagianModel();

            $successCount = 0;
            $errorCount = 0;
            $errorMessages = [];

            for ($row = $startRow; $row <= $dataSheet->getHighestRow(); $row++) {
                // Deklarasi ulang $isValid pada setiap iterasi
                $isValid = true;
                $errorMessage = "Row {$row}: ";
                $kodeKartu = $dataSheet->getCell('A' . $row)->getValue();
                $namaKaryawan = $dataSheet->getCell('B' . $row)->getValue();
                $tanggalMasuk = $dataSheet->getCell('C' . $row)->getValue();
                $jenisKelamin = $dataSheet->getCell('D' . $row)->getValue();
                $shift = $dataSheet->getCell('E' . $row)->getValue();
                $namaBagian = $dataSheet->getCell('F' . $row)->getValue();
                

                // Validasi data per kolom
                if (empty($kodeKartu)) {
                    $isValid = false;
                    $errorMessage .= "Kode Kartu is required. ";
                }
                if (empty($namaKaryawan)) {
                    $isValid = false;
                    $errorMessage .= "Nama Karyawan is required. ";
                }
                if (empty($tanggalMasuk)) {
                    $isValid = false;
                    $errorMessage .= "Tanggal Masuk is required. ";
                }
                if (empty($jenisKelamin)) {
                    $isValid = false;
                    $errorMessage .= "Jenis Kelamin is required. ";
                }
                if (empty($shift)) {
                    $isValid = false;
                    $errorMessage .= "Shift is required. ";
                }
                if (empty($namaBagian)) {
                    $isValid = false;
                    $errorMessage .= "Nama Bagian is required. ";
                }
                

                if ($isValid) {
                    $bagian = $bagianModel->where('nama_bagian', $namaBagian)->first();
                    if ($bagian) {
                        $data = [
                            'kode_kartu' => $kodeKartu,
                            'nama_karyawan' => $namaKaryawan,
                            'tanggal_masuk' => $tanggalMasuk,
                            'jenis_kelamin' => $jenisKelamin,
                            'shift' => $shift,
                            'id_bagian' => $bagian['id_bagian']
                        ];
                        $karyawanModel->save($data);
                        $successCount++;
                    } else {
                        $isValid = false;
                        $errorMessage .= "Bagian not found. ";
                    }
                } else {
                    $errorCount++;
                    $errorMessages[] = $errorMessage;
                }
            }

            if ($errorCount > 0) {
                $message = "Imported {$successCount} data successfully. {$errorCount} data failed to import.";
                foreach ($errorMessages as $error) {
                    $message .= "<br>{$error}";
                }
                return redirect()->to(base_url('monitoring/karyawanImport'))->with('error', $message);
            } else {
                return redirect()->to(base_url('monitoring/karyawanImport'))->with('success', "Imported {$successCount} data successfully.");
            }
        } else {
            // dd ($file);
            return redirect()->to(base_url('monitoring/karyawanImport'))->with('error', 'File upload failed');  
        }

        
    }
}