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

        // Cek presensi hari ini
        $attendance = $this->attendanceModel->where('employee_id', $employeeId)
            ->where('attendance_date', $today)
            ->first();

        $presence_status = $attendance['presence_status'] ?? null;
        $leave_statuses = [
            2 => ['text' => 'Izin',  'icon' => 'fa-user-clock',      'color' => 'text-warning'],
            3 => ['text' => 'Sakit', 'icon' => 'fa-medkit',          'color' => 'text-warning'],
            4 => ['text' => 'Cuti',  'icon' => 'fa-calendar-check',  'color' => 'text-dark'],
            5 => ['text' => 'Libur', 'icon' => 'fa-calendar-day',    'color' => 'text-primary']
        ];

        if (array_key_exists($presence_status, $leave_statuses)) {
            $status_info = $leave_statuses[$presence_status];
            $data['status_text'] = $status_info['text'];
            $data['status_icon'] = $status_info['icon'];
            $data['status_color'] = $status_info['color'];
            $data['status_subtitle'] = 'Hari ini kamu ' . strtolower($status_info['text']) . '.'; // Tambahan untuk konsistensi

            echo view('layout/header', $data);
            echo view('layout/sidebar');
            echo view('layout/topbar');
            echo view('employee/attendance/status_display', $data);
            echo view('layout/footer');
            return;
        }

        $data['all_shifts'] = $this->shiftModel->findAll();

        $schedule = $this->workScheduleModel->where('employee_id', $employeeId)
            ->where('schedule_date', $today)
            ->where('shift_id !=', null)
            ->first();

        $isFlexibleShift = false;

        if ($schedule) {
            $data['has_shift'] = true;
            $data['schedule_shift'] = $schedule;
            $shiftDetails = $this->shiftModel->find($schedule['shift_id']);
            $data['shift_details'] = $shiftDetails;

            if ($shiftDetails && $shiftDetails['start_time'] == '05:00:00' && $shiftDetails['end_time'] == '22:00:00') {
                $isFlexibleShift = true;
            }
            $data['is_flexible_shift'] = $isFlexibleShift;

            $data['shift_start_time'] = date('H:i', strtotime($shiftDetails['start_time']));
            $data['shift_end_time'] = date('H:i', strtotime($shiftDetails['end_time']));

            $shiftStart = strtotime($today . ' ' . $shiftDetails['start_time']);
            $shiftEnd = strtotime($today . ' ' . $shiftDetails['end_time']);

            if ($isFlexibleShift) {
                $data['shift_status'] = 'presensi masuk';
            } else {
                if ($shiftEnd < $shiftStart) {
                    $shiftEnd = strtotime($today . ' ' . $shiftDetails['end_time'] . ' +1 day');
                }

                if ($currentTime < $shiftStart) {
                    $data['shift_status'] = 'belum mulai';
                } elseif ($currentTime >= $shiftStart && $currentTime <= $shiftEnd) {
                    $data['shift_status'] = 'presensi masuk';
                } else {
                    $data['shift_status'] = 'sudah selesai';
                }
            }
        } else {
            $data['status_text']      = 'Jadwal Kerja Tidak Tersedia';
            $data['status_icon']      = 'fa-calendar-times';
            $data['status_color']     = 'text-secondary';
            $data['status_subtitle']  = 'Jadwal kerja anda tidak tersedia, harap hubungi admin.'; // Pesan kustom

            echo view('layout/header', $data);
            echo view('layout/sidebar');
            echo view('layout/topbar');
            echo view('employee/attendance/status_display', $data);
            echo view('layout/footer');
            return;
        }

        $data['already_checked_in'] = !empty($attendance) && !empty($attendance['in_time']);
        $data['presence_status'] = $attendance['presence_status'] ?? null;
        $data['already_checked_out'] = !empty($attendance['out_time']) && $attendance['out_time'] !== '-';

        $data['can_check_in'] = !$data['already_checked_in'];

        $data['can_check_out'] = false;
        if ($data['already_checked_in'] && !$data['already_checked_out']) {
            if ($isFlexibleShift) {
                $checkInTimestamp = strtotime($today . ' ' . $attendance['in_time']);
                $shiftEndTimestamp = strtotime($today . ' 22:00:00');

                // waktu paling cepat untuk bisa presensi keluar
                $eightHoursAfterCheckIn = $checkInTimestamp + (8 * 60 * 60);

                // waktu yang lebih awal antara (waktu masuk + 8 jam) atau (akhir shift jam 22:00)
                $validCheckOutTime = min($eightHoursAfterCheckIn, $shiftEndTimestamp);

                if ($currentTime >= $validCheckOutTime) {
                    $data['can_check_out'] = true;
                }
            } elseif (isset($data['shift_status']) && $data['shift_status'] == 'sudah selesai') {
                $data['can_check_out'] = true;
            }
        }

        $data['attendance_message'] = null;

        if ($data['already_checked_out']) {
            $checkOutTimeFormatted = date('H:i', strtotime($attendance['out_time']));
            $data['attendance_message'] = [
                'text' => "Anda telah berhasil presensi keluar pada pukul {$checkOutTimeFormatted}.",
                'icon' => 'fa-check-circle',
                'color' => 'text-success'
            ];
        } else if ($data['already_checked_in'] && !$data['can_check_out']) {
            $checkInTimeFormatted = date('H:i', strtotime($attendance['in_time']));
            $messageText = '';

            if ($isFlexibleShift) {
                $checkInTimestamp = strtotime($today . ' ' . $attendance['in_time']);
                $shiftEndTimestamp = strtotime($today . ' 22:00:00');
                $eightHoursAfterCheckIn = $checkInTimestamp + (8 * 60 * 60);
                $validCheckOutTime = min($eightHoursAfterCheckIn, $shiftEndTimestamp);
                $checkOutAvailableTimeFormatted = date('H:i', $validCheckOutTime);
                $messageText = "Berhasil presensi masuk pada pukul {$checkInTimeFormatted}. Presensi keluar dapat dilakukan pada pukul {$checkOutAvailableTimeFormatted}. Selamat bekerja.";
            } else {
                $messageText = "Berhasil presensi masuk pada pukul {$checkInTimeFormatted}. Selamat bekerja.";
            }

            $data['attendance_message'] = [
                'text' => $messageText,
                'icon' => 'fa-info-circle',
                'color' => 'text-info'
            ];
        }

        if ($this->request->isAJAX()) {
            if ($this->request->getPost('check_in')) {
                return $this->handleCheckIn($data);
            } elseif ($this->request->getPost('check_out')) {
                return $this->handleCheckOut($data);
            }
        }

        echo view('layout/header', $data);
        echo view('layout/sidebar');
        echo view('layout/topbar');
        echo view('employee/attendance/index', $data);
        echo view('layout/footer');
    }

    private function handleCheckIn($data)
    {
        if ($data['already_checked_in']) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Anda sudah melakukan presensi masuk hari ini.']);
        }

        $latitude = $this->request->getPost('latitude');
        $longitude = $this->request->getPost('longitude');
        $shift_id = $this->request->getPost('work_shift');

        $rules = [
            'latitude'    => 'required|decimal',
            'longitude'   => 'required|decimal',
            'work_shift'  => 'required|integer'
        ];

        if (!$this->validate($rules)) {
            log_message('error', 'Check-in gagal: Data lokasi atau shift tidak valid.');
            return $this->response->setJSON(['status' => 'error', 'message' => 'Data lokasi atau shift tidak valid.']);
        }

        $shiftData = $this->shiftModel->find($shift_id);
        if (!$shiftData) {
            log_message('error', "Check-in gagal: Shift dengan ID {$shift_id} tidak ditemukan.");
            return $this->response->setJSON(['status' => 'error', 'message' => 'Shift yang dipilih tidak ditemukan.']);
        }

        $employeeId = $data['account']['employee_id'];
        $today = date('Y-m-d');
        $departmentId = $this->attendanceModel->getEmployeeDepartment($employeeId);
        if (is_null($departmentId)) {
            log_message('error', "Check-in gagal: Department ID tidak ditemukan untuk employee ID {$employeeId}.");
            return $this->response->setJSON(['status' => 'error', 'message' => 'Gagal menemukan data departemen Anda!']);
        }

        $schedule = $this->workScheduleModel
            ->where('employee_id', $employeeId)
            ->where('schedule_date', $today)
            ->first();

        $schedule_id = null;
        if ($schedule) {
            // Jika jadwal sudah ada, update shift_id nya
            $this->workScheduleModel->update($schedule['schedule_id'], ['shift_id' => $shift_id]);
            $schedule_id = $schedule['schedule_id'];
        } else {
            // Jika jadwal tidak ada, buat baru
            $newScheduleData = [
                'employee_id'   => $employeeId,
                'schedule_date' => $today,
                'shift_id'      => $shift_id,
                'department_id' => $departmentId,
            ];
            $schedule_id = $this->workScheduleModel->insert($newScheduleData);
        }

        if (!$schedule_id) {
            log_message('error', "Check-in gagal: Gagal memperbarui atau membuat jadwal untuk Employee ID {$employeeId}.");
            return $this->response->setJSON(['status' => 'error', 'message' => 'Gagal memproses jadwal kerja.']);
        }

        $username = session()->get('username');
        $departmentId = $this->attendanceModel->getEmployeeDepartment($employeeId);

        if (is_null($departmentId)) {
            log_message('error', "Check-in gagal: Department ID tidak ditemukan untuk employee ID {$employeeId}.");
            return $this->response->setJSON(['status' => 'error', 'message' => 'Department ID tidak ditemukan!']);
        }

        $in_time = date('H:i:s');
        $isFlexibleShift = ($shiftData['start_time'] == '05:00:00' && $shiftData['end_time'] == '22:00:00');

        if ($isFlexibleShift) {
            $inStatus = 'Tugas Luar';
        } else {
            $checkInTimestamp = strtotime($in_time);
            $shiftStartTimestamp = strtotime($shiftData['start_time']);

            // Batas waktu untuk dianggap "Tepat Waktu" (jam masuk + 15 menit)
            $onTimeLimitTimestamp = $shiftStartTimestamp + (15 * 60);

            if ($checkInTimestamp < $shiftStartTimestamp) {
                $inStatus = 'Lebih Awal';
            } elseif ($checkInTimestamp >= $shiftStartTimestamp && $checkInTimestamp <= $onTimeLimitTimestamp) {
                $inStatus = 'Tepat Waktu';
            } else {
                $inStatus = 'Terlambat';
            }
        }

        $attendanceData = [
            'employee_id'       => $employeeId,
            'username'          => $username,
            'attendance_date'   => $today,
            'department_id'     => $departmentId,
            'schedule_id'       => $schedule_id,
            'in_time'           => $in_time,
            'in_status'         => $inStatus,
            'presence_status'   => 1, // Hadir
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

    private function handleCheckOut($data)
    {
        if (!$data['can_check_out']) {
            log_message('error', 'Clock-out gagal: Tidak dapat melakukan presensi keluar saat ini.');
            return $this->response->setJSON(['status' => 'error', 'message' => 'Tidak dapat melakukan presensi keluar saat ini.']);
        }

        if (!$data['already_checked_in']) {
            log_message('error', 'Clock-out gagal: Belum melakukan presensi masuk.');
            return $this->response->setJSON(['status' => 'error', 'message' => 'Belum melakukan presensi masuk.']);
        }

        $latitude = $this->request->getPost('latitude');
        $longitude = $this->request->getPost('longitude');

        $rules = [
            'latitude' => 'required|decimal',
            'longitude' => 'required|decimal',
        ];

        if (!$this->validate($rules)) {
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

        $data['month'] = $month;
        $data['year'] = $year;

        // Ambil data kehadiran
        $attendance = $this->attendanceModel->getAttendanceByEmployeeAndDate($employee_id, $month, $year);

        $attendanceData = [];
        foreach ($attendance as $att) {
            if (is_array($att) && isset($att['date']) && isset($att['presence_status'])) {
                $attendanceData[$att['date']] = $att['presence_status'];
            }
        }
        $data['attendance'] = $attendanceData;

        $summary = [
            'hadir' => 0,
            'izin_sakit' => 0,
            'alpha' => 0,
            'libur_cuti' => 0,
        ];

        $daysInMonth = cal_days_in_month(CAL_GREGORIAN, $month, $year);
        $today = date('Y-m-d');

        for ($day = 1; $day <= $daysInMonth; $day++) {
            $date = date('Y-m-d', strtotime("$year-$month-$day"));

            // Hanya hitung tanggal yang sudah lewat
            if ($date > $today) {
                continue;
            }

            if (isset($attendanceData[$date])) {
                switch ($attendanceData[$date]) {
                    case 1:
                        $summary['hadir']++;
                        break;
                    case 2:
                        $summary['izin_sakit']++;
                        break; // Izin
                    case 3:
                        $summary['izin_sakit']++;
                        break; // Sakit
                    case 4:
                        $summary['libur_cuti']++;
                        break; // Cuti
                    case 5:
                        $summary['libur_cuti']++;
                        break; // Libur
                }
            } else {
                $summary['alpha']++;
            }
        }
        $data['summary'] = $summary;

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

        $month = (int) ($this->request->getGet('month') ?? date('m'));
        $year = (int) ($this->request->getGet('year') ?? date('Y'));

        $data['month'] = $month;
        $data['year'] = $year;

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
                    $shiftClass = 'dark';
                } elseif ($scheduleStatus == 5) {
                    // Libur
                    $statusKerja = 'Libur';
                    $shiftClass = 'primary';
                } elseif ($shiftId && $startTime && $endTime) {
                    // Shift kerja
                    $formattedStartTime = date('H:i', strtotime($startTime));
                    $formattedEndTime = date('H:i', strtotime($endTime));
                    $statusKerja = "{$formattedStartTime} - {$formattedEndTime}";

                    $shiftClass = 'success';
                } else {
                    $statusKerja = 'Tidak Ada Jadwal';
                    $shiftClass = 'secondary';
                }

                $scheduleData[$date] = [
                    'status_kerja' => esc($statusKerja),
                    'shift_class' => esc($shiftClass)
                ];
            }
        }
        $data['schedule'] = $scheduleData;

        echo view('layout/header', $data);
        echo view('layout/sidebar');
        echo view('layout/topbar');
        echo view('employee/work_schedule/index', $data);
        echo view('layout/footer');
    }
}
