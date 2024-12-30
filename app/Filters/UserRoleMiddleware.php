<?php

namespace App\Filters;

use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\Filters\FilterInterface;

// class UserRoleMiddleware implements FilterInterface
// {
//     public function before(RequestInterface $request, $arguments = null)
//     {
//         $session = session();
//         $userRoleId = $session->get('user_role_id');
//         $currentPath = trim($request->getUri()->getPath(), '/'); // Pastikan path tidak memiliki leading/trailing slashes

//         // Define accessible views for user_role_id 1 (Admin)
//         $adminPaths = [
//             'admin/dashboard',
//             'admin/master/department',
//             'admin/master/department/add',
//             'admin/master/department/edit/.*',
//             'admin/master/employee',
//             'admin/master/employee/add',
//             'admin/master/employee/edit',
//             'admin/master/employee/detail',
//             'admin/master/employee/attendance',
//             'admin/master/shift',
//             'admin/master/shift/add',
//             'admin/master/shift/edit',
//             'admin/master/user_account',
//             'admin/master/user_account/add',
//             'admin/master/user_account/edit',
//             'admin/report',
//             'admin/report/print_attendance_employee',
//             'admin/report/print_attendance_all',
//         ];

//         // Define accessible views for user_role_id 2 (Employee)
//         $employeePaths = [
//             'employee/attendance',
//             'employee/profile',
//             'employee/change_password',
//             'employee/attendance_history',
//         ];

//         // Define views accessible by all users
//         $publicPaths = [
//             'auth/login',
//             'auth/blocked',
//         ];

//         // Check access rules
//         if (in_array($currentPath, $adminPaths) && $userRoleId != 1) {
//             return redirect()->to('auth/blocked');
//         }

//         if (in_array($currentPath, $employeePaths) && $userRoleId != 2) {
//             return redirect()->to('auth/blocked');
//         }

//         // Allow access to public views without restriction
//         if (in_array($currentPath, $publicPaths)) {
//             return;
//         }

//         // Default redirect for unauthorized access
//         if (!in_array($currentPath, array_merge($adminPaths, $employeePaths, $publicPaths))) {
//             return redirect()->to('auth/blocked');
//         }
//     }

//     public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
//     {
//         // No post-processing required
//     }
// }

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
            'admin/master/employee/edit/.*', // Support dynamic ID
            'admin/master/employee/detail/.*',
            'admin/master/employee/delete/.*', // Support dynamic ID
            'admin/master/employee/attendance/.*', // Support dynamic ID
            'admin/master/employee/attendance',
            'admin/master/employee/attendance/edit/.*', // Support dynamic ID   
            'admin/master/employee/work_schedule/.*', 
            'admin/master/shift',
            'admin/master/shift/add',
            'admin/master/shift/edit/.*', // Support dynamic ID
            'admin/master/shift/delete/.*', // Support dynamic ID
            'admin/master/user_account',
            'admin/master/user_account/add/.*',
            'admin/master/user_account/edit/.*', // Support dynamic ID
            'admin/master/user_account/delete/.*',
            'admin/report',
            'admin/report/print_attendance_employee/pdf/.*',
            'admin/report/print_attendance_employee/excel/.*',
            'admin/report/print_attendance_all/pdf/.*',
            'admin/report/print_attendance_all/excel/.*',
        ];

        $employeePaths = [
            'employee/attendance',
            'employee/profile',
            'employee/change_password',
            'employee/attendance_history',
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
