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
$downloadReady = false;

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['generate_navigation'])) {
    $menuItems = json_decode($_POST['menu_data'], true);
    
    if ($menuItems) {
        // Save menu data to JSON file
        $jsonPath = __DIR__ . '/navigation-data.json';
        file_put_contents($jsonPath, json_encode($menuItems, JSON_PRETTY_PRINT));
        
        // Generate navigation HTML
        $navHTML = generateNavigationHTML($menuItems);
        
        // Create updated HTML files in a temp directory
        $tempDir = __DIR__ . '/temp_files/';
        if (!is_dir($tempDir)) {
            mkdir($tempDir, 0755, true);
        }
        
        $files = ['index.html', 'about.html', 'locations.html'];
        $generated = 0;
        
        foreach ($files as $file) {
            $filePath = SITE_PATH . $file;
            if (file_exists($filePath)) {
                $content = file_get_contents($filePath);
                
                // Replace navigation
                $pattern = '/<!-- Navigation -->.*?<\/nav>/s';
                $replacement = "<!-- Navigation -->\n    " . $navHTML;
                $newContent = preg_replace($pattern, $replacement, $content);
                
                if ($newContent) {
                    file_put_contents($tempDir . $file, $newContent);
                    $generated++;
                }
            }
        }
        
        if ($generated > 0) {
            $success = "Navigation HTML generated! Download the files below and upload them via SFTP.";
            $downloadReady = true;
        } else {
            $error = "Failed to generate navigation files.";
        }
    } else {
        $error = "Invalid menu data.";
    }
}

// Handle file download
if (isset($_GET['download'])) {
    $file = basename($_GET['download']);
    $filePath = __DIR__ . '/temp_files/' . $file;
    
    if (file_exists($filePath)) {
        header('Content-Type: text/html');
        header('Content-Disposition: attachment; filename="' . $file . '"');
        header('Content-Length: ' . filesize($filePath));
        readfile($filePath);
        exit;
    }
}

function generateNavigationHTML($menuItems) {
    $html = '<nav class="navbar">
        <div class="container nav-container">
            <button class="mobile-menu-toggle" onclick="document.getElementById(\'mobileMenu\').classList.add(\'active\')">
                ☰
            </button>
            <div class="nav-left">';
    
    foreach ($menuItems as $item) {
        if (!empty($item['submenu'])) {
            // Dropdown menu
            $html .= "\n                <div class=\"nav-dropdown\">";
            $html .= "\n                    <a href=\"" . htmlspecialchars($item['url']) . "\" class=\"nav-link dropdown-toggle\">" . htmlspecialchars($item['label']) . "</a>";
            $html .= "\n                    <div class=\"dropdown-menu\">";
            
            // Group submenu items by section (every 8 items)
            $chunks = array_chunk($item['submenu'], 8);
            foreach ($chunks as $chunk) {
                $html .= "\n                        <div class=\"dropdown-section\">";
                foreach ($chunk as $subitem) {
                    $class = $subitem['is_main'] ? 'dropdown-main' : 'dropdown-sub';
                    $html .= "\n                            <a href=\"" . htmlspecialchars($subitem['url']) . "\" class=\"$class\">" . htmlspecialchars($subitem['label']) . "</a>";
                }
                $html .= "\n                        </div>";
            }
            
            $html .= "\n                    </div>";
            $html .= "\n                </div>";
        } else {
            // Regular menu item
            $html .= "\n                <a href=\"" . htmlspecialchars($item['url']) . "\" class=\"nav-link\">" . htmlspecialchars($item['label']) . "</a>";
        }
    }
    
    $html .= '
            </div>
            <div class="nav-center">
                <a href="index.html" class="logo">
                    <img src="mikey-mobile-logo.png" alt="Mikey Mobile Oil Change">
                </a>
            </div>
            <div class="nav-right">
                <a href="#services" class="nav-link">SERVICES</a>
                <a href="#pricing" class="nav-link">PRICING</a>
            </div>
        </div>
    </nav>';
    
    return $html;
}

