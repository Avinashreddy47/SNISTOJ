<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SNISTOJ - Online Judge System</title>
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
        
        header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 20px 0;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }
        
        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
        }
        
        .header-content {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        h1 {
            font-size: 32px;
            font-weight: 700;
        }
        
        nav a {
            color: white;
            text-decoration: none;
            margin-left: 20px;
            transition: opacity 0.3s;
        }
        
        nav a:hover {
            opacity: 0.8;
        }
        
        .hero {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 100px 0;
            text-align: center;
        }
        
        .hero h2 {
            font-size: 48px;
            margin-bottom: 20px;
        }
        
        .hero p {
            font-size: 20px;
            margin-bottom: 30px;
        }
        
        .cta-button {
            display: inline-block;
            padding: 12px 40px;
            background: white;
            color: #667eea;
            text-decoration: none;
            border-radius: 5px;
            font-weight: 600;
            margin: 10px;
            transition: transform 0.3s;
        }
        
        .cta-button:hover {
            transform: scale(1.05);
        }
        
        .features {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 30px;
            padding: 60px 0;
        }
        
        .feature {
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            text-align: center;
        }
        
        .feature h3 {
            color: #667eea;
            margin-bottom: 15px;
            font-size: 24px;
        }
        
        .feature p {
            color: #666;
            line-height: 1.6;
        }
        
        footer {
            background: #333;
            color: white;
            padding: 30px 0;
            text-align: center;
            margin-top: 60px;
        }
    </style>
</head>
<body>
    <header>
        <div class="container">
            <div class="header-content">
                <h1>SNISTOJ</h1>
                <nav>
                    <?php if (isset($_SESSION['user_id'])): ?>
                        <a href="/problems">Problems</a>
                        <a href="/user/profile">Profile</a>
                        <a href="/logout">Logout</a>
                    <?php else: ?>
                        <a href="/login">Login</a>
                        <a href="/register">Register</a>
                    <?php endif; ?>
                </nav>
            </div>
        </div>
    </header>
    
    <div class="hero">
        <div class="container">
            <h2>Online Judge System</h2>
            <p>Practice programming problems, compete in contests, and improve your skills</p>
            <?php if (!isset($_SESSION['user_id'])): ?>
                <a href="/register" class="cta-button">Get Started</a>
                <a href="/login" class="cta-button">Login</a>
            <?php else: ?>
                <a href="/problems" class="cta-button">Solve Problems</a>
            <?php endif; ?>
        </div>
    </div>
    
    <div class="container">
        <div class="features">
            <div class="feature">
                <h3>📚 Problem Archive</h3>
                <p>Hundreds of programming problems to practice, from beginner to advanced levels.</p>
            </div>
            
            <div class="feature">
                <h3>🏆 Contests</h3>
                <p>Participate in programming contests and compete with other users.</p>
            </div>
            
            <div class="feature">
                <h3>💻 Multi-Language</h3>
                <p>Support for C, C++, C++11, Java, and Python programming languages.</p>
            </div>
            
            <div class="feature">
                <h3>📊 Statistics</h3>
                <p>Track your progress, view submissions, and analyze your performance.</p>
            </div>
            
            <div class="feature">
                <h3>🔒 Secure</h3>
                <p>Enterprise-grade security with prepared statements and secure authentication.</p>
            </div>
            
            <div class="feature">
                <h3>⚡ Fast</h3>
                <p>Real-time code compilation and execution with instant feedback.</p>
            </div>
        </div>
    </div>
    
    <footer>
        <p>&copy; 2024 SNISTOJ. All rights reserved.</p>
    </footer>
</body>
</html>
