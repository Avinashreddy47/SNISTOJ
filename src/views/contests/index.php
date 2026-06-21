<?php
// Check authentication
if (!isset($_SESSION['user_id'])) {
    header('Location: /login');
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contests - SNISTOJ</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: #f5f5f5;
        }
        
        .navbar {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 15px 30px;
            display: flex;
            justify-content: space-between;
        }
        
        .navbar a {
            color: white;
            text-decoration: none;
            margin-left: 20px;
        }
        
        .container {
            max-width: 1200px;
            margin: 40px auto;
            padding: 0 20px;
        }
        
        h1 {
            color: #333;
            margin-bottom: 30px;
        }
        
        .tabs {
            display: flex;
            gap: 20px;
            margin-bottom: 30px;
            border-bottom: 2px solid #eee;
        }
        
        .tab {
            padding: 12px 20px;
            background: none;
            border: none;
            cursor: pointer;
            font-size: 16px;
            color: #666;
            border-bottom: 3px solid transparent;
            transition: all 0.3s;
        }
        
        .tab.active {
            color: #667eea;
            border-bottom-color: #667eea;
        }
        
        .contests-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 20px;
        }
        
        .contest-card {
            background: white;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s;
        }
        
        .contest-card:hover {
            transform: translateY(-5px);
        }
        
        .contest-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 20px;
        }
        
        .contest-header h3 {
            font-size: 18px;
            margin-bottom: 8px;
        }
        
        .contest-header .status {
            display: inline-block;
            padding: 4px 8px;
            background: rgba(255, 255, 255, 0.3);
            border-radius: 4px;
            font-size: 12px;
        }
        
        .contest-body {
            padding: 20px;
        }
        
        .contest-info {
            margin-bottom: 15px;
            font-size: 14px;
        }
        
        .contest-info p {
            color: #666;
            margin: 5px 0;
        }
        
        .contest-footer {
            display: flex;
            gap: 10px;
            padding: 0 20px 20px;
        }
        
        .btn {
            flex: 1;
            padding: 10px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-weight: 600;
            font-size: 14px;
            text-decoration: none;
            text-align: center;
        }
        
        .btn-primary {
            background: #667eea;
            color: white;
        }
        
        .btn-primary:hover {
            background: #5568d3;
        }
        
        .btn-secondary {
            background: #e0e0e0;
            color: #333;
        }
        
        .btn-secondary:hover {
            background: #d0d0d0;
        }
    </style>
</head>
<body>
    <div class="navbar">
        <h2>SNISTOJ</h2>
        <div>
            <a href="/problems">Problems</a>
            <a href="/user/profile">Profile</a>
            <a href="/compiler">Compiler</a>
            <a href="/logout">Logout</a>
        </div>
    </div>
    
    <div class="container">
        <h1>Contests</h1>
        
        <div class="tabs">
            <button class="tab active">All</button>
            <button class="tab">Upcoming</button>
            <button class="tab">Running</button>
            <button class="tab">Ended</button>
        </div>
        
        <div class="contests-grid">
            <div class="contest-card">
                <div class="contest-header">
                    <h3>Weekly Contest #1</h3>
                    <span class="status">Upcoming</span>
                </div>
                <div class="contest-body">
                    <div class="contest-info">
                        <p><strong>Start:</strong> 2026-06-25 15:00</p>
                        <p><strong>Duration:</strong> 2 hours</p>
                        <p><strong>Problems:</strong> 5</p>
                    </div>
                </div>
                <div class="contest-footer">
                    <button class="btn btn-primary">Register</button>
                    <button class="btn btn-secondary">Details</button>
                </div>
            </div>
            
            <div class="contest-card">
                <div class="contest-header">
                    <h3>Monthly Challenge</h3>
                    <span class="status">Running</span>
                </div>
                <div class="contest-body">
                    <div class="contest-info">
                        <p><strong>Started:</strong> 2026-06-21 00:00</p>
                        <p><strong>Ends:</strong> 2026-06-30 23:59</p>
                        <p><strong>Participants:</strong> 342</p>
                    </div>
                </div>
                <div class="contest-footer">
                    <button class="btn btn-primary">View</button>
                    <button class="btn btn-secondary">Standings</button>
                </div>
            </div>
            
            <div class="contest-card">
                <div class="contest-header">
                    <h3>Beginners Contest</h3>
                    <span class="status">Ended</span>
                </div>
                <div class="contest-body">
                    <div class="contest-info">
                        <p><strong>Ended:</strong> 2026-06-15 17:00</p>
                        <p><strong>Participants:</strong> 156</p>
                        <p><strong>Your Rank:</strong> 42</p>
                    </div>
                </div>
                <div class="contest-footer">
                    <button class="btn btn-secondary">Results</button>
                    <button class="btn btn-secondary">Solutions</button>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
