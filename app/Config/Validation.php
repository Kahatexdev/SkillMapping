<?php

namespace Config;

use CodeIgniter\Config\BaseConfig;
use CodeIgniter\Validation\StrictRules\CreditCardRules;
use CodeIgniter\Validation\StrictRules\FileRules;
use CodeIgniter\Validation\StrictRules\FormatRules;
use CodeIgniter\Validation\StrictRules\Rules;

class Validation extends BaseConfig
{
    // --------------------------------------------------------------------
    // Setup
    // --------------------------------------------------------------------

    /**
     * Stores the classes that contain the
     * rules that are available.
     *
     * @var list<string>
     */
    public array $ruleSets = [
        Rules::class,
        FormatRules::class,
        FileRules::class,
        CreditCardRules::class,
    ];

    /**
     * Specifies the views that are used to display the
     * errors.
     *
     * @var array<string, string>
     */
    public array $templates = [
        'list'   => 'CodeIgniter\Validation\Views\list',
        'single' => 'CodeIgniter\Validation\Views\single',
    ];

    // --------------------------------------------------------------------
    // Rules
    // --------------------------------------------------------------------

    public $create_master_karyawan = [
        'kode_kartu' => [
            'label' => 'Kode Kartu',
            'rules' => 'required|is_unique[karyawan.kode_kartu]',
        ],
        'nama_karyawan' => [
            'label' => 'Nama Karyawan',
            'rules' => 'required',
        ],
        'shift' => [
            'label' => 'Shift',
            'rules' => 'required',
        ],
        'jenis_kelamin' => [
            'label' => 'Jenis Kelamin',
            'rules' => 'required',
        ],
        'libur' => [
            'label' => 'Libur',
            'rules' => 'required',
        ],
        'libur_tambahan' => [
            'label' => 'Libur Tambahan',
            'rules' => 'required',
        ],
        'warna_baju' => [
            'label' => 'Warna Baju',
            'rules' => 'required',
        ],
        'status_baju' => [
            'label' => 'Status Baju',
            'rules' => 'required',
        ],
        'tgl_lahir' => [
            'label' => 'Tanggal Lahir',
            'rules' => 'required',
        ],
        'tgl_masuk' => [
            'label' => 'Tanggal Masuk',
            'rules' => 'required',
        ],
        'id_bagian' => [
            'label' => 'Bagian',
            'rules' => 'required',
        ],
        'status_aktif' => [
            'label' => 'Status Aktif',
            'rules' => 'required',
        ],
    ];

    public $update_master_karyawan =
    [
        'kode_kartu' => [
            'label' => 'Kode Kartu',
            'rules' => 'required|is_unique[karyawan.kode_kartu]',
        ],
        'nama_karyawan' => [
            'label' => 'Nama Karyawan',
            'rules' => 'required',
        ],
        'shift' => [
            'label' => 'Shift',
            'rules' => 'required',
        ],
        'jenis_kelamin' => [
            'label' => 'Jenis Kelamin',
            'rules' => 'required',
        ],
        'libur' => [
            'label' => 'Libur',
            'rules' => 'required',
        ],
        'libur_tambahan' => [
            'label' => 'Libur Tambahan',
            'rules' => 'required',
        ],
        'warna_baju' => [
            'label' => 'Warna Baju',
            'rules' => 'required',
        ],
        'status_baju' => [
            'label' => 'Status Baju',
            'rules' => 'required',
        ],
        'tgl_lahir' => [
            'label' => 'Tanggal Lahir',
            'rules' => 'required',
        ],
        'tgl_masuk' => [
            'label' => 'Tanggal Masuk',
            'rules' => 'required',
        ],
        'id_bagian' => [
            'label' => 'Bagian',
            'rules' => 'required',
        ],
        'status_aktif' => [
            'label' => 'Status Aktif',
            'rules' => 'required',
        ],
    ];
}
