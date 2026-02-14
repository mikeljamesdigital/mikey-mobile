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
            max-width: 1400px;
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
        
        .metadata-section {
            background: #f8f9ff;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 20px;
            border: 2px solid #e0e0e0;
        }
        
        .metadata-section h3 {
            color: #667eea;
            margin-bottom: 15px;
            font-size: 18px;
        }
        
        .char-count {
            font-size: 12px;
            color: #666;
            margin-top: 5px;
        }
        
        .char-count.warning {
            color: #ff9800;
        }
        
        .char-count.error {
            color: #f44336;
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
        
        optgroup {
            font-weight: bold;
            color: #667eea;
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
            $title = $_POST['title'];
            $description = $_POST['description'];
            $content = $_POST['content'];
            
            $filePath = '../' . $page;
            
            if (file_exists($filePath)) {
                // Read the current content
                $currentContent = file_get_contents($filePath);
                
                // Update title
                $currentContent = preg_replace(
                    '/<title>.*?<\/title>/s',
                    '<title>' . htmlspecialchars($title) . '</title>',
                    $currentContent
                );
                
                // Update or add meta description
                if (preg_match('/<meta name="description".*?>/i', $currentContent)) {
                    $currentContent = preg_replace(
                        '/<meta name="description".*?>/i',
                        '<meta name="description" content="' . htmlspecialchars($description) . '">',
                        $currentContent
                    );
                } else {
                    // Add meta description after charset or viewport
                    $currentContent = preg_replace(
                        '/(<meta name="viewport".*?>)/i',
                        '$1' . "\n    " . '<meta name="description" content="' . htmlspecialchars($description) . '">',
                        $currentContent
                    );
                }
                
                // Save the updated content
                if (file_put_contents($filePath, $currentContent)) {
                    $message = 'Content and metadata saved successfully! Remember to purge Cloudflare cache to see changes live.';
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
            'Main Pages' => [
                'index.html' => 'Home Page',
                'about.html' => 'About Page',
                'locations.html' => 'Locations Page',
            ],
            'City Pages' => [
                'fresno/index.html' => 'Fresno',
                'clovis/index.html' => 'Clovis',
                'madera-ranchos/index.html' => 'Madera Ranchos',
            ],
            'Fresno Neighborhoods' => [
                'fresno/fig-garden/index.html' => 'Fig Garden',
                'fresno/sunnyside/index.html' => 'Sunnyside',
                'fresno/fig-garden-loop/index.html' => 'Fig Garden Loop',
                'fresno/woodward-park/index.html' => 'Woodward Park',
                'fresno/riverpark/index.html' => 'Riverpark',
                'fresno/pinedale/index.html' => 'Pinedale',
                'fresno/sierra-sky-park/index.html' => 'Sierra Sky Park',
                'fresno/fort-washington/index.html' => 'Fort Washington',
            ],
            'Clovis Neighborhoods' => [
                'clovis/clovis-north/index.html' => 'Clovis North',
                'clovis/cindy-lane/index.html' => 'Cindy Lane',
                'clovis/dry-creek/index.html' => 'Dry Creek',
                'clovis/clovis-high/index.html' => 'Clovis High',
                'clovis/quail-lakes/index.html' => 'Quail Lakes',
                'clovis/harlan-ranch/index.html' => 'Harlan Ranch',
            ],
            'Madera Ranchos Neighborhoods' => [
                'madera-ranchos/rolling-hills/index.html' => 'Rolling Hills',
                'madera-ranchos/riverstone/index.html' => 'Riverstone',
            ],
        ];

        $selectedPage = isset($_GET['page']) ? $_GET['page'] : 'index.html';
        $currentContent = '';
        $currentTitle = '';
        $currentDescription = '';

        if (file_exists('../' . $selectedPage)) {
            $currentContent = file_get_contents('../' . $selectedPage);
            
            // Extract current title
            if (preg_match('/<title>(.*?)<\/title>/s', $currentContent, $matches)) {
                $currentTitle = $matches[1];
            }
            
            // Extract current meta description
            if (preg_match('/<meta name="description" content="(.*?)"/i', $currentContent, $matches)) {
                $currentDescription = $matches[1];
            }
        }
        ?>

        <?php if ($message): ?>
            <div class="alert alert-<?php echo $messageType; ?>">
                <?php echo htmlspecialchars($message); ?>
            </div>
        <?php endif; ?>

        <div class="editor-card">
            <div class="info-box">
                <p><strong>💡 Tip:</strong> Edit page metadata (title & description for SEO) and HTML content. The title appears in browser tabs and search results. The description appears in search engine results. After saving, remember to purge your Cloudflare cache.</p>
            </div>

            <form method="GET" action="">
                <div class="form-group">
                    <label for="page">Select Page to Edit:</label>
                    <select name="page" id="page" onchange="this.form.submit()">
                        <?php foreach ($pages as $groupName => $groupPages): ?>
                            <optgroup label="<?php echo $groupName; ?>">
                                <?php foreach ($groupPages as $file => $name): ?>
                                    <option value="<?php echo $file; ?>" <?php echo $selectedPage === $file ? 'selected' : ''; ?>>
                                        <?php echo $name; ?>
                                    </option>
                                <?php endforeach; ?>
                            </optgroup>
                        <?php endforeach; ?>
                    </select>
                </div>
            </form>

            <form method="POST" action="">
                <input type="hidden" name="page" value="<?php echo htmlspecialchars($selectedPage); ?>">
                
                <div class="metadata-section">
                    <h3>🔍 SEO Metadata</h3>
                    
                    <div class="form-group">
                        <label for="title">Page Title (appears in browser tab & search results)</label>
                        <input type="text" name="title" id="title" value="<?php echo htmlspecialchars($currentTitle); ?>" maxlength="70" oninput="updateCharCount('title', 70)">
                        <div class="char-count" id="title-count">
                            <?php echo strlen($currentTitle); ?> / 70 characters (optimal: 50-60)
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="description">Meta Description (appears in search results)</label>
                        <textarea name="description" id="description" rows="3" maxlength="160" oninput="updateCharCount('description', 160)"><?php echo htmlspecialchars($currentDescription); ?></textarea>
                        <div class="char-count" id="description-count">
                            <?php echo strlen($currentDescription); ?> / 160 characters (optimal: 120-155)
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <label for="content">HTML Content:</label>
                    <textarea name="content" id="content"><?php echo htmlspecialchars($currentContent); ?></textarea>
                </div>

                <div class="button-group">
                    <button type="submit" name="save_content" class="btn-primary">💾 Save Changes</button>
                    <button type="button" onclick="location.reload()" class="btn-secondary">🔄 Reload</button>
                    <a href="../<?php echo htmlspecialchars($selectedPage); ?>" target="_blank" class="btn-secondary" style="text-decoration: none; display: inline-block; line-height: 1;">👁️ Preview Page</a>
                </div>
            </form>
        </div>
    </div>

    <script>
        function updateCharCount(fieldName, maxLength) {
            const field = document.getElementById(fieldName);
            const countDiv = document.getElementById(fieldName + '-count');
            const length = field.value.length;
            
            let optimalMin, optimalMax;
            if (fieldName === 'title') {
                optimalMin = 50;
                optimalMax = 60;
            } else {
                optimalMin = 120;
                optimalMax = 155;
            }
            
            countDiv.textContent = length + ' / ' + maxLength + ' characters (optimal: ' + optimalMin + '-' + optimalMax + ')';
            
            if (length > maxLength) {
                countDiv.className = 'char-count error';
            } else if (length < optimalMin || length > optimalMax) {
                countDiv.className = 'char-count warning';
            } else {
                countDiv.className = 'char-count';
            }
        }
        
        // Initialize character counts on page load
        updateCharCount('title', 70);
        updateCharCount('description', 160);
    </script>
</body>
</html>
