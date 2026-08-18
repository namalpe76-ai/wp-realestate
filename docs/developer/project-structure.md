# Project Structure — 11AA Real Estate WordPress Project

Developer documentation explaining how the project is organized.

---

## Directory Structure

```
D:\11AA WP RealEstate\
├── child-theme/
│   └── realestate-child/              # Astra child theme
│       ├── style.css                  # Theme header + CSS custom properties + all styles
│       ├── functions.php              # Theme setup, enqueues, menus, widgets
│       ├── front-page.php             # Homepage template (hero, search, properties, stats)
│       ├── header.php                 # Custom sticky header
│       ├── footer.php                 # Custom 4-column footer
│       ├── page.php                   # Default page template
│       ├── single.php                 # Single post template
│       ├── index.php                  # Fallback template
│       ├── archive.php                # Archive template
│       ├── search.php                 # Search results template
│       ├── 404.php                    # 404 error page
│       └── template-parts/
│           └── content-property-card.php  # Reusable property card component
│
├── plugins/
│   ├── realestate-core/               # Core property functionality
│   │   ├── realestate-core.php        # Plugin bootstrap (singleton class)
│   │   ├── includes/
│   │   │   ├── class-property-post-type.php   # Property CPT + taxonomies
│   │   │   ├── class-property-meta-boxes.php  # Admin meta boxes
│   │   │   ├── class-property-search.php      # Search form + results + REST API
│   │   │   ├── class-property-widgets.php     # Sidebar widgets
│   │   │   └── class-property-email.php       # HTML email templates
│   │   ├── templates/
│   │   │   ├── single-property.php           # Single property template
│   │   │   ├── search-results.php            # Search results template
│   │   │   ├── archive-properties.php        # Property archive template
│   │   │   └── property-card.php             # Property card partial
│   │   └── assets/
│   │       ├── css/property.css              # Property page styles
│   │       └── js/property.js                # Property page scripts
│   │
│   ├── realestate-enquiry/            # Enquiry form and management
│   │   ├── realestate-enquiry.php     # Plugin bootstrap + DB table creation
│   │   ├── includes/
│   │   │   ├── class-enquiry-form.php     # Shortcode + AJAX form handler
│   │   │   ├── class-enquiry-storage.php  # Database operations
│   │   │   └── class-enquiry-email.php    # Email notifications
│   │   ├── admin/
│   │   │   ├── class-enquiry-admin.php    # WP admin list table
│   │   │   └── views/
│   │   │       ├── enquiry-list.php       # Admin enquiry list view
│   │   │       └── enquiry-detail.php     # Admin enquiry detail view
│   │   └── assets/
│   │       ├── css/enquiry-form.css       # Frontend form styles
│   │       ├── css/enquiry-admin.css      # Admin styles
│   │       ├── js/enquiry-form.js         # Frontend AJAX form
│   │       └── js/enquiry-admin.js        # Admin scripts
│   │
│   ├── realestate-submit-property/    # Public property submission
│   │   ├── realestate-submit-property.php   # Plugin bootstrap
│   │   ├── includes/
│   │   │   ├── class-submit-form.php            # Shortcode form renderer
│   │   │   ├── class-submission-handler.php     # AJAX handler + file uploads
│   │   │   └── class-submission-email.php       # Email notifications
│   │   ├── admin/
│   │   │   ├── class-submission-admin.php       # Admin management
│   │   │   └── views/
│   │   │       ├── submission-list.php          # Admin submission list
│   │   │       └── submission-detail.php        # Admin submission detail
│   │   └── assets/
│   │       ├── css/submit-form.css              # Frontend form styles
│   │       ├── css/submission-admin.css         # Admin styles
│   │       └── js/submit-form.js                # Frontend AJAX + file upload
│   │
│   ├── realestate-datetime/           # Live date/time display
│   │   ├── realestate-datetime.php    # Plugin bootstrap
│   │   ├── includes/
│   │   │   └── class-datetime-display.php   # Shortcode + clock rendering
│   │   └── assets/
│   │       ├── js/datetime-clock.js         # Live clock JavaScript
│   │       └── css/datetime-widget.css      # Widget styles
│   │
│   ├── realestate-weather/            # Weather widget (OpenWeatherMap)
│   │   ├── realestate-weather.php     # Plugin bootstrap + settings init
│   │   ├── includes/
│   │   │   ├── class-weather-api.php       # API client with caching
│   │   │   └── class-weather-widget.php    # Shortcode + settings page
│   │   └── assets/
│   │       ├── js/weather-widget.js        # Frontend refresh logic
│   │       └── css/weather-widget.css      # Widget styles
│   │
│   └── realestate-analytics/          # Visitor tracking + stats
│       ├── realestate-analytics.php   # Plugin bootstrap + shortcode
│       ├── includes/
│       │   ├── class-visitor-tracker.php    # IP hashing + visit logging
│       │   └── class-visitor-stats.php      # Statistics aggregation + REST API
│       ├── admin/
│       │   └── class-analytics-admin.php    # Admin dashboard widget
│       └── assets/
│           ├── js/analytics-counter.js      # Counter animation script
│           └── css/analytics-admin.css      # Admin styles
│
├── docker/
│   ├── docker-compose.yml            # WordPress + MySQL + phpMyAdmin
│   ├── .env                          # Database credentials
│   ├── php-custom.ini                # PHP configuration overrides
│   └── SETUP.txt                     # Quick setup instructions
│
├── docs/                             # Project documentation
│   ├── setup/
│   │   ├── getting-started.md
│   │   └── configuration.md
│   ├── migration/
│   │   ├── migration-guide.md
│   │   └── backup-procedure.md
│   ├── testing/
│   │   └── testing-checklist.md
│   └── developer/
│       ├── project-structure.md      # (this file)
│       └── shortcode-reference.md
│
└── elementor-templates/              # Elementor page templates
```

