<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class UserController extends Controller
{
    public function register(Request $request)
    {
        $attrs = $request->validate([
            'name' => 'required|string|min:4',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:4'
        ],[
            'email.required' => 'The email field is required',
            'password.min' => 'The password is too short',
        ]);

        /** @var User $user */
        $user = User::query()->create([
            'name' => $attrs['name'],
            'email' => $attrs['email'],
            'password' => Hash::make($attrs['password']),
        ]);

        \Auth::login($user, true);

        return $user;
    }

    public function login(Request $request){
        $attrs = $request->validate([
           'email' => 'required|email|exists:users,email',
           'password' => 'required|min:4',
        ],[
            'email.exists' => 'There is no such user with these credentials',
        ]);

        if(\Auth::attempt($attrs)){
            return \Auth::user();
        }

        throw ValidationException::withMessages([
           'password' => ['The password is incorrect.'],
        ]);
    }
}
