<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */

$routes->get('/', 'Auth::index');
$routes->group('auth', function ($routes) {
    $routes->get('login', 'Auth::index');
    $routes->post('login', 'Auth::login');
    $routes->get('logout', 'Auth::logout');
    $routes->get('blocked', 'Auth::blocked');
});

$routes->group('admin', ['filter' => 'userRole'], function ($routes) {
    $routes->get('dashboard', 'DashboardAdmin::index');

    $routes->get('master/department', 'DepartmentMaster::index');
    $routes->get('master/department/add', 'DepartmentMaster::add');
    $routes->post('master/department/add', 'DepartmentMaster::add');
    $routes->get('master/department/edit/(:segment)', 'DepartmentMaster::edit/$1');
    $routes->post('master/department/edit/(:segment)', 'DepartmentMaster::edit/$1');
    $routes->post('master/department/delete/(:segment)', 'DepartmentMaster::delete/$1');

    $routes->get('master/employee', 'EmployeeMaster::index');
    $routes->get('master/employee/add', 'EmployeeMaster::add');
    $routes->post('master/employee/add', 'EmployeeMaster::add');
    $routes->get('master/employee/edit/(:segment)', 'EmployeeMaster::edit/$1');
    $routes->post('master/employee/edit/(:segment)', 'EmployeeMaster::edit/$1');
    $routes->get('master/employee/detail/(:segment)', 'EmployeeMaster::detail/$1');
    $routes->post('master/employee/delete/(:segment)', 'EmployeeMaster::delete/$1');

    $routes->get('master/employee/attendance/(:segment)', 'EmployeeMaster::attendanceEmployee/$1');
    $routes->get('master/employee/attendance', 'EmployeeMaster::attendanceEmployee');
    $routes->post('master/employee/attendance/edit/(:segment)', 'EmployeeMaster::updateAttendanceEmployee/$1');
    $routes->post('master/employee/attendance/edit', 'EmployeeMaster::updateAttendanceEmployee');

    $routes->get('master/employee/work_schedule/(:segment)', 'EmployeeMaster::workScheduleEmployee/$1');
    $routes->post('master/employee/work_schedule/add', 'EmployeeMaster::storeWorkSchedule');
    $routes->post('master/employee/work_schedule/edit/(:segment)', 'EmployeeMaster::updateWorkSchedule/$1');

    $routes->get('master/shift', 'ShiftMaster::index');
    $routes->get('master/shift/add', 'ShiftMaster::add');
    $routes->post('master/shift/add', 'ShiftMaster::add');
    $routes->get('master/shift/edit/(:segment)', 'ShiftMaster::edit/$1');
    $routes->post('master/shift/edit/(:segment)', 'ShiftMaster::edit/$1');
    $routes->post('master/shift/delete/(:segment)', 'ShiftMaster::delete/$1');

    $routes->get('master/user_account', 'UserAccountMaster::index');
    $routes->get('master/user_account/add/(:segment)', 'UserAccountMaster::add/$1');
    $routes->post('master/user_account/add/(:segment)', 'UserAccountMaster::add/$1');
    $routes->get('master/user_account/edit/(:segment)', 'UserAccountMaster::edit/$1');
    $routes->post('master/user_account/edit/(:segment)', 'UserAccountMaster::edit/$1');
    $routes->post('master/user_account/delete/(:segment)', 'UserAccountMaster::delete/$1');

    $routes->get('report', 'Report::index');
    $routes->get('report/print_attendance_employee/(:segment)', 'Report::index/$1');
    $routes->get('report/print_attendance_employee/pdf/(:segment)', 'Report::printPdfAttendanceHistory/$1');
    $routes->get('report/print_attendance_employee/excel/(:segment)', 'Report::printExcelAttendanceHistory/$1');
    $routes->get('report/print_attendance_all/pdf/(:segment)/(:segment)/(:segment)', 'Report::printPdfAttendanceByDepartment/$1/$2/$3');
    $routes->get('report/print_attendance_all/excel/(:segment)/(:segment)/(:segment)', 'Report::printExcelAttendanceByDepartment/$1/$2/$3');
    $routes->get('report/print_work_schedule/pdf/(:segment)', 'Report::printWorkSchedulePdf/$1');
    $routes->get('report/print_work_schedule/excel/(:segment)', 'Report::printWorkScheduleExcel/$1');
    $routes->get('report/print_biodata/pdf/(:segment)', 'Report::printBiodataPdf/$1');
});

$routes->group('employee', ['filter' => 'userRole'], function ($routes) {
    $routes->get('attendance', 'EmployeeAttendance::index');
    $routes->post('attendance', 'EmployeeAttendance::index');
    $routes->get('profile', 'EmployeeProfile::index');
    $routes->post('profile/edit', 'EmployeeProfile::uploadImage');
    $routes->get('attendance_history', 'EmployeeAttendance::attendanceHistory');
    $routes->get('work_schedule', 'EmployeeAttendance::workSchedule');
    $routes->get('change_password', 'EmployeeChangePassword::index');
    $routes->post('change_password', 'EmployeeChangePassword::update');
    $routes->post('get_location', 'EmployeeGetLocation::getAddress');
});

