<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Footer Editor - Mikey Mobile Admin</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, sans-serif;
            background: #f5f5f5;
            padding: 20px;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            background: white;
            border-radius: 12px;
            padding: 30px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        h1 {
            color: #333;
            margin-bottom: 10px;
        }

        .subtitle {
            color: #666;
            margin-bottom: 30px;
        }

        .nav-links {
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 2px solid #e0e0e0;
        }

        .nav-links a {
            display: inline-block;
            margin-right: 20px;
            color: #00BCD4;
            text-decoration: none;
            font-weight: 600;
        }

        .nav-links a:hover {
            text-decoration: underline;
        }

        .section {
            margin-bottom: 30px;
            padding: 20px;
            background: #f9f9f9;
            border-radius: 8px;
        }

        .section h2 {
            color: #333;
            margin-bottom: 15px;
            font-size: 1.3rem;
        }

        .form-group {
            margin-bottom: 20px;
        }

        label {
            display: block;
            margin-bottom: 8px;
            color: #555;
            font-weight: 600;
        }

        input[type="text"],
        input[type="email"],
        textarea {
            width: 100%;
            padding: 12px;
            border: 2px solid #e0e0e0;
            border-radius: 6px;
            font-size: 1rem;
            font-family: inherit;
        }

        input:focus,
        textarea:focus {
            outline: none;
            border-color: #00BCD4;
        }

        textarea {
            min-height: 100px;
            resize: vertical;
        }

        .column-editor {
            border: 2px solid #e0e0e0;
            padding: 15px;
            border-radius: 6px;
            margin-bottom: 15px;
            background: white;
        }

        .column-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 15px;
        }

        .link-item {
            padding: 10px;
            background: #f5f5f5;
            border-radius: 4px;
            margin-bottom: 10px;
        }

        .btn {
            padding: 12px 24px;
            border: none;
            border-radius: 6px;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .btn-primary {
            background: #00BCD4;
            color: white;
        }

        .btn-primary:hover {
            background: #00ACC1;
        }

        .btn-success {
            background: #4CAF50;
            color: white;
        }

        .btn-success:hover {
            background: #45a049;
        }

        .btn-danger {
            background: #f44336;
            color: white;
        }

        .btn-danger:hover {
            background: #da190b;
        }

        .btn-secondary {
            background: #757575;
            color: white;
        }

        .btn-secondary:hover {
            background: #616161;
        }

        .button-group {
            display: flex;
            gap: 15px;
            margin-top: 30px;
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

        .json-editor {
            width: 100%;
            min-height: 400px;
            font-family: 'Courier New', monospace;
            font-size: 14px;
            padding: 15px;
            border: 2px solid #e0e0e0;
            border-radius: 6px;
            background: #f9f9f9;
        }

        .preview {
            background: #2A1548;
            color: white;
            padding: 30px;
            border-radius: 8px;
            margin-top: 20px;
        }

        .preview h3 {
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Footer Editor</h1>
        <p class="subtitle">Edit the footer content that appears on all pages</p>

        <div class="nav-links">
            <a href="navigation-editor.php">← Back to Navigation Editor</a>
            <a href="../index.html" target="_blank">View Website →</a>
        </div>

        <div id="alert-container"></div>

        <div class="section">
            <h2>Edit Footer JSON</h2>
            <p style="margin-bottom: 15px; color: #666;">
                Edit the JSON configuration below. Changes will be saved to footer-config.json and will appear site-wide.
            </p>
            <textarea id="json-editor" class="json-editor"></textarea>
        </div>

        <div class="button-group">
            <button class="btn btn-success" onclick="saveFooter()">💾 Save Footer</button>
            <button class="btn btn-secondary" onclick="loadFooter()">🔄 Reload from File</button>
            <button class="btn btn-primary" onclick="validateJSON()">✓ Validate JSON</button>
        </div>

        <div class="section">
            <h2>Preview</h2>
            <div id="preview" class="preview">
                <p style="color: #999;">Preview will appear here after saving...</p>
            </div>
        </div>
    </div>

    <script>
        // Load footer configuration
        async function loadFooter() {
            try {
                const response = await fetch('../footer-config.json?t=' + Date.now());
                const data = await response.json();
                document.getElementById('json-editor').value = JSON.stringify(data, null, 2);
                showAlert('Footer configuration loaded successfully', 'success');
            } catch (error) {
                showAlert('Error loading footer configuration: ' + error.message, 'error');
            }
        }

        // Validate JSON
        function validateJSON() {
            try {
                const jsonText = document.getElementById('json-editor').value;
                JSON.parse(jsonText);
                showAlert('JSON is valid!', 'success');
                return true;
            } catch (error) {
                showAlert('Invalid JSON: ' + error.message, 'error');
                return false;
            }
        }

        // Save footer configuration
        async function saveFooter() {
            if (!validateJSON()) {
                return;
            }

            const jsonText = document.getElementById('json-editor').value;
            const config = JSON.parse(jsonText);

            try {
                const response = await fetch('save-footer.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: jsonText
                });

                const result = await response.json();

                if (result.success) {
                    showAlert('Footer saved successfully! Changes will appear site-wide.', 'success');
                    generatePreview(config);
                } else {
                    showAlert('Error saving footer: ' + result.message, 'error');
                }
            } catch (error) {
                showAlert('Error saving footer: ' + error.message, 'error');
            }
        }

        // Generate preview
        function generatePreview(config) {
            let html = '<h3>Footer Preview</h3>';
            html += '<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 20px;">';

            config.columns.forEach(column => {
                html += '<div>';
                html += '<h4 style="margin-bottom: 10px;">' + column.title + '</h4>';
                
                if (column.links) {
                    html += '<ul style="list-style: none; padding: 0;">';
                    column.links.forEach(link => {
                        html += '<li style="margin-bottom: 5px;"><a href="' + link.url + '" style="color: #00BCD4;">' + link.label + '</a></li>';
                    });
                    html += '</ul>';
                } else if (column.content) {
                    column.content.forEach(item => {
                        html += '<p style="margin-bottom: 5px; font-size: 0.9rem;">' + item.text + '</p>';
                    });
                }
                
                html += '</div>';
            });

            html += '</div>';
            html += '<div style="margin-top: 20px; padding-top: 20px; border-top: 1px solid rgba(255,255,255,0.2);">';
            html += '<p style="text-align: center; font-size: 0.9rem; opacity: 0.8;">' + config.copyright + ' | ' + config.legal + '</p>';
            html += '</div>';

            document.getElementById('preview').innerHTML = html;
        }

        // Show alert message
        function showAlert(message, type) {
            const alertContainer = document.getElementById('alert-container');
            const alertClass = type === 'success' ? 'alert-success' : 'alert-error';
            alertContainer.innerHTML = '<div class="alert ' + alertClass + '">' + message + '</div>';
            
            setTimeout(() => {
                alertContainer.innerHTML = '';
            }, 5000);
        }

        // Load footer on page load
        loadFooter();
    </script>
</body>
</html>