---

## How the Child Theme Works

### Parent Theme: Astra

The child theme inherits from the Astra WordPress theme. The `style.css` header declares `Template: astra` to establish the parent relationship.

### Child Theme Responsibilities

The child theme handles only **presentation and layout**:

- **`functions.php`**: Registers theme support, menus, widget areas, and enqueues CSS/JS assets. Contains no business logic.
- **`style.css`**: Contains all CSS including design tokens (CSS custom properties), component styles, and responsive breakpoints.
- **`front-page.php`**: Renders the homepage with all sections (hero, search, datetime/weather, stats, properties, services, testimonials, CTA).
- **`header.php`**: Custom sticky header with logo, navigation, and mobile hamburger menu.
- **`footer.php`**: Custom 4-column footer with newsletter form and social links.

### Enqueue Chain

```
Astra parent style.css
  └── realestate-child style.css
       └── custom.css (optional additions)
```

JavaScript files loaded conditionally (only if file exists):
- `datetime.js` — Live clock
- `weather.js` — Weather widget
- `analytics.js` — Visitor counter
- `header.js` — Sticky header + mobile menu

---

## How Each Plugin Is Organized

### Plugin Architecture Pattern

Each plugin follows a consistent structure:

```
plugin-name/
├── plugin-name.php          # Bootstrap: constants, requires, hooks
├── includes/
│   └── class-*.php          # Core business logic classes
├── admin/
│   ├── class-*-admin.php    # Admin-specific functionality
│   └── views/               # Admin template files
└── assets/
    ├── css/                 # Stylesheets
    └── js/                  # JavaScript files
```

### Plugin Loading Order

1. **realestate-core** — Registers Property CPT, taxonomies, meta boxes, search, widgets, email
2. **realestate-enquiry** — Creates enquiry DB table, registers enquiry manager role
3. **realestate-submit-property** — Registers submission CPT, file upload handling
4. **realestate-datetime** — Registers datetime shortcode
5. **realestate-weather** — Registers weather shortcode, settings page, API integration
6. **realestate-analytics** — Creates visitors DB table, registers stats REST endpoint

---

## Custom Post Types and Taxonomies

### Post Types

| Post Type | Slug | Plugin | Public | Archive | Supports |
|-----------|------|--------|--------|---------|----------|
| Property | `property` | realestate-core | Yes | Yes | title, editor, thumbnail, excerpt, custom-fields |
| Property Submission | `property_submission` | realestate-submit-property | No | No | title |

### Taxonomies

| Taxonomy | Slug | Post Type | Hierarchical | Terms (Auto-Created) |
|----------|------|-----------|--------------|---------------------|
| Property Type | `property_type` | property | Yes | House, Apartment, Land, Commercial Property, Office, Shop, Warehouse, Villa |
| Property Status | `property_status` | property | Yes | For Sale, For Rent |
| Property Location | `property_location` | property | Yes | Colombo 01-15, Kandy, Galle, Negombo |

---

## Meta Boxes and Custom Fields

### Property Details Meta Box

