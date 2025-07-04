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
            'validation' => \Config\Services::validation()
        ];

        echo view('layout/header', $data);
        echo view('layout/sidebar');
        echo view('layout/topbar');
        echo view('employee/profile/index', $data);
        echo view('layout/footer');
    }

    public function uploadImage()
    {
        $rules = [
            'image' => [
                'rules' => 'uploaded[image]|max_size[image,3072]|is_image[image]|mime_in[image,image/jpg,image/jpeg,image/png]',
                'errors' => [
                    'uploaded' => 'Tidak ada foto yang dipilih. Silakan pilih foto untuk diunggah.',
                    'max_size' => 'Ukuran file gambar maksimal 3MB.',
                    'is_image' => 'File yang Anda pilih bukan gambar.',
                    'mime_in'  => 'Format file harus JPG, JPEG, atau PNG.',
                ],
            ]
        ];

        if (!$this->validate($rules)) {
            session()->setFlashdata('error', $this->validator->listErrors());
            return redirect()->to('/employee/profile');
        }

        $imageFile = $this->request->getFile('image');
        $employee = $this->employeeModel->getAllEmployeeData(session()->get('username'));
        $oldImageName = $employee['image'];

        // Cek apakah file valid dan belum dipindahkan
        if ($imageFile->isValid() && !$imageFile->hasMoved()) {
            // Buat nama file random baru
            $newImageName = $imageFile->getRandomName();

            $imageFile->move('img/pp/', $newImageName);

            // Hapus file gambar lama jika bukan file default
            if ($oldImageName != 'default.jpg' && file_exists('img/pp/' . $oldImageName)) {
                unlink('img/pp/' . $oldImageName);
            }

            $employeeId = $employee['id'];
            $this->employeeModel->update($employeeId, ['image' => $newImageName]);

            session()->setFlashdata('success', 'Foto profil berhasil diperbarui.');
            return redirect()->to('/employee/profile');
        }

        session()->setFlashdata('message', 'Gagal mengunggah foto. Silakan coba lagi.');
        return redirect()->to('/employee/profile');
    }
}
