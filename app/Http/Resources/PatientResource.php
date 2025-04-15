<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Carbon\Carbon;

class PatientResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
       return([
        'name' => $this->name,
        'address'=>$this->address,
        'age'=>Carbon::parse($this->DOB)->age,
        'gender'=>$this->gender,
       ]);
    }
}
