<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\UserRegister;
use App\Http\Requests\UserLogin;
use App\Http\Requests\UserChangePass;
use App\Http\Requests\UserUpdate;
use App\Models\User;

class AuthController extends Controller
{
    public function register(UserRegister $request)
    {
        $validated = $request->validated();
        $validated['password'] = bcrypt($validated['password']);
        $validated['active'] = 0;
        $validated['level'] = 'Employee';
        $user = User::create($validated);
        return response()->json(['user' => $user, 'msg' => 'Created successfully'], 200);
    }

    public function login(UserLogin $request)
    {
        $validated = $request->validated();
        if(auth()->attempt($validated)){
            $user = auth()->user();
            $token = $user->createToken('utp')->plainTextToken;
            return response()->json(['user' => $user, 'token' => $token, 'msg' => 'Logged in successfully'], 200);
        }else{
            return response()->json(['msg' => 'Login failed'], 211);
        }
    }

    public function getMe()
    {
        $user = auth()->user();
        return response()->json(['user' => $user], 200);
    }

    public function changePassword(UserChangePass $request)
    {
        $validated = $request->validated();
        $user = auth()->user();

        if(auth()->guard('web')->attempt(['email' => $user->email,'password' => $validated['old_password']])){
            $isUpdate = User::where('id', $user->id)
                ->update(['password' => bcrypt($validated['password'])]);

            if($isUpdate){
                return response()->json(['msg' => 'Change password successfully'], 200);
            }else{
                return response()->json(['msg' => 'Change password failed'], 211);
            }
        }else{
            return response()->json(['msg' => 'Wrong password information'], 211);
        }
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();
        return response()->json(['msg' => 'Successfully logged out']);
    }

    public function updateProfile(UserUpdate $request)
    {
        $validated = $request->validated();
        $user = auth()->user();

        $isUpdate = User::where('id', $user->id)->update($validated);

        if($isUpdate){
            return response()->json(['msg' => 'Change profile successfully'], 200);
        }else{
            return response()->json(['msg' => 'Change profile failed'], 211);
        }
    }
}
