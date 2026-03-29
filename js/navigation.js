/**
 * Navigation Loader - Mikey Mobile Oil Change
 * Dynamically loads and renders site navigation from navigation-config.json
 * Includes mobile hamburger menu with close button
 */

(function() {
    'use strict';

    // Load navigation configuration
    async function loadNavigationConfig() {
        try {
            const response = await fetch('/navigation-config.json?v=' + Date.now());
            if (!response.ok) throw new Error('Failed to load navigation config');
            return await response.json();
        } catch (error) {
            console.error('Error loading navigation:', error);
            return null;
        }
    }

    // Generate dropdown menu HTML
    function generateDropdown(dropdown) {
        if (!dropdown || dropdown.length === 0) return '';
        // Use narrow class when there's only one top-level item (e.g. Services)
        const narrowClass = dropdown.length === 1 && (!dropdown[0].subitems || dropdown[0].subitems.length === 0) ? ' dropdown-narrow' : '';
        let html = `<div class="dropdown-menu${narrowClass}">`;
        dropdown.forEach(section => {
            html += '<div class="dropdown-section">';
            html += `<a href="${section.url}" class="dropdown-city">${section.label}</a>`;
            if (section.subitems && section.subitems.length > 0) {
                section.subitems.forEach(subitem => {
                    html += `<a href="${subitem.url}" class="dropdown-neighborhood">${subitem.label}</a>`;
                });
            }
            html += '</div>';
        });
        html += '</div>';
        return html;
    }

    // Generate navigation HTML
    function generateNavigation(config) {
        let html = `
        <!-- Top Bar -->
        <div class="top-bar">
            <div class="container">
                <span>⭐⭐⭐⭐⭐ 5-STAR RATED • LICENSED &amp; INSURED • SAME-DAY SERVICE AVAILABLE</span>
            </div>
        </div>

        <!-- Navigation -->
        <nav class="navbar">
            <div class="container">
                <div class="nav-container">

                    <!-- Left Navigation (desktop only) -->
                    <div class="nav-left">`;

        config.main_nav.left.forEach(item => {
            if (item.dropdown) {
                html += `
                        <div class="nav-dropdown">
                            <a href="${item.url}" class="nav-link dropdown-trigger">${item.label}</a>
                            ${generateDropdown(item.dropdown)}
                        </div>`;
            } else {
                html += `<a href="${item.url}" class="nav-link">${item.label}</a>`;
            }
        });

        html += `
                    </div>

                    <!-- Center Logo -->
                    <div class="nav-center">
                        <a href="/">
                            <picture>
                                <source srcset="/pink-full-gear.webp" type="image/webp">
                                <img src="/pink-full-gear.png" alt="${config.logo.alt}" class="logo" width="120" height="120" loading="eager">
                            </picture>
                        </a>
                    </div>

                    <!-- Right Navigation (desktop only) -->
                    <div class="nav-right">`;

        config.main_nav.right.forEach(item => {
            if (item.dropdown) {
                html += `
                        <div class="nav-dropdown">
                            <a href="${item.url}" class="nav-link dropdown-trigger">${item.label}</a>
                            ${generateDropdown(item.dropdown)}
                        </div>`;
            } else {
                html += `<a href="${item.url}" class="nav-link">${item.label}</a>`;
            }
        });

        html += `
                    </div>

                    <!-- Hamburger Button (mobile only, right side of nav bar) -->
                    <button class="mobile-menu-toggle" id="mobile-menu-toggle" aria-label="Open navigation menu">
                        <span></span>
                        <span></span>
                        <span></span>
                    </button>

                </div>
            </div>
        </nav>

        <!-- Mobile Full-Screen Menu Overlay -->
        <div class="mobile-menu" id="mobile-menu" role="dialog" aria-modal="true" aria-label="Navigation menu">

            <!-- Close Button at top right of overlay -->
            <button class="mobile-menu-close" id="mobile-menu-close" aria-label="Close navigation menu">&#x2715;</button>

            <div class="mobile-menu-content">`;

        // All nav links in the overlay
        config.main_nav.left.forEach(item => {
            html += `<a href="${item.url}" class="mobile-nav-link">${item.label}</a>`;
        });
        config.main_nav.right.forEach(item => {
            html += `<a href="${item.url}" class="mobile-nav-link">${item.label}</a>`;
        });

        html += `
            </div>
        </div>`;

        return html;
    }

    // Close the mobile menu
    function closeMenu(toggle, menu) {
        toggle.classList.remove('active');
        menu.classList.remove('active');
        document.body.classList.remove('menu-open');
        toggle.setAttribute('aria-expanded', 'false');
    }

    // Open the mobile menu
    function openMenu(toggle, menu) {
        toggle.classList.add('active');
        menu.classList.add('active');
        document.body.classList.add('menu-open');
        toggle.setAttribute('aria-expanded', 'true');
    }

    // Initialize mobile menu functionality
    function initMobileMenu() {
        const toggle = document.getElementById('mobile-menu-toggle');
        const menu   = document.getElementById('mobile-menu');
        const closeBtn = document.getElementById('mobile-menu-close');

        if (!toggle || !menu) {
            console.warn('Mobile menu elements not found');
            return;
        }

        // Hamburger toggles the menu
        toggle.addEventListener('click', function(e) {
            e.stopPropagation();
            if (menu.classList.contains('active')) {
                closeMenu(toggle, menu);
            } else {
                openMenu(toggle, menu);
            }
        });

        // Close button inside the overlay
        if (closeBtn) {
            closeBtn.addEventListener('click', function(e) {
                e.stopPropagation();
                closeMenu(toggle, menu);
            });
        }

        // Close menu when clicking any nav link
        menu.querySelectorAll('.mobile-nav-link').forEach(link => {
            link.addEventListener('click', function() {
                closeMenu(toggle, menu);
            });
        });

        // Close menu on Escape key
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape' && menu.classList.contains('active')) {
                closeMenu(toggle, menu);
                toggle.focus();
            }
        });
    }

    // Load chat widget after page is fully loaded (desktop only) — delayed to not block rendering
    function injectChatWidget() {
        if (window.innerWidth <= 768) return;
        if (document.querySelector('script[data-widget-id="69a35468a27e8c1ba27492ee"]')) return;
        // Delay chat widget by 3s after page load to avoid blocking main thread
        setTimeout(function() {
            const script = document.createElement('script');
            script.src = 'https://beta.leadconnectorhq.com/loader.js';
            script.setAttribute('data-resources-url', 'https://beta.leadconnectorhq.com/chat-widget/loader.js');
            script.setAttribute('data-widget-id', '69a35468a27e8c1ba27492ee');
            document.body.appendChild(script);
        }, 3000);
    }

    async function init() {
        injectChatWidget();

        const config = await loadNavigationConfig();
        if (!config) {
            console.error('Failed to load navigation configuration');
            return;
        }

        const header = document.querySelector('header');
        if (!header) {
            console.error('Header element not found');
            return;
        }

        header.innerHTML = generateNavigation(config);
        initMobileMenu();
        console.log('Navigation loaded successfully');
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }
})();
