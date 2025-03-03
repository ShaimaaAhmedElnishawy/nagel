<?php

namespace App\Http\Controllers;

use Illuminate\Routing\Controller as BaseController;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use App\Models\Nail_image;
use App\Models\Disease;
use App\Models\Diagnosis;

class AIController extends BaseController
{
    public function diagnose($imageId)
    {

        try {
            // 1. Get Image Record
            $nailImage = Nail_image::findOrFail($imageId);

            // 2. Verify File Exists
            $imagePath = storage_path('app/' . $nailImage->image_file);
            if (!file_exists($imagePath)) {
                throw new \Exception("Image file not found");
            }

            // 3. Send to FastAPI
            $response = Http::withHeaders([
                'ngrok-skip-browser-warning' => 'true' // If needed
            ])
                ->attach('file', file_get_contents($imagePath), basename($imagePath))
                ->post('https://d07a-102-42-86-5.ngrok-free.app/predict');

            if (!$response->successful()) {
                throw new \Exception("API request failed: " . $response->status());
            }

            $apiData = $response->json();

            // 4. Validate API Response
            if (!isset($apiData['class']) || !isset($apiData['confidence'])) {
                throw new \Exception("Invalid API response format");
            }

            // 5. Find Matching Disease
            $disease = Disease::where('name', $apiData['class'])->first();
            if (!$disease) {
                throw new \Exception("Disease not found in database: " . $apiData['class']);
            }

            // 6. Create Diagnosis Record
            Diagnosis::create([
                'image_id' => $imageId,
                'disease_id' => $disease->id,
                'date' => now(),
                'percentage' => $apiData['confidence'],
                'notes' => json_encode([
                    'probabilities' => $apiData['probabilities'],
                    'full_response' => $apiData
                ])
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Diagnosis saved successfully',
                'data' => $apiData
            ]);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json(['error' => 'Image not found'], 404);
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error("Diagnosis failed: " . $e->getMessage());
            return response()->json([
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
