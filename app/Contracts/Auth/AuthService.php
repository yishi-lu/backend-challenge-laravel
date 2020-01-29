<?php

namespace App\Contracts\Auth;

/**
 * Auth Service interface
 *
 * Created by Yishi Lu.
 * User: Yishi Lu
 * Date: 2020/01/27
 */

interface AuthService
{
    //login user by username and password
    public function login($username, $password, $remember = true);

    //register user
    public function register($userInfo);

}