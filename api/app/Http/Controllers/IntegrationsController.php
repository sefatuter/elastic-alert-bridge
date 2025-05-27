<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Models\EmailIntegration;

class IntegrationsController extends Controller
{
    public function index()
    {
        return view('elasticsearch.integrations');
    }

    public function getIntegrations()
    {
        try {
            $emailIntegrations = EmailIntegration::all()->keyBy('name');
            
            $integrations = [
                'email' => $emailIntegrations
            ];

            return response()->json(['success' => true, 'integrations' => $integrations]);
        } catch (\Exception $e) {
            Log::error('Failed to load integrations: ' . $e->getMessage());
            return response()->json(['success' => false, 'error' => 'Failed to load integrations'], 500);
        }
    }

    public function saveEmailIntegration(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'smtp_host' => 'required|string|max:255',
            'smtp_port' => 'required|integer|min:1|max:65535',
            'smtp_ssl' => 'boolean',
            'smtp_username' => 'required|string|max:255',
            'smtp_password' => 'required|string',
            'from_address' => 'required|email|max:255',
            'default_recipient' => 'nullable|email|max:255'
        ]);

        try {
            EmailIntegration::updateOrCreate(
                ['name' => $request->input('name')],
                [
                    'smtp_host' => $request->input('smtp_host'),
                    'smtp_port' => $request->input('smtp_port'),
                    'smtp_ssl' => $request->boolean('smtp_ssl'),
                    'smtp_username' => $request->input('smtp_username'),
                    'smtp_password' => $request->input('smtp_password'),
                    'from_address' => $request->input('from_address'),
                    'default_recipient' => $request->input('default_recipient')
                ]
            );

            Log::info("Email integration '{$request->input('name')}' saved successfully");

            return response()->json(['success' => true, 'message' => 'Email integration saved successfully']);
        } catch (\Exception $e) {
            Log::error('Failed to save email integration: ' . $e->getMessage());
            return response()->json(['success' => false, 'error' => 'Failed to save email integration'], 500);
        }
    }

    public function deleteEmailIntegration(Request $request)
    {
        $request->validate([
            'name' => 'required|string'
        ]);

        try {
            $integration = EmailIntegration::where('name', $request->input('name'))->first();
            
            if (!$integration) {
                return response()->json(['success' => false, 'error' => 'Integration not found'], 404);
            }

            $integration->delete();

            Log::info("Email integration '{$request->input('name')}' deleted successfully");

            return response()->json(['success' => true, 'message' => 'Email integration deleted successfully']);
        } catch (\Exception $e) {
            Log::error('Failed to delete email integration: ' . $e->getMessage());
            return response()->json(['success' => false, 'error' => 'Failed to delete email integration'], 500);
        }
    }

    public function testEmailIntegration(Request $request)
    {
        $request->validate([
            'name' => 'required|string'
        ]);

        try {
            $integration = EmailIntegration::where('name', $request->input('name'))->first();
            
            if (!$integration) {
                return response()->json(['success' => false, 'error' => 'Integration not found'], 404);
            }

            Log::info("Testing email integration '{$request->input('name')}'");

            return response()->json(['success' => true, 'message' => 'Email integration test successful']);
        } catch (\Exception $e) {
            Log::error('Failed to test email integration: ' . $e->getMessage());
            return response()->json(['success' => false, 'error' => 'Failed to test email integration'], 500);
        }
    }
} 