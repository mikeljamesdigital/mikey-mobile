<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Settings - Mikey Mobile Admin</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 20px;
        }
        
        .container {
            max-width: 1200px;
            margin: 0 auto;
        }
        
        .header {
            background: white;
            padding: 20px 30px;
            border-radius: 10px;
            margin-bottom: 20px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .header h1 {
            color: #333;
            font-size: 24px;
        }
        
        .back-link {
            color: #667eea;
            text-decoration: none;
            font-weight: 500;
        }
        
        .back-link:hover {
            text-decoration: underline;
        }
        
        .card {
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            margin-bottom: 20px;
        }
        
        .form-group {
            margin-bottom: 25px;
        }
        
        label {
            display: block;
            margin-bottom: 8px;
            color: #333;
            font-weight: 500;
        }
        
        input, textarea {
            width: 100%;
            padding: 12px;
            border: 2px solid #e0e0e0;
            border-radius: 6px;
            font-size: 14px;
            font-family: inherit;
        }
        
        input:focus, textarea:focus {
            outline: none;
            border-color: #667eea;
        }
        
        .help-text {
            color: #666;
            font-size: 13px;
            margin-top: 5px;
        }
        
        .btn {
            padding: 12px 30px;
            border: none;
            border-radius: 6px;
            font-size: 16px;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.3s;
        }
        
        .btn-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
        }
        
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(102, 126, 234, 0.4);
        }
        
        .alert {
            padding: 15px 20px;
            border-radius: 6px;
            margin-bottom: 20px;
        }
        
        .alert-success {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        
        .alert-error {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        
        .section-title {
            color: #333;
            font-size: 20px;
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 2px solid #e0e0e0;
        }
        
        .info-box {
            background: #fff3cd;
            border-left: 4px solid #ffc107;
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 4px;
        }
        
        .info-box p {
            color: #856404;
            line-height: 1.6;
        }
        
        .settings-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 20px;
        }
        
        .setting-card {
            border: 2px solid #e0e0e0;
            border-radius: 8px;
            padding: 20px;
        }
        
        .setting-card h3 {
            color: #333;
            margin-bottom: 10px;
            font-size: 16px;
        }
        
        .setting-card p {
            color: #666;
            font-size: 14px;
            line-height: 1.6;
        }
        
        .current-value {
            background: #f8f9ff;
            padding: 10px;
            border-radius: 4px;
            margin-top: 10px;
            font-family: monospace;
            font-size: 13px;
            color: #667eea;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>⚙️ Settings</h1>
            <a href="index.php" class="back-link">← Back to Dashboard</a>
        </div>

        <?php
        $message = '';
        $messageType = '';
        $configFile = '../site-config.json';

        // Default configuration
        $defaultConfig = [
            'site_name' => 'Mikey Mobile Oil Change',
            'phone' => '(559) 838-4267',
            'email' => 'info@mikeymobile.com',
            'service_area' => 'Fresno, CA & Surrounding Areas',
            'hours' => 'Monday - Saturday: 8:00 AM - 6:00 PM',
            'pricing' => '$75 + Oil & Filter',
            'top_bar_text' => 'SERVING FRESNO & SURROUNDING AREAS'
        ];

        // Load existing config or use defaults
        if (file_exists($configFile)) {
            $config = json_decode(file_get_contents($configFile), true);
            if (!$config) {
                $config = $defaultConfig;
            }
        } else {
            $config = $defaultConfig;
        }

        // Handle form submission
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save_settings'])) {
            $newConfig = [
                'site_name' => $_POST['site_name'] ?? $config['site_name'],
                'phone' => $_POST['phone'] ?? $config['phone'],
                'email' => $_POST['email'] ?? $config['email'],
                'service_area' => $_POST['service_area'] ?? $config['service_area'],
                'hours' => $_POST['hours'] ?? $config['hours'],
                'pricing' => $_POST['pricing'] ?? $config['pricing'],
                'top_bar_text' => $_POST['top_bar_text'] ?? $config['top_bar_text']
            ];

            if (file_put_contents($configFile, json_encode($newConfig, JSON_PRETTY_PRINT))) {
                $config = $newConfig;
                $message = 'Settings saved successfully! Note: These settings are stored but need to be manually applied to your pages.';
                $messageType = 'success';
            } else {
                $message = 'Error: Could not save settings. Check file permissions.';
                $messageType = 'error';
            }
        }
        ?>

        <?php if ($message): ?>
            <div class="alert alert-<?php echo $messageType; ?>">
                <?php echo htmlspecialchars($message); ?>
            </div>
        <?php endif; ?>

        <div class="card">
            <div class="info-box">
                <p><strong>⚠️ Important:</strong> These settings are stored in a configuration file but are not automatically applied to your website. You'll need to manually update your HTML files to use these values, or integrate them via PHP includes.</p>
            </div>

            <h2 class="section-title">🏢 Business Information</h2>
            <form method="POST" action="">
                <div class="form-group">
                    <label for="site_name">Site Name</label>
                    <input type="text" id="site_name" name="site_name" value="<?php echo htmlspecialchars($config['site_name']); ?>">
                    <div class="help-text">The name of your business displayed across the site</div>
                </div>

                <div class="form-group">
                    <label for="phone">Phone Number</label>
                    <input type="text" id="phone" name="phone" value="<?php echo htmlspecialchars($config['phone']); ?>">
                    <div class="help-text">Your business phone number</div>
                </div>

                <div class="form-group">
                    <label for="email">Email Address</label>
                    <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($config['email']); ?>">
                    <div class="help-text">Your business email address</div>
                </div>

                <h2 class="section-title" style="margin-top: 40px;">📍 Service Information</h2>

                <div class="form-group">
                    <label for="service_area">Service Area</label>
                    <input type="text" id="service_area" name="service_area" value="<?php echo htmlspecialchars($config['service_area']); ?>">
                    <div class="help-text">Geographic area you serve</div>
                </div>

                <div class="form-group">
                    <label for="hours">Business Hours</label>
                    <input type="text" id="hours" name="hours" value="<?php echo htmlspecialchars($config['hours']); ?>">
                    <div class="help-text">Your operating hours</div>
                </div>

                <div class="form-group">
                    <label for="pricing">Pricing Information</label>
                    <input type="text" id="pricing" name="pricing" value="<?php echo htmlspecialchars($config['pricing']); ?>">
                    <div class="help-text">Your standard pricing display</div>
                </div>

                <h2 class="section-title" style="margin-top: 40px;">🎨 Display Settings</h2>

                <div class="form-group">
                    <label for="top_bar_text">Top Bar Text</label>
                    <input type="text" id="top_bar_text" name="top_bar_text" value="<?php echo htmlspecialchars($config['top_bar_text']); ?>">
                    <div class="help-text">Text displayed in the top bar of your website</div>
                </div>

                <button type="submit" name="save_settings" class="btn btn-primary">💾 Save Settings</button>
            </form>
        </div>

        <div class="card">
            <h2 class="section-title">🔧 System Information</h2>
            <div class="settings-grid">
                <div class="setting-card">
                    <h3>📁 Configuration File</h3>
                    <p>Location of settings file</p>
                    <div class="current-value">site-config.json</div>
                </div>

                <div class="setting-card">
                    <h3>🌐 Web Server</h3>
                    <p>Server software</p>
                    <div class="current-value"><?php echo $_SERVER['SERVER_SOFTWARE'] ?? 'Unknown'; ?></div>
                </div>

                <div class="setting-card">
                    <h3>🐘 PHP Version</h3>
                    <p>Current PHP version</p>
                    <div class="current-value"><?php echo phpversion(); ?></div>
                </div>

                <div class="setting-card">
                    <h3>📂 Document Root</h3>
                    <p>Website root directory</p>
                    <div class="current-value"><?php echo $_SERVER['DOCUMENT_ROOT'] ?? 'Unknown'; ?></div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
