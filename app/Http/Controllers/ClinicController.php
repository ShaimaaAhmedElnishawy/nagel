<?php

namespace App\Http\Controllers;

use Illuminate\Routing\Controller as BaseController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Clinic;
use App\Models\Doctor;
use App\Models\Schedule;
use App\Http\Resources\ClinicResource;
use App\Http\Resources\ScheduleResource;


class ClinicController extends BaseController
{
    public function AddClinic(Request $request){
        try{ //name,address,phone,doctor_id,available_hours
           
           $doctor= Auth::guard('doctor')->user();
            $vaildateData = $request->validate([
                'name' => 'required|string',
                'address' => 'required|string',
                'location'=>'nullable|string',
                'phone' => 'required|string|min:7|max:15',
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


    public function AddAvailableHours(Request $request){
        
        $doctor= Auth::guard('doctor')->user();
        $vaildateData = $request->validate([            
            'available_hours' => 'required|string',
        ]);
        $vaildateData['doctor_id'] = $doctor->id;
        $schedule = Schedule::create($vaildateData);
        return response()->json(['success' => true, 'schedule' => $schedule, 'message' => 'schedule added successfully']);
    }

    public function DisplayAvailableHours(){
        $doctor = Auth::guard('doctor')->user();
        $schedules = $doctor->scheudule->all();
        if($schedules){
            return  ScheduleResource::collection($schedules);
        } else{
            return response()->json(['success' => false, 'message' => 'schedule not found']);
        }
    }

    public function EditAvailableHours(Request $request, $id){
        try {
            // Get the authenticated doctor
            $doctor = Auth::guard('doctor')->user();

            // Find the schedule by ID
            $schedule = Schedule::find($id);

            // Check if the schedule exists
            if($schedule->doctor_id !== $doctor->id){
                return response()->json([
                    'success' => false,
                    'message' => 'You are not authorized to edit this schedule',
                ], 403);
            }

            // Validate the request data
            $validateData = $request->validate([
                'available_hours' => 'sometimes|string',
            ]);

            // Update the schedule
            $schedule->update($validateData);

            return response()->json([
                'success' => true,
                'schedule' => $schedule,
                'message' => 'Schedule updated successfully',
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'An error occurred: ' . $e->getMessage(),
            ], 500);
        }
    }

    

    public function DeleteAvailableHours($id){

        // Get the authenticated doctor
        $doctor = Auth::guard('doctor')->user();

        // Find the schedule by ID
        $schedule = Schedule::find($id);

        // Check if the schedule exists
        if (!$schedule) {
            return response()->json([
                'success' => false,
                'message' => 'Schedule not found.',
            ], 404);
        }

        // Check if the schedule belongs to the authenticated doctor
        if ($schedule->doctor_id !== $doctor->id) {
            return response()->json([
                'success' => false,
                'message' => 'You are not authorized to delete this schedule.',
            ], 403);
        }

        // Delete the schedule
        $schedule->delete();

        return response()->json([
            'success' => true,
            'message' => 'Schedule deleted successfully.',
        ]);
    }

    public function DeleteClinic($id){        
        $doctor = Auth::guard('doctor')->user();
        $clinic = Clinic::find($id);
        if (!$clinic) {
            return response()->json([
                'success' => false,
                'message' => 'Clinic not found.',
            ], 404);
        }
        if($doctor->clinic->id !== $doctor->id){
            return response()->json(['success' => false, 'message' => 'You are not authorized to delete this clinic']);
        }
        $clinic->delete();
        return response()->json(['success' => true, 'message' => 'Clinic deleted successfully']);
    }

    

}
