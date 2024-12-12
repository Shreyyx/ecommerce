<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Validator;
use Exception;
use Auth;
use App\Models\User;

class AuthController extends Controller
{
    //
    public function register(Request $request){
        //validation
        $validator= Validator::make($request->all(),[
            'name' => 'required',
            'email' => 'required',
            'password' => 'required',
            'c_password' => 'required|same:password',
        ]);

        if($validator->fails()){
            $response =[
                'success' => false,
                'message' => $validator->errors()
            ];
            return response()->json($response, 400);
        }

        $input= $request->all();
        $input['password'] =bcrypt($input['password']);
        $user= User::create($input);

        $success['token'] = $user->createToken('MyApp')->plainTextToken;
        $success['name'] = $user->name;

        $response = [
            'success' => true,
            'data' => $success,
            'message' => 'User registered successfully'
        ];
        
        return response()->json($response,200);
    }

    public function login(Request $request){
        if(Auth::attempt(['email'=>$request->email,'password'=>$request->password])){
            $user = Auth::user();
            $success['token'] = $user->createToken('MyApp')->plainTextToken;
            $success['name'] = $user->name;
            
            $response = [
                'success' => true,
                'data' =>$success,
                'message' => 'User login successfully'
            ];
            return response()->json($response,200);
        }
        else{
            $response = [
                'success' => false,
                'message' => 'Unauthorised'
            ];
            return response()->json($response);
        }
    }

    public function logout(Request $request)
{
    if (Auth::check()) {
        $request->user()->currentAccessToken()->delete();

        $response = [
            'success' => true,
            'message' => 'User logged out successfully'
        ];

        return response()->json($response, 200);
    } else {
        $response = [
            'success' => false,
            'message' => 'Unauthorised'
        ];

        return response()->json($response, 401);
    }
}

}