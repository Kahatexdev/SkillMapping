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
use App\Models\PeriodeModel;
use DateTime;

class JarumController extends BaseController
{
    protected $summaryJarum;
    protected $batchModel;
    protected $periodeModel;

    public function __construct()
    {
        $this->summaryJarum = new SummaryJarumModel();
        $this->batchModel = new BatchModel();
        $this->periodeModel = new PeriodeModel();
    }
    public function index()
    {
        //
    }

    public function tampilPerBatch($area)
    {
        $summaryJarum = $this->summaryJarum->getDatabyArea($area);
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
            'area' => $area,
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

    // public function upload()
    // {
    //     $file = $this->request->getFile('file');
    //     if ($file && $file->isValid() && !$file->hasMoved()) {
    //         $fileType = $file->getClientMimeType();
    //         if (!in_array($fileType, ['application/vnd.ms-excel', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'])) {
    //             return redirect()->to(base_url('Monitoring/dataJarum'))->with('error', 'Invalid file type. Please upload an Excel file.');
    //         }

    //         $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($file->getTempName());
    //         $dataSheet = $spreadsheet->getActiveSheet();
    //         $startRow = 2;

    //         $bagianModel = new \App\Models\BagianModel();
    //         $this->karyawanmodel = new \App\Models\KaryawanModel();
    //         // $this->summaryRosso = new \App\Models\SummaryRossoModel();

    //         $batch = $this->request->getPost('id_batch');
    //         // dd ($batch);
    //         $successCount = 0;
    //         $errorCount = 0;
    //         $errorMessages = [];

    //         for ($row = $startRow; $row <= $dataSheet->getHighestRow(); $row++) {
    //             $isValid = true;
    //             $errorMessage = "Row {$row}: ";

    //             $kodeKartu = $dataSheet->getCell('A' . $row)->getValue();
    //             $namaLengkap = $dataSheet->getCell('B' . $row)->getValue();
    //             $jenisKelamin = $dataSheet->getCell('C' . $row)->getValue();
    //             $tglMasukKerja = $dataSheet->getCell('D' . $row)->getFormattedValue();
    //             $bagian = $dataSheet->getCell('E' . $row)->getValue();
    //             $avgUsedNeedle = $dataSheet->getCell('F' . $row)->getValue();

    //             if (empty($kodeKartu)) {
    //                 $isValid = false;
    //                 $errorMessage .= "Kode Kartu is required. ";
    //             } else {
    //                 $karyawan = $this->karyawanmodel->where('kode_kartu', $kodeKartu)->where('nama_karyawan', $namaLengkap)->first();
    //                 if (!$karyawan) {
    //                     $isValid = false;
    //                     $errorMessage .= "Kode Kartu not found. ";
    //                 }
    //             }

    //             if (empty($jenisKelamin) || !in_array($jenisKelamin, ['L', 'P'])) {
    //                 $isValid = false;
    //                 $errorMessage .= "Jenis Kelamin must be L or P. ";
    //             }

    //             if ($isValid) {
    //                 $data = [
    //                     'id_batch' => $batch,
    //                     'id_karyawan' => $karyawan['id_karyawan'],
    //                     'avg_used_needle' => $avgUsedNeedle
    //                 ];
    //                 // var_dump($data);

    //                 // dd ($data);
    //                 $this->summaryJarum->insert($data);

    //                 $successMessage = "Summary Jarum berhasil disimpan.";
    //                 $successCount++;
    //             } else {
    //                 $errorMessages[] = $errorMessage;
    //                 $errorCount++;
    //             }
    //         }
    //         // Jika ada data yang gagal disimpan
    //         if ($errorCount > 0) {
    //             $errorMessages = implode("<br>", $errorMessages);
    //             return redirect()->to(base_url('Monitoring/dataJarum'))->with('error', "{$errorCount} data gagal disimpan. <br>{$errorMessages}");
    //         } else {
    //             return redirect()->to(base_url('Monitoring/dataJarum'))->with('success', "{$successCount} data berhasil disimpan.");
    //         }
    //     } else {
    //         return redirect()->to(base_url('Monitoring/dataJarum'))->with('error', 'Invalid file.');
    //     }
    // }

    public function upload()
    {
        $getTglInput = $this->request->getPost('tgl_input');
        $getArea = $this->request->getPost('area');
        $getKaryawan = $this->request->getPost('id_karyawan');
        $getNeedle = $this->request->getPost('used_needle');

        $currentDate = date('Y-m-d');

        if ($getTglInput > $currentDate) {
            return redirect()->to(base_url('Monitoring/dataJarum'))->with('error', 'Tanggal Input tidak boleh melebihi tanggal sekarang');
        }

        $data = [];
        for ($i = 0; $i < count($getArea); $i++) {
            $data[] = [
                'tgl_input'    => $getTglInput,
                'id_karyawan'  => $getKaryawan[$i],
                'used_needle'  => $getNeedle[$i],
                'created_at'   => date('Y-m-d H:i:s'),
                'area'         => $getArea[$i]
            ];
        }

        if (!empty($data)) {
            $this->summaryJarum->insertBatch($data);
        }

        return redirect()->to(base_url('Monitoring/dataJarum'))->with('success', 'Data Berhasil Disimpan');
    }


    // public function excelSummaryJarum($area_utama, $id_batch)
    // {
    //     $summaryJarum = $this->summaryJarum->getDatabyAreaUtamaAndPeriodeInBatch($area_utama, $id_batch);
    //     $namaBatch = $this->batchModel->where('id_batch', $id_batch)->first();
    //     // $id_batch = $this->request->getPost('id_batch');
    //     // dd ($summaryRosso);
    //     $spreadsheet = new Spreadsheet();
    //     $sheet = $spreadsheet->getActiveSheet();

    //     $sheet->mergeCells('A1:H2');
    //     $sheet->setCellValue('A1', 'REPORT SUMMARY JARUM');
    //     $sheet->getStyle('A1')->getAlignment()->setHorizontal('center');
    //     $sheet->getStyle('A1')->getFont()->setBold(true);
    //     $sheet->getStyle('A1')->getFont()->setUnderline(true);
    //     $sheet->getStyle('A1')->getFont()->setName('Times New Roman');
    //     $sheet->getStyle('A1')->getFont()->setSize(16);

    //     $sheet->mergeCells('A3:B3');
    //     $sheet->setCellValue('A3', 'AREA');
    //     $sheet->setCellValue('C3', ': ' . $area_utama);
    //     $sheet->getStyle('A3:C3')->getFont()->setBold(true);

    //     $sheet->mergeCells('A4:B4');
    //     $sheet->setCellValue('A4', 'NAMA BATCH');
    //     $sheet->setCellValue('C4', ': ' . $namaBatch['nama_batch']);
    //     $sheet->getStyle('A4:C4')->getFont()->setBold(true);
    //     $sheet->getStyle('A3:C4')->getFont()->setName('Times New Roman');
    //     $sheet->getStyle('A3:C4')->getFont()->setSize(12);


    //     $sheet->mergeCells('A6:A7');
    //     $sheet->setCellValue('A6', 'NO');
    //     $sheet->mergeCells('B6:B7');
    //     $sheet->setCellValue('B6', 'KODE KARTU');
    //     $sheet->mergeCells('C6:C7');
    //     $sheet->setCellValue('C6', 'NAMA LENGKAP');
    //     $sheet->mergeCells('D6:D7');
    //     $sheet->setCellValue('D6', 'L/P');
    //     $sheet->mergeCells('E6:E7');
    //     $sheet->setCellValue('E6', 'TGL. MASUK KERJA');
    //     $sheet->mergeCells('F6:F7');
    //     $sheet->setCellValue('F6', 'BAGIAN');
    //     $sheet->mergeCells('G6:G7');
    //     $sheet->setCellValue('G6', 'AVG USED NEEDLE');

    //     $sheet->getStyle('A6:G7')->getFont()->setBold(true);
    //     $sheet->getStyle('A6:G7')->getFont()->setName('Times New Roman');
    //     $sheet->getStyle('A6:G7')->getFont()->setSize(10);
    //     $sheet->getStyle('A6:G7')->getAlignment()->setHorizontal('center');
    //     $sheet->getStyle('A6:G7')->getAlignment()->setVertical('center');
    //     $sheet->getStyle('A6:G7')->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
    //     $sheet->getStyle('A6:G7')->getAlignment()->setWrapText(true);

    //     $sheet->getColumnDimension('A')->setWidth(5);
    //     $sheet->getColumnDimension('B')->setWidth(10);
    //     $sheet->getColumnDimension('C')->setWidth(20);
    //     $sheet->getColumnDimension('D')->setWidth(5);
    //     $sheet->getColumnDimension('E')->setWidth(15);
    //     $sheet->getColumnDimension('F')->setWidth(10);
    //     $sheet->getColumnDimension('G')->setWidth(10);

    //     $startRow = 8;
    //     $no = 1;

    //     foreach ($summaryJarum as $row) {
    //         $sheet->setCellValue('A' . $startRow, $no);
    //         $sheet->setCellValue('B' . $startRow, $row['kode_kartu']);
    //         $sheet->setCellValue('C' . $startRow, $row['nama_karyawan']);
    //         $sheet->setCellValue('D' . $startRow, $row['jenis_kelamin']);
    //         $sheet->setCellValue('E' . $startRow, $row['tgl_masuk']);
    //         $sheet->setCellValue('F' . $startRow, $row['nama_bagian']);
    //         $sheet->setCellValue('G' . $startRow, $row['avg_used_needle']);

    //         $sheet->getStyle('A' . $startRow . ':G' . $startRow)->getFont()->setName('Times New Roman');
    //         $sheet->getStyle('A' . $startRow . ':G' . $startRow)->getFont()->setSize(10);
    //         $sheet->getStyle('A' . $startRow . ':G' . $startRow)->getAlignment()->setHorizontal('center');
    //         $sheet->getStyle('A' . $startRow . ':G' . $startRow)->getAlignment()->setVertical('center');
    //         $sheet->getStyle('A' . $startRow . ':G' . $startRow)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
    //         $sheet->getStyle('A' . $startRow . ':G' . $startRow)->getAlignment()->setWrapText(true);

    //         $no++;
    //         $startRow++;
    //     }

    //     // get 3 karyawan dengan max average produksi dan min average bs
    //     $getTop3 = $this->summaryJarum->getTop3Produksi($area_utama, $id_batch);
    //     // dd($getTop3);
    //     // Header untuk Top 3 Produksi
    //     $sheet->mergeCells('J6:P6');
    //     $sheet->setCellValue('J6', 'TOP 3 AVG USED NEEDLE');
    //     $sheet->getStyle('J6')->getFont()->setBold(true);
    //     $sheet->getStyle('J6')->getFont()->setName('Times New Roman');
    //     $sheet->getStyle('J6')->getFont()->setSize(10);
    //     $sheet->getStyle('J6')->getAlignment()->setHorizontal('center');
    //     $sheet->getStyle('J6')->getAlignment()->setVertical('center');
    //     $sheet->getStyle('J6:P6')->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);

    //     // Sub-header untuk kolom Top 3 Produksi
    //     $sheet->setCellValue('J7', 'NO');
    //     $sheet->setCellValue('K7', 'KODE KARTU');
    //     $sheet->setCellValue('L7', 'NAMA KARYAWAN');
    //     $sheet->setCellValue('M7', 'L/P');
    //     $sheet->setCellValue('N7', 'TGL MASUK');
    //     $sheet->setCellValue('O7', 'BAGIAN');
    //     $sheet->setCellValue('P7', 'AVG USED NEEDLE');

    //     $sheet->getStyle('J7:P7')->getFont()->setBold(true);
    //     $sheet->getStyle('J7:P7')->getFont()->setName('Times New Roman');
    //     $sheet->getStyle('J7:P7')->getFont()->setSize(10);
    //     $sheet->getStyle('J7:P7')->getAlignment()->setHorizontal('center');
    //     $sheet->getStyle('J7:P7')->getAlignment()->setVertical('center');
    //     $sheet->getStyle('J7:P7')->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
    //     $sheet->getStyle('J7:P7')->getAlignment()->setWrapText(true);

    //     // column dimension
    //     $sheet->getColumnDimension('J')->setWidth(5);
    //     $sheet->getColumnDimension('K')->setWidth(10);
    //     $sheet->getColumnDimension('L')->setWidth(20);
    //     $sheet->getColumnDimension('M')->setWidth(5);
    //     $sheet->getColumnDimension('N')->setWidth(15);
    //     $sheet->getColumnDimension('O')->setWidth(10);
    //     $sheet->getColumnDimension('P')->setWidth(10);

    //     // Data Top 3 Produksi
    //     $startRow = 8;
    //     $no = 1;
    //     foreach ($getTop3 as $row) {
    //         $sheet->setCellValue('J' . $startRow, $no);
    //         $sheet->setCellValue('K' . $startRow, $row['kode_kartu']);
    //         $sheet->setCellValue('L' . $startRow, $row['nama_karyawan']);
    //         $sheet->setCellValue('M' . $startRow, $row['jenis_kelamin']);
    //         $sheet->setCellValue('N' . $startRow, $row['tgl_masuk']);
    //         $sheet->setCellValue('O' . $startRow, $row['nama_bagian']);
    //         $sheet->setCellValue('P' . $startRow, $row['avg_used_needle']);

    //         $sheet->getStyle('J' . $startRow . ':P' . $startRow)->getFont()->setName('Times New Roman');
    //         $sheet->getStyle('J' . $startRow . ':P' . $startRow)->getFont()->setSize(10);
    //         $sheet->getStyle('J' . $startRow . ':P' . $startRow)->getAlignment()->setHorizontal('center');
    //         $sheet->getStyle('J' . $startRow . ':P' . $startRow)->getAlignment()->setVertical('center');
    //         $sheet->getStyle('J' . $startRow . ':P' . $startRow)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
    //         $sheet->getStyle('J' . $startRow . ':P' . $startRow)->getAlignment()->setWrapText(true);

    //         $no++;
    //         $startRow++;
    //     }



    //     $spreadsheet->getActiveSheet()->setTitle('REPORT SUMMARY JARUM');

    //     $filename = 'REPORT SUMMARY JARUM ' . date('d-m-Y H:i:s') . '.xlsx';

    //     header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    //     header('Content-Disposition: attachment;filename="' . $filename . '"');
    //     header('Cache-Control: max-age=0');

    //     $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
    //     $writer->save('php://output');
    //     exit;
    // }
    public function excelSummaryJarum($area, $id_batch)
    {
        $summaryJarum = $this->summaryJarum->getSummaryJarum($area, $id_batch);
        $namaBatch = $this->batchModel->where('id_batch', $id_batch)->first();
        $start_dates = array_column($summaryJarum, 'end_date');
        // Konversi setiap start_date menjadi nama bulan
        $bulan = array_unique(array_map(fn($date) => date('F', strtotime($date)), $start_dates));

        // Urutkan bulan
        $month_order = ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'];
        usort($bulan, fn($a, $b) => array_search($a, $month_order) - array_search($b, $month_order));

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
        $sheet->setCellValue('C3', ': ' . $area);
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
        $sheet->mergeCells('G6:I6');
        $sheet->setCellValue('G6', 'NEEDLE USED');
        // Masukkan data bulan ke G7, H7, I7, dst.
        $col = 'G';
        foreach ($bulan as $index => $bln) {
            $sheet->setCellValue($col . '7', substr($bln, 0, 3)); // Gunakan 3 huruf awal bulan
            $col++;
        }
        $sheet->setCellValue('J6', 'AVG USED NEEDLE');
        $sheet->mergeCells('J6:J7');

        $sheet->getStyle('A6:J7')->getFont()->setBold(true);
        $sheet->getStyle('A6:J7')->getFont()->setName('Times New Roman');
        $sheet->getStyle('A6:J7')->getFont()->setSize(10);
        $sheet->getStyle('A6:J7')->getAlignment()->setHorizontal('center');
        $sheet->getStyle('A6:J7')->getAlignment()->setVertical('center');
        $sheet->getStyle('A6:J7')->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
        $sheet->getStyle('A6:J7')->getAlignment()->setWrapText(true);

        $sheet->getColumnDimension('A')->setWidth(5);
        $sheet->getColumnDimension('B')->setWidth(10);
        $sheet->getColumnDimension('C')->setWidth(20);
        $sheet->getColumnDimension('D')->setWidth(5);
        $sheet->getColumnDimension('E')->setWidth(15);
        $sheet->getColumnDimension('F')->setWidth(10);
        $sheet->getColumnDimension('J')->setWidth(10);

        $startRow = 8;
        $no = 1;

        // Array untuk menyimpan data unik berdasarkan kode kartu
        $groupedData = [];

        foreach ($summaryJarum as $row) {
            $kode_kartu = $row['kode_kartu'];
            if (!isset($groupedData[$kode_kartu])) {
                // Jika kode kartu belum ada, simpan data awal
                $groupedData[$kode_kartu] = [
                    'kode_kartu'    => $row['kode_kartu'],
                    'nama_karyawan' => $row['nama_karyawan'],
                    'jenis_kelamin' => $row['jenis_kelamin'],
                    'tgl_masuk'     => $row['tgl_masuk'],
                    'nama_bagian'   => $row['nama_bagian'],
                    'used_needle'   => array_fill_keys($bulan, 0), // Inisialisasi penggunaan jarum per bulan
                    'hari_kerja'    => array_fill_keys($bulan, 0), // Inisialisasi hari kerja per bulan
                ];
            }
            // Menghitung jumlah hari kerja dalam bulan tersebut
            $startDate = new DateTime($row['start_date']);
            $endDate   = new DateTime($row['end_date']);
            $jumlahHari = $endDate->diff($startDate)->days + 1; // Total hari dalam periode
            $hariKerja = $jumlahHari - (int) $row['jml_libur']; // Hari kerja setelah dikurangi libur

            // Ambil jumlah hari kerja dari tabel periode
            $periode = $this->periodeModel->where('start_date <=', $row['end_date'])
                ->where('end_date >=', $row['end_date'])
                ->first();

            if ($periode) {
                $jumlah_hari_kerja = ((strtotime($periode['end_date']) - strtotime($periode['start_date'])) / (60 * 60 * 24)) + 1 - $periode['jml_libur'];
                if ($jumlah_hari_kerja > 0) {
                    $bulanData = date('F', strtotime($row['end_date']));
                    $groupedData[$kode_kartu]['used_needle'][$bulanData] += round($row['total_jarum'] / $jumlah_hari_kerja);
                }
            }
        }

        // Loop untuk memasukkan data ke dalam Excel
        foreach ($groupedData as $data) {

            $sheet->setCellValue('A' . $startRow, $no);
            $sheet->setCellValue('B' . $startRow, $data['kode_kartu']);
            $sheet->setCellValue('C' . $startRow, $data['nama_karyawan']);
            $sheet->setCellValue('D' . $startRow, $data['jenis_kelamin']);
            $sheet->setCellValue('E' . $startRow, $data['tgl_masuk']);
            $sheet->setCellValue('F' . $startRow, $data['nama_bagian']);

            $colProd = 'G';
            $totalProduksi = 0;
            $totalHariKerja = 0;
            $jumlahBulan = count($bulan);

            // Loop bulan untuk memasukkan produksi & bs
            foreach ($bulan as $bln) {
                $produksiBulan = $data['used_needle'][$bln];
                $hariKerjaBulan = $data['hari_kerja'][$bln];
                $totalProduksi += $produksiBulan;
                $totalHariKerja += $hariKerjaBulan;
                $sheet->setCellValue($colProd . $startRow, $produksiBulan);

                // Geser ke kolom berikutnya
                $colProd++;
            }

            // Hitung rata-rata penggunaan jarum berdasarkan 3 bulan
            $rataJarumPerBatch = $jumlahBulan > 0 ? round($totalProduksi / $jumlahBulan) : 0;
            // dd($rataJarumPerBatch);
            // Masukkan rata-rata ke kolom yang sesuai
            $sheet->setCellValue('J' . $startRow, $rataJarumPerBatch);

            $sheet->getStyle('A' . $startRow . ':J' . $startRow)->getFont()->setName('Times New Roman');
            $sheet->getStyle('A' . $startRow . ':J' . $startRow)->getFont()->setSize(10);
            $sheet->getStyle('A' . $startRow . ':J' . $startRow)->getAlignment()->setHorizontal('center');
            $sheet->getStyle('A' . $startRow . ':J' . $startRow)->getAlignment()->setVertical('center');
            $sheet->getStyle('A' . $startRow . ':J' . $startRow)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
            $sheet->getStyle('A' . $startRow . ':J' . $startRow)->getAlignment()->setWrapText(true);

            $no++;
            $startRow++;
        }

        // Header untuk Top 3 Rata-Rata Penggunaan Jarum
        $sheet->mergeCells('M6:S6');
        $sheet->setCellValue('M6', 'TOP 3 AVG USED NEEDLE');
        $sheet->getStyle('M6')->getFont()->setBold(true);
        $sheet->getStyle('M6')->getFont()->setName('Times New Roman');
        $sheet->getStyle('M6')->getFont()->setSize(10);
        $sheet->getStyle('M6')->getAlignment()->setHorizontal('center');
        $sheet->getStyle('M6')->getAlignment()->setVertical('center');
        $sheet->getStyle('M6:S6')->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);

        // Sub-header untuk kolom Top 3 Produksi
        $sheet->setCellValue('M7', 'NO');
        $sheet->setCellValue('N7', 'KODE KARTU');
        $sheet->setCellValue('O7', 'NAMA KARYAWAN');
        $sheet->setCellValue('P7', 'L/P');
        $sheet->setCellValue('Q7', 'TGL MASUK');
        $sheet->setCellValue('R7', 'BAGIAN');
        $sheet->setCellValue('S7', 'AVG USED NEEDLE');

        $sheet->getStyle('M7:S7')->getFont()->setBold(true);
        $sheet->getStyle('M7:S7')->getFont()->setName('Times New Roman');
        $sheet->getStyle('M7:S7')->getFont()->setSize(10);
        $sheet->getStyle('M7:S7')->getAlignment()->setHorizontal('center');
        $sheet->getStyle('M7:S7')->getAlignment()->setVertical('center');
        $sheet->getStyle('M7:S7')->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
        $sheet->getStyle('M7:S7')->getAlignment()->setWrapText(true);

        // column dimension
        $sheet->getColumnDimension('M')->setWidth(5);
        $sheet->getColumnDimension('N')->setWidth(10);
        $sheet->getColumnDimension('O')->setWidth(20);
        $sheet->getColumnDimension('P')->setWidth(5);
        $sheet->getColumnDimension('Q')->setWidth(15);
        $sheet->getColumnDimension('R')->setWidth(10);
        $sheet->getColumnDimension('S')->setWidth(10);

        // Data Top 3 Minimum Penggunaan Jarum
        // Urutkan 7 data ini berdasarkan BS terkecil
        usort($groupedData, function ($a, $b) {
            return array_sum($a['used_needle']) <=> array_sum($b['used_needle']); // Ascending
        });
        // Ambil 3 data Min Penggunaan Jarum
        $getTop3 = array_slice($groupedData, 0, 3);

        $startRow = 8;
        $no = 1;

        foreach ($getTop3 as $row) {

            $avgNeedle = array_sum($row['used_needle']) / count($bulan);

            $sheet->setCellValue('M' . $startRow, $no);
            $sheet->setCellValue('N' . $startRow, $row['kode_kartu']);
            $sheet->setCellValue('O' . $startRow, $row['nama_karyawan']);
            $sheet->setCellValue('P' . $startRow, $row['jenis_kelamin']);
            $sheet->setCellValue('Q' . $startRow, $row['tgl_masuk']);
            $sheet->setCellValue('R' . $startRow, $row['nama_bagian']);
            $sheet->setCellValue('S' . $startRow, round($avgNeedle));

            $sheet->getStyle('M' . $startRow . ':S' . $startRow)->getFont()->setName('Times New Roman');
            $sheet->getStyle('M' . $startRow . ':S' . $startRow)->getFont()->setSize(10);
            $sheet->getStyle('M' . $startRow . ':S' . $startRow)->getAlignment()->setHorizontal('center');
            $sheet->getStyle('M' . $startRow . ':S' . $startRow)->getAlignment()->setVertical('center');
            $sheet->getStyle('M' . $startRow . ':S' . $startRow)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
            $sheet->getStyle('M' . $startRow . ':S' . $startRow)->getAlignment()->setWrapText(true);

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
