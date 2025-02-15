<?php

namespace App\Http\Controllers;
use Illuminate\Routing\Controller as BaseController;
use App\Models\Patient;
use App\Models\Doctor;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthController extends BaseController {
    public function PatientRegister(Request $request) {

        try{   
                $validData=$request->validate([
                    'name' => 'required|string',
                    'email' => 'required|email|unique:patients',
                    'password' => 'required|string|confirmed',
                    'phone' => 'required|string|min:11|max:15',
                    'DOB' => 'required|date', //year-month-day
                    'address' => 'required|string'
                ]);
                        $validData['password'] = Hash::make($validData['password']);
                        $patient = Patient::create($validData);
                    return response()->json(['success' => true, 'patient' => $patient , 'message' => 'you are registered successfully']);
            }catch(\Exception $e){
                return response()->json(['success' => false, 'message' => $e->getMessage()]);
            }
    }

    public function DoctorRegister(Request $request) {
        try{
                $validData=$request->validate([
                'name' => 'required|string',
                'email' => 'required|email|unique:doctors',
                'password' => 'required|string|confirmed',
                'phone' => 'required|string|min:11|max:11',
                'speciality' => 'required|string',
                'proof' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
                ]);
                $validData['password'] = Hash::make($validData['password']);
                $doctore= Doctor::create($validData);
             return response()->json(['success' => true, 'doctore' => $doctore , 'message' => 'you are registered successfully']);
        }catch(\Exception $e){
            return response()->json(['success' => false, 'message' => $e->getMessage()]);
        }
    }

    public function PatientLogin(Request $request) {

        // $validData=$request->validate([
        //     'email' => 'required|email',
        //     'password' => 'required|string'
        // ]);

        // if(Auth::guard('patient-api')->attempt($validData)){
        //     $token = $request->user()->createToken('patient_token')->plainTextToken;
        //     return response()->json(['success' => true , 'message' => 'you are logged in successfully', 'token' => $token]);
        // }else{
        //     return response()->json(['success' => false , 'message' => ' invalid email or password '],401);
        // }

        $validData = $request->validate([
            'email' => 'required|email',
            'password' => 'required|string'
        ]);
    
        // Find patient by email
        $patient = Patient::where('email', $validData['email'])->first();
    
        // Check if patient exists and password is correct
        if ($patient && Hash::check($validData['password'], $patient->password)) {
            // Create Sanctum token
            $token = $patient->createToken('patient_token')->plainTextToken;
    
            return response()->json(['success' => true,'message' => 'You are logged in successfully','token' => $token]);
        }
    
        return response()->json([ 'success' => false,'message' => 'Invalid email or password' ], 401);

    }

    public function DoctorLogin(Request $request) {

        $validData = $request->validate([
            'email' => 'required|email',
            'password' => 'required|string'
        ]);
    
       
        $doctor = Doctor::where('email', $validData['email'])->first();
    
        // Check if doctor exists and password is correct
        if ($doctor && Hash::check($validData['password'], $doctor->password)) {
            // Create Sanctum token
            $token = $doctor->createToken('doctor_token')->plainTextToken;
    
            return response()->json([
                'success' => true,
                'message' => 'You are logged in successfully',
                'token' => $token
            ]);
        }
    
        return response()->json([
            'success' => false,
            'message' => 'Invalid email or password'
        ], 401);
    }

    public function PatientLogout(Request $request) {
        if ($request->user()) {
            $request->user()->currentAccessToken()->delete();
            return response()->json(['success' => true, 'message' => 'You are logged out successfully']);
        }
    
        return response()->json(['success' => false, 'message' => 'No authenticated user found'], 401);
    }
    
    public function DoctorLogout(Request $request) {
        if ($request->user()) {
            $request->user()->currentAccessToken()->delete();
            return response()->json(['success' => true, 'message' => 'You are logged out successfully']);
        }
    
        return response()->json(['success' => false, 'message' => 'No authenticated user found'], 401);
    }
    

}