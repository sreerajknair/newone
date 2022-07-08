<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\User;
use Hash;
class AuthController extends Controller
{ 
    //Add User details
    public function add(Request $request){
        // $request->validate([
        //     'name' => ['required', 'string', 'max:255'],
        //     'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
        //     'password' => ['required', 'string', 'min:8', 'confirmed'],

        // ]);

        //validation of fields
        $validator = Validator::make($request->all(),
        [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8'],
            'confirm_password'=>['required','same:password']
            
            
        ]);
        if($validator->fails()){
            return response()->json([
                'message'=>'Validation fails',
                'errors'=>$validator->errors()
            ],422);  
        }
        


        //newly created users record is assigned to user
        $user=User::create([
            'name'=>$request->name,
            'email'=>$request->email,
            'password'=>Hash::make($request->password),
            
        ]);
        return response()->json([
            'message'=>'Adding succesfull',
            'data'=>$user
        ],200);

    }
    //code for logging in with added users
    public  function login(Request $request){
        $validator = Validator::make($request->all(),
        [
            'email' => ['required', 'email' ],
            'password' => ['required'],
        ]);
        if($validator->fails()){
            return response()->json([
                'message'=>'Validation fails',
                'errors'=>$validator->errors()
            ],422);  
        }
        $user=User::where('email',$request->email)->first();//checking the email
        if($user){
           if(Hash::check($request->password, $user->password)){//if email is valid password is checked

            $token=$user->createToken('auth-token')->plainTextToken;//generating token for authentication purpose
            return response()->json([
                'message'=>'login succesfull',
                'token'=>$token,
                'data'=>$user
                
            ],200);  

           }else{
            return response()->json([
                'message'=>'incorrect credentials',
                
            ],422);  
           }
        }else{
            return response()->json([
                'message'=>'incorrect credentials',
                
            ],422);  
        }
    }
    //code for fetching personal detais
    public function user(Request $request){

        return response()->json([
            'message'=>'User succesfully fetched',
            'data'=>$request->user()
            ],200); 

        
    }
}
