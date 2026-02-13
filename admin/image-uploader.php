<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Image Uploader - Mikey Mobile Admin</title>
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
        
        .upload-area {
            border: 3px dashed #667eea;
            border-radius: 10px;
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
        
        .upload-area.dragover {
            background: #e7ebff;
            border-color: #4557c2;
        }
        
        .upload-icon {
            font-size: 48px;
            margin-bottom: 15px;
        }
        
        .upload-text {
            color: #333;
            font-size: 18px;
            margin-bottom: 10px;
        }
        
        .upload-subtext {
            color: #666;
            font-size: 14px;
        }
        
        #fileInput {
            display: none;
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
        
        .image-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
            gap: 20px;
            margin-top: 30px;
        }
        
        .image-card {
            border: 2px solid #e0e0e0;
            border-radius: 8px;
            overflow: hidden;
            transition: all 0.3s;
        }
        
        .image-card:hover {
            border-color: #667eea;
            box-shadow: 0 4px 12px rgba(102, 126, 234, 0.2);
        }
        
        .image-card img {
            width: 100%;
            height: 150px;
            object-fit: cover;
        }
        
        .image-info {
            padding: 10px;
        }
        
        .image-name {
            color: #333;
            font-size: 14px;
            font-weight: 500;
            margin-bottom: 5px;
            word-break: break-all;
        }
        
        .image-path {
            color: #666;
            font-size: 12px;
            font-family: monospace;
            background: #f0f0f0;
            padding: 5px;
            border-radius: 3px;
            word-break: break-all;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>🖼️ Image Uploader</h1>
            <a href="index.php" class="back-link">← Back to Dashboard</a>
        </div>

        <?php
        $message = '';
        $messageType = '';

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['image'])) {
            $uploadDir = '../images/';
            
            // Create images directory if it doesn't exist
            if (!file_exists($uploadDir)) {
                mkdir($uploadDir, 0755, true);
            }
            
            $file = $_FILES['image'];
            $fileName = basename($file['name']);
            $targetPath = $uploadDir . $fileName;
            
            // Check if file is an actual image
            $imageFileType = strtolower(pathinfo($targetPath, PATHINFO_EXTENSION));
            $allowedTypes = ['jpg', 'jpeg', 'png', 'gif', 'webp', 'svg'];
            
            if (in_array($imageFileType, $allowedTypes)) {
                if (move_uploaded_file($file['tmp_name'], $targetPath)) {
                    $message = 'Image uploaded successfully! Path: images/' . $fileName;
                    $messageType = 'success';
                } else {
                    $message = 'Error uploading file. Check directory permissions.';
                    $messageType = 'error';
                }
            } else {
                $message = 'Only image files are allowed (JPG, JPEG, PNG, GIF, WEBP, SVG).';
                $messageType = 'error';
            }
        }

        // Get list of existing images
        $imageDir = '../images/';
        $images = [];
        if (file_exists($imageDir)) {
            $files = scandir($imageDir);
            foreach ($files as $file) {
                if ($file !== '.' && $file !== '..' && !is_dir($imageDir . $file)) {
                    $ext = strtolower(pathinfo($file, PATHINFO_EXTENSION));
                    if (in_array($ext, ['jpg', 'jpeg', 'png', 'gif', 'webp', 'svg'])) {
                        $images[] = $file;
                    }
                }
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
                <p><strong>💡 How to use:</strong> Upload images here and they will be saved to the <code>images/</code> directory. You can then reference them in your HTML using <code>&lt;img src="images/filename.jpg"&gt;</code>. Supported formats: JPG, PNG, GIF, WEBP, SVG.</p>
            </div>

            <form method="POST" enctype="multipart/form-data" id="uploadForm">
                <div class="upload-area" onclick="document.getElementById('fileInput').click()" id="uploadArea">
                    <div class="upload-icon">📤</div>
                    <div class="upload-text">Click to upload or drag and drop</div>
                    <div class="upload-subtext">JPG, PNG, GIF, WEBP, SVG (Max 10MB)</div>
                </div>
                <input type="file" name="image" id="fileInput" accept="image/*" onchange="document.getElementById('uploadForm').submit()">
            </form>

            <?php if (!empty($images)): ?>
                <h2 style="color: #333; margin-top: 40px; margin-bottom: 20px;">📁 Uploaded Images</h2>
                <div class="image-grid">
                    <?php foreach ($images as $image): ?>
                        <div class="image-card">
                            <img src="../images/<?php echo htmlspecialchars($image); ?>" alt="<?php echo htmlspecialchars($image); ?>">
                            <div class="image-info">
                                <div class="image-name"><?php echo htmlspecialchars($image); ?></div>
                                <div class="image-path">images/<?php echo htmlspecialchars($image); ?></div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <p style="text-align: center; color: #666; margin-top: 30px;">No images uploaded yet.</p>
            <?php endif; ?>
        </div>
    </div>

    <script>
        // Drag and drop functionality
        const uploadArea = document.getElementById('uploadArea');
        const fileInput = document.getElementById('fileInput');
        const uploadForm = document.getElementById('uploadForm');

        ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
            uploadArea.addEventListener(eventName, preventDefaults, false);
        });

        function preventDefaults(e) {
            e.preventDefault();
            e.stopPropagation();
        }

        ['dragenter', 'dragover'].forEach(eventName => {
            uploadArea.addEventListener(eventName, () => {
                uploadArea.classList.add('dragover');
            }, false);
        });

        ['dragleave', 'drop'].forEach(eventName => {
            uploadArea.addEventListener(eventName, () => {
                uploadArea.classList.remove('dragover');
            }, false);
        });

        uploadArea.addEventListener('drop', (e) => {
            const dt = e.dataTransfer;
            const files = dt.files;
            
            if (files.length > 0) {
                fileInput.files = files;
                uploadForm.submit();
            }
        }, false);
    </script>
</body>
</html>
