<?php

namespace App\Models;

use CodeIgniter\Model;
use Config\Database;

class AttendanceModel extends Model
{
    protected $table = 'attendance';
    protected $primaryKey = 'attendance_id';
    protected $returnType = 'array';
    protected $useAutoIncrement = true;
    protected $useTimestamps = false;
    protected $allowedFields = [
        'username',
        'employee_id',
        'department_id',
        'schedule_id',
        'attendance_date',
        'in_time',
        'in_status',
        'out_time',
        'presence_status',
        'check_in_latitude',
        'check_in_longitude',
        'check_out_latitude',
        'check_out_longitude'
    ];

    public function getAttendanceByEmployeeAndDate($employeeId, $month, $year)
    {
        return $this->select('
            attendance.attendance_date AS date,
            attendance.presence_status,
            attendance.check_in_latitude,
            attendance.check_in_longitude,
            attendance.check_out_latitude,
            attendance.check_out_longitude,
            shift.end_time
        ')
            ->join('schedule', 'attendance.schedule_id = schedule.schedule_id', 'left')
            ->join('shift', 'schedule.shift_id = shift.shift_id', 'left')
            ->where('attendance.employee_id', $employeeId)
            ->where('MONTH(attendance.attendance_date)', $month)
            ->where('YEAR(attendance.attendance_date)', $year)
            ->findAll();
    }

    public function updateAttendanceByEmployeeAndDate(string $employeeId, string $date, array $data): bool
    {
        return $this->where('employee_id', $employeeId)
            ->where('attendance_date', $date)
            ->set($data)
            ->update();
    }

    public function getAttendanceByEmployeeId($start, $end, $employee_id)
    {
        $builder = $this->db->table('attendance a');
        $builder->select('
        a.*,
        e.employee_name,
        d.department_name,
        s.shift_id,
        s.start_time AS shift_start,
        s.end_time   AS shift_end
    ');
        $builder->join('employee e', 'e.employee_id = a.employee_id', 'left');
        $builder->join('department d', 'd.department_id = a.department_id', 'left');
        $builder->join('schedule sch', 'sch.schedule_id = a.schedule_id', 'left');
        $builder->join('shift s', 's.shift_id = sch.shift_id', 'left');

        $builder->where('a.attendance_date >=', $start);
        $builder->where('a.attendance_date <=', $end);
        $builder->where('a.employee_id', $employee_id);
        $builder->orderBy('a.attendance_date', 'ASC');

        return $builder->get()->getResultArray();
    }

    public function getAttendanceReportData($start, $end, $dept = null)
    {
        $db = Database::connect();
        $rptraName = session()->get('rptra_name');

        $employeeBuilder = $db->table('employee');
        if (!is_null($dept) && $dept !== 'all') {
            $employeeBuilder->where('department_id', $dept);
        }
        $employeeBuilder->where('rptra_name', $rptraName);
        $employees = $employeeBuilder->get()->getResultArray();

        $reportData = [];
        $currentDate = date('Y-m-d');

        foreach ($employees as $employee) {
            $period = new \DatePeriod(
                new \DateTime($start),
                new \DateInterval('P1D'),
                (new \DateTime($end))->modify('+1 day')
            );

            foreach ($period as $date) {
                $attendanceDate = $date->format('Y-m-d');

                $builder = $this->builder();
                $builder->select('
                    attendance.attendance_id,
                    attendance.attendance_date,
                    attendance.in_time,
                    attendance.in_status,
                    attendance.out_time,
                    attendance.presence_status,
                    shift.shift_id,
                    shift.start_time AS shift_start,
                    shift.end_time AS shift_end
                ');
                $builder->join('schedule', 'attendance.schedule_id = schedule.schedule_id', 'left');
                $builder->join('shift', 'schedule.shift_id = shift.shift_id', 'left');
                $builder->where('attendance.employee_id', $employee['employee_id']);
                $builder->where('attendance.attendance_date', $attendanceDate);

                $attendance = $builder->get()->getRowArray();

                $rowData = [
                    'employee_id'       => $employee['employee_id'],
                    'employee_name'     => $employee['employee_name'],
                    'rptra_name'        => $employee['rptra_name'], 
                    'attendance_date'   => $attendanceDate,
                    'in_time'           => null,
                    'in_status'         => null,
                    'out_time'          => null,
                    'out_status'        => null,
                    'shift_id'          => null,
                    'shift_start'       => null,
                    'shift_end'         => null,
                    'presence_status'   => null,
                    'presence_status_text' => ''
                ];

                if ($attendance) {
                    $rowData = array_merge($rowData, $attendance);
                }

                $status = $rowData['presence_status'];
                if ($status == '1') {
                    $rowData['presence_status_text'] = 'Hadir';
                } elseif ($status == '2') {
                    $rowData['presence_status_text'] = 'Izin';
                } elseif ($status == '3') {
                    $rowData['presence_status_text'] = 'Sakit';
                } elseif ($status == '4') {
                    $rowData['presence_status_text'] = 'Cuti';
                } elseif ($status == '5') {
                    $rowData['presence_status_text'] = 'Libur';
                } else {
                    // Jika tidak ada data di tabel attendance
                    if ($attendanceDate > $currentDate) {
                        $rowData['presence_status'] = 'default';
                        $rowData['presence_status_text'] = 'Tidak Ada Data';
                    } else {
                        $rowData['presence_status'] = '0';
                        $rowData['presence_status_text'] = 'Tidak Hadir';
                    }
                }

                $reportData[] = $rowData;
            }
        }

        // hasil berdasarkan tanggal dan nama pegawai
        usort($reportData, function ($a, $b) {
            if ($a['attendance_date'] == $b['attendance_date']) {
                return strcmp($a['employee_name'], $b['employee_name']);
            }
            return strcmp($a['attendance_date'], $b['attendance_date']);
        });

        return $reportData;
    }

    public function getAttendanceWithShift($start, $end, $dept = null)
    {
        $builder = $this->builder();
        $builder->select('
            attendance.attendance_id,
            attendance.attendance_date,
            employee.employee_name,
            attendance.in_status,
            attendance.in_time,
            attendance.out_time,
            shift.start_time AS shift_start,
            shift.end_time AS shift_end
        ');
        $builder->join('employee', 'attendance.employee_id = employee.employee_id', 'left');
        $builder->join('schedule', 'attendance.schedule_id = schedule.schedule_id', 'left'); // Join ke schedule
        $builder->join('shift', 'schedule.shift_id = shift.shift_id', 'left'); // Join ke shift melalui schedule

        if (!is_null($dept)) {
            $builder->where('attendance.department_id', $dept);
        }

        $builder->where('attendance.attendance_date >=', $start);
        $builder->where('attendance.attendance_date <=', $end);

        $builder->orderBy('attendance.attendance_date', 'ASC');

        return $builder->get()->getResultArray();
    }

    public function getEmployeeDepartment($employeeId)
    {
        $result = $this->db->table('employee')
            ->select('department_id')
            ->where('employee.employee_id', $employeeId)
            ->get()
            ->getRow();

        return $result ? $result->department_id : null;
    }

    public function clockOut($employeeId, $date, $data)
    {
        return $this->where('employee_id', $employeeId)
            ->where('attendance_date', $date)
            ->set($data)
            ->update();
    }
}
