<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth; 
use App\Http\Controllers\Controller; 
use Illuminate\Support\Facades\Cookie;

use App\Contracts\Auth\AuthService;

/**
 * AuthController
 *
 * Created by Yishi Lu.
 * User: Yishi Lu
 * Date: 2020/01/27
 */

class AuthController extends Controller
{

    protected $service;
    public $successStatus = 200;

    /**
     * AuthController constructor, get AuthService object by DI
     * @param $service
     */
    public function __construct(AuthService $service)
    {
        $this->service = $service;
    }

    /**
     * call AuthService to register user
     *
     * @param Request $request
     * @return json response
     */
    public function registration(Request $request){

        $validator = $this->validateRegister($request);

        $user = $this->service->register($request);

        if($user == null) return response()->json(['message'=>'Failed to register with provided information.']);

        return response()->json(['Success'=>"registered successfully"], $this->successStatus); 
    }
    
    /**
     * call AuthService to login user
     *
     * @param Request $request
     * @return json response
     */
    public function login(Request $request){ 

        $validator = $this->validateLogin($request);

        $name = $request->get('name');
        $password = $request->get('password');

        $user = $this->service->login($name, $password);

        if($user) {
            Auth::login($user);
            $success['token'] =  $user->createToken(config('app.name'))->accessToken;
            $cookie = $this->makeTokenCookie($success['token']);
            return response()->json(['success'=>Auth::user()], $this->successStatus)->withCookie($cookie);
        }
        else {
            return response()->json(['message'=>['Invalid username or password']], 401);
        }
    }

    /**
     * call to log out user, remove cookie and revoke token
     *
     * @param Request $request
     * @return json response
     */
    public function logout(Request $request){
        $request->user()->token()->revoke();
        $cookie = Cookie::forget(env('AUTH_TOKEN'));

        return response()->json(['message' => 'Successfully logged out'], $this->successStatus)->withCookie($cookie);
    }

    /**
     * validate user information before register user
     *
     * @param Request $request
     * @return null
     */
    protected function validateRegister(Request $request){

        $this->validate($request, [
            'name' => "required|string|max:255|unique:users",
            'email' => "required|string|email|max:255|unique:users",
            'password' => "required|string|min:8",
            'confirm_password' => "required|same:password",
        ]);
    }

    /**
     * validate user information before login user
     *
     * @param Request $request
     * @return null
     */
    protected function validateLogin(Request $request){

        $this->validate($request, [
            "name" => "required|string",
            "password" => "required|string",
        ]);
    }

    private function makeTokenCookie($token){

        return cookie(env('AUTH_TOKEN'), $token, time()+60*60*24*7, null, null, null, true); //'name', 'value', $minutes, $path, $domain, $secure, $httpOnly
    }


}
