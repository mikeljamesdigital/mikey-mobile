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

// Handle file upload
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['image'])) {
    $file = $_FILES['image'];
    $uploadDir = SITE_PATH;
    
    // Validate file
    $allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp', 'image/svg+xml'];
    $maxSize = 10 * 1024 * 1024; // 10MB
    
    if ($file['error'] === UPLOAD_ERR_OK) {
        if (in_array($file['type'], $allowedTypes)) {
            if ($file['size'] <= $maxSize) {
                $filename = basename($file['name']);
                $targetPath = $uploadDir . $filename;
                
                // Create backup if file exists
                if (file_exists($targetPath)) {
                    $backupPath = SITE_PATH . 'backups/';
                    if (!is_dir($backupPath)) {
                        mkdir($backupPath, 0755, true);
                    }
                    copy($targetPath, $backupPath . $filename . '.' . date('Y-m-d-H-i-s') . '.bak');
                }
                
                if (move_uploaded_file($file['tmp_name'], $targetPath)) {
                    $success = 'Image uploaded successfully!';
                } else {
                    $error = 'Failed to upload image.';
                }
            } else {
                $error = 'File size exceeds 10MB limit.';
            }
        } else {
            $error = 'Invalid file type. Only JPG, PNG, GIF, WebP, and SVG are allowed.';
        }
    } else {
        $error = 'Upload error: ' . $file['error'];
    }
}

// Handle file deletion
if (isset($_GET['delete'])) {
    $filename = basename($_GET['delete']);
    $filePath = SITE_PATH . $filename;
    
    if (file_exists($filePath) && is_file($filePath)) {
        // Create backup before deleting
        $backupPath = SITE_PATH . 'backups/';
        if (!is_dir($backupPath)) {
            mkdir($backupPath, 0755, true);
        }
        copy($filePath, $backupPath . $filename . '.' . date('Y-m-d-H-i-s') . '.deleted');
        
        if (unlink($filePath)) {
            $success = 'Image deleted successfully!';
        } else {
            $error = 'Failed to delete image.';
        }
    }
}

// Get list of images
$imageExtensions = ['jpg', 'jpeg', 'png', 'gif', 'webp', 'svg'];
$images = [];

$files = scandir(SITE_PATH);
foreach ($files as $file) {
    if ($file === '.' || $file === '..') continue;
    
    $ext = strtolower(pathinfo($file, PATHINFO_EXTENSION));
    if (in_array($ext, $imageExtensions)) {
        $filePath = SITE_PATH . $file;
        $images[] = [
            'name' => $file,
            'size' => filesize($filePath),
            'modified' => filemtime($filePath)
        ];
    }
}

// Sort by modified date (newest first)
usort($images, function($a, $b) {
    return $b['modified'] - $a['modified'];
});
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Image Manager - Mikey Mobile Admin</title>
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
        
        .upload-section {
            background: white;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            margin-bottom: 30px;
        }
        
        .upload-area {
            border: 3px dashed #667eea;
            border-radius: 12px;
            padding: 40px;
            text-align: center;
            background: #f8f9ff;
            cursor: pointer;
            transition: all 0.3s;
        }
        
        .upload-area:hover {
            background: #f0f2ff;
            border-color: #5568d3;
        }
        
        .upload-area input[type="file"] {
            display: none;
        }
        
        .upload-btn {
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
        
        .images-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
            gap: 20px;
        }
        
        .image-card {
            background: white;
            border-radius: 12px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            overflow: hidden;
            transition: transform 0.3s;
        }
        
        .image-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 30px rgba(0,0,0,0.15);
        }
        
        .image-preview {
            width: 100%;
            height: 200px;
            object-fit: cover;
            background: #f5f5f5;
        }
        
        .image-info {
            padding: 15px;
        }
        
        .image-name {
            font-weight: 600;
            color: #333;
            margin-bottom: 8px;
            word-break: break-all;
        }
        
        .image-meta {
            font-size: 12px;
            color: #666;
            margin-bottom: 10px;
        }
        
        .image-actions {
            display: flex;
            gap: 10px;
        }
        
        .btn-copy {
            flex: 1;
            padding: 8px;
            background: #667eea;
            color: white;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-size: 13px;
        }
        
        .btn-delete {
            padding: 8px 15px;
            background: #f44336;
            color: white;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-size: 13px;
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
    </style>
</head>
<body>
    <div class="header">
        <h1>🖼️ Image Manager</h1>
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
        
        <div class="upload-section">
            <h2>Upload New Image</h2>
            <p style="color: #666; margin-bottom: 20px;">Upload images to your website. Supported formats: JPG, PNG, GIF, WebP, SVG (Max 10MB)</p>
            
            <form method="POST" enctype="multipart/form-data" id="uploadForm">
                <div class="upload-area" onclick="document.getElementById('fileInput').click()">
                    <div style="font-size: 48px; margin-bottom: 15px;">📤</div>
                    <p style="font-size: 18px; color: #667eea; margin-bottom: 10px;">Click to select an image</p>
                    <p style="font-size: 14px; color: #999;">or drag and drop here</p>
                    <input type="file" id="fileInput" name="image" accept="image/*" onchange="document.getElementById('uploadForm').submit()">
                </div>
            </form>
        </div>
        
        <div style="background: white; padding: 30px; border-radius: 12px; box-shadow: 0 2px 10px rgba(0,0,0,0.1);">
            <h2 style="margin-bottom: 20px;">Your Images (<?php echo count($images); ?>)</h2>
            
            <div class="images-grid">
                <?php foreach ($images as $image): ?>
                    <div class="image-card">
                        <img src="<?php echo htmlspecialchars($image['name']); ?>" 
                             alt="<?php echo htmlspecialchars($image['name']); ?>" 
                             class="image-preview"
                             onerror="this.src='data:image/svg+xml,%3Csvg xmlns=%22http://www.w3.org/2000/svg%22 width=%22200%22 height=%22200%22%3E%3Crect fill=%22%23ddd%22 width=%22200%22 height=%22200%22/%3E%3Ctext x=%2250%25%22 y=%2250%25%22 text-anchor=%22middle%22 dy=%22.3em%22 fill=%22%23999%22%3EImage%3C/text%3E%3C/svg%3E'">
                        <div class="image-info">
                            <div class="image-name"><?php echo htmlspecialchars($image['name']); ?></div>
                            <div class="image-meta">
                                Size: <?php echo number_format($image['size'] / 1024, 1); ?> KB<br>
                                Modified: <?php echo date('M d, Y', $image['modified']); ?>
                            </div>
                            <div class="image-actions">
                                <button class="btn-copy" onclick="copyPath('<?php echo htmlspecialchars($image['name']); ?>')">
                                    📋 Copy Path
                                </button>
                                <button class="btn-delete" onclick="deleteImage('<?php echo htmlspecialchars($image['name']); ?>')">
                                    🗑️
                                </button>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
    
    <script>
        function copyPath(filename) {
            const path = '/' + filename;
            navigator.clipboard.writeText(path).then(() => {
                alert('Path copied to clipboard: ' + path);
            });
        }
        
        function deleteImage(filename) {
            if (confirm('Are you sure you want to delete ' + filename + '? A backup will be created.')) {
                window.location.href = 'images.php?delete=' + encodeURIComponent(filename);
            }
        }
    </script>
</body>
</html>
