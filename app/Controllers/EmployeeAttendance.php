<?php

namespace App\Controllers;

use App\Models\AuthModel;
use App\Models\AttendanceModel;
use App\Models\WorkScheduleModel;
use App\Models\ShiftModel;
use CodeIgniter\HTTP\ResponseInterface;

class EmployeeAttendance extends BaseController
{
    protected $authModel;
    protected $attendanceModel;
    protected $workScheduleModel;
    protected $shiftModel;

    public function __construct()
    {
        $this->authModel = new AuthModel();
        $this->attendanceModel = new AttendanceModel();
        $this->workScheduleModel = new WorkScheduleModel();
        $this->shiftModel = new ShiftModel();
    }

    public function index()
    {
        $data['title'] = 'Form Presensi';
        $data['account'] = $this->authModel->getAccount(session()->get('username'));

        date_default_timezone_set('Asia/Jakarta');
        $currentTime = time();

        $employeeId = $data['account']['employee_id'];
        $today = date('Y-m-d');

        // Mengambil jadwal kerja hari ini dari tabel schedule (shift only)
        $schedule = $this->workScheduleModel->where('employee_id', $employeeId)
            ->where('schedule_date', $today)
            ->where('shift_id !=', null) // Hanya jadwal berupa shift
            ->first();

        if ($schedule) {
            $data['has_shift'] = true;
            $data['schedule_shift'] = $schedule;

            // Ambil detail shift berdasarkan shift_id dari schedule
            $shiftDetails = $this->shiftModel->find($schedule['shift_id']);
            $data['shift_details'] = $shiftDetails;

            // Format waktu shift ke 'H:i'
            $data['shift_start_time'] = date('H:i', strtotime($shiftDetails['start_time']));
            $data['shift_end_time'] = date('H:i', strtotime($shiftDetails['end_time']));

            // Menentukan status shift berdasarkan waktu shift
            $shiftStart = strtotime($shiftDetails['start_time']);
            $shiftEnd = strtotime($shiftDetails['end_time']);

            // Jika shift berakhir di hari berikutnya (misal shift malam)
            if ($shiftEnd < $shiftStart) {
                $shiftEnd = strtotime($shiftDetails['end_time'] . ' +1 day');
            }

            // Menentukan status shift
            if ($currentTime < $shiftStart) {
                $data['shift_status'] = 'belum mulai';
            } elseif ($currentTime >= $shiftStart && $currentTime <= $shiftEnd) {
                $data['shift_status'] = 'presensi masuk';
            } else {
                $data['shift_status'] = 'sudah selesai';
            }
        } else {
            $data['has_shift'] = false;
            $data['schedule_shift'] = null;
            $data['shift_details'] = null;
            $data['shift_status'] = 'tidak ada jadwal';
            $data['shift_start_time'] = null;
            $data['shift_end_time'] = null;
        }

        // Cek presensi hari ini
        $attendance = $this->attendanceModel->where('employee_id', $employeeId)
            ->where('attendance_date', $today)
            ->first();

        $data['already_checked_in'] = !empty($attendance) && !empty($attendance['in_time']);
        $data['presence_status'] = $attendance['presence_status'] ?? null;
        $data['already_checked_out'] = !empty($attendance['out_time']) && $attendance['out_time'] !== '-';

        // Menentukan status presensi
        if ($data['already_checked_in']) {
            if ($data['already_checked_out']) {
                $data['presence_status_label'] = 'Sudah Keluar';
                $data['presence_status_class'] = 'btn-secondary';
                $data['presence_icon'] = 'fa-check-circle';
                $data['presence_text_class'] = 'text-secondary';
            } else {
                switch ($data['presence_status']) {
                    case 1:
                        $data['presence_status_label'] = 'Hadir';
                        $data['presence_status_class'] = 'btn-success';
                        $data['presence_icon'] = 'fa-check';
                        $data['presence_text_class'] = 'text-success';
                        break;
                    case 0:
                        $data['presence_status_label'] = 'Tidak Hadir';
                        $data['presence_status_class'] = 'btn-danger';
                        $data['presence_icon'] = 'fa-times';
                        $data['presence_text_class'] = 'text-danger';
                        break;
                    case 2:
                        $data['presence_status_label'] = 'Izin';
                        $data['presence_status_class'] = 'btn-warning';
                        $data['presence_icon'] = 'fa-calendar-day';
                        $data['presence_text_class'] = 'text-warning';
                        break;
                    case 3:
                        $data['presence_status_label'] = 'Sakit';
                        $data['presence_status_class'] = 'btn-warning';
                        $data['presence_icon'] = 'fa-medkit';
                        $data['presence_text_class'] = 'text-warning';
                        break;
                    case 4:
                        $data['presence_status_label'] = 'Cuti';
                        $data['presence_status_class'] = 'btn-dark';
                        $data['presence_icon'] = 'fa-calendar-check';
                        $data['presence_text_class'] = 'text-dark';
                        break;
                    case 5:
                        $data['presence_status_label'] = 'Libur';
                        $data['presence_status_class'] = 'btn-primary';
                        $data['presence_icon'] = 'fa-calendar-times';
                        $data['presence_text_class'] = 'text-primary';
                        break;
                    default:
                        $data['presence_status_label'] = 'Tidak Hadir';
                        $data['presence_status_class'] = 'btn-danger';
                        $data['presence_icon'] = 'fa-times';
                        $data['presence_text_class'] = 'text-danger';
                        break;
                }
            }
        } else {
            // Jika belum check-in, tentukan status berdasarkan shift_status
            if ($data['has_shift']) {
                switch ($data['shift_status']) {
                    case 'belum mulai':
                    case 'presensi masuk':
                    case 'sudah selesai':
                        $data['presence_status_label'] = 'Tidak Hadir';
                        $data['presence_status_class'] = 'btn-danger';
                        $data['presence_icon'] = 'fa-times';
                        $data['presence_text_class'] = 'text-danger';
                        break;
                    default:
                        $data['presence_status_label'] = 'Tidak Hadir';
                        $data['presence_status_class'] = 'btn-danger';
                        $data['presence_icon'] = 'fa-times';
                        $data['presence_text_class'] = 'text-danger';
                        break;
                }
            } else {
                // Jika tidak ada jadwal shift
                $data['presence_status_label'] = 'Tidak Hadir';
                $data['presence_status_class'] = 'btn-danger';
                $data['presence_icon'] = 'fa-times';
                $data['presence_text_class'] = 'text-danger';
            }
        }

        // Menentukan apakah bisa check-in
        $data['can_check_in'] = false;
        if ($data['shift_status'] == 'presensi masuk' && !$data['already_checked_in']) {
            $data['can_check_in'] = true;
        }

        // Menentukan apakah bisa check-out
        $data['can_check_out'] = false;
        if ($data['shift_status'] == 'sudah selesai' && $data['already_checked_in'] && !$data['already_checked_out']) {
            $data['can_check_out'] = true;
        }

        // Handle AJAX Check-In dan Check-Out
        if ($this->request->isAJAX()) {
            if ($this->request->getPost('check_in')) {
                return $this->handleCheckIn($data);
            } elseif ($this->request->getPost('check_out')) {
                return $this->handleClockOut($data);
            }
        }

        // Mengirimkan status shift ke frontend untuk pengaturan tombol
        if ($data['has_shift']) {
        } else {
            $data['shift_end_time'] = null;
        }

        echo view('layout/header', $data);
        echo view('layout/sidebar');
        echo view('layout/topbar');
        echo view('employee/attendance/index', $data);
        echo view('layout/footer');
    }

