<?php

namespace App\Controllers;

use App\Models\AdminModel;
use App\Models\AuthModel;

class DashboardAdmin extends BaseController
{
    protected $adminModel;
    protected $authModel;

    public function __construct()
    {
        $this->adminModel = new AdminModel();
        $this->authModel = new AuthModel();
    }

    public function index()
    {
        $role_id   = session()->get('user_role_id');
        $rptraName = session()->get('rptra_name');
        $title = $role_id == 1 ? 'Dashboard Super Admin' : 'Dashboard Admin';
        $roleName = $role_id == 1 ? 'Admin' : 'Pegawai';

        $data['title']   = $title;
        $data['role_name'] = $roleName;
        $data['account'] = $this->authModel->getAccount(session()->get('username'));

        if ($role_id == 1) {
            // Super Admin
            $data['display'] = $this->adminModel->getDataForSuperAdminDashboard();
            $data['s_list']  = [];
            $data['d_list']  = [];
        } else {
            $data['display'] = $this->adminModel->getDataForDashboard($rptraName);
            $data['s_list']  = $this->adminModel->getAllShifts();
            $data['d_list']  = $this->adminModel->getEmployeeCountByDepartment($rptraName);
        }

        echo view('layout/dashboard_header', $data);
        echo view('layout/sidebar');
        echo view('layout/topbar');
        echo view('admin/dashboard/index', $data);
        echo view('layout/dashboard_footer');
    }
}
