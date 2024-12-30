<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use Config\Database;

class Auth extends BaseController
{
    protected $db;

    public function __construct()
    {
        $this->db = Database::connect();
    }

    public function index()
    {
        // Cek apakah user sudah login
        if (session()->get('logged_in')) {
            // Arahkan sesuai dengan role user
            $userRoleId = session()->get('user_role_id');

            return $userRoleId == 1
                ? redirect()->to('admin/dashboard')
                : redirect()->to('employee/profile');
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

            // Atur aturan validasi
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
            $user = $this->db->table('user_accounts')
                ->select('user_accounts.username, user_accounts.password, user_accounts.user_role_id, user_role.user_role_name')
                ->join('user_role', 'user_accounts.user_role_id = user_role.user_role_id')
                ->where('user_accounts.username', $username)
                ->get()
                ->getRow();

            // Jika user ditemukan
            if ($user) {
                // Verifikasi password
                if (password_verify($password, $user->password)) {
                    // Set session
                    session()->set([
                        'username'     => $user->username,
                        'user_role_id' => $user->user_role_id,
                        'role'         => $user->user_role_name,
                        'logged_in'    => true,
                    ]);

                    // Redirect sesuai role user
                    return $user->user_role_id == 1
                        ? redirect()->to('admin/dashboard')
                        : redirect()->to('employee/profile');
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
        // Hapus semua sesi
        session()->destroy();
        return redirect()->to('auth/login');
    }

    public function blocked()
    {
        $data['title'] = 'Access Blocked';
        return view('auth/blocked', $data);
    }
}
