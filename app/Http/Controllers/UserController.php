<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    public function create(Request $request)
    {
        $validation = Validator::make($request->all(),[
            'name'                  => 'required',
            'email'                 => 'required|email|max:40|unique:users,email',
            'password'              => 'required|min:8|confirmed',
            'password_confirmation' => 'required'
        ]);

        if($validation->fails())
            return error('Validation Error',$validation->errors());

        $user = User::create($request->only(['name','email'])+[
            'password'  => Hash::make($request->password)
        ]);

        return ok('User Created Successfully',$user);
    }
}
