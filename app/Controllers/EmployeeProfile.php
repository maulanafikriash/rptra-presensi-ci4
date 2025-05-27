<?php

namespace App\Controllers;

use App\Models\AuthModel;
use App\Models\EmployeeModel;

class EmployeeProfile extends BaseController
{
    protected $authModel;
    protected $employeeModel;

    public function __construct()
    {
        $this->authModel = new AuthModel();
        $this->employeeModel = new EmployeeModel();
    }

    public function index()
    {
        $data = [
            'title' => 'Profil Saya',
            'account' => $this->authModel->getAccount(session()->get('username')),
        ];

        // Load views
        echo view('layout/header', $data);
        echo view('layout/sidebar');
        echo view('layout/topbar');
        echo view('employee/profile/index', $data);
        echo view('layout/footer');
    }

    public function uploadImage()
    {
        $data = [
            'validation' => \Config\Services::validation()
        ];

        if ($this->request->getMethod() === 'POST') {
            $file = $this->request->getFile('image');

            if (!$file->isValid() || $file->getError() == 4) {  // Error 4 berarti tidak ada file yang dipilih
                session()->setFlashdata('message', 'Tidak ada foto yang dipilih. Silakan pilih foto untuk diunggah.');
                return redirect()->to('/employee/profile')->withInput();
            }

            $rules = [
                'image' => [
                    'rules' => 'uploaded[image]|is_image[image]|max_size[image,3072]|mime_in[image,image/jpg,image/jpeg,image/png]',
                    'errors' => [
                        'max_size' => 'Ukuran file gambar maksimal 3MB.',
                        'is_image' => 'File harus berupa gambar.',
                        'mime_in' => 'Format file harus JPG, JPEG, atau PNG.',
                    ],
                ]
            ];

            if (!$this->validate($rules)) {
                $data['validation'] = $this->validator;
                session()->setFlashdata('error', 'Format file tidak didukung atau ukuran file lebih dari 3MB!');
                return redirect()->to('/employee/profile');
            } else {
                // Jika file valid, ambil file dan data pegawai
                $file = $this->request->getFile('image');
                $employee = $this->employeeModel->getAllEmployeeData(session()->get('username'));
                $imageName = $employee['image'];

                if ($file->isValid() && !$file->hasMoved()) {
                    // Nama file gambar baru yang diupload
                    $imageName = 'item-' . date('ymd') . '-' . substr(md5(rand()), 0, 10) . '.' . $file->getExtension();

                    $file->move('./img/pp/', $imageName);

                    $employeeId = $employee['id'];
                    $this->employeeModel->update($employeeId, ['image' => $imageName]);

                    session()->setFlashdata('success', 'Foto profil berhasil diperbarui.');
                    return redirect()->to('/employee/profile');
                }
            }
        }
    }
}
