<?php

namespace App\Controllers;

use App\Models\DepartmentModel;
use App\Models\AuthModel;

class DepartmentMaster extends BaseController
{
    protected $departmentModel;
    protected $authModel;

    public function __construct()
    {
        $this->departmentModel = new DepartmentModel();
        $this->authModel = new AuthModel();
    }

    public function index()
    {
        $data = [
            'title' => 'Department',
            'department' => $this->departmentModel->findAll(),
            'account' => $this->authModel->getAccount(session()->get('username')),
        ];

        echo view('layout/table_header', $data);
        echo view('layout/sidebar');
        echo view('layout/topbar');
        echo view('admin/master/department/index', $data);
        echo view('layout/table_footer');
    }

    public function add()
    {
        $data = [
            'title' => 'Tambah Department',
            'account' => $this->authModel->getAccount(session()->get('username')),
            'validation' => \Config\Services::validation(),
        ];

        if ($this->request->getMethod() === 'POST') {
            $rules = [
                'd_id' => [
                    'rules' => 'required|exact_length[3]|alpha',
                    'errors' => [
                        'required' => 'ID Department wajib diisi.',
                        'exact_length' => 'ID Department harus tepat 3 karakter.',
                        'alpha' => 'ID Department hanya boleh berisi huruf.',
                    ],
                ],
                'd_name' => [
                    'rules' => 'required',
                    'errors' => [
                        'required' => 'Nama Department wajib diisi.',
                    ],
                ],
            ];

            if (!$this->validate($rules)) {
                $data['validation'] = $this->validator;
            } else {
                // Ambil data dari form
                $d_id = strtoupper($this->request->getPost('d_id'));
                $d_name = $this->request->getPost('d_name');

                $insertData = [
                    'department_id' => $d_id,
                    'department_name' => $d_name
                ];

                // Cek apakah ID sudah ada di database
                $checkId = $this->departmentModel->where('department_id', $d_id)->countAllResults();

                if ($checkId > 0) {
                    session()->setFlashdata('message', '<div class="alert alert-danger" role="alert">Gagal ditambahkan, ID telah digunakan!</div>');
                } else {
                    if ($this->departmentModel->insert($insertData)) {
                        session()->setFlashdata('message', '<div class="alert alert-danger" role="alert">Gagal menyimpan department baru ke database!</div>');
                    } else {
                        session()->setFlashdata('message', '<div class="alert alert-success" role="alert">Berhasil menambahkan department baru!</div>');
                    }
                }
                return redirect()->to('/admin/master/department');
            }
        }

        echo view('layout/header', $data);
        echo view('layout/sidebar');
        echo view('layout/topbar');
        echo view('admin/master/department/add_dept', $data);
        echo view('layout/footer');
    }

    public function edit($d_id)
    {
        $data = [
            'title' => 'Edit Department',
            'd_old' => $this->departmentModel->find($d_id),
            'account' =>  $this->authModel->getAccount(session()->get('username')),
        ];

        if ($this->request->getMethod() === 'POST') {
            $rules = ['d_name' => 'required'];

            if ($this->validate($rules)) {
                $name = $this->request->getPost('d_name');
                $this->departmentModel->update($d_id, ['department_name' => $name]);
                session()->setFlashdata('message', '<div class="alert alert-success" role="alert">Berhasil mengedit department!</div>');
                return redirect()->to('admin/master/department');
            }
        }

        echo view('layout/header', $data);
        echo view('layout/sidebar');
        echo view('layout/topbar');
        echo view('admin/master/department/edit_dept', $data);
        echo view('layout/footer');
    }

    public function delete($d_id)
    {
        $this->departmentModel->deleteDepartmentWithRelations($d_id);
        session()->setFlashdata('message', '<div class="alert alert-success" role="alert">Berhasil menghapus department!</div>');
        return redirect()->to('admin/master/department');
    }
}
