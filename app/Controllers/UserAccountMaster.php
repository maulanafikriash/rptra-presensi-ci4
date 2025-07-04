<?php

namespace App\Controllers;

use App\Models\UserAccountModel;
use App\Models\AuthModel;

class UserAccountMaster extends BaseController
{
    protected $userAccountModel;
    protected $authModel;

    public function __construct()
    {
        $this->userAccountModel = new UserAccountModel();
        $this->authModel = new AuthModel();
    }

    public function index()
    {
        $rptraName = session()->get('rptra_name');
        $allUsers = $this->userAccountModel->getAllUsersWithEmployee();
        $filtered = array_filter($allUsers, fn($u) => isset($u['rptra_name']) && $u['rptra_name'] === $rptraName);
        $data = [
            'title'   => 'User Account',
            'data'    => $filtered,
            'account' => $this->authModel->getAccount(session()->get('username'))
        ];

        echo view('layout/table_header', $data);
        echo view('layout/sidebar');
        echo view('layout/topbar');
        echo view('admin/master/user_accounts/index', $data);
        echo view('layout/table_footer');
    }

    public function add($employeeId)
    {
        $employee = $this->userAccountModel->getEmployeeById($employeeId);
        if (!$employee || $employee['rptra_name'] !== session()->get('rptra_name')) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('Akses ditolak untuk pegawai ini.');
        }

        $user = $this->userAccountModel->getUserByEmployeeId($employeeId);

        $data = [
            'title' => 'Tambah Akun User',
            'e_id' => $employeeId,
            'username' => $employee['department_id'] . $employee['employee_id'],
            'account' => $this->authModel->getAccount(session()->get('username')),
            'validation' => \Config\Services::validation()
        ];

        if ($this->request->getMethod() === 'POST') {
            $rules = [
                'u_username' => [
                    'rules' => 'required|trim|min_length[6]',
                    'errors' => [
                        'required' => 'Username wajib diisi.',
                        'trim' => 'Username tidak boleh memiliki spasi di awal atau akhir.',
                        'min_length' => 'Username harus memiliki minimal 6 karakter.',
                    ],
                ],
                'u_password' => [
                    'rules' => 'required|trim|min_length[6]|regex_match[/^\S+$/]',
                    'errors' => [
                        'required' => 'Password wajib diisi.',
                        'trim' => 'Password tidak boleh memiliki spasi di awal atau akhir.',
                        'min_length' => 'Password harus memiliki minimal 6 karakter.',
                        'regex_match' => 'Password tidak boleh mengandung spasi.',
                    ],
                ],
            ];

            if (!$this->validate($rules)) {
                $data['validation'] = $this->validator;
            } else {
                $username = $this->request->getPost('u_username');
                $roleId = ($employee['department_id'] === 'ADM') ? 1 : 2;

                $userData = [
                    'username' => $username,
                    'password' => password_hash($this->request->getPost('u_password'), PASSWORD_DEFAULT),
                    'employee_id' => $employeeId,
                    'user_role_id' => $roleId
                ];

                if ($user) {
                    $this->userAccountModel->updateUser($userData, $user['username']);
                } else {
                    $this->userAccountModel->addUser($userData);
                }

                session()->setFlashdata('message', '<div class="alert alert-success" role="alert">Akun berhasil dibuat!</div>');
                return redirect()->to('admin/master/user_account');
            }
        }

        echo view('layout/header', $data);
        echo view('layout/sidebar');
        echo view('layout/topbar');
        echo view('admin/master/user_accounts/add_user_account', $data);
        echo view('layout/footer');
    }

    public function edit($username)
    {
        $userRec = $this->userAccountModel->getUserByUsername($username);
        // RPTRA yang sama
        if (!$userRec || $userRec['rptra_name'] !== session()->get('rptra_name')) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('Akses ditolak untuk akun ini.');
        }
        $data = [
            'title'      => 'Edit User',
            'users'      => $userRec,
            'account'    => $this->authModel->getAccount(session()->get('username')),
            'validation' => \Config\Services::validation()
        ];

        if ($this->request->getMethod() === 'POST') {
            $rules = [
                'password' => [
                    'rules' => 'required|trim|min_length[6]|regex_match[/^\S+$/]',
                    'errors' => [
                        'required' => 'Password wajib diisi.',
                        'trim' => 'Password tidak boleh memiliki spasi di awal atau akhir.',
                        'min_length' => 'Password harus memiliki minimal 6 karakter.',
                        'regex_match' => 'Password tidak boleh mengandung spasi.'
                    ],
                ],
            ];

            if (!$this->validate($rules)) {
                $data['validation'] = $this->validator;
            } else {
                $this->userAccountModel->updateUser(
                    ['password' => password_hash($this->request->getPost('password'), PASSWORD_DEFAULT)],
                    $username
                );

                session()->setFlashdata('message', '<div class="alert alert-success" role="alert">Password berhasil diperbarui!</div>');
                return redirect()->to('admin/master/user_account');
            }
        }

        echo view('layout/header', $data);
        echo view('layout/sidebar');
        echo view('layout/topbar');
        echo view('admin/master/user_accounts/edit_user_account', $data);
        echo view('layout/footer');
    }

    public function delete($username)
    {
        $this->userAccountModel->deleteUserAttendance($username);
        $this->userAccountModel->deleteUser($username);

        session()->setFlashdata('message', '<div class="alert alert-success" role="alert">Akun berhasil dihapus!</div>');
        return redirect()->to('admin/master/user_account');
    }
}
