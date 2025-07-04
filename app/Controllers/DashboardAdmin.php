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
        $rptraName = session()->get('rptra_name');
        $data['title']   = 'Dashboard Admin';
        $data['account'] = $this->authModel->getAccount(session()->get('username'));
        $data['s_list'] = $this->adminModel->getAllShifts();
        $data['display'] = $this->adminModel->getDataForDashboard($rptraName);
        $data['d_list']  = $this->adminModel->getEmployeeCountByDepartment($rptraName);

        echo view('layout/dashboard_header', $data);
        echo view('layout/sidebar');
        echo view('layout/topbar');
        echo view('admin/dashboard/index', $data);
        echo view('layout/dashboard_footer');
    }
}
