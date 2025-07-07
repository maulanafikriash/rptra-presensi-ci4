<?php

namespace App\Controllers;

use App\Models\EmployeeModel;
use App\Models\AttendanceModel;
use App\Models\AuthModel;
use App\Models\ShiftModel;
use App\Models\WorkScheduleModel;

class EmployeeMaster extends BaseController
{
    protected $employeeModel;
    protected $attendanceModel;
    protected $authModel;
    protected $shiftModel;
    protected $workScheduleModel;
    protected $db;


    public function __construct()
    {
        $this->employeeModel = new EmployeeModel();
        $this->attendanceModel = new AttendanceModel();
        $this->workScheduleModel = new WorkScheduleModel();
        $this->authModel = new AuthModel();
        $this->shiftModel = new ShiftModel();
        $this->db = \Config\Database::connect();
    }

    public function index()
    {
        $rptraName = session()->get('rptra_name');
        $data = [
            'title' => 'Pegawai',
            'employee' => $this->employeeModel
                ->where('rptra_name', $rptraName)
                ->findAll(),
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
        $rptraName = session()->get('rptra_name');
        $rptraAddress = session()->get('rptra_address');
        $data = [
            'title' => 'Tambah Pegawai',
            'department' => $this->employeeModel->getDepartments(),
            'account' => $this->authModel->getAccount(session()->get('username')),
            'validation' => \Config\Services::validation(),
            'rptra_name' => $rptraName,
            'rptra_address' => $rptraAddress,
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
                'birth_place' => [
                    'rules'  => 'required',
                    'errors' => [
                        'required' => 'Tempat lahir wajib diisi.',
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
                    'rules' => 'permit_empty|uploaded[image]|max_size[image,3072]|is_image[image]|mime_in[image,image/jpg,image/jpeg,image/png]',
                    'errors' => [
                        'max_size' => 'Ukuran file gambar maksimal 3MB.',
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
                $birthPlace  = $this->request->getPost('birth_place');
                $birthDate = $this->request->getPost('birth_date');
                $hireDate = $this->request->getPost('hire_date');
                $departmentId = $this->request->getPost('department_id');
                $maritalStatus = $this->request->getPost('marital_status');
                $numChildren = $this->request->getPost('num_children');
                $education = $this->request->getPost('education');
                $employeeAddress = $this->request->getPost('employee_address');
                $telephone = $this->request->getPost('telephone');
                $rptraName = session()->get('rptra_name');
                $rptraAddress = session()->get('rptra_address');


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
                    'birth_place' => $birthPlace,
                    'birth_date' => $birthDate,
                    'hire_date' => $hireDate,
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
        $employee = $this->employeeModel->findEmployeeWithRelations($id);

        if (!$employee) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException("Pegawai tidak ditemukan");
        }

        $departmentSelected = array_column($this->employeeModel->getDepartments(), 'department_id');
        $selectedDepartment = in_array($employee['department_id'], $departmentSelected) ? $employee['department_id'] : null;

        $data = [
            'title' => 'Edit Data Pegawai', // Judul bisa dibuat lebih deskriptif
            'employee' => $employee,
            'department' => $this->employeeModel->getDepartments(),
            'selectedDepartment' => $selectedDepartment, // Kirim ke view
            'account' => $this->authModel->getAccount(session()->get('username')),
            'validation' => \Config\Services::validation(),
            'rptra_name' => session()->get('rptra_name'),
            'rptra_address' => session()->get('rptra_address'),
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
                'birth_place' => [
                    'rules'  => 'required',
                    'errors' => [
                        'required' => 'Tempat lahir wajib diisi.',
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
                    'rules' => 'permit_empty|uploaded[image]|max_size[image,3072]|is_image[image]|mime_in[image,image/jpg,image/jpeg,image/png]',
                    'errors' => [
                        'max_size' => 'Ukuran file gambar maksimal 3MB.',
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
                    if (in_array($file->getClientExtension(), ['jpg', 'png', 'jpeg']) && $file->getSize() <= 3145728) {
                        $imageName = 'item-' . date('ymd') . '-' . substr(md5(rand()), 0, 10) . '.' . $file->getExtension();
                        $file->move('./img/pp/', $imageName);
                        if ($employee['image'] !== 'default.png') {
                            unlink('./img/pp/' . $employee['image']);
                        }
                    } else {
                        session()->setFlashdata('message', '<div class="alert alert-danger" role="alert">Format file tidak didukung atau ukuran file lebih dari 3MB!</div>');
                        return redirect()->back()->withInput();
                    }
                }

                $currentDepartmentId = $employee['department_id'];
                $newDepartmentId = $this->request->getPost('department_id');

                // Periksa jika department_id berubah
                if ($currentDepartmentId !== $newDepartmentId) {
                    // Format username baru
                    $newUsername = strtoupper($newDepartmentId) . str_pad($employee['employee_id'], 4, '0', STR_PAD_LEFT);

                    // Tentukan user_role_id berdasarkan department_id
                    $newUserRoleId = ($newDepartmentId === 'ADM') ? 1 : 2;

                    $this->authModel->updateUserAccount($employee['employee_id'], [
                        'username' => $newUsername,
                        'user_role_id' => $newUserRoleId,
                    ]);
                }

                $this->employeeModel->update($id, [
                    'employee_name' => ucwords(strtolower($this->request->getPost('employee_name'))),
                    'email' => $this->request->getPost('email'),
                    'gender' => $this->request->getPost('gender'),
                    'birth_place' => $this->request->getPost('birth_place'),
                    'birth_date' => $this->request->getPost('birth_date'),
                    'hire_date' => $this->request->getPost('hire_date'),
                    'department_id' => $this->request->getPost('department_id'),
                    'marital_status' => $this->request->getPost('marital_status'),
                    'num_children' => $this->request->getPost('num_children'),
                    'contraceptive_use' => $this->request->getPost('contraceptive_use'),
                    'education' => $this->request->getPost('education'),
                    'employee_address' => $this->request->getPost('employee_address'),
                    'telephone' => $this->request->getPost('telephone'),
                    'rptra_name'    => session()->get('rptra_name'),
                    'rptra_address' => session()->get('rptra_address'),
                    'image' => $imageName
                ]);

                session()->setFlashdata('message', '<div class="alert alert-success" role="alert">Data pegawai berhasil diperbarui!</div>');
                return redirect()->to('/admin/master/employee/detail/' . $id);
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

        if ($employee['rptra_name'] !== session()->get('rptra_name')) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException("Anda tidak memiliki akses ke data presensi pegawai ini");
        }

        $data = [
            'title' => 'Riwayat Kehadiran Pegawai',
            'employee' => $employee,
            'employeeId' => $employeeId,
            'department_current' => $this->employeeModel->getDepartments($employee['department_id']),
            'month' => $this->request->getGet('month') ?: date('m'),
            'year' => $this->request->getGet('year') ?: date('Y'),
            'account' => $this->authModel->getAccount(session()->get('username')),
            'shifts' => $this->shiftModel->findAll(),
        ];

        if (!$data['department_current']) {
            $data['department_current'] = [
                'department_id' => 'Not assigned',
                'department_name' => 'Department not assigned'
            ];
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

        $summary = [
            'hadir'      => 0,
            'izin_sakit' => 0,
            'alpha'      => 0,
            'libur_cuti' => 0,
        ];

        $daysInMonth = cal_days_in_month(CAL_GREGORIAN, $data['month'], $data['year']);
        $todayTimestamp = strtotime(date('Y-m-d'));

        for ($day = 1; $day <= $daysInMonth; $day++) {
            $currentDateStr = "{$data['year']}-{$data['month']}-{$day}";
            $currentTimestamp = strtotime($currentDateStr);

            if ($currentTimestamp > $todayTimestamp) {
                continue;
            }

            // Cek apakah ada data di hari ini
            if (isset($attendanceData[$day])) {
                $status = $attendanceData[$day]['presence_status'];
                switch ($status) {
                    case 1: // Hadir
                        $summary['hadir']++;
                        break;
                    case 2: // Izin
                    case 3: // Sakit
                        $summary['izin_sakit']++;
                        break;
                    case 4: // Cuti
                    case 5: // Libur
                        $summary['libur_cuti']++;
                        break;
                    case 0: // Tidak Hadir (dianggap Alpha)
                        $summary['alpha']++;
                        break;
                }
            } else {
                $summary['alpha']++;
            }
        }
        $data['summary'] = $summary;

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

        $employee = $this->employeeModel->find($employeeId);
        if (!$employee || $employee['rptra_name'] !== session()->get('rptra_name')) {
            session()->setFlashdata('message', '<div class="alert alert-danger" role="alert">Akses ditolak: pegawai tidak berada di RPTRA Anda.</div>');
            return redirect()->to('admin/master/employee/attendance/' . $employeeId);
        }

        if ($employee) {
            $departmentId = $employee['department_id'];

            $attendanceModel = new AttendanceModel();

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
                    $session->setFlashdata('message', 'Status presensi berhasil diperbarui!');
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
                    'in_status' => 'via admin',
                    'in_time' => date('H:i:s'),
                ]);

                if ($inserted) {
                    $session->setFlashdata('message', 'Status presensi berhasil ditambahkan!');
                } else {
                    $session->setFlashdata('message', '<div class="alert alert-danger" role="alert">Gagal menambahkan status presensi!</div>');
                }
            }
        } else {
            $session->setFlashdata('message', '<div class="alert alert-danger" role="alert">Pegawai tidak ditemukan!</div>');
        }

        return redirect()->to('admin/master/employee/attendance/' . $employeeId);
    }

    public function workScheduleEmployee($employeeId)
    {
        $employee = $this->employeeModel->find($employeeId);
        if (!$employee) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException("Pegawai dengan ID: $employeeId tidak ditemukan.");
        }

        if ($employee['rptra_name'] !== session()->get('rptra_name')) {
            return redirect()->to('admin/dashboard')->with('error', 'Anda tidak memiliki akses ke jadwal pegawai ini.');
        }

        $month = (int)($this->request->getGet('month') ?? date('m'));
        $year  = $this->request->getGet('year') ?? date('Y');

        $allShifts = $this->shiftModel->findAll();
        $shiftsById = array_column($allShifts, null, 'shift_id');

        $workSchedules = $this->workScheduleModel->getWorkSchedulesByEmployeeAndMonth($employeeId, $month, $year);

        $workScheduleData = [];
        foreach ($workSchedules as $ws) {
            $day = (int)date('j', strtotime($ws['schedule_date']));
            $ws['shift_info'] = $shiftsById[$ws['shift_id']] ?? null;
            $workScheduleData[$day] = $ws;
        }

        $data = [
            'title'              => 'Jadwal Kerja Pegawai',
            'employee'           => $employee,
            'department_current' => $this->employeeModel->getDepartments($employee['department_id']) ?? ['department_name' => 'Tidak Ditemukan'],
            'month'              => $month,
            'year'               => $year,
            'account'            => $this->authModel->getAccount(session()->get('username')),
            'shifts'             => $allShifts,
            'workSchedules'      => $workScheduleData,
        ];

        echo view('layout/attendance_header', $data);
        echo view('layout/sidebar');
        echo view('layout/topbar');
        echo view('admin/master/employee/work_schedule', $data);
        echo view('layout/schedule_footer');
    }

    public function storeWorkSchedule()
    {
        $username       = session()->get('username');
        $employee_id    = $this->request->getPost('employee_id');
        $schedule_date  = $this->request->getPost('schedule_date');
        $status_input   = $this->request->getPost('schedule_status');
        $shift_id_input = $this->request->getPost('shift_id');

        $schedule_status = null;
        $shift_id        = null;

        if ($status_input === 'shift') {
            $schedule_status = null;
            $shift_id        = !empty($shift_id_input) ? (int)$shift_id_input : null;
        } elseif (in_array($status_input, ['4', '5'])) {
            $schedule_status = (int)$status_input; // Status 4 (Cuti) atau 5 (Libur)
            $shift_id        = null;
        }

        if (is_null($schedule_status) && is_null($shift_id)) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Shift harus dipilih untuk jadwal shift kerja.']);
        }
        if (empty($status_input)) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Status Jadwal harus dipilih.']);
        }

        $emp = $this->employeeModel->find($employee_id);
        if (!$emp || $emp['rptra_name'] !== session()->get('rptra_name')) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Akses ditolak: pegawai tidak berada di RPTRA Anda.']);
        }

        $existingSchedule = $this->workScheduleModel->where('employee_id', $employee_id)
            ->where('schedule_date', $schedule_date)
            ->first();

        if ($existingSchedule) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Jadwal sudah ada untuk tanggal tersebut.']);
        }

