<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\GeminiVisualizerService;
use Exception;

class VisualizationController extends Controller
{
    protected $visualizerService;

    public function __construct(GeminiVisualizerService $visualizerService)
    {
        $this->visualizerService = $visualizerService;
    }

    public function index()
    {
        return view('tools.visualizer');
    }

    public function visualize(Request $request)
    {
        $request->validate([
            'room_image' => 'required|string', // Base64
            'surface' => 'required|string',
            'product_description' => 'required|string',
        ]);

        try {
            $imageUrl = $this->visualizerService->generate(
                $request->room_image,
                $request->surface,
                $request->product_description
            );

            return response()->json(['success' => true, 'image_url' => $imageUrl]);

        } catch (Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }
}
