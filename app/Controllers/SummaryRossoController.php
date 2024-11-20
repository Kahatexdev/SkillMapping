<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;
use App\Models\KaryawanModel;
use App\Models\AbsenModel;
use App\Models\UserModel;
use App\Models\SummaryRossoModel;
use App\Models\PenilaianModel;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\IOFactory;


class SummaryRossoController extends BaseController
{
    protected $karyawanmodel;
    protected $absenmodel;
    protected $usermodel;
    protected $summaryRosso;
    protected $penilaianmodel;

    public function __construct()
    {
        $this->karyawanmodel = new KaryawanModel();
        $this->absenmodel = new AbsenModel();
        $this->usermodel = new UserModel();
        $this->summaryRosso = new SummaryRossoModel();
        $this->penilaianmodel = new PenilaianModel();
    }

    public function downloadTemplate()
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Header Kolom
        $sheet->setCellValue('A1', 'KODE KARTU')
        ->setCellValue('B1', 'NAMA LENGKAP')
        ->setCellValue('C1', 'L/P')
            ->setCellValue('D1', 'BAGIAN')
            ->setCellValue('E1', 'AREA')
            ->setCellValue('F1', 'TGL PRODUKSI')
            ->setCellValue('G1', 'PROD')
            ->setCellValue('H1', 'REWORK')
            ->setCellValue('I1', 'REJECT');

        // Lebar Kolom
        foreach (range('A', 'I') as $col) {
            $sheet->getColumnDimension($col)->setWidth(20);
        }

        // Style Header
        $sheet->getStyle('A1:I1')->getFont()->setBold(true);
        $sheet->getStyle('A1:I1')->getFill()->setFillType('solid')->getStartColor()->setARGB('FFA0A0A0');
        $sheet->getStyle('A1:I1')->getAlignment()->setHorizontal('center');

        // Data Contoh
        $sheet->setCellValue('A2', 'KK0001')
        ->setCellValue('B2', 'JOHN DOE')
        ->setCellValue('C2', 'L')
            ->setCellValue('D2', 'ROSSO')
            ->setCellValue('E2', 'KK1')
            ->setCellValue('F2', '2024-10-20')
            ->setCellValue('G2', '6086')
            ->setCellValue('H2', '13')
            ->setCellValue('I2', '0');

