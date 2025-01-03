<?php

namespace App\Controllers;

use Config\Database;
use App\Models\DepartmentModel;
use App\Models\AttendanceModel;
use App\Models\EmployeeModel;
use \App\Models\AuthModel;
use App\Models\ShiftModel;
use App\Models\WorkScheduleModel;
use \Mpdf\Mpdf;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class Report extends BaseController
{
    protected $authModel;
    protected $attendanceModel;
    protected $departmentModel;
    protected $shiftModel;
    protected $employeeModel;
    protected $workScheduleModel;
    protected $db;

    public function __construct()
    {
        $this->authModel = new AuthModel();
        $this->attendanceModel = new AttendanceModel();
        $this->shiftModel = new ShiftModel();
        $this->departmentModel = new DepartmentModel();
        $this->employeeModel = new EmployeeModel();
        $this->workScheduleModel = new WorkScheduleModel();
        $this->db = Database::connect();
    }

    public function index()
    {
        $start = $this->request->getGet('start');
        $end = $this->request->getGet('end');
        $dept = $this->request->getGet('dept');

        $data = [
            'title' => 'Laporan Kehadiran',
            'account' => $this->authModel->getAccount(session()->get('username')),
            'department' => $this->employeeModel->getDepartments(),
            'start' => $start,
            'end' => $end,
            'dept' => $dept,
            'attendance' => $this->attendanceDetails($start, $end, $dept),
            // 'shift_data' => $this->shiftModel->getAllShifts(),
        ];

        echo view('layout/header', $data);
        echo view('layout/sidebar');
        echo view('layout/topbar');
        echo view('admin/report/index', $data);
        echo view('layout/footer');
    }

    private function attendanceDetails($start, $end, $dept)
    {
        if (!$start || !$end) {
            return false;
        } else {
            $attendanceModel = new AttendanceModel();

            // Ambil data presensi dengan shift, pastikan schedule_date = attendance_date dan presence_status = 1
            $attendance = $attendanceModel->getAttendance($start, $end, $dept);

            return $attendance;
        }
    }

    public function printPdfAttendanceByDepartment($start, $end, $dept)
    {
        $attendance = $this->attendanceModel->getAttendance($start, $end, $dept);
        $department = $this->departmentModel->getDepartmentById($dept);
        $data = [
            'start' => $start,
            'end' => $end,
            'dept' => $dept,
            'dept_name' => $department['department_name'],
            'attendance' => $this->groupAttendanceByDate($attendance),
        ];

        $html = view('admin/report/print_attendance_all', $data);

        // Bersihkan output buffering
        while (ob_get_level() > 0) {
            ob_end_clean();
        }

        $mpdf = new Mpdf([
            'mode' => 'utf-8',
            'format' => 'A4-L',
            'default_font_size' => 12,
            'default_font' => 'Arial'
        ]);
        $mpdf->SetHeader('RPTRA Cibubur Berseri');
        $mpdf->SetFooter('Dicetak pada: {DATE j-m-Y H:i:s}');
        $mpdf->WriteHTML($html);
        $mpdf->Output('Laporan_Kehadiran_Pengelola.pdf', 'I');
    }

    public function printExcelAttendanceByDepartment($start, $end, $dept)
    {
        // Mengambil data presensi berdasarkan tanggal dan departemen
        $attendance = $this->attendanceModel->getAttendance($start, $end, $dept);
        $department = $this->departmentModel->getDepartmentById($dept);
        $startDate = new \DateTime($start);
        $endDate = new \DateTime($end);

        // Membuat spreadsheet baru
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Header laporan
        $sheet->setCellValue('A1', 'Laporan Kehadiran');
        $sheet->setCellValue('A2', 'Dari Tanggal: ' . $startDate->format('d-m-Y') . ' - ' . $endDate->format('d-m-Y'));
        $sheet->setCellValue('A3', 'Department: ' . ($department['department_name'] ?? 'Semua Department'));

        // Header tabel
        $sheet->setCellValue('A5', 'No');
        $sheet->setCellValue('B5', 'Tanggal');
        $sheet->setCellValue('C5', 'Nama');
        $sheet->setCellValue('D5', 'Shift');
        $sheet->setCellValue('E5', 'Check In');
        $sheet->setCellValue('F5', 'Status Masuk');
        $sheet->setCellValue('G5', 'Check Out');

        // Menambahkan styling pada header tabel
        $headerStyleArray = [
            'font' => [
                'bold' => true,
                'color' => ['argb' => 'FFFFFFFF'],
            ],
            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'startColor' => ['argb' => 'FF007BFF'],
            ],
            'alignment' => [
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                ],
            ],
        ];
        $sheet->getStyle('A5:G5')->applyFromArray($headerStyleArray);

        // Isi data
        $row = 6;
        $i = 1;
        $groupedAttendance = $this->groupAttendanceByDate($attendance);

        foreach ($groupedAttendance as $date => $attendances) { // Group by Y-m-d
            foreach ($attendances as $atd) {
                // Menggunakan shift_start dan shift_end dari data presensi
                $checkout_status = get_checkout_status($atd, [
                    'start_time' => $atd['shift_start'],
                    'end_time' => $atd['shift_end']
                ], $atd['attendance_date']);

                // Menulis data ke spreadsheet
                $sheet->setCellValue('A' . $row, $i++);
                $sheet->setCellValue('B' . $row, date('d-m-Y', strtotime($atd['attendance_date'])));
                $sheet->setCellValue('C' . $row, $atd['employee_name']);
                $sheet->setCellValue('D' . $row, (!empty($atd['shift_id']) && !empty($atd['shift_start']) && !empty($atd['shift_end'])) ? htmlspecialchars($atd['shift_id']) . " = " . date('H:i', strtotime($atd['shift_start'])) . " - " . date('H:i', strtotime($atd['shift_end'])) : "Shift Tidak Ditemukan");
                $sheet->setCellValue('E' . $row, $atd['in_time'] ? date('H:i:s', strtotime($atd['in_time'])) : 'Belum check in');
                $sheet->setCellValue('F' . $row, $atd['in_status']);
                $sheet->setCellValue('G' . $row, $checkout_status);
                $row++;
            }
        }

        // Menambahkan styling pada isi tabel
        $bodyStyleArray = [
            'alignment' => [
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                ],
            ],
        ];
        $sheet->getStyle('A5:G' . ($row - 1))->applyFromArray($bodyStyleArray);

        // Menyesuaikan lebar kolom secara otomatis
        foreach (range('A', 'G') as $columnID) {
            $sheet->getColumnDimension($columnID)->setAutoSize(true);
        }

        // Mengatur style header dan footer
        $sheet->getStyle('A1:G1')->getFont()->setBold(true)->setSize(14);
        $sheet->getStyle('A2:G2')->getFont()->setBold(true);
        $sheet->getStyle('A3:G3')->getFont()->setBold(true);

        // Membuat filename dengan format yang diinginkan
        $filename = 'Laporan_Kehadiran_Pengelola_' . date('d-m-Y_H-i-s') . '.xlsx';

        // Mengirimkan file ke browser untuk diunduh
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');

        // Menulis file ke output
        $writer = new Xlsx($spreadsheet);
        $writer->save('php://output');
        exit;
    }

    private function groupAttendanceByDate($attendance)
    {
        $grouped = [];
        foreach ($attendance as $atd) {
            $date = $atd['attendance_date']; // Group by Y-m-d
            $grouped[$date][] = $atd;
        }
        return $grouped;
    }
    // ---------------section----------
    public function printPdfAttendanceHistory($employee_id)
    {
        $db = \Config\Database::connect();
        $employee = $db->table('employee')->where('employee_id', $employee_id)->get()->getRowArray();

        if (!$employee) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('Employee not found');
        }

        $month = $this->request->getGet('month') ?: date('m');
        $year = $this->request->getGet('year') ?: date('Y');
        $daysInMonth = cal_days_in_month(CAL_GREGORIAN, $month, $year);
        $currentDate = date('Y-m-d');

        $attendanceData = $this->attendanceModel->getAttendanceByEmployeeAndDate($employee_id, $month, $year);

        $attendance = [];
        for ($day = 1; $day <= $daysInMonth; $day++) {
            $date = sprintf('%04d-%02d-%02d', $year, $month, $day);
            $attendance[$date] = ($date <= $currentDate) ? 'Tidak Hadir' : 'Tidak Ada Data';
        }

        foreach ($attendanceData as $att) {
            $date = $att['date'];
            $statusMap = [
                1 => 'Hadir',
                0 => 'Tidak Hadir',
                2 => 'Izin',
                3 => 'Sakit',
                4 => 'Cuti',
                5 => 'Libur',
            ];
            $attendance[$date] = $statusMap[$att['presence_status']] ?? 'Tidak Ada Data';
        }

        $department = $this->departmentModel->getDepartmentById($employee['department_id']);
        $dept_name = $department['department_name'] ?? 'Departemen';

        $data = [
            'employee' => $employee,
            'attendance' => $attendance,
            'month' => $month,
            'year' => $year,
            'dept_name' => $dept_name,
        ];

        $html = mb_convert_encoding(view('admin/report/print_attendance_employee', $data), 'UTF-8', 'UTF-8');

        // Bersihkan output buffering
        while (ob_get_level() > 0) {
            ob_end_clean();
        }

        $pdf = new \Mpdf\Mpdf();
        $pdf->SetHeader('RPTRA Cibubur Berseri');
        $pdf->SetFooter('Dicetak pada: {DATE j-m-Y H:i:s}');
        $pdf->WriteHTML($html);

        // Set header HTTP
        header('Content-Type: application/pdf');
        header('Content-Disposition: inline; filename="Riwayat_Presensi_' . $employee['employee_name'] . "_{$month}_{$year}.pdf\"");
        header('Cache-Control: no-store, no-cache, must-revalidate');
        header('Pragma: public');

        // Nama file
        $filename = "Riwayat_Presensi_" . preg_replace('/[^a-zA-Z0-9_\-]/', '_', $employee['employee_name']) . "_{$month}_{$year}.pdf";
        $pdf->Output($filename, 'I');
    }

    public function printExcelAttendanceHistory($employee_id)
    {
        $db = \Config\Database::connect();
        $employee = $db->table('employee')->where('employee_id', $employee_id)->get()->getRowArray();

        if (!$employee) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('Employee not found');
        }

        $month = $this->request->getGet('month') ?: date('m');
        $year = $this->request->getGet('year') ?: date('Y');

        $daysInMonth = cal_days_in_month(CAL_GREGORIAN, $month, $year);
        $currentDate = date('Y-m-d');

        $attendanceData = $this->attendanceModel->getAttendanceByEmployeeAndDate($employee_id, $month, $year);

        $attendance = [];
        for ($day = 1; $day <= $daysInMonth; $day++) {
            $date = sprintf('%04d-%02d-%02d', $year, $month, $day);

            if ($date <= $currentDate) {
                $attendance[$date] = 'Tidak Hadir';
            } else {
                $attendance[$date] = 'Tidak Ada Data';
            }
        }

        foreach ($attendanceData as $att) {
            $date = $att['date'];
            switch ($att['presence_status']) {
                case 1:
                    $attendance[$date] = 'Hadir';
                    break;
                case 0:
                    $attendance[$date] = 'Tidak Hadir';
                    break;
                case 2:
                    $attendance[$date] = 'Izin';
                    break;
                case 3:
                    $attendance[$date] = 'Sakit';
                    break;
                case 4:
                    $attendance[$date] = 'Cuti';
                    break;
                case 5:
                    $attendance[$date] = 'Libur';
                    break;
                default:
                    $attendance[$date] = 'Tidak Ada Data';
            }
        }

        // Ambil department_id dari employee dan kemudian department_name
        $department = $this->departmentModel->getDepartmentById($employee['department_id']);
        $dept_name = $department['department_name'] ?? 'Departemen';

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Header laporan
        $sheet->setCellValue('A1', 'Riwayat Presensi ' . ($dept_name ?? 'Departemen'));
        $sheet->setCellValue('A2', 'Nama : ' . $employee['employee_name']);
        $sheet->setCellValue('A3', 'Bulan : ' . date('F', mktime(0, 0, 0, $month, 1)) . " $year");

        // Header tabel
        $sheet->setCellValue('A5', 'Tanggal');
        $sheet->setCellValue('B5', 'Status Presensi');

        // Menambahkan styling pada header tabel
        $headerStyleArray = [
            'font' => [
                'bold' => true,
                'color' => ['argb' => 'FFFFFFFF'],
            ],
            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'startColor' => ['argb' => 'FF007BFF'],
            ],
            'alignment' => [
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                ],
            ],
        ];
        $sheet->getStyle('A5:B5')->applyFromArray($headerStyleArray);

        // Isi data
        $row = 6;
        foreach ($attendance as $date => $status) {
            // Tentukan warna badge berdasarkan status
            $badgeColor = '';
            switch ($status) {
                case 'Hadir':
                    $badgeColor = 'FF28A745'; // Hijau
                    break;
                case 'Tidak Hadir':
                    $badgeColor = 'DC3545'; // Merah
                    break;
                case 'Izin':
                case 'Sakit':
                    $badgeColor = 'FFC107'; // Kuning
                    break;
                case 'Cuti':
                    $badgeColor = '6C757D'; // Abu-abu
                    break;
                case 'Libur':
                    $badgeColor = '007BFF'; // Biru
                    break;
                case 'Tidak Ada Data':
                    $badgeColor = '6C757D'; // Abu-abu
                    break;
                default:
                    $badgeColor = '6C757D'; // Abu-abu
            }

            // Menulis data ke spreadsheet
            $sheet->setCellValue('A' . $row, date('d-m-Y', strtotime($date)));
            $sheet->setCellValue('B' . $row, $status);

            // Terapkan warna latar belakang sel berdasarkan status
            $sheet->getStyle('B' . $row)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                ->getStartColor()->setARGB($badgeColor);

            // Terapkan warna teks putih untuk kontras
            $sheet->getStyle('B' . $row)->getFont()->getColor()->setARGB('FFFFFFFF');

            // Terapkan alignment center
            $sheet->getStyle('A' . $row . ':B' . $row)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

            // Menambahkan border
            $sheet->getStyle('A' . $row . ':B' . $row)->getBorders()->getAllBorders()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);

            $row++;
        }

        // Menyesuaikan lebar kolom secara otomatis
        foreach (range('A', 'B') as $columnID) {
            $sheet->getColumnDimension($columnID)->setAutoSize(true);
        }

        // Mengatur style header dan footer
        $sheet->getStyle('A1:B1')->getFont()->setBold(true)->setSize(14);
        $sheet->getStyle('A2:B2')->getFont()->setBold(true);
        $sheet->getStyle('A3:B3')->getFont()->setBold(true);

        // Membuat filename dengan format yang diinginkan
        $filename = "Riwayat_Presensi_{$employee['employee_name']}_{$month}_{$year}.xlsx";

        // Mengirimkan file ke browser untuk diunduh
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');

        // Menulis file ke output
        $writer = new Xlsx($spreadsheet);
        $writer->save('php://output');
        exit;
    }

    public function printWorkSchedulePdf($employeeId)
    {
        $month = $this->request->getGet('month') ?: date('m');
        $year = $this->request->getGet('year') ?: date('Y');

        $workSchedules = $this->workScheduleModel->getWorkSchedulesByEmployeeAndMonth($employeeId, $month, $year);

        // Mengambil data pegawai
        $employeeModel = new \App\Models\EmployeeModel();
        $employee = $employeeModel->find($employeeId);

        // Mengambil data departemen dan shift
        $department = $employeeModel->getDepartments($employee['department_id']);
        $shift = $employeeModel->getShifts($employee['shift_id']);

        $data = [
            'employee' => $employee,
            'department' => $department,
            'shift' => $shift,
            'month' => $month,
            'year' => $year,
            'workSchedules' => $workSchedules,
        ];

        $html = view('admin/report/work_schedule_pdf', $data);

        $dompdf = new Mpdf();
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'landscape');
        $dompdf->render();
        $dompdf->stream('work_schedule.pdf', ['Attachment' => 0]);
    }

    public function printWorkScheduleExcel($employeeId)
    {
        $month = $this->request->getGet('month') ?: date('m');
        $year = $this->request->getGet('year') ?: date('Y');

        $workSchedules = $this->workScheduleModel->getWorkSchedulesByEmployeeAndMonth($employeeId, $month, $year);

        // Mengambil data pegawai
        $employeeModel = new \App\Models\EmployeeModel();
        $employee = $employeeModel->find($employeeId);

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Header
        $sheet->setCellValue('A1', 'Jadwal Kerja Pegawai');
        $sheet->setCellValue('A2', 'Nama Pegawai: ' . $employee['employee_name']);
        $sheet->setCellValue('A3', 'Departemen: ' . $employeeModel->getDepartments($employee['department_id'])['department_name']);
        $sheet->setCellValue('A4', 'Bulan: ' . date('F', mktime(0, 0, 0, $month, 10)) . ' ' . $year);

        // Tabel Header
        $sheet->setCellValue('A6', 'No');
        $sheet->setCellValue('B6', 'Tanggal');
        $sheet->setCellValue('C6', 'Status');
        $sheet->setCellValue('D6', 'Shift');

        // Mengisi data jadwal
        $row = 7;
        $no = 1;
        foreach ($workSchedules as $ws) {
            $sheet->setCellValue('A' . $row, $no++);
            $sheet->setCellValue('B' . $row, date('d-m-Y', strtotime($ws['schedule_date'])));
            if ($ws['schedule_status'] === null && $ws['shift_id'] !== null) {
                $status = 'Shift Kerja';
                // Ambil shift detail
                $shiftDetail = $this->shiftModel->find($ws['shift_id']);
                $shiftTime = $shiftDetail['start_time'] . ' - ' . $shiftDetail['end_time'];
            } elseif ($ws['schedule_status'] === 4) {
                $status = 'Cuti';
                $shiftTime = '-';
            } elseif ($ws['schedule_status'] === 5) {
                $status = 'Libur';
                $shiftTime = '-';
            } else {
                $status = 'Tidak Ada Jadwal';
                $shiftTime = '-';
            }
            $sheet->setCellValue('C' . $row, $status);
            $sheet->setCellValue('D' . $row, $shiftTime);
            $row++;
        }

        // Membuat file Excel
        $writer = new Xlsx($spreadsheet);
        $fileName = 'Work_Schedule_' . $employee['employee_name'] . '_' . $month . '_' . $year . '.xlsx';

        // Redirect hasil generate ke browser
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="' . $fileName . '"');
        $writer->save('php://output');
        exit;
    }
}
