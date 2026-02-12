<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard | Mikey Mobile</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Arial', sans-serif;
            background: linear-gradient(135deg, #2A1548 0%, #3D2066 100%);
            color: #ffffff;
            padding: 20px;
            min-height: 100vh;
        }
        
        .container {
            max-width: 1200px;
            margin: 0 auto;
        }
        
        h1 {
            color: #E85D9A;
            margin-bottom: 30px;
            font-size: 42px;
            text-align: center;
        }
        
        .dashboard-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 30px;
            margin-top: 40px;
        }
        
        .dashboard-card {
            background: rgba(255, 255, 255, 0.1);
            border: 2px solid #00BCD4;
            border-radius: 15px;
            padding: 30px;
            text-align: center;
            transition: all 0.3s ease;
            text-decoration: none;
            color: #ffffff;
            display: block;
        }
        
        .dashboard-card:hover {
            transform: translateY(-5px);
            border-color: #E85D9A;
            box-shadow: 0 10px 30px rgba(232, 93, 154, 0.3);
        }
        
        .dashboard-card h2 {
            color: #00BCD4;
            font-size: 28px;
            margin-bottom: 15px;
        }
        
        .dashboard-card:hover h2 {
            color: #E85D9A;
        }
        
        .dashboard-card p {
            color: #ffffff;
            font-size: 16px;
            line-height: 1.6;
        }
        
        .dashboard-card .icon {
            font-size: 60px;
            margin-bottom: 20px;
        }
        
        .logout-link {
            display: inline-block;
            color: #E85D9A;
            text-decoration: none;
            margin-top: 30px;
            font-size: 18px;
            text-align: center;
            width: 100%;
        }
        
        .logout-link:hover {
            color: #ff6eb4;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>🚗 Mikey Mobile Admin Dashboard</h1>
        
        <div class="dashboard-grid">
            <a href="navigation-editor.php" class="dashboard-card">
                <div class="icon">🧭</div>
                <h2>Navigation Editor</h2>
                <p>Manage site navigation menu, add new pages, and update links throughout the website.</p>
            </a>
            
            <a href="content-editor.php" class="dashboard-card">
                <div class="icon">📝</div>
                <h2>Content Editor</h2>
                <p>Edit page content, update text, and manage website copy.</p>
            </a>
            
            <a href="location-manager.php" class="dashboard-card">
                <div class="icon">📍</div>
                <h2>Location Manager</h2>
                <p>Add new service locations, manage neighborhoods, and update location pages.</p>
            </a>
            
            <a href="image-uploader.php" class="dashboard-card">
                <div class="icon">🖼️</div>
                <h2>Image Uploader</h2>
                <p>Upload and manage images, logos, and other media files.</p>
            </a>
            
            <a href="settings.php" class="dashboard-card">
                <div class="icon">⚙️</div>
                <h2>Settings</h2>
                <p>Configure site settings, contact information, and general preferences.</p>
            </a>
            
            <a href="../index.html" class="dashboard-card">
                <div class="icon">🌐</div>
                <h2>View Website</h2>
                <p>Visit the live website to see your changes in action.</p>
            </a>
        </div>
        
        <a href="logout.php" class="logout-link">Logout</a>
    </div>
</body>
</html>
