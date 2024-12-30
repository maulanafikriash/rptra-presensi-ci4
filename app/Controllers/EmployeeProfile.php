<?php

namespace App\Controllers;

use App\Models\AuthModel;

class EmployeeProfile extends BaseController
{
    protected $authModel;

    public function __construct()
    {
        $this->authModel = new AuthModel();
    }

    public function index()
    {
        $data = [
            'title' => 'My Profile',
            'account' => $this->authModel->getAccount(session()->get('username')),
        ];

        // Load views
        echo view('layout/header', $data);
        echo view('layout/sidebar');
        echo view('layout/topbar');
        echo view('employee/profile/index', $data);
        echo view('layout/footer');
    }
}
