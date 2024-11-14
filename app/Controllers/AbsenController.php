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
    protected $karyawanmodel;


    public function __construct()
    {
        $this->absenmodel = new AbsenModel();
        $this->karyawanmodel = new KaryawanModel();
    }
    public function index() {}

    public function create()
    {
        $data = new AbsenModel();

        $datas = $data->getdata();
        $karyawan = new KaryawanModel();
        $karyawans = $karyawan->getIdKaryawan();
        // dd ($karyawans);

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
            'karyawans' => $karyawans,
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
            'izin' => $this->request->getPost('izin'),
            'sakit' => $this->request->getPost('sakit'),
            'mangkir' => $this->request->getPost('mangkir'),
            'cuti' => $this->request->getPost('cuti'),
            'id_user' => $this->request->getPost('id_user')
        ];
        // dd ($data);
        $tanggal = $this->karyawanmodel->where('id_karyawan', $data['id_karyawan'])->first()['tgl_masuk'];
        // dd ($tanggal);
        // validasi tanggal masuk karyawan
        if ($data['tanggal'] != $tanggal) {
            session()->setFlashdata('error', 'Tanggal tidak sama tanggal masuk karyawan');
            return redirect()->to('/monitoring/absenCreate');
        } else {
            if ($absen->insert($data)) {
                session()->setFlashdata('success', 'Data berhasil ditambahkan');
            } else {
                session()->setFlashdata('error', 'Data gagal ditambahkan');
            }
        }

        return redirect()->to('/monitoring/dataAbsen');
    }

    public function edit($id)
    {
        $datajoin = $this->absenmodel->getdata();
        $usermodel = new UserModel();

        $users = $usermodel->findAll();
        $data = $this->absenmodel->find($id);

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
            'izin' => $this->request->getPost('izin'),
            'sakit' => $this->request->getPost('sakit'),
            'mangkir' => $this->request->getPost('mangkir'),
            'cuti' => $this->request->getPost('cuti'),
            'id_user' => $this->request->getPost('id_user')
        ];

        $tanggal = $this->karyawanmodel->where('id_karyawan', $data['id_karyawan'])->first()['tgl_masuk'];
        // dd ($tanggal);
        // validasi tanggal masuk karyawan
        if ($data['tanggal'] != $tanggal) {
            session()->setFlashdata('error', 'Tanggal tidak sama tanggal masuk karyawan');
            return redirect()->to('/monitoring/absenEdit/' . $id);
        } else {
            if ($absen->update($id, $data)) {
                session()->setFlashdata('success', 'Data berhasil diubah');
            } else {
                session()->setFlashdata('error', 'Data gagal diubah');
            }
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
            $startRow = 3; // Assuming the first row is for headers

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
                $kodeKartu = $dataSheet->getCell('A' . $row)->getFormattedValue();
                $namaKaryawan = $dataSheet->getCell('D' . $row)->getValue();
                $tanggal = $dataSheet->getCell('G' . $row)->getFormattedValue();
                $sakit = $dataSheet->getCell('I' . $row)->getValue();
                $izin = $dataSheet->getCell('J' . $row)->getValue();
                $cuti = $dataSheet->getCell('K' . $row)->getValue();
                $mangkir = $dataSheet->getCell('L' . $row)->getValue();
                $idUser = session()->get('id_user');

                
                // dd ($kodeKartu, $namaKaryawan, $tanggal, $sakit, $izin, $cuti, $mangkir, $idUser);
                // Validate data
                // Validasi tangal 
                if (empty($tanggal)) {
                    $isValid = false;
                    $errorMessage .= "Tanggal harus diisi. ";
                } else {
                    $tanggal = date('Y-m-d', strtotime($tanggal));
                    // dd ($tanggal);
                    // Validasi format tanggal
                    if ($tanggal === false) {
                        $isValid = false;
                        $errorMessage .= "Tanggal  tidak valid. ";
                    } 
                }
                if (empty($namaKaryawan)) {
                    $isValid = false;
                    $errorMessage .= "Nama Karyawan is required. ";
                }
                if (empty($kodeKartu)) {
                    $isValid = false;
                    $errorMessage .= "Kode Kartu is required. ";
                }

                // If valid, proceed to save
                if ($isValid) {
                    // Fetch the karyawan data
                    $karyawan = $karyawanModel->where('nama_karyawan', $namaKaryawan)->first();
                    if ($karyawan) {
                        // Prepare the data for saving
                        $data = [
                            'id_karyawan' => $karyawan['id_karyawan'],
                            'tanggal' => $tanggal,
                            'sakit' => $sakit,
                            'izin' => $izin,
                            'cuti' => $cuti,
                            'mangkir' => $mangkir,
                            'id_user' => $idUser
                        ];

                        // kalau ada data karyawan dan tanggal absen sama maka tidak bisa diinputkan
                        $absen = $absenModel->where('id_karyawan', $karyawan['id_karyawan'])->where('tanggal', $tanggal)->first();
                        if ($absen) {
                            $isValid = false;
                            $errorMessage .= "Data absen sudah ada. ";
                        } else {
                            // Save the data
                            $absenModel->insert($data);
                            $successCount++;
                        }
                    } else {
                        $isValid = false;
                        $errorMessage .= "Karyawan tidak ditemukan. ";
                    }
                }

                // If invalid, add to error count and log the error message
                if (!$isValid) {
                    $errorCount++;
                    $errorMessages[] = $errorMessage;
                } 
            }

            // Redirect with success and error messages
            $message = "Data absen berhasil diupload. Total data: {$successCount}.";
            if ($errorCount > 0) {
                $message .= " Terdapat {$errorCount} data yang gagal diupload.";
                $message .= "<ul>";
                foreach ($errorMessages as $msg) {
                    $message .= "<li>{$msg}</li>";
                }
                $message .= "</ul>";
            }
            return redirect()->to(base_url('monitoring/dataAbsen'))->with('success', $message);
        } else {
            return redirect()->to(base_url('monitoring/dataAbsen'))->with('error', 'Invalid file. Please upload an Excel file.');
        }
        
    }

    public function absenReport()
    {
        $data = $this->absenmodel->getdata();

        // export data ke excel
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // header
        $sheet->setCellValue('A1', 'No');
        $sheet->setCellValue('B1', ' Nama Karyawan');
        $sheet->setCellValue('C1', 'Tanggal');
        $sheet->setCellValue('D1', 'Izin');
        $sheet->setCellValue('E1', 'Sakit');
        $sheet->setCellValue('F1', 'Mangkir');
        $sheet->setCellValue('G1', 'Cuti');
        $sheet->setCellValue('H1', 'Input By');
        $sheet->setCellValue('I1', 'Created At');
        $sheet->setCellValue('J1', 'Updated At');

        $no = 1;
        $column = 2;

        // style kolom manual
        $sheet->getColumnDimension('A')->setWidth(5);
        $sheet->getColumnDimension('B')->setWidth(20);
        $sheet->getColumnDimension('C')->setWidth(20);
        $sheet->getColumnDimension('D')->setWidth(10);
        $sheet->getColumnDimension('E')->setWidth(10);
        $sheet->getColumnDimension('F')->setWidth(10);
        $sheet->getColumnDimension('G')->setWidth(10);
        $sheet->getColumnDimension('H')->setWidth(20);
        $sheet->getColumnDimension('I')->setWidth(20);
        $sheet->getColumnDimension('J')->setWidth(20);
        
        $sheet->getStyle('A1:J1')->getFont()->setBold(true);
        $sheet->getStyle('A1:J1')->getFill()->setFillType('solid')->getStartColor()->setARGB('FFA0A0A0');
        $sheet->getStyle('A1:J1')->getAlignment()->setHorizontal('center');

        foreach ($data as $row) {
            $sheet->setCellValue('A' . $column, $no++);
            $sheet->setCellValue('B' . $column, $row['nama_karyawan']);
            $sheet->setCellValue('C' . $column, $row['tanggal']);
            $sheet->setCellValue('D' . $column, $row['izin']);
            $sheet->setCellValue('E' . $column, $row['sakit']);
            $sheet->setCellValue('F' . $column, $row['mangkir']);
            $sheet->setCellValue('G' . $column, $row['cuti']);
            $sheet->setCellValue('H' . $column, $row['username']);
            $sheet->setCellValue('I' . $column, $row['created_at']);
            $sheet->setCellValue('J' . $column, $row['updated_at']);

            $column++;
        }

        // Set the header
        $fileName = 'Data_Absen.xlsx';
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header("Content-Disposition: attachment; filename=\"{$fileName}\"");
        header('Cache-Control: max-age=0');

        // Save the file
        $writer = new Xlsx($spreadsheet);
        $writer->save('php://output');
        exit;
    }

    public function empty()
    {
        $absenModel = new \App\Models\AbsenModel();
        $absenModel->truncate();
        return redirect()->to(base_url('monitoring/dataAbsen'))->with('success', 'Data absen berhasil dikosongkan.');
    }
}