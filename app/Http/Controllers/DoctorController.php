<?php

namespace App\Http\Controllers;
use Illuminate\Routing\Controller as BaseController;
use App\Models\Doctor;
use App\Models\Clinic;
use App\Http\Resources\DoctorResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
class DoctorController extends BaseController 
{

    public function show(){

        $doctor = Auth::guard('doctor')->user();
        if($doctor){
            return new DoctorResource($doctor);
        } else{
            return response()->json(['success' => false, 'message' => 'doctor not found']);
        }
        
    }

//     public function UploadProfilePicture(Request $request){

//         try {
//             $doctor = Auth::guard('doctor')->user();
            
//             $validData = $request->validate([
//                 'photo' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
//             ]);

//             // Delete old photo if it exists
            
//             // Store new photo
//             $file = $request->file('photo');
//             $filename = uniqid() . '.' . $file->getClientOriginalExtension();
//             $path= $file->storeAs('doctors/photos', $filename, 'public');

            

            
//             // Update doctor's photo
//             Doctor::where('id', $doctor->id)->update(['photo' => $path]);

//             return response()->json([
//                 'success' => true,
//                 'message' => 'Photo uploaded successfully',
//                 'photo_url' =>  url(Storage::url($path)) 
//             ], 200);

//         } catch (\Exception $e) {
//             return response()->json([
//                 'success' => false,
//                 'message' => $e->getMessage()
//             ], 500);
//             }
// }


    public function UploadProfilePicture(Request $request)
    {
        // Validate the request
        $request->validate([
            'photo' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        try {
            // Get the authenticated doctor
            $doctor = Auth::guard('doctor')->user();

            // Store the photo
            $path = $request->file('photo')->store('doctors', 'public');

            // Update doctor's photo
            Doctor::where('id', $doctor->id)->update(['photo' => $path]);

            return response()->json([
                'success' => true,
                'message' => 'Photo uploaded successfully',
                'photo_url' => url(Storage::url($path))
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    
    // ##DATA EDITING :-
    public function editName(Request $request){

        $doctor= Auth::guard('doctor')->user(); 
        $validator=Validator::make($request->all(),[
            'name'=>'required|string',
        ]);

        if($validator->fails()){
            return response()->json($validator->errors(),400);
        } 
        
        // $doctor->name = $request->name;
       // $doctor->save();
        Doctor::where('id', $doctor->id)->update(['name' => $request->name]);
        return response()->json(['success'=>true,'message'=>'Name Updated Successfully'],200);

        

        
    }

    public function editEmail(Request $request){

        $doctor= Auth::guard('doctor')->user(); 

        $validator=Validator::make($request->all(),[
            'email' => 'required|string|email|max:255|unique:doctors',
        ]);

        if($validator->fails()){
            return response()->json($validator->errors(),400);
        }
        Doctor::where('id', $doctor->id)->update(['email' => $request->email]);

        return response()->json(['success'=>true,'message'=>'Email Updated Successfully'],200);
    }

    public function editPassword(Request $request){

        $doctor= Auth::guard('doctor')->user(); 
        $validator = Validator::make($request->all(), [
            'current_password' => 'required|string',
            'new_password' => 'required|string|confirmed'
        ]);

        if($validator->fails()){
            return response()->json($validator->errors(),400);
        }
        if (!Hash::check($request->current_password, $doctor->password)) {
            return response()->json(['error' => 'Current password is incorrect'], 401);
        }
        Doctor::where('id', $doctor->id)->update(['password' => Hash::make($request->new_password)]); 

        return response()->json(['success'=>true,'message'=>'Password Updated Successfully'],200);
    }

    public function editPhone(Request $request){

        $doctor = Auth::guard('doctor')->user();
        $validator=Validator::make($request->all(),[
            'phone' => 'required|string|min:11|max:15',
        ]); 

        if($validator->fails()){
            return response()->json($validator->errors(),400);
        }
        Doctor::where('id', $doctor->id)->update(['phone' => $request->phone]);

        return response()->json(['success'=>true,'message'=>'Phone Updated Successfully'],200);
    }



}
