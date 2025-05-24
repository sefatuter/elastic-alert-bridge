<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create New Rule - ElastAlert</title>
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
        
        .form-section {
            background: white;
            border-radius: 15px;
            padding: 30px;
            box-shadow: 0 8px 25px rgba(0,0,0,0.08);
            margin-bottom: 30px;
            border-left: 5px solid #4a90e2;
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
        
        .input-field {
            width: 100%;
            padding: 15px 20px;
            border: 2px solid #e2e8f0;
            border-radius: 10px;
            font-size: 1rem;
            transition: all 0.3s ease;
            background: #f8fafc;
        }
        
        .input-field:focus {
            outline: none;
            border-color: #4a90e2;
            background: white;
            box-shadow: 0 0 0 3px rgba(74, 144, 226, 0.1);
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
        
        .yaml-tips {
            background: linear-gradient(135deg, #e6f7ff, #bae7ff);
            border-radius: 12px;
            padding: 20px;
            margin-bottom: 30px;
            border-left: 5px solid #1890ff;
        }
        
        .yaml-tips h3 {
            color: #003a8c;
            margin-bottom: 15px;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .yaml-tips ul {
            color: #003a8c;
            margin: 0;
            padding-left: 20px;
        }
        
        .yaml-tips li {
            margin-bottom: 8px;
            line-height: 1.5;
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
        
        .btn-cancel { 
            display: inline-flex;
            align-items: center;
            gap: 8px;
            color: #6c757d;
            text-decoration: none;
            font-weight: 600;
            padding: 15px 25px;
            border-radius: 12px;
            background: rgba(108, 117, 125, 0.1);
            transition: all 0.3s ease;
        }
        
        .btn-cancel:hover {
            background: rgba(108, 117, 125, 0.2);
            transform: translateY(-2px);
            text-decoration: none;
            color: #495057;
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
            
            .btn-save, .btn-cancel {
                justify-content: center;
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
            <h1><i class="fas fa-plus-circle"></i> Create New Rule</h1>
            <div class="subtitle">Add a new ElastAlert monitoring rule</div>
        </div>
        
        <div class="content">
            <a href="{{ route('elastalert_rules.index') }}" class="btn-back">
                <i class="fas fa-arrow-left"></i>
                Back to Rules
            </a>
            
            @if($errors->any())
                <div class="alert-danger">
                    <i class="fas fa-exclamation-triangle"></i>
                    <strong>Please fix the following errors:</strong>
                    <ul>
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="yaml-tips">
                <h3><i class="fas fa-lightbulb"></i> YAML Rule Tips</h3>
                <ul>
                    <li><strong>name:</strong> Unique identifier for your rule</li>
                    <li><strong>type:</strong> Rule type (frequency, any, change, etc.)</li>
                    <li><strong>index:</strong> Elasticsearch index pattern to monitor</li>
                    <li><strong>filter:</strong> Query conditions to match documents</li>
                    <li><strong>alert:</strong> Notification method (email, slack, etc.)</li>
                </ul>
            </div>

            <form method="POST" action="{{ route('elastalert_rules.store') }}">
                @csrf
                
                <div class="form-section">
                    <div class="form-group">
                        <label for="filename">
                            <i class="fas fa-file-signature"></i>
                            Rule Filename
                        </label>
                        <input 
                            type="text" 
                            id="filename" 
                            name="filename" 
                            class="input-field"
                            placeholder="Enter rule filename (e.g., my_alert_rule)" 
                            value="{{ old('filename') }}"
                            required
                        >
                        <small style="color: #6c757d; margin-top: 8px; display: block;">
                            <i class="fas fa-info-circle"></i>
                            Filename will automatically get .yaml extension if not provided
                        </small>
                    </div>

                    <div class="form-group">
                        <label for="content">
                            <i class="fas fa-code"></i>
                            YAML Rule Content
                        </label>
                        <div class="textarea-container">
                            <textarea 
                                id="content" 
                                name="content" 
                                placeholder="Enter your YAML rule configuration here...

Example:
name: Sample Alert Rule
type: frequency
index: logs-*
num_events: 10
timeframe:
  minutes: 5

filter:
- term:
    level: ERROR

alert:
- debug

"
                                required
                            >{{ old('content') }}</textarea>
                        </div>
                    </div>
                </div>

                <div class="button-group">
                    <button type="submit" class="btn-save">
                        <i class="fas fa-save"></i>
                        Create Rule
                    </button>
                    <a href="{{ route('elastalert_rules.index') }}" class="btn-cancel">
                        <i class="fas fa-times"></i>
                        Cancel
                    </a>
                </div>
            </form>
        </div>
    </div>
</body>
</html> 