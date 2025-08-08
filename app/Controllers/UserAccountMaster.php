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
        $session  = session();
        $role_id  = $session->get('user_role_id');
        $roleName = $role_id == 1 ? 'Admin' : 'Pegawai';
        $allUsers = $this->userAccountModel->getAllUsersWithEmployee();

        if ($role_id == 1) {
            // Super Admin: hanya pegawai di department 'ADM'
            $filtered = array_filter($allUsers, fn($u) => isset($u['d_id']) && $u['d_id'] === 'ADM');
            $title    = 'Admin Account';
        } else {
            $rptraName = $session->get('rptra_name');
            $filtered  = array_filter($allUsers, fn($u) => isset($u['rptra_name']) && $u['rptra_name'] === $rptraName);
            $title     = 'User Account';
        }

        $data = [
            'role_name' => $roleName,
            'title'   => $title,
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
        $session   = session();
        $role_id   = $session->get('user_role_id');
        $employee  = $this->userAccountModel->getEmployeeById($employeeId);
        $user = $this->userAccountModel->getUserByEmployeeId($employeeId);
        $title = $role_id == 1 ? 'Admin' : 'User';

        $data = [
            'title'      => $title,
            'e_id'       => $employeeId,
            'username'   => $employee['department_id'] . $employee['employee_id'],
            'role_id'    => $role_id,
            'account'    => $this->authModel->getAccount($session->get('username')),
            'validation' => \Config\Services::validation()
        ];

        if ($this->request->getMethod() === 'POST') {
            $rules = [
                'u_username' => [
                    'rules' => 'required|trim|is_unique[user_account.username]|exact_length[7]',
                    'errors' => [
                        'required' => 'Username wajib diisi.',
                        'trim' => 'Username tidak boleh memiliki spasi di awal atau akhir.',
                        'is_unique'  => 'Username sudah digunakan.',
                        'exact_length' => 'Username harus terdiri dari 7 karakter.',
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
                $roleId = ($employee['department_id'] === 'ADM') ? 2 : 3;

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
                if (session()->get('user_role_id') == 1) {
                    return redirect()->to('superadmin/master/admin_account');
                }

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
        $session = session();
        $role_id = $session->get('user_role_id');
        $userRec = $this->userAccountModel->getUserByUsername($username);
        $title = $role_id == 1 ? 'Admin' : 'User';

        $data = [
            'title'      => $title,
            'users'      => $userRec,
            'role_id'    => $role_id,
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
            if ($role_id == 1) {
                $rules['u_username'] = [
                    'rules'  => 'required|trim|is_unique[user_account.username]|exact_length[7]',
                    'errors' => [
                        'required'   => 'Username wajib diisi.',
                        'trim' => 'Username tidak boleh memiliki spasi di awal atau akhir.',
                        'is_unique'  => 'Username sudah digunakan.',
                        'exact_length' => 'Username harus terdiri dari 7 karakter.',
                    ],
                ];
            }

            if (!$this->validate($rules)) {
                $data['validation'] = $this->validator;
            } else {
                $updateData = [
                    'password' => password_hash($this->request->getPost('password'), PASSWORD_DEFAULT),
                ];
                if ($role_id == 1) {
                    $updateData['username'] = $this->request->getPost('u_username');
                }

                $this->userAccountModel->updateUser($updateData, $username);

                session()->setFlashdata(
                    'message',
                    '<div class="alert alert-success" role="alert">Password' . ($role_id == 1 ? ' & Username' : '') . ' berhasil diperbarui!</div>'
                );
                if ($role_id == 1) {
                    return redirect()->to('superadmin/master/admin_account');
                }
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

        // Redirect sesuai role
        if (session()->get('user_role_id') == 1) {
            return redirect()->to('superadmin/master/admin_account');
        }

        return redirect()->to('admin/master/user_account');
    }
}
