<?php

if (!function_exists('get_checkout_status')) {
    function get_checkout_status($atd, $shift, $current_date)
    {
        $shift_start = strtotime($atd['attendance_date'] . ' ' . $shift['start_time']);
        $shift_end = strtotime($atd['attendance_date'] . ' ' . $shift['end_time']);

        // Jika shift end time sudah melewati tengah malam, tambahkan 1 hari
        if (strtotime($shift['end_time']) < strtotime($shift['start_time'])) {
            $shift_end = strtotime('+1 day', $shift_end);
        }

        $report_date = strtotime($atd['attendance_date']);
        $today = strtotime(date('Y-m-d'));
        if ($report_date == $today) {
            $reference_time = time();
        } else {
            $reference_time = $shift_end;
        }

        if ($reference_time < $shift_end) {
            return 'Shift belum selesai';
        } else {
            if (empty($atd['out_time'])) {
                if ($report_date < $today) {
                    return '-';
                } else {
                    return 'Belum presensi keluar';
                }
            } else {
                return $atd['out_time'];
            }
        }
    }
}
