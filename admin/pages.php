<?php
session_start();

// Check if logged in
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: index.php');
    exit;
}

// Path to website files
define('SITE_PATH', '/home/master/applications/njfxrssvcj/public_html/');

$success = '';
$error = '';

// Get list of editable pages
$pages = [
    'index.html' => 'Homepage',
    'about.html' => 'About Us',
    'locations.html' => 'Locations',
    'services.html' => 'Services',
    'pricing.html' => 'Pricing'
];

$currentPage = $_GET['page'] ?? 'index.html';
$pageContent = '';

if (isset($pages[$currentPage])) {
    $filePath = SITE_PATH . $currentPage;
    if (file_exists($filePath)) {
        $pageContent = file_get_contents($filePath);
    }
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    if ($_POST['action'] === 'save_page') {
        $page = $_POST['page'] ?? '';
        $content = $_POST['content'] ?? '';
        
        if (isset($pages[$page])) {
            $filePath = SITE_PATH . $page;
            
            // Create backup
            $backupPath = SITE_PATH . 'backups/';
            if (!is_dir($backupPath)) {
                mkdir($backupPath, 0755, true);
            }
            copy($filePath, $backupPath . $page . '.' . date('Y-m-d-H-i-s') . '.bak');
            
            // Save new content
            if (file_put_contents($filePath, $content)) {
                $success = 'Page saved successfully!';
                $pageContent = $content;
            } else {
                $error = 'Failed to save page.';
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Page Editor - Mikey Mobile Admin</title>
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
            max-width: 1400px;
            margin: 40px auto;
            padding: 0 20px;
        }
        
        .page-selector {
            background: white;
            padding: 20px;
            border-radius: 12px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            margin-bottom: 20px;
        }
        
        .page-selector select {
            padding: 10px 15px;
            border: 2px solid #e0e0e0;
            border-radius: 8px;
            font-size: 15px;
            min-width: 250px;
        }
        
        .editor-container {
            background: white;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        
        .editor-toolbar {
            display: flex;
            gap: 10px;
            margin-bottom: 20px;
            padding-bottom: 20px;
            border-bottom: 2px solid #e0e0e0;
        }
        
        .editor-toolbar button {
            padding: 8px 15px;
            background: #667eea;
            color: white;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-size: 14px;
        }
        
        .editor-toolbar button:hover {
            background: #5568d3;
        }
        
        textarea {
            width: 100%;
            min-height: 600px;
            padding: 20px;
            border: 2px solid #e0e0e0;
            border-radius: 8px;
            font-family: 'Courier New', monospace;
            font-size: 14px;
            line-height: 1.6;
            resize: vertical;
        }
        
        .save-btn {
            padding: 15px 40px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            margin-top: 20px;
        }
        
        .save-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(102, 126, 234, 0.4);
        }
        
        .alert {
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
        }
        
        .alert-success {
            background: #d4edda;
            color: #155724;
            border-left: 4px solid #28a745;
        }
        
        .alert-error {
            background: #f8d7da;
            color: #721c24;
            border-left: 4px solid #dc3545;
        }
        
        .info-box {
            background: #e3f2fd;
            padding: 20px;
            border-radius: 8px;
            margin-top: 20px;
            border-left: 4px solid #2196F3;
        }
        
        .info-box h4 {
            color: #1976d2;
            margin-bottom: 10px;
        }
        
        .info-box ul {
            color: #555;
            line-height: 1.8;
            margin-left: 20px;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>📄 Page Content Editor</h1>
        <div class="user-info">
            <a href="dashboard.php" class="logout-btn">← Back to Dashboard</a>
            <a href="logout.php" class="logout-btn">Logout</a>
        </div>
    </div>
    
    <div class="container">
        <?php if ($success): ?>
            <div class="alert alert-success"><?php echo htmlspecialchars($success); ?></div>
        <?php endif; ?>
        
        <?php if ($error): ?>
            <div class="alert alert-error"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>
        
        <div class="page-selector">
            <label for="pageSelect"><strong>Select Page to Edit:</strong></label>
            <select id="pageSelect" onchange="window.location.href='pages.php?page=' + this.value">
                <?php foreach ($pages as $file => $name): ?>
                    <option value="<?php echo $file; ?>" <?php echo $currentPage === $file ? 'selected' : ''; ?>>
                        <?php echo $name; ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        
        <div class="editor-container">
            <h2>Editing: <?php echo $pages[$currentPage]; ?></h2>
            <p style="color: #666; margin-bottom: 20px;">Edit the HTML content below. A backup will be created automatically when you save.</p>
            
            <form method="POST">
                <input type="hidden" name="action" value="save_page">
                <input type="hidden" name="page" value="<?php echo htmlspecialchars($currentPage); ?>">
                
                <div class="editor-toolbar">
                    <button type="button" onclick="document.execCommand('undo')">↶ Undo</button>
                    <button type="button" onclick="document.execCommand('redo')">↷ Redo</button>
                    <button type="button" onclick="findText()">🔍 Find</button>
                    <button type="button" onclick="formatCode()">✨ Format</button>
                </div>
                
                <textarea name="content" id="editor"><?php echo htmlspecialchars($pageContent); ?></textarea>
                
                <button type="submit" class="save-btn">💾 Save Page</button>
            </form>
            
            <div class="info-box">
                <h4>⚠️ Important Notes:</h4>
                <ul>
                    <li>A backup is automatically created each time you save</li>
                    <li>Be careful when editing HTML - syntax errors can break the page</li>
                    <li>Test your changes on the live site after saving</li>
                    <li>To edit text content, look for text between HTML tags</li>
                    <li>Use Ctrl+F (or Cmd+F on Mac) to search within the editor</li>
                </ul>
            </div>
        </div>
    </div>
    
    <script>
        function findText() {
            const searchTerm = prompt('Enter text to find:');
            if (searchTerm) {
                const editor = document.getElementById('editor');
                const content = editor.value;
                const index = content.toLowerCase().indexOf(searchTerm.toLowerCase());
                
                if (index !== -1) {
                    editor.focus();
                    editor.setSelectionRange(index, index + searchTerm.length);
                    editor.scrollTop = editor.scrollHeight * (index / content.length);
                } else {
                    alert('Text not found');
                }
            }
        }
        
        function formatCode() {
            alert('Code formatting is a basic feature. For advanced formatting, use an external HTML formatter.');
        }
        
        // Auto-save warning
        window.addEventListener('beforeunload', function(e) {
            const editor = document.getElementById('editor');
            const original = <?php echo json_encode($pageContent); ?>;
            
            if (editor.value !== original) {
                e.preventDefault();
                e.returnValue = 'You have unsaved changes. Are you sure you want to leave?';
                return e.returnValue;
            }
        });
    </script>
</body>
</html>
