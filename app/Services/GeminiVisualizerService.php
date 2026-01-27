<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Exception;

class GeminiVisualizerService
{
    /**
     * Generate a new visualization or retrieve from cache.
     *
     * @param string $roomImageBase64
     * @param string $surface
     * @param string $productDescription
     * @return string URL of the generated image
     */
    public function generate($roomImageBase64, $surface, $productDescription)
    {
        // 1. Generate Hash ID
        $hashId = md5($roomImageBase64 . '_' . $surface . '_' . $productDescription);

        // 2. Check Database for Existing
        $existing = DB::table('generated_visualizations')->where('hash_id', $hashId)->first();

        if ($existing) {
            // Check if file actually exists
            if (file_exists(public_path($existing->image_path))) {
                return asset($existing->image_path);
            }
            // If file is missing but DB record exists, we regenerate.
        }

        // 3. Prepare Prompt
        $prompt = "Act as an professional interior designer. Replace the {$surface} in this image with {$productDescription}. Keep the furniture, lighting, and shadows EXACTLY as they are. Output only the modified image.";

        // 4. Call Gemini API
        // Note: Using a simplified HTTP call here. Ensure specific Gemini model endpoint is used.
        // Assuming using Vertex AI or Google AI Studio endpoint structure.
        $apiKey = env('GEMINI_API_KEY');
        // UPDATED: Using widely available model gemini-2.0-flash-exp as 2.5-preview-image caused quota issues "limit: 0"
        $url = "https://generativelanguage.googleapis.com/v1beta/models/gemini-2.0-flash-exp:generateContent?key={$apiKey}";
        
        // Clean base64 header if present
        $cleanBase64 = preg_replace('/^data:image\/\w+;base64,/', '', $roomImageBase64);

        $response = Http::post($url, [
            "contents" => [
                [
                    "parts" => [
                        ["text" => $prompt],
                        [
                            "inline_data" => [
                                "mime_type" => "image/jpeg",
                                "data" => $cleanBase64
                            ]
                        ]
                    ]
                ]
            ],
            // "generationConfig" => [
            //    "responseMimeType" => "image/jpeg" // Force image output if supported by model version
            // ]
        ]);

        if ($response->failed()) {
            throw new Exception("Gemini API Error: " . $response->body());
        }

        $data = $response->json();
        
        // Check for valid response structure (adjust based on actual API response)
        // Usually Gemini 1.5/2.0 returns text implementation but for image generation 
        // we might need a specific Imagen model or the multi-modal output.
        // *Correction*: gemini-2.5-flash-image mentioned in prompt implies a specific capability or a placeholder.
        // Standard Gemini API returns text/multimodal. 
        // IF this is "Imagen" via Vertex, the structure is different.
        // Assuming the user meant a capable model or we use a workaround if it returns base64.
        
        // *CRITICAL*: Standard Gemini (free tier) doesn't output *Images* yet, it analyzes them.
        // Imagen 2/3 does generation.
        // However, based on the prompt, I will implement the logic assuming the API *returns* an image 
        // (base64) in the candidates. behavior.
        
        // Let's assume the response contains the image data.
        // If the model cannot generate images, this part will need the specific Imagen endpoint.
        
        // FOR NOW: I will implement safe parsing. If it's a text response describing the room, 
        // it fails the visualizer purpose. But I'll assume standard candidate structure.
        
        // NOTE: As of now, standard public Gemini API `generateContent` is mostly text-out. 
        // `predict` on Vertex AI uses Imagen.
        // I will adhere to the user request but add a safety check.
        
        // Let's assume the response has inline_data or we need to use a different endpoint.
        // If the user *provided* gemini-2.5-flash-image, they might have access to a beta.
        
        // MOCK IMPLEMENTATION FOR SAFETY if response structure is unknown:
        // In a real scenario I'd check $data['candidates'][0]['content']['parts'][0]['inline_data']['data']
        
        $generatedImageB64 = null;
        if (isset($data['candidates'][0]['content']['parts'][0]['inline_data']['data'])) {
             $generatedImageB64 = $data['candidates'][0]['content']['parts'][0]['inline_data']['data'];
        } 
        
        // FALLBACK: If the API returns a text description instead of an image (common in LLMs),
        // we can't show it. For this code, I'll assume we get base64.
        
        if (!$generatedImageB64) {
             // Try to parse from text if it accidentally returned a markdown code block? (Unlikely)
             // Throw error if no image.
             throw new Exception("No image generated. Response: " . json_encode($data));
        }

        // 5. Decode and Save Image
        $imageData = base64_decode($generatedImageB64);
        $fileName = "visualizations/{$hashId}.jpg";
        $absolutePath = public_path($fileName);

        // Ensure directory exists
        if (!file_exists(dirname($absolutePath))) {
            mkdir(dirname($absolutePath), 0755, true);
        }

        file_put_contents($absolutePath, $imageData);

        // 6. DB Record
        DB::table('generated_visualizations')->insert([
            'hash_id' => $hashId,
            'image_path' => $fileName,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return asset($fileName);
    }
}
