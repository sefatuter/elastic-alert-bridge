<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Elastic Alert Bridge - Dashboard</title>
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
                padding: 50px 40px;
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
                font-size: 3.5rem;
                font-weight: 300;
                margin-bottom: 15px;
                position: relative;
                z-index: 1;
            }
            
            .header .subtitle {
                font-size: 1.3rem;
                opacity: 0.9;
                position: relative;
                z-index: 1;
            }
            
            .content {
                padding: 50px 40px;
            }
            
            .dashboard-grid {
                display: grid;
                gap: 30px;
                grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
                margin-bottom: 40px;
            }
            
            .dashboard-card {
                background: white;
                border-radius: 16px;
                padding: 30px;
                box-shadow: 0 8px 25px rgba(0,0,0,0.08);
                transition: all 0.3s ease;
                border: 2px solid transparent;
                position: relative;
                overflow: hidden;
            }
            
            .dashboard-card::before {
                content: '';
                position: absolute;
                top: 0;
                left: 0;
                right: 0;
                height: 4px;
                background: linear-gradient(90deg, #4a90e2, #357abd);
            }
            
            .dashboard-card:hover {
                transform: translateY(-8px);
                box-shadow: 0 15px 40px rgba(0,0,0,0.15);
                border-color: #4a90e2;
            }
            
            .card-header {
                display: flex;
                align-items: center;
                gap: 15px;
                margin-bottom: 20px;
            }
            
            .card-icon {
                width: 60px;
                height: 60px;
                border-radius: 12px;
                display: flex;
                align-items: center;
                justify-content: center;
                font-size: 1.8rem;
                color: white;
            }
            
            .card-icon.logs {
                background: linear-gradient(135deg, #52c41a, #389e0d);
            }
            
            .card-icon.rules {
                background: linear-gradient(135deg, #4a90e2, #357abd);
            }
            
            .card-icon.indices {
                background: linear-gradient(135deg, #722ed1, #531dab);
            }
            
            .card-title {
                font-size: 1.5rem;
                font-weight: 600;
                color: #2c3e50;
                margin-bottom: 8px;
            }
            
            .card-description {
                color: #6c757d;
                line-height: 1.6;
                margin-bottom: 25px;
            }
            
            .card-button {
                background: linear-gradient(135deg, #4a90e2, #357abd);
                color: white;
                padding: 12px 24px;
                border-radius: 10px;
                text-decoration: none;
                font-weight: 600;
                display: inline-flex;
                align-items: center;
                gap: 10px;
                transition: all 0.3s ease;
                box-shadow: 0 4px 12px rgba(74, 144, 226, 0.3);
            }
            
            .card-button:hover {
                background: linear-gradient(135deg, #357abd, #2c5aa0);
                transform: translateY(-2px);
                box-shadow: 0 6px 16px rgba(74, 144, 226, 0.4);
                text-decoration: none;
                color: white;
            }
            
            .card-button.green {
                background: linear-gradient(135deg, #52c41a, #389e0d);
                box-shadow: 0 4px 12px rgba(82, 196, 26, 0.3);
            }
            
            .card-button.green:hover {
                background: linear-gradient(135deg, #389e0d, #237804);
                box-shadow: 0 6px 16px rgba(82, 196, 26, 0.4);
            }
            
            .stats-section {
                background: linear-gradient(135deg, #e6f7ff, #bae7ff);
                border-radius: 16px;
                padding: 30px;
                margin-bottom: 40px;
                border-left: 5px solid #1890ff;
            }
            
            .stats-title {
                color: #003a8c;
                font-size: 1.4rem;
                font-weight: 600;
                margin-bottom: 20px;
                display: flex;
                align-items: center;
                gap: 12px;
            }
            
            .stats-grid {
                display: grid;
                gap: 20px;
                grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            }
            
            .stat-item {
                background: rgba(255, 255, 255, 0.8);
                padding: 20px;
                border-radius: 12px;
                text-align: center;
                box-shadow: 0 4px 8px rgba(0,0,0,0.05);
            }
            
            .stat-number {
                font-size: 2rem;
                font-weight: 700;
                color: #1890ff;
                margin-bottom: 5px;
            }
            
            .stat-label {
                color: #003a8c;
                font-weight: 600;
                font-size: 0.9rem;
            }
            
            @media (max-width: 768px) {
                .container {
                    margin: 10px;
                    border-radius: 15px;
                }
                
                .header {
                    padding: 40px 20px;
                }
                
                .header h1 {
                    font-size: 2.5rem;
                }
                
                .content {
                    padding: 30px 20px;
                }
                
                .dashboard-grid {
                    grid-template-columns: 1fr;
                    gap: 20px;
                }
                
                .stats-grid {
                    grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
                }
            }
            
            .fade-in {
                animation: fadeIn 0.8s ease-in;
            }
            
            @keyframes fadeIn {
                from { opacity: 0; transform: translateY(30px); }
                to { opacity: 1; transform: translateY(0); }
            }
        </style>
    </head>
    <body>
        <div class="container fade-in">
            <div class="header">
                <h1><i class="fas fa-shield-alt"></i> Elastic Alert Bridge</h1>
                <div class="subtitle">Centralized Log Monitoring & Alert Management System</div>
            </div>
            
            <div class="content">
                <div class="stats-section">
                    <div class="stats-title">
                        <i class="fas fa-chart-line"></i>
                        System Overview
                    </div>
                    <div class="stats-grid">
                        <div class="stat-item">
                            <div class="stat-number">{{ \Illuminate\Support\Facades\File::glob('/home/sefaubuntu/elastic-alert-bridge/api/app/Services/elastalert2/rules/*.yaml') ? count(\Illuminate\Support\Facades\File::glob('/home/sefaubuntu/elastic-alert-bridge/api/app/Services/elastalert2/rules/*.yaml')) : 0 }}</div>
                            <div class="stat-label">Active Rules</div>
                        </div>
                        <div class="stat-item">
                            <div class="stat-number">{{ date('H:i') }}</div>
                            <div class="stat-label">Current Time</div>
                        </div>
                        <div class="stat-item">
                            <div class="stat-number">Online</div>
                            <div class="stat-label">System Status</div>
                        </div>
                    </div>
                </div>

                <div class="dashboard-grid">
                    <div class="dashboard-card">
                        <div class="card-header">
                            <div class="card-icon logs">
                                <i class="fas fa-file-alt"></i>
                            </div>
                            <div>
                                <div class="card-title">System Logs</div>
                                <div class="card-description">View and analyze system logs, monitor application performance, and track system events in real-time.</div>
                            </div>
                        </div>
                        <a href="/logs" class="card-button green">
                            <i class="fas fa-eye"></i>
                            View Logs
                        </a>
                    </div>

                    <div class="dashboard-card">
                        <div class="card-header">
                            <div class="card-icon indices">
                                <i class="fas fa-database"></i>
                            </div>
                            <div>
                                <div class="card-title">Elasticsearch Indices</div>
                                <div class="card-description">Browse all available Elasticsearch indices, search through documents, and analyze your data.</div>
                            </div>
                        </div>
                        <a href="/logs/indices" class="card-button">
                            <i class="fas fa-search"></i>
                            Browse Indices
                        </a>
                    </div>

                    <div class="dashboard-card">
                        <div class="card-header">
                            <div class="card-icon rules">
                                <i class="fas fa-cogs"></i>
                            </div>
                            <div>
                                <div class="card-title">ElastAlert Rules</div>
                                <div class="card-description">Manage alert rules, configure notifications, and customize monitoring parameters for your infrastructure.</div>
                            </div>
                        </div>
                        <a href="/elastalert-rules" class="card-button">
                            <i class="fas fa-edit"></i>
                            Manage Rules
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <script>
            // Update current time every minute
            document.addEventListener('DOMContentLoaded', function() {
                setInterval(function() {
                    const timeElement = document.querySelector('.stat-item:nth-child(2) .stat-number');
                    if (timeElement) {
                        timeElement.textContent = new Date().toLocaleTimeString('en-US', {
                            hour: '2-digit',
                            minute: '2-digit',
                            hour12: false
                        });
                    }
                }, 60000);
            });
        </script>
    </body>
</html>
