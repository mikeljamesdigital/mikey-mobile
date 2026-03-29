<?php
session_start();

// Check if logged in
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: index.php');
    exit;
}

// Path to website files
define('SITE_PATH', '/home/master/applications/njfxrssvcj/public_html/');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mikey Mobile Admin - Dashboard</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, sans-serif;
            background: #f5f5f5;
        }
        
        .header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 20px 40px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        
        .header h1 {
            font-size: 24px;
        }
        
        .header .user-info {
            display: flex;
            align-items: center;
            gap: 20px;
        }
        
        .logout-btn {
            background: rgba(255,255,255,0.2);
            color: white;
            padding: 8px 20px;
            border-radius: 6px;
            text-decoration: none;
            transition: background 0.3s;
        }
        
        .logout-btn:hover {
            background: rgba(255,255,255,0.3);
        }
        
        .container {
            max-width: 1200px;
            margin: 40px auto;
            padding: 0 20px;
        }
        
        .welcome {
            background: white;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            margin-bottom: 30px;
        }
        
        .welcome h2 {
            color: #333;
            margin-bottom: 10px;
        }
        
        .welcome p {
            color: #666;
            line-height: 1.6;
        }
        
        .cards {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }
        
        .card {
            background: white;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            text-decoration: none;
            color: inherit;
            transition: transform 0.3s, box-shadow 0.3s;
            border-top: 4px solid #667eea;
        }
        
        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 30px rgba(0,0,0,0.15);
        }
        
        .card-icon {
            font-size: 48px;
            margin-bottom: 15px;
        }
        
        .card h3 {
            color: #333;
            margin-bottom: 10px;
            font-size: 20px;
        }
        
        .card p {
            color: #666;
            font-size: 14px;
            line-height: 1.5;
        }
        
        .stats {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
        }
        
        .stat-card {
            background: white;
            padding: 25px;
            border-radius: 12px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            text-align: center;
        }
        
        .stat-card .number {
            font-size: 36px;
            font-weight: bold;
            color: #667eea;
            margin-bottom: 5px;
        }
        
        .stat-card .label {
            color: #666;
            font-size: 14px;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>🚗 Mikey Mobile Admin</h1>
        <div class="user-info">
            <span>Welcome, <?php echo htmlspecialchars($_SESSION['admin_username']); ?></span>
            <a href="logout.php" class="logout-btn">Logout</a>
        </div>
    </div>
    
    <div class="container">
        <div class="welcome">
            <h2>Welcome to Your Admin Panel!</h2>
            <p>Manage your Mikey Mobile website content, navigation, and location pages all in one place. Select an option below to get started.</p>
        </div>
        
        <div class="cards">
            <a href="navigation.php" class="card">
                <div class="card-icon">📋</div>
                <h3>Navigation Menu</h3>
                <p>Edit your site's navigation menu, add or remove links, and reorder menu items.</p>
            </a>
            
            <a href="pages.php" class="card">
                <div class="card-icon">📄</div>
                <h3>Page Content</h3>
                <p>Update text, headings, and content on your main pages like Home, About, and Services.</p>
            </a>
            
            <a href="locations.php" class="card">
                <div class="card-icon">📍</div>
                <h3>Location Pages</h3>
                <p>Manage your service location pages for Fresno, Clovis, Madera Ranchos, and neighborhoods.</p>
            </a>
            
            <a href="images.php" class="card">
                <div class="card-icon">🖼️</div>
                <h3>Images & Media</h3>
                <p>Upload new images, replace existing ones, and manage your site's media files.</p>
            </a>
        </div>
        
        <div class="stats">
            <div class="stat-card">
                <div class="number">22</div>
                <div class="label">Total Pages</div>
            </div>
            <div class="stat-card">
                <div class="number">3</div>
                <div class="label">Main Cities</div>
            </div>
            <div class="stat-card">
                <div class="number">16</div>
                <div class="label">Neighborhoods</div>
            </div>
            <div class="stat-card">
                <div class="number">✓</div>
                <div class="label">Site Status</div>
            </div>
        </div>
    </div>
</body>
</html>
