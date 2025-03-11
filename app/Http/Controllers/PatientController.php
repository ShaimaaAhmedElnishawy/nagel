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
use App\Http\Resources\Doctor2Resource;

class PatientController extends BaseController
{
    public function uploadNailImage(Request $request){
    
        // Validate the request
        $request->validate([
            'patient_id' => 'required|exists:patients,id',
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        $patient=Auth::guard('patient')->user();
    
        // Store the image
        $path = $request->file('image')->store('nail_images', 'public');
    
        // Save the image to the database
        $nailImage = Nail_image::create([
            'patient_id' => $patient->id,
            'image_file' => $path,
        ]);
    
        return response()->json(['success' => true, 'nailImage' => $nailImage ],201);
    }

    public function editName(Request $request){

        $patient= Auth::guard('patient')->user();
        $validator=Validator::make($request->all(),[
            'name' => 'required|string|max:255',
        ]);

        if($validator->fails()){
            return response()->json($validator->errors(),400);
        }
        Patient::where('id',$patient->id)->update(['name'=>$request->name]);
        return response()->json(['success'=>true,'message'=>'Name Updated Successfully'],200);
    }

    public function editEmail(Request $request,$id){

        $patient= Auth::guard('patient')->user();
        $validator=Validator::make($request->all(),[
            'email' => 'required|string|email|max:255|unique:patients',
        ]);

        if($validator->fails()){
            return response()->json($validator->errors(),400);
        }
        Patient::where('id',$patient->id)->update(['email'=>$request->email]);
        return response()->json(['success'=>true,'message'=>'Email Updated Successfully'],200);
    }

    public function editPassword(Request $request,$id){

        $patient= Auth::guard('patient')->user();
        $validator=Validator::make($request->all(),[
            'password' => 'required|string|confirmed',
        ]);

        if($validator->fails()){
            return response()->json($validator->errors(),400);
        }
        if (!Hash::check($request->current_password, $patient->password)) {
            return response()->json(['error' => 'Current password is incorrect'], 401);
        }
        Patient::where('id',$patient->id)->update(['password'=>Hash::make($request->password)]);
        return response()->json(['success'=>true,'message'=>'Password Updated Successfully'],200);
    }

    public function editPhone(Request $request,$id){

        $patient= Auth::guard('patient')->user();
        $validator=Validator::make($request->all(),[
            'phone' => 'required|string|min:11|max:15',
        ]);

        if($validator->fails()){
            return response()->json($validator->errors(),400);
        }
        Patient::where('id',$patient->id)->update(['phone'=>$request->phone]);

        return response()->json(['success'=>true,'message'=>'Phone Updated Successfully'],200);
    }

    public function showDoctorData(){

       $doctors=Doctor::with(['clinic', 'schedule'])->get();
       return Doctor2Resource::collection($doctors);
        
    }

    public function rateDoctor(Request $request, $doctorId) {
        try {
            $patient = Auth::guard('patient')->user();
    
            $validated = $request->validate([
                'rateing' => 'required|numeric|min:1|max:5', // Rating from 1 to 5
            ]);
    
            $doctor = Doctor::findOrFail($doctorId);
    
            // Calculate new average rating
            $currentRateing = $doctor->rateing;
            $totalRateings = $doctor->total_rateings;
            $newRateing = ($currentRateing * $totalRateings + $validated['rateing']) / ($totalRateings + 1);
    
            // Update doctor's rating
            $doctor->rateing = $newRateing;
            $doctor->total_rateings += 1; // Increment total ratings
            $doctor->save();
    
            return response()->json([
                'success' => true,
                'message' => 'Rating submitted successfully.',
                'new_rateing' => $doctor->rateing,
            ]);
    
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

}