<?php

namespace App\Http\Controllers;
use Illuminate\Routing\Controller as BaseController;
use App\Models\Doctor;
use App\Models\Clinic;
use App\Http\Resources\DoctorResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;

class DoctorController extends BaseController 
{

    public function show($id){

        $doctor = Doctor::find($id);
        if($doctor){
            return new DoctorResource($doctor);
        } else{
            return response()->json(['success' => false, 'message' => 'doctor not found']);
        }
        
    }

    public function getClinic($doctor_id){}
    // ##DATA EDITING :-
    public function editName(Request $request,$id){

        $doctor= Doctor::find($id);
        $validator=Validator::make($request->all(),[
            'name'=>'required|string|max:255',
        ]);

        if($validator->fails()){
            return response()->json($validator->errors(),400);
        }
        $doctor->name=$request->name;
        $doctor->save();
        return response()->json(['success'=>true,'message'=>'Name Updated Successfully'],200);

        
    }

    public function editEmail(Request $request,$id){

        $doctor= Doctor::find($id);
        $validator=Validator::make($request->all(),[
            'email' => 'required|string|email|max:255|unique:doctors',
        ]);

        if($validator->fails()){
            return response()->json($validator->errors(),400);
        }
        $doctor->email=$request->email;
        $doctor->save();
        return response()->json(['success'=>true,'message'=>'Email Updated Successfully'],200);
    }

    public function editPassword(Request $request,$id){

        $doctor= Doctor::find($id);
        $validator=Validator::make($request->all(),[
            'password' => 'required|string|confirmed',
        ]);

        if($validator->fails()){
            return response()->json($validator->errors(),400);
        }
        $doctor->password=Hash::make($request->password);
        $doctor->save();
        return response()->json(['success'=>true,'message'=>'Password Updated Successfully'],200);
    }

    public function editPhone(Request $request,$id){

        $doctor= Doctor::find($id);
        $validator=Validator::make($request->all(),[
            'phone' => 'required|string|min:11|max:15',
        ]); 

        if($validator->fails()){
            return response()->json($validator->errors(),400);
        }
        $doctor->phone=$request->phone;
        $doctor->save();
        return response()->json(['success'=>true,'message'=>'Phone Updated Successfully'],200);
    }



}
