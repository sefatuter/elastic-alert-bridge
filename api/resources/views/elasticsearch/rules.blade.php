<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ElastAlert Rules</title>
    <style>
        body {
            font-family: Inter, -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
            background-color: #f7f9fb;
            color: #343741;
            font-size: 14px;
            line-height: 1.5;
            margin: 0;
            display: flex;
            flex-direction: column;
            height: 100vh;
        }
        .rules-header {
            background: #ffffff;
            border-bottom: 1px solid #d3dae6;
            padding: 16px 24px;
            display: flex;
            align-items: center;
            box-shadow: 0 1px 3px rgba(0,0,0,0.05);
        }
        .rules-title {
            font-size: 20px;
            font-weight: 500;
            margin-left: 16px;
        }
        .back-button {
            background: #006bb4;
            color: #ffffff;
            border: none;
            border-radius: 6px;
            padding: 8px 16px;
            font-size: 14px;
            font-weight: 500;
            cursor: pointer;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }
        .back-button:hover {
            background: #005a9e;
        }
        .rules-container {
            display: flex;
            flex-grow: 1;
            overflow: hidden; /* Prevent body scroll, allow internal scroll */
        }
        .rules-sidebar {
            width: 280px;
            background: #ffffff;
            border-right: 1px solid #d3dae6;
            padding: 20px;
            overflow-y: auto;
        }
        .rules-sidebar h3 {
            font-size: 16px;
            font-weight: 600;
            color: #343741;
            margin-top: 0;
            margin-bottom: 16px;
            padding-bottom: 12px;
            border-bottom: 1px solid #f0f2f5;
        }
        .rules-list {
            list-style: none;
            padding: 0;
            margin: 0;
        }
        .rules-list-item a {
            display: block;
            padding: 10px 12px;
            text-decoration: none;
            color: #343741;
            border-radius: 4px;
            margin-bottom: 4px;
            font-weight: 500;
        }
        .rules-list-item a:hover {
            background-color: #f0f2f5;
            color: #006bb4;
        }
        .rules-list-item a.selected {
            background-color: #e7f3ff;
            color: #006bb4;
            font-weight: 600;
        }
        .rules-content-area {
            flex-grow: 1;
            padding: 24px;
            background-color: #ffffff;
            overflow-y: auto;
        }
        .rules-content-area pre {
            background-color: #f7f9fb;
            border: 1px solid #d3dae6;
            padding: 16px;
            border-radius: 6px;
            white-space: pre-wrap; /* Handles long lines */
            word-wrap: break-word; /* Ensures words break to fit */
            font-family: 'Monaco', 'Menlo', 'Ubuntu Mono', monospace;
            font-size: 13px;
            margin: 0;
        }
        .no-rule-selected {
            color: #69707d;
            font-size: 16px;
            text-align: center;
            margin-top: 40px;
        }
    </style>
