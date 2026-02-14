# Google Search Console Submission Guide

## Sitemap Created ✅

Your XML sitemap has been created and deployed to:
**https://mikeymobile.com/sitemap.xml**

## What's Included

The sitemap contains **22 pages** organized by priority:

### Priority 1.0 (Highest)
- Homepage (/)

### Priority 0.9 (Very High)
- Locations overview page
- Fresno city page
- Clovis city page
- Madera Ranchos city page

### Priority 0.8 (High)
- About page

### Priority 0.7 (Medium)
- All 16 neighborhood pages:
  - 8 Fresno neighborhoods
  - 6 Clovis neighborhoods
  - 2 Madera Ranchos neighborhoods

## How to Submit to Google Search Console

### Step 1: Access Google Search Console
1. Go to https://search.google.com/search-console
2. Log in with your Google account
3. If you haven't added your property yet, click **"Add Property"**
4. Select **"URL prefix"** and enter: `https://mikeymobile.com`
5. Verify ownership using one of these methods:
   - HTML file upload
   - HTML meta tag
   - Google Analytics
   - Google Tag Manager
   - Domain name provider

### Step 2: Submit Your Sitemap
1. Once verified, click on **"Sitemaps"** in the left sidebar
2. Under "Add a new sitemap", enter: `sitemap.xml`
3. Click **"Submit"**

### Step 3: Wait for Processing
- Google will crawl your sitemap (usually within 24-48 hours)
- You'll see the status change from "Pending" to "Success"
- Check back in a few days to see indexed pages

## Robots.txt File

Your robots.txt file has also been updated:
**https://mikeymobile.com/robots.txt**

This file:
- Allows all search engines to crawl your site
- Blocks the admin panel from being indexed
- References your sitemap location

## Important: Purge Cloudflare Cache

After uploading the sitemap, you need to **purge your Cloudflare cache**:

1. Log into Cloudflare dashboard
2. Select mikeymobile.com
3. Go to **Caching** → **Configuration**
4. Click **"Purge Everything"**
5. Wait 30 seconds

This ensures Google sees the latest version of your sitemap.

## Verification

You can verify your sitemap is working by:
1. Visiting https://mikeymobile.com/sitemap.xml (after purging cache)
2. Using Google's Sitemap Validator
3. Checking the "Coverage" report in Search Console after submission

## SEO Best Practices

### Update Frequency
- **Homepage & Locations**: Weekly (you update these frequently)
- **City Pages**: Weekly (main landing pages)
- **Neighborhood Pages**: Monthly (less frequent updates)
- **About Page**: Monthly (rarely changes)

### Priority Levels
- **1.0**: Homepage only
- **0.9**: Main category/city pages
- **0.8**: Important static pages
- **0.7**: Individual location pages

## Next Steps After Submission

1. **Monitor Coverage Report**: Check which pages are indexed
2. **Fix Any Errors**: Address crawl errors or indexing issues
3. **Submit URL Inspection**: Request indexing for important pages
4. **Check Performance**: Monitor clicks, impressions, and rankings
5. **Update Sitemap**: When you add new pages, update the sitemap and resubmit

## Adding New Pages to Sitemap

When you create new pages in the future:

1. Edit `sitemap.xml` on your server
2. Add the new URL with appropriate priority and change frequency
3. Update the `<lastmod>` date to today
4. Resubmit the sitemap in Google Search Console
5. Purge Cloudflare cache

## Sitemap Statistics

- **Total URLs**: 22
- **Main Pages**: 3
- **City Pages**: 3
- **Neighborhood Pages**: 16
- **Last Modified**: February 12, 2026
- **Format**: XML with image annotations

---

**Your sitemap is ready for Google!** 🚀

After purging Cloudflare cache, submit it to Google Search Console and your pages will start appearing in search results.
