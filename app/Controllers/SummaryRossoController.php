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
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\IOFactory;
use DateTime;


class SummaryRossoController extends BaseController
{
    protected $karyawanmodel;
    protected $absenmodel;
    protected $usermodel;
    protected $summaryRosso;
    protected $periodeModel;
    protected $penilaianmodel;

    public function __construct()
    {
        $this->karyawanmodel = new KaryawanModel();
        $this->absenmodel = new AbsenModel();
        $this->usermodel = new UserModel();
        $this->summaryRosso = new SummaryRossoModel();
        $this->periodeModel = new PeriodeModel();
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
                return redirect()->to(base_url('Monitoring/dataRosso'))->with('error', 'Invalid file type. Please upload an Excel file.');
            }

            $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($file->getTempName());
            $dataSheet = $spreadsheet->getActiveSheet();
            $startRow = 2;

            $bagianModel = new \App\Models\BagianModel();
            $this->karyawanmodel = new \App\Models\KaryawanModel();
            $this->summaryRosso = new \App\Models\SummaryRossoModel();

            $periode = $this->request->getPost('periode');
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
                    $errorMessage .= "Jenis Kelamin must be L or P. ";
                }

                if (!$tglProduksi) {
                    $tglProduksi = \DateTime::createFromFormat('d/m/Y', $tglProduksiRaw); // Fallback jika format lain digunakan.
                }

                if (!$tglProduksi) {
                    $isValid = false;
                    $errorMessage .= "Invalid Tanggal Produksi format. ";
                } else {
                    // $cekdata = $this->summaryRosso
                    //     ->where('id_periode', $periode)
                    //     ->where('id_karyawan', $karyawan['id_karyawan'])
                    //     ->where('tgl_prod_rosso', $tglProduksi->format('Y-m-d'))
                    //     ->first();

                    // if ($cekdata) {
                    //     $isValid = false;
                    //     $errorMessage .= "Data already exists. ";
                    // }
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
                        'id_periode' => $periode,
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

    public function tampilPerBatch()
    {
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
            'active8' => ''
        ];

        return view('Rosso/tampilPerBatch', $data);
    }
}
