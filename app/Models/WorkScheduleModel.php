<?php

namespace App\Models;

use CodeIgniter\Model;

class WorkScheduleModel extends Model
{
    protected $table = 'schedule';
    protected $primaryKey = 'schedule_id';
    protected $allowedFields = [
        'employee_id',
        'department_id',
        'shift_id',
        'schedule_date',
        'schedule_status'
    ];

    public function getWorkSchedulesByEmployeeAndMonth($employeeId, $month, $year)
    {
        $builder = $this->db->table($this->table);
        $builder->select('schedule.*, shift.start_time, shift.end_time');
        $builder->join('shift', 'schedule.shift_id = shift.shift_id', 'left');
        $builder->where('schedule.employee_id', $employeeId);
        $builder->where('MONTH(schedule.schedule_date)', $month);
        $builder->where('YEAR(schedule.schedule_date)', $year);
        $builder->orderBy('schedule.schedule_date', 'ASC');
        $schedules = $builder->get()->getResultArray();

        $result = [];
        foreach ($schedules as $schedule) {
            $day = (int) date('j', strtotime($schedule['schedule_date']));
            $result[$day] = $schedule;
        }

        return $result;
    }

    public function getWorkSchedulesByEmployeeAndMonthDate($employee_id, $month, $year)
    {
        // Menghitung tanggal awal dan akhir bulan
        $startDate = "$year-" . str_pad($month, 2, '0', STR_PAD_LEFT) . "-01";
        $endDate = date("Y-m-t", strtotime($startDate)); // 't' untuk mendapatkan jumlah hari dalam bulan

        // Query untuk mengambil jadwal kerja dengan join ke tabel shift
        return $this->select('schedule.schedule_date, schedule.shift_id, shift.start_time, shift.end_time, schedule.schedule_status')
                    ->join('shift', 'schedule.shift_id = shift.shift_id', 'left')
                    ->where('schedule.employee_id', $employee_id)
                    ->where('schedule_date >=', $startDate)
                    ->where('schedule_date <=', $endDate)
                    ->findAll();
    }
}
