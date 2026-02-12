#!/usr/bin/env python3
"""
Script to add navigation to all location pages
"""

import os
import re

# Navigation HTML for pages in subdirectories (needs ../ for paths)
nav_html_subdir = '''    <!-- Navigation -->
    <nav class="navbar">
        <div class="container">
            <div class="nav-container">
                <!-- Left Navigation -->
                <div class="nav-left">
                    <a href="../../index.html" class="nav-link">HOME</a>
                    <a href="../../about.html" class="nav-link">ABOUT</a>
                    <div class="nav-dropdown">
                        <a href="../../locations.html" class="nav-link dropdown-trigger">LOCATIONS</a>
                        <div class="dropdown-menu">
                            <div class="dropdown-section">
                                <a href="../../fresno/index.html" class="dropdown-city">Fresno</a>
                                <a href="../../fresno/fig-garden/index.html" class="dropdown-neighborhood">Fig Garden</a>
                                <a href="../../fresno/sunnyside/index.html" class="dropdown-neighborhood">Sunnyside</a>
                                <a href="../../fresno/fig-garden-loop/index.html" class="dropdown-neighborhood">Fig Garden Loop</a>
                                <a href="../../fresno/woodward-park/index.html" class="dropdown-neighborhood">Woodward Park</a>
                                <a href="../../fresno/riverpark/index.html" class="dropdown-neighborhood">Riverpark</a>
                                <a href="../../fresno/pinedale/index.html" class="dropdown-neighborhood">Pinedale</a>
                                <a href="../../fresno/sierra-sky-park/index.html" class="dropdown-neighborhood">Sierra Sky Park</a>
                                <a href="../../fresno/fort-washington/index.html" class="dropdown-neighborhood">Fort Washington</a>
                            </div>
                            <div class="dropdown-section">
                                <a href="../../clovis/index.html" class="dropdown-city">Clovis</a>
                                <a href="../../clovis/clovis-north/index.html" class="dropdown-neighborhood">Clovis North</a>
                                <a href="../../clovis/cindy-lane/index.html" class="dropdown-neighborhood">Cindy Lane</a>
                                <a href="../../clovis/dry-creek/index.html" class="dropdown-neighborhood">Dry Creek</a>
                                <a href="../../clovis/clovis-high/index.html" class="dropdown-neighborhood">Clovis High</a>
                                <a href="../../clovis/quail-lakes/index.html" class="dropdown-neighborhood">Quail Lakes</a>
                                <a href="../../clovis/harlan-ranch/index.html" class="dropdown-neighborhood">Harlan Ranch</a>
                            </div>
                            <div class="dropdown-section">
                                <a href="../../madera-ranchos/index.html" class="dropdown-city">Madera Ranchos</a>
                                <a href="../../madera-ranchos/rolling-hills/index.html" class="dropdown-neighborhood">Rolling Hills</a>
                                <a href="../../madera-ranchos/riverstone/index.html" class="dropdown-neighborhood">Riverstone</a>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Center Logo -->
                <div class="nav-center">
                    <a href="../../index.html">
                        <img src="../../logo.png" alt="Mikey Mobile Oil Change" class="logo">
                    </a>
                </div>

                <!-- Right Navigation -->
                <div class="nav-right">
                    <a href="../../index.html#services" class="nav-link">SERVICES</a>
                    <a href="../../index.html#pricing" class="nav-link">PRICING</a>
                    <a href="../../index.html#contact" class="nav-link">CONTACT</a>
                </div>

                <!-- Mobile Hamburger Menu Button -->
                <button class="hamburger-menu" onclick="document.getElementById('mobileMenu').classList.add('active')" aria-label="Open menu">
                    <span></span>
                    <span></span>
                    <span></span>
                </button>
            </div>
        </div>
    </nav>

'''