        $fileName = 'Template_Produksi_Rosso.xlsx';

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
                return redirect()->to(base_url('monitoring/dataSummaryRosso'))->with('error', 'Invalid file type. Please upload an Excel file.');
            }

            $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($file->getTempName());
            $dataSheet = $spreadsheet->getActiveSheet();
            $startRow = 2;

            $bagianModel = new \App\Models\BagianModel();
            $this->karyawanmodel = new \App\Models\KaryawanModel();
            $this->summaryRosso = new \App\Models\SummaryRossoModel();

            $successCount = 0;
            $errorCount = 0;
            $errorMessages = [];

            for ($row = $startRow; $row <= $dataSheet->getHighestRow(); $row++) {
                $isValid = true;
                $errorMessage = "Row {$row}: ";

                $kodeKartu = $dataSheet->getCell('A' . $row)->getValue();
                $namaLengkap = $dataSheet->getCell('B' . $row)->getValue();
                $jenisKelamin = $dataSheet->getCell('C' . $row)->getValue();
                $bagian = $dataSheet->getCell('D' . $row)->getValue();
                $area = $dataSheet->getCell('E' . $row)->getValue();
                $tglProduksiRaw = $dataSheet->getCell('F' . $row)->getFormattedValue();
                $tglProduksi = \DateTime::createFromFormat('Y-m-d', $tglProduksiRaw);
                $produksi = $dataSheet->getCell('G' . $row)->getValue() ?? 0;
                $rework = $dataSheet->getCell('H' . $row)->getValue() ?? 0;
                $reject = $dataSheet->getCell('I' . $row)->getValue() ?? 0;

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
                    $errorMessage .= "Jenis Kelamin must be 'L' or 'P'. ";
                }

                if (!$tglProduksi) {
                    $tglProduksi = \DateTime::createFromFormat('d/m/Y', $tglProduksiRaw); // Fallback jika format lain digunakan.
                }

                if (!$tglProduksi) {
                    $isValid = false;
                    $errorMessage .= "Invalid Tanggal Produksi format. ";
                } else {
                    $cekdata = $this->summaryRosso
                    ->where('id_karyawan', $karyawan['id_karyawan'])
                    ->where('tgl_prod_rosso', $tglProduksi->format('Y-m-d'))
                    ->first();

                    if ($cekdata) {
                        $isValid = false;
                        $errorMessage .= "Data already exists. ";
                    }
                }

                if (empty($bagian)) {
                    $isValid = false;
                    $errorMessage .= "Bagian is required. ";
                } else {
                    $bagianData = $bagianModel->where('nama_bagian', $bagian)->where('area_utama', $area)->first();
                    if (!$bagianData) {
                        $isValid = false;
                        $errorMessage .= "Bagian not found. ";
                    }
                }

                $perbaikan = $rework + $reject;

                if ($isValid) {
                    $data = [
                        'id_karyawan' => $karyawan['id_karyawan'],
                        'tgl_prod_rosso' => $tglProduksi->format('Y-m-d'),
                        'qty_prod_rosso' => $produksi,
                        'qty_bs' => $perbaikan
                    ];

                    
                    $this->summaryRosso->insert($data);

                    $successMessage = "Data karyawan berhasil disimpan.";
                    $successCount++;
                } else {
                    $errorMessages[] = $errorMessage;
                    $errorCount++;
                }
            }
            // dd($errorCount);
            if ($errorCount > 0) {
                $errorMessages = implode("<br>", $errorMessages);
                // dd ($errorMessages);
                return redirect()->to(base_url('monitoring/dataSummaryRosso'))->with('error', "{$errorCount} data failed to upload. <br> {$errorMessages}");
            } else {
                return redirect()->to(base_url('monitoring/dataSummaryRosso'))->with('success', "{$successCount} data successfully uploaded.");
            }
        } else {
            return redirect()->to(base_url('monitoring/dataSummaryRosso'))->with('error', 'Invalid file.');
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
        return view('summaryRosso/create', $data);

    }

    public function store()
    {

        //tambahkan validasi stardate dan end date
        $karyawan = $this->karyawanmodel->where('kode_kartu', $this->request->getPost('kode_kartu'))->where('nama_karyawan', $this->request->getPost('nama_karyawan'))->first();
        if (!$karyawan) {
            return redirect()->to(base_url('monitoring/dataSummaryRosso'))->with('error', 'Kode Kartu not found.');
        } else {
            $data = [
                'id_karyawan' => $karyawan['id_karyawan'],
                'start_date' => $this->request->getPost('start_date'),
                'end_date' => $this->request->getPost('end_date'),
                'qty_bs' => $this->request->getPost('qty_bs'),
                'qty_prod_rosso' => $this->request->getPost('qty_prod_rosso')
            ];
            $this->summaryRosso->insert($data);
            return redirect()->to(base_url('monitoring/dataSummaryRosso'))->with('success', 'Data successfully added.');
        }
    }

    public function show($id)
    {
        //
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
        $karyawan = $this->karyawanmodel->where('kode_kartu', $this->request->getPost('kode_kartu'))->where('nama_karyawan', $this->request->getPost('nama_karyawan'))->first();
        if (!$karyawan) {
            return redirect()->to(base_url('monitoring/dataSummaryRosso'))->with('error', 'Kode Kartu not found.');
        } else {
            $data = [
                'id_karyawan' => $karyawan['id_karyawan'],
                'start_date' => $this->request->getPost('start_date'),
                'end_date' => $this->request->getPost('end_date'),
                'qty_bs' => $this->request->getPost('qty_bs'),
                'qty_prod_rosso' => $this->request->getPost('qty_prod_rosso')
            ];
            $this->summaryRosso->update($id, $data);
            return redirect()->to(base_url('monitoring/dataSummaryRosso'))->with('success', 'Data successfully updated.');
        }
    }

    public function delete($id)
    {
        $this->summaryRosso->delete($id);
        return redirect()->to(base_url('monitoring/dataSummaryRosso'))->with('success', 'Data successfully deleted.');
    }
}
