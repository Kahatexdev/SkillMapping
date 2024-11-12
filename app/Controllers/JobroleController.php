<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;
use App\Models\JobroleModel;
use App\Models\BagianModel;

class JobroleController extends BaseController
{
    public function index()
    {
        $data = [
            'role' => session()->get('role'),
            'title' => 'Job Role',
            'active1' => '',
            'active2' => '',
            'active3' => '',
            'active4' => '',
            'active5' => '',
            'active6' => 'active',
        ];
        $model = new JobRoleModel();
        $rawData = $model->getAllData();

        // Mengubah data JSON ke array
        $dataArray = [];
        foreach ($rawData as $data) {
            $dataArray[$data['id_jobrole']][] = [
                'keterangan' => json_decode($data['keterangan']),
                'jobdesc' => json_decode($data['jobdesc']),
            ];
        }

        // Mengelompokkan data berdasarkan 'keterangan' (JOB dan 6S) dalam setiap id_jobrole
        $groupedData = [];

        foreach ($dataArray as $id_jobrole => $jobData) {
            // Mengelompokkan berdasarkan 'keterangan' di dalam setiap id_jobrole
            $groupedData[$id_jobrole] = [
                'JOB' => [],
                '6S' => []
            ];

            foreach ($jobData as $data) {
                // Mengiterasi array 'jobdesc' dan 'keterangan' berdasarkan indeks yang sama
                foreach ($data['jobdesc'] as $index => $jobdesc) {
                    $keterangan = $data['keterangan'][$index];

                    // Menambahkan jobdesc ke kategori yang sesuai berdasarkan 'keterangan'
                    if ($keterangan === 'JOB') {
                        $groupedData[$id_jobrole]['JOB'][] = $jobdesc;
                    } elseif ($keterangan === '6S') {
                        $groupedData[$id_jobrole]['6S'][] = $jobdesc;
                    }
                }
            }
        }

        // Pastikan kunci JOB dan 6S ada meskipun kosong
        foreach ($groupedData as $id_jobrole => $data) {
            if (!isset($groupedData[$id_jobrole]['JOB'])) {
                $groupedData[$id_jobrole]['JOB'] = [];
            }
            if (!isset($groupedData[$id_jobrole]['6S'])) {
                $groupedData[$id_jobrole]['6S'] = [];
            }
        }

        return view('monitoring/coba', [
            'role' => session()->get('role'),
            'title' => 'Job Role',
            'active1' => '',
            'active2' => '',
            'active3' => '',
            'active4' => '',
            'active5' => '',
            'active6' => 'active',
            'jobrole' => $rawData,
            'groupedData' => $groupedData
        ]);
    }


    public function create()
    {
        $bagianmodel = new BagianModel();

        $bagians = $bagianmodel->findAll();

        $data = [
            'role' => session()->get('role'),
            'title' => 'Job Role',
            'active1' => '',
            'active2' => '',
            'active3' => '',
            'active4' => '',
            'active5' => '',
            'active6' => 'active',
            'bagians' => $bagians
        ];

        return view('jobrole/create', $data);
    }

    public function store()
    {
        // Load the JobroleModel
        $jobroleModel = new JobroleModel();

        // Get form data
        $data = [
            'id_bagian' => $this->request->getPost('id_bagian'),
            'jobdesc' => $this->request->getPost('jobdesc'),
            'keterangan' => $this->request->getPost('keterangan'),
        ];

        // Encode only if `keterangan` or `jobdesc` is an array (to avoid double encoding)
        if (is_array($data['keterangan'])) {
            $data['keterangan'] = json_encode($data['keterangan']);
        }
        if (is_array($data['jobdesc'])) {
            $data['jobdesc'] = json_encode($data['jobdesc']);
        }

        // jika id_bagian sudah ada di database maka tidak bisa diinputkan lagi
        $id_bagian = $this->request->getPost('id_bagian');
        $jobrole = $jobroleModel->getJobRoleByBagianId($id_bagian);
        if ($jobrole) {
            return redirect()->back()->withInput()->with('error', 'Job role Untuk Bagian ini sudah ada.');
        }
        // dd($data);
        // Attempt to save the data
        if ($jobroleModel->insert($data)) {
            // If successful, redirect to a success page
            return redirect()->to(base_url('monitoring/dataJob'))->with('success', 'Job role Berhasil Ditambahkan.');
        } else {
            // If not successful, redirect back with an error
            return redirect()->back()->withInput()->with('error', 'Gagal Menyimpan Data Job Role.');
        }
    }

    public function edit($id)
    {
        $jobroleModel = new JobroleModel();
        $bagianModel = new BagianModel();

        // Ambil data job role berdasarkan id
        $jobrole = $jobroleModel->find($id);
        $bagians = $bagianModel->findAll();

        // Jika data jobrole tidak ditemukan, redirect atau tampilkan error
        if (!$jobrole) {
            return redirect()->back()->with('error', 'Data tidak ditemukan');
        }

        $data = [
            'role' => session()->get('role'),
            'title' => 'Job Role',
            'active1' => '',
            'active2' => '',
            'active3' => '',
            'active4' => '',
            'active5' => '',
            'active6' => 'active',
            'jobrole' => $jobrole,
            'bagians' => $bagians,
        ];

        return view('jobrole/edit', $data);
    }


    public function update($id)
    {
        // Load the JobroleModel
        $jobroleModel = new JobroleModel();

        // Get form data
        $data = [
            'id_bagian' => $this->request->getPost('id_bagian'),
            'status' => $this->request->getPost('status'),
            'jobdesc' => $this->request->getPost('jobdesc'),
            'keterangan' => $this->request->getPost('keterangan'),
        ];

        // Ensure jobdesc and keterangan are arrays before encoding them as JSON
        if (is_array($data['keterangan'])) {
            $data['keterangan'] = json_encode($data['keterangan']);
        }
        if (is_array($data['jobdesc'])) {
            $data['jobdesc'] = json_encode($data['jobdesc']);
        }

        // Attempt to update the record
        if ($jobroleModel->update($id, $data)) {
            // If successful, redirect to the success page
            return redirect()->to(base_url('monitoring/dataJob'))->with('success', 'Job role updated successfully.');
        } else {
            // If not successful, redirect back with an error message
            return redirect()->back()->withInput()->with('error', 'Failed to update job role.');
        }
    }


    public function delete($id)
    {
        $jobrolemodel = new JobroleModel();

        if ($jobrolemodel->delete($id)) {
            session()->setFlashdata('success', 'Data berhasil dihapus');
        } else {
            session()->setFlashdata('error', 'Data gagal dihapus');
        }

        return redirect()->to('/monitoring/dataJob');
    }
}