# Navigation HTML for city-level pages (needs ../ for paths)
nav_html_city = '''    <!-- Navigation -->
    <nav class="navbar">
        <div class="container">
            <div class="nav-container">
                <!-- Left Navigation -->
                <div class="nav-left">
                    <a href="../index.html" class="nav-link">HOME</a>
                    <a href="../about.html" class="nav-link">ABOUT</a>
                    <div class="nav-dropdown">
                        <a href="../locations.html" class="nav-link dropdown-trigger">LOCATIONS</a>
                        <div class="dropdown-menu">
                            <div class="dropdown-section">
                                <a href="../fresno/index.html" class="dropdown-city">Fresno</a>
                                <a href="../fresno/fig-garden/index.html" class="dropdown-neighborhood">Fig Garden</a>
                                <a href="../fresno/sunnyside/index.html" class="dropdown-neighborhood">Sunnyside</a>
                                <a href="../fresno/fig-garden-loop/index.html" class="dropdown-neighborhood">Fig Garden Loop</a>
                                <a href="../fresno/woodward-park/index.html" class="dropdown-neighborhood">Woodward Park</a>
                                <a href="../fresno/riverpark/index.html" class="dropdown-neighborhood">Riverpark</a>
                                <a href="../fresno/pinedale/index.html" class="dropdown-neighborhood">Pinedale</a>
                                <a href="../fresno/sierra-sky-park/index.html" class="dropdown-neighborhood">Sierra Sky Park</a>
                                <a href="../fresno/fort-washington/index.html" class="dropdown-neighborhood">Fort Washington</a>
                            </div>
                            <div class="dropdown-section">
                                <a href="../clovis/index.html" class="dropdown-city">Clovis</a>
                                <a href="../clovis/clovis-north/index.html" class="dropdown-neighborhood">Clovis North</a>
                                <a href="../clovis/cindy-lane/index.html" class="dropdown-neighborhood">Cindy Lane</a>
                                <a href="../clovis/dry-creek/index.html" class="dropdown-neighborhood">Dry Creek</a>
                                <a href="../clovis/clovis-high/index.html" class="dropdown-neighborhood">Clovis High</a>
                                <a href="../clovis/quail-lakes/index.html" class="dropdown-neighborhood">Quail Lakes</a>
                                <a href="../clovis/harlan-ranch/index.html" class="dropdown-neighborhood">Harlan Ranch</a>
                            </div>
                            <div class="dropdown-section">
                                <a href="../madera-ranchos/index.html" class="dropdown-city">Madera Ranchos</a>
                                <a href="../madera-ranchos/rolling-hills/index.html" class="dropdown-neighborhood">Rolling Hills</a>
                                <a href="../madera-ranchos/riverstone/index.html" class="dropdown-neighborhood">Riverstone</a>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Center Logo -->
                <div class="nav-center">
                    <a href="../index.html">
                        <img src="../logo.png" alt="Mikey Mobile Oil Change" class="logo">
                    </a>
                </div>

                <!-- Right Navigation -->
                <div class="nav-right">
                    <a href="../index.html#services" class="nav-link">SERVICES</a>
                    <a href="../index.html#pricing" class="nav-link">PRICING</a>
                    <a href="../index.html#contact" class="nav-link">CONTACT</a>
                </div>

                <!-- Mobile Hamburger Menu Button -->
                <button class="hamburger-menu" onclick="document.getElementById('mobileMenu').classList.add('active')" aria-label="Open menu">
                    <span></span>
                    <span></span>
                    <span></span>
                </button>
            </div>
        </div>
    </nav>

'''

location_files = [
    # City pages
    ('fresno/index.html', 'city'),
    ('clovis/index.html', 'city'),
    ('madera-ranchos/index.html', 'city'),
    # Fresno neighborhoods
    ('fresno/fig-garden/index.html', 'neighborhood'),
    ('fresno/sunnyside/index.html', 'neighborhood'),
    ('fresno/fig-garden-loop/index.html', 'neighborhood'),
    ('fresno/woodward-park/index.html', 'neighborhood'),
    ('fresno/riverpark/index.html', 'neighborhood'),
    ('fresno/pinedale/index.html', 'neighborhood'),
    ('fresno/sierra-sky-park/index.html', 'neighborhood'),
    ('fresno/fort-washington/index.html', 'neighborhood'),
    # Clovis neighborhoods
    ('clovis/clovis-north/index.html', 'neighborhood'),
    ('clovis/cindy-lane/index.html', 'neighborhood'),
    ('clovis/dry-creek/index.html', 'neighborhood'),
    ('clovis/clovis-high/index.html', 'neighborhood'),
    ('clovis/quail-lakes/index.html', 'neighborhood'),
    ('clovis/harlan-ranch/index.html', 'neighborhood'),
    # Madera Ranchos neighborhoods
    ('madera-ranchos/rolling-hills/index.html', 'neighborhood'),
    ('madera-ranchos/riverstone/index.html', 'neighborhood'),
]

base_dir = '/home/ubuntu/mikey-mobile-fix'

for file_path, page_type in location_files:
    full_path = os.path.join(base_dir, file_path)
    
    if not os.path.exists(full_path):
        print(f"Skipping {file_path} - file not found")
        continue
    
    with open(full_path, 'r', encoding='utf-8') as f:
        content = f.read()
    
    # Check if navigation.css is already linked
    if 'navigation.css' not in content:
        # Add navigation.css link
        if page_type == 'city':
            css_link = '    <link rel="stylesheet" href="../navigation.css">'
        else:  # neighborhood
            css_link = '    <link rel="stylesheet" href="../../navigation.css">'
        
        # Add after the last stylesheet link
        content = re.sub(
            r'(</head>)',
            f'{css_link}\n\\1',
            content
        )
    
    # Check if navigation HTML already exists
    if '<nav class="navbar">' not in content:
        # Choose the right navigation HTML based on page type
        nav_html = nav_html_city if page_type == 'city' else nav_html_subdir
        
        # Insert navigation after top bar and before mobile menu
        content = re.sub(
            r'(</div>\s*</div>\s*\n\s*<!-- Mobile Menu -->)',
            f'</div>\n    </div>\n\n{nav_html}    <!-- Mobile Menu -->',
            content
        )
    
    # Write updated content
    with open(full_path, 'w', encoding='utf-8') as f:
        f.write(content)
    
    print(f"✓ Updated {file_path}")

print("\nAll location pages updated with navigation!")
