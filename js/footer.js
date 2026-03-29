/**
 * Footer Loader - Mikey Mobile Oil Change
 * Injects the site footer HTML directly into the <footer> element.
 * Uses hardcoded absolute URLs so it works from any page or subdirectory.
 */
(function() {
    'use strict';

    var FOOTER_HTML = [
        '<div class="footer-content">',
        '    <div class="footer-col">',
        '        <h4>Quick Links</h4>',
        '        <ul>',
        '            <li><a href="/">Home</a></li>',
        '            <li><a href="/about.html">About</a></li>',
        '            <li><a href="/services.html">Services</a></li>',
        '            <li><a href="/locations.html">Locations</a></li>',
        '            <li><a href="/contact.html">Contact</a></li>',
        '        </ul>',
        '    </div>',
        '    <div class="footer-col">',
        '        <h4>Services</h4>',
        '        <ul>',
        '            <li><a href="/#pricing">Oil Change Service</a></li>',
        '            <li><a href="/#pricing">Diesel Service</a></li>',
        '            <li><a href="/services/boat-summer-service-fresno-ca.html">Boat Summer Prep</a></li>',
        '        </ul>',
        '    </div>',
        '    <div class="footer-col">',
        '        <h4>Service Areas</h4>',
        '        <ul>',
        '            <li><a href="/locations.html">Fresno</a></li>',
        '            <li><a href="/locations.html">Clovis</a></li>',
        '            <li><a href="/locations.html">Madera Ranchos</a></li>',
        '        </ul>',
        '    </div>',
        '    <div class="footer-col">',
        '        <h4>Contact</h4>',
        '        <p><a href="tel:5598384267">(559) 838-4267</a></p>',
        '        <p><a href="mailto:mikey@mikeymobile.com">mikey@mikeymobile.com</a></p>',
        '        <p>Mon-Fri: 8am-6pm</p>',
        '        <p>Sat: 9am-3pm</p>',
        '    </div>',
        '    <div class="footer-col">',
        '        <h4>Follow Us</h4>',
        '        <div class="social-links">',
        '            <a href="https://share.google/G0S2Z1Xx6vjRxakwF" target="_blank" rel="noopener">Google</a>',
        '            <a href="https://www.facebook.com/mikeymobileoilchange/" target="_blank" rel="noopener">Facebook</a>',
        '            <a href="https://www.instagram.com/mikeymobileoilchange/" target="_blank" rel="noopener">Instagram</a>',
        '            <a href="https://www.yelp.com/biz/mikey-mobile-oil-change-fresno" target="_blank" rel="noopener">Yelp</a>',
        '        </div>',
        '    </div>',
        '</div>',
        '<div class="footer-bottom">',
        '    &copy; 2025 Mikey Mobile Oil Change. All rights reserved. | Licensed &amp; Insured in California | <a href="/privacy-policy.html" style="color:#aaa; text-decoration:none;">Privacy Policy</a>',
        '</div>'
    ].join('\n');

    function injectFooter() {
        var footer = document.querySelector('footer');
        if (footer) {
            footer.innerHTML = FOOTER_HTML;
        } else {
            console.warn('Footer element not found on this page.');
        }
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', injectFooter);
    } else {
        injectFooter();
    }
})();
