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
    <title>Problems - SNISTOJ</title>
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
        
        .filters {
            background: white;
            padding: 20px;
            border-radius: 10px;
            margin-bottom: 20px;
            display: flex;
            gap: 20px;
        }
        
        .filters select {
            padding: 8px 12px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 14px;
        }
        
        .problems-list {
            background: white;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
        }
        
        th, td {
            padding: 15px;
            text-align: left;
            border-bottom: 1px solid #eee;
        }
        
        th {
            background: #f9f9f9;
            color: #333;
            font-weight: 600;
        }
        
        tr:hover {
            background: #f5f5f5;
            cursor: pointer;
        }
        
        .difficulty-easy {
            color: #28a745;
        }
        
        .difficulty-medium {
            color: #ffc107;
        }
        
        .difficulty-hard {
            color: #dc3545;
        }
        
        .accepted {
            background: #d4edda;
            color: #155724;
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 12px;
        }
        
        a {
            color: #667eea;
            text-decoration: none;
        }
        
        a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="navbar">
        <h2>SNISTOJ</h2>
        <div>
            <a href="/user/profile">Profile</a>
            <a href="/contests">Contests</a>
            <a href="/compiler">Compiler</a>
            <a href="/logout">Logout</a>
        </div>
    </div>
    
    <div class="container">
        <h1 style="margin-bottom: 20px; color: #333;">Problem Archive</h1>
        
        <div class="filters">
            <select>
                <option value="">All Difficulties</option>
                <option value="easy">Easy</option>
                <option value="medium">Medium</option>
                <option value="hard">Hard</option>
            </select>
            <select>
                <option value="">All Categories</option>
                <option value="basic">Basic</option>
                <option value="math">Math</option>
                <option value="sorting">Sorting</option>
                <option value="strings">Strings</option>
            </select>
        </div>
        
        <div class="problems-list">
            <table>
                <thead>
                    <tr>
                        <th>Problem</th>
                        <th>Difficulty</th>
                        <th>Category</th>
                        <th>Accepted</th>
                        <th>Submission</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td><a href="/problem/1">Hello World</a></td>
                        <td><span class="difficulty-easy">Easy</span></td>
                        <td>Basic</td>
                        <td><span class="accepted">✓</span></td>
                        <td><a href="/problem/1">Solve</a></td>
                    </tr>
                    <tr>
                        <td><a href="/problem/2">Simple Sum</a></td>
                        <td><span class="difficulty-easy">Easy</span></td>
                        <td>Basic</td>
                        <td><span class="accepted">✓</span></td>
                        <td><a href="/problem/2">Solve</a></td>
                    </tr>
                    <tr>
                        <td><a href="/problem/3">Fibonacci Series</a></td>
                        <td><span class="difficulty-medium">Medium</span></td>
                        <td>Math</td>
                        <td></td>
                        <td><a href="/problem/3">Solve</a></td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>
