<?php

namespace App\Controllers;

use App\Models\EmployeeModel;
use App\Models\AttendanceModel;
use App\Models\AuthModel;
use CodeIgniter\Controller;

class EmployeeMaster extends BaseController
{
    protected $employeeModel;
    protected $attendanceModel;
    protected $authModel;

    public function __construct()
    {
        $this->employeeModel = new EmployeeModel();
        $this->attendanceModel = new AttendanceModel();
        $this->authModel = new AuthModel();
    }

    public function index()
    {
        $data = [
            'title' => 'Pegawai',
            'employee' => $this->employeeModel->findAll(),
            'account' => $this->authModel->getAccount(session()->get('username')),
        ];

        echo view('layout/table_header', $data);
        echo view('layout/sidebar');
        echo view('layout/topbar');
        echo view('admin/master/employee/index', $data);
        echo view('layout/table_footer');
    }

    public function add()
    {
        $data = [
            'title' => 'Tambah Pegawai',
            'department' => $this->employeeModel->getDepartments(),
            'shift' => $this->employeeModel->getShifts(),
            'account' => $this->authModel->getAccount(session()->get('username')),
            'validation' => \Config\Services::validation(),
        ];

        if ($this->request->getMethod() === 'POST') {
            $rules = [
                'employee_name' => [
                    'rules' => 'required',
                    'errors' => [
                        'required' => 'Nama pegawai wajib diisi.',
                    ],
                ],
                'email' => [
                    'rules' => 'required|valid_email',
                    'errors' => [
                        'required' => 'Email wajib diisi.',
                        'valid_email' => 'Masukkan email yang valid.',
                    ],
                ],
                'gender' => [
                    'rules' => 'required',
                    'errors' => [
                        'required' => 'Jenis kelamin wajib diisi.',
                    ],
                ],
                'birth_date' => [
                    'rules' => 'required',
                    'errors' => [
                        'required' => 'Tanggal lahir wajib diisi.',
                    ],
                ],
                'hire_date' => [
                    'rules' => 'required',
                    'errors' => [
                        'required' => 'Tanggal masuk kerja wajib diisi.',
                    ],
                ],
                'shift_id' => [
                    'rules' => 'required',
                    'errors' => [
                        'required' => 'Shift wajib dipilih.',
                    ],
                ],
                'department_id' => [
                    'rules' => 'required',
                    'errors' => [
                        'required' => 'Department wajib dipilih.',
                    ],
                ],
                'marital_status' => [
                    'rules' => 'required',
                    'errors' => [
                        'required' => 'Status perkawinan wajib diisi.',
                    ],
                ],
                'num_children' => [
                    'rules' => 'required|integer',
                    'errors' => [
                        'required' => 'Jumlah anak wajib diisi.',
                        'integer' => 'Jumlah anak harus berupa angka.',
                    ],
                ],
                'education' => [
                    'rules' => 'required',
                    'errors' => [
                        'required' => 'Pendidikan wajib diisi.',
                    ],
                ],
                'employee_address' => [
                    'rules' => 'required',
                    'errors' => [
                        'required' => 'Alamat pegawai wajib diisi.',
                    ],
                ],
                'telephone' => [
                    'rules' => 'required|numeric',
                    'errors' => [
                        'required' => 'Nomor telepon wajib diisi.',
                        'numeric' => 'Nomor telepon harus berupa angka.',
                    ],
                ],
                'rptra_name' => [
                    'rules' => 'required',
                    'errors' => [
                        'required' => 'Nama RPTRA wajib diisi.',
                    ],
                ],
                'rptra_address' => [
                    'rules' => 'required',
                    'errors' => [
                        'required' => 'Alamat RPTRA wajib diisi.',
                    ],
                ],
                'image' => [
                    'rules' => 'permit_empty|uploaded[image]|max_size[image,5240]|is_image[image]|mime_in[image,image/jpg,image/jpeg,image/png]',
                    'errors' => [
                        'max_size' => 'Ukuran file gambar maksimal 5MB.',
                        'is_image' => 'File harus berupa gambar.',
                        'mime_in' => 'Format file harus JPG, JPEG, atau PNG.',
                    ],
                ],
            ];

            if (!$this->validate($rules)) {
                $data['validation'] = $this->validator;
            } else {
                // Ambil data dari form
                $employeeName = ucwords(strtolower($this->request->getPost('employee_name')));
                $email = $this->request->getPost('email');
                $gender = $this->request->getPost('gender');
                $birthDate = $this->request->getPost('birth_date');
                $hireDate = $this->request->getPost('hire_date');
                $shiftId = $this->request->getPost('shift_id');
                $departmentId = $this->request->getPost('department_id');
                $maritalStatus = $this->request->getPost('marital_status');
                $numChildren = $this->request->getPost('num_children');
                $education = $this->request->getPost('education');
                $employeeAddress = $this->request->getPost('employee_address');
                $telephone = $this->request->getPost('telephone');
                $rptraName = $this->request->getPost('rptra_name');
                $rptraAddress = $this->request->getPost('rptra_address');

                $file = $this->request->getFile('image');
                $imageName = 'default.png';

                if ($file && $file->isValid() && !$file->hasMoved()) {
                    $imageName = 'item-' . date('ymd') . '-' . substr(md5(rand()), 0, 10) . '.' . $file->getExtension();
                    $file->move('./img/pp/', $imageName);
                }

                $employeeData = [
                    'employee_name' => $employeeName,
                    'email' => $email,
                    'gender' => $gender,
                    'birth_date' => $birthDate,
                    'hire_date' => $hireDate,
                    'shift_id' => $shiftId,
                    'department_id' => $departmentId,
                    'marital_status' => $maritalStatus,
                    'num_children' => $numChildren,
                    'education' => $education,
                    'employee_address' => $employeeAddress,
                    'telephone' => $telephone,
                    'rptra_name' => $rptraName,
                    'rptra_address' => $rptraAddress,
                    'image' => $imageName
                ];

                if ($this->employeeModel->insert($employeeData)) {
                    session()->setFlashdata('message', '<div class="alert alert-success" role="alert">Pegawai baru berhasil ditambahkan!</div>');
                } else {
                    session()->setFlashdata('message', '<div class="alert alert-danger" role="alert">Gagal menyimpan pegawai baru ke database!</div>');
                }

                return redirect()->to('admin/master/employee');
            }
        }

        echo view('layout/header', $data);
        echo view('layout/sidebar');
        echo view('layout/topbar');
        echo view('admin/master/employee/add_employee', $data);
        echo view('layout/footer');
    }