| Field Key | Type | Description |
|-----------|------|-------------|
| `_property_id_number` | number | Auto-generated sequential ID (RE-001, RE-002, ...) |
| `_property_display_id` | text | Display ID (RE-001) |
| `_property_price` | number | Property price |
| `_property_currency` | select | Currency: LKR, USD, GBP, EUR, AUD |
| `_property_bedrooms` | number | Bedroom count |
| `_property_bathrooms` | number | Bathroom count |
| `_property_parking` | number | Parking spaces |
| `_property_land_size` | number | Land size value |
| `_property_land_size_unit` | select | Perches, Acres, sqft, sqm |
| `_property_building_size` | number | Building size value |
| `_property_building_size_unit` | select | sqft, sqm |
| `_property_address` | text | Full property address |

### Location & Map Meta Box

| Field Key | Type | Description |
|-----------|------|-------------|
| `_property_google_map_url` | url | Google Maps embed URL |

### Property Features Meta Box

| Field Key | Type | Values |
|-----------|------|--------|
| `_property_features` | array | swimming_pool, garden, garage, air_conditioning, security_system, balcony, servant_quarters, gym, laundry, store_room |

### Property Gallery Meta Box

| Field Key | Type | Description |
|-----------|------|-------------|
| `_property_gallery` | array | Comma-separated attachment IDs |

### Submission Meta (property_submission CPT)

All prefixed with `_resp_`:

`owner_name`, `owner_telephone`, `owner_email`, `property_type`, `property_location`, `property_address`, `expected_price`, `land_size`, `land_size_unit`, `building_size`, `building_size_unit`, `bedrooms`, `bathrooms`, `parking_spaces`, `property_description`, `property_images`, `submission_status`, `submission_date`

---

## Shortcode Reference

See [shortcode-reference.md](shortcode-reference.md) for complete documentation.

| Shortcode | Plugin | Purpose |
|-----------|--------|---------|
| `[property_search]` | realestate-core | Property search form |
| `[property_results]` | realestate-core | Search results grid |
| `[property_enquiry_form]` | realestate-enquiry | Enquiry form |
| `[submit_property_form]` | realestate-submit-property | Property submission form |
| `[weather_widget]` | realestate-weather | Weather display |
| `[datetime_display]` | realestate-datetime | Live date/time |
| `[realestate_stats]` | realestate-analytics | Statistics counter |

---

## Hooks and Filters Used

### Actions

| Hook | Plugin | Purpose |
|------|--------|---------|
| `init` | realestate-core | Register CPT, taxonomies |
| `init` | realestate-submit-property | Register submission CPT |
| `init` | realestate-datetime | Initialize datetime display |
| `init` | realestate-weather | Register shortcode routes |
| `init` | realestate-analytics | Track visitor, register REST routes |
| `add_meta_boxes` | realestate-core | Add property meta boxes |
| `save_post_property` | realestate-core | Save property meta data |
| `wp_enqueue_scripts` | child-theme | Enqueue theme assets |
| `wp_enqueue_scripts` | realestate-core | Enqueue property assets |
| `wp_enqueue_scripts` | realestate-enquiry | Enqueue enquiry form assets |
| `wp_enqueue_scripts` | realestate-submit-property | Enqueue submission form assets |
| `wp_enqueue_scripts` | realestate-weather | Enqueue weather widget assets |
| `wp_enqueue_scripts` | realestate-analytics | Enqueue analytics counter JS |
| `admin_menu` | realestate-weather | Add Weather Settings page |
| `admin_init` | realestate-weather | Register settings |
| `widgets_init` | child-theme | Register footer widget areas |
| `widgets_init` | realestate-core | Register sidebar widgets |
| `plugins_loaded` | realestate-core | Initialize singleton instance |
| `after_setup_theme` | child-theme | Theme support + menu registration |
| `wp_ajax_ree_submit_enquiry` | realestate-enquiry | AJAX enquiry submission |
| `wp_ajax_nopriv_ree_submit_enquiry` | realestate-enquiry | AJAX enquiry (non-logged-in) |
| `wp_ajax_resp_submit_property` | realestate-submit-property | AJAX property submission |
| `wp_ajax_nopriv_resp_submit_property` | realestate-submit-property | AJAX submission (non-logged-in) |
| `wp_ajax_realestate_weather_refresh` | realestate-weather | AJAX weather refresh |

### Custom Actions (Fired by Plugins)

| Action | Trigger |
|--------|---------|
| `realestate_property_published` | Property published to frontend |
| `realestate_property_submitted` | Property submitted via public form |
| `realestate_enquiry_received` | Enquiry form submitted |

### Filters

| Filter | Plugin | Purpose |
|--------|--------|---------|
| `realestate_enqueue_version` | child-theme | Version string for cache busting |
| `widget_title` | WP Core | Modify widget titles |
| `body_class` | child-theme | Add `re-front-page` class |
| `excerpt_length` | child-theme | Set to 25 words |
| `excerpt_more` | child-theme | Set to ellipsis |

---

## Database Tables Created

### `{prefix}_realestate_enquiries`

Created by: `realestate-enquiry` plugin on activation.

```sql
CREATE TABLE wp_realestate_enquiries (
    id BIGINT(20) NOT NULL AUTO_INCREMENT,
    full_name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL,
    telephone VARCHAR(30) NOT NULL,
    contact_method VARCHAR(20) NOT NULL DEFAULT 'phone',
    property_id VARCHAR(50) DEFAULT '',
    property_name VARCHAR(200) DEFAULT '',
    enquiry_type VARCHAR(50) NOT NULL,
    viewing_date DATE DEFAULT NULL,
    message TEXT NOT NULL,
    status VARCHAR(30) NOT NULL DEFAULT 'new',
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    KEY idx_email (email),
    KEY idx_status (status),
    KEY idx_created_at (created_at),
    KEY idx_property_id (property_id)
);
```

### `{prefix}_realestate_visitors`

Created by: `realestate-analytics` plugin on activation.

```sql
CREATE TABLE wp_realestate_visitors (
    id BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
    ip_hash VARCHAR(64) NOT NULL,
    user_agent TEXT,
    page_url VARCHAR(500) NOT NULL,
    page_title VARCHAR(255) DEFAULT '',
    referrer VARCHAR(500) DEFAULT '',
    is_unique TINYINT(1) NOT NULL DEFAULT 1,
    session_id VARCHAR(64) NOT NULL,
    country VARCHAR(100) DEFAULT '',
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    KEY idx_ip_hash (ip_hash),
    KEY idx_session_id (session_id),
    KEY idx_created_at (created_at),
    KEY idx_page_url (page_url(191))
);
```

---

## REST API Endpoints

### Property Search

```
GET /wp-json/realestate-core/v1/search
```

**Parameters:**

| Param | Type | Description |
|-------|------|-------------|
| `type` | string | Property type slug |
| `status` | string | Property status slug |
| `location` | string | Location slug |
| `min_price` | integer | Minimum price |
| `max_price` | integer | Maximum price |
| `bedrooms` | integer | Minimum bedrooms |
| `bathrooms` | integer | Minimum bathrooms |
| `min_size` | integer | Minimum building size (sqft) |
| `page` | integer | Page number |
| `sort` | string | newest, oldest, price_asc, price_desc |

**Response:**

```json
{
    "properties": [...],
    "total": 25,
    "total_pages": 3,
    "page": 1
}
```

### Analytics Stats

```
GET /wp-json/realestate-analytics/v1/stats
```

**Response:**

```json
{
    "total_visitors": 1500,
    "total_page_views": 8500,
    "today_visitors": 45,
    "month_visitors": 1200,
    "properties_listed": 50,
    "properties_sold": 12,
    "happy_customers": 350,
    "last_7_days": [...]
}
```

---

## File Naming Conventions

| Pattern | Example | Purpose |
|---------|---------|---------|
| `class-{name}.php` | `class-property-search.php` | PHP class files |
| `{plugin-name}.php` | `realestate-core.php` | Plugin main bootstrap file |
| `{feature}-{type}.js` | `enquiry-form.js`, `weather-widget.js` | JavaScript files |
| `{feature}-{type}.css` | `submit-form.css`, `analytics-admin.css` | CSS files |
| `{view-name}.php` | `enquiry-list.php`, `submission-detail.php` | Admin view templates |
| `content-{name}.php` | `content-property-card.php` | Template parts |
| `{name}-{taxonomy}.php` | `archive-properties.php` | Archive templates |

---

## Code Standards Followed

- **WordPress Coding Standards**: Tabs for indentation, snake_case for functions, camelCase not used
- **PHP 7.4+**: Type hints where appropriate, null coalescing operator used
- **Security**: All output escaped (`esc_html`, `esc_attr`, `esc_url`), all input sanitized, nonce verification on all AJAX handlers, capability checks on admin actions
- **Internationalization**: All strings wrapped in `__()` or `esc_html_e()` with plugin text domain
- **Documentation**: PHPDoc blocks on all classes and public methods
- **No Third-Party Dependencies**: Plugins use only WordPress core functions and jQuery (no React, no external libraries)
- **BEM-like CSS**: CSS classes follow `re-` prefix convention (e.g., `re-property-card`, `re-search-form__field`)
