<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use Config\Database;
use App\Models\AuthModel;

class Auth extends BaseController
{
    protected $db;
    protected $authModel;

    public function __construct()
    {
        $this->db = Database::connect();
        $this->authModel = new AuthModel();
    }

    public function index()
    {
        if (session()->get('logged_in')) {
            // Arahkan sesuai dengan role user
            $userRoleId = session()->get('user_role_id');

            if ($userRoleId == 1) {
                // Super Admin
                return redirect()->to('superadmin/dashboard');
            } elseif ($userRoleId == 2) {
                // Admin biasa
                return redirect()->to('admin/dashboard');
            }

            return redirect()->to('employee/attendance');
        }

        $data['title'] = 'Login Page';
        $data['validation'] = session()->getFlashdata('validation');
        $data['error'] = session()->getFlashdata('error');

        echo view('layout/auth_header', $data);
        echo view('auth/login', $data);
        echo view('layout/auth_footer');
    }

    public function login()
    {
        if ($this->request->getMethod() === 'POST') {
            $validation = \Config\Services::validation();

            $validation->setRules([
                'username' => [
                    'label' => 'Username',
                    'rules' => 'required',
                    'errors' => [
                        'required' => 'Username wajib diisi.'
                    ],
                ],
                'password' => [
                    'label' => 'Password',
                    'rules' => 'required|min_length[6]',
                    'errors' => [
                        'required' => 'Password wajib diisi.',
                        'min_length' => 'Password harus memiliki minimal 6 karakter.'
                    ]
                ]
            ]);

            // Validasi input
            if (!$this->validate($validation->getRules())) {
                session()->setFlashdata('validation', $this->validator);
                return redirect()->to('auth/login')->withInput();
            }

            $username = $this->request->getPost('username');
            $password = $this->request->getPost('password');

            // Ambil data user berdasarkan username
            $user = $this->authModel->getUserByUsername($username);

            // Jika user ditemukan
            if ($user) {
                if (password_verify($password, $user->password)) {
                    // Session berdasarkan role
                    if ($user->user_role_id == 1) {
                        // Super Admin
                        session()->set([
                            'username'      => $user->username,
                            'user_role_id'  => $user->user_role_id,
                            'role'          => $user->user_role_name,
                            'logged_in'     => true,
                        ]);
                        return redirect()->to('superadmin/dashboard');
                    } elseif ($user->user_role_id == 2) {
                        // Admin Biasa
                        session()->set([
                            'username'      => $user->username,
                            'user_role_id'  => $user->user_role_id,
                            'role'          => $user->user_role_name,
                            'rptra_name'    => $user->rptra_name,
                            'rptra_address' => $user->rptra_address,
                            'logged_in'     => true,
                        ]);
                        return redirect()->to('admin/dashboard');
                    } else {
                        // Employee
                        session()->set([
                            'username'      => $user->username,
                            'user_role_id'  => $user->user_role_id,
                            'role'          => $user->user_role_name,
                            'rptra_name'    => $user->rptra_name,
                            'rptra_address' => $user->rptra_address,
                            'logged_in'     => true,
                        ]);
                        return redirect()->to('employee/attendance');
                    }
                } else {
                    session()->setFlashdata('message', 'Password salah!');
                }
            } else {
                session()->setFlashdata('message', 'Username tidak ditemukan!');
            }

            // Redirect kembali ke login dengan pesan error
            return redirect()->to('auth/login')->withInput();
        }

        // Jika bukan metode POST
        return redirect()->to('auth/login');
    }

    public function logout()
    {
        // Hapus sesi
        session()->remove(['username', 'user_role_id', 'role', 'logged_in']);
        session()->setFlashdata('success', 'Anda telah logout');
        return redirect()->to('auth/login');
    }

    public function blocked()
    {
        $data['title'] = 'Access Blocked';
        return view('auth/blocked', $data);
    }
}
