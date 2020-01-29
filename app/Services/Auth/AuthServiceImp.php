<?php

namespace App\Services\Auth;

use App\Contracts\Auth\AuthService;
use App\User; 

use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

/**
 * AuthServiceImp
 *
 * Created by Yishi Lu.
 * User: Yishi Lu
 * Date: 2020/01/27
 */
class AuthServiceImp implements AuthService
{

    /**
     * Login user by email and password
     *
     * @param $email
     * @param null $password
     * @param bool $remember
     * @return User|null
     */
    public function login($username, $given_password, $remember = true){

        $username = $username;
        $password = $given_password;

        $user = User::where("name", $username)->first();
        if ($user==null || !Hash::check($password, $user->password)) $user = null;

        return $user;
    }

    /**
     * register user
     *
     * @param $userInfo
     * @return User|null
     */
    public function register($userInfo){

        $input = $userInfo->all();  
        $input['password'] = Hash::make($userInfo['password']);

        $user = User::create($input); 

        return $user;

    }

}