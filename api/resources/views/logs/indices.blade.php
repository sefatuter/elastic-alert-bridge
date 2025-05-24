<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Elasticsearch Indices - Elastic Alert Bridge</title>
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

        .indices-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
            gap: 20px;
            margin-top: 20px;
        }

        .index-card {
            background: white;
            padding: 25px;
            border-radius: 15px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.05);
            border-left: 4px solid #4a90e2;
            transition: all 0.3s ease;
            cursor: pointer;
        }

        .index-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(0,0,0,0.1);
            border-left-color: #357abd;
        }

        .index-name {
            font-size: 1.3rem;
            font-weight: 600;
            color: #2d3748;
            margin-bottom: 12px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .index-stats {
            display: flex;
            justify-content: space-between;
            margin-bottom: 15px;
        }

        .stat-item {
            text-align: center;
        }

        .stat-value {
            font-size: 1.1rem;
            font-weight: 600;
            color: #4a90e2;
        }

        .stat-label {
            font-size: 0.85rem;
            color: #6c757d;
            margin-top: 2px;
        }

        .search-button {
            width: 100%;
            padding: 12px;
            background: linear-gradient(135deg, #4a90e2, #357abd);
            color: white;
            border: none;
            border-radius: 8px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            text-decoration: none;
            display: inline-block;
            text-align: center;
        }

        .search-button:hover {
            background: linear-gradient(135deg, #357abd, #2563a8);
            transform: translateY(-1px);
            color: white;
            text-decoration: none;
        }

        .no-indices {
            text-align: center;
            padding: 60px 20px;
            color: #8c8c8c;
        }

        .no-indices i {
            font-size: 4rem;
            color: #d9d9d9;
            margin-bottom: 20px;
        }

        .no-indices h3 {
            font-size: 1.5rem;
            margin-bottom: 10px;
            color: #595959;
        }

        .indices-count {
            background: linear-gradient(135deg, #e6f7ff, #bae7ff);
            border-radius: 12px;
            padding: 20px;
            margin-bottom: 30px;
            border-left: 5px solid #1890ff;
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .indices-count i {
            color: #1890ff;
            font-size: 1.2rem;
        }

        .indices-count span {
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

            .indices-grid {
                grid-template-columns: 1fr;
                gap: 15px;
            }
        }
        
        .fade-in {
            animation: fadeIn 0.6s ease-in;
        }
        
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .filter-section {
            background: #f8fafc;
            border-radius: 12px;
            padding: 20px;
            margin-bottom: 20px;
            border: 1px solid #e2e8f0;
        }

        .filter-input {
            width: 100%;
            max-width: 400px;
            padding: 12px 40px 12px 16px;
            border: 2px solid #e2e8f0;
            border-radius: 8px;
            font-size: 1rem;
            transition: all 0.3s ease;
            background: white;
        }

        .filter-input:focus {
            outline: none;
            border-color: #4a90e2;
            box-shadow: 0 0 0 3px rgba(74, 144, 226, 0.1);
        }

        .filter-wrapper {
            position: relative;
            display: inline-block;
            width: 100%;
            max-width: 400px;
        }

        .filter-icon {
            position: absolute;
            right: 12px;
            top: 50%;
            transform: translateY(-50%);
            color: #a0aec0;
            font-size: 1rem;
        }

        .filter-label {
            display: block;
            font-weight: 600;
            color: #4a5568;
            margin-bottom: 8px;
        }
    </style>
</head>
<body>
    <div class="container fade-in">
        <div class="header">
            <h1><i class="fas fa-database"></i> Elasticsearch Indices</h1>
            <div class="subtitle">Browse and search through available indices</div>
        </div>
        
        <div class="content">
            <a href="/" class="back-button">
                <i class="fas fa-arrow-left"></i>
                Back to Dashboard
            </a>
            
            @if(session('error'))
                <div class="error-alert">
                    <i class="fas fa-exclamation-triangle"></i>
                    <span><strong>Error:</strong> {{ session('error') }}</span>
                </div>
            @endif

            @if(empty($indices))
                <div class="no-indices">
                    <i class="fas fa-database"></i>
                    <h3>No Indices Found</h3>
                    <p>No Elasticsearch indices are currently available.</p>
                </div>
            @else
                <div class="indices-count">
                    <i class="fas fa-list"></i>
                    <span>Found {{ count($indices) }} indices</span>
                </div>
                
                <div class="filter-section">
                    <label for="filter-input" class="filter-label">Filter by index name:</label>
                    <div class="filter-wrapper">
                        <input type="text" id="filter-input" class="filter-input" placeholder="Enter index name">
                        <i class="fas fa-search filter-icon"></i>
                    </div>
                </div>
                
                <div class="indices-grid">
                    @foreach($indices as $index)
                        <div class="index-card">
                            <div class="index-name">
                                <i class="fas fa-layer-group"></i>
                                {{ $index['index'] }}
                            </div>
                            
                            <div class="index-stats">
                                <div class="stat-item">
                                    <div class="stat-value">{{ number_format($index['docs.count']) }}</div>
                                    <div class="stat-label">Documents</div>
                                </div>
                                <div class="stat-item">
                                    <div class="stat-value">{{ $index['store.size'] ?? 'N/A' }}</div>
                                    <div class="stat-label">Size</div>
                                </div>
                            </div>
                            
                            <a href="{{ route('logs.search', ['index' => $index['index']]) }}" class="search-button">
                                <i class="fas fa-search"></i> Search Index
                            </a>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const filterInput = document.getElementById('filter-input');
            const indexCards = document.querySelectorAll('.index-card');
            
            filterInput.addEventListener('input', function() {
                const searchTerm = this.value.toLowerCase().trim();
                
                indexCards.forEach(function(card) {
                    const indexName = card.querySelector('.index-name').textContent.toLowerCase();
                    
                    if (searchTerm === '' || indexName.includes(searchTerm)) {
                        card.style.display = 'block';
                    } else {
                        card.style.display = 'none';
                    }
                });
            });
        });
    </script>
</body>
</html> 