<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;
use App\Models\KaryawanModel;
use App\Models\BagianModel;
use App\Models\UserModel;
use App\Models\JobroleModel;
use App\Models\AbsenModel;
use PhpOffice\PhpSpreadsheet\IOFactory;

class MonitoringController extends BaseController
{
    protected $karyawanmodel;
    protected $bagianmodel;
    protected $usermodel;
    protected $jobrole;
    protected $absenmodel;

    public function __construct()
    {

        $this->karyawanmodel = new KaryawanModel();
        $this->bagianmodel = new BagianModel();
        $this->usermodel = new UserModel();
        $this->jobrole = new JobroleModel();
        $this->absenmodel = new AbsenModel();
    }
    public function index()
    {
        $data = [
            'role' => session()->get('role'),
            'title' => 'Dashboard',
            'active1' => 'active',
            'active2' => '',
            'active3' => '',
            'active4' => '',
            'active5' => '',
            'active6' => ''

        ];
        return view(session()->get('role') . '/index', $data);
    }
    public function karyawan()
    {
        $karyawan = $this->karyawanmodel->getdata();
        $data = [
            'role' => session()->get('role'),
            'title' => 'Karyawan',
            'active1' => '',
            'active2' => '',
            'active3' => '',
            'active4' => 'active',
            'active5' => '',
            'active6' => '',
            'karyawan' => $karyawan

        ];
        return view(session()->get('role') . '/karyawan', $data);
    }
    public function user()
    {
        $usermodels = new UserModel();

        $users = $usermodels->findAll();

        $data = [
            'role' => session()->get('role'),
            'title' => 'User',
            'active1' => '',
            'active2' => 'active',
            'active3' => '',
            'active4' => '',
            'active5' => '',
            'active6' => '',
            'users' => $users
        ];
        

        // dd($users);
        return view(session()->get('role') . '/user', $data);
    }
    public function bagian()
    {
        $bagian = $this->bagianmodel->findAll();
        $data = [
            'role' => session()->get('role'),
            'title' => 'Bagian',
            'active1' => '',
            'active2' => '',
            'active3' => 'active',
            'active4' => '',
            'active5' => '',
            'active6' => '',
            'bagian' => $bagian
        ];
        return view(session()->get('role') . '/bagian', $data);
    }
    public function absen()
    {
        $absen = $this->absenmodel->getAbsenWithKaryawan();

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
            'absen' => $absen
        ];
        // dd($absen);
        return view(session()->get('role') . '/absen', $data, $users);
    }
    public function job()
    {
        $jobrole = $this->jobrole->getJobRolesWithBagian();
        $data = [
            'role' => session()->get('role'),
            'title' => 'Job Role',
            'active1' => '',
            'active2' => '',
            'active3' => '',
            'active4' => '',
            'active5' => '',
            'active6' => 'active',
            'jobrole' => $jobrole
        ];
        return view(session()->get('role') . '/jobrole', $data);
    }
    // public function inputbagian()
    // {
    //     $nama_bagian    = $this->request->getPost('nama_bagian');
    //     $area           = $this->request->getPost('area');
    //     $keterangan     = $this->request->getPost('keterangan');
    //     $data = [
    //         'nama_bagian' => $nama_bagian,
    //         'area' => $area,
    //         'keterangan' => $keterangan
    //     ];
    //     $save = $this->bagianmodel->save($data);
    //     if ($save) {
    //         // return redirect()->to(base_url('datakaryawan'))->withInput()->with('success', 'Data Berhasil di Input');
    //         return redirect()->to(base_url(session()->get('role') . '/datakaryawan'))->withInput()->with('success', 'Data Berhasil di Input');
    //     } else {
    //         return redirect()->to(base_url(session()->get('role') . '/datakaryawan'))->withInput()->with('error', 'Data Gagal di Input');
    //     }


    // }
    // public function importkaryawan()
    // {
    //     $file = $this->request->getFile('excel_file');
    //     if ($file->isValid() && !$file->hasMoved()) {
    //         // Pastikan file adalah Excel
    //         $fileType = $file->getClientMimeType();
    //         if (!in_array($fileType, ['application/vnd.ms-excel', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'])) {
    //             return redirect()->to(base_url('Monitoring/datakaryawan'))->with('error', 'Invalid file type. Please upload an Excel file.');
    //         }

    //         $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($file->getTempName());
    //         $dataSheet = $spreadsheet->getActiveSheet();
    //         $startRow = 2; // Sesuaikan dengan baris a wal data di file Excel
    //         function excelDateToDate($excelDate) {
    //             $unixDate = ($excelDate - 25569) * 86400;
    //             return gmdate("Y-m-d", $unixDate);
    //         }
    //         foreach ($dataSheet->getRowIterator($startRow) as $row) {
    //             $cellIterator = $row->getCellIterator();
    //             $cellIterator->setIterateOnlyExistingCells(false);
    //             $data_excel = [];
    //             foreach ($cellIterator as $cell) {
    //                 $data_excel[] = $cell->getValue();
    //             }

    //             $nik = $data_excel[0];
    //             $kode_kartu = $data_excel[1];
    //             $nama_karyawan = $data_excel[2];
    //             $jenis_kelamin = $data_excel[3];
    //             $tgl_masuk = $data_excel[4];
    //             // Cek apakah tgl_masuk adalah angka (serial date dari Excel)
    //             if (is_numeric($tgl_masuk)) {
    //                 // Konversi angka serial Excel ke tanggal
    //                 $tgl_masuk = excelDateToDate($tgl_masuk);
    //             } else {
    //                 $tgl_masuk = null; // Set null jika format tidak valid
    //             }
    //             $shift = isset($data_excel[8]) ? $data_excel[8] : null; // Mengatur nilai default jika $data[8] tidak ada
    //             $nama_bagian = $data_excel[5];
    //             $area_utama = $data_excel[6];
    //             $area = $data_excel[7];
    //             $id_bagian = $this->bagianmodel->getIdBagian($nama_bagian, $area_utama, $area);
    //             $datakaryawan = [
    //                 'nik' => $nik,
    //                 'kode_kartu' => $kode_kartu,
    //                 'nama_karyawan' => $nama_karyawan,
    //                 'jenis_kelamin' => $jenis_kelamin,
    //                 'tgl_masuk' => $tgl_masuk,
    //                 'shift' => $shift,
    //                 'id_bagian' => $id_bagian
    //             ];

    //             $check = $this->karyawanmodel->cek_karyawan($nik);
    //             // if (!$check) {
    //             //     // Insert jika karyawan belum ada
    //             //     $this->karyawanmodel->insert($datakaryawan);
    //             //     return redirect()->to(base_url('Monitoring/datakaryawan'))->with('success', 'Data berhasil di import');
    //             // } else {
    //             //     return redirect()->to(base_url('Monitoring/datakaryawan'))->with('error', 'Data sudah ada');
    //             // }
    //             try {
    //                 if (!$check) {
    //                     $this->karyawanmodel->insert($datakaryawan);
    //                     return redirect()->to(base_url('Monitoring/datakaryawan'))->with('success', 'Data berhasil di import');
    //                     // echo "Data berhasil diinsert: " . $nik, $tgl_masuk . "<br/>";
    //                 } else {
    //                     return redirect()->to(base_url('Monitoring/datakaryawan'))->with('error', "Karyawan sudah ada: " . $nik . "<br/>");
    //                     // echo "Karyawan sudah ada: " . $nik . "<br/>";
    //                 }
    //             } catch (\Exception $e) {
    //                 // return redirect()->to(base_url('Monitoring/datakaryawan'))->with('error', "Gagal insert untuk NIK " . $nik . ": " . $e->getMessage() . "<br/>");
    //                 echo "Gagal insert untuk NIK " . $nik . ": " . $e->getMessage() . "<br/>";
    //             }
    //         }

    //         // return redirect()->to(base_url('Monitoring/datakaryawan'))->with('success', 'Data berhasil di import');
    //     } else {
    //         return redirect()->to(base_url('Monitoring/datakaryawan'))->with('error', 'File upload failed');
    //     }
    // }
}