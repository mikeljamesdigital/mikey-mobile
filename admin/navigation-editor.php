<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Navigation Editor | Mikey Mobile Admin</title>
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
        }
        
        .container {
            max-width: 1200px;
            margin: 0 auto;
        }
        
        h1 {
            color: #E85D9A;
            margin-bottom: 30px;
            font-size: 36px;
        }
        
        .back-link {
            display: inline-block;
            color: #00BCD4;
            text-decoration: none;
            margin-bottom: 20px;
            font-size: 18px;
        }
        
        .back-link:hover {
            color: #E85D9A;
        }
        
        .section {
            background: rgba(255, 255, 255, 0.1);
            border: 2px solid #00BCD4;
            border-radius: 10px;
            padding: 30px;
            margin-bottom: 30px;
        }
        
        h2 {
            color: #00BCD4;
            margin-bottom: 20px;
            font-size: 24px;
        }
        
        .nav-item {
            background: rgba(0, 0, 0, 0.3);
            border: 1px solid #00BCD4;
            border-radius: 8px;
            padding: 15px;
            margin-bottom: 15px;
        }
        
        .form-group {
            margin-bottom: 15px;
        }
        
        label {
            display: block;
            color: #00BCD4;
            margin-bottom: 5px;
            font-weight: bold;
        }
        
        input[type="text"],
        input[type="url"] {
            width: 100%;
            padding: 10px;
            background: rgba(255, 255, 255, 0.1);
            border: 1px solid #00BCD4;
            border-radius: 5px;
            color: #ffffff;
            font-size: 16px;
        }
        
        input[type="text"]:focus,
        input[type="url"]:focus {
            outline: none;
            border-color: #E85D9A;
        }
        
        .btn {
            background: linear-gradient(135deg, #E85D9A 0%, #d14d87 100%);
            color: #ffffff;
            padding: 12px 30px;
            border: 2px solid #00BCD4;
            border-radius: 50px;
            font-size: 18px;
            font-weight: bold;
            cursor: pointer;
            transition: all 0.3s ease;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        
        .btn:hover {
            background: linear-gradient(135deg, #ff6eb4 0%, #E85D9A 100%);
            transform: translateY(-2px);
        }
        
        .success-message {
            background: rgba(0, 255, 0, 0.2);
            border: 2px solid #00ff00;
            color: #00ff00;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
        }
        
        .error-message {
            background: rgba(255, 0, 0, 0.2);
            border: 2px solid #ff0000;
            color: #ff0000;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
        }
        
        .info-box {
            background: rgba(0, 188, 212, 0.2);
            border: 2px solid #00BCD4;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 20px;
        }
        
        .dropdown-item {
            margin-left: 30px;
            padding: 10px;
            background: rgba(0, 0, 0, 0.2);
            border-left: 3px solid #E85D9A;
            margin-top: 10px;
        }
        
        .subitem {
            margin-left: 60px;
            padding: 8px;
            background: rgba(0, 0, 0, 0.15);
            border-left: 2px solid #00BCD4;
            margin-top: 5px;
        }
        
        textarea {
            width: 100%;
            min-height: 400px;
            padding: 15px;
            background: rgba(0, 0, 0, 0.5);
            border: 1px solid #00BCD4;
            border-radius: 5px;
            color: #ffffff;
            font-family: 'Courier New', monospace;
            font-size: 14px;
            line-height: 1.5;
        }
        
        textarea:focus {
            outline: none;
            border-color: #E85D9A;
        }
    </style>
</head>
<body>
    <div class="container">
        <a href="index.php" class="back-link">← Back to Admin Dashboard</a>
        
        <h1>Navigation Editor</h1>
        
        <?php
        $config_file = '../navigation-config.json';
        $message = '';
        $message_type = '';
        
        // Handle form submission
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save_navigation'])) {
            $json_data = $_POST['navigation_json'];
            
            // Validate JSON
            $decoded = json_decode($json_data, true);
            if (json_last_error() === JSON_ERROR_NONE) {
                // Save to file
                if (file_put_contents($config_file, json_encode($decoded, JSON_PRETTY_PRINT))) {
                    $message = 'Navigation configuration saved successfully!';
                    $message_type = 'success';
                } else {
                    $message = 'Error: Could not write to configuration file. Check file permissions.';
                    $message_type = 'error';
                }
            } else {
                $message = 'Error: Invalid JSON format. Please check your syntax.';
                $message_type = 'error';
            }
        }
        
        // Load current configuration
        if (file_exists($config_file)) {
            $config = json_decode(file_get_contents($config_file), true);
        } else {
            $config = [
                'main_nav' => [
                    'left' => [],
                    'right' => []
                ],
                'logo' => [
                    'image' => 'logo.png',
                    'alt' => 'Mikey Mobile Oil Change'
                ]
            ];
        }
        
        // Display message
        if ($message) {
            echo '<div class="' . $message_type . '-message">' . htmlspecialchars($message) . '</div>';
        }
        ?>
        
        <div class="info-box">
            <h3 style="color: #00BCD4; margin-bottom: 10px;">How to Use This Editor</h3>
            <p>Edit the JSON configuration below to update your site navigation. The configuration controls:</p>
            <ul style="margin-top: 10px; margin-left: 20px;">
                <li><strong>Left Navigation:</strong> Links that appear on the left side of the logo (HOME, ABOUT, LOCATIONS)</li>
                <li><strong>Right Navigation:</strong> Links that appear on the right side of the logo (SERVICES, PRICING, CONTACT)</li>
                <li><strong>Dropdowns:</strong> The LOCATIONS menu has a dropdown with cities and neighborhoods</li>
                <li><strong>Logo:</strong> Configure the logo image file and alt text</li>
            </ul>
            <p style="margin-top: 10px;"><strong>Note:</strong> After saving, you may need to purge your Cloudflare cache to see changes on the live site.</p>
        </div>
        
        <div class="section">
            <h2>Current Navigation Structure</h2>
            
            <h3 style="color: #E85D9A; margin: 20px 0 10px;">Left Side Navigation</h3>
            <?php foreach ($config['main_nav']['left'] as $item): ?>
                <div class="nav-item">
                    <strong><?php echo htmlspecialchars($item['label']); ?></strong>
                    <br>URL: <?php echo htmlspecialchars($item['url']); ?>
                    
                    <?php if (isset($item['dropdown']) && $item['dropdown']): ?>
                        <div style="margin-top: 10px;">
                            <?php foreach ($item['dropdown'] as $dropdown): ?>
                                <div class="dropdown-item">
                                    <strong><?php echo htmlspecialchars($dropdown['label']); ?></strong>
                                    <br>URL: <?php echo htmlspecialchars($dropdown['url']); ?>
                                    
                                    <?php if (isset($dropdown['subitems'])): ?>
                                        <?php foreach ($dropdown['subitems'] as $subitem): ?>
                                            <div class="subitem">
                                                <?php echo htmlspecialchars($subitem['label']); ?>
                                                <br>URL: <?php echo htmlspecialchars($subitem['url']); ?>
                                            </div>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
            
            <h3 style="color: #E85D9A; margin: 20px 0 10px;">Right Side Navigation</h3>
            <?php foreach ($config['main_nav']['right'] as $item): ?>
                <div class="nav-item">
                    <strong><?php echo htmlspecialchars($item['label']); ?></strong>
                    <br>URL: <?php echo htmlspecialchars($item['url']); ?>
                </div>
            <?php endforeach; ?>
        </div>
        
        <div class="section">
            <h2>Edit Navigation JSON</h2>
            <form method="POST">
                <div class="form-group">
                    <label>Navigation Configuration (JSON Format)</label>
                    <textarea name="navigation_json"><?php echo json_encode($config, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES); ?></textarea>
                </div>
                <button type="submit" name="save_navigation" class="btn">Save Navigation</button>
            </form>
        </div>
        
        <div class="info-box">
            <h3 style="color: #E85D9A; margin-bottom: 10px;">Example: Adding a New Link</h3>
            <p>To add a new link to the right navigation (like a new PRICING page), add this to the "right" array:</p>
            <pre style="background: rgba(0,0,0,0.5); padding: 10px; border-radius: 5px; margin-top: 10px; overflow-x: auto;">
{
  "label": "PRICING",
  "url": "pricing.html",
  "dropdown": null
}</pre>
        </div>
    </div>
</body>
</html>
