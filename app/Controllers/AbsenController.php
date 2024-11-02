<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\KaryawanModel;
use CodeIgniter\HTTP\ResponseInterface;
use App\Models\UserModel;
use App\Models\AbsenModel;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\IOFactory;


class AbsenController extends BaseController
{

    protected $absenmodel;

    public function __construct()
    {
        $this->absenmodel = new AbsenModel();
    }
    public function index() {}

    public function create()
    {
        $data = new AbsenModel();

        $datas = $data->getdata();

        $usermodel = new UserModel();
        $users = $usermodel->findAll();

        $data = [
            'role' => session()->get('role'),
            'title' => 'Absen',
            'active1' => '',
            'active2' => '',
            'active3' => '',
            'active4' => '',
            'active5' => 'active',
            'active6' => '',
            'datas' => $datas,
            'users' => $users
        ];

        return view('absen/create', $data);
    }

    public function store()
    {

        $absen = new AbsenModel();

        $data = [
            'id_karyawan' => $this->request->getPost('id_karyawan'),
            'tanggal' => $this->request->getPost('tanggal'),
            'ket_absen' => $this->request->getPost('ket_absen'),
            'id_user' => $this->request->getPost('id_user')
        ];

        if ($absen->insert($data)) {
            session()->setFlashdata('success', 'Data berhasil ditambahkan');
        } else {
            session()->setFlashdata('error', 'Data gagal ditambahkan');
        }

        return redirect()->to('/monitoring/dataAbsen');
    }

    public function edit($id)
    {
        $absen = new AbsenModel();
        $datajoin = $absen->getAbsenWithKaryawan();
        $usermodel = new UserModel();

        $users = $usermodel->findAll();
        $data = $absen->find($id);

        $data = [
            'role' => session()->get('role'),
            'title' => 'Absen',
            'active1' => '',
            'active2' => '',
            'active3' => '',
            'active4' => '',
            'active5' => 'active',
            'active6' => '',
            'data' => $data,
            'datajoin' => $datajoin,
            'users' => $users
        ];
        return view('absen/edit', $data);
    }

    public function update($id)
    {
        $absen = new AbsenModel();

        $data = [
            'id_karyawan' => $this->request->getPost('id_karyawan'),
            'tanggal' => $this->request->getPost('tanggal'),
            'ket_absen' => $this->request->getPost('ket_absen'),
            'id_user' => $this->request->getPost('id_user')
        ];

        if ($absen->update($id, $data)) {
            session()->setFlashdata('success', 'Data berhasil diubah');
        } else {
            session()->setFlashdata('error', 'Data gagal diubah');
        }

        return redirect()->to('/monitoring/dataAbsen');
    }

    public function delete($id)
    {
        $absen = new AbsenModel();

        if ($absen->delete($id)) {
            session()->setFlashdata('success', 'Data berhasil dihapus');
        } else {
            session()->setFlashdata('error', 'Data gagal dihapus');
        }

        return redirect()->to('/monitoring/dataAbsen');
    }

    public function import()
    {
        $data = [
            'role' => session()->get('role'),
            'title' => 'Absen',
            'active1' => '',
            'active2' => '',
            'active3' => '',
            'active4' => '',
            'active5' => 'active',
            'active6' => ''
        ];
        return view('absen/import', $data);
    }

