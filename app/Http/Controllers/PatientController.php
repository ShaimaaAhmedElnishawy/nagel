<?php

namespace App\Http\Controllers;
use Illuminate\Routing\Controller as BaseController;

use Illuminate\Http\Request;
use App\Models\Patient;
use App\Models\Doctor;
use App\Models\Nail_image;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use App\Http\Resources\DoctorResource;

class PatientController extends BaseController
{
    public function uploadNailImage(Request $request){
    
        // Validate the request
        $request->validate([
            'patient_id' => 'required|exists:patients,id',
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);
    
        // Store the image
        $path = $request->file('image')->store('nail_images', 'public');
    
        // Save the image to the database
        $nailImage = Nail_image::create([
            'patient_id' => $request->patient_id,
            'image_file' => $path,
        ]);
    
        return response()->json(['success' => true, 'nailImage' => $nailImage ],201);
    }

    public function editName(Request $request,$id){

        $patient= Patient::find($id);
        $validator=Validator::make($request->all(),[
            'name' => 'required|string|max:255',
        ]);

        if($validator->fails()){
            return response()->json($validator->errors(),400);
        }
        $patient->name=$request->name;
        $patient->save();
        return response()->json(['success'=>true,'message'=>'Name Updated Successfully'],200);
    }

    public function editEmail(Request $request,$id){

        $patient= Patient::find($id);
        $validator=Validator::make($request->all(),[
            'email' => 'required|string|email|max:255|unique:patients',
        ]);

        if($validator->fails()){
            return response()->json($validator->errors(),400);
        }
        $patient->email=$request->email;
        $patient->save();
        return response()->json(['success'=>true,'message'=>'Email Updated Successfully'],200);
    }

    public function editPassword(Request $request,$id){

        $patient= Patient::find($id);
        $validator=Validator::make($request->all(),[
            'password' => 'required|string|confirmed',
        ]);

        if($validator->fails()){
            return response()->json($validator->errors(),400);
        }
        $patient->password=Hash::make($request->password);
        $patient->save();
        return response()->json(['success'=>true,'message'=>'Password Updated Successfully'],200);
    }

    public function editPhone(Request $request,$id){

        $patient= Patient::find($id);
        $validator=Validator::make($request->all(),[
            'phone' => 'required|string|min:11|max:15',
        ]);

        if($validator->fails()){
            return response()->json($validator->errors(),400);
        }
        $patient->phone=$request->phone;
        $patient->save();
        return response()->json(['success'=>true,'message'=>'Phone Updated Successfully'],200);
    }

    public function showDoctorData(){

       $doctor=Doctor::all();
       return new DoctorResource($doctor);
        
    }

}