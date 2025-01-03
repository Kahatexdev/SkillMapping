<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;
use App\Models\KaryawanModel;
use App\Models\AbsenModel;
use App\Models\UserModel;
use App\Models\SummaryRossoModel;
use App\Models\PeriodeModel;
use App\Models\PenilaianModel;
use App\Models\BatchModel;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Style\Border;
use DateTime;


class SummaryRossoController extends BaseController
{
    protected $karyawanmodel;
    protected $absenmodel;
    protected $usermodel;
    protected $summaryRosso;
    protected $periodeModel;
    protected $penilaianmodel;
    protected $batchModel;

    public function __construct()
    {
        $this->karyawanmodel = new KaryawanModel();
        $this->absenmodel = new AbsenModel();
        $this->usermodel = new UserModel();
        $this->summaryRosso = new SummaryRossoModel();
        $this->periodeModel = new PeriodeModel();
        $this->penilaianmodel = new PenilaianModel();
        $this->batchModel = new BatchModel();
    }

    public function downloadTemplate()
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Header Kolom
        $sheet->setCellValue('A1', 'KODE KARTU')
        ->setCellValue('B1', 'NAMA LENGKAP')
        ->setCellValue('C1', 'L/P')
        ->setCellValue('D1', 'TGL. MASUK KERJA')
        ->setCellValue('E1', 'BAGIAN')
        ->setCellValue('F1', 'RATA-RATA PRODUKSI')
        ->setCellValue('G1', 'RATA-RATA BS');

        // Lebar Kolom
        foreach (range('A', 'G') as $col) {
            $sheet->getColumnDimension($col)->setWidth(20);
        }

        // Style Header
        $sheet->getStyle('A1:G1')->getFont()->setBold(true);
        $sheet->getStyle('A1:G1')->getFill()->setFillType('solid')->getStartColor()->setARGB('FFA0A0A0');
        $sheet->getStyle('A1:G1')->getAlignment()->setHorizontal('center');

        // Data Contoh
        $sheet->setCellValue('A2', 'KK0001')
        ->setCellValue('B2', 'JOHN DOE')
        ->setCellValue('C2', 'L')
        ->setCellValue('D2', '2024-10-20')
        ->setCellValue('E2', 'ROSSO')
        ->setCellValue('F2', '5999')
        ->setCellValue('G2', '16');

        $fileName = 'Template_Summary_Rosso.xlsx';

