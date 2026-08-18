# 11AA Real Estate WordPress — Setup Instructions

Complete step-by-step guide to set up the Real Estate website using Docker, WordPress, and all custom plugins.

---

## Prerequisites

- **Docker Desktop** installed and running
- **Browser** (Chrome, Firefox, or Edge)
- All project files in `D:\11AA WP RealEstate\`

---

## Step 1: Start Docker Containers

```bash
cd D:\11AA WP RealEstate\docker
docker-compose up -d
```

Wait 30-60 seconds for MySQL to initialize. Verify containers are running:

```bash
docker-compose ps
```

**URLs:**
| Service | URL |
|---|---|
| WordPress | http://localhost:8080 |
| phpMyAdmin | http://localhost:8081 |

---

## Step 2: Install WordPress

1. Open **http://localhost:8080** in your browser
2. Select language (English)
3. Fill in the setup form:
   - **Site Title:** 11AA Real Estate
   - **Username:** admin
   - **Password:** (choose a strong password)
   - **Email:** admin@11aarealestate.com
4. Click **Install WordPress**
5. Log in with the credentials you just created

---

## Step 3: Install Astra Theme

1. Go to **Appearance > Themes > Add New**
2. Search for **"Astra"**
3. Click **Install**, then **Activate**

---

## Step 4: Upload the Child Theme

1. Zip the child theme folder:
   - Navigate to `D:\11AA WP RealEstate\child-theme\realestate-child\`
   - Create a zip file named `realestate-child.zip` containing the folder contents
2. In WordPress Admin: **Appearance > Themes > Add New > Upload Theme**
3. Choose `realestate-child.zip` and click **Install Now**
4. Click **Activate** on the child theme

The child theme depends on Astra (installed in Step 3).

---

## Step 5: Upload and Activate Custom Plugins

Upload each plugin ZIP file from `D:\11AA WP RealEstate\plugins\`:

### 5a. 11AA Real Estate Core
```bash
# From the project root, zip the plugin:
# Create zip of plugins\realestate-core\ folder as realestate-core.zip
```
1. **Plugins > Add New > Upload Plugin**
2. Upload `realestate-core.zip`
3. Click **Install Now**, then **Activate**

### 5b. 11AA Real Estate DateTime
1. Upload `realestate-datetime.zip`
2. Install and activate

### 5c. 11AA Real Estate Weather
1. Upload `realestate-weather.zip`
2. Install and activate

### 5d. 11AA Real Estate Enquiries
1. Upload `realestate-enquiry.zip`
2. Install and activate

### 5e. 11AA Real Estate Submit Property
1. Upload `realestate-submit-property.zip`
2. Install and activate

### 5f. 11AA Real Estate Analytics
1. Upload `realestate-analytics.zip`
2. Install and activate

**Verify all plugins are active:** Go to **Plugins** and confirm all 6 show as "Active."

> **Tip:** To create ZIP files from the project directory, you can use:
> ```powershell
> Compress-Archive -Path "D:\11AA WP RealEstate\plugins\realestate-core\*" -DestinationPath "D:\11AA WP RealEstate\realestate-core.zip"
> ```
> Repeat for each plugin.

---

## Step 6: Install Elementor

1. Go to **Plugins > Add New**
2. Search for **"Elementor Website Builder"**
3. Click **Install Now**, then **Activate**
4. Elementor's onboarding wizard will appear — you can skip it or follow it

Elementor Free is sufficient for all templates in this project.

---

## Step 7: Run the Page Setup

### Option A: Via WordPress Admin (Recommended)

1. Copy `elementor-templates\page-setup.php` to `wp-content\mu-plugins\`:
   ```powershell
   # Inside Docker:
   docker cp "D:\11AA WP RealEstate\elementor-templates\page-setup.php" realestate_wordpress:/var/www/html/wp-content/mu-plugins/page-setup.php
   ```
2. Go to **Settings > 11AA RE Setup** in WordPress Admin
3. Click **Run Page Setup**
4. All 10 pages will be created with proper content and shortcodes

### Option B: Via WP-CLI (if available)

```bash
docker exec -it realestate_wordpress bash
wp eval 'require "/var/www/html/wp-content/mu-plugins/page-setup.php"; realestate_setup_all_pages();'
```

### Option C: Manual One-Time Run

1. Place `page-setup.php` in `wp-content/mu-plugins/`
2. Visit **http://localhost:8080/wp-admin/?realestate_run_setup=1**
3. After setup completes, delete or rename `page-setup.php` to prevent re-runs

### Pages Created

| Page | Slug | Content |
|---|---|---|
| Home | `/home/` | Front page with shortcodes |
| Properties | `/properties/` | Search + results grid |
| About Us | `/about-us/` | Company info + stats |
| Services | `/services/` | 6 service descriptions |
| Contact Us | `/contact-us/` | Enquiry form + contact info |
| Submit Your Property | `/submit-your-property/` | Submission form |
| Customer Enquiry | `/customer-enquiry/` | Enquiry form |
| Privacy Policy | `/privacy-policy/` | Legal content |
| Terms & Conditions | `/terms-conditions/` | Legal content |
| Thank You | `/thank-you/` | Confirmation page |

---

## Step 8: Configure Elementor Templates (Optional Enhancement)

The pages created in Step 7 have all shortcodes and content pre-filled. To enhance them with Elementor's visual editor:

### Import Templates

1. Go to **Templates > Saved Templates** in WordPress Admin
2. Click **Import Templates** (the icon at the top)
3. Import each JSON file from `elementor-templates\json\`:
   - `home-page.json`
   - `about-page.json`
   - `services-page.json`
   - `contact-page.json`
4. The templates appear under "My Templates"

### Apply Template to Page

1. Edit any page (e.g., Home) with **Elementor**
2. Click the folder icon (Add Template) at the bottom
3. Go to **My Templates**
4. Select the matching template (e.g., "11AA Home Page")
5. Click **Insert**

### Customize After Import

- Replace placeholder images with your actual photos
- Update colors if your brand colors differ (#c8a951 gold, #1A1A2E dark)
- Verify all shortcode widgets contain the correct shortcode
- Adjust spacing and responsive settings

> **Note:** The JSON templates are Elementor-structure guides. After import, you may need to re-assign post queries for the "Featured Properties" section and re-link buttons to the correct page URLs.

---

## Step 9: Set Up Weather API Key

The weather widget uses OpenWeatherMap's free API.

1. Go to https://openweathermap.org/api
2. Sign up for a free account
3. Navigate to **My API Keys** in your account dashboard
4. Copy your API key
5. In WordPress Admin: **Settings > Weather Settings**
6. Paste your API key in the **OpenWeatherMap API Key** field
7. Set **Location** to `Colombo,LK`
8. Set **Temperature Unit** to `Celsius (C)`
9. Click **Save Settings**
10. Click **Test Connection** to verify

> **Note:** New API keys may take 2-10 hours to activate on OpenWeatherMap.

---

## Step 10: Configure DateTime Settings (Optional)

1. Go to **Settings > DateTime Settings**
2. Set **Timezone** to `Sri Lanka (Asia/Colombo)`
3. Set **Date Format** to `Full - 18 August 2026`
4. Set **Time Format** to `12-hour (10:50 PM)`
5. Click **Save Settings**

---

## Step 11: Test Each Page

Visit each page and verify it renders correctly:

### Home Page
- [ ] Hero section displays with background image
- [ ] Property search form renders with all filter dropdowns
- [ ] Date/Time widget shows current Sri Lanka time
- [ ] Weather widget shows Colombo weather (requires API key)
- [ ] Stats counters display and animate on scroll
- [ ] Featured properties section shows cards
- [ ] Services section shows 4 service cards
- [ ] CTA banner at bottom

### Properties Page
- [ ] Search form with title renders
- [ ] Results grid shows (empty initially, populate with property posts)
- [ ] Sorting dropdown works

### About Us
- [ ] Mission and Vision sections display
- [ ] Core values icons and text render
- [ ] Why Choose Us list shows
- [ ] Stats counter section renders

### Services
- [ ] 6 service cards display in 3x2 grid
- [ ] Icons, titles, and descriptions render
- [ ] CTA section at bottom

### Contact Us
- [ ] 3 info cards (Address, Phone, Hours) render
- [ ] Google Maps embed shows (configure API key if needed)
- [ ] Enquiry form renders with all fields
- [ ] Social links display

### Submit Your Property
- [ ] Multi-step submission form renders
- [ ] Image upload zone works
- [ ] Form validation works on submit

### Customer Enquiry
- [ ] Enquiry form renders with all fields
- [ ] Enquiry types dropdown works
- [ ] Form submits via AJAX

### Legal Pages
- [ ] Privacy Policy content renders
- [ ] Terms & Conditions content renders
- [ ] Thank You page renders

---

## Step 12: Google Maps Setup (Optional)

If the Google Maps embed doesn't show:

1. Get a Google Maps API key from https://console.cloud.google.com/apis/credentials
2. Enable the **Maps JavaScript API** in your Google Cloud project
3. In WordPress Admin: go to **Elementor > Settings > Integrations**
4. Paste your Google Maps API key
5. Save changes

---

## Troubleshooting

### Shortcodes show as text
- Verify all 6 custom plugins are activated
- Go to **Plugins** and check plugin status

### Weather widget shows error
- Verify API key in **Settings > Weather Settings**
- New OpenWeatherMap keys may take 2-10 hours to activate

### Pages show 404
- Go to **Settings > Permalinks** and click **Save Changes** (flushes rewrite rules)

### Elementor templates won't import
- Ensure Elementor Free or Pro is installed and active
- JSON files are for Elementor 3.x+
- If import fails, use the page-setup.php approach instead (pages already have content)

### Child theme not showing
- Ensure Astra parent theme is installed and active
- The child theme zip must contain the `realestate-child` folder at its root

---

## Project Structure

```
D:\11AA WP RealEstate\
├── child-theme/
│   └── realestate-child/          # WordPress child theme (Astra-based)
├── docker/
│   ├── docker-compose.yml         # Docker configuration
│   ├── .env                       # Environment variables
│   └── SETUP.txt                  # Quick Docker reference
├── elementor-templates/           # THIS FOLDER
│   ├── page-setup.php             # PHP page creation utility
│   ├── elementor-widgets-map.php  # Widget/shortcode reference
│   ├── setup-instructions.md      # This file
│   └── json/
│       ├── home-page.json         # Home page Elementor template
│       ├── about-page.json        # About page Elementor template
│       ├── services-page.json     # Services page Elementor template
│       └── contact-page.json      # Contact page Elementor template
├── plugins/
│   ├── realestate-core/           # Property CPT, search, widgets
│   ├── realestate-datetime/       # Date/time display widget
│   ├── realestate-weather/        # Weather widget (OpenWeatherMap)
│   ├── realestate-enquiry/        # Customer enquiry form
│   ├── realestate-submit-property/# Public property submission
│   └── realestate-analytics/      # Visitor tracking & stats
└── docs/                          # Existing documentation
```

---

## Quick Reference — Shortcodes

| Shortcode | Plugin | Purpose |
|---|---|---|
| `[property_search]` | Core | Property search form |
| `[property_results]` | Core | Search results grid |
| `[datetime_display]` | DateTime | Live date/time |
| `[weather_widget]` | Weather | Weather card |
| `[realestate_stats show="all"]` | Analytics | Stats counter |
| `[property_enquiry_form]` | Enquiry | Contact/enquiry form |
| `[submit_property_form]` | Submit | Property submission form |

---

*Last updated: August 2026*
