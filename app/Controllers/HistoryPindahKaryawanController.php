<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;

use App\Models\KaryawanModel;

class HistoryPindahKaryawanController extends BaseController
{

    protected $karyawanModel;

    public function __construct()
    {
        $this->karyawanModel = new KaryawanModel();
    }
    public function index()
    {
        //
    }

    public function reportExcel()
    {
        // Ambil data karyawan
        $dataKaryawan = $this->karyawanModel->exportPindahKaryawanAll();
        // dd ($dataKaryawan);
        // Definisikan urutan kode kartu berdasarkan area
        // Tentukan urutan prefix kode kartu secara global
        $sortOrders = [
            'KKMA',
            'KKMB',
            'KKMC',
            'KKMNS',
            'KKSA',
            'KKSB',
            'KKSC',
            'KKJHA',
            'KKJHB',
            'KKJHC',
            'KK2MA',
            'KK2MB',
            'KK2MC',
            'KK2MNS',
            'KK2SA',
            'KK2SB',
            'KK2SC',
            'KK5A',
            'KK5B',
            'KK5C',
            'KK5NS',
            'KK7A',
            'KK7B',
            'KK7C',
            'KK7NS',
            'KK8MA',
            'KK8MB',
            'KK8MC',
            'KK8MNS',
            'KK8SA',
            'KK8SB',
            'KK8SC',
            'KK9A',
            'KK9B',
            'KK9C',
            'KK9NS',
            'KK10A',
            'KK10B',
            'KK10C',
            'KK10NS',
            'KK11A',
            'KK11B',
            'KK11C',
            'KK11NS',
        ];
        // Urutkan data karyawan berdasarkan kode kartu
        usort($dataKaryawan, function ($a, $b) use ($sortOrders) {
            // Ekstrak prefix kode kartu
            preg_match('/^[A-Z]+/', $a['kode_kartu'], $matchA);
            preg_match('/^[A-Z]+/', $b['kode_kartu'], $matchB);

            $prefixA = $matchA[0] ?? '';
            $prefixB = $matchB[0] ?? '';

            // Cari posisi prefix di array $sortOrders
            $posA = array_search($prefixA, $sortOrders);
            $posB = array_search($prefixB, $sortOrders);

            // Jika tidak ditemukan, posisikan di akhir
            $posA = ($posA === false) ? PHP_INT_MAX : $posA;
            $posB = ($posB === false) ? PHP_INT_MAX : $posB;

            // Bandingkan berdasarkan posisi prefix
            if ($posA !== $posB) {
                return $posA <=> $posB;
            }

            // Jika prefix sama, bandingkan berdasarkan angka di kode kartu
            preg_match('/\d+/', $a['kode_kartu'], $numberA);
            preg_match('/\d+/', $b['kode_kartu'], $numberB);

            $numA = (int)($numberA[0] ?? PHP_INT_MAX); // Default jika tidak ada angka
            $numB = (int)($numberB[0] ?? PHP_INT_MAX);

            return $numA <=> $numB;
        });
        // dd($dataKaryawan);
        // Buat spreadsheet
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Style
        $styleTitle = [
            'font' => [
                'size' => 14,
                'bold' => true,
                'name' => 'Arial',
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER, // Alignment rata tengah
                'vertical' => Alignment::VERTICAL_CENTER, // Alignment rata tengah
            ],
        ];
        $styleHeader = [
            'font' => [
                'size' => 10,
                'bold' => true,
                'name' => 'Arial',
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER, // Alignment rata tengah
                'vertical' => Alignment::VERTICAL_CENTER, // Alignment rata tengah
            ],
            'borders' => [
                'outline' => [
                    'borderStyle' => Border::BORDER_THIN, // Gaya garis tipis
                    'color' => ['argb' => 'FF000000'],    // Warna garis hitam
                ],
            ],
        ];

        $styleAlignCenter = [
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
        ];

        $sheet->setCellValue('A1', 'Report History Pindah Karyawan');
        $sheet->mergeCells('A1:S2')->getStyle('A1:S2')->applyFromArray($styleTitle);

        $sheet->setCellValue('A3', 'No');
        $sheet->getStyle('A3')->applyFromArray($styleHeader);

        $sheet->setCellValue('B3', 'Kode Kartu');
        $sheet->getStyle('B3')->applyFromArray($styleHeader);

        $sheet->setCellValue('C3', 'Nama Karyawan');
        $sheet->getStyle('C3')->applyFromArray($styleHeader);

        $sheet->setCellValue('D3', 'Shift');
        $sheet->getStyle('D3')->applyFromArray($styleHeader);

        $sheet->setCellValue('E3', 'Jenis Kelamin');
        $sheet->getStyle('E3')->applyFromArray($styleHeader);

        $sheet->setCellValue('F3', 'Libur');
        $sheet->getStyle('F3')->applyFromArray($styleHeader);

        $sheet->setCellValue('G3', 'Libur Tambahan');
        $sheet->getStyle('G3')->applyFromArray($styleHeader);

        $sheet->setCellValue('H3', 'Warna Baju');
        $sheet->getStyle('H3')->applyFromArray($styleHeader);

        $sheet->setCellValue('I3', 'Status Baju');
        $sheet->getStyle('I3')->applyFromArray($styleHeader);

        $sheet->setCellValue('J3', 'Tanggal Lahir');
        $sheet->getStyle('J3')->applyFromArray($styleHeader);

        $sheet->setCellValue('K3', 'Tanggal Masuk');
        $sheet->getStyle('K3')->applyFromArray($styleHeader);

        $sheet->setCellValue('L3', 'Bagian Asal');
        $sheet->getStyle('L3')->applyFromArray($styleHeader);

        // $sheet->setCellValue('M3', 'Area Utama Asal');
        // $sheet->getStyle('M3')->applyFromArray($styleHeader);

        $sheet->setCellValue('M3', 'Area Asal');
        $sheet->getStyle('M3')->applyFromArray($styleHeader);

        $sheet->setCellValue('N3', 'Bagian Aktual');
        $sheet->getStyle('N3')->applyFromArray($styleHeader);

        // $sheet->setCellValue('P3', 'Area Utama Aktual');
        // $sheet->getStyle('P3')->applyFromArray($styleHeader);

        $sheet->setCellValue('O3', 'Area Aktual');
        $sheet->getStyle('O3')->applyFromArray($styleHeader);

        $sheet->setCellValue('P3', 'Tanggal Pindah');
        $sheet->getStyle('P3')->applyFromArray($styleHeader);

        $sheet->setCellValue('Q3', 'Keterangan');
        $sheet->getStyle('Q3')->applyFromArray($styleHeader);
        
        $sheet->setCellValue('R3', 'Status');
        $sheet->getStyle('R3')->applyFromArray($styleHeader);
        
        $sheet->setCellValue('S3', 'Diupdate Oleh');
        $sheet->getStyle('S3')->applyFromArray($styleHeader);


        $styleData = [
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['argb' => 'FF000000'],
                ],
            ],
        ];

        $row = 4;
        $no = 1;
        foreach ($dataKaryawan as $key => $id) {
            $sheet->setCellValue('A' . $row, $no++);
            $sheet->setCellValue('B' . $row, $id['kode_kartu']);
            $sheet->setCellValue('C' . $row, $id['nama_karyawan']);
            $sheet->setCellValue('D' . $row, $id['shift']);
            $sheet->setCellValue('E' . $row, $id['jenis_kelamin']);
            $sheet->setCellValue('F' . $row, $id['libur']);
            $sheet->setCellValue('G' . $row, $id['libur_tambahan']);
            $sheet->setCellValue('H' . $row, $id['warna_baju']);
            $sheet->setCellValue('I' . $row, $id['status_baju']);
            $sheet->setCellValue('J' . $row, $id['tgl_lahir']);
            $sheet->setCellValue('K' . $row, $id['tgl_masuk']);
            $sheet->setCellValue('L' . $row, $id['nama_bagian_asal']);
            if ($id['area_asal'] == null) {
                $sheet->setCellValue('M' . $row, $id['area_utama_asal']);
            } else {
                $sheet->setCellValue('M' . $row, $id['area_asal']);
            }
            // $sheet->setCellValue('M' . $row, $id['area_utama_asal']);
            // $sheet->setCellValue('N' . $row, $id['area_asal']);
            $sheet->setCellValue('N' . $row, $id['bagian_aktual']);
            if ($id['area_aktual'] == null) {
                $sheet->setCellValue('O' . $row, $id['area_utama_aktual']);
            } else {
                $sheet->setCellValue('O' . $row, $id['area_aktual']);
            }
            // $sheet->setCellValue('P' . $row, $id['area_utama_aktual']);
            // $sheet->setCellValue('Q' . $row, $id['area_aktual']);
            $sheet->setCellValue('P' . $row, $id['tgl_pindah']);
            $sheet->setCellValue('Q' . $row, $id['keterangan']);
            $sheet->setCellValue('R' . $row, $id['status_aktif']);
            $sheet->setCellValue('S' . $row, $id['username']);
            $row++;
        }

        // total karyawan
        $sheet->setCellValue('A' . $row, 'Total Karyawan');
        $sheet->mergeCells('A' . $row . ':B' . $row);
        $sheet->getStyle('A' . $row . ':B' . $row)->applyFromArray($styleHeader);
        $sheet->setCellValue('C' . $row, count($dataKaryawan));
        $sheet->getStyle('C' . $row)->applyFromArray($styleHeader);
        $sheet->setCellValue('D' . $row, 'Org');
        // $sheet->mergeCells('D' . $row . ':S' . $row);
        $sheet->getStyle('D' . $row . ':D' . $row)->applyFromArray($styleHeader);


        // Terapkan gaya border ke seluruh data
        $dataRange = 'A4:S' . ($row - 1); // Dari baris 4 sampai baris terakhir
        $sheet->getStyle($dataRange)->applyFromArray($styleData);

        // Terapkan alignment rata-tengah ke seluruh data
        $sheet->getStyle($dataRange)->applyFromArray($styleAlignCenter);

        // Autosize untuk setiap kolom
        foreach (range('A', 'S') as $columnID) {
            $sheet->getColumnDimension($columnID)->setAutoSize(true);
        }

        // Set judul file dan header untuk download
        $filename = 'Report_History_Pindah_Karyawan_' . date('YmdHis') . '.xlsx';
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Cache-Control: max-age=0');

        // Tulis file excel ke output
        $writer = new Xlsx($spreadsheet);
        $writer->save('php://output');
        exit;
    }
}
