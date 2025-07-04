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
use IntlDateFormatter;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;

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
        $rptraName = session()->get('rptra_name');

        // filter sesuai RPTRA
        if ($start && $end) {
            $raw = $this->attendanceDetails($start, $end, $dept);
            $attendance = array_filter($raw, function ($row) use ($rptraName) {
                return isset($row['rptra_name']) && $row['rptra_name'] === $rptraName;
            });
        } else {
            $attendance = [];
        }

        $data = [
            'title'      => 'Laporan Kehadiran',
            'account'    => $this->authModel->getAccount(session()->get('username')),
            'department' => $this->employeeModel->getDepartments(),
            'start'      => $start,
            'end'        => $end,
            'dept'       => $dept,
            'attendance' => $attendance,
        ];

        echo view('layout/header', $data);
        echo view('layout/sidebar');
        echo view('layout/topbar');
        echo view('admin/report/index', $data);
        echo view('layout/footer');
    }

    private function attendanceDetails($start, $end, $dept)
    {
        return $this->attendanceModel->getAttendanceReportData($start, $end, $dept);
    }

    private function groupAttendanceByDate($attendance)
    {
        $grouped = [];
        foreach ($attendance as $atd) {
            $date = $atd['attendance_date'];
            $grouped[$date][] = $atd;
        }
        // urutan tanggal
        ksort($grouped);
        return $grouped;
    }

    public function printPdfAttendanceByDepartment($start, $end, $dept)
    {
        $rptraName = session()->get('rptra_name') ?: 'tidak ditemukan';
        // Panggil fungsi baru untuk mendapatkan data lengkap
        $attendance = $this->attendanceModel->getAttendanceReportData($start, $end, $dept);
        $department = $this->departmentModel->getDepartmentById($dept);
        $data = [
            'start' => $start,
            'end' => $end,
            'dept' => $dept,
            'rptra_name' => $rptraName,
            'dept_name' => $department['department_name'] ?? 'Semua Department',
            'attendance' => $this->groupAttendanceByDate($attendance),
        ];

        $html = view('admin/report/print_attendance_all', $data);

        while (ob_get_level() > 0) {
            ob_end_clean();
        }

        $mpdf = new Mpdf([
            'mode' => 'utf-8',
            'format' => 'A4-L',
            'default_font_size' => 10,
            'default_font' => 'Arial'
        ]);
        $mpdf->SetHeader('RPTRA ' . $rptraName);
        $mpdf->SetFooter('Dicetak pada: {DATE j-m-Y H:i:s}');
        $mpdf->WriteHTML($html);
        $mpdf->Output('Laporan_Kehadiran_Seluruh_Pengelola.pdf', 'I');
    }

    public function printExcelAttendanceByDepartment($start, $end, $dept)
    {
        $rptraName = session()->get('rptra_name') ?: 'tidak ditemukan';
        $attendanceData = $this->attendanceModel->getAttendanceReportData($start, $end, $dept);
        $department = $this->departmentModel->getDepartmentById($dept);
        $startDate = new \DateTime($start);
        $endDate = new \DateTime($end);

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Header Laporan
        $sheet->mergeCells('A1:G1');
        $sheet->setCellValue('A1', 'LAPORAN KEHADIRAN SELURUH ' . strtoupper(($department['department_name'])) . ' ' . strtoupper($rptraName));
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
        $sheet->getStyle('A1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        $sheet->mergeCells('A2:G2');
        $dateRange = 'Periode: ' . $startDate->format('d M Y') . ' - ' . $endDate->format('d M Y');
        $sheet->setCellValue('A2', $dateRange);
        $sheet->getStyle('A2')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        // Header Tabel
        $sheet->setCellValue('A5', 'No');
        $sheet->setCellValue('B5', 'Tanggal');
        $sheet->setCellValue('C5', 'Nama');
        $sheet->setCellValue('D5', 'Shift');
        $sheet->setCellValue('E5', 'Check In');
        $sheet->setCellValue('F5', 'Status Masuk');
        $sheet->setCellValue('G5', 'Check Out');

        // Styling Header Tabel
        $headerStyle = [
            'font' => ['bold' => true, 'color' => ['argb' => 'FFFFFFFF']],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FF4F81BD']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]]
        ];
        $sheet->getStyle('A5:G5')->applyFromArray($headerStyle);

        // Isi Tabel
        $row = 6;
        $no = 1;
        $groupedAttendance = $this->groupAttendanceByDate($attendanceData);

        foreach ($groupedAttendance as $date => $attendances) {
            foreach ($attendances as $atd) {
                $sheet->setCellValue('A' . $row, $no++);
                $sheet->setCellValue('B' . $row, \PhpOffice\PhpSpreadsheet\Shared\Date::PHPToExcel($atd['attendance_date']));
                $sheet->getStyle('B' . $row)->getNumberFormat()->setFormatCode('DD-MM-YYYY');
                $sheet->setCellValue('C' . $row, $atd['employee_name']);

                $status = $atd['presence_status'];

                switch ($status) {
                    case '1': // Hadir
                        $sheet->setCellValue('D' . $row, (!empty($atd['shift_id'])) ? $atd['shift_id'] . " (" . substr($atd['shift_start'], 0, 5) . "-" . substr($atd['shift_end'], 0, 5) . ")" : 'N/A');
                        $sheet->setCellValue('E' . $row, $atd['in_time'] ? substr($atd['in_time'], 0, 5) : '-');
                        $sheet->setCellValue('F' . $row, $atd['in_status']);
                        $sheet->setCellValue('G' . $row, $atd['out_time'] ? substr($atd['out_time'], 0, 5) : '-');
                        break;

                    case '2': // Izin
                    case '3': // Sakit
                    case '4': // Cuti
                    case '5': // Libur
                    case '0': // Tidak Hadir
                        // Gabungkan kolom D sampai G
                        $sheet->mergeCells('D' . $row . ':G' . $row);
                        $sheet->setCellValue('D' . $row, $atd['presence_status_text']);
                        $sheet->getStyle('D' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                        break;

                    default: // Tidak Ada Data
                        $sheet->mergeCells('C' . $row . ':G' . $row);
                        $sheet->setCellValue('C' . $row, $atd['presence_status_text']);
                        $sheet->getStyle('C' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                        $sheet->setCellValue('B' . $row, \PhpOffice\PhpSpreadsheet\Shared\Date::PHPToExcel($atd['attendance_date'])); // Tetap tampilkan tanggal
                        break;
                }
                $row++;
            }
        }

        // Styling Body Tabel
        $bodyStyle = [
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
            'alignment' => ['vertical' => Alignment::VERTICAL_CENTER]
        ];
        $sheet->getStyle('A6:G' . ($row - 1))->applyFromArray($bodyStyle);
        // Center alignment untuk kolom tertentu
        $sheet->getStyle('A6:B' . ($row - 1))->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('E6:G' . ($row - 1))->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        // Atur lebar kolom otomatis
        foreach (range('A', 'G') as $columnID) {
            $sheet->getColumnDimension($columnID)->setAutoSize(true);
        }

        $filename = 'Laporan_Kehadiran_' . str_replace('-', '', $start) . '_' . str_replace('-', '', $end) . '.xlsx';
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');

        $writer = new Xlsx($spreadsheet);
        $writer->save('php://output');
        exit;
    }

    private function prepareAttendanceHistoryData($employee_id, $month, $year)
    {
        $daysInMonth = cal_days_in_month(CAL_GREGORIAN, $month, $year);
        $currentDate = new \DateTime();

        // Mengambil semua data absensi yang relevan dalam satu query untuk efisiensi
        $startDate = sprintf('%04d-%02d-01', $year, $month);
        $endDate = sprintf('%04d-%02d-%02d', $year, $month, $daysInMonth);
        $attendanceData = $this->attendanceModel->getAttendanceByEmployeeId($startDate, $endDate, $employee_id);

        // Mengindeks data absensi berdasarkan tanggal untuk pencarian cepat
        $indexedAttendance = [];
        foreach ($attendanceData as $att) {
            $indexedAttendance[$att['attendance_date']] = $att;
        }

        $reportData = [];
        for ($day = 1; $day <= $daysInMonth; $day++) {
            $date = sprintf('%04d-%02d-%02d', $year, $month, $day);
            $dateObj = new \DateTime($date);
            $dailyRecord = null;

            if (isset($indexedAttendance[$date])) {
                $dailyRecord = $indexedAttendance[$date];
            } else {
                $defaultStatus = 0; // Tidak Hadir
                if ($dateObj > $currentDate) {
                    $defaultStatus = null; // Tidak Ada Data
                }

                $dailyRecord = [
                    'attendance_date' => $date,
                    'presence_status' => $defaultStatus,
                    'shift_id' => null,
                    'shift_start' => null,
                    'shift_end' => null,
                    'in_time' => null,
                    'in_status' => null,
                    'out_time' => null
                ];
            }
            $reportData[] = $dailyRecord;
        }

        return $reportData;
    }

    // ---------------section----------
    public function printPdfAttendanceHistory($employee_id)
    {
        $employee = $this->employeeModel->find($employee_id);
        if (!$employee) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('Employee not found');
        }

        $rptraName = session()->get('rptra_name') ?: 'tidak ditemukan';
        $month = (int) ($this->request->getGet('month') ?: date('m'));
        $year = (int) ($this->request->getGet('year') ?: date('Y'));

        // Menggunakan fungsi helper untuk menyiapkan data
        $attendance = $this->prepareAttendanceHistoryData($employee_id, $month, $year);

        $department = $this->departmentModel->find($employee['department_id']);
        $dept_name = $department['department_name'] ?? 'Departemen Tidak Ditemukan';

        $data = [
            'employee' => $employee,
            'attendance' => $attendance,
            'month' => $month,
            'year' => $year,
            'dept_name' => $dept_name,
        ];

        $html = view('admin/report/print_attendance_employee', $data);

        while (ob_get_level() > 0) {
            ob_end_clean();
        }

        $pdf = new Mpdf([
            'mode' => 'utf-8',
            'format' => 'A4',
            'default_font_size' => 10,
            'default_font' => 'Arial'
        ]);
        $pdf->SetHeader('RPTRA ' . $rptraName);
        $pdf->SetFooter('Dicetak pada: {DATE j-m-Y H:i:s}');
        $pdf->WriteHTML($html);

        $filename = "Riwayat_Presensi_" . preg_replace('/[^a-zA-Z0-9_\-]/', '_', $employee['employee_name']) . "_{$month}_{$year}.pdf";

        $pdf->Output($filename, 'I');
        exit;
    }

    public function printExcelAttendanceHistory($employee_id)
    {
        $employee = $this->employeeModel->find($employee_id);
        if (!$employee) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('Employee not found');
        }
        $rptraName = session()->get('rptra_name') ?: 'tidak ditemukan';

        $month = (int) ($this->request->getGet('month') ?: date('m'));
        $year = (int) ($this->request->getGet('year') ?: date('Y'));

        $attendance = $this->prepareAttendanceHistoryData($employee_id, $month, $year);

        $department = $this->departmentModel->find($employee['department_id']);
        $dept_name = $department['department_name'] ?? 'Departemen Tidak Ditemukan';

        $bulanIndonesia = [
            1 => 'Januari',
            2 => 'Februari',
            3 => 'Maret',
            4 => 'April',
            5 => 'Mei',
            6 => 'Juni',
            7 => 'Juli',
            8 => 'Agustus',
            9 => 'September',
            10 => 'Oktober',
            11 => 'November',
            12 => 'Desember',
        ];

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // --- Header Laporan ---
        $sheet->mergeCells('A1:F1');
        $sheet->setCellValue('A1', 'Daftar Hadir ' . $dept_name . $rptraName);
        $sheet->mergeCells('A2:F2');
        $sheet->setCellValue('A2', 'Nama: ' . $employee['employee_name']);
        $sheet->mergeCells('A3:F3');
        $sheet->setCellValue('A3', 'Periode: ' . $bulanIndonesia[$month] . " " . $year);

        // --- Header Tabel ---
        $sheet->setCellValue('A6', 'No');
        $sheet->setCellValue('B6', 'Tanggal');
        $sheet->setCellValue('C6', 'Shift');
        $sheet->setCellValue('D6', 'Check In');
        $sheet->setCellValue('E6', 'Status Masuk');
        $sheet->setCellValue('F6', 'Check Out');

        // --- Styling Header ---
        $headerStyle = [
            'font' => ['bold' => true, 'color' => ['argb' => 'FFFFFFFF']],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FF007BFF']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]]
        ];
        $sheet->getStyle('A6:F6')->applyFromArray($headerStyle);

        // --- Isi Tabel ---
        $row = 7;
        $no = 1;
        $statusMap = [
            1 => 'Hadir',
            0 => 'Tidak Hadir',
            2 => 'Izin',
            3 => 'Sakit',
            4 => 'Cuti',
            5 => 'Libur',
            null => 'Tidak Ada Data'
        ];

        foreach ($attendance as $att) {
            $sheet->setCellValue('A' . $row, $no++);
            $sheet->setCellValue('B' . $row, date('d-m-Y', strtotime($att['attendance_date'])));

            $presence_status = $att['presence_status'];

            if ($presence_status == 1) { // Hadir
                $shiftText = "Tidak ada shift";
                if (!empty($att['shift_id']) && !empty($att['shift_start']) && !empty($att['shift_end'])) {
                    $shiftText = htmlspecialchars($att['shift_id']) . " (" . date('H:i', strtotime($att['shift_start'])) . " - " . date('H:i', strtotime($att['shift_end'])) . ")";
                }

                $checkout_status = 'Belum check out';
                if (function_exists('get_checkout_status')) {
                    $checkout_status = get_checkout_status($att, ['start_time' => $att['shift_start'], 'end_time' => $att['shift_end']], $att['attendance_date']);
                } elseif (!empty($att['out_time'])) {
                    $checkout_status = date('H:i:s', strtotime($att['out_time']));
                }

                $sheet->setCellValue('C' . $row, $shiftText);
                $sheet->setCellValue('D' . $row, $att['in_time'] ? date('H:i:s', strtotime($att['in_time'])) : '-');
                $sheet->setCellValue('E' . $row, $att['in_status'] ?? '-');
                $sheet->setCellValue('F' . $row, $checkout_status);
            } else {
                $sheet->mergeCells('C' . $row . ':F' . $row);
                $statusText = $statusMap[$presence_status];
                $sheet->setCellValue('C' . $row, $statusText);
                $sheet->getStyle('C' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            }
            $row++;
        }

        // --- Styling Body ---
        $bodyStyle = [
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]]
        ];
        $sheet->getStyle('A7:F' . ($row - 1))->applyFromArray($bodyStyle);
        $sheet->getStyle('C7:C' . ($row - 1))->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);

        foreach (range('A', 'F') as $columnID) {
            $sheet->getColumnDimension($columnID)->setAutoSize(true);
        }
        $sheet->getStyle('A1:F1')->getFont()->setBold(true)->setSize(14);
        $sheet->getStyle('A1:F4')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        $filename = "Riwayat_Presensi_" . preg_replace('/[^a-zA-Z0-9_\-]/', '_', $employee['employee_name']) . "_{$month}_{$year}.xlsx";

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');

        $writer = new Xlsx($spreadsheet);
        $writer->save('php://output');
        exit;
    }

    // -------------section----------
    private function getIndonesianMonthName($month, $year)
    {
        $formatter = new IntlDateFormatter(
            'id_ID',
            IntlDateFormatter::LONG,
            IntlDateFormatter::NONE,
            'Asia/Jakarta',
            IntlDateFormatter::GREGORIAN,
            'MMMM'
        );

        return $formatter->format(mktime(0, 0, 0, $month, 10, $year));
    }

    public function printWorkSchedulePdf($employeeId)
    {
        $rptraName = session()->get('rptra_name') ?: 'tidak ditemukan';
        $month = (int) ($this->request->getGet('month') ?: date('m'));
        $year = (int) ($this->request->getGet('year') ?: date('Y'));

        // Mengambil data pegawai
        $employee = $this->employeeModel->find($employeeId);
        if (!$employee) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException("Pegawai tidak ditemukan");
        }

        // Mengambil data departemen
        $department = $this->departmentModel->find($employee['department_id']);
        if (!$department) {
            $department = [
                'department_id' => 'Not assigned',
                'department_name' => 'Department not assigned'
            ];
        }

        // Mengambil jadwal kerja
        $workSchedules = $this->workScheduleModel->getWorkSchedulesByEmployeeAndMonth(
            $employeeId,
            $month,
            $year
        );

        $monthName = $this->getIndonesianMonthName($month, $year);

        $data = [
            'department_name' => $department['department_name'],
            'employee_name' => $employee['employee_name'],
            'month_name' => $monthName,
            'month_number' => $month,
            'year' => $year,
            'workSchedules' => $workSchedules,
        ];

        $html = view('admin/report/print_work_schedule', $data);

        while (ob_get_level() > 0) {
            ob_end_clean();
        }

        try {
            $mpdf = new \Mpdf\Mpdf([
                'format' => 'A4',
                'orientation' => 'portrait',
                'margin_left' => 15,
                'margin_right' => 15,
                'margin_top' => 16,
                'margin_bottom' => 16,
                'margin_header' => 9,
                'margin_footer' => 9,
            ]);

            $mpdf->SetHeader('RPTRA ' . $rptraName);
            $mpdf->SetFooter('Dicetak pada: {DATE j-m-Y H:i:s}');
            $mpdf->WriteHTML($html);
            $filename = "Jadwal_Kerja_{$employee['employee_name']}_{$month}_{$year}.pdf";

            $mpdf->Output($filename, 'I');
        } catch (\Mpdf\MpdfException $e) {
            log_message('error', 'mPDF Error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Terjadi kesalahan saat menghasilkan PDF.');
        }
    }

    public function printWorkScheduleExcel($employeeId)
    {
        $month = (int) ($this->request->getGet('month') ?: date('m'));
        $year = (int) ($this->request->getGet('year') ?: date('Y'));

        $employee = $this->employeeModel->find($employeeId);
        if (!$employee) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException("Pegawai tidak ditemukan");
        }
        $rptraName = session()->get('rptra_name') ?: 'tidak ditemukan';

        $department = $this->departmentModel->find($employee['department_id']);
        if (!$department) {
            $department = [
                'department_id' => 'Not assigned',
                'department_name' => 'Department not assigned'
            ];
        }

        $workSchedules = $this->workScheduleModel->getWorkSchedulesByEmployeeAndMonth(
            $employeeId,
            $month,
            $year
        );
        $monthName = $this->getIndonesianMonthName($month, $year);
        $data = [
            'department_name' => $department['department_name'],
            'employee_name' => $employee['employee_name'],
            'month_name' => $monthName,
            'month_number' => $month,
            'year' => $year,
            'workSchedules' => $workSchedules,
        ];
        $indonesianDays = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Mengatur judul
        $sheet->mergeCells('A1:C1');
        $sheet->setCellValue('A1', strtoupper("Jadwal Kerja {$department['department_name']} - {$rptraName}"));
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(16);
        $sheet->getStyle('A1')->getAlignment()->setHorizontal('center');

        // Mengatur informasi pegawai
        $sheet->setCellValue('A3', 'Nama');
        $sheet->setCellValue('B3', $employee['employee_name']);
        $sheet->setCellValue('A4', 'Bulan');
        $sheet->setCellValue('B4', "{$data['month_name']} {$year}");

        // Mengatur header tabel
        $sheet->setCellValue('A6', 'Hari');
        $sheet->setCellValue('B6', 'Tanggal');
        $sheet->setCellValue('C6', 'Shift Kerja');
        $sheet->getStyle("A6:C6")->getFont()->setBold(true);
        $row = 7;

        for ($day = 1; $day <= cal_days_in_month(CAL_GREGORIAN, $month, $year); $day++) {
            $currentDate = sprintf('%04d-%02d-%02d', $year, $month, $day);
            $timestamp = strtotime($currentDate);
            $dayName = $indonesianDays[date('w', $timestamp)];
            $formattedDate = date('d-m-Y', $timestamp);

            if (isset($workSchedules[$day]) && $workSchedules[$day] !== null) {
                $schedule = $workSchedules[$day];
                switch ($schedule['schedule_status']) {
                    case 4:
                        $shiftTime = 'Cuti';
                        $fillColor = '000000'; // Hitam
                        $fontColor = 'FFFFFF'; // Putih untuk kontras
                        break;
                    case 5:
                        $shiftTime = 'Libur';
                        $fillColor = '0000FF'; // Biru
                        $fontColor = 'FFFFFF'; // Putih untuk kontras
                        break;
                    default:
                        if (!empty($schedule['start_time']) && !empty($schedule['end_time'])) {
                            // Format waktu menjadi HH:MM
                            $formattedStartTime = date('H:i', strtotime($schedule['start_time']));
                            $formattedEndTime = date('H:i', strtotime($schedule['end_time']));
                            $shiftTime = "{$formattedStartTime} - {$formattedEndTime}";
                            $fillColor = '00FF00';
                            $fontColor = '000000';
                        } else {
                            $shiftTime = 'Tidak ada jadwal';
                            $fillColor = 'FFFFFF';
                            $fontColor = '000000';
                        }
                        break;
                }
            } else {
                $shiftTime = 'Tidak ada jadwal';
                $fillColor = 'FFFFFF';
                $fontColor = '000000';
            }

            $sheet->setCellValue("A{$row}", $dayName);
            $sheet->setCellValue("B{$row}", $formattedDate);
            $sheet->setCellValue("C{$row}", $shiftTime);

            $sheet->getStyle("C{$row}")->getFill()->setFillType(Fill::FILL_SOLID)
                ->getStartColor()->setRGB($fillColor);
            $sheet->getStyle("C{$row}")->getFont()->getColor()->setRGB($fontColor);

            $row++;
        }

        $sheet->getStyle("A6:C{$row}")->getBorders()->getAllBorders()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);

        foreach (range('A', 'C') as $columnID) {
            $sheet->getColumnDimension($columnID)->setAutoSize(true);
        }

        $writer = new Xlsx($spreadsheet);
        $filename = "Jadwal_Kerja_{$employee['employee_name']}_{$month}_{$year}.xlsx";

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header("Content-Disposition: attachment; filename=\"{$filename}\"");
        header('Cache-Control: max-age=0');
        $writer->save('php://output');
        exit;
    }

    public function printBiodataPdf($id)
    {
        $employee = $this->employeeModel->findEmployeeWithRelations($id);
        if (!$employee) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException("Pegawai tidak ditemukan");
        }
        $rptraName = session()->get('rptra_name') ?: 'tidak ditemukan';
        $department = $this->departmentModel->find($employee['department_id']);
        $departmentName = $department['department_name'] ?? 'Tidak Diketahui';

        $data = [
            'employee' => $employee,
            'department_current' => [
                'department_id' => $employee['department_id'] ?? null,
                'department_name' => $departmentName
            ],
        ];

        $html = view('admin/report/print_biodata', $data);

        while (ob_get_level() > 0) {
            ob_end_clean();
        }

        $mpdf = new Mpdf([
            'format' => 'A4',
            'orientation' => 'P',
        ]);

        $mpdf->SetHeader('RPTRA ' . $rptraName);
        $mpdf->SetFooter('Dicetak pada: {DATE j-m-Y H:i:s}');

        $mpdf->SetTitle('Biodata ' . $departmentName);
        $mpdf->WriteHTML($html);

        $mpdf->Output('Biodata_' . $employee['employee_name'] . '.pdf', 'I');
    }
}
