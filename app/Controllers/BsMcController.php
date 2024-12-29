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
        set_time_limit(0); // Disable time limit for this script
        // ini_set('memory_limit', '2048M'); // Set memory limit to 2GB

        $file = $this->request->getFile('file'); // Nama input file excel
        if ($file && $file->isValid() && !$file->hasMoved()) {
            $fileType = $file->getClientMimeType();
            if (!in_array($fileType, ['application/vnd.ms-excel', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'])) {
                return redirect()->to(base_url('monitoring/dataBsmc'))->with('error', 'Invalid file type. Please upload an Excel file.');
            }

            $spreadsheet = IOFactory::load($file->getTempName());

            $karyawanModel = new \App\Models\KaryawanModel();
            $bsmcModel = new \App\Models\BsmcModel();

            $successCount = 0;
            $errorCount = 0;
            $errorMessages = [];

            $dataToInsert = [];

            for ($sheetIndex = 2; $sheetIndex <= 32; $sheetIndex++) { // 1-31
                $sheet = $spreadsheet->getSheet($sheetIndex);
                $highestRow = min(40, $sheet->getHighestRow());
                $highestColumn = $sheet->getHighestColumn();
                // limit highestColumn to 5 to avoid memory limit
                $highestColumn = $highestColumn > 'S' ? 'S' : $highestColumn;

                for ($row = 33; $row <= $highestRow; $row++) {
                    $rowData = $sheet->rangeToArray('J' . $row . ':' . $highestColumn . $row, NULL, TRUE, FALSE)[0];

                    if (!empty($rowData[4])){
                        $nama_karyawan = $rowData[4];
                        $no_model = $rowData[1];
                        $inisial = $rowData[2];
                        $qty_bs = $rowData[8];
                        // tanggal didapat dari nama sheet
                        $tmp_tgl = $sheet->getTitle();
                        // bulan sekarang
                        $tmp_month = date('m');
                        // tahun sekarang
                        $tmp_year = date('Y');
                        // tanggal sekarang
                        $tanggal = date('Y-m-d', strtotime($tmp_year . '-' . $tmp_month . '-' . $tmp_tgl));

                        // dd ($nama_karyawan, $no_model, $inisial, $qty_bs, $tanggal, $rowData);

                        // Validasi untuk record yang hilang di databse.
                        $karyawan = $karyawanModel->where('nama_karyawan', $nama_karyawan)->first();
                        // dd ($karyawan);
                        if ($karyawan) {
                            $dataToInsert[] = [
                                'id_karyawan' => $karyawan['id_karyawan'],
                                'no_model' => $no_model,
                                'tanggal' => $tanggal,
                                'inisial' => $inisial,
                                'qty_bs' => (float) $qty_bs
                            ];
                            $successCount++;
                            // dd ($dataToInsert);
                        } else {
                            $errorCount++;
                            $errorMessages[] = "Sheet $sheetIndex, Row $row: Nama $nama_karyawan not found.";
                        }
                    }
                    if (!empty($rowData[5])){
                        $nama_karyawan = $rowData[5];
                        $no_model = $rowData[1];
                        $inisial = $rowData[2];
                        $qty_bs = $rowData[11];
                        // tanggal didapat dari nama sheet
                        $tmp_tgl = $sheet->getTitle();
                        // bulan sekarang
                        $tmp_month = date('m');
                        // tahun sekarang
                        $tmp_year = date('Y');
                        // tanggal sekarang
                        $tanggal = date('Y-m-d', strtotime($tmp_year . '-' . $tmp_month . '-' . $tmp_tgl));

                        // dd ($nama_karyawan, $no_model, $inisial, $qty_bs, $tanggal);

                        // Validasi untuk record yang hilang di databse.
                        $karyawan = $karyawanModel->where('nama_karyawan', $nama_karyawan)->first();
                        // dd ($karyawan);
                        if ($karyawan) {
                            $dataToInsert[] = [
                                'id_karyawan' => $karyawan['id_karyawan'],
                                'no_model' => $no_model,
                                'tanggal' => $tanggal,
                                'inisial' => $inisial,
                                'qty_bs' => (float) $qty_bs
                            ];
                            $successCount++;
                            // dd ($dataToInsert);
                        } else {
                            $errorCount++;
                            $errorMessages[] = "Sheet $sheetIndex, Row $row: Nama $nama_karyawan not found.";
                        }
                    }
                    if(!empty($rowData[6])){
                        $nama_karyawan = $rowData[6];
                        $no_model = $rowData[1];
                        $inisial = $rowData[2];
                        $qty_bs = $rowData[14];
                        // tanggal didapat dari nama sheet
                        $tmp_tgl = $sheet->getTitle();
                        // bulan sekarang
                        $tmp_month = date('m');
                        // tahun sekarang
                        $tmp_year = date('Y');
                        // tanggal sekarang
                        $tanggal = date('Y-m-d', strtotime($tmp_year . '-' . $tmp_month . '-' . $tmp_tgl));

                        // dd ($nama_karyawan, $no_model, $inisial, $qty_bs, $tanggal);

                        // Validasi untuk record yang hilang di databse.
                        $karyawan = $karyawanModel->where('nama_karyawan', $nama_karyawan)->first();
                        // dd ($karyawan);
                        if ($karyawan) {
                            $dataToInsert[] = [
                                'id_karyawan' => $karyawan['id_karyawan'],
                                'no_model' => $no_model,
                                'tanggal' => $tanggal,
                                'inisial' => $inisial,
                                'qty_bs' => (float) $qty_bs
                            ];
                            $successCount++;
                            // dd ($dataToInsert);
                        } else {
                            $errorCount++;
                            $errorMessages[] = "Sheet $sheetIndex, Row $row: Nama $nama_karyawan not found.";
                        }
                    } else {
                        $errorCount++;
                        $errorMessages[] = "Sheet $sheetIndex, Row $row empty nama_karyawan";
                    }
                }
            }

            if(!empty($dataToInsert)) {
                // dd ($dataToInsert);
                $bsmcModel->insertBatch($dataToInsert);
            }
        
            $message = "Upload success: $successCount record(s). <br>Error: $errorCount.<br>";
            if ($errorMessages) $message .= implode(' | ', $errorMessages);
            return redirect()->to(base_url('monitoring/dataBsmc'))->with('success', $message);
            
        } else {
            return redirect()->to(base_url('monitoring/dataBsmc'))->with('error', 'Invalid file.');
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

    public function tampilPerBatch()
    {
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
            'active8' => ''

        ];

        return view('Bsmc/tampilPerBatch', $data);
    }

    public function summaryBsmc()
    {
        $data = [
            'role' => session()->get('role'),
            'title' => 'Bs Mesin',
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
        return view('Bsmc/summaryPerPeriode', $data);
    } 
}
