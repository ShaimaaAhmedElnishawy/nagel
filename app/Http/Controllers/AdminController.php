<?php

namespace App\Http\Controllers;

use Illuminate\Routing\Controller as BaseController;
use Illuminate\Http\Request;
use App\Models\Doctor;
use App\Models\Admin;

class AdminController extends BaseController
{
    //

    public function showPendedDoctors(){

        $doctors = Doctor::where('status','pending')->get();
        return response()->json($doctors);
    }

    public function approveDoctor(Request $request,$doctor_id){

        $request->validate([
            'status'=>'required|in:approved',
        ]);

        $doctor = Doctor::findorFail($doctor_id);

        if($doctor && $doctor->status == 'pending'){

            $doctor->status = $request->status;
            $doctor->save();
            return response()->json(['success'=>true,'message'=>'Doctor approved Successfully'],200);
        }

        else{
            return response()->json(['success'=>false,'message'=>'Doctor Not Found'],404);
        }
    }


    public function rejectDoctor(Request $request,$doctor_id){

        $request->validate([
            'status'=>'required|in:rejected',
        ]);

        $doctor = Doctor::findorFail($doctor_id);

        if($doctor && $doctor->status == 'pending'){

            $doctor->status = $request->status;
            $doctor->save();
            return response()->json(['success'=>true,'message'=>'Doctor rejected Successfully'],200);
        }

        else{
            return response()->json(['success'=>false,'message'=>'Doctor Not Found'],404);
        }
    }

    
}