// Load current navigation from JSON or parse from HTML
$currentNav = [];
$jsonPath = __DIR__ . '/navigation-data.json';

if (file_exists($jsonPath)) {
    $currentNav = json_decode(file_get_contents($jsonPath), true);
} else {
    // Default navigation structure
    $currentNav = [
        ['label' => 'HOME', 'url' => 'index.html', 'submenu' => []],
        ['label' => 'ABOUT US', 'url' => 'about.html', 'submenu' => []],
        ['label' => 'LOCATIONS', 'url' => 'locations.html', 'submenu' => [
            ['label' => 'Fresno', 'url' => 'fresno/index.html', 'is_main' => true],
            ['label' => 'Fig Garden', 'url' => 'fresno/fig-garden/index.html', 'is_main' => false],
            ['label' => 'Sunnyside', 'url' => 'fresno/sunnyside/index.html', 'is_main' => false],
            ['label' => 'Woodward Park', 'url' => 'fresno/woodward-park/index.html', 'is_main' => false],
            ['label' => 'Fig Garden Loop', 'url' => 'fresno/fig-garden-loop/index.html', 'is_main' => false],
            ['label' => 'Riverpark', 'url' => 'fresno/riverpark/index.html', 'is_main' => false],
            ['label' => 'Pinedale', 'url' => 'fresno/pinedale/index.html', 'is_main' => false],
            ['label' => 'Sierra Sky Park', 'url' => 'fresno/sierra-sky-park/index.html', 'is_main' => false],
            ['label' => 'Fort Washington', 'url' => 'fresno/fort-washington/index.html', 'is_main' => false],
            ['label' => 'Clovis', 'url' => 'clovis/index.html', 'is_main' => true],
            ['label' => 'Clovis North', 'url' => 'clovis/clovis-north/index.html', 'is_main' => false],
            ['label' => 'Cindy Lane', 'url' => 'clovis/cindy-lane/index.html', 'is_main' => false],
            ['label' => 'Dry Creek', 'url' => 'clovis/dry-creek/index.html', 'is_main' => false],
            ['label' => 'Harlan Ranch', 'url' => 'clovis/harlan-ranch/index.html', 'is_main' => false],
            ['label' => 'Clovis High', 'url' => 'clovis/clovis-high/index.html', 'is_main' => false],
            ['label' => 'Quail Lakes', 'url' => 'clovis/quail-lakes/index.html', 'is_main' => false],
            ['label' => 'Madera Ranchos', 'url' => 'madera-ranchos/index.html', 'is_main' => true],
            ['label' => 'Rolling Hills', 'url' => 'madera-ranchos/rolling-hills/index.html', 'is_main' => false],
            ['label' => 'Riverstone', 'url' => 'madera-ranchos/riverstone/index.html', 'is_main' => false],
        ]],
    ];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Navigation Editor - Mikey Mobile Admin</title>
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
        
        .editor-section {
            background: white;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            margin-bottom: 30px;
        }
        
        .menu-item {
            background: #f8f9fa;
            border: 2px solid #e9ecef;
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 15px;
        }
        
        .menu-item-header {
            display: flex;
            gap: 15px;
            align-items: center;
            margin-bottom: 15px;
        }
        
        .menu-item-header input {
            flex: 1;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 6px;
            font-size: 14px;
        }
        
        .submenu-container {
            margin-top: 15px;
            padding-left: 30px;
            border-left: 3px solid #667eea;
        }
        
        .submenu-item {
            background: white;
            padding: 10px;
            margin-bottom: 10px;
            border-radius: 6px;
            display: flex;
            gap: 10px;
            align-items: center;
        }
        
        .submenu-item input[type="text"] {
            flex: 1;
            padding: 8px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 13px;
        }
        
        .submenu-item input[type="checkbox"] {
            width: 20px;
            height: 20px;
        }
        
        .btn {
            padding: 10px 20px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-size: 14px;
            font-weight: 600;
            transition: all 0.3s;
            text-decoration: none;
            display: inline-block;
        }
        
        .btn-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
        }
        
        .btn-secondary {
            background: #6c757d;
            color: white;
        }
        
        .btn-success {
            background: #28a745;
            color: white;
        }
        
        .btn-danger {
            background: #dc3545;
            color: white;
        }
        
        .btn:hover {
            opacity: 0.9;
            transform: translateY(-2px);
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
        
        .download-section {
            background: #e7f3ff;
            padding: 20px;
            border-radius: 8px;
            border-left: 4px solid #007bff;
            margin-top: 20px;
        }
        
        .download-section h3 {
            margin-bottom: 15px;
            color: #004085;
        }
        
        .download-links {
            display: flex;
            gap: 15px;
            flex-wrap: wrap;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>📋 Navigation Editor</h1>
        <div style="display: flex; gap: 15px;">
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
        
        <?php if ($downloadReady): ?>
            <div class="download-section">
                <h3>📥 Download Updated Files</h3>
                <p style="margin-bottom: 15px;">Download these files and upload them to your server via SFTP:</p>
                <div class="download-links">
                    <a href="?download=index.html" class="btn btn-primary">Download index.html</a>
                    <a href="?download=about.html" class="btn btn-primary">Download about.html</a>
                    <a href="?download=locations.html" class="btn btn-primary">Download locations.html</a>
                </div>
                <p style="margin-top: 15px; font-size: 13px; color: #666;">
                    <strong>SFTP Details:</strong><br>
                    Host: 143.244.190.129<br>
                    Username: master_npwtmaftpw<br>
                    Upload to: /home/master/applications/njfxrssvcj/public_html/
                </p>
            </div>
        <?php endif; ?>
        
        <div class="editor-section">
            <h2 style="margin-bottom: 20px;">Edit Navigation Menu</h2>
            <p style="color: #666; margin-bottom: 30px;">Manage your top-level menu items and their sub-menus. Sub-menu items can be marked as "Main" to appear bold in the dropdown.</p>
            
            <form method="POST" id="navForm">
                <input type="hidden" name="generate_navigation" value="1">
                <input type="hidden" name="menu_data" id="menuData">
                
                <div id="menuItems">
                    <?php foreach ($currentNav as $index => $item): ?>
                        <div class="menu-item" data-index="<?php echo $index; ?>">
                            <div class="menu-item-header">
                                <input type="text" placeholder="Menu Label" value="<?php echo htmlspecialchars($item['label']); ?>" class="menu-label">
                                <input type="text" placeholder="URL" value="<?php echo htmlspecialchars($item['url']); ?>" class="menu-url">
                                <button type="button" class="btn btn-secondary" onclick="toggleSubmenu(<?php echo $index; ?>)">+ Sub-menu</button>
                                <button type="button" class="btn btn-danger" onclick="removeMenuItem(<?php echo $index; ?>)">Remove</button>
                            </div>
                            
                            <?php if (!empty($item['submenu'])): ?>
                                <div class="submenu-container" id="submenu-<?php echo $index; ?>">
                                    <h4 style="margin-bottom: 10px; color: #667eea;">Sub-menu Items:</h4>
                                    <?php foreach ($item['submenu'] as $subindex => $subitem): ?>
                                        <div class="submenu-item">
                                            <input type="text" placeholder="Label" value="<?php echo htmlspecialchars($subitem['label']); ?>" class="submenu-label">
                                            <input type="text" placeholder="URL" value="<?php echo htmlspecialchars($subitem['url']); ?>" class="submenu-url">
                                            <label style="display: flex; align-items: center; gap: 5px; white-space: nowrap;">
                                                <input type="checkbox" class="submenu-main" <?php echo $subitem['is_main'] ? 'checked' : ''; ?>>
                                                Main
                                            </label>
                                            <button type="button" class="btn btn-danger btn-sm" onclick="removeSubmenuItem(this)">×</button>
                                        </div>
                                    <?php endforeach; ?>
                                    <button type="button" class="btn btn-secondary" onclick="addSubmenuItem(<?php echo $index; ?>)">+ Add Sub-item</button>
                                </div>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>
                </div>
                
                <div style="margin-top: 30px; display: flex; gap: 15px;">
                    <button type="button" class="btn btn-secondary" onclick="addMenuItem()">+ Add Menu Item</button>
                    <button type="submit" class="btn btn-success" onclick="return saveNavigation()">💾 Generate Files</button>
                </div>
            </form>
        </div>
    </div>
    
    <script>
        function toggleSubmenu(index) {
            const container = document.getElementById('submenu-' + index);
            if (container) {
                container.style.display = container.style.display === 'none' ? 'block' : 'none';
            } else {
                const menuItem = document.querySelector(`.menu-item[data-index="${index}"]`);
                const submenuHTML = `
                    <div class="submenu-container" id="submenu-${index}">
                        <h4 style="margin-bottom: 10px; color: #667eea;">Sub-menu Items:</h4>
                        <button type="button" class="btn btn-secondary" onclick="addSubmenuItem(${index})">+ Add Sub-item</button>
                    </div>
                `;
                menuItem.insertAdjacentHTML('beforeend', submenuHTML);
            }
        }
        
        function addSubmenuItem(index) {
            const container = document.getElementById('submenu-' + index);
            const submenuHTML = `
                <div class="submenu-item">
                    <input type="text" placeholder="Label" class="submenu-label">
                    <input type="text" placeholder="URL" class="submenu-url">
                    <label style="display: flex; align-items: center; gap: 5px; white-space: nowrap;">
                        <input type="checkbox" class="submenu-main">
                        Main
                    </label>
                    <button type="button" class="btn btn-danger btn-sm" onclick="removeSubmenuItem(this)">×</button>
                </div>
            `;
            const addButton = container.querySelector('button');
            addButton.insertAdjacentHTML('beforebegin', submenuHTML);
        }
        
        function removeSubmenuItem(button) {
            button.closest('.submenu-item').remove();
        }
        
        function addMenuItem() {
            const menuItems = document.getElementById('menuItems');
            const index = menuItems.children.length;
            const menuHTML = `
                <div class="menu-item" data-index="${index}">
                    <div class="menu-item-header">
                        <input type="text" placeholder="Menu Label" class="menu-label">
                        <input type="text" placeholder="URL" class="menu-url">
                        <button type="button" class="btn btn-secondary" onclick="toggleSubmenu(${index})">+ Sub-menu</button>
                        <button type="button" class="btn btn-danger" onclick="removeMenuItem(${index})">Remove</button>
                    </div>
                </div>
            `;
            menuItems.insertAdjacentHTML('beforeend', menuHTML);
        }
        
        function removeMenuItem(index) {
            const menuItem = document.querySelector(`.menu-item[data-index="${index}"]`);
            if (confirm('Remove this menu item?')) {
                menuItem.remove();
            }
        }
        
        function saveNavigation() {
            const menuItems = [];
            document.querySelectorAll('.menu-item').forEach(item => {
                const label = item.querySelector('.menu-label').value;
                const url = item.querySelector('.menu-url').value;
                
                if (!label || !url) return;
                
                const menuItem = { label, url, submenu: [] };
                
                const submenuContainer = item.querySelector('.submenu-container');
                if (submenuContainer) {
                    submenuContainer.querySelectorAll('.submenu-item').forEach(subitem => {
                        const sublabel = subitem.querySelector('.submenu-label').value;
                        const suburl = subitem.querySelector('.submenu-url').value;
                        const isMain = subitem.querySelector('.submenu-main').checked;
                        
                        if (sublabel && suburl) {
                            menuItem.submenu.push({ label: sublabel, url: suburl, is_main: isMain });
                        }
                    });
                }
                
                menuItems.push(menuItem);
            });
            
            document.getElementById('menuData').value = JSON.stringify(menuItems);
            return true;
        }
    </script>
</body>
</html>
