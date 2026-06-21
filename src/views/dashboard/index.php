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
    <title>Dashboard - SNISTOJ</title>
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
            align-items: center;
        }
        
        .navbar h2 {
            font-size: 24px;
        }
        
        .navbar a {
            color: white;
            text-decoration: none;
            margin: 0 15px;
            transition: opacity 0.3s;
        }
        
        .navbar a:hover {
            opacity: 0.8;
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
        
        .cards {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 20px;
        }
        
        .card {
            background: white;
            border-radius: 10px;
            padding: 25px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s;
        }
        
        .card:hover {
            transform: translateY(-5px);
        }
        
        .card h3 {
            color: #667eea;
            margin-bottom: 10px;
            font-size: 18px;
        }
        
        .card p {
            color: #666;
            font-size: 14px;
            line-height: 1.6;
        }
        
        .card a {
            display: inline-block;
            margin-top: 15px;
            padding: 8px 16px;
            background: #667eea;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            transition: background 0.3s;
        }
        
        .card a:hover {
            background: #764ba2;
        }
    </style>
</head>
<body>
    <div class="navbar">
        <h2>SNISTOJ</h2>
        <div>
            <a href="/problems">Problems</a>
            <a href="/contests">Contests</a>
            <a href="/user/profile">Profile</a>
            <a href="/logout">Logout</a>
        </div>
    </div>
    
    <div class="container">
        <h1>Welcome, <?php echo htmlspecialchars($_SESSION['username'] ?? 'User'); ?>!</h1>
        
        <div class="cards">
            <div class="card">
                <h3>📚 Problem Archive</h3>
                <p>Browse and solve programming problems from various categories and difficulty levels.</p>
                <a href="/problems">View Problems</a>
            </div>
            
            <div class="card">
                <h3>🏆 Contests</h3>
                <p>Participate in programming contests and compete with other users.</p>
                <a href="/contests">View Contests</a>
            </div>
            
            <div class="card">
                <h3>👤 Profile</h3>
                <p>View your statistics, submissions, and update your profile information.</p>
                <a href="/user/profile">Go to Profile</a>
            </div>
            
            <div class="card">
                <h3>⚙️ Compiler</h3>
                <p>Write and compile code in multiple languages (C, C++, Java, Python).</p>
                <a href="/compiler">Open Compiler</a>
            </div>
        </div>
    </div>
</body>
</html>