        $department_id = $emp['department_id'];

        $this->db->transStart();

        $schedule_id_new = $this->workScheduleModel->insert([
            'employee_id'     => $employee_id,
            'department_id'   => $department_id,
            'schedule_date'   => $schedule_date,
            'schedule_status' => $schedule_status,
            'shift_id'        => $shift_id,
        ]);

        if ($schedule_id_new) {
            if (in_array($schedule_status, [4, 5])) {
                $this->attendanceModel->insert([
                    'username'        => $username,
                    'employee_id'     => $employee_id,
                    'department_id'   => $department_id,
                    'schedule_id'     => $schedule_id_new,
                    'attendance_date' => $schedule_date,
                    'presence_status' => $schedule_status,
                ]);
            }
        }

        $this->db->transComplete();

        if ($this->db->transStatus() === FALSE) {
            log_message('error', 'Transaksi gagal saat menambahkan jadwal kerja. Employee ID: ' . $employee_id);
            return $this->response->setJSON(['status' => 'error', 'message' => 'Gagal menambahkan jadwal kerja karena kesalahan server.']);
        }

        session()->setFlashdata('message', 'Jadwal kerja berhasil ditambahkan.');
        return $this->response->setJSON(['status' => 'success', 'message' => 'Jadwal kerja berhasil ditambahkan.']);
    }

    public function updateWorkSchedule($scheduleId)
    {
        $schedule = $this->workScheduleModel->find($scheduleId);
        if (!$schedule) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Jadwal kerja tidak ditemukan.']);
        }

        $emp = $this->employeeModel->find($schedule['employee_id']);
        if (!$emp || $emp['rptra_name'] !== session()->get('rptra_name')) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Akses ditolak: pegawai tidak berada di RPTRA Anda.']);
        }

        $username       = session()->get('username');
        $status_input   = $this->request->getPost('schedule_status');
        $shift_id_input = $this->request->getPost('shift_id');

        $schedule_status = null;
        $shift_id        = null;

        if ($status_input === 'shift') {
            $schedule_status = null;
            $shift_id        = !empty($shift_id_input) ? (int)$shift_id_input : null;
        } elseif (in_array($status_input, ['4', '5'])) {
            $schedule_status = (int)$status_input;
            $shift_id        = null;
        }

        if (is_null($schedule_status) && is_null($shift_id)) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Shift harus dipilih untuk jadwal shift kerja.']);
        }
        if (empty($status_input)) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Status Jadwal harus dipilih.']);
        }
        $this->db->transStart();

        $this->workScheduleModel->update($scheduleId, [
            'schedule_status' => $schedule_status,
            'shift_id'        => $shift_id,
        ]);

        // Hapus data absensi Cuti/Libur lama jika ada
        $this->attendanceModel->where('schedule_id', $scheduleId)
            ->whereIn('presence_status', [4, 5])
            ->delete();

        // Jika status baru adalah Cuti/Libur, masukkan data absensi baru
        if (in_array($schedule_status, [4, 5])) {
            $this->attendanceModel->insert([
                'username'        => $username,
                'employee_id'     => $schedule['employee_id'],
                'department_id'   => $schedule['department_id'],
                'schedule_id'     => $scheduleId,
                'attendance_date' => $schedule['schedule_date'],
                'presence_status' => $schedule_status,
            ]);
        }

        $this->db->transComplete();

        if ($this->db->transStatus() === FALSE) {
            log_message('error', 'Transaksi gagal saat mengupdate jadwal kerja. Schedule ID: ' . $scheduleId);
            return $this->response->setJSON(['status' => 'error', 'message' => 'Gagal memperbarui jadwal kerja karena kesalahan server.']);
        }

        session()->setFlashdata('message', 'Jadwal kerja berhasil diperbarui.');
        return $this->response->setJSON(['status' => 'success', 'message' => 'Jadwal kerja berhasil diperbarui.']);
    }

    public function delete($id)
    {
        $this->employeeModel->deleteEmployeeWithRelations($id);
        session()->setFlashdata('message', '<div class="alert alert-success" role="alert">Berhasil menghapus data pegawai!</div>');
        return redirect()->to('admin/master/employee');
    }
}
