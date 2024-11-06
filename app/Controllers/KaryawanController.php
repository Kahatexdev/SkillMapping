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
    public function index() {}

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
        $sheet->setCellValue('C1', 'Shift');
        $sheet->setCellValue('D1', 'Jenis Kelamin');
        $sheet->setCellValue('E1', 'Libur');
        $sheet->setCellValue('F1', 'Libur Tambahan');
        $sheet->setCellValue('G1', 'Warna Baju');
        $sheet->setCellValue('H1', 'Status Baju');
        $sheet->setCellValue('I1', 'Tanggal Lahir');
        $sheet->setCellValue('J1', 'Tanggal Masuk');
        $sheet->setCellValue('K1', 'Nama Bagian');
        $sheet->setCellValue('L1', 'Area Utama');
        $sheet->setCellValue('M1', 'Area');
        $sheet->setCellValue('N1', 'Status Aktif');

        // Mengatur lebar kolom
        $sheet->getColumnDimension('A')->setWidth(20);
        $sheet->getColumnDimension('B')->setWidth(20);
        $sheet->getColumnDimension('C')->setWidth(20);
        $sheet->getColumnDimension('D')->setWidth(20);
        $sheet->getColumnDimension('E')->setWidth(20);
        $sheet->getColumnDimension('F')->setWidth(20);
        $sheet->getColumnDimension('G')->setWidth(20);
        $sheet->getColumnDimension('H')->setWidth(20);
        $sheet->getColumnDimension('I')->setWidth(20);
        $sheet->getColumnDimension('J')->setWidth(20);
        $sheet->getColumnDimension('K')->setWidth(20);
        $sheet->getColumnDimension('L')->setWidth(20);
        $sheet->getColumnDimension('M')->setWidth(20);
        $sheet->getColumnDimension('N')->setWidth(20);


        // Mengatur style header
        $sheet->getStyle('A1:N1')->getFont()->setBold(true);
        $sheet->getStyle('A1:N1')->getFill()->setFillType('solid')->getStartColor()->setARGB('FFA0A0A0');
        $sheet->getStyle('A1:N1')->getAlignment()->setHorizontal('center');

        // isi data
        $sheet->setCellValue('A2', 'KK001');
        $sheet->setCellValue('B2', 'John Doe');
        $sheet->setCellValue('C2', 'A');
        $sheet->setCellValue('D2', 'L');
        $sheet->setCellValue('E2', '2');
        $sheet->setCellValue('F2', '2');
        $sheet->setCellValue('G2', 'PINK');
        $sheet->setCellValue('H2', 'STAFF');
        $sheet->setCellValue('I2', '2001/09/12');
        $sheet->setCellValue('J2', '2024/09/12');
        $sheet->setCellValue('K2', 'KNITTER');
        $sheet->setCellValue('L2', 'KK1');
        $sheet->setCellValue('M2', 'KK1A');
        $sheet->setCellValue('N2', 'Aktif');


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
                $status = $dataSheet->getCell('G' . $row)->getValue();
                // dd($status);


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
                if (empty($status)) {
                    $isValid = false;
                    $errorMessage .= "Status is required. ";
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
                            'id_bagian' => $bagian['id_bagian'],
                            'status' => $status
                        ];
                        // Check if kode_kartu already exists in the database
                        $existingKaryawan = $karyawanModel->where('kode_kartu', $kodeKartu)->first();
                        // dd($existingKaryawan);   
                        if ($existingKaryawan) {
                            $isValid = false;
                            $errorMessage .= "Kode kartu already exists. ";
                        }

                        // Only save if valid
                        if (!$isValid) {
                            $isValid = false;
                            $errorMessage .= "Kode kartu already exists. ";
                        } else {
                            $karyawanModel->save($data);
                            $successCount++;
                        }
                    } else {
                        $isValid = false;
                        $errorMessage .= "Bagian not found. ";
                    }
                } else {
                    $errorCount++;
                    $errorMessages[] = $errorMessage;
                }
            }

            // kalau ada kartu yang sama maka tidak akan di save
            if ($isValid) {
                return redirect()->to(base_url('monitoring/datakaryawan'))->with('success', 'Data karyawan berhasil diimport.');
            } else {
                return redirect()->to(base_url('monitoring/datakaryawan'))->with('error', 'Data karyawan gagal diimport.');
            }

            return redirect()->to(base_url('monitoring/datakaryawan'))->with('success', 'Data karyawan berhasil diimport.');
        } else {
            return redirect()->to(base_url('monitoring/karyawanImport'))->with('error', 'Invalid file.');
        }
    }

    public function empty()
    {
        $karyawanModel = new \App\Models\KaryawanModel();
        $karyawanModel->truncate();
        return redirect()->to(base_url('monitoring/datakaryawan'))->with('success', 'Data karyawan berhasil dikosongkan.');
    }
}
