<?php

namespace App\Controllers;

use App\Models\ShiftModel;
use App\Models\EmployeeModel;
use App\Models\AuthModel;

class ShiftMaster extends BaseController
{
    protected $shiftModel;
    protected $authModel;
    protected $employeeModel;

    public function __construct()
    {
        $this->shiftModel = new ShiftModel();
        $this->authModel = new AuthModel();
        $this->employeeModel = new EmployeeModel();
    }

    public function index()
    {
        $data = [
            'title' => 'Shift',
            'shift' => $this->shiftModel->findAll(),
            'account' => $this->authModel->getAccount(session()->get('username'))
        ];

        echo view('layout/table_header', $data);
        echo view('layout/sidebar');
        echo view('layout/topbar');
        echo view('admin/master/shift/index', $data);
        echo view('layout/table_footer');
    }

    public function add()
    {
        $data = [
            'title' => 'Tambah Shift',
            's_id' => $this->shiftModel->countAll() + 1,
            'account' => $this->authModel->getAccount(session()->get('username')),
            'validation' => \Config\Services::validation(),
        ];

        if ($this->request->getMethod() === 'POST') {
            $rules = [
                's_start_h' => [
                    'rules' => 'required|numeric',
                    'errors' => [
                        'required' => 'Jam mulai wajib diisi.',
                        'numeric' => 'Jam mulai harus berupa angka.',
                    ],
                ],
                's_start_m' => [
                    'rules' => 'required|numeric',
                    'errors' => [
                        'required' => 'Menit mulai wajib diisi.',
                        'numeric' => 'Menit mulai harus berupa angka.',
                    ],
                ],
                's_start_s' => [
                    'rules' => 'required|numeric',
                    'errors' => [
                        'required' => 'Detik mulai wajib diisi.',
                        'numeric' => 'Detik mulai harus berupa angka.',
                    ],
                ],
                's_end_h' => [
                    'rules' => 'required|numeric',
                    'errors' => [
                        'required' => 'Jam selesai wajib diisi.',
                        'numeric' => 'Jam selesai harus berupa angka.',
                    ],
                ],
                's_end_m' => [
                    'rules' => 'required|numeric',
                    'errors' => [
                        'required' => 'Menit selesai wajib diisi.',
                        'numeric' => 'Menit selesai harus berupa angka.',
                    ],
                ],
                's_end_s' => [
                    'rules' => 'required|numeric',
                    'errors' => [
                        'required' => 'Detik selesai wajib diisi.',
                        'numeric' => 'Detik selesai harus berupa angka.',
                    ],
                ],
            ];

            if (!$this->validate($rules)) {
                $data['validation'] = $this->validator;
            } else {
                // Ambil waktu mulai dan selesai dari form
                $startTime = $this->request->getPost('s_start_h') . ':' . $this->request->getPost('s_start_m') . ':' . $this->request->getPost('s_start_s');
                $endTime = $this->request->getPost('s_end_h') . ':' . $this->request->getPost('s_end_m') . ':' . $this->request->getPost('s_end_s');

                // Validasi waktu mulai dan selesai
                if (strtotime($startTime) >= strtotime($endTime)) {
                    return redirect()->back()->with('message', '<div class="alert alert-danger" role="alert">Waktu mulai tidak boleh lebih dari atau sama dengan waktu selesai.</div>');
                }

                $shiftData = [
                    'start_time' => $startTime,
                    'end_time' => $endTime
                ];

                if ($this->shiftModel->insert($shiftData)) {
                    session()->setFlashdata('message', '<div class="alert alert-success" role="alert">Shift baru berhasil ditambahkan!</div>');
                } else {
                    session()->setFlashdata('message', '<div class="alert alert-danger" role="alert">Gagal menyimpan shift baru ke database!</div>');
                }

                return redirect()->to('admin/master/shift');
            }
        }

        echo view('layout/header', $data);
        echo view('layout/sidebar');
        echo view('layout/topbar');
        echo view('admin/master/shift/add_shift', $data);
        echo view('layout/footer');
    }