        // Header untuk unduh
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header("Content-Disposition: attachment; filename=\"{$fileName}\"");
        header('Cache-Control: max-age=0');

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
                return redirect()->to(base_url('Monitoring/dataRosso'))->with('error', 'Invalid file type. Please upload an Excel file.');
            }

            $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($file->getTempName());
            $dataSheet = $spreadsheet->getActiveSheet();
            $startRow = 2;

            $bagianModel = new \App\Models\BagianModel();
            $this->karyawanmodel = new \App\Models\KaryawanModel();
            $this->summaryRosso = new \App\Models\SummaryRossoModel();

            $periode = $this->request->getPost('id_periode');
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
                        'id_periode' => $periode,
                        'id_karyawan' => $karyawan['id_karyawan'],
                        'average_produksi' => $averageProduksi,
                        'average_bs' => $averageBS
                    ];
                    // var_dump($data);

                    // dd ($data);
                    $this->summaryRosso->insert($data);

                    $successMessage = "Data Rosso berhasil disimpan.";
                    $successCount++;
                } else {
                    $errorMessages[] = $errorMessage;
                    $errorCount++;
                }
            }
            // Jika ada data yang gagal disimpan
            if ($errorCount > 0) {
                $errorMessages = implode("<br>", $errorMessages);
                return redirect()->to(base_url('Monitoring/dataRosso'))->with('error', "{$errorCount} data gagal disimpan. <br>{$errorMessages}");
            } else {
                return redirect()->to(base_url('Monitoring/dataRosso'))->with('success', "{$successCount} data berhasil disimpan.");
            }
        } else {
            return redirect()->to(base_url('Monitoring/dataRosso'))->with('error', 'Invalid file.');
        }
    }


    public function index()
    {
        //
    }

    public function create()
    {
        $bagianModel = new \App\Models\BagianModel();
        $bagian = $bagianModel->where('nama_bagian', 'ROSSO')->findAll();
        $data = [
            'role' => session()->get('role'),
            'title' => 'Summary Rosso',
            'active1' => '',
            'active2' => '',
            'active3' => '',
            'active4' => '',
            'active5' => '',
            'active6' => '',
            'active7' => '',
            'active8' => 'active',
            'bagian' => $bagian
        ];
        return view('Rosso/create', $data);

    }

    public function store()
    {

        //tambahkan validasi stardate dan end date
        $karyawan = $this->karyawanmodel->where('id_karyawan', $this->request->getPost('id_karyawan'))->first();
        if (!$karyawan) {
            return redirect()->to(base_url('Monitoring/dataRosso'))->with('error', 'Id Karyawan not found.');
        } else {
            $data = [
                'id_periode' => $this->request->getPost('id_periode'),
                'id_karyawan' => $karyawan['id_karyawan'],
                'tgl_prod_rosso' => $this->request->getPost('tgl_prod_rosso'),
                'qty_bs' => $this->request->getPost('qty_bs'),
                'qty_prod_rosso' => $this->request->getPost('qty_prod_rosso')
            ];

            // dd($data);  
            $this->summaryRosso->insert($data);
            return redirect()->to(base_url('Monitoring/dataRosso'))->with('success', 'Data successfully added.');
        }
    }

    public function show($id_periode)
    {
        $summaryRosso = new SummaryRossoModel();
        $SummaryRosso = $summaryRosso->getRossoByPeriode($id_periode);
        $periode = $this->periodeModel->checkPeriode($id_periode);

        // Perhitungan rata-rata berdasarkan rentang hari
        foreach ($SummaryRosso as &$row) {
            $start_date = new DateTime($row['start_date']); // pastikan 'start_date' ada di hasil query
            $end_date = new DateTime($row['end_date']); // pastikan 'end_date' ada di hasil query
            $jml_libur = $row['jml_libur'];
            $days = $start_date->diff($end_date)->days + 1; // Hitung rentang hari (inklusif)
            $days -= $jml_libur; // Kurangi jumlah hari libur
            // dd($days);
            
            // Hitung rata-rata
            $row['avg_qty_prod_rosso'] = $days > 0 ? $row['total_qty_prod_rosso'] / $days : 0;
            $row['avg_qty_bs'] = $days > 0 ? $row['total_qty_bs'] / $days : 0;
        }
        $data = [
            'role' => session()->get('role'),
            'title' => 'Summary Rosso',
            'active1' => '',
            'active2' => '',
            'active3' => '',
            'active4' => '',
            'active5' => '',
            'active6' => '',
            'active7' => '',
            'active8' => 'active',
            'SummaryRosso' => $SummaryRosso,
            'periode' => $periode
        ];
        // dd ($SummaryRosso);
        return view('Rosso/show-detail', $data);
    }


    public function edit($id)
    {
        // $bagianModel = new \App\Models\BagianModel();
        // $bagian = $bagianModel->where('nama_bagian', 'ROSSO')->findAll();
        $summaryRosso = new SummaryRossoModel();
        $SummaryRosso = $summaryRosso->getDataById($id);
        $sr = $summaryRosso->getDataById($id);
        $data = [
            'role' => session()->get('role'),
            'title' => 'Summary Rosso',
            'active1' => '',
            'active2' => '',
            'active3' => '',
            'active4' => '',
            'active5' => '',
            'active6' => '',
            'active7' => '',
            'active8' => 'active',
            // 'bagian' => $bagian,
            'SummaryRosso' => $SummaryRosso,
            'sr' => $sr
        ];
        // dd ($data); 
        return view('summaryRosso/edit', $data);
    }

    public function update($id)
    {
        $karyawan = $this->karyawanmodel->where('id_karyawan', $this->request->getPost('id_karyawan'))->first();
        if (!$karyawan) {
            return redirect()->to(base_url('Monitoring/dataRosso'))->with('error', 'ID Karyawan not found.');
        } else {
            $data = [
                'id_karyawan' => $karyawan['id_karyawan'],
                'tgl_prod_rosso' => $this->request->getPost('tgl_prod_rosso'),
                'qty_bs' => $this->request->getPost('qty_bs'),
                'qty_prod_rosso' => $this->request->getPost('qty_prod_rosso')
            ];
            $this->summaryRosso->update($id, $data);
            return redirect()->to(base_url('Monitoring/dataRosso'))->with('success', 'Data successfully updated.');
        }
    }

    public function delete($id)
    {
        $this->summaryRosso->delete($id);
        return redirect()->to(base_url('Monitoring/dataRosso'))->with('success', 'Data successfully deleted.');
    }

    public function summaryRosso()
    {
        $data = [
            'role' => session()->get('role'),
            'title' => 'Rosso',
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
        return view('Rosso/summaryPerPeriode', $data);
    }

    public function tampilPerBatch($area_utama)
    {
        $summaryRosso = $this->summaryRosso->getDatabyAreaUtama($area_utama);
        $batch = $this->batchModel->getBatch();
        // dd ($summaryRosso);
        $data = [
            'role' => session()->get('role'),
            'title' => 'Rosso',
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
            'summaryRosso' => $summaryRosso
        ];

        return view('Rosso/tampilPerBatch', $data);
    }

    public function excelSummaryRosso($area_utama, $id_batch)
    {
        
        $summaryRosso = $this->summaryRosso->getDatabyAreaUtamaAndPeriodeInBatch($area_utama, $id_batch);
        $namaBatch = $this->batchModel->where('id_batch', $id_batch)->first();
        // $id_batch = $this->request->getPost('id_batch');
        // dd ($summaryRosso);
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $sheet->mergeCells('A1:H2');
        $sheet->setCellValue('A1', 'REPORT SUMMARY ROSSO');
        $sheet->getStyle('A1')->getAlignment()->setHorizontal('center');
        $sheet->getStyle('A1')->getFont()->setBold(true);
        $sheet->getStyle('A1')->getFont()->setUnderline(true);
        $sheet->getStyle('A1')->getFont()->setName('Times New Roman');
        $sheet->getStyle('A1')->getFont()->setSize(16);

        $sheet->mergeCells('A3:B3');
        $sheet->setCellValue('A3', 'AREA');
        $sheet->setCellValue('C3', ': '.$area_utama);
        $sheet->getStyle('A3:C3')->getFont()->setBold(true);

        $sheet->mergeCells('A4:B4');
        $sheet->setCellValue('A4', 'NAMA BATCH');
        $sheet->setCellValue('C4', ': '.$namaBatch['nama_batch']);
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

        foreach ($summaryRosso as $row) {
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
        $getTop3 = $this->summaryRosso->getTop3Produksi($area_utama, $id_batch);
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



        $spreadsheet->getActiveSheet()->setTitle('Report Summary Rosso');

        $filename = 'Report Summary Rosso ' . date('d-m-Y H:i:s') . '.xlsx';

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');

        $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
        $writer->save('php://output');
        exit;
    }
}