    private function handleCheckIn($data)
    {
        if (!$data['can_check_in']) {
            log_message('error', 'Check-in gagal: Tidak dapat melakukan presensi saat ini.');
            return $this->response->setJSON(['status' => 'error', 'message' => 'Tidak dapat melakukan presensi saat ini.']);
        }

        // Ambil data menggunakan getPost()
        $latitude = $this->request->getPost('latitude');
        $longitude = $this->request->getPost('longitude');
        $shift_id = $this->request->getPost('work_shift');

        log_message('info', "Check-in data: latitude={$latitude}, longitude={$longitude}, shift_id={$shift_id}");

        $rules = [
            'latitude' => 'required|decimal',
            'longitude' => 'required|decimal',
            'work_shift' => 'required|integer'
        ];

        $input = [
            'latitude' => $latitude,
            'longitude' => $longitude,
            'work_shift' => $shift_id
        ];

        if (!$this->validate($rules, $input)) {
            log_message('error', 'Check-in gagal: Data lokasi atau shift tidak valid.');
            return $this->response->setJSON(['status' => 'error', 'message' => 'Data lokasi atau shift tidak valid.']);
        }

        // Validasi shift_id dari schedule_shift
        if (!$data['has_shift'] || $data['schedule_shift']['shift_id'] != $shift_id) {
            log_message('error', "Check-in gagal: Shift ID tidak valid atau tidak sesuai dengan jadwal.");
            return $this->response->setJSON(['status' => 'error', 'message' => 'Shift tidak valid atau tidak sesuai dengan jadwal.']);
        }

        // Validasi shift_id
        $shiftData = $this->shiftModel->find($shift_id);
        if (!$shiftData) {
            log_message('error', "Check-in gagal: Shift dengan ID {$shift_id} tidak ditemukan.");
            return $this->response->setJSON(['status' => 'error', 'message' => 'Shift tidak ditemukan.']);
        }

        $username = session()->get('username');
        $employeeId = $data['account']['employee_id'];
        $departmentId = $this->attendanceModel->getEmployeeDepartment($employeeId);

        if (is_null($departmentId)) {
            log_message('error', "Check-in gagal: Department ID tidak ditemukan untuk employee ID {$employeeId}.");
            return $this->response->setJSON(['status' => 'error', 'message' => 'Department ID tidak ditemukan!']);
        }

        $in_time = date('H:i:s');
        $today = date('Y-m-d');

        $allowedTime = date('H:i:s', strtotime($shiftData['start_time'] . '+5 minutes +59 seconds'));
        $inStatus = (strtotime($in_time) <= strtotime($allowedTime)) ? 'Tepat Waktu' : 'Terlambat';
        $presence_status = 1; // Hadir

        $attendanceData = [
            'employee_id' => $employeeId,
            'username' => $username,
            'attendance_date' => $today,
            'department_id' => $departmentId,
            'schedule_id' => $data['schedule_shift']['schedule_id'],
            'in_time' => $in_time,
            'in_status' => $inStatus,
            'presence_status' => $presence_status,
            'check_in_latitude' => $latitude,
            'check_in_longitude' => $longitude,
        ];

        $inserted = $this->attendanceModel->insert($attendanceData);

        if ($inserted) {
            log_message('info', "Check-in berhasil: Employee ID {$employeeId}.");
            return $this->response->setJSON(['status' => 'success', 'message' => 'Berhasil Presensi Masuk!']);
        } else {
            log_message('error', "Check-in gagal: Gagal menyimpan data untuk Employee ID {$employeeId}.");
            return $this->response->setJSON(['status' => 'error', 'message' => 'Gagal Presensi Masuk!']);
        }
    }

