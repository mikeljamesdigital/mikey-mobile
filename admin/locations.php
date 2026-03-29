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

// Define location structure
$locations = [
    'fresno' => [
        'name' => 'Fresno',
        'main' => 'index.html',
        'neighborhoods' => [
            'fig-garden' => 'Fig Garden',
            'sunnyside' => 'Sunnyside',
            'woodward-park' => 'Woodward Park',
            'fig-garden-loop' => 'Fig Garden Loop',
            'riverpark' => 'Riverpark',
            'pinedale' => 'Pinedale',
            'sierra-sky-park' => 'Sierra Sky Park',
            'fort-washington' => 'Fort Washington'
        ]
    ],
    'clovis' => [
        'name' => 'Clovis',
        'main' => 'index.html',
        'neighborhoods' => [
            'clovis-north' => 'Clovis North',
            'cindy-lane' => 'Cindy Lane',
            'dry-creek' => 'Dry Creek',
            'harlan-ranch' => 'Harlan Ranch',
            'clovis-high' => 'Clovis High',
            'quail-lakes' => 'Quail Lakes'
        ]
    ],
    'madera-ranchos' => [
        'name' => 'Madera Ranchos',
        'main' => 'index.html',
        'neighborhoods' => [
            'rolling-hills' => 'Rolling Hills',
            'riverstone' => 'Riverstone'
        ]
    ]
];

// Get current location
$currentCity = $_GET['city'] ?? 'fresno';
$currentPage = $_GET['page'] ?? 'index.html';

$pageContent = '';
$filePath = '';

if (isset($locations[$currentCity])) {
    if ($currentPage === 'index.html') {
        $filePath = SITE_PATH . $currentCity . '/index.html';
    } else {
        $filePath = SITE_PATH . $currentCity . '/' . $currentPage . '/index.html';
    }
    
    if (file_exists($filePath)) {
        $pageContent = file_get_contents($filePath);
    }
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    if ($_POST['action'] === 'save_location') {
        $city = $_POST['city'] ?? '';
        $page = $_POST['page'] ?? '';
        $content = $_POST['content'] ?? '';
        
        if (isset($locations[$city])) {
            if ($page === 'index.html') {
                $savePath = SITE_PATH . $city . '/index.html';
            } else {
                $savePath = SITE_PATH . $city . '/' . $page . '/index.html';
            }
            
            // Create backup
            $backupPath = SITE_PATH . 'backups/';
            if (!is_dir($backupPath)) {
                mkdir($backupPath, 0755, true);
            }
            if (file_exists($savePath)) {
                copy($savePath, $backupPath . $city . '-' . str_replace('/', '-', $page) . '.' . date('Y-m-d-H-i-s') . '.bak');
            }
            
            // Save new content
            if (file_put_contents($savePath, $content)) {
                $success = 'Location page saved successfully!';
                $pageContent = $content;
            } else {
                $error = 'Failed to save location page.';
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
    <title>Location Pages Manager - Mikey Mobile Admin</title>
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
        
        .location-selector {
            background: white;
            padding: 20px;
            border-radius: 12px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            margin-bottom: 20px;
            display: flex;
            gap: 15px;
            align-items: center;
            flex-wrap: wrap;
        }
        
        .location-selector select {
            padding: 10px 15px;
            border: 2px solid #e0e0e0;
            border-radius: 8px;
            font-size: 15px;
            min-width: 200px;
        }
        
        .editor-container {
            background: white;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
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
            background: #fff3cd;
            padding: 20px;
            border-radius: 8px;
            margin-top: 20px;
            border-left: 4px solid #ffc107;
        }
        
        .info-box h4 {
            color: #856404;
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
        <h1>📍 Location Pages Manager</h1>
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
        
        <div class="location-selector">
            <div>
                <label for="citySelect"><strong>Select City:</strong></label>
                <select id="citySelect" onchange="updatePageSelector()">
                    <?php foreach ($locations as $cityKey => $cityData): ?>
                        <option value="<?php echo $cityKey; ?>" <?php echo $currentCity === $cityKey ? 'selected' : ''; ?>>
                            <?php echo $cityData['name']; ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <div>
                <label for="pageSelect"><strong>Select Page:</strong></label>
                <select id="pageSelect" onchange="navigateToPage()">
                    <option value="index.html" <?php echo $currentPage === 'index.html' ? 'selected' : ''; ?>>
                        Main City Page
                    </option>
                    <?php if (isset($locations[$currentCity]['neighborhoods'])): ?>
                        <?php foreach ($locations[$currentCity]['neighborhoods'] as $key => $name): ?>
                            <option value="<?php echo $key; ?>" <?php echo $currentPage === $key ? 'selected' : ''; ?>>
                                <?php echo $name; ?>
                            </option>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </select>
            </div>
        </div>
        
        <div class="editor-container">
            <h2>Editing: <?php echo $locations[$currentCity]['name']; ?> - 
                <?php 
                if ($currentPage === 'index.html') {
                    echo 'Main Page';
                } else {
                    echo $locations[$currentCity]['neighborhoods'][$currentPage] ?? $currentPage;
                }
                ?>
            </h2>
            <p style="color: #666; margin-bottom: 20px;">Edit the location page content below.</p>
            
            <form method="POST">
                <input type="hidden" name="action" value="save_location">
                <input type="hidden" name="city" value="<?php echo htmlspecialchars($currentCity); ?>">
                <input type="hidden" name="page" value="<?php echo htmlspecialchars($currentPage); ?>">
                
                <textarea name="content" id="editor"><?php echo htmlspecialchars($pageContent); ?></textarea>
                
                <button type="submit" class="save-btn">💾 Save Location Page</button>
            </form>
            
            <div class="info-box">
                <h4>📝 Editing Location Pages:</h4>
                <ul>
                    <li>Each city has a main page and multiple neighborhood pages</li>
                    <li>Update location-specific content like addresses, landmarks, and service areas</li>
                    <li>Backups are created automatically when you save</li>
                    <li>Be careful with HTML syntax to avoid breaking the page layout</li>
                </ul>
            </div>
        </div>
    </div>
    
    <script>
        const locations = <?php echo json_encode($locations); ?>;
        
        function updatePageSelector() {
            const city = document.getElementById('citySelect').value;
            const pageSelect = document.getElementById('pageSelect');
            
            // Clear existing options
            pageSelect.innerHTML = '<option value="index.html">Main City Page</option>';
            
            // Add neighborhood options
            if (locations[city] && locations[city].neighborhoods) {
                for (const [key, name] of Object.entries(locations[city].neighborhoods)) {
                    const option = document.createElement('option');
                    option.value = key;
                    option.textContent = name;
                    pageSelect.appendChild(option);
                }
            }
            
            navigateToPage();
        }
        
        function navigateToPage() {
            const city = document.getElementById('citySelect').value;
            const page = document.getElementById('pageSelect').value;
            window.location.href = `locations.php?city=${city}&page=${page}`;
        }
    </script>
</body>
</html>
