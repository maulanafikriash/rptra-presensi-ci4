<?php

namespace App\Models;

use CodeIgniter\Model;

class AttendanceModel extends Model
{
    protected $table = 'attendance';
    protected $primaryKey = 'attendance_id';
    protected $returnType = 'array';
    protected $allowedFields = [
        'employee_id',
        'username',
        'attendance_date',
        'in_time',
        'in_status',
        'out_time',
        'presence_status',
        'check_in_latitude',
        'check_in_longitude',
        'check_out_latitude',
        'check_out_longitude',
        'shift_id',
        'department_id'
    ];

    /**
     * Mengambil data presensi berdasarkan ID karyawan, bulan, dan tahun
     */
    public function getAttendanceByEmployeeAndDate($employeeId, $month, $year)
    {
        return $this->select('attendance_date AS date, presence_status, check_in_latitude, check_in_longitude, check_out_latitude, check_out_longitude, shift.end_time')
            ->join('shift', 'attendance.shift_id = shift.shift_id', 'left')
            ->where('employee_id', $employeeId)
            ->where('MONTH(attendance_date)', $month)
            ->where('YEAR(attendance_date)', $year)
            ->findAll();
    }

    /**
     * Memperbarui data presensi berdasarkan ID karyawan dan tanggal
     */
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
            attendance.shift_id,
            employee.employee_name,
            attendance.in_status,
            attendance.in_time,
            attendance.out_time,
            shift.start_time AS shift_start,
            shift.end_time AS shift_end
        ');
        $builder->join('employee', 'attendance.employee_id = employee.employee_id', 'left');
        $builder->join('shift', 'attendance.shift_id = shift.shift_id', 'left');

        if (!is_null($dept)) {
            $builder->where('attendance.department_id', $dept);
        }

        $builder->where('attendance.attendance_date >=', $start);
        $builder->where('attendance.attendance_date <=', $end);
        $builder->where('attendance.presence_status', 1);

        $builder->orderBy('attendance.attendance_date', 'ASC');

        return $builder->get()->getResultArray();
    }

    /**
     * Mengambil data presensi dengan informasi shift berdasarkan rentang tanggal dan departemen
     */
    public function getAttendanceWithShift($start, $end, $dept = null)
    {
        $builder = $this->builder();
        $builder->select('attendance.*, shift.end_time');
        $builder->join('shift', 'attendance.shift_id = shift.shift_id', 'left');

        if (!is_null($dept)) {
            $builder->where('attendance.department_id', $dept);
        }
        $builder->where('attendance.attendance_date >=', $start);
        $builder->where('attendance.attendance_date <=', $end);

        return $builder->get()->getResultArray();
    }

    /**
     * Mengambil departemen berdasarkan ID karyawan
     */
    public function getEmployeeDepartment($employeeId)
    {
        $result = $this->db->table('employee')
            ->select('department_id')
            ->where('employee_id', $employeeId)
            ->get()
            ->getRow();

        return $result ? $result->department_id : null;
    }

    /**
     * Mengambil shift berdasarkan ID karyawan
     */
    public function getEmployeeShift($employeeId)
    {
        $result = $this->db->table('employee')
            ->select('shift_id')
            ->where('employee_id', $employeeId)
            ->get()
            ->getRow();

        return $result ? $result->shift_id : null;
    }

    /**
     * Metode untuk melakukan check-out
     */
    public function clockOut($employeeId, $date, $data)
    {
        return $this->where('employee_id', $employeeId)
            ->where('attendance_date', $date)
            ->set($data)
            ->update();
    }
}