    public function edit($id)
    {
        $employee = $this->employeeModel->find($id);
        if (!$employee) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException("Pegawai tidak ditemukan");
        }

        $data = [
            'title' => 'Edit Pegawai',
            'employee' => $employee,
            'department' => $this->employeeModel->getDepartments(),
            'shift' => $this->employeeModel->getShifts(),
            'account' => $this->authModel->getAccount(session()->get('username')),
            'validation' => \Config\Services::validation()
        ];

        if ($this->request->getMethod() === 'POST') {
            $rules = [
                'employee_name' => [
                    'rules' => 'required',
                    'errors' => [
                        'required' => 'Nama pegawai wajib diisi.',
                    ],
                ],
                'email' => [
                    'rules' => 'required|valid_email',
                    'errors' => [
                        'required' => 'Email wajib diisi.',
                        'valid_email' => 'Masukkan email yang valid.',
                    ],
                ],
                'gender' => [
                    'rules' => 'required',
                    'errors' => [
                        'required' => 'Jenis kelamin wajib diisi.',
                    ],
                ],
                'birth_date' => [
                    'rules' => 'required',
                    'errors' => [
                        'required' => 'Tanggal lahir wajib diisi.',
                    ],
                ],
                'hire_date' => [
                    'rules' => 'required',
                    'errors' => [
                        'required' => 'Tanggal masuk kerja wajib diisi.',
                    ],
                ],
                'shift_id' => [
                    'rules' => 'required',
                    'errors' => [
                        'required' => 'Shift wajib dipilih.',
                    ],
                ],
                'department_id' => [
                    'rules' => 'required',
                    'errors' => [
                        'required' => 'Department wajib dipilih.',
                    ],
                ],
                'marital_status' => [
                    'rules' => 'required',
                    'errors' => [
                        'required' => 'Status perkawinan wajib diisi.',
                    ],
                ],
                'num_children' => [
                    'rules' => 'required|integer',
                    'errors' => [
                        'required' => 'Jumlah anak wajib diisi.',
                        'integer' => 'Jumlah anak harus berupa angka.',
                    ],
                ],
                'education' => [
                    'rules' => 'required',
                    'errors' => [
                        'required' => 'Pendidikan wajib diisi.',
                    ],
                ],
                'employee_address' => [
                    'rules' => 'required',
                    'errors' => [
                        'required' => 'Alamat pegawai wajib diisi.',
                    ],
                ],
                'telephone' => [
                    'rules' => 'required|numeric',
                    'errors' => [
                        'required' => 'Nomor telepon wajib diisi.',
                        'numeric' => 'Nomor telepon harus berupa angka.',
                    ],
                ],
                'rptra_name' => [
                    'rules' => 'required',
                    'errors' => [
                        'required' => 'Nama RPTRA wajib diisi.',
                    ],
                ],
                'rptra_address' => [
                    'rules' => 'required',
                    'errors' => [
                        'required' => 'Alamat RPTRA wajib diisi.',
                    ],
                ],
                'image' => [
                    'rules' => 'permit_empty|uploaded[image]|max_size[image,5240]|is_image[image]|mime_in[image,image/jpg,image/jpeg,image/png]',
                    'errors' => [
                        'max_size' => 'Ukuran file gambar maksimal 5MB.',
                        'is_image' => 'File harus berupa gambar.',
                        'mime_in' => 'Format file harus JPG, JPEG, atau PNG.',
                    ],
                ],
            ];

            if (!$this->validate($rules)) {
                $data['validation'] = $this->validator;
            } else {

                $file = $this->request->getFile('image');
                $imageName = $employee['image'];

                if ($file->isValid() && !$file->hasMoved()) {
                    if (in_array($file->getClientExtension(), ['jpg', 'png', 'jpeg']) && $file->getSize() <= 5242880) {
                        $imageName = 'item-' . date('ymd') . '-' . substr(md5(rand()), 0, 10) . '.' . $file->getExtension();
                        $file->move('./img/pp/', $imageName);
                        if ($employee['image'] !== 'default.png') {
                            unlink('./img/pp/' . $employee['image']);
                        }
                    } else {
                        session()->setFlashdata('message', '<div class="alert alert-danger" role="alert">Format file tidak didukung atau ukuran file lebih dari 5MB!</div>');
                        return redirect()->back()->withInput();
                    }
                }

                // Ambil data department_id sebelumnya
                $currentDepartmentId = $employee['department_id'];
                $newDepartmentId = $this->request->getPost('department_id');

                // Periksa jika department_id berubah
                if ($currentDepartmentId !== $newDepartmentId) {
                    // Format username baru
                    $newUsername = strtoupper($newDepartmentId) . str_pad($employee['employee_id'], 4, '0', STR_PAD_LEFT);

                    // Tentukan user_role_id berdasarkan department_id
                    $newUserRoleId = ($newDepartmentId === 'ADM') ? 1 : 2;

                    // Update username dan user_role_id di tabel user_accounts
                    $this->authModel->updateUserAccount($employee['employee_id'], [
                        'username' => $newUsername,
                        'user_role_id' => $newUserRoleId,
                    ]);
                }

                $this->employeeModel->update($id, [
                    'employee_name' => ucwords(strtolower($this->request->getPost('employee_name'))),
                    'email' => $this->request->getPost('email'),
                    'gender' => $this->request->getPost('gender'),
                    'birth_date' => $this->request->getPost('birth_date'),
                    'hire_date' => $this->request->getPost('hire_date'),
                    'shift_id' => $this->request->getPost('shift_id'),
                    'department_id' => $this->request->getPost('department_id'),
                    'marital_status' => $this->request->getPost('marital_status'),
                    'num_children' => $this->request->getPost('num_children'),
                    'contraceptive_use' => $this->request->getPost('contraceptive_use'),
                    'education' => $this->request->getPost('education'),
                    'employee_address' => $this->request->getPost('employee_address'),
                    'telephone' => $this->request->getPost('telephone'),
                    'rptra_name' => $this->request->getPost('rptra_name'),
                    'rptra_address' => $this->request->getPost('rptra_address'),
                    'image' => $imageName
                ]);

                session()->setFlashdata('message', '<div class="alert alert-success" role="alert">Data pegawai berhasil diperbarui!</div>');
                return redirect()->to('/admin/master/employee');
            }
        }

