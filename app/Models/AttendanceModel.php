<?php

namespace App\Models;

use CodeIgniter\Model;

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

    /**
     * Mengambil data presensi berdasarkan rentang tanggal dan departemen
     */
    public function getAttendance($start, $end, $dept = null)
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
        $builder->where('attendance.presence_status', 1);

        $builder->orderBy('attendance.attendance_date', 'ASC');

        return $builder->get()->getResultArray();
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