    private function handleClockOut($data)
    {
        if (!$data['can_check_out']) {
            log_message('error', 'Clock-out gagal: Tidak dapat melakukan presensi keluar saat ini.');
            return $this->response->setJSON(['status' => 'error', 'message' => 'Tidak dapat melakukan presensi keluar saat ini.']);
        }

        // Cek apakah sudah melakukan check-in
        if (!$data['already_checked_in']) {
            log_message('error', 'Clock-out gagal: Belum melakukan presensi masuk.');
            return $this->response->setJSON(['status' => 'error', 'message' => 'Belum melakukan presensi masuk.']);
        }

        // Ambil data menggunakan getPost()
        $latitude = $this->request->getPost('latitude');
        $longitude = $this->request->getPost('longitude');

        $rules = [
            'latitude' => 'required|decimal',
            'longitude' => 'required|decimal',
        ];

        $input = [
            'latitude' => $latitude,
            'longitude' => $longitude,
        ];

        if (!$this->validate($rules, $input)) {
            log_message('error', 'Clock-out gagal: Data lokasi tidak valid.');
            return $this->response->setJSON(['status' => 'error', 'message' => 'Data lokasi tidak valid.']);
        }

        $employeeId = $data['account']['employee_id'];
        $today = date('Y-m-d');
        $out_time = date('H:i:s');

        $attendanceData = [
            'out_time' => $out_time,
            'check_out_latitude' => $latitude,
            'check_out_longitude' => $longitude,
        ];

        $updated = $this->attendanceModel->clockOut($employeeId, $today, $attendanceData);

        if ($updated) {
            log_message('info', "Clock-out berhasil: Employee ID {$employeeId}.");
            return $this->response->setJSON(['status' => 'success', 'message' => 'Berhasil Presensi Keluar!']);
        } else {
            log_message('error', "Clock-out gagal: Gagal menyimpan data untuk Employee ID {$employeeId}.");
            return $this->response->setJSON(['status' => 'error', 'message' => 'Gagal Presensi Keluar!']);
        }
    }