        echo view('layout/header', $data);
        echo view('layout/sidebar');
        echo view('layout/topbar');
        echo view('admin/master/employee/edit_employee', $data);
        echo view('layout/footer');
    }


    public function detail($id)
    {
        $employee = $this->employeeModel->findEmployeeWithRelations($id);
        if (!$employee) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException("Pegawai tidak ditemukan");
        }

        $data = [
            'title' => 'Detail Pegawai',
            'employee' => $employee,
            'employeeId' => $id,
            'department_current' => [
                'department_id' => $employee['department_id'] ?? null,
                'department_name' => $employee['department_name'] ?? 'Tidak Diketahui'
            ],
            'shift_current' => [
                'shift_id' => $employee['shift_id'] ?? null,
                'start' => $employee['start_time'] ?? null,
                'end' => $employee['end_time'] ?? null
            ],
            'shift' => $this->employeeModel->getShifts(),
            'account' => $this->authModel->getAccount(session()->get('username')),
        ];

        echo view('layout/header', $data);
        echo view('layout/sidebar');
        echo view('layout/topbar');
        echo view('admin/master/employee/detail_employee', $data);
        echo view('layout/footer');
    }

    public function attendanceEmployee($employeeId)
    {
        $employee = $this->employeeModel->find($employeeId);
        if (!$employee) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException("Pegawai tidak ditemukan");
        }

        $data = [
            'title' => 'Riwayat Kehadiran Pegawai',
            'employee' => $employee,
            'employeeId' => $employeeId,
            'department_current' => $this->employeeModel->getDepartments($employee['department_id']),
            'shift_current' => $this->employeeModel->getShifts($employee['shift_id']),
            'month' => $this->request->getGet('month') ?: date('m'),
            'year' => $this->request->getGet('year') ?: date('Y'),
            'account' => $this->authModel->getAccount(session()->get('username')),
        ];

        if (!$data['department_current']) {
            $data['department_current'] = [
                'department_id' => 'Not assigned',
                'department_name' => 'Department not assigned'
            ];
        }

        if (!$data['shift_current']) {
            $data['shift_current'] = ['shift_id' => 'Shift not assigned'];
        }

        $attendance = $this->attendanceModel->getAttendanceByEmployeeAndDate(
            $employeeId,
            $data['month'],
            $data['year']
        );

        $attendanceData = [];
        foreach ($attendance as $att) {
            if (isset($att['date'])) {
                $day = (int) date('j', strtotime($att['date']));
                $attendanceData[$day] = [
                    'date' => $att['date'],
                    'presence_status' => $att['presence_status'],
                    'check_in_latitude' => $att['check_in_latitude'],
                    'check_in_longitude' => $att['check_in_longitude'],
                    'check_out_latitude' => $att['check_out_latitude'], 
                    'check_out_longitude' => $att['check_out_longitude'], 
                    'end_time' => $att['end_time']
                ];
            }
        }
        $data['attendance'] = $attendanceData;

        echo view('layout/attendance_header', $data);
        echo view('layout/sidebar');
        echo view('layout/topbar');
        echo view('admin/master/employee/attendance_employee', $data);
        echo view('layout/attendance_footer', $data);
    }

    public function updateAttendanceEmployee()
    {
        $request = \Config\Services::request();
        $session = session();

        // Ambil data dari form
        $employeeId = $request->getPost('employee_id');
        $date = $request->getPost('date');
        $presenceStatus = $request->getPost('presence_status');

        // Ambil data department_id dan shift_id dari tabel employee
        $employeeModel = new EmployeeModel();
        $employee = $employeeModel->find($employeeId);

        if ($employee) {
            $departmentId = $employee['department_id'];
            $shiftId = $employee['shift_id'];

            $attendanceModel = new AttendanceModel();

            // Cek apakah data presensi untuk `employee_id` dan `date` sudah ada
            $existingAttendance = $attendanceModel->where([
                'employee_id' => $employeeId,
                'attendance_date' => $date,
            ])->first();

            if ($existingAttendance) {
                $updated = $attendanceModel->updateAttendanceByEmployeeAndDate($employeeId, $date, [
                    'presence_status' => $presenceStatus,
                    'in_time' => date('H:i:s'),
                ]);

                if ($updated) {
                    $session->setFlashdata('message', '<div class="alert alert-success" role="alert">Status presensi berhasil diperbarui!</div>');
                } else {
                    $session->setFlashdata('message', '<div class="alert alert-danger" role="alert">Gagal memperbarui status presensi!</div>');
                }
            } else {
                // Jika data tidak ada, tambahkan data presensi baru
                $inserted = $attendanceModel->insert([
                    'employee_id' => $employeeId,
                    'attendance_date' => $date,
                    'presence_status' => $presenceStatus,
                    'username' => $session->get('username'),
                    'department_id' => $departmentId,
                    'shift_id' => $shiftId,
                    'in_status' => 'via admin',
                    'in_time' => date('H:i:s'),
                ]);

                if ($inserted) {
                    $session->setFlashdata('message', '<div class="alert alert-success" role="alert">Status presensi berhasil ditambahkan!</div>');
                } else {
                    $session->setFlashdata('message', '<div class="alert alert-danger" role="alert">Gagal menambahkan status presensi!</div>');
                }
            }
        } else {
            $session->setFlashdata('message', '<div class="alert alert-danger" role="alert">Pegawai tidak ditemukan!</div>');
        }

        return redirect()->to('admin/master/employee/attendance/' . $employeeId);
    }

    public function delete($id)
    {
        $this->employeeModel->deleteEmployeeWithRelations($id);
        session()->setFlashdata('message', '<div class="alert alert-success" role="alert">Berhasil menghapus data pegawai!</div>');
        return redirect()->to('admin/master/employee');
    }
}
