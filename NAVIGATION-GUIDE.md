# Navigation Management Guide

## Overview

Your website now has a professional site-wide navigation menu with the following features:

### Desktop Navigation
- **Centered Logo**: Mikey Mobile logo positioned in the center of the navigation bar
- **Left Side Links**: HOME, ABOUT, LOCATIONS (with dropdown)
- **Right Side Links**: SERVICES, PRICING, CONTACT
- **Hover Dropdown**: LOCATIONS menu expands on hover showing all cities and neighborhoods

### Mobile Navigation
- **Hamburger Menu**: Three-line menu icon positioned below the centered logo
- **Full-Screen Menu**: Taps to expand into a full-screen mobile menu
- **Hierarchical Structure**: Cities and neighborhoods properly organized

## Navigation Structure

### Current Menu Items

**Left Side:**
1. HOME → index.html
2. ABOUT → about.html
3. LOCATIONS → locations.html (with dropdown)
   - **Fresno** → fresno/index.html
     - Fig Garden → fresno/fig-garden/index.html
     - Sunnyside → fresno/sunnyside/index.html
     - Fig Garden Loop → fresno/fig-garden-loop/index.html
     - Woodward Park → fresno/woodward-park/index.html
     - Riverpark → fresno/riverpark/index.html
     - Pinedale → fresno/pinedale/index.html
     - Sierra Sky Park → fresno/sierra-sky-park/index.html
     - Fort Washington → fresno/fort-washington/index.html
   - **Clovis** → clovis/index.html
     - Clovis North → clovis/clovis-north/index.html
     - Cindy Lane → clovis/cindy-lane/index.html
     - Dry Creek → clovis/dry-creek/index.html
     - Clovis High → clovis/clovis-high/index.html
     - Quail Lakes → clovis/quail-lakes/index.html
     - Harlan Ranch → clovis/harlan-ranch/index.html
   - **Madera Ranchos** → madera-ranchos/index.html
     - Rolling Hills → madera-ranchos/rolling-hills/index.html
     - Riverstone → madera-ranchos/riverstone/index.html

**Right Side:**
1. SERVICES → index.html#services
2. PRICING → index.html#pricing
3. CONTACT → index.html#contact

## How to Update Navigation via Admin Panel

### Step 1: Access the Admin Panel

1. Go to: **https://mikeymobile.com/admin/**
2. Login with your credentials
3. Click on **"Navigation Editor"**

### Step 2: Understanding the JSON Configuration

The navigation is controlled by a JSON configuration file located at `navigation-config.json`. The admin panel provides a visual editor for this file.

### Step 3: Adding a New Page (Example: Pricing Page)

When you create a new pricing page at `pricing.html`, you'll want to update the PRICING link:

1. In the Navigation Editor, find the "right" section
2. Locate the PRICING entry:
```json
{
  "label": "PRICING",
  "url": "index.html#pricing",
  "dropdown": null
}
```

3. Change the URL to your new page:
```json
{
  "label": "PRICING",
  "url": "pricing.html",
  "dropdown": null
}
```

4. Click **"Save Navigation"**
5. **Important**: Purge your Cloudflare cache to see changes live

### Step 4: Adding a New Location

To add a new neighborhood under Fresno (example: "Tower District"):

1. Find the Fresno section in the dropdown array
2. Add a new entry to the "subitems" array:
```json
{
  "label": "Fresno",
  "url": "fresno/index.html",
  "subitems": [
    {"label": "Fig Garden", "url": "fresno/fig-garden/index.html"},
    {"label": "Tower District", "url": "fresno/tower-district/index.html"},
    ...other neighborhoods...
  ]
}
```

3. Save and purge Cloudflare cache

### Step 5: Adding a Completely New City

To add a new city (example: "Madera"):

1. In the LOCATIONS dropdown array, add a new city object:
```json
{
  "label": "Madera",
  "url": "madera/index.html",
  "subitems": [
    {"label": "Downtown Madera", "url": "madera/downtown/index.html"},
    {"label": "Madera Acres", "url": "madera/acres/index.html"}
  ]
}
```

2. Save and purge Cloudflare cache

## Important Notes

### Cloudflare Cache
After making any changes, you **must** purge your Cloudflare cache:
1. Log into Cloudflare dashboard
2. Select mikeymobile.com
3. Go to Caching → Configuration
4. Click "Purge Everything"
5. Wait 30 seconds, then hard refresh your browser (Ctrl+Shift+R)

### File Permissions
If you encounter permission errors when saving:
- The admin panel saves to `navigation-config.json`
- Contact your hosting provider to ensure PHP has write permissions
- Alternative: Update the JSON file via SFTP and upload manually

### Testing Changes
Always test your navigation changes on:
1. Desktop browsers (Chrome, Firefox, Safari)
2. Mobile devices (iPhone, Android)
3. Tablet devices (iPad)

## Technical Details

### Files Involved
- `navigation.css` - All navigation styling
- `navigation-config.json` - Navigation structure data
- `admin/navigation-editor.php` - Admin panel editor
- Each HTML page includes the navigation HTML directly

### CSS Classes
- `.navbar` - Main navigation container
- `.nav-dropdown` - Dropdown container
- `.dropdown-menu` - Dropdown content (shows on hover)
- `.dropdown-city` - City links (pink)
- `.dropdown-neighborhood` - Neighborhood links (white)
- `.hamburger-menu` - Mobile menu button
- `.mobile-menu` - Mobile menu overlay

### Responsive Breakpoints
- Desktop: 1024px and above (full navigation)
- Tablet: 768px - 1023px (compressed navigation)
- Mobile: 767px and below (hamburger menu)

## Support

If you need help with navigation updates:
1. Check this guide first
2. Test in the admin panel
3. Remember to purge Cloudflare cache
4. Contact support if issues persist

---

**Last Updated**: February 12, 2026
**Version**: 1.0
