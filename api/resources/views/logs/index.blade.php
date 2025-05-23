<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PostgreSQL Logs</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
            background-color: #f5f5f5;
        }
        .container {
            max-width: 1200px;
            margin: 0 auto;
        }
        .log-entry {
            background-color: white;
            padding: 15px;
            margin-bottom: 10px;
            border-radius: 4px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        }
        .timestamp {
            color: #666;
            font-size: 0.9em;
            margin-bottom: 5px;
        }
        .message {
            color: #333;
            white-space: pre-wrap;
            word-break: break-word;
            font-family: monospace;
            background-color: #f8f9fa;
            padding: 10px;
            border-radius: 3px;
            margin-top: 5px;
        }
        .type {
            display: inline-block;
            padding: 2px 6px;
            background-color: #e0e0e0;
            border-radius: 3px;
            font-size: 0.8em;
            margin-right: 10px;
        }
        .log-type {
            display: inline-block;
            padding: 2px 6px;
            border-radius: 3px;
            font-size: 0.8em;
            margin-right: 10px;
        }
        .log-type.csv {
            background-color: #e3f2fd;
            color: #1565c0;
        }
        .log-type.log {
            background-color: #f3e5f5;
            color: #7b1fa2;
        }
        .file-path {
            color: #2196f3;
            font-size: 0.9em;
            margin-top: 5px;
        }
        h1 {
            color: #333;
            margin-bottom: 20px;
        }
        .error {
            background-color: #ffebee;
            color: #c62828;
            padding: 15px;
            border-radius: 4px;
            margin-bottom: 20px;
            border: 1px solid #ffcdd2;
            white-space: pre-wrap;
            word-break: break-word;
        }
        .debug-info {
            background-color: #e3f2fd;
            color: #1565c0;
            padding: 15px;
            border-radius: 4px;
            margin-bottom: 20px;
            border: 1px solid #bbdefb;
            font-family: monospace;
            white-space: pre-wrap;
            word-break: break-word;
        }
        .raw-data {
            background-color: #f5f5f5;
            padding: 15px;
            border-radius: 4px;
            margin-top: 20px;
            font-family: monospace;
            white-space: pre-wrap;
            word-break: break-word;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>PostgreSQL Logs</h1>
        
        @if(isset($error))
            <div class="error">
                <strong>Error:</strong> {{ $error }}
            </div>
            <div class="debug-info">
                <strong>Debug Information:</strong>
                <br>
                URL: http://localhost:9200/postgresql-logs-*/_search
                <br>
                Time: {{ now() }}
            </div>
        @endif

        @if(empty($logs))
            <div class="log-entry">
                <div class="message">No logs found.</div>
            </div>
        @else
            <div class="debug-info">
                <strong>Found {{ count($logs) }} logs</strong>
            </div>
            @foreach($logs as $log)
                <div class="log-entry">
                    <div class="timestamp">
                        <span class="type">{{ $log['type'] }}</span>
                        <span class="log-type {{ strtolower($log['log_type']) }}">{{ $log['log_type'] }}</span>
                        {{ $log['timestamp'] }}
                    </div>
                    @if(isset($log['file_path']))
                        <div class="file-path">File: {{ $log['file_path'] }}</div>
                    @endif
                    <div class="message">{{ $log['message'] }}</div>
                </div>
            @endforeach
        @endif
    </div>
</body>
</html> 