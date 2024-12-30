<?php

namespace App\Controllers;

use App\Models\AdminModel;
use App\Models\AuthModel;
use CodeIgniter\Controller;

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
        // jumlah pegawai berdasarkan departemen
        $data['d_list'] = $this->adminModel->getEmployeeCountByDepartment();

        // jumlah pegawai berdasarkan shift
        $data['s_list'] = $this->adminModel->getEmployeeCountByShift();

        // Dashboard data
        $data['title'] = 'Dashboard Admin';
        $data['account'] = $this->authModel->getAccount(session()->get('username'));
        $data['display'] = $this->adminModel->getDataForDashboard();

        // Load views
        echo view('layout/dashboard_header', $data);
        echo view('layout/sidebar');
        echo view('layout/topbar');
        echo view('admin/dashboard/index', $data);
        echo view('layout/dashboard_footer');
    }
}