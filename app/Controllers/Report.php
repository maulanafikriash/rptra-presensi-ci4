<?php

namespace App\Controllers;

use Config\Database;
use App\Models\DepartmentModel;
use App\Models\AttendanceModel;
use App\Models\EmployeeModel;
use \App\Models\AuthModel;
use App\Models\ShiftModel;
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
    protected $db;

    public function __construct()
    {
        $this->authModel = new AuthModel();
        $this->attendanceModel = new AttendanceModel();
        $this->shiftModel = new ShiftModel();
        $this->departmentModel = new DepartmentModel();
        $this->employeeModel = new EmployeeModel();
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
            'shift_data' => $this->shiftModel->getAllShifts(),
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
            $attendanceModel = new \App\Models\AttendanceModel();

            // Ambil data presensi
            $attendance = $attendanceModel->getAttendance($start, $end, $dept);

            // Ambil data presensi dengan shift
            $attendanceWithShift = $attendanceModel->getAttendanceWithShift($start, $end, $dept);

            // Gabungkan data presensi dengan data shift
            foreach ($attendance as &$atd) {
                foreach ($attendanceWithShift as $shiftData) {
                    if ($shiftData['attendance_id'] == $atd['attendance_id']) {
                        $atd['end_time'] = $shiftData['end_time'];
                        break;
                    }
                }
            }

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
            'shift_data' => $this->shiftModel->getAllShifts(),
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
        $mpdf->Output('Laporan_Kehadiran_Pegawai.pdf', 'I');
    }

    public function printExcelAttendanceByDepartment($start, $end, $dept)
    {
        // Mengambil data presensi berdasarkan tanggal dan departemen
        $attendance = $this->attendanceModel->getAttendance($start, $end, $dept);
        $department = $this->departmentModel->getDepartmentById($dept);

        // Mengambil semua data shift
        $shift_data = $this->shiftModel->getAllShifts();

        // Membuat spreadsheet baru
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Header laporan
        $sheet->setCellValue('A1', 'Laporan Kehadiran Pegawai');
        $sheet->setCellValue('A2', 'Tanggal: ' . $start . ' - ' . $end);
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

        foreach ($groupedAttendance as $date => $attendances) {
            foreach ($attendances as $atd) {
                // Mendapatkan informasi shift
                $shift_info = array_filter($shift_data, function ($shift) use ($atd) {
                    return $shift['shift_id'] == $atd['shift_id'];
                });
                $shift_info = array_values($shift_info);
                if (!empty($shift_info)) {
                    $shift = $shift_info[0];
                    // Menggunakan helper function untuk mendapatkan status checkout
                    $checkout_status = get_checkout_status($atd, $shift, $atd['attendance_date']);
                } else {
                    $checkout_status = 'Shift Tidak Ditemukan';
                }

                // Menulis data ke spreadsheet
                $sheet->setCellValue('A' . $row, $i++);
                $sheet->setCellValue('B' . $row, date('d-m-Y', strtotime($date)));
                $sheet->setCellValue('C' . $row, $atd['employee_name']);
                $sheet->setCellValue('D' . $row, $this->getShiftInfoFromData($atd['shift_id'], $shift_data));
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
        $filename = 'Laporan_Kehadiran_Pegawai_' . date('Y-m-d_H-i-s') . '.xlsx';

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
            $date = date('l, d F Y', strtotime($atd['attendance_date']));
            $grouped[$date][] = $atd;
        }
        return $grouped;
    }

    private function getShiftInfoFromData($shift_id, $shift_data)
    {
        foreach ($shift_data as $shift) {
            if ($shift['shift_id'] == $shift_id) {
                return $shift['shift_id'] . " = " . date('H:i', strtotime($shift['start_time'])) . " - " . date('H:i', strtotime($shift['end_time']));
            }
        }
        return "Shift Tidak Ditemukan";
    }
    // ---------------batass
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

        $data = [
            'employee' => $employee,
            'attendance' => $attendance,
            'month' => $month,
            'year' => $year,
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

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $sheet->setCellValue('A1', 'Riwayat Presensi Pegawai');
        $sheet->setCellValue('A2', 'Nama Pegawai: ' . $employee['employee_name']);
        $sheet->setCellValue('A3', 'Bulan: ' . date('F', mktime(0, 0, 0, $month, 1)) . " $year");

        $sheet->setCellValue('A5', 'Tanggal');
        $sheet->setCellValue('B5', 'Status Presensi');

        $row = 6;
        foreach ($attendance as $date => $status) {
            $sheet->setCellValue('A' . $row, date('d-m-Y', strtotime($date)));
            $sheet->setCellValue('B' . $row, $status);
            $row++;
        }

        $filename = "Riwayat_Presensi_{$employee['employee_name']}_{$month}_{$year}.xlsx";

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');

        $writer = new Xlsx($spreadsheet);
        $writer->save('php://output');
    }
}
