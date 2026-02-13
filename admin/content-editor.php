<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Content Editor - Mikey Mobile Admin</title>
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
        
        .editor-card {
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        
        .form-group {
            margin-bottom: 20px;
        }
        
        label {
            display: block;
            margin-bottom: 8px;
            color: #333;
            font-weight: 500;
        }
        
        select, input, textarea {
            width: 100%;
            padding: 12px;
            border: 2px solid #e0e0e0;
            border-radius: 6px;
            font-size: 14px;
            font-family: inherit;
        }
        
        select:focus, input:focus, textarea:focus {
            outline: none;
            border-color: #667eea;
        }
        
        textarea {
            min-height: 400px;
            font-family: 'Monaco', 'Courier New', monospace;
            resize: vertical;
        }
        
        .button-group {
            display: flex;
            gap: 10px;
            margin-top: 20px;
        }
        
        button {
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
        
        .btn-secondary {
            background: #f0f0f0;
            color: #333;
        }
        
        .btn-secondary:hover {
            background: #e0e0e0;
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
        
        .info-box {
            background: #e7f3ff;
            border-left: 4px solid #2196F3;
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 4px;
        }
        
        .info-box p {
            color: #0c5460;
            line-height: 1.6;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>📝 Content Editor</h1>
            <a href="index.php" class="back-link">← Back to Dashboard</a>
        </div>

        <?php
        $message = '';
        $messageType = '';

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save_content'])) {
            $page = $_POST['page'];
            $content = $_POST['content'];
            
            $filePath = '../' . $page;
            
            if (file_exists($filePath)) {
                if (file_put_contents($filePath, $content)) {
                    $message = 'Content saved successfully! Remember to purge Cloudflare cache to see changes live.';
                    $messageType = 'success';
                } else {
                    $message = 'Error: Could not write to file. Check file permissions.';
                    $messageType = 'error';
                }
            } else {
                $message = 'Error: File not found.';
                $messageType = 'error';
            }
        }

        // Get list of editable pages
        $pages = [
            'index.html' => 'Home Page',
            'about.html' => 'About Page',
            'locations.html' => 'Locations Page',
            'fresno/index.html' => 'Fresno City Page',
            'clovis/index.html' => 'Clovis City Page',
            'madera-ranchos/index.html' => 'Madera Ranchos City Page',
        ];

        $selectedPage = isset($_GET['page']) ? $_GET['page'] : 'index.html';
        $currentContent = '';

        if (file_exists('../' . $selectedPage)) {
            $currentContent = file_get_contents('../' . $selectedPage);
        }
        ?>

        <?php if ($message): ?>
            <div class="alert alert-<?php echo $messageType; ?>">
                <?php echo htmlspecialchars($message); ?>
            </div>
        <?php endif; ?>

        <div class="editor-card">
            <div class="info-box">
                <p><strong>💡 Tip:</strong> This editor allows you to directly edit HTML files. Be careful when making changes. Always test on a staging environment first if possible. After saving, remember to purge your Cloudflare cache.</p>
            </div>

            <form method="GET" action="">
                <div class="form-group">
                    <label for="page">Select Page to Edit:</label>
                    <select name="page" id="page" onchange="this.form.submit()">
                        <?php foreach ($pages as $file => $name): ?>
                            <option value="<?php echo $file; ?>" <?php echo $selectedPage === $file ? 'selected' : ''; ?>>
                                <?php echo $name; ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </form>

            <form method="POST" action="">
                <input type="hidden" name="page" value="<?php echo htmlspecialchars($selectedPage); ?>">
                
                <div class="form-group">
                    <label for="content">HTML Content:</label>
                    <textarea name="content" id="content"><?php echo htmlspecialchars($currentContent); ?></textarea>
                </div>

                <div class="button-group">
                    <button type="submit" name="save_content" class="btn-primary">💾 Save Changes</button>
                    <button type="button" onclick="location.reload()" class="btn-secondary">🔄 Reload</button>
                </div>
            </form>
        </div>
    </div>
</body>
</html>
