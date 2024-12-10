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

        // 
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
                return redirect()->to(base_url('Monitoring/datakaryawan'))->with('error', 'Invalid file type. Please upload an Excel file.');
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
                $shift = $dataSheet->getCell('C' . $row)->getValue();
                $jenisKelamin = $dataSheet->getCell('D' . $row)->getValue();
                $libur = $dataSheet->getCell('E' . $row)->getValue();
                $liburTambahan = $dataSheet->getCell('F' . $row)->getValue();
                $warnaBaju = $dataSheet->getCell('G' . $row)->getValue();
                $statusBaju = $dataSheet->getCell('H' . $row)->getValue();
                $tanggalLahir = $dataSheet->getCell('I' . $row)->getFormattedValue();
                $tanggalMasuk = $dataSheet->getCell('J' . $row)->getFormattedValue();
                $namaBagian = $dataSheet->getCell('K' . $row)->getValue();
                $areaUtama = $dataSheet->getCell('L' . $row)->getValue();
                $area = $dataSheet->getCell('M' . $row)->getValue();
                $statusAktif = $dataSheet->getCell('N' . $row)->getValue();
                // dd($statusAktif);
                // dd($namaBagian, $areaUtama, $area);
                // dd($kodeKartu, $namaKaryawan, $shift, $jenisKelamin, $libur, $liburTambahan, $warnaBaju, $statusBaju, $tanggalLahir, $tanggalMasuk, $namaBagian, $areaUtama, $area, $statusAktif);
                // Validasi kode kartu
                if (empty($kodeKartu)) {
                    $isValid = false;
                    $errorMessage .= "Kode Kartu harus diisi. ";
                } else {
                    $karyawan = $karyawanModel->where('kode_kartu', $kodeKartu)->first();
                    if ($karyawan) {
                        $isValid = false;
                        $errorMessage .= "Kode Kartu sudah ada. ";
                    }
                }

                // Validasi nama karyawan
                if (empty($namaKaryawan)) {
                    $isValid = false;
                    $errorMessage .= "Nama Karyawan harus diisi. ";
                } else {
                    $karyawan = $karyawanModel->where('nama_karyawan', $namaKaryawan)->first();
                    if ($karyawan) {
                        $isValid = false;
                        $errorMessage .= "Nama Karyawan sudah ada. ";
                    }
                }

                // Validasi shift
                if (empty($shift)) {
                    continue;
                }
                // validasi libur
                if (empty($libur)) {
                    continue;
                }
                // Validasi tangal lahir
                if (empty($tanggalLahir)) {
                    $isValid = false;
                    $errorMessage .= "Tanggal Lahir harus diisi. ";
                } else {
                    $tanggalLahir = date_create_from_format('m/d/Y', $tanggalLahir);
                    // dd ($tanggalLahir);
                    if (!$tanggalLahir) {
                        $isValid = false;
                        $errorMessage .= "Format Tanggal Lahir salah. ";
                    }
                }

                // Validasi tanggal masuk
                if (empty($tanggalMasuk)) {
                    $isValid = false;
                    $errorMessage .= "Tanggal Masuk harus diisi. ";
                } else {
                    $tanggalMasuk = date_create_from_format('m/d/Y', $tanggalMasuk);
                    // dd($tanggalMasuk);
                    if (!$tanggalMasuk) {
                        $isValid = false;
                        $errorMessage .= "Format Tanggal Masuk salah. ";
                    }
                }

                // validasi nama bagian ketika area utama dan area cocok dengan table bagian maka data di save
                if (empty($namaBagian)) {
                    $isValid = false;
                    $errorMessage .= "Nama Bagian harus diisi. ";
                } else {
                    if ($area == '') {
                        $area = null;
                    }
                    $bagian = $bagianModel->where('nama_bagian', $namaBagian)->where('area_utama', $areaUtama)->where('area', $area)->first();
                    // dd ($bagian, $namaBagian, $areaUtama, $area);
                    if (!$bagian) {
                        $isValid = false;
                        $errorMessage .= "Nama Bagian tidak ditemukan. ";
                    }
                }

                // status aktif
                if (empty($statusAktif)) {
                    continue;
                }

                // dd ($isValid);
                // kalau ada kartu yang sama maka tidak akan di save
                if ($isValid) {
                    $data = [
                        'kode_kartu' => $kodeKartu,
                        'nama_karyawan' => $namaKaryawan,
                        'shift' => $shift,
                        'jenis_kelamin' => $jenisKelamin,
                        'libur' => $libur,
                        'libur_tambahan' => $liburTambahan,
                        'warna_baju' => $warnaBaju,
                        'status_baju' => $statusBaju,
                        'tgl_lahir' => $tanggalLahir->format('Y-m-d'),
                        'tgl_masuk' => $tanggalMasuk->format('Y-m-d'),
                        'id_bagian' => $bagian['id_bagian'],
                        'status_aktif' => $statusAktif,
                        'created_at' => date('Y-m-d H:i:s'),
                        'updated_at' => date('Y-m-d H:i:s')
                    ];
                    // dd($data);

                    $karyawanModel->insert($data);
                    // dd ($data);
                    $successMessage = "Data karyawan berhasil disimpan.";
                    $successCount++;
                } else {
                    $errorMessages[] = $errorMessage;
                    $errorCount++;
                }
                // dd ($data);
            }
            $role = session()->get('role');


            // Jika ada data yang gagal disimpan
            if ($errorCount > 0) {
                $errorMessages = implode("<br>", $errorMessages);
                if ($role === 'Monitoring') {
                    return redirect()->to(base_url('Monitoring/datakaryawan'))->with('error', "{$errorCount} data gagal disimpan. <br>{$errorMessages}");
                } else {
                    return redirect()->to(base_url('TrainingSchool/datakaryawan'))->with('error', "{$errorCount} data gagal disimpan. <br>{$errorMessages}");
                }
            } else {
                if ($role === 'Monitoring') {
                    return redirect()->to(base_url('Monitoring/datakaryawan'))->with('success', "{$successCount} data berhasil disimpan.");
                } else {
                    return redirect()->to(base_url('TrainingSchool/datakaryawan'))->with('error', "{$errorCount} data gagal disimpan. <br>{$errorMessages}");
                }
            }
        } else {
            return redirect()->to(base_url('Monitoring/datakaryawan'))->with('error', 'Invalid file.');
        }
    }

    public function create()
    {
        $bagianModel = new \App\Models\BagianModel();
        $bagian = $bagianModel->findAll();

        $data = [
            'role' => session()->get('role'),
            'title' => 'Karyawan',
            'active1' => '',
            'active2' => '',
            'active3' => '',
            'active4' => 'active',
            'active5' => '',
            'active6' => '',
            'bagian' => $bagian
        ];
        return view('Karyawan/create', $data);
    }

    public function store()
    {
        $karyawanModel = new \App\Models\KaryawanModel();
        $bagianModel = new \App\Models\BagianModel();

        $kodeKartu = $this->request->getPost('kode_kartu');
        $namaKaryawan = $this->request->getPost('nama_karyawan');
        $shift = $this->request->getPost('shift');
        $jenisKelamin = $this->request->getPost('jenis_kelamin');
        $libur = $this->request->getPost('libur');
        $liburTambahan = $this->request->getPost('libur_tambahan');
        $warnaBaju = $this->request->getPost('warna_baju');
        $statusBaju = $this->request->getPost('status_baju');
        $tanggalLahir = $this->request->getPost('tgl_lahir');
        $tanggalMasuk = $this->request->getPost('tgl_masuk');
        $bagian = $this->request->getPost('bagian');
        $statusAktif = $this->request->getPost('status_aktif');

        $bagian = $bagianModel->find($bagian);
        // dd($bagian);

        $data = [
            'kode_kartu' => $kodeKartu,
            'nama_karyawan' => $namaKaryawan,
            'shift' => $shift,
            'jenis_kelamin' => $jenisKelamin,
            'libur' => $libur,
            'libur_tambahan' => $liburTambahan,
            'warna_baju' => $warnaBaju,
            'status_baju' => $statusBaju,
            'tgl_lahir' => $tanggalLahir,
            'tgl_masuk' => $tanggalMasuk,
            'id_bagian' => $bagian['id_bagian'],
            'status_aktif' => $statusAktif,
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s')
        ];

        $karyawanModel->insert($data);
        $role = session()->get('role');

        if ($role === 'Monitoring') {
            return redirect()->to(base_url('Monitoring/datakaryawan'))->with('success', 'Data karyawan berhasil disimpan.');
        } elseif ($role === "TrainingSchool") {
            return redirect()->to(base_url('TrainingSchool/'))->with('success', 'Data karyawan berhasil diubah.');
        }
    }

    public function edit($id)
    {
        $karyawanModel = new \App\Models\KaryawanModel();
        $bagianModel = new \App\Models\BagianModel();
        $karyawan = $karyawanModel->find($id);
        $bagian = $bagianModel->findAll();

        $data = [
            'role' => session()->get('role'),
            'title' => 'Karyawan',
            'active1' => '',
            'active2' => '',
            'active3' => '',
            'active4' => 'active',
            'active5' => '',
            'active6' => '',
            'karyawan' => $karyawan,
            'bagian' => $bagian
        ];
        return view('Karyawan/edit', $data);
    }

    public function update($id)
    {
        $karyawanModel = new \App\Models\KaryawanModel();
        $bagianModel = new \App\Models\BagianModel();

        $kodeKartu = $this->request->getPost('kode_kartu');
        $namaKaryawan = $this->request->getPost('nama_karyawan');
        $shift = $this->request->getPost('shift');
        $jenisKelamin = $this->request->getPost('jenis_kelamin');
        $libur = $this->request->getPost('libur');
        $liburTambahan = $this->request->getPost('libur_tambahan');
        $warnaBaju = $this->request->getPost('warna_baju');
        $statusBaju = $this->request->getPost('status_baju');
        $tanggalLahir = $this->request->getPost('tgl_lahir');
        $tanggalMasuk = $this->request->getPost('tgl_masuk');
        $bagian = $this->request->getPost('bagian');
        $statusAktif = $this->request->getPost('status_aktif');

        $bagian = $bagianModel->find($bagian);

        $data = [
            'kode_kartu' => $kodeKartu,
            'nama_karyawan' => $namaKaryawan,
            'shift' => $shift,
            'jenis_kelamin' => $jenisKelamin,
            'libur' => $libur,
            'libur_tambahan' => $liburTambahan,
            'warna_baju' => $warnaBaju,
            'status_baju' => $statusBaju,
            'tgl_lahir' => $tanggalLahir,
            'tgl_masuk' => $tanggalMasuk,
            'id_bagian' => $bagian['id_bagian'],
            'status_aktif' => $statusAktif,
            'updated_at' => date('Y-m-d H:i:s')
        ];

        $karyawanModel->update($id, $data);
        $role = session()->get('role');
        if ($role === 'Monitoring') {
            return redirect()->to(base_url('Monitoring/datakaryawan'))->with('success', 'Data karyawan berhasil diubah.');
        } elseif ($role === "TrainingSchool") {
            return redirect()->to(base_url('TrainingSchool/'))->with('success', 'Data karyawan berhasil diubah.');
        }
    }


    public function delete($id)
    {
        $karyawanModel = new \App\Models\KaryawanModel();
        $karyawanModel->delete($id);
        $role = session()->get('role');

        if ($role === 'Monitoring') {
            return redirect()->to(base_url('Monitoring/datakaryawan'))->with('success', 'Data karyawan berhasil dihapus.');
        } elseif ($role === "TrainingSchool") {
            return redirect()->to(base_url('TrainingSchool/'))->with('success', 'Data karyawan berhasil dihapus.');
        }
    }
}
