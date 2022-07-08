<?php

namespace App\Http\Controllers\Api;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Hash;
use File;
//controller for changing password and for editing the user record
class ProfileController extends Controller
{ 
    
   public function change_password(Request $request){
    $validator = Validator::make($request->all(), 
        [
            'old_password' => ['required'],
            'password' => ['required', 'min:8'],
            'confirm_password'=>['required','same:password']
        ]);
        if($validator->fails()){ //when validation fails
            return response()->json([
                'message'=>'Validation fails',
                'errors'=>$validator->errors()
            ],422); 
        }
        $user=$request->user();
        if(Hash::check($request->old_password,$user->password)){
          $user->update([
            'password'=>Hash::make($request->password)//hash is used to encrypt the password 
          ]);
          return response()->json([
            'message'=>'password successfully updated',
        ],200); 
        }
        else{
            return response()->json([
                'message'=>'old password doesnt match',
            ],400); 
        }
   }
//code for editing the profile
   public function update_profile(Request $request){
    $validator = Validator::make($request->all(),
    [
        'name' => ['required', 'string', 'max:255'],
        'profile_photo'=>'nullable|image|mimes:jpg,bmp,png'
        
    ]);
    if($validator->fails()){
        return response()->json([
            'message'=>'Validation fails',
            'errors'=>$validator->errors()
        ],422);  
    }
    $user = $request->user();
    if($request->hasFile('profile_photo')){//checking if request has profie photo
        if($user->profile_photo){
            $old_path=public_path().'/uploads/profile_images/'.$user->profile_photo;
            if(File::exists($old_path)){
                File::delete($old_path);//if updating with new profile photo we need to delete the existing profile photo
            }
        }
        $image_name='profile-image'.time().'.'.$request->profile_photo->extension();
        $request->profile_photo->move(public_path('/uploads/profile_images'),$image_name);//setting up the path for uploading the profile photo
    }
    else{
        $image_name=$user->profile_photo;
    }
    $user->update([//updating user name and password
       'name'=>$request->name,
       'profile_photo'=>$image_name
    ]);
    return response()->json([
        'message'=>'profile successfully updated',
    ],200); 
   }
}
