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
    <title>User Profile - SNISTOJ</title>
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
        
        .container {
            max-width: 1200px;
            margin: 40px auto;
            padding: 0 20px;
        }
        
        .profile-header {
            background: white;
            border-radius: 10px;
            padding: 30px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            margin-bottom: 30px;
            display: grid;
            grid-template-columns: 1fr 2fr;
            gap: 30px;
        }
        
        .avatar {
            width: 150px;
            height: 150px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 60px;
            margin: auto;
        }
        
        .profile-info h1 {
            color: #333;
            margin-bottom: 10px;
        }
        
        .profile-info p {
            color: #666;
            margin: 5px 0;
        }
        
        .stats {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-top: 30px;
        }
        
        .stat-card {
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            text-align: center;
        }
        
        .stat-card h3 {
            color: #667eea;
            font-size: 24px;
            margin-bottom: 5px;
        }
        
        .stat-card p {
            color: #666;
            font-size: 14px;
        }
        
        .submissions {
            background: white;
            border-radius: 10px;
            padding: 30px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }
        
        .submissions h2 {
            color: #333;
            margin-bottom: 20px;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
        }
        
        th, td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #eee;
        }
        
        th {
            background: #f9f9f9;
            color: #333;
            font-weight: 600;
        }
        
        .status {
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 12px;
            font-weight: 600;
        }
        
        .status.accepted {
            background: #d4edda;
            color: #155724;
        }
        
        .status.wrong {
            background: #f8d7da;
            color: #721c24;
        }
        
        .status.pending {
            background: #fff3cd;
            color: #856404;
        }
    </style>
</head>
<body>
    <div class="navbar">
        <h2>SNISTOJ</h2>
        <div>
            <a href="/problems" style="color: white; text-decoration: none; margin-right: 20px;">Problems</a>
            <a href="/logout" style="color: white; text-decoration: none;">Logout</a>
        </div>
    </div>
    
    <div class="container">
        <div class="profile-header">
            <div class="avatar"><?php echo strtoupper(substr($_SESSION['username'], 0, 1)); ?></div>
            <div class="profile-info">
                <h1><?php echo htmlspecialchars($_SESSION['username']); ?></h1>
                <p><strong>Email:</strong> <?php echo htmlspecialchars($_SESSION['email'] ?? 'N/A'); ?></p>
                <p><strong>Member Since:</strong> 2024</p>
                <p><strong>Role:</strong> <?php echo ucfirst($_SESSION['role'] ?? 'User'); ?></p>
            </div>
        </div>
        
        <div class="stats">
            <div class="stat-card">
                <h3>25</h3>
                <p>Problems Solved</p>
            </div>
            <div class="stat-card">
                <h3>42</h3>
                <p>Total Submissions</p>
            </div>
            <div class="stat-card">
                <h3>8</h3>
                <p>Contests Participated</p>
            </div>
            <div class="stat-card">
                <h3>1250</h3>
                <p>Total Points</p>
            </div>
        </div>
        
        <div class="submissions" style="margin-top: 30px;">
            <h2>Recent Submissions</h2>
            <table>
                <thead>
                    <tr>
                        <th>Problem</th>
                        <th>Language</th>
                        <th>Status</th>
                        <th>Submitted</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>Hello World</td>
                        <td>Python</td>
                        <td><span class="status accepted">Accepted</span></td>
                        <td>2 hours ago</td>
                    </tr>
                    <tr>
                        <td>Simple Sum</td>
                        <td>C++</td>
                        <td><span class="status accepted">Accepted</span></td>
                        <td>5 hours ago</td>
                    </tr>
                    <tr>
                        <td>Fibonacci Series</td>
                        <td>Java</td>
                        <td><span class="status wrong">Wrong Answer</span></td>
                        <td>1 day ago</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>
