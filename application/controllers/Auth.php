<?php

class Auth extends MY_Controller
{
    public function login()
    {
        $user = [
            'id' => random_int(1, 1000),
            'username' => 'Test User',
        ];

        $this->response($user);
    }

    public function logout()
    {
        $response = 'Good bye, user!';

        $this->response($response);
    }
}