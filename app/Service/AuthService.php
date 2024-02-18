<?php

namespace App\Service;

use App\Models\User;

class AuthService {

    /**
     * register a new user
     */
    public function register($name, $email, $picture, $password)  {

         $user = User::findByEmail($email);
        if (!$user) {
            $user = User::create([
                'name' => $name, 
                'email' => $email, 
                'picture' => $picture, 
                'password' => $password]);
        }
        return $user;
    }
}