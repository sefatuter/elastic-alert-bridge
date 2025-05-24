<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PostgreSQL Logs - Elastic Alert Bridge</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body { 
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; 
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
            min-height: 100vh;
            padding: 20px;
            color: #333;
        }
        
        .container { 
            max-width: 1200px;
            margin: 0 auto;
            background: rgba(255, 255, 255, 0.95);
            border-radius: 20px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.1);
            backdrop-filter: blur(10px);
            overflow: hidden;
        }
        
        .header {
            background: linear-gradient(135deg, #4a90e2 0%, #357abd 100%);
            color: white;
            padding: 30px 40px;
            text-align: center;
            position: relative;
        }
        
        .header h1 { 
            font-size: 2.5rem;
            font-weight: 300;
            margin-bottom: 10px;
        }
        
        .header .subtitle {
            font-size: 1.1rem;
            opacity: 0.9;
        }
        
        .content {
            padding: 40px;
        }
        
        .back-button {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            color: #4a90e2;
            text-decoration: none;
            font-weight: 600;
            padding: 12px 20px;
            border-radius: 10px;
            background: rgba(74, 144, 226, 0.1);
            transition: all 0.3s ease;
            margin-bottom: 30px;
        }
        
        .back-button:hover {
            background: rgba(74, 144, 226, 0.2);
            transform: translateX(-5px);
            text-decoration: none;
            color: #357abd;
        }
        
        .log-entry {
            background: white;
            padding: 20px;
            margin-bottom: 15px;
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.05);
            border-left: 4px solid #4a90e2;
            transition: all 0.3s ease;
        }
        
        .log-entry:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(0,0,0,0.1);
        }
        
        .log-header {
            display: flex;
            align-items: center;
            gap: 12px;
            margin-bottom: 12px;
            flex-wrap: wrap;
        }
        
        .timestamp {
            color: #6c757d;
            font-size: 0.9rem;
            font-weight: 500;
        }
        
        .log-type {
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 600;
            text-transform: uppercase;
        }
        
        .log-type.csv {
            background-color: #e3f2fd;
            color: #1565c0;
        }
        
        .log-type.log {
            background-color: #f3e5f5;
            color: #7b1fa2;
        }
        
        .type-badge {
            padding: 4px 12px;
            background-color: #e9ecef;
            color: #495057;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 600;
        }
        
        .file-path {
            color: #4a90e2;
            font-size: 0.85rem;
            margin-bottom: 8px;
            display: flex;
            align-items: center;
            gap: 6px;
        }
        
        .message {
            color: #2d3748;
            font-family: 'Fira Code', 'Monaco', 'Cascadia Code', monospace;
            background-color: #f8fafc;
            padding: 15px;
            border-radius: 8px;
            white-space: pre-wrap;
            word-break: break-word;
            line-height: 1.5;
            font-size: 0.9rem;
        }
        
        .error-alert {
            background: linear-gradient(135deg, #ff6b6b, #ee5a52);
            color: white;
            padding: 20px;
            border-radius: 12px;
            margin-bottom: 30px;
            display: flex;
            align-items: center;
            gap: 12px;
        }
        
        .error-alert i {
            font-size: 1.2rem;
        }
        
        .no-logs {
            text-align: center;
            padding: 60px 20px;
            color: #8c8c8c;
        }
        
        .no-logs i {
            font-size: 4rem;
            color: #d9d9d9;
            margin-bottom: 20px;
        }
        
        .no-logs h3 {
            font-size: 1.5rem;
            margin-bottom: 10px;
            color: #595959;
        }
        
        .logs-count {
            background: linear-gradient(135deg, #e6f7ff, #bae7ff);
            border-radius: 12px;
            padding: 20px;
            margin-bottom: 30px;
            border-left: 5px solid #1890ff;
            display: flex;
            align-items: center;
            gap: 12px;
        }
        
        .logs-count i {
            color: #1890ff;
            font-size: 1.2rem;
        }
        
        .logs-count span {
            color: #003a8c;
            font-weight: 600;
        }
        
        @media (max-width: 768px) {
            .container {
                margin: 10px;
                border-radius: 15px;
            }
            
            .header {
                padding: 20px;
            }
            
            .header h1 {
                font-size: 2rem;
            }
            
            .content {
                padding: 20px;
            }
            
            .log-header {
                flex-direction: column;
                align-items: flex-start;
                gap: 8px;
            }
        }
        
        .fade-in {
            animation: fadeIn 0.6s ease-in;
        }
        
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
    </style>
</head>
<body>
    <div class="container fade-in">
        <div class="header">
            <h1><i class="fas fa-file-alt"></i> PostgreSQL Logs</h1>
            <div class="subtitle">System log monitoring and analysis</div>
        </div>
        
        <div class="content">
            <a href="/" class="back-button">
                <i class="fas fa-arrow-left"></i>
                Back to Dashboard
            </a>
            
            @if(isset($error))
                <div class="error-alert">
                    <i class="fas fa-exclamation-triangle"></i>
                    <span><strong>Error:</strong> {{ $error }}</span>
                </div>
            @endif

            @if(empty($logs))
                <div class="no-logs">
                    <i class="fas fa-inbox"></i>
                    <h3>No Logs Found</h3>
                    <p>No PostgreSQL logs are currently available.</p>
                </div>
            @else
                <div class="logs-count">
                    <i class="fas fa-list"></i>
                    <span>Found {{ count($logs) }} log entries</span>
                </div>
                
                @foreach($logs as $log)
                    <div class="log-entry">
                        <div class="log-header">
                            <span class="timestamp">
                                <i class="fas fa-clock"></i>
                                {{ $log['timestamp'] }}
                            </span>
                            <span class="type-badge">{{ $log['type'] }}</span>
                            <span class="log-type {{ strtolower($log['log_type']) }}">{{ $log['log_type'] }}</span>
                        </div>
                        
                        @if(isset($log['file_path']))
                            <div class="file-path">
                                <i class="fas fa-file"></i>
                                {{ $log['file_path'] }}
                            </div>
                        @endif
                        
                        <div class="message">{{ $log['message'] }}</div>
                    </div>
                @endforeach
            @endif
        </div>
    </div>
</body>
</html> 