<?php
if (!function_exists('get_checkout_status')) {
    function get_checkout_status($attendance, $shift, $date)
    {
        // Cek apakah out_time sudah diisi
        if (empty($attendance['out_time'])) {
            // Konversi waktu end_shift dan waktu saat ini
            $shift_end_datetime = strtotime($date . ' ' . $shift['end_time']);
            $current_time = time();

            // Mendapatkan tanggal saat ini tanpa waktu
            $today_date = strtotime(date('Y-m-d'));

            // Mendapatkan tanggal shift
            $shift_date = strtotime($date);

            // Kondisi 1: Shift masih berlangsung
            if ($shift_end_datetime > $current_time) {
                return 'Shift Belum Selesai';
            }

            // Kondisi 2: Shift sudah selesai pada hari sebelumnya dan belum check-out
            if ($shift_date < $today_date && $shift_end_datetime <= $current_time) {
                return '-';
            }

            // Kondisi 3: Shift sudah selesai hari ini tetapi belum check-out
            if ($shift_date == $today_date && $shift_end_datetime <= $current_time) {
                return 'Belum Presensi Keluar';
            }

            // Default: Shift sudah selesai dan belum check-out
            return 'Belum Presensi Keluar';
        } else {
            // Jika out_time sudah diisi, tampilkan waktu check-out
            return date('H:i:s', strtotime($attendance['out_time']));
        }
    }
}
