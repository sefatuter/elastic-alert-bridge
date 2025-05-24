<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $filename }} - Edit Rule</title>
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
            padding: 30px;
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
            background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><defs><pattern id="grain" width="100" height="100" patternUnits="userSpaceOnUse"><circle cx="50" cy="50" r="1" fill="white" fill-opacity="0.1"/></pattern></defs><rect width="100" height="100" fill="url(%23grain)"/></svg>');
            opacity: 0.3;
        }
        
        .header h1 { 
            font-size: 2.5rem;
            font-weight: 300;
            margin-bottom: 10px;
            position: relative;
            z-index: 1;
        }
        
        .header .subtitle {
            font-size: 1.1rem;
            opacity: 0.9;
            position: relative;
            z-index: 1;
        }
        
        .content {
            padding: 40px;
        }
        
        .alert-danger { 
            background: linear-gradient(135deg, #ff6b6b, #ee5a52);
            color: white;
            padding: 20px;
            border-radius: 12px;
            margin-bottom: 30px;
            border: none;
            box-shadow: 0 8px 16px rgba(255, 107, 107, 0.3);
        }
        
        .alert-danger ul { 
            margin: 0;
            padding-left: 20px;
        }
        
        .alert-danger i {
            margin-right: 10px;
            font-size: 1.2rem;
        }
        
        .form-group {
            margin-bottom: 30px;
        }
        
        label { 
            display: block;
            margin-bottom: 15px;
            font-weight: 600;
            font-size: 1.1rem;
            color: #4a5568;
            display: flex;
            align-items: center;
        }
        
        label i {
            margin-right: 10px;
            color: #4a90e2;
            font-size: 1.2rem;
        }
        
        .textarea-container {
            position: relative;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 8px 25px rgba(0,0,0,0.1);
        }
        
        textarea {
            width: 100%;
            min-height: 500px;
            padding: 25px;
            border: 2px solid #e2e8f0;
            border-radius: 12px;
            font-family: 'Fira Code', 'Monaco', 'Cascadia Code', monospace;
            font-size: 14px;
            line-height: 1.6;
            background: #f8fafc;
            color: #2d3748;
            transition: all 0.3s ease;
            resize: vertical;
        }
        
        textarea:focus {
            outline: none;
            border-color: #4a90e2;
            background: white;
            box-shadow: 0 0 0 3px rgba(74, 144, 226, 0.1);
            transform: translateY(-2px);
        }
        
        .button-group {
            display: flex;
            gap: 15px;
            align-items: center;
            flex-wrap: wrap;
            margin-top: 30px;
        }
        
        .btn-save { 
            background: linear-gradient(135deg, #52c41a, #389e0d);
            color: white;
            padding: 15px 30px;
            border: none;
            border-radius: 12px;
            cursor: pointer;
            font-size: 1.1rem;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 10px;
            transition: all 0.3s ease;
            box-shadow: 0 8px 16px rgba(82, 196, 26, 0.3);
        }
        
        .btn-save:hover {
            background: linear-gradient(135deg, #389e0d, #237804);
            transform: translateY(-2px);
            box-shadow: 0 12px 20px rgba(82, 196, 26, 0.4);
        }
        
        .btn-save:active {
            transform: translateY(0);
        }
        
        .btn-back { 
            display: inline-flex;
            align-items: center;
            gap: 8px;
            color: #4a90e2;
            text-decoration: none;
            font-weight: 600;
            padding: 15px 25px;
            border-radius: 12px;
            background: rgba(74, 144, 226, 0.1);
            transition: all 0.3s ease;
        }
        
        .btn-back:hover {
            background: rgba(74, 144, 226, 0.2);
            transform: translateX(-5px);
            text-decoration: none;
            color: #357abd;
        }
        
        .file-info {
            background: linear-gradient(135deg, #e6f7ff, #bae7ff);
            border-radius: 12px;
            padding: 20px;
            margin-bottom: 30px;
            border-left: 5px solid #1890ff;
        }
        
        .file-info h3 {
            color: #003a8c;
            margin-bottom: 5px;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .file-info p {
            color: #1890ff;
            margin: 0;
            font-size: 0.95rem;
        }
        
        .stats {
            display: flex;
            gap: 20px;
            margin-top: 15px;
            flex-wrap: wrap;
        }
        
        .stat-item {
            background: rgba(255, 255, 255, 0.7);
            padding: 10px 15px;
            border-radius: 8px;
            font-size: 0.9rem;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        
        .stat-item i {
            color: #1890ff;
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
            
            .button-group {
                flex-direction: column;
                align-items: stretch;
            }
            
            .btn-save, .btn-back {
                justify-content: center;
            }
        }
        
        /* YAML Syntax highlighting hints */
        .syntax-hint {
            font-size: 0.85rem;
            color: #718096;
            margin-top: 10px;
            padding: 10px;
            background: rgba(113, 128, 150, 0.1);
            border-radius: 8px;
            border-left: 3px solid #cbd5e0;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1><i class="fas fa-edit"></i> Edit Rule</h1>
            <div class="subtitle">Modify your ElastAlert configuration</div>
        </div>
        
        <div class="content">
            <div class="file-info">
                <h3><i class="fas fa-file-code"></i> File Information</h3>
                <p><strong>Filename:</strong> {{ $filename }}</p>
                <div class="stats">
                    <div class="stat-item">
                        <i class="fas fa-calendar"></i>
                        <span>Last Modified: {{ date('M d, Y H:i', filemtime('/home/sefaubuntu/elastic-alert-bridge/api/app/Services/elastalert2/rules/' . $filename)) }}</span>
                    </div>
                    <div class="stat-item">
                        <i class="fas fa-file-alt"></i>
                        <span>Size: {{ number_format(strlen($content)) }} characters</span>
                    </div>
                </div>
            </div>

            @if ($errors->any())
                <div class="alert-danger">
                    <h4><i class="fas fa-exclamation-triangle"></i> Validation Errors</h4>
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('elastalert_rules.update', $filename) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="form-group">
                    <label for="content">
                        <i class="fas fa-code"></i>
                        Rule Content (YAML Format)
                    </label>
                    <div class="textarea-container">
                        <textarea name="content" id="content" placeholder="Enter your YAML configuration here...">{{ old('content', $content) }}</textarea>
                    </div>
                    <div class="syntax-hint">
                        <i class="fas fa-lightbulb"></i>
                        <strong>Tip:</strong> Make sure your YAML syntax is correct. Use proper indentation (spaces, not tabs) and ensure all required fields are present.
                    </div>
                </div>

                <div class="button-group">
                    <button type="submit" class="btn-save">
                        <i class="fas fa-save"></i>
                        Save Changes
                    </button>
                    
                    <a href="{{ route('elastalert_rules.index') }}" class="btn-back">
                        <i class="fas fa-arrow-left"></i>
                        Back to Rules
                    </a>
                </div>
            </form>
        </div>
    </div>
</body>
</html> 