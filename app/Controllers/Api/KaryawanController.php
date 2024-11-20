<?php

namespace App\Controllers\Api;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\RESTful\ResourceController;
use CodeIgniter\API\ResponseTrait;
use App\Models\KaryawanModel;
use App\Models\BagianModel;

class KaryawanController extends ResourceController
{
    protected $format = 'json'; // Response format JSON
    protected $karyawanModel;
    protected $bagianModel;
    /*db*/
    protected $db;

    public function __construct()
    {
        $this->karyawanModel = new KaryawanModel();
        $this->bagianModel = new BagianModel();
        $this->validation = \Config\Services::validation();
        $this->db = \Config\Database::connect();
    }
    public function index()
    {
        $data = $this->karyawanModel->findAll();

        return $this->respond($data, 200);
    }

    public function show($id = null)
    {
        $data = $this->karyawanModel->findById($id);
        if ($data) {
            return $this->respond($data);
        } else {
            return $this->failNotFound('No Data Found with id ' . $id);
        }
    }

    public function getKaryawanByAreaUtama($areaUtama)
    {
        $data = $this->karyawanModel->getKaryawanByAreaUtama($areaUtama);

        return $this->respond($data, 200);
    }

    public function getKaryawanByArea($area)
    {
        $data = $this->karyawanModel->getKaryawanByArea($area);

        return $this->respond($data, 200);
    }

    public function create()
    {
        $kodeKartu = $this->request->getPost('kode_kartu');
        $namaKaryawan = $this->request->getPost('nama_karyawan');
        $shift = $this->request->getPost('shift');
        $jenisKelamin = $this->request->getPost('jenis_kelamin');
        $libur = $this->request->getPost('libur');
        $liburTambahan = $this->request->getPost('libur_tambahan');
        $warnaBaju = $this->request->getPost('warna_baju');
        $statusBaju = $this->request->getPost('status_baju');
        $tglLahir = $this->request->getPost('tgl_lahir');
        $tglMasuk = $this->request->getPost('tgl_masuk');
        $bagian = $this->request->getPost('nama_bagian');
        $area = $this->request->getPost('area');
        $areaUtama = $this->request->getPost('area_utama');
        $statusAktif = $this->request->getPost('status_aktif');

        $idBagian = $this->bagianModel->getIdBagian($bagian, $area, $areaUtama);

        $data = [
            'kode_kartu' => $kodeKartu,
            'nama_karyawan' => $namaKaryawan,
            'shift' => $shift,
            'jenis_kelamin' => $jenisKelamin,
            'libur' => $libur,
            'libur_tambahan' => $liburTambahan,
            'warna_baju' => $warnaBaju,
            'status_baju' => $statusBaju,
            'tgl_lahir' => $tglLahir,
            'tgl_masuk' => $tglMasuk,
            'id_bagian' => $idBagian,
            'status_aktif' => $statusAktif
        ];

        $validate = $this->validation->run($data, 'create_master_karyawan');
        $errors = $this->validation->getErrors();

        if ($errors) {
            return $this->fail($errors);
        }

        $this->karyawanModel->insert($data);

        $response = [
            'status' => 201,
            'error' => null,
            'message' => [
                'success' => 'Data Karyawan berhasil ditambahkan'
            ]
        ];

        return $this->respondCreated($response, 201);
    }

    public function update($id = null)
    {
        $data = $this->request->getRawInput();
        $data['id_karyawan'] = $id;

        $validate = $this->validation->run($data, 'update_master_karyawan');
        $errors = $this->validation->getErrors();

        if ($errors) {
            return $this->fail($errors);
        }

        if (!$this->karyawanModel->findById($id)) {
            return $this->fail('id tidak ditemukan');
        }

        $this->karyawanModel->update($id, $data);
        $response = [
            'status' => 200,
            'error' => null,
            'message' => [
                'success' => 'Data Karyawan berhasil diupdate'
            ]
        ];

        return $this->respond($response);

    }

    public function delete($id = null)
    {
        if (!$this->karyawanModel->findById($id)) {
            return $this->fail('id tidak ditemukan');
        }

        if ($this->karyawanModel->delete($id)) {
            return $this->respondDeleted(['id' => $id . ' Deleted']);
        }
    }
}
