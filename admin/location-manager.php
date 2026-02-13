<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Location Manager - Mikey Mobile Admin</title>
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
        
        .location-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 20px;
            margin-top: 20px;
        }
        
        .location-card {
            border: 2px solid #e0e0e0;
            border-radius: 8px;
            padding: 20px;
            transition: all 0.3s;
        }
        
        .location-card:hover {
            border-color: #667eea;
            box-shadow: 0 4px 12px rgba(102, 126, 234, 0.2);
        }
        
        .location-card h3 {
            color: #333;
            margin-bottom: 10px;
            font-size: 18px;
        }
        
        .location-card p {
            color: #666;
            font-size: 14px;
            margin-bottom: 15px;
        }
        
        .location-card .actions {
            display: flex;
            gap: 10px;
        }
        
        .btn {
            padding: 8px 16px;
            border: none;
            border-radius: 4px;
            font-size: 14px;
            cursor: pointer;
            text-decoration: none;
            display: inline-block;
            transition: all 0.3s;
        }
        
        .btn-edit {
            background: #667eea;
            color: white;
        }
        
        .btn-edit:hover {
            background: #5568d3;
        }
        
        .btn-view {
            background: #f0f0f0;
            color: #333;
        }
        
        .btn-view:hover {
            background: #e0e0e0;
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
        
        .section-title {
            color: #333;
            font-size: 20px;
            margin-bottom: 15px;
            padding-bottom: 10px;
            border-bottom: 2px solid #e0e0e0;
        }
        
        .neighborhood-list {
            list-style: none;
            padding-left: 20px;
        }
        
        .neighborhood-list li {
            padding: 5px 0;
            color: #666;
        }
        
        .neighborhood-list li:before {
            content: "→ ";
            color: #667eea;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>📍 Location Manager</h1>
            <a href="index.php" class="back-link">← Back to Dashboard</a>
        </div>

        <div class="card">
            <div class="info-box">
                <p><strong>💡 About Location Manager:</strong> This page shows all your location pages. To edit a location page, click "Edit Content" which will take you to the Content Editor. To add new locations, you'll need to create new HTML files and add them to the navigation via the Navigation Editor.</p>
            </div>

            <h2 class="section-title">🏙️ City Pages</h2>
            <div class="location-grid">
                <div class="location-card">
                    <h3>Fresno</h3>
                    <p>Main city page with 8 neighborhoods</p>
                    <ul class="neighborhood-list">
                        <li>Fig Garden</li>
                        <li>Sunnyside</li>
                        <li>Fig Garden Loop</li>
                        <li>Woodward Park</li>
                        <li>Riverpark</li>
                        <li>Pinedale</li>
                        <li>Sierra Sky Park</li>
                        <li>Fort Washington</li>
                    </ul>
                    <div class="actions" style="margin-top: 15px;">
                        <a href="content-editor.php?page=fresno/index.html" class="btn btn-edit">✏️ Edit Content</a>
                        <a href="../fresno/index.html" target="_blank" class="btn btn-view">👁️ View Page</a>
                    </div>
                </div>

                <div class="location-card">
                    <h3>Clovis</h3>
                    <p>Main city page with 6 neighborhoods</p>
                    <ul class="neighborhood-list">
                        <li>Clovis North</li>
                        <li>Cindy Lane</li>
                        <li>Dry Creek</li>
                        <li>Clovis High</li>
                        <li>Quail Lakes</li>
                        <li>Harlan Ranch</li>
                    </ul>
                    <div class="actions" style="margin-top: 15px;">
                        <a href="content-editor.php?page=clovis/index.html" class="btn btn-edit">✏️ Edit Content</a>
                        <a href="../clovis/index.html" target="_blank" class="btn btn-view">👁️ View Page</a>
                    </div>
                </div>

                <div class="location-card">
                    <h3>Madera Ranchos</h3>
                    <p>Main city page with 2 neighborhoods</p>
                    <ul class="neighborhood-list">
                        <li>Rolling Hills</li>
                        <li>Riverstone</li>
                    </ul>
                    <div class="actions" style="margin-top: 15px;">
                        <a href="content-editor.php?page=madera-ranchos/index.html" class="btn btn-edit">✏️ Edit Content</a>
                        <a href="../madera-ranchos/index.html" target="_blank" class="btn btn-view">👁️ View Page</a>
                    </div>
                </div>
            </div>

            <h2 class="section-title" style="margin-top: 40px;">🏘️ Neighborhood Pages</h2>
            
            <h3 style="color: #667eea; margin: 20px 0 15px 0;">Fresno Neighborhoods</h3>
            <div class="location-grid">
                <?php
                $fresnoNeighborhoods = [
                    'fig-garden' => 'Fig Garden',
                    'sunnyside' => 'Sunnyside',
                    'fig-garden-loop' => 'Fig Garden Loop',
                    'woodward-park' => 'Woodward Park',
                    'riverpark' => 'Riverpark',
                    'pinedale' => 'Pinedale',
                    'sierra-sky-park' => 'Sierra Sky Park',
                    'fort-washington' => 'Fort Washington'
                ];

                foreach ($fresnoNeighborhoods as $slug => $name) {
                    echo '<div class="location-card">';
                    echo '<h3>' . $name . '</h3>';
                    echo '<p>Fresno neighborhood page</p>';
                    echo '<div class="actions">';
                    echo '<a href="content-editor.php?page=fresno/' . $slug . '/index.html" class="btn btn-edit">✏️ Edit</a>';
                    echo '<a href="../fresno/' . $slug . '/index.html" target="_blank" class="btn btn-view">👁️ View</a>';
                    echo '</div>';
                    echo '</div>';
                }
                ?>
            </div>

            <h3 style="color: #667eea; margin: 30px 0 15px 0;">Clovis Neighborhoods</h3>
            <div class="location-grid">
                <?php
                $clovisNeighborhoods = [
                    'clovis-north' => 'Clovis North',
                    'cindy-lane' => 'Cindy Lane',
                    'dry-creek' => 'Dry Creek',
                    'clovis-high' => 'Clovis High',
                    'quail-lakes' => 'Quail Lakes',
                    'harlan-ranch' => 'Harlan Ranch'
                ];

                foreach ($clovisNeighborhoods as $slug => $name) {
                    echo '<div class="location-card">';
                    echo '<h3>' . $name . '</h3>';
                    echo '<p>Clovis neighborhood page</p>';
                    echo '<div class="actions">';
                    echo '<a href="content-editor.php?page=clovis/' . $slug . '/index.html" class="btn btn-edit">✏️ Edit</a>';
                    echo '<a href="../clovis/' . $slug . '/index.html" target="_blank" class="btn btn-view">👁️ View</a>';
                    echo '</div>';
                    echo '</div>';
                }
                ?>
            </div>

            <h3 style="color: #667eea; margin: 30px 0 15px 0;">Madera Ranchos Neighborhoods</h3>
            <div class="location-grid">
                <?php
                $maderaNeighborhoods = [
                    'rolling-hills' => 'Rolling Hills',
                    'riverstone' => 'Riverstone'
                ];

                foreach ($maderaNeighborhoods as $slug => $name) {
                    echo '<div class="location-card">';
                    echo '<h3>' . $name . '</h3>';
                    echo '<p>Madera Ranchos neighborhood page</p>';
                    echo '<div class="actions">';
                    echo '<a href="content-editor.php?page=madera-ranchos/' . $slug . '/index.html" class="btn btn-edit">✏️ Edit</a>';
                    echo '<a href="../madera-ranchos/' . $slug . '/index.html" target="_blank" class="btn btn-view">👁️ View</a>';
                    echo '</div>';
                    echo '</div>';
                }
                ?>
            </div>
        </div>
    </div>
</body>
</html>
