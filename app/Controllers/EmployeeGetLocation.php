<?php

namespace App\Controllers;

use App\Controllers\BaseController;

class EmployeeGetLocation extends BaseController
{
    public function getAddress()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setStatusCode(403, 'Forbidden');
        }

        // Validasi input
        $validation = \Config\Services::validation();
        $validation->setRules([
            'lat' => 'required|numeric',
            'lon' => 'required|numeric',
        ]);

        if (!$validation->withRequest($this->request)->run()) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Invalid coordinates.'])->setStatusCode(400);
        }

        $lat = $this->request->getPost('lat');
        $lon = $this->request->getPost('lon');

        // HTTP Client CodeIgniter 4
        $client = \Config\Services::curlrequest();

        try {
            $response = $client->request('GET', "https://nominatim.openstreetmap.org/reverse", [
                'query' => [
                    'format' => 'json',
                    'lat' => $lat,
                    'lon' => $lon,
                    'zoom'   => 18,
                    'addressdetails' => 1
                ],
                'headers' => [
                    'User-Agent' => 'RPTRAPresensi/1.0 (https://rptrapresensi.web.id/; maulanafikriash@gmail.com)'
                ],
                'timeout' => 10, // Timeout dalam detik
            ]);

            if ($response->getStatusCode() === 200) {
                $body = json_decode($response->getBody());
                if (isset($body->display_name)) {
                    return $this->response->setJSON(['status' => 'success', 'address' => $body->display_name]);
                } else {
                    return $this->response->setJSON(['status' => 'error', 'message' => 'Address not found in response.']);
                }
            } else {
                return $this->response->setJSON(['status' => 'error', 'message' => 'Nominatim API returned an error.'])->setStatusCode($response->getStatusCode());
            }
        } catch (\Exception $e) {
            log_message('error', '[Nominatim Proxy] ' . $e->getMessage());
            return $this->response->setJSON(['status' => 'error', 'message' => 'Failed to connect to geocoding service.'])->setStatusCode(500);
        }
    }
}
