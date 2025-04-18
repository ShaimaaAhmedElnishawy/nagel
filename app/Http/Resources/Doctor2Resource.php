<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Log;

class Doctor2Resource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        //return parent::toArray($request);
        
        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'phone' => $this->phone,
            'rating' => $this->rating,
            'specialization' => $this->specialization,
            'total_ratings' => $this->total_ratings,
            'photo' => $this->photo,
            'clinics' =>$this->clinic ? $this->clinic->map(function ($clinic) {
                return [
                    'clinic_id' => $clinic->id,
                    'name' => $clinic->name,
                    'address' => $clinic->address,
                    'phone' => $clinic->phone,
                    
                ];
            }):[],
            'available_hours' => $this->schedule ? $this->schedule->map(function ($schedule) {
                return $schedule->available_hours;
            }) : [],
        ];
    }
}