    public function edit($s_id)
    {
        $shift = $this->shiftModel->find($s_id);
        if (!$shift) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('Shift tidak ditemukan');
        }

        $start = explode(':', $shift['start_time']);
        $end = explode(':', $shift['end_time']);

        $data = [
            'title' => 'Edit Shift',
            'shift' => $shift,
            's_sh' => $start[0],
            's_sm' => $start[1],
            's_ss' => $start[2],
            's_eh' => $end[0],
            's_em' => $end[1],
            's_es' => $end[2],
            'account' => $this->authModel->getAccount(session()->get('username')),
            'validation' => \Config\Services::validation(),
            's_id' => $shift['shift_id']
        ];

        if ($this->request->getMethod() === 'POST') {
            $rules = [
                's_start_h' => [
                    'rules' => 'required|numeric',
                    'errors' => [
                        'required' => 'Jam mulai wajib diisi.',
                        'numeric' => 'Jam mulai harus berupa angka.',
                    ],
                ],
                's_start_m' => [
                    'rules' => 'required|numeric',
                    'errors' => [
                        'required' => 'Menit mulai wajib diisi.',
                        'numeric' => 'Menit mulai harus berupa angka.',
                    ],
                ],
                's_start_s' => [
                    'rules' => 'required|numeric',
                    'errors' => [
                        'required' => 'Detik mulai wajib diisi.',
                        'numeric' => 'Detik mulai harus berupa angka.',
                    ],
                ],
                's_end_h' => [
                    'rules' => 'required|numeric',
                    'errors' => [
                        'required' => 'Jam selesai wajib diisi.',
                        'numeric' => 'Jam selesai harus berupa angka.',
                    ],
                ],
                's_end_m' => [
                    'rules' => 'required|numeric',
                    'errors' => [
                        'required' => 'Menit selesai wajib diisi.',
                        'numeric' => 'Menit selesai harus berupa angka.',
                    ],
                ],
                's_end_s' => [
                    'rules' => 'required|numeric',
                    'errors' => [
                        'required' => 'Detik selesai wajib diisi.',
                        'numeric' => 'Detik selesai harus berupa angka.',
                    ],
                ],
            ];

            if (!$this->validate($rules)) {
                $data['validation'] = $this->validator;
            } else {

                // Ambil waktu mulai dan selesai dari form
                $startTime = $this->request->getPost('s_start_h') . ':' . $this->request->getPost('s_start_m') . ':' . $this->request->getPost('s_start_s');
                $endTime = $this->request->getPost('s_end_h') . ':' . $this->request->getPost('s_end_m') . ':' . $this->request->getPost('s_end_s');

                // Validasi waktu mulai dan selesai
                if (strtotime($startTime) >= strtotime($endTime)) {
                    session()->setFlashdata('message', '<div class="alert alert-danger" role="alert">Waktu mulai tidak boleh lebih dari atau sama dengan waktu selesai.</div>');
                    return redirect()->back()->withInput();
                }

                $this->shiftModel->update($s_id, [
                    'start_time' => $startTime,
                    'end_time' => $endTime
                ]);

                session()->setFlashdata('message', '<div class="alert alert-success" role="alert">Shift berhasil diperbarui!</div>');
                return redirect()->to('admin/master/shift');
            }
        }

        echo view('layout/header', $data);
        echo view('layout/sidebar');
        echo view('layout/topbar');
        echo view('admin/master/shift/edit_shift', $data);
        echo view('layout/footer');
    }


    public function delete($s_id)
    {
        $this->shiftModel->deleteShift($s_id);

        session()->setFlashdata('message', '<div class="alert alert-success" role="alert">Berhasil Menghapus Shift!</div>');
        return redirect()->to('admin/master/shift');
    }
}
