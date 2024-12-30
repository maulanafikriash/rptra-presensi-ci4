<?php

namespace App\Controllers;

use App\Models\AuthModel;
use CodeIgniter\Exceptions\PageNotFoundException;

class EmployeeChangePassword extends BaseController
{
    protected $authModel;

    public function __construct()
    {
        $this->authModel = new AuthModel();
    }

    public function index()
    {
        $data['title'] = 'Ubah Password';

        $username = session()->get('username');
        $data['account'] = $this->authModel->getAccount($username);

        if (!$data['account']) {
            throw new PageNotFoundException('Account not found.');
        }

        $employee_id = $data['account']['employee_id'];
        if (!$employee_id) {
            return $this->response->setStatusCode(400, 'Employee ID is required but not found.');
        }

        // Data pegawai berdasarkan employee_id
        $db = \Config\Database::connect();
        $data['employee'] = $db->table('employee')
            ->where('employee_id', $employee_id)
            ->get()
            ->getRowArray();

        if (!$data['employee']) {
            throw new PageNotFoundException('Employee not found.');
        }

        // Proses hanya jika form dikirimkan
        if ($this->request->getMethod() === 'POST') {
            // Validasi form
            $validation = \Config\Services::validation();
            $validation->setRules([
                'current_password' => ['label' => 'Password Aktif', 'rules' => 'required'],
                'new_password' => ['label' => 'Password Baru', 'rules' => 'required|min_length[6]'],
                'confirm_password' => ['label' => 'Konfirmasi Password Baru', 'rules' => 'required|matches[new_password]']
            ], [
                'min_length' => '{field} harus berisi minimal {param} karakter.',
                'matches' => '{field} harus sama dengan Password Baru.'
            ]);

            if (!$validation->withRequest($this->request)->run()) {
                // Jika validasi gagal
                session()->setFlashdata('error', $validation->listErrors());
            } else {
                // Jika validasi berhasil
                $current_password = $this->request->getPost('current_password');
                $new_password = $this->request->getPost('new_password');

                // Ambil hash password dari database
                $user = $db->table('user_accounts')
                    ->select('password')
                    ->where('username', $username)
                    ->get()
                    ->getRow();

                if ($user && password_verify($current_password, $user->password)) {
                    // Cek apakah password baru sama dengan password lama
                    if (password_verify($new_password, $user->password)) {
                        session()->setFlashdata('error', 'Password baru tidak boleh sama dengan password aktif.');
                    } else {
                        // Hash password baru dan lakukan update
                        $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
                        $db->table('user_accounts')
                            ->where('username', $username)
                            ->update(['password' => $hashed_password]);

                        session()->setFlashdata('success', 'Password berhasil diubah.');
                    }
                } else {
                    session()->setFlashdata('error', 'Password aktif tidak sesuai.');
                }

                return redirect()->to('/employee/change_password');
            }
        }

        // Load views dengan data error
        return view('layout/header', $data)
            . view('layout/sidebar')
            . view('layout/topbar')
            . view('employee/change_password/index', $data)
            . view('layout/footer');
    }
}