    public function downloadTemplate()
    {
        // Membuat spreadsheet baru
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Menyusun header kolom
        $sheet->setCellValue('A1', 'Nama Karyawan');
        $sheet->setCellValue('B1', 'Tanggal');
        $sheet->setCellValue('C1', 'Keterangan Absen');
        $sheet->setCellValue('D1', 'Nama User');

        // Mengatur lebar kolom
        $sheet->getColumnDimension('A')->setWidth(20);
        $sheet->getColumnDimension('B')->setWidth(30);
        $sheet->getColumnDimension('C')->setWidth(15);
        $sheet->getColumnDimension('D')->setWidth(15);


        // Mengatur style header
        $sheet->getStyle('A1:D1')->getFont()->setBold(true);
        $sheet->getStyle('A1:D1')->getFill()->setFillType('solid')->getStartColor()->setARGB('FFA0A0A0');
        $sheet->getStyle('A1:D1')->getAlignment()->setHorizontal('center');

        // isi data
        $sheet->setCellValue('A2', 'Budi');
        $sheet->setCellValue('B2', '2021-01-01');
        $sheet->setCellValue('C2', 'Hadir');
        $sheet->setCellValue('D2', session()->get('username'));


        // Menentukan nama file
        $fileName = 'Template_Data_Absen.xlsx';

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

        // Check if the file is valid and has not been moved
        if ($file && $file->isValid() && !$file->hasMoved()) {
            $fileType = $file->getClientMimeType();

            // Validate the file type
            if (!in_array($fileType, ['application/vnd.ms-excel', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'])) {
                return redirect()->to(base_url('monitoring/karyawanImport'))->with('error', 'Invalid file type. Please upload an Excel file.');
            }

            // Load the spreadsheet
            $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($file->getTempName());
            $dataSheet = $spreadsheet->getActiveSheet();
            $startRow = 2; // Assuming the first row is for headers

            // Models
            $absenModel = new \App\Models\AbsenModel();
            $karyawanModel = new \App\Models\KaryawanModel();

            // Counters for success and errors
            $successCount = 0;
            $errorCount = 0;
            $errorMessages = [];

            // Iterate through each row of the spreadsheet
            for ($row = $startRow; $row <= $dataSheet->getHighestRow(); $row++) {
                $isValid = true;
                $errorMessage = "Row {$row}: ";

                // Get cell values
                $namaKaryawan = $dataSheet->getCell('A' . $row)->getValue();
                $tanggal = $dataSheet->getCell('B' . $row)->getValue();
                $KetAbsen = $dataSheet->getCell('C' . $row)->getValue();
                $namaUser = $dataSheet->getCell('D' . $row)->getValue();

                // Validate data
                if (empty($namaKaryawan)) {
                    $isValid = false;
                    $errorMessage .= "Nama Karyawan is required. ";
                }
                if (empty($tanggal)) {
                    $isValid = false;
                    $errorMessage .= "Tanggal is required. ";
                }
                if (empty($KetAbsen)) {
                    $isValid = false;
                    $errorMessage .= "Keterangan Absen is required. ";
                }
                if (empty($namaUser)) {
                    $isValid = false;
                    $errorMessage .= "Nama User is required. ";
                }

                // If valid, proceed to save
                if ($isValid) {
                    // Fetch the karyawan data
                    $karyawan = $karyawanModel->where('nama_karyawan', $namaKaryawan)->first();
                    if ($karyawan) {
                        // Prepare the data for saving
                        $data = [
                            'id_karyawan' => $karyawan['id_karyawan'], // Ensure this is the correct foreign key
                            'tanggal' => $tanggal,
                            'ket_absen' => $KetAbsen,
                            'id_user' => session()->get('id_user'),
                        ];

                        // kalau ada data karyawan dan tanggal absen sama maka tidak bisa diinputkan
                        $absen = $absenModel->where('id_karyawan', $karyawan['id_karyawan'])->where('tanggal', $tanggal)->first();
                        if ($absen) {
                            $isValid = false;
                            $errorMessage .= "Data absen sudah ada. ";
                        } 

                        // only save if the data is valid
                        if (!$isValid) {
                            $errorCount++;
                            $errorMessages[] = $errorMessage;
                        } else {
                            $absenModel->save($data);
                            $successCount++;
                        }
                    } else {
                        $isValid = false;
                        $errorMessage .= "Karyawan not found. ";
                        $errorCount++;
                        $errorMessages[] = $errorMessage;
                    }
                } else {
                    $errorCount++;
                    $errorMessages[] = $errorMessage;
                }
            }

            // Set flash messages
            if ($isValid) {
                return redirect()->to(base_url('monitoring/dataAbsen'))->with('success', 'Data absen berhasil diimport.');
            } else {
                return redirect()->to(base_url('monitoring/dataAbsen'))->with('error', 'Data absen gagal diimport. ');
            }

            return redirect()->to(base_url('monitoring/dataAbsen'))->with('success', 'Data absen berhasil diimport.');
        } else {
            return redirect()->to(base_url('monitoring/dataAbsen'))->with('error', 'Invalid file. Please upload an Excel file.');
        }
        
    }

    public function empty()
    {
        $absenModel = new \App\Models\AbsenModel();
        $absenModel->truncate();
        return redirect()->to(base_url('monitoring/dataAbsen'))->with('success', 'Data absen berhasil dikosongkan.');
    }
}