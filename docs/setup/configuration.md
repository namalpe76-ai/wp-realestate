# Configuration Guide — 11AA Real Estate WordPress Project

This document covers all configuration settings required for the project.

---

## 1. WordPress General Settings

Navigate to **Settings > General**:

| Setting | Value |
|---------|-------|
| Site Title | `11AA Real Estate` |
| Tagline | `Premium Real Estate Solutions` |
| WordPress Address (URL) | `http://localhost:8080` |
| Site Address (URL) | `http://localhost:8080` |
| Admin Email | `admin@example.com` |
| Timezone | `(GMT+05:30) Colombo` |
| Date Format | `F j, Y` (January 18, 2026) |
| Time Format | `g:i a` (11:18 pm) |
| Language | `English` |
| Site Language | `English` |

> **Production**: Change URLs to your live domain (e.g., `https://www.yoursite.com`).

---

## 2. Permalink Settings

Navigate to **Settings > Permalinks**:

- Select **Post name** structure: `/%postname%/`
- Click **Save Changes**

This produces clean URLs:

```
/properties/modern-luxury-villa/
/property-type/house/
/property-status/for-sale/
/location/colombo-07/
```

### Custom Post Type Permalinks

The property CPT uses the slug `property`:

| URL Pattern | Content |
|-------------|---------|
| `/property/` | Property archive |
| `/property/{slug}/` | Single property |
| `/property-type/{slug}/` | Property type archive |
| `/property-status/{slug}/` | Property status archive |
| `/location/{slug}/` | Location archive |

---

## 3. Reading Settings

Navigate to **Settings > Reading**:

| Setting | Value |
|---------|-------|
| Your homepage displays | A static page |
| Homepage | `Home` |
| Posts page | `Blog` |
| Blog pages show at most | `10` posts |
| Syndication feeds show the most recent | `10` items |
| Search engine visibility | Uncheck (allow indexing) |

---

## 4. Astra Theme Customizer Settings

Navigate to **Appearance > Customize**:

### 4a. Global

| Setting | Value |
|---------|-------|
| Container Width | `1280px` |
| Content Style | Full width / Contained |

### 4b. Colors

The child theme uses CSS custom properties defined in `style.css`:

| Variable | Value | Usage |
|----------|-------|-------|
| `--re-primary` | `#0A1628` | Dark navy — headings, header, footer |
| `--re-secondary` | `#C8A951` | Gold — CTAs, accents, highlights |
| `--re-background` | `#FFFFFF` | Page background |
| `--re-secondary-bg` | `#F5F6FA` | Alternating section background |
| `--re-text` | `#2D3436` | Body text |
| `--re-text-light` | `#636E72` | Muted text, labels |
| `--re-border` | `#DFE6E9` | Borders, dividers |

To change the color scheme, override these variables in the child theme's `style.css`.

### 4c. Typography

| Element | Font | Weight |
|---------|------|--------|
| Body | Inter, system stack | 400 |
| Headings | Inter, system stack | 700 |
| Buttons | Inter, system stack | 600 |

### 4d. Header

