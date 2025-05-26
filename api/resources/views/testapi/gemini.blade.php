<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Test Gemini API</title>
    <meta name="csrf-token" content="{{ csrf_token() }}"> {{-- CSRF token for POST requests --}}
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
            margin: 0;
            padding: 20px;
            background-color: #f4f6f8;
            color: #333;
            line-height: 1.6;
        }
        .container {
            max-width: 700px;
            margin: 20px auto;
            padding: 25px;
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        h1 {
            color: #2c3e50;
            text-align: center;
            margin-bottom: 25px;
        }
        label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: #34495e;
        }
        textarea,
        input[type='text'] {
            width: 100%;
            padding: 12px;
            margin-bottom: 20px;
            border: 1px solid #dcdfe6;
            border-radius: 4px;
            box-sizing: border-box;
            font-size: 1rem;
        }
        textarea {
            min-height: 120px;
            resize: vertical;
        }
        button {
            display: inline-block;
            background-color: #3498db;
            color: white;
            padding: 12px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 1rem;
            font-weight: 500;
            transition: background-color 0.2s ease;
        }
        button:hover {
            background-color: #2980b9;
        }
        button:disabled {
            background-color: #bdc3c7;
            cursor: not-allowed;
        }
        #resultContainer {
            margin-top: 30px;
            padding: 20px;
            background-color: #ecf0f1;
            border: 1px solid #dcdfe6;
            border-radius: 4px;
            white-space: pre-wrap; /* To respect newlines from API */
            word-wrap: break-word;
            min-height: 50px;
            font-family: monospace;
        }
        .error-message {
            color: #e74c3c;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Test Gemini API</h1>

        <form id="geminiForm">
            <div>
                <label for="apiKey">API Key (Test Only - Remove for Production)</label>
                <input type="text" id="apiKeyInput" placeholder="Enter your API Key (used by controller for this test)" value="" readonly>
                <p style="font-size:0.8em; color: #7f8c8d; margin-top:-10px; margin-bottom:20px;">Note: For this demo, the controller uses a hardcoded key. This input is illustrative.</p>
            </div>

            <div>
                <label for="prompt">Enter your prompt:</label>
                <textarea id="promptInput" rows="5" required placeholder="e.g., What is the capital of France?"></textarea>
            </div>

            <button type="submit" id="submitBtn">Send Prompt</button>
        </form>

        <h2>Result:</h2>
        <div id="resultContainer">
            <p>API response will appear here...</p>
        </div>
    </div>

    <script>
        document.getElementById('geminiForm').addEventListener('submit', async function(event) {
            event.preventDefault();
            
            const prompt = document.getElementById('promptInput').value;
            // const apiKey = document.getElementById('apiKeyInput').value; // API Key is handled server-side in this example
            const resultContainer = document.getElementById('resultContainer');
            const submitBtn = document.getElementById('submitBtn');

            resultContainer.innerHTML = 'Loading...';
            submitBtn.disabled = true;

            try {
                const response = await fetch('{{ route("api.test.gemini.prompt") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({ 
                        prompt: prompt 
                        // apiKey: apiKey // Not sending API key from client as it's handled server-side
                    })
                });

                const data = await response.json();

                if (response.ok) {
                    resultContainer.textContent = data.result;
                } else {
                    resultContainer.innerHTML = `<p class="error-message">Error: ${data.error || 'Unknown error'}</p>`;
                    if(data.details) {
                        resultContainer.innerHTML += `<pre>${JSON.stringify(data.details, null, 2)}</pre>`;
                    }
                }
            } catch (error) {
                console.error('Fetch error:', error);
                resultContainer.innerHTML = `<p class="error-message">Failed to fetch. Check console. ${error.message}</p>`;
            } finally {
                submitBtn.disabled = false;
            }
        });
    </script>
</body>
</html> 