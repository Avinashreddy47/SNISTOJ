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
    <title>Compiler - SNISTOJ</title>
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
            max-width: 1400px;
            margin: 20px auto;
            padding: 0 20px;
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
        }
        
        .editor-section, .output-section {
            background: white;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }
        
        .section-header {
            background: #667eea;
            color: white;
            padding: 15px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .section-header h2 {
            font-size: 18px;
        }
        
        .section-header select, .section-header button {
            padding: 8px 12px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 14px;
        }
        
        .section-header button {
            background: white;
            color: #667eea;
            font-weight: 600;
        }
        
        .section-header button:hover {
            background: #f0f0f0;
        }
        
        textarea {
            width: 100%;
            height: 300px;
            padding: 15px;
            border: none;
            font-family: 'Monaco', 'Courier New', monospace;
            font-size: 14px;
            resize: none;
        }
        
        .input-output {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
            margin: 20px;
        }
        
        .input-section, .output-section-inner {
            display: flex;
            flex-direction: column;
        }
        
        .input-section label, .output-section-inner label {
            font-weight: 600;
            margin-bottom: 8px;
            color: #333;
        }
        
        .input-section textarea, .output-section-inner textarea {
            height: 150px;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-family: 'Monaco', 'Courier New', monospace;
        }
        
        .output-section-inner textarea {
            background: #f9f9f9;
            color: #333;
            resize: none;
        }
        
        .actions {
            padding: 15px;
            display: flex;
            gap: 10px;
        }
        
        .actions button {
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-weight: 600;
            font-size: 14px;
        }
        
        .run-btn {
            background: #28a745;
            color: white;
        }
        
        .run-btn:hover {
            background: #218838;
        }
        
        .submit-btn {
            background: #667eea;
            color: white;
        }
        
        .submit-btn:hover {
            background: #5568d3;
        }
        
        .stats {
            padding: 15px;
            background: #f9f9f9;
            border-top: 1px solid #eee;
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 15px;
            text-align: center;
        }
        
        .stat {
            font-size: 12px;
            color: #666;
        }
        
        .stat-value {
            font-size: 18px;
            font-weight: 600;
            color: #667eea;
        }
    </style>
</head>
<body>
    <div class="navbar">
        <h2>SNISTOJ - Compiler</h2>
        <div>
            <a href="/problems">Problems</a>
            <a href="/user/profile">Profile</a>
            <a href="/logout">Logout</a>
        </div>
    </div>
    
    <div class="container">
        <div class="editor-section">
            <div class="section-header">
                <h2>Code Editor</h2>
                <select>
                    <option value="cpp">C++</option>
                    <option value="c">C</option>
                    <option value="java">Java</option>
                    <option value="python">Python</option>
                </select>
            </div>
            <textarea placeholder="Write your code here..."></textarea>
            <div class="actions">
                <button class="run-btn">▶ Run</button>
                <button class="submit-btn">✓ Submit</button>
            </div>
        </div>
        
        <div class="output-section">
            <div class="section-header">
                <h2>Output</h2>
            </div>
            <div class="input-output">
                <div class="input-section">
                    <label>Input</label>
                    <textarea placeholder="Enter input here..."></textarea>
                </div>
                <div class="output-section-inner">
                    <label>Output</label>
                    <textarea placeholder="Output will appear here..." readonly></textarea>
                </div>
            </div>
            <div class="stats">
                <div class="stat">
                    <div class="stat-value">0.023s</div>
                    <div>Execution Time</div>
                </div>
                <div class="stat">
                    <div class="stat-value">12MB</div>
                    <div>Memory Used</div>
                </div>
                <div class="stat">
                    <div class="stat-value">OK</div>
                    <div>Status</div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