    public function attendanceHistory()
    {
        $data['title'] = 'Riwayat Presensi';
        $data['account'] = $this->authModel->getAccount(session()->get('username'));

        $employee_id = $data['account']['employee_id'] ?? null;

        if (!$employee_id) {
            return $this->response->setStatusCode(ResponseInterface::HTTP_BAD_REQUEST, 'Employee ID is required but not found.')->sendBody();
        }

        // Ambil bulan dan tahun dari request, default ke bulan dan tahun sekarang
        $month = $this->request->getGet('month') ?? date('m');
        $year = $this->request->getGet('year') ?? date('Y');

        // Simpan ke data untuk digunakan di view
        $data['month'] = $month;
        $data['year'] = $year;

        // Ambil data kehadiran berdasarkan employee_id, bulan, dan tahun melalui model
        $attendance = $this->attendanceModel->getAttendanceByEmployeeAndDate($employee_id, $month, $year);

        $attendanceData = [];
        foreach ($attendance as $att) {
            if (is_array($att) && isset($att['date']) && isset($att['presence_status'])) {
                // Simpan status presensi berdasarkan tanggal
                $attendanceData[$att['date']] = $att['presence_status'];
            }
        }
        $data['attendance'] = $attendanceData;

        // Load views
        echo view('layout/header', $data);
        echo view('layout/sidebar');
        echo view('layout/topbar');
        echo view('employee/attendance_history/index', $data);
        echo view('layout/footer');
    }

    public function workSchedule()
    {
        $data['title'] = 'Jadwal Kerja';
        $data['account'] = $this->authModel->getAccount(session()->get('username'));

        $employee_id = $data['account']['employee_id'] ?? null;

        if (!$employee_id) {
            return $this->response->setStatusCode(ResponseInterface::HTTP_BAD_REQUEST, 'Employee ID is required but not found.')->sendBody();
        }

        // Ambil bulan dan tahun dari request, default ke bulan dan tahun sekarang
        $month = (int) ($this->request->getGet('month') ?? date('m'));
        $year = (int) ($this->request->getGet('year') ?? date('Y'));

        // Simpan ke data untuk digunakan di view
        $data['month'] = $month;
        $data['year'] = $year;

        // Ambil data jadwal kerja berdasarkan employee_id, bulan, dan tahun melalui model
        $schedule = $this->workScheduleModel->getWorkSchedulesByEmployeeAndMonthDate($employee_id, $month, $year);

        $scheduleData = [];
        foreach ($schedule as $sch) {
            if (isset($sch['schedule_date'])) {
                $date = $sch['schedule_date'];
                $scheduleStatus = $sch['schedule_status'] ?? null;
                $shiftId = $sch['shift_id'] ?? null;
                $startTime = $sch['start_time'] ?? null;
                $endTime = $sch['end_time'] ?? null;

                if ($scheduleStatus == 4) {
                    // Cuti
                    $statusKerja = 'Cuti';
                    $shiftClass = 'dark'; // Kelas badge untuk Cuti
                } elseif ($scheduleStatus == 5) {
                    // Libur
                    $statusKerja = 'Libur';
                    $shiftClass = 'primary';
                } elseif ($shiftId && $startTime && $endTime) {
                    // Shift kerja
                    $formattedStartTime = date('H:i', strtotime($startTime));
                    $formattedEndTime = date('H:i', strtotime($endTime));
                    $statusKerja = "{$formattedStartTime} - {$formattedEndTime}";

                    // Semua shift menggunakan kelas badge 'success'
                    $shiftClass = 'success';
                } else {
                    // Tidak ada jadwal
                    $statusKerja = 'Tidak Ada Jadwal';
                    $shiftClass = 'secondary';
                }

                // Simpan data ke array
                $scheduleData[$date] = [
                    'status_kerja' => esc($statusKerja),
                    'shift_class' => esc($shiftClass)
                ];
            }
        }
        $data['schedule'] = $scheduleData;

        // Load views
        echo view('layout/header', $data);
        echo view('layout/sidebar');
        echo view('layout/topbar');
        echo view('employee/work_schedule/index', $data);
        echo view('layout/footer');
    }
}
