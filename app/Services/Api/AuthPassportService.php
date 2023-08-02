<?php

namespace App\Services\Api;

use Illuminate\Support\Facades\Http;


class AuthPassportService
{




    public function requestAuthToken($email, $password, $guard)
    {

        try {
            $response = Http::asForm()->post('http://127.0.0.1:88/oauth/token', [
                'grant_type' => 'password',
                'client_id' => config('passport.' . $guard . '.client_id'),
                'client_secret' => config('passport.' . $guard . '.client_secret'),
                'username' => $email,
                'password' => $password,
                'scope' => '',
            ]);

            if ($response->successful()) {

                return $response->json();

            } else {
                return $response->json();
            }
        } catch (\Exception $e) {
            return response($e->getMessage());
        }
    }

}
