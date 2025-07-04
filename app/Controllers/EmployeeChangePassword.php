<?php

namespace App\Controllers;

use App\Models\AuthModel;
use CodeIgniter\Exceptions\PageNotFoundException;

class EmployeeChangePassword extends BaseController
{
    protected $authModel;
    protected $db;

    public function __construct()
    {
        $this->authModel = new AuthModel();
        $this->db = \Config\Database::connect();
    }

    public function index()
    {
        $data['title']     = 'Ubah Password';
        $username          = session()->get('username');
        $data['account']   = $this->authModel->getAccount($username);

        if (!$data['account']) {
            throw new PageNotFoundException('Account not found.');
        }

        $employee_id = $data['account']['employee_id'];
        if (!$employee_id) {
            return $this->response->setStatusCode(400, 'Employee ID is required but not found.');
        }

        $data['employee'] = $this->db->table('employee')
            ->where('employee_id', $employee_id)
            ->get()
            ->getRowArray();

        if (!$data['employee']) {
            throw new PageNotFoundException('Employee not found.');
        }

        echo view('layout/header', $data);
        echo view('layout/sidebar');
        echo view('layout/topbar');
        echo view('employee/change_password/index', $data);
        echo view('layout/footer');
    }

    public function update()
    {
        if ($this->request->getMethod() !== 'POST') {
            return redirect()->to('/employee/change_password');
        }

        $username   = session()->get('username');
        $validation = \Config\Services::validation();

        // Rules validasi
        $validation->setRules([
            'current_password' => [
                'label' => 'Password Aktif',
                'rules' => 'required'
            ],
            'new_password' => [
                'label' => 'Password Baru',
                'rules' => 'required|min_length[6]'
            ],
            'confirm_password' => [
                'label' => 'Konfirmasi Password Baru',
                'rules' => 'required|matches[new_password]'
            ],
        ], [
            'min_length' => '{field} harus berisi minimal {param} karakter.',
            'matches'    => '{field} harus sama dengan Password Baru.'
        ]);

        // Cek validasi
        if (!$validation->withRequest($this->request)->run()) {
            session()->setFlashdata('error', $validation->listErrors());
            return redirect()->back()->withInput();
        }

        // Ambil input
        $current_password = $this->request->getPost('current_password');
        $new_password     = $this->request->getPost('new_password');

        // Ambil hash password lama
        $user = $this->db->table('user_account')
            ->select('password')
            ->where('username', $username)
            ->get()
            ->getRow();

        if (!$user || !password_verify($current_password, $user->password)) {
            session()->setFlashdata('error', 'Password Saat ini, tidak sesuai.');
            return redirect()->back()->withInput();
        }

        // Cek password baru tidak sama dengan lama
        if (password_verify($new_password, $user->password)) {
            session()->setFlashdata('error', 'Password baru tidak boleh sama dengan password saat ini.');
            return redirect()->back()->withInput();
        }

        // Update password
        $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
        $this->db->table('user_account')
            ->where('username', $username)
            ->update(['password' => $hashed_password]);

        session()->setFlashdata('success', 'Password berhasil diubah.');
        return redirect()->to('/employee/change_password');
    }
}
