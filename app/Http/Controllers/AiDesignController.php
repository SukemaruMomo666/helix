<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class AiDesignController extends Controller
{
    public function index()
    {
        return view('pages.ai_design');
    }

    public function generate(Request $request)
    {
        // Validasi input
        $request->validate([
            'image' => 'required|string', // Base64
            'mask' => 'nullable|string',  // Base64
            'prompt' => 'required|string|max:1000'
        ]);

        $originalImageBase64 = $this->cleanBase64($request->input('image'));
        $maskImageBase64 = $request->input('mask') ? $this->cleanBase64($request->input('mask')) : null;
        $prompt = $request->input('prompt');

        $apiKeysString = config('services.gemini.api_keys', env('GEMINI_API_KEYS', ''));
        $apiKeys = array_filter(array_map('trim', explode(',', $apiKeysString)));

        if (empty($apiKeys)) {
            return response()->json(['error' => 'API Key Gemini belum disetting di .env'], 500);
        }

        // Model rotasi (fokus ke image editing models)
        $models = ['gemini-3.1-flash-image', 'gemini-2.5-flash-image', 'imagen-4.0-generate-001'];

        $berhasil = false;
        $pesanError = '';
        $generatedImageBase64 = null;

        foreach ($apiKeys as $key) {
            foreach ($models as $model) {
                try {
                    $url = "https://generativelanguage.googleapis.com/v1beta/models/{$model}:generateContent?key={$key}";
                    
                    // Struktur payload Gemini
                    $parts = [
                        ['text' => "Task: Image Inpainting/Editing. Instruction: " . $prompt]
                    ];

                    $parts[] = [
                        'inlineData' => [
                            'mimeType' => 'image/jpeg',
                            'data' => $originalImageBase64
                        ]
                    ];

                    if ($maskImageBase64) {
                        $parts[] = [
                            'inlineData' => [
                                'mimeType' => 'image/jpeg',
                                'data' => $maskImageBase64
                            ]
                        ];
                    }

                    $payload = [
                        'contents' => [
                            ['parts' => $parts]
                        ]
                    ];

                    // Jika menggunakan Imagen 4 API, endpointnya berbeda (:predict)
                    if (str_contains($model, 'imagen')) {
                        $url = "https://generativelanguage.googleapis.com/v1beta/models/{$model}:predict?key={$key}";
                        $payload = [
                            'instances' => [
                                [
                                    'prompt' => $prompt,
                                    'image' => [
                                        'bytesBase64Encoded' => $originalImageBase64
                                    ]
                                ]
                            ],
                            'parameters' => [
                                'sampleCount' => 1
                            ]
                        ];
                    }

                    $response = Http::withoutVerifying()
                        ->timeout(60)
                        ->withHeaders(['Content-Type' => 'application/json'])
                        ->post($url, $payload);

                    if ($response->successful()) {
                        $data = $response->json();
                        
                        // Ekstrak respons gambar berdasarkan jenis model
                        if (str_contains($model, 'imagen')) {
                            $generatedImageBase64 = $data['predictions'][0]['bytesBase64Encoded'] ?? null;
                        } else {
                            // Cek di text (kadang dikembalikan sebagai base64 string di text)
                            $textResp = $data['candidates'][0]['content']['parts'][0]['text'] ?? null;
                            if ($textResp && preg_match('/^[a-zA-Z0-9\+\/]+={0,2}$/', trim($textResp)) && strlen($textResp) > 1000) {
                                $generatedImageBase64 = trim($textResp);
                            } else {
                                // Cek di inlineData (kalo standar API baru support return image part)
                                $inlineData = $data['candidates'][0]['content']['parts'][0]['inlineData']['data'] ?? null;
                                if ($inlineData) {
                                    $generatedImageBase64 = $inlineData;
                                }
                            }
                        }

                        if ($generatedImageBase64) {
                            $berhasil = true;
                            break 2; // Sukses, keluar dari loop
                        } else {
                            $pesanError = "Model merespons sukses tapi tidak mengembalikan data gambar.";
                            Log::error("Gemini AI Image Error Response: " . json_encode($data));
                            continue;
                        }

                    } else {
                        $pesanError = $response->body();
                        continue;
                    }

                } catch (\Exception $e) {
                    $pesanError = $e->getMessage();
                    continue;
                }
            }
        }

        if ($berhasil && $generatedImageBase64) {
            return response()->json([
                'success' => true,
                'image' => 'data:image/jpeg;base64,' . $generatedImageBase64
            ]);
        }

        return response()->json([
            'error' => 'Semua AI Model sedang sibuk atau limit. Error: ' . $pesanError
        ], 500);
    }

    private function cleanBase64($base64String)
    {
        if (strpos($base64String, ',') !== false) {
            $base64String = explode(',', $base64String)[1];
        }
        return $base64String;
    }
}
