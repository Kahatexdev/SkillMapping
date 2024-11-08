<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\BsmcModel;
use CodeIgniter\HTTP\ResponseInterface;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\IOFactory;

use App\Models\KaryawanModel;

class BsMcController extends BaseController
{
    protected $bsmcModel;
    protected $karyawanModel;

    public function __construct()
    {

        $this->bsmcModel = new BsmcModel();
        $this->karyawanModel = new KaryawanModel();
    }

    public function downloadTemplate()
    {
        // Membuat spreadsheet baru
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Menyusun header kolom
        $sheet->setCellValue('A1', 'Kode Kartu');
        $sheet->setCellValue('B1', 'Nama Karyawan');
        $sheet->setCellValue('C1', 'Tanggal');
        $sheet->setCellValue('D1', 'Nomor Model	');
        $sheet->setCellValue('E1', 'Inisial');
        $sheet->setCellValue('F1', 'Qty Prod Mc');
        $sheet->setCellValue('G1', 'Qty Bs');

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
        $sheet->setCellValue('C2', '2024/09/12');
        $sheet->setCellValue('D2', 'LN2541');
        $sheet->setCellValue('E2', 'LN');
        $sheet->setCellValue('F2', '2');
        $sheet->setCellValue('G2', '3');

        // 
        // Menentukan nama file
        $fileName = 'Template_Data_Bs_Mesin.xlsx';

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
                return redirect()->to(base_url('monitoring/dataBsmc'))->with('error', 'Invalid file type. Please upload an Excel file.');
            }

            $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($file->getTempName());
            $dataSheet = $spreadsheet->getActiveSheet();
            $startRow = 2;

            $karyawanModel = new \App\Models\KaryawanModel();
            $bsmcModel = new \App\Models\BsmcModel();

            $successCount = 0;
            $errorCount = 0;
            $errorMessages = [];

            for ($row = $startRow; $row <= $dataSheet->getHighestRow(); $row++) {
                $isValid = true;
                $errorMessage = "Row {$row}: ";
                $kodeKartu = $dataSheet->getCell('A' . $row)->getValue();
                $namaKaryawan = $dataSheet->getCell('B' . $row)->getValue();
                $tanggal = $dataSheet->getCell('C' . $row)->getValue();
                $no_model = $dataSheet->getCell('D' . $row)->getValue();
                $inisial = $dataSheet->getCell('E' . $row)->getValue();
                $qty_prod_mc = $dataSheet->getCell('F' . $row)->getValue();
                $qty_bs = $dataSheet->getCell('G' . $row)->getValue();

                if (empty($kodeKartu)) {
                    $isValid = false;
                    $errorMessage .= "Kode Kartu tidak ada / harus diisi. ";
                }
                if (empty($namaKaryawan)) {
                    $isValid = false;
                    $errorMessage .= "Nama Karyawan tidak ada / harus diisi. ";
                }

                if ($isValid) {
                    $karyawan = $karyawanModel->where([
                        'kode_kartu' => $kodeKartu,
                        'nama_karyawan' => $namaKaryawan
                    ])->first();
                    // dd($karyawan);

                    if ($karyawan) {
                        $idKaryawan = $karyawan['id_karyawan'];
                        // dd($idKaryawan);
                        $errorMessage .= "Karyawan ditemukan dengan ID: {$idKaryawan}. ";
                    } else {
                        $isValid = false;
                        $errorMessage .= "Karyawan dengan Kode Kartu dan Nama tersebut tidak ditemukan. ";
                    }
                }

                if (empty($tanggal)) {
                    $isValid = false;
                    $errorMessage .= "Tanggal harus diisi. ";
                } else {
                    // Cek jika tanggal dalam format Excel (serial number)
                    if (\PhpOffice\PhpSpreadsheet\Shared\Date::isDateTime($dataSheet->getCell('C' . $row))) {
                        try {
                            // Konversi serial Excel ke objek DateTime
                            $tanggalObject = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($tanggal);
                        } catch (\Exception $e) {
                            $isValid = false;
                            $errorMessage .= "Gagal mengonversi tanggal dari format Excel. ";
                            $tanggalObject = false;
                        }
                    } else {
                        // Coba parsing menggunakan format yang berbeda
                        $tanggalObject = date_create_from_format('Y/m/d', $tanggal) ?:
                            date_create_from_format('d/m/Y', $tanggal) ?:
                            date_create($tanggal);
                    }

                    // Validasi hasil konversi tanggal
                    if ($tanggalObject instanceof \DateTime) {
                        // Format ulang tanggal menjadi 'Y-m-d' untuk database
                        $tanggal = $tanggalObject->format('Y-m-d');
                    } else {
                        $isValid = false;
                        $errorMessage .= "Format Tanggal salah atau tidak bisa dikonversi. ";
                    }
                }



                if ($isValid) {
                    $data = [
                        'id_karyawan' => $karyawan['id_karyawan'],
                        'tanggal' => $tanggal,
                        'no_model' => $no_model,
                        'inisial' => $inisial,
                        'qty_prod_mc' => $qty_prod_mc,
                        'qty_bs' => $qty_bs,
                        'created_at' => date('Y-m-d H:i:s'),
                        'updated_at' => date('Y-m-d H:i:s')
                    ];
                    // dd($data);


                    $bsmcModel->insert($data);
                    $successMessage = "Data Bs Mesin berhasil disimpan.";
                    $successCount++;
                } else {
                    $errorMessages[] = $errorMessage;
                    $errorCount++;
                }
            }

            if ($errorCount > 0) {
                $errorMessages = implode("<br>", $errorMessages);
                return redirect()->to(base_url('monitoring/dataBsmc'))->with('error', "{$errorCount} data gagal disimpan. <br>{$errorMessages}");
            } else {
                return redirect()->to(base_url('monitoring/dataBsmc'))->with('success', "{$successCount} data berhasil disimpan.");
            }
        } else {
            return redirect()->to(base_url('monitoring/dataBsmc'))->with('error', 'Invalid file.');
        }
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
}