</head>
<body>
    <div class="rules-header">
        <a href="{{ route('elasticsearch.index') }}" class="back-button">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor"><path d="M20,11V13H8L13.5,18.5L12.08,19.92L4.16,12L12.08,4.08L13.5,5.5L8,11H20Z"/></svg>
            Back
        </a>
        <h1 class="rules-title">ElastAlert Rules</h1>
    </div>
    <div class="rules-container">
        <div class="rules-sidebar">
            <h3>Rule Files</h3>
            <ul class="rules-list" id="rulesList">
                {{-- Rule files will be listed here by JavaScript --}}
                <li class="no-rule-selected" style="text-align:left; padding:10px 0;">Loading rules...</li>
            </ul>
        </div>
        <div class="rules-content-area">
            <pre id="ruleContentDisplay"><p class="no-rule-selected">Select a rule file from the left to view its content.</p></pre>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const rulesListUl = document.getElementById('rulesList');
            const ruleContentDisplayPre = document.getElementById('ruleContentDisplay');
            let currentSelectedFileElement = null;

            async function loadRuleFiles() {
                rulesListUl.innerHTML = '<li class="no-rule-selected" style="text-align:left; padding:10px 0;">Loading rules...</li>'; // Set loading message upfront

                try {
                    const response = await fetch('{{ route("api.elasticsearch.rules.list") }}');
                    
                    let filesData;
                    // Try to parse JSON regardless of response.ok, as error responses from our controller are JSON
                    try {
                        filesData = await response.json();
                    } catch (jsonError) {
                        console.error('JSON parsing error:', jsonError);
                        const responseText = await response.text().catch(() => 'Could not read response text.'); 
                        throw new Error(`Failed to parse server response. Status: ${response.status}. Server sent: ${responseText.substring(0,200)}...`);
                    }

                    if (!response.ok) {
                        let errorMsg = filesData.error || `HTTP error! Status: ${response.status}`;
                        if (filesData.path) {
                            errorMsg += ` (Path: ${filesData.path})`;
                        }
                        throw new Error(errorMsg);
                    }
                    
                    rulesListUl.innerHTML = ''; // Clear loading message

                    // filesData should now be the actual array of filenames or an object with an error key handled by the !response.ok block above
                    if (!Array.isArray(filesData)) {
                        // This case should ideally be caught by !response.ok if the controller sends an error object.
                        // But as a fallback if filesData is an object without an error key from a 200 OK response.
                        console.error('Expected an array of files, but received:', filesData);
                        rulesListUl.innerHTML = `<li class="no-rule-selected" style="color:red; text-align:left; padding:10px 0;">Error: Received invalid data format from server.</li>`;
                        return;
                    }

                    if (filesData.length === 0) {
                        rulesListUl.innerHTML = '<li class="no-rule-selected" style="text-align:left; padding:10px 0;">No rule files found in the directory.</li>';
                        return;
                    }

                    filesData.forEach(fileName => {
                        const listItem = document.createElement('li');
                        listItem.classList.add('rules-list-item');
                        const link = document.createElement('a');
                        link.href = '#';
                        link.textContent = fileName;
                        link.dataset.fileName = fileName;
                        link.addEventListener('click', async function(e) {
                            e.preventDefault();
                            await loadRuleContent(fileName, this);
                        });
                        listItem.appendChild(link);
                        rulesListUl.appendChild(listItem);
                    });

                    const urlParams = new URLSearchParams(window.location.search);
                    const selectedFileFromUrl = urlParams.get('selected_file');
                    if (selectedFileFromUrl) {
                        const linkToSelect = rulesListUl.querySelector(`a[data-file-name="${selectedFileFromUrl}"]`);
                        if (linkToSelect) {
                            await loadRuleContent(selectedFileFromUrl, linkToSelect);
                        } else {
                            console.warn(`Selected file '${selectedFileFromUrl}' from URL not found in the list.`);
                        }
                    }

                } catch (error) { 
                    console.error('Failed to load rule files (outer catch):', error);
                    rulesListUl.innerHTML = `<li class="no-rule-selected" style="color:red; text-align:left; padding:10px 0;">Failed to load rules: ${error.message}. Check console.</li>`;
                }
            }

            async function loadRuleContent(fileName, linkElement) {
                let displayElement = ruleContentDisplayPre;
                if (!displayElement) { 
                    const contentArea = document.querySelector('.rules-content-area');
                    if (contentArea) {
                        displayElement = document.createElement('pre'); // Ensure it's a pre for consistency
                        displayElement.id = 'ruleContentDisplay'; // Give it the id if creating
                        contentArea.innerHTML = ''; 
                        contentArea.appendChild(displayElement);
                    } else {
                        console.error('.rules-content-area not found!');
                        alert('Error: Content display area not found.');
                        return;
                    }
                }
                displayElement.textContent = 'Loading content...';
                
                if (currentSelectedFileElement) {
                    currentSelectedFileElement.classList.remove('selected');
                }
                linkElement.classList.add('selected');
                currentSelectedFileElement = linkElement;

                try {
                    const response = await fetch(`{{ route("api.elasticsearch.rules.content") }}?file=${encodeURIComponent(fileName)}`);
                    if (!response.ok) {
                        const errorData = await response.json().catch(() => ({error: response.statusText}) ); // Try to parse error, fallback
                        throw new Error(`HTTP error! status: ${response.status}. ${errorData.error || 'Failed to fetch content.'}`);
                    }
                    const content = await response.text();
                    displayElement.textContent = content;
                } catch (error) {
                    console.error(`Failed to load content for ${fileName}:`, error);
                    displayElement.textContent = `Error loading ${fileName}:\n${error.message}`
                }
            }

            loadRuleFiles();
        });
    </script>
</body>
</html>