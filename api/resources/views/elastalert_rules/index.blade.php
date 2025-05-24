<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ElastAlert Rules - Management Dashboard</title>
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
            padding: 40px;
            text-align: center;
            position: relative;
        }
        
        .header::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><defs><pattern id="grain" width="100" height="100" patternUnits="userSpaceOnUse"><circle cx="25" cy="25" r="1" fill="white" fill-opacity="0.1"/><circle cx="75" cy="75" r="1" fill="white" fill-opacity="0.1"/></pattern></defs><rect width="100" height="100" fill="url(%23grain)"/></svg>');
            opacity: 0.3;
        }
        
        .header h1 { 
            font-size: 2.8rem;
            font-weight: 300;
            margin-bottom: 10px;
            position: relative;
            z-index: 1;
        }
        
        .header .subtitle {
            font-size: 1.2rem;
            opacity: 0.9;
            position: relative;
            z-index: 1;
        }
        
        .content {
            padding: 40px;
        }
        
        .alert-success { 
            background: linear-gradient(135deg, #52c41a, #389e0d);
            color: white;
            padding: 20px;
            border-radius: 12px;
            margin-bottom: 30px;
            border: none;
            box-shadow: 0 8px 16px rgba(82, 196, 26, 0.3);
            display: flex;
            align-items: center;
            gap: 15px;
        }
        
        .alert-success i {
            font-size: 1.5rem;
        }
        
        .stats-bar {
            background: linear-gradient(135deg, #e6f7ff, #bae7ff);
            border-radius: 12px;
            padding: 20px;
            margin-bottom: 30px;
            border-left: 5px solid #1890ff;
        }
        
        .stats-bar h3 {
            color: #003a8c;
            margin-bottom: 10px;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .stats-info {
            display: flex;
            gap: 30px;
            flex-wrap: wrap;
            margin-top: 15px;
        }
        
        .stat-item {
            background: rgba(255, 255, 255, 0.8);
            padding: 15px 20px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            gap: 10px;
            box-shadow: 0 4px 8px rgba(0,0,0,0.05);
        }
        
        .stat-item i {
            color: #1890ff;
            font-size: 1.2rem;
        }
        
        .stat-item span {
            font-weight: 600;
            color: #003a8c;
        }
        
        .rules-grid {
            display: grid;
            gap: 20px;
            grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
        }
        
        .rule-card {
            background: white;
            border-radius: 15px;
            padding: 25px;
            box-shadow: 0 8px 25px rgba(0,0,0,0.08);
            transition: all 0.3s ease;
            border: 2px solid transparent;
            position: relative;
            overflow: hidden;
        }
        
        .rule-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(90deg, #4a90e2, #357abd);
        }
        
        .rule-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 35px rgba(0,0,0,0.12);
            border-color: #4a90e2;
        }
        
        .rule-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 20px;
        }
        
        .rule-name {
            display: flex;
            align-items: center;
            gap: 12px;
            flex: 1;
        }
        
        .rule-name i {
            color: #4a90e2;
            font-size: 1.3rem;
        }
        
        .rule-name span {
            font-size: 1.1rem;
            font-weight: 600;
            color: #2c3e50;
            word-break: break-word;
        }
        
        .rule-actions {
            display: flex;
            gap: 10px;
        }
        
        .btn-edit {
            background: linear-gradient(135deg, #4a90e2, #357abd);
            color: white;
            padding: 10px 20px;
            border-radius: 8px;
            text-decoration: none;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 8px;
            transition: all 0.3s ease;
            font-size: 0.9rem;
            box-shadow: 0 4px 12px rgba(74, 144, 226, 0.3);
        }
        
        .btn-edit:hover {
            background: linear-gradient(135deg, #357abd, #2c5aa0);
            transform: translateY(-2px);
            box-shadow: 0 6px 16px rgba(74, 144, 226, 0.4);
            text-decoration: none;
            color: white;
        }
        
        .rule-meta {
            display: flex;
            gap: 15px;
            margin-top: 15px;
            padding-top: 15px;
            border-top: 1px solid #f0f0f0;
            flex-wrap: wrap;
        }
        
        .meta-item {
            display: flex;
            align-items: center;
            gap: 6px;
            font-size: 0.85rem;
            color: #8c8c8c;
        }
        
        .meta-item i {
            color: #4a90e2;
        }
        
        .empty-state {
            text-align: center;
            padding: 60px 20px;
            color: #8c8c8c;
        }
        
        .empty-state i {
            font-size: 4rem;
            color: #d9d9d9;
            margin-bottom: 20px;
        }
        
        .empty-state h3 {
            font-size: 1.5rem;
            margin-bottom: 10px;
            color: #595959;
        }
        
        .empty-state p {
            font-size: 1rem;
            line-height: 1.6;
        }
        
        .search-bar {
            background: white;
            border-radius: 12px;
            padding: 20px;
            margin-bottom: 30px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.05);
        }
        
        .search-input {
            width: 100%;
            padding: 15px 20px;
            border: 2px solid #f0f0f0;
            border-radius: 8px;
            font-size: 1rem;
            transition: all 0.3s ease;
        }
        
        .search-input:focus {
            outline: none;
            border-color: #4a90e2;
            box-shadow: 0 0 0 3px rgba(74, 144, 226, 0.1);
        }
        
        @media (max-width: 768px) {
            .container {
                margin: 10px;
                border-radius: 15px;
            }
            
            .header {
                padding: 30px 20px;
            }
            
            .header h1 {
                font-size: 2.2rem;
            }
            
            .content {
                padding: 20px;
            }
            
            .rules-grid {
                grid-template-columns: 1fr;
            }
            
            .stats-info {
                flex-direction: column;
                gap: 15px;
            }
            
            .rule-header {
                flex-direction: column;
                align-items: flex-start;
                gap: 15px;
            }
            
            .rule-actions {
                width: 100%;
                justify-content: flex-end;
            }
        }
        
        .fade-in {
            animation: fadeIn 0.6s ease-in;
        }
        
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        .btn-back { 
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
        
        .btn-back:hover {
            background: rgba(74, 144, 226, 0.2);
            transform: translateX(-5px);
            text-decoration: none;
            color: #357abd;
        }
        
        .btn-add-rule {
            background: linear-gradient(135deg, #52c41a, #389e0d);
            color: white;
            padding: 15px 25px;
            border-radius: 10px;
            text-decoration: none;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 10px;
            transition: all 0.3s ease;
            box-shadow: 0 4px 12px rgba(82, 196, 26, 0.3);
            margin-left: auto;
        }
        
        .btn-add-rule:hover {
            background: linear-gradient(135deg, #389e0d, #237804);
            transform: translateY(-2px);
            box-shadow: 0 6px 16px rgba(82, 196, 26, 0.4);
            text-decoration: none;
            color: white;
        }
    </style>
</head>
<body>
    <div class="container fade-in">
        <div class="header">
            <h1><i class="fas fa-shield-alt"></i> ElastAlert Rules</h1>
            <div class="subtitle">Manage and monitor your alert configurations</div>
        </div>
        
        <div class="content">
            <a href="/" class="btn-back">
                <i class="fas fa-arrow-left"></i>
                Back to Dashboard
            </a>
            
            @if(session('success'))
                <div class="alert-success">
                    <i class="fas fa-check-circle"></i>
                    <span>{{ session('success') }}</span>
                </div>
            @endif

            <div class="stats-bar">
                <h3><i class="fas fa-chart-bar"></i> Rules Overview</h3>
                <div class="stats-info">
                    <div class="stat-item">
                        <i class="fas fa-file-code"></i>
                        <span>{{ count($rules) }} Total Rules</span>
                    </div>
                    <div class="stat-item">
                        <i class="fas fa-clock"></i>
                        <span>Last Updated: {{ date('M d, Y H:i') }}</span>
                    </div>
                    <div class="stat-item">
                        <i class="fas fa-server"></i>
                        <span>ElastAlert Service</span>
                    </div>
                    <a href="{{ route('elastalert_rules.create') }}" class="btn-add-rule">
                        <i class="fas fa-plus"></i>
                        Add New Rule
                    </a>
                </div>
            </div>

            @if(!empty($rules))
                <div class="search-bar">
                    <input type="text" class="search-input" placeholder="🔍 Search rules..." id="searchInput">
                </div>
                
                <div class="rules-grid" id="rulesGrid">
                    @foreach($rules as $rule)
                        <div class="rule-card" data-rule-name="{{ strtolower($rule) }}">
                            <div class="rule-header">
                                <div class="rule-name">
                                    <i class="fas fa-cog"></i>
                                    <span>{{ $rule }}</span>
                                </div>
                                <div class="rule-actions">
                                    <a href="{{ route('elastalert_rules.edit', $rule) }}" class="btn-edit">
                                        <i class="fas fa-edit"></i>
                                        Edit
                                    </a>
                                </div>
                            </div>
                            <div class="rule-meta">
                                <div class="meta-item">
                                    <i class="fas fa-file-alt"></i>
                                    <span>YAML Config</span>
                                </div>
                                <div class="meta-item">
                                    <i class="fas fa-calendar"></i>
                                    <span>{{ date('M d', filemtime('/home/sefaubuntu/elastic-alert-bridge/api/app/Services/elastalert2/rules/' . $rule)) }}</span>
                                </div>
                                <div class="meta-item">
                                    <i class="fas fa-weight"></i>
                                    <span>{{ number_format(filesize('/home/sefaubuntu/elastic-alert-bridge/api/app/Services/elastalert2/rules/' . $rule) / 1024, 1) }}KB</span>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="empty-state">
                    <i class="fas fa-inbox"></i>
                    <h3>No Rules Found</h3>
                    <p>There are no ElastAlert rules configured yet.<br>Rules will appear here once they are created.</p>
                </div>
            @endif
        </div>
    </div>

    <script>
        // Simple search functionality
        document.getElementById('searchInput').addEventListener('input', function(e) {
            const searchTerm = e.target.value.toLowerCase();
            const ruleCards = document.querySelectorAll('.rule-card');
            
            ruleCards.forEach(card => {
                const ruleName = card.getAttribute('data-rule-name');
                if (ruleName.includes(searchTerm)) {
                    card.style.display = 'block';
                } else {
                    card.style.display = 'none';
                }
            });
        });
    </script>
</body>
</html> 