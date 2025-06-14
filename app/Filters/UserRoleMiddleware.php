<?php

namespace App\Filters;

use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\Filters\FilterInterface;

class UserRoleMiddleware implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        $session = session();
        $userRoleId = $session->get('user_role_id');
        $currentPath = trim($request->getUri()->getPath(), '/'); // Path tanpa leading/trailing slashes    

        if (!$userRoleId) {
            return redirect()->to('auth/login');
        }

        // Define regex patterns for accessible views
        $adminPaths = [
            'admin/dashboard',
            'admin/master/department',
            'admin/master/department/add',
            'admin/master/department/edit/.*', // Support dynamic ID
            'admin/master/department/delete/.*', // Support dynamic ID
            'admin/master/employee',
            'admin/master/employee/add',
            'admin/master/employee/edit/.*',
            'admin/master/employee/detail/.*',
            'admin/master/employee/delete/.*',
            'admin/master/employee/attendance/.*',
            'admin/master/employee/attendance',
            'admin/master/employee/attendance/edit/.*',   
            'admin/master/employee/work_schedule/.*', 
            'admin/master/employee/work_schedule/add', 
            'admin/master/employee/work_schedule/edit/.*', 
            'admin/master/shift',
            'admin/master/shift/add',
            'admin/master/shift/edit/.*',
            'admin/master/shift/delete/.*',
            'admin/master/user_account',
            'admin/master/user_account/add/.*',
            'admin/master/user_account/edit/.*',
            'admin/master/user_account/delete/.*',
            'admin/report',
            'admin/report/print_attendance_employee/pdf/.*',
            'admin/report/print_attendance_employee/excel/.*',
            'admin/report/print_attendance_all/pdf/.*',
            'admin/report/print_attendance_all/excel/.*',
            'admin/report/print_work_schedule/pdf/.*',
            'admin/report/print_work_schedule/excel/.*',
            'admin/report/print_biodata/pdf/.*',
        ];

        $employeePaths = [
            'employee/attendance',
            'employee/profile',
            'employee/profile/edit',
            'employee/change_password',
            'employee/attendance_history',
            'employee/work_schedule',
            'employee/get_location',
        ];

        $publicPaths = [
            'auth/login',
            'auth/blocked',
        ];

        // Check admin paths
        if ($userRoleId == 1) {
            foreach ($adminPaths as $pattern) {
                if (preg_match("#^{$pattern}$#", $currentPath)) {
                    return;
                }
            }
        }

        // Check employee paths
        if ($userRoleId == 2) {
            foreach ($employeePaths as $pattern) {
                if (preg_match("#^{$pattern}$#", $currentPath)) {
                    return;
                }
            }
        }

        // Allow public paths without restriction
        foreach ($publicPaths as $pattern) {
            if (preg_match("#^{$pattern}$#", $currentPath)) {
                return;
            }
        }

        // Default redirect for unauthorized access
        return redirect()->to('auth/blocked');
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // No post-processing required
    }
}