The child theme implements a **custom sticky header** (not using Astra's header builder):

- Fixed position with blur backdrop
- Logo on left, navigation on right
- CTA button in header
- Mobile hamburger menu with slide-in drawer

### 4e. Footer

The child theme implements a **custom footer**:

- 4-column layout (Company, Quick Links, Services, Contact)
- Newsletter subscription form
- Social media icons
- Copyright bar with legal links

### 4f. Blog

- Layout: Grid (2 columns)
- Featured image displayed
- Excerpt length: 25 words

---

## 5. Weather API Key Configuration

### Get an API Key

1. Create a free account at [OpenWeatherMap](https://openweathermap.org/api)
2. Navigate to **My API Keys** in your account dashboard
3. Copy the API key

### Configure in WordPress

Go to **Settings > Weather Settings**:

| Setting | Value | Notes |
|---------|-------|-------|
| API Key | *(your key)* | Required for weather data |
| Location | `Colombo,LK` | Format: `City,CountryCode` |
| Temperature Unit | `Celsius (C)` | Metric for Sri Lanka |
| Cache Duration | `1800` | 30 minutes (300-86400 seconds) |

### Test Connection

Click the **Test Connection** button on the settings page to verify:

- If successful: Shows current temperature and location
- If failed: Check API key and location format

### Caching Behavior

Weather data is cached using WordPress transients:

- Primary cache: `{location}_{unit}` for 30 minutes
- Fallback cache: Same key with `_fallback` suffix for 90 minutes
- Cache clears on manual refresh or expiration

### Adding Weather Widget to Pages

Use the shortcode in any page, post, or Elementor widget:

```
[weather_widget]
```

Override location per-instance:

```
[weather_widget location="Kandy,LK"]
```

---

## 6. Email/SMTP Configuration

### Default PHP Mail

By default, WordPress uses PHP's `mail()` function. This works in Docker but may not work on all hosting environments.

### Recommended: Configure SMTP

Install an SMTP plugin such as **WP Mail SMTP** and configure:

#### Gmail SMTP Example

| Setting | Value |
|---------|-------|
| From Email | `noreply@yoursite.com` |
| From Name | `11AA Real Estate` |
| SMTP Host | `smtp.gmail.com` |
| Encryption | TLS |
| SMTP Port | `587` |
| Authentication | Yes |
| SMTP Username | `your-email@gmail.com` |
| SMTP Password | *(App Password)* |

#### Production SMTP (Recommended)

| Provider | Host | Port | Encryption |
|----------|------|------|------------|
| SendGrid | `smtp.sendgrid.net` | 587 | TLS |
| Mailgun | `smtp.mailgun.org` | 587 | TLS |
| Amazon SES | `email-smtp.region.amazonaws.com` | 587 | TLS |

### Email Notifications Sent by the System

| Trigger | Recipient | Subject Pattern |
|---------|-----------|-----------------|
| Property published | Admin | `[Site] New Property Published: {title}` |
| Property submitted | Customer | `Thank you for your property submission on {site}` |
| Enquiry received | Admin | `[Site] New Enquiry for: {property}` |
| Enquiry received | Customer | `[Site] We received your enquiry about: {property}` |

All emails use the HTML email template defined in `class-property-email.php`.

---

## 7. SEO Plugin Setup

### Option A: Yoast SEO

1. Install and activate **Yoast SEO**
2. Go to **Yoast SEO > General > Configuration Wizard**:
   - Environment: `This is a live site`
   - Site type: `Small business`
   - Organization or person: `Organization`
   - Organization name: `11AA Real Estate`
3. Go to **Search Appearance**:
   - Enable `property` post type for SEO
   - Enable `property_type`, `property_status`, `property_location` taxonomies

### Option B: Rank Math

1. Install and activate **Rank Math**
2. Run the setup wizard:
   - Mode: `Easy`
   - Analytics: Connect Google Analytics/Search Console
   - Sitemap: Enable
   - SEO Tweaks: Enable all

### Recommended SEO Settings

| Setting | Value |
|---------|-------|
| Title Separator | `-` |
| Homepage Title | `11AA Real Estate - Premium Property Listings` |
| Homepage Description | `Discover exceptional properties...` |
| Property Title Template | `%title% - 11AA Real Estate` |
| Property Description | `Excerpt` or custom meta |

### XML Sitemaps

Ensure these post types are included in the sitemap:

- `property` (custom post type)
- `page`
- `post`

### Schema Markup

The child theme includes basic structured data for:

- Organization info (homepage)
- Property listings (individual property pages)

---

## 8. Additional Configuration

### Custom PHP Settings

The `docker/php-custom.ini` file configures PHP limits:

```ini
upload_max_filesize = 64M
post_max_size = 64M
max_execution_time = 300
memory_limit = 256M
max_input_vars = 3000
```

> These settings support property image uploads (up to 50MB total).

### Database Tables Created

| Table | Plugin | Purpose |
|-------|--------|---------|
| `{prefix}_realestate_enquiries` | realestate-enquiry | Stores enquiry submissions |
| `{prefix}_realestate_visitors` | realestate-analytics | Tracks visitor activity |

### WordPress Options Set by Plugins

| Option Key | Plugin | Default |
|------------|--------|---------|
| `realestate_weather_api_key` | weather | *(empty)* |
| `realestate_weather_location` | weather | `Colombo,LK` |
| `realestate_weather_unit` | weather | `metric` |
| `realestate_weather_cache_time` | weather | `1800` |
| `realestate_happy_customers` | analytics | `0` |
| `realestate_analytics_db_version` | analytics | `1.0` |
| `ree_db_version` | enquiry | `1.0.0` |
