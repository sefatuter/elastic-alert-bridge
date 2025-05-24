<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Symfony\Component\Yaml\Yaml;

class ElastAlertRulesController extends Controller
{
    protected $rulesPath;

    public function __construct()
    {
        // Rules directory path
        $this->rulesPath = '/home/sefaubuntu/elastic-alert-bridge/api/app/Services/elastalert2/rules';
    }

    /**
     * All ElastAlert rules list.
     */
    public function index()
    {
        if (!File::isDirectory($this->rulesPath)) {
            return response("Rule directory not found: " . $this->rulesPath, 404);
        }

        $files = File::glob($this->rulesPath . '/*.yaml');
        $rules = [];
        foreach ($files as $file) {
            $rules[] = basename($file);
        }

        return view('elastalert_rules.index', [
            'rules' => $rules
        ]);
    }

    /**
     * Edit a specific rule.
     */
    public function edit($filename)
    {
        $filePath = $this->rulesPath . '/' . $filename;

        if (!File::exists($filePath)) {
            return response("Rule file not found: " . $filename, 404);
        }

        $content = File::get($filePath);

        return view('elastalert_rules.edit', [
            'filename' => $filename,
            'content' => $content
        ]);
    }

    /**
     * Update a specific rule.
     */
    public function update(Request $request, $filename)
    {
        $filePath = $this->rulesPath . '/' . $filename;

        if (!File::exists($filePath)) {
            return response("Rule file not found: " . $filename, 404);
        }

        $validated = $request->validate([
            'content' => 'required|string',
        ]);

        try {
            Yaml::parse($validated['content']);
        } catch (\Symfony\Component\Yaml\Exception\ParseException $e) {
            return back()->withErrors(['yaml_error' => 'Invalid YAML format: ' . $e->getMessage()])->withInput();
        }

        File::put($filePath, $validated['content']);

        return redirect()->route('elastalert_rules.index')->with('success', $filename . ' Updated Successfully!');
    }

    /**
     * Show form for creating a new rule.
     */
    public function create()
    {
        return view('elastalert_rules.create');
    }

    /**
     * Store a new rule.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'filename' => 'required|string|regex:/^[a-zA-Z0-9_-]+$/|max:100',
            'content' => 'required|string',
        ]);

        // Ensure filename ends with .yaml
        $filename = $validated['filename'];
        if (!str_ends_with($filename, '.yaml')) {
            $filename .= '.yaml';
        }

        $filePath = $this->rulesPath . '/' . $filename;

        // Check if file already exists
        if (File::exists($filePath)) {
            return back()->withErrors(['filename' => 'A rule with this name already exists.'])->withInput();
        }

        // Validate YAML content
        try {
            Yaml::parse($validated['content']);
        } catch (\Symfony\Component\Yaml\Exception\ParseException $e) {
            return back()->withErrors(['yaml_error' => 'Invalid YAML format: ' . $e->getMessage()])->withInput();
        }

        // Create the file
        File::put($filePath, $validated['content']);

        return redirect()->route('elastalert_rules.index')->with('success', $filename . ' created successfully!');
    }
}