<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Elastic\Elasticsearch\ClientBuilder;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;
use App\Models\EmailIntegration;

class ElasticsearchController extends Controller
{
    private $integrationAuthPath = '/home/sefaubuntu/elastic-alert-bridge/api/app/Services/elastalert2/rules';

    // Helper method to find the correct SMTP auth file for an integration
    private function findIntegrationAuthFile($integrationId)
    {
        if (!$integrationId) {
            return null;
        }
        
        // Use the integration ID as the file number for a direct mapping
        $authFileName = "smtp_auth_file{$integrationId}.txt";
        $authFilePath = $this->integrationAuthPath . DIRECTORY_SEPARATOR . $authFileName;
        
        // Check if the file exists
        if (file_exists($authFilePath)) {
            return $authFileName;
        }
        
        return null;
    }

    public function index()
    {
        return view('elasticsearch.index');
    }

    public function createAlert()
    {
        return view('elasticsearch.create-alert');
    }    

    
} 