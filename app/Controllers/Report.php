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
use Intervention\Image\ImageManagerStatic as Image;
use PhpOffice\PhpSpreadsheet\Style\Fill;
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

        $month = (int) ($this->request->getGet('month') ?: date('m'));
        $year = (int) ($this->request->getGet('year') ?: date('Y'));
        $daysInMonth = cal_days_in_month(CAL_GREGORIAN, $month, $year);
        $currentDate = new \DateTime(); // Current date

        $attendanceData = $this->attendanceModel->getAttendanceByEmployeeAndDate($employee_id, $month, $year);

        // Initialize attendance array
        $attendance = [];
        for ($day = 1; $day <= $daysInMonth; $day++) {
            $date = sprintf('%04d-%02d-%02d', $year, $month, $day);
            $dateObj = new \DateTime($date);

            if ($dateObj <= $currentDate) {
                $attendance[$date] = 'Tidak Hadir'; // Default status
            } else {
                $attendance[$date] = 'Tidak Ada Data';
            }
        }

        // Update attendance based on actual data only for dates <= today
        foreach ($attendanceData as $att) {
            $date = $att['date'];
            $presence_status = $att['presence_status'];
            $dateObj = new \DateTime($date);

            if ($dateObj <= $currentDate) {
                $statusMap = [
                    1 => 'Hadir',
                    0 => 'Tidak Hadir',
                    2 => 'Izin',
                    3 => 'Sakit',
                    4 => 'Cuti',
                    5 => 'Libur',
                ];
                $attendance[$date] = $statusMap[$presence_status] ?? 'Tidak Ada Data';
            }
            // Jika tanggal > today, status tetap 'Tidak Ada Data'
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

        $html = view('admin/report/print_attendance_employee', $data);

        // Bersihkan output buffering
        while (ob_get_level() > 0) {
            ob_end_clean();
        }

        $pdf = new Mpdf();
        $pdf->SetHeader('RPTRA Cibubur Berseri');
        $pdf->SetFooter('Dicetak pada: {DATE j-m-Y H:i:s}');
        $pdf->WriteHTML($html);

        // Nama file
        $filename = "Riwayat_Presensi_" . preg_replace('/[^a-zA-Z0-9_\-]/', '_', $employee['employee_name']) . "_{$month}_{$year}.pdf";

        // Output PDF ke browser
        $pdf->Output($filename, 'I');
        exit;
    }

    public function printExcelAttendanceHistory($employee_id)
    {
        $db = \Config\Database::connect();
        $employee = $db->table('employee')->where('employee_id', $employee_id)->get()->getRowArray();

        if (!$employee) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('Employee not found');
        }

        $month = (int) ($this->request->getGet('month') ?: date('m'));
        $year = (int) ($this->request->getGet('year') ?: date('Y'));

        $daysInMonth = cal_days_in_month(CAL_GREGORIAN, $month, $year);
        $currentDate = new \DateTime(); // Current date

        $attendanceData = $this->attendanceModel->getAttendanceByEmployeeAndDate($employee_id, $month, $year);

        // Initialize attendance array
        $attendance = [];
        for ($day = 1; $day <= $daysInMonth; $day++) {
            $date = sprintf('%04d-%02d-%02d', $year, $month, $day);
            $dateObj = new \DateTime($date);

            if ($dateObj <= $currentDate) {
                $attendance[$date] = 'Tidak Hadir'; // Default status
            } else {
                $attendance[$date] = 'Tidak Ada Data';
            }
        }

        // Update attendance based on actual data only for dates <= today
        foreach ($attendanceData as $att) {
            $date = $att['date'];
            $presence_status = $att['presence_status'];
            $dateObj = new \DateTime($date);

            if ($dateObj <= $currentDate) {
                switch ($presence_status) {
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
            // Jika tanggal > today, status tetap 'Tidak Ada Data'
        }

        // Ambil department_id dari employee dan kemudian department_name
        $department = $this->departmentModel->getDepartmentById($employee['department_id']);
        $dept_name = $department['department_name'] ?? 'Departemen';

        // Array bulan dalam Bahasa Indonesia
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

        // Header laporan
        $sheet->setCellValue('A1', 'Riwayat Presensi ' . ($dept_name ?? 'Departemen'));
        $sheet->setCellValue('A2', 'Nama : ' . $employee['employee_name']);
        $sheet->setCellValue('A3', 'Bulan : ' . $bulanIndonesia[$month] . " $year");

        // Header tabel
        $sheet->setCellValue('A5', 'Hari');
        $sheet->setCellValue('B5', 'Tanggal');
        $sheet->setCellValue('C5', 'Status Presensi');

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
        $sheet->getStyle('A5:C5')->applyFromArray($headerStyleArray);

        // Isi data
        $row = 6;
        foreach ($attendance as $date => $status) {
            $dateObj = new \DateTime($date);
            $formatter = new \IntlDateFormatter('id_ID', \IntlDateFormatter::FULL, \IntlDateFormatter::NONE);
            $formatter->setPattern('EEEE'); // Format: Nama Hari
            $hari = $formatter->format($dateObj);

            // Format tanggal: dd-MM-yyyy
            $tanggal = $dateObj->format('d-m-Y');

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
            $sheet->setCellValue('A' . $row, $hari);
            $sheet->setCellValue('B' . $row, $tanggal);
            $sheet->setCellValue('C' . $row, $status);

            // Terapkan warna latar belakang sel berdasarkan status
            $sheet->getStyle('C' . $row)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                ->getStartColor()->setARGB($badgeColor);

            // Terapkan warna teks putih untuk kontras
            $sheet->getStyle('C' . $row)->getFont()->getColor()->setARGB('FFFFFFFF');

            // Terapkan alignment center
            $sheet->getStyle('A' . $row . ':C' . $row)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

            // Menambahkan border
            $sheet->getStyle('A' . $row . ':C' . $row)->getBorders()->getAllBorders()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);

            $row++;
        }

        // Menyesuaikan lebar kolom secara otomatis
        foreach (range('A', 'C') as $columnID) {
            $sheet->getColumnDimension($columnID)->setAutoSize(true);
        }

        // Mengatur style header dan footer
        $sheet->getStyle('A1:C1')->getFont()->setBold(true)->setSize(14);
        $sheet->getStyle('A2:C2')->getFont()->setBold(true);
        $sheet->getStyle('A3:C3')->getFont()->setBold(true);

        // Membuat filename dengan format yang diinginkan
        $filename = "Riwayat_Presensi_" . preg_replace('/[^a-zA-Z0-9_\-]/', '_', $employee['employee_name']) . "_{$month}_{$year}.xlsx";

        // Mengirimkan file ke browser untuk diunduh
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');

        // Menulis file ke output
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

        return $formatter->format(mktime(0, 0, 0, $month, 10, $year)); // Contoh: "Januari"
    }

    public function printWorkSchedulePdf($employeeId)
    {
        // Mengambil parameter bulan dan tahun dari query string
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

        // Mendapatkan nama bulan dalam Bahasa Indonesia
        $monthName = $this->getIndonesianMonthName($month, $year);

        // Menyiapkan data untuk view
        $data = [
            'department_name' => $department['department_name'],
            'employee_name' => $employee['employee_name'],
            'month_name' => $monthName, // Nama bulan dalam Bahasa Indonesia
            'month_number' => $month, // Angka bulan
            'year' => $year,
            'workSchedules' => $workSchedules,
        ];

        // Render view ke HTML
        $html = view('admin/report/print_work_schedule', $data);

        // Bersihkan output buffering untuk menghindari output yang tidak diinginkan
        while (ob_get_level() > 0) {
            ob_end_clean();
        }

        try {
            // Inisialisasi mPDF
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

            // Menambahkan header dan footer
            $mpdf->SetHeader('RPTRA Cibubur Berseri');
            $mpdf->SetFooter('Dicetak pada: {DATE j-m-Y H:i:s}');

            // Menulis HTML ke PDF
            $mpdf->WriteHTML($html);

            // Menentukan nama file
            $filename = "Jadwal_Kerja_{$employee['employee_name']}_{$month}_{$year}.pdf";

            // Output PDF ke browser
            $mpdf->Output($filename, 'I'); // 'I' untuk menampilkan di browser, 'D' untuk download
        } catch (\Mpdf\MpdfException $e) {
            // Handle exception jika mPDF gagal
            log_message('error', 'mPDF Error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Terjadi kesalahan saat menghasilkan PDF.');
        }
    }

    public function printWorkScheduleExcel($employeeId)
    {
        // Mengambil parameter bulan dan tahun dari query string
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

        // Mendapatkan nama bulan dalam Bahasa Indonesia
        $monthName = $this->getIndonesianMonthName($month, $year);

        // Menyiapkan data untuk view
        $data = [
            'department_name' => $department['department_name'],
            'employee_name' => $employee['employee_name'],
            'month_name' => $monthName, // Nama bulan dalam Bahasa Indonesia
            'month_number' => $month, // Angka bulan
            'year' => $year,
            'workSchedules' => $workSchedules,
        ];

        // Mendefinisikan hari dalam Bahasa Indonesia
        $indonesianDays = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];

        // Membuat spreadsheet baru
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Mengatur judul
        $sheet->mergeCells('A1:C1');
        $sheet->setCellValue('A1', "Jadwal Kerja {$department['department_name']}");
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

        // Mengatur header tabel dengan font tebal
        $sheet->getStyle("A6:C6")->getFont()->setBold(true);

        // Menambahkan data jadwal
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
                            $fillColor = '00FF00'; // Hijau
                            $fontColor = '000000'; // Hitam
                        } else {
                            $shiftTime = 'Tidak ada jadwal';
                            $fillColor = 'FFFFFF'; // Putih (tanpa warna)
                            $fontColor = '000000'; // Hitam
                        }
                        break;
                }
            } else {
                $shiftTime = 'Tidak ada jadwal';
                $fillColor = 'FFFFFF'; // Putih (tanpa warna)
                $fontColor = '000000'; // Hitam
            }

            // Menetapkan nilai sel
            $sheet->setCellValue("A{$row}", $dayName);
            $sheet->setCellValue("B{$row}", $formattedDate);
            $sheet->setCellValue("C{$row}", $shiftTime);

            // Mengatur warna latar belakang dan warna font berdasarkan status
            $sheet->getStyle("C{$row}")->getFill()->setFillType(Fill::FILL_SOLID)
                ->getStartColor()->setRGB($fillColor);
            $sheet->getStyle("C{$row}")->getFont()->getColor()->setRGB($fontColor);

            $row++;
        }

        // Mengatur border untuk seluruh tabel
        $sheet->getStyle("A6:C{$row}")->getBorders()->getAllBorders()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);

        // Mengatur lebar kolom otomatis
        foreach (range('A', 'C') as $columnID) {
            $sheet->getColumnDimension($columnID)->setAutoSize(true);
        }

        // Membuat writer dan output ke browser
        $writer = new Xlsx($spreadsheet);
        $filename = "Jadwal_Kerja_{$employee['employee_name']}_{$month}_{$year}.xlsx";

        // Header untuk download
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header("Content-Disposition: attachment; filename=\"{$filename}\"");
        header('Cache-Control: max-age=0');

        // Menulis file ke output
        $writer->save('php://output');
        exit;
    }

    public function printBiodataPdf($id)
    {
        $employee = $this->employeeModel->findEmployeeWithRelations($id);
        if (!$employee) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException("Pegawai tidak ditemukan");
        }

        // Mengambil nama department dari model Department
        $department = $this->departmentModel->find($employee['department_id']);
        $departmentName = $department['department_name'] ?? 'Tidak Diketahui';

        $data = [
            'employee' => $employee,
            'department_current' => [
                'department_id' => $employee['department_id'] ?? null,
                'department_name' => $departmentName
            ],
        ];

        // Render view untuk PDF
        $html = view('admin/report/print_biodata', $data);

         // Bersihkan output buffering untuk menghindari output yang tidak diinginkan
         while (ob_get_level() > 0) {
            ob_end_clean();
        }

        // Inisialisasi mPDF
        $mpdf = new Mpdf([
            'format' => 'A4',
            'orientation' => 'P',
        ]);

        // Menambahkan header dan footer
        $mpdf->SetHeader('RPTRA Cibubur Berseri');
        $mpdf->SetFooter('Dicetak pada: {DATE j-m-Y H:i:s}');

        // Set judul PDF
        $mpdf->SetTitle('Biodata ' . $departmentName);

        // Menulis HTML ke PDF
        $mpdf->WriteHTML($html);

        // Output ke browser
        $mpdf->Output('Biodata_' . $employee['employee_name'] . '.pdf', 'I');
    }
}
