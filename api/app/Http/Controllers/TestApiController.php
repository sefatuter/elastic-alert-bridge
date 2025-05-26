<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http; // Make sure to import Laravel HTTP client

class TestApiController extends Controller
{
    public function showTestPage()
    {
        return view('testapi.gemini'); // Assuming view is in resources/views/testapi/gemini.blade.php
    }

    public function handlePrompt(Request $request)
    {
        $prompt = $request->input('prompt', '');
        // API Key provided by user for this test. 
        // WARNING: Do NOT commit this directly to version control in a real project.
        // Store in .env (e.g., GEMINI_API_KEY=...) and use env('GEMINI_API_KEY') instead.
        $apiKey = ''; 

        if (empty($prompt)) {
            return response()->json(['error' => 'Prompt cannot be empty.'], 400);
        }

        if (empty($apiKey)) {
            return response()->json(['error' => 'API Key is missing. Please configure it on the server.'], 500);
        }

        // This is a common endpoint for Gemini Pro. Adjust if you use a different model.
        $geminiApiUrl = 'https://generativelanguage.googleapis.com/v1beta/models/gemini-2.0-flash:generateContent?key=' . $apiKey;

        try {
            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
            ])->post($geminiApiUrl, [
                'contents' => [
                    [
                        'parts' => [
                            ['text' => $prompt]
                        ]
                    ]
                ]
                // Add other generation config if needed, e.g.,
                // 'generationConfig' => [
                //     'temperature' => 0.7,
                //     'maxOutputTokens' => 256,
                // ]
            ]);

            if ($response->successful()) {
                // The Gemini API response structure can be nested.
                // Typically, the generated text is in response()->json()['candidates'][0]['content']['parts'][0]['text']
                $responseData = $response->json();
                $generatedText = data_get($responseData, 'candidates.0.content.parts.0.text', 'No content generated or unexpected response structure.');
                
                return response()->json(['result' => $generatedText]);
            } else {
                return response()->json(['error' => 'API call failed.', 'details' => $response->json() ?: $response->body()], $response->status());
            }
        } catch (\Illuminate\Http\Client\ConnectionException $e) {
            // Handle connection exceptions (e.g., DNS resolution, network issues)
            return response()->json(['error' => 'Connection Exception: Could not connect to the API. ' . $e->getMessage()], 500);
        } catch (\Exception $e) {
            // Handle other general exceptions
            return response()->json(['error' => 'Exception during API call: ' . $e->getMessage()], 500);
        }
    }
}
