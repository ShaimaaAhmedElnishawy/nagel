<?php

namespace App\Http\Controllers;

use Illuminate\Routing\Controller as BaseController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Clinic;
use App\Models\Doctor;
use App\Http\Resources\ClinicResource;


class ClinicController extends BaseController
{
    public function AddClinic(Request $request){
        try{ //name,address,phone,doctor_id,available_hours
           
           $doctor= Auth::guard('doctor')->user();
            $vaildateData = $request->validate([
                'name' => 'required|string',
                'address' => 'required|string',
                'phone' => 'required|string|min:11|max:15',
                //'available_hours' => 'required|string',
            ]);
            $vaildateData['doctor_id'] = $doctor->id;
            $clinic = Clinic::create($vaildateData);
            return response()->json(['success' => true, 'clinic' => $clinic, 'message' => 'clinic added successfully']);
        } catch(\Exception $e){
            return response()->json(['success' => false, 'message' => $e->getMessage()]);
        }
    }

    public function DisplayClinics(){
        $doctor = Auth::guard('doctor')->user();
        $clinics = $doctor->clinic->all();
        if($clinics){
            return ClinicResource::collection($clinics);
        } else{
            return response()->json(['success' => false, 'message' => 'clinic not found']);
        }
    }

    
    public function EditClinic(Request $request, $id)
    {
        try {
            // Get the authenticated doctor
            $doctor = Auth::guard('doctor')->user();

            // Find the clinic by ID
            $clinic = Clinic::find($id);

            // Check if the clinic exists
            if (!$clinic) {
                return response()->json([
                    'success' => false,
                    'message' => 'Clinic not found',
                ], 404);
            }

            // Ensure the authenticated doctor owns the clinic
            if ($clinic->doctor_id !== $doctor->id) {
                return response()->json([
                    'success' => false,
                    'message' => 'You are not authorized to edit this clinic',
                ], 403);
            }

            // Validate the request data
            $validateData = $request->validate([
                'name' => 'sometimes|string',
                'address' => 'sometimes|string',
                'phone' => 'sometimes|string|min:11|max:15',
                //'available_hours' => 'sometimes|string',
            ]);

            // Update the clinic
            $clinic->update($validateData);

            return response()->json([
                'success' => true,
                'clinic' => $clinic,
                'message' => 'Clinic updated successfully',
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'An error occurred: ' . $e->getMessage(),
            ], 500);
        }
    }

    

}
