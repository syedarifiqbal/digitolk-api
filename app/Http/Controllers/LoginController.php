<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Hash;

class LoginController extends Controller
{
    public function store(LoginRequest $request)
    {
        $user = User::query()->where('email', $request->email)->first();

        if (!Hash::check($request->password, $user->password)) {
            return $this->errorResponse('Invalid Credentials', false, Response::HTTP_UNAUTHORIZED);
        }

        if(request('device_id')){
            $user->device_id = request('device_id');
            $user->save();
        }

        $token = $user->createToken('User Password')->accessToken;
    
        $response = ['token' => $token, 'user' => $user];
        return response($response);
    }
}
