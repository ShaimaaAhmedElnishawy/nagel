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
use Illuminate\Support\Facades\Http;
use App\Http\Resources\PatientResource;
use App\Http\Resources\PatientResource2;

class PatientController extends BaseController
{
    public function DisplyInfo(Request $request){
        $patient = Auth::guard('patient')->user();
        return new PatientResource($patient);
    }

    
    public function uploadNailImage(Request $request){
        
        // Validate the request (only the image is required)
        $request->validate([
            'image_file' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        // Get the authenticated patient
        $patient = Auth::guard('patient')->user();

        // Store the image
        $path = $request->file('image_file')->store('nail_images', 'public');

        // Prepare the image file for the FASTAPI request
        $imageFile = $request->file('image_file');
        $imageContent = file_get_contents($imageFile->getRealPath());

        // Send the image to the FASTAPI endpoint
        $response = Http::withOptions([
            'verify' => false, // Disable SSL verification
        ])->attach(
            'file', // The key for the file
            $imageContent, // The file content
            $imageFile->getClientOriginalName() // The file name
        )->post('https://nagel-connection2-1.onrender.com/predict/');
        
        // Check if the request was successful
        if ($response->successful()) {
            $aiResponse = $response->json();

            // Save the image and AI response to the database
            $nailImage = Nail_image::create([
                'patient_id' => $patient->id, // Use the authenticated patient's ID
                'image_file' => asset('storage/' . $path),
                'diagnosis' => $aiResponse['class'],
                'confidence' => $aiResponse['confidence'],
                'probabilities' => json_encode($aiResponse['probabilities']), // Store probabilities as JSON
            ]);

            return response()->json(['success' => true, 'nailImage' => $nailImage], 201);
        } else {
            // Handle the error
            return response()->json(['success' => false, 'message' => 'Failed to get prediction from AI model'], 500);
        }
    }

    public function showHistory(Request $request){

        $patient= Auth::guard('patient')->user();
        $nail_images= Nail_image::where('patient_id',$patient->id)->get();

        // $nail_images->transform(function ($item) {
        //     $item->image_url = asset('storage/' . $item->image_file);
        //     return $item;
        // });
        return response()->json(['success'=>true,'nail_images'=>$nail_images],200);
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

    public function editEmail(Request $request){

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

    public function editPassword(Request $request){

        $patient= Auth::guard('patient')->user();
        $validator=Validator::make($request->all(),[
            'current_password' => 'required|string',
            'new_password' => 'required|string|confirmed'
        ]);

        if($validator->fails()){
            return response()->json($validator->errors(),400);
        }
        if (!Hash::check($request->current_password, $patient->password)) {
            return response()->json(['error' => 'Current password is incorrect'], 401);
        }
        Patient::where('id',$patient->id)->update(['password'=>Hash::make($request->new_password)]);
        return response()->json(['success'=>true,'message'=>'Password Updated Successfully'],200);
    }

    public function editPhone(Request $request){

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
    public function editAddress(Request $request){

        $patient= Auth::guard('patient')->user();
        $validator=Validator::make($request->all(),[
            'address' => 'required|string',
        ]);

        if($validator->fails()){
            return response()->json($validator->errors(),400);
        }
        Patient::where('id',$patient->id)->update(['address'=>$request->address]);

        return response()->json(['success'=>true,'message'=>'address Updated Successfully'],200);
    }
    public function editBirthdate(Request $request){

        $patient= Auth::guard('patient')->user();
        $validator=Validator::make($request->all(),[
            'DOB' => 'required|string',
        ]);

        if($validator->fails()){
            return response()->json($validator->errors(),400);
        }
        Patient::where('id',$patient->id)->update(['DOB'=>$request->DOB]);

        return response()->json(['success'=>true,'message'=>'birthdate Updated Successfully'],200);
    }

    public function showDoctorData(){

       $doctors=Doctor::with(['clinic', 'schedule'])->get();
       return Doctor2Resource::collection($doctors);
        
    }

    public function rateDoctor(Request $request, $doctorId) {
        try {
            $patient = Auth::guard('patient')->user();

            if(!$patient) {
                return response()->json([
                    'success' => false, 
                    'message' => 'Patient not found.',
                ], 404);
            }
    
            $validated = $request->validate([
                'rating' => 'required|numeric|min:1|max:5',
            ]);
    
            $doctor = Doctor::findOrFail($doctorId);
    
            $newRating = $validated['rating'];
            $numberOfRatings = $doctor->number_of_ratings;
            $ratingAverage = $doctor->total_ratings;
    
            $newNumberOfRatings = $numberOfRatings + 1;
            $newRatingAverage = ($ratingAverage * $numberOfRatings + $newRating) / $newNumberOfRatings;
    
            $doctor->update([
                'rating' => $newRating,  // Save the latest rating
                'total_ratings' => $newRatingAverage,
                'number_of_ratings' => $newNumberOfRatings,
            ]);
    
            return response()->json([
                'success' => true,
                'message' => 'Rating submitted successfully.',
                'rating' => $doctor->rating,
                'total_rating' => round($doctor->total_ratings, 2),
                'number_of_ratings' => $doctor->number_of_ratings,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }
    
    public function searchByName(Request $request){

        // Validate the request
        $request->validate([
            'dr_name' => 'required|string', // Remove 'exists' rule
        ]);

        // Search for doctors with names matching the search term
        $doctors = Doctor::with(['clinic', 'schedule'])
            ->where('name', 'like', '%' . $request->dr_name . '%')
            ->get();

        // Check if any doctors were found
        if ($doctors->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'No doctors found with the specified name.',
            ], 404);
        }

        // Return the results using the custom resource
        return Doctor2Resource::collection($doctors);
    }

    public function searchByAddress(Request $request){

        // Validate the request
        $request->validate([
            'address' => 'required|string', // Ensure this matches the key in the request
        ]);

        // Search for doctors with clinics at the specified address
        $doctors = Doctor::whereHas('clinic', function ($query) use ($request) {
            $query->where('address', 'like', '%' . $request->address . '%'); // Use 'address' instead of 'clinic_address'
        })->with(['clinic', 'schedule'])->get();

        // Check if any doctors were found
        if ($doctors->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'No doctors found with the specified address.',
            ], 404);
        }

        // Return the results using the custom resource
        return Doctor2Resource::collection($doctors);
    }

    public function filterDoctorsBySpecialization(Request $request){

        // Validate the request
        $request->validate([
            'specialization' => 'required|string',
        ]);

        // Filter doctors by specialization
        $doctors = Doctor::where('specialization', $request->specialization)
            ->with(['clinic', 'schedule'])
            ->get();

        // Check if any doctors were found
        if ($doctors->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'No doctors found with the specified specialization.',
            ], 404);
        }

        // Return the results using the custom resource
        return Doctor2Resource::collection($doctors);
    }
    
}