<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use App\traits;
use App\Traits\ListingApiTrait;

class AuthController extends Controller
{
    use ListingApiTrait;
    public function create(Request $request)
    {
        $validation = Validator::make($request->all(), [
            'name'                  => 'required',
            'email'                 => 'required|email|max:40|unique:users,email',
            'password'              => 'required|min:8|confirmed',
            'password_confirmation' => 'required'
        ]);

        if ($validation->fails())
            return error('Validation Error', $validation->errors(), 'validation');

        $user = User::create($request->only(['name', 'email']) + [
            'password'          => Hash::make($request->password),
            'remember_token'    => Str::random(10)
        ]);

        return ok('User Created Successfully', $user);
    }

    public function login(Request $request)
    {
        $validation = Validator::make($request->all(), [
            'email'     => 'required|exists:users,email',
            'password'  => 'required'
        ]);

        if ($validation->fails())
            return error('Validation Error', $validation->errors(), 'validation');

        $user = User::where('email', $request->email)->first();
        if (Auth::attempt(['email' => $request->email, 'password' => $request->password])) {
            $apiToken = $user->createToken("API TOKEN")->plainTextToken;
            return ok('Login Successful',$apiToken);
        } else {
            return error('Password Incorrect');
        }
    }

   
    //List of users from users table: For pagination practice
    public function userlist(Request $request)
    {       
        $this->ListingValidation();
        $query = User::query();
        $searchable_fields = ['name','email','created_at'];
        $data = $this->filterSearchPagination($query,$searchable_fields);
        return ok('User Data',[
            'users'=>$data['query']->paginate($request->perpage),
            'count'=>$data['count']
        ]);
    }
}
