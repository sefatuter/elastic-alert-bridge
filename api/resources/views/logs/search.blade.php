<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Search {{ $index }} - Elastic Alert Bridge</title>
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

        .search-form {
            background: white;
            padding: 25px;
            border-radius: 15px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.05);
            margin-bottom: 30px;
        }

        .search-controls {
            display: flex;
            gap: 15px;
            align-items: end;
        }

        .search-input-group {
            flex: 1;
        }

        .search-input-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: #2d3748;
        }

        .search-input {
            width: 100%;
            padding: 12px 15px;
            border: 2px solid #e2e8f0;
            border-radius: 8px;
            font-size: 1rem;
            transition: border-color 0.3s ease;
        }

        .search-input:focus {
            outline: none;
            border-color: #4a90e2;
        }

        .search-button {
            padding: 12px 24px;
            background: linear-gradient(135deg, #4a90e2, #357abd);
            color: white;
            border: none;
            border-radius: 8px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .search-button:hover {
            background: linear-gradient(135deg, #357abd, #2563a8);
            transform: translateY(-1px);
        }

        .index-info {
            background: linear-gradient(135deg, #e6f7ff, #bae7ff);
            border-radius: 12px;
            padding: 20px;
            margin-bottom: 30px;
            border-left: 5px solid #1890ff;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .index-name {
            display: flex;
            align-items: center;
            gap: 12px;
            font-weight: 600;
            color: #003a8c;
        }

        .search-results {
            color: #6c757d;
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
        
        .log-id {
            padding: 4px 12px;
            background-color: #f8fafc;
            color: #4a90e2;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 600;
            font-family: monospace;
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

        .pagination {
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 10px;
            margin-top: 30px;
        }

        .pagination a, .pagination span {
            padding: 10px 15px;
            border-radius: 8px;
            text-decoration: none;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .pagination a {
            background: white;
            color: #4a90e2;
            border: 2px solid #e2e8f0;
        }

        .pagination a:hover {
            background: #4a90e2;
            color: white;
            border-color: #4a90e2;
        }

        .pagination .current {
            background: #4a90e2;
            color: white;
            border: 2px solid #4a90e2;
        }

        .pagination .disabled {
            background: #f8fafc;
            color: #a0aec0;
            border: 2px solid #e2e8f0;
            cursor: not-allowed;
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

            .search-controls {
                flex-direction: column;
                align-items: stretch;
            }

            .search-button {
                justify-content: center;
            }

            .index-info {
                flex-direction: column;
                align-items: flex-start;
                gap: 10px;
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
            <h1><i class="fas fa-search"></i> Search Index</h1>
            <div class="subtitle">{{ $index }}</div>
        </div>
        
        <div class="content">
            <a href="{{ route('logs.indices') }}" class="back-button">
                <i class="fas fa-arrow-left"></i>
                Back to Indices
            </a>

            <form method="GET" action="{{ route('logs.search') }}" class="search-form">
                <input type="hidden" name="index" value="{{ $index }}">
                
                <div class="search-controls">
                    <div class="search-input-group">
                        <label for="search">Search Query</label>
                        <input type="text" 
                               id="search" 
                               name="search" 
                               class="search-input" 
                               value="{{ $searchQuery }}" 
                               placeholder="Enter search terms... (leave empty to show all documents)">
                    </div>
                    <button type="submit" class="search-button">
                        <i class="fas fa-search"></i>
                        Search
                    </button>
                </div>
            </form>

            <div class="index-info">
                <div class="index-name">
                    <i class="fas fa-layer-group"></i>
                    Index: {{ $index }}
                </div>
                <div class="search-results">
                    @if($searchQuery)
                        Search results for "{{ $searchQuery }}": {{ number_format($total) }} documents
                    @else
                        Total documents: {{ number_format($total) }}
                    @endif
                </div>
            </div>
            
            @if($error)
                <div class="error-alert">
                    <i class="fas fa-exclamation-triangle"></i>
                    <span><strong>Error:</strong> {{ $error }}</span>
                </div>
            @endif

            @if(empty($logs))
                <div class="no-logs">
                    <i class="fas fa-search"></i>
                    <h3>No Documents Found</h3>
                    @if($searchQuery)
                        <p>No documents match your search query "{{ $searchQuery }}"</p>
                    @else
                        <p>This index appears to be empty.</p>
                    @endif
                </div>
            @else
                @foreach($logs as $log)
                    <div class="log-entry">
                        <div class="log-header">
                            @if($log['timestamp'])
                                <span class="timestamp">
                                    <i class="fas fa-clock"></i>
                                    {{ $log['timestamp'] }}
                                </span>
                            @endif
                            @if($log['id'])
                                <span class="log-id">{{ $log['id'] }}</span>
                            @endif
                        </div>
                        
                        <div class="message">{{ $log['message'] }}</div>
                    </div>
                @endforeach

                @if($totalPages > 1)
                    <div class="pagination">
                        @if($currentPage > 1)
                            <a href="{{ route('logs.search', ['index' => $index, 'search' => $searchQuery, 'page' => $currentPage - 1]) }}">
                                <i class="fas fa-chevron-left"></i> Previous
                            </a>
                        @else
                            <span class="disabled">
                                <i class="fas fa-chevron-left"></i> Previous
                            </span>
                        @endif

                        <span class="current">Page {{ $currentPage }} of {{ $totalPages }}</span>

                        @if($currentPage < $totalPages)
                            <a href="{{ route('logs.search', ['index' => $index, 'search' => $searchQuery, 'page' => $currentPage + 1]) }}">
                                Next <i class="fas fa-chevron-right"></i>
                            </a>
                        @else
                            <span class="disabled">
                                Next <i class="fas fa-chevron-right"></i>
                            </span>
                        @endif
                    </div>
                @endif
            @endif
        </div>
    </div>
</body>
</html> 