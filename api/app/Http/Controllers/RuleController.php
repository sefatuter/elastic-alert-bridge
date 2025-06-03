<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class RuleController extends Controller
{
    private $rulesPath;
    private $integrationAuthPath;

    public function __construct()
    {
        $this->rulesPath = base_path('storage/app/elastalert2/rules');
        $this->integrationAuthPath = base_path('storage/app/elastalert2/rules');
    }

    public function showRulesPage()
    {
        return view('elasticsearch.rules', ['rulesPath' => $this->rulesPath]);
    }

    public function listRuleFiles()
    {
        try {
            if (!is_dir($this->rulesPath) || !is_readable($this->rulesPath)) {
                return response()->json(['error' => 'Rules directory is not accessible.', 'path' => $this->rulesPath], 500);
            }
            $files = scandir($this->rulesPath);
            $yamlFiles = array_filter($files, function($file) {
                return pathinfo($file, PATHINFO_EXTENSION) === 'yaml' || pathinfo($file, PATHINFO_EXTENSION) === 'yml';
            });
            return response()->json(array_values($yamlFiles));
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to list rule files: ' . $e->getMessage()], 500);
        }
    }

    public function getRuleFileContent(Request $request)
    {
        $fileName = $request->get('file');
        if (!$fileName) {
            return response()->json(['error' => 'File name parameter is required.'], 400);
        }
        $fileName = basename($fileName);
        $filePath = $this->rulesPath . DIRECTORY_SEPARATOR . $fileName;

        if (pathinfo($filePath, PATHINFO_EXTENSION) !== 'yaml' && pathinfo($filePath, PATHINFO_EXTENSION) !== 'yml') {
            return response()->json(['error' => 'Invalid file type. Only YAML files are allowed.'], 400);
        }

        try {
            if (!file_exists($filePath) || !is_readable($filePath)) {
                return response()->json(['error' => 'Rule file not found or not readable.', 'path' => $filePath], 404);
            }
            $content = file_get_contents($filePath);
            return response($content, 200)->header('Content-Type', 'text/plain');
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to read rule file: ' . $e->getMessage(), 'path' => $filePath], 500);
        }
    }
}
