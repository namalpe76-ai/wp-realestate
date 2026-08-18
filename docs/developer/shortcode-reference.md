# Shortcode Reference — 11AA Real Estate WordPress Project

Complete reference for all custom shortcodes provided by the project plugins.

---

## 1. `[property_search]`

**Plugin:** realestate-core
**File:** `plugins/realestate-core/includes/class-property-search.php:17`

Renders the property search form with filter dropdowns.

### Parameters

| Parameter | Type | Default | Description |
|-----------|------|---------|-------------|
| `show_title` | bool | `false` | Show "Search Properties" heading |
| `layout` | string | `horizontal` | Layout: `horizontal` or `vertical` |

### Usage

```html
[property_search]
[property_search show_title="true"]
[property_search layout="vertical"]
```

### Output

Renders a form with:
- Property Type dropdown (from `property_type` taxonomy)
- Sale/Rent status dropdown (from `property_status` taxonomy)
- Location dropdown (from `property_location` taxonomy)
- Price Range (min/max number inputs)
- Bedrooms dropdown (1-10+)
- Bathrooms dropdown (1-10+)
- Min Size (sqft) input
- Search and Reset buttons

Form submits via GET to the property archive URL.

### Example (Elementor)

1. Add a **Shortcode** widget
2. Paste: `[property_search show_title="true"]`

---

## 2. `[property_results]`

**Plugin:** realestate-core
**File:** `plugins/realestate-core/includes/class-property-search.php:154`

Renders property search results based on URL query parameters.

### Parameters

None.

### Usage

```html
[property_results]
```

### Output

Renders:
- Results count ("X properties found")
- Sort dropdown (Newest, Oldest, Price Low-High, Price High-Low)
- Property cards in a grid
- Pagination links

Reads parameters from the URL: `type`, `status`, `location`, `min_price`, `max_price`, `bedrooms`, `bathrooms`, `min_size`, `sort`, `page`

---

## 3. `[property_enquiry_form]`

**Plugin:** realestate-enquiry
**File:** `plugins/realestate-enquiry/includes/class-enquiry-form.php:47`

Renders the customer enquiry form.

### Parameters

| Parameter | Type | Default | Description |
|-----------|------|---------|-------------|
| `property_id` | string | `""` | Pre-fill property ID |
| `property_name` | string | `""` | Pre-fill property name |

### Usage

```html
[property_enquiry_form]
[property_enquiry_form property_id="RE-001" property_name="Modern Villa"]
```

Also accepts values via URL query string:

```
/property/some-property/?property_id=RE-001&property_name=Modern+Villa
```

### Output

Renders a form with:
- Full Name (required, max 100 chars)
- Email Address (required, email validation)
- Telephone Number (required, 7-30 chars)
- Preferred Contact Method (radio: Phone, Email, WhatsApp, Any)
- Property ID (text input, pre-filled)
- Property Name (text input, pre-filled)
- Enquiry Type (required, dropdown with 6 options)
- Preferred Viewing Date (date picker)
- Message (required, max 2000 chars)
- Honeypot anti-spam field (hidden)
- Submit button with loading state

### AJAX Behavior

Submits via AJAX to `wp_ajax_ree_submit_enquiry`. On success:
- Shows success message
- Clears form
- Sends admin notification email
- Sends customer acknowledgement email

### Validation Rules

| Field | Rule |
|-------|------|
| full_name | Required, max 100 characters |
| email | Required, valid email format |
| telephone | Required, 7-30 chars, matches `/^[+]?[\d\s\-()]{7,30}$/` |
| enquiry_type | Required, must be one of: property_information, schedule_viewing, purchase, rental, sell_my_property, general_enquiry |
| message | Required, max 2000 characters |

---

## 4. `[submit_property_form]`

**Plugin:** realestate-submit-property
**File:** `plugins/realestate-submit-property/includes/class-submit-form.php:12`

Renders the public property submission form.

### Parameters

None.

### Usage

```html
[submit_property_form]
```

### Output

Renders a multi-section form:

**Owner Information:**
- Owner Name (required, max 100 chars)
- Telephone (required, max 20 chars)
- Email (required, valid email)

**Property Details:**
- Property Type (required, dropdown: House, Apartment, Land, Commercial, Office, Shop, Warehouse, Villa)
- Location (required, text, max 200 chars)
- Full Address (required, textarea, max 500 chars)
- Expected Price (required, LKR prefix, number)
- Land Size (optional, number + unit: Perches/Acres/sqft/sqm)
- Building Size (optional, number + unit: sqft/sqm)
- Bedrooms (number, 0-20)
- Bathrooms (number, 0-10)
- Parking Spaces (number, 0-10)
- Description (required, min 50 chars, max 5000 chars)

**Property Images:**
- Drag-and-drop upload zone
- Accepted: JPG, JPEG, PNG, WebP
- Max 10 files, 5MB each, 50MB total
- Preview grid with remove buttons
- Progress bar

**Consent:**
- GDPR consent checkbox (required)
- Privacy Policy link

### AJAX Behavior

Submits via AJAX to `wp_ajax_resp_submit_property`. On success:
- Shows success message
- Saves as `property_submission` CPT with `pending` status
- Uploads images to WordPress media library
- Sends admin notification email
- Sends customer acknowledgement email

### File Upload Limits

| Limit | Value |
|-------|-------|
| Max files | 10 |
| Max size per file | 5MB |
| Max total size | 50MB |
| Allowed types | jpg, jpeg, png, webp |

---

## 5. `[weather_widget]`

**Plugin:** realestate-weather
**File:** `plugins/realestate-weather/includes/class-weather-widget.php:22`

Renders the weather widget with current conditions.

### Parameters

| Parameter | Type | Default | Description |
|-----------|------|---------|-------------|
| `location` | string | `""` | Override location (e.g., `Kandy,LK`) |
| `unit` | string | `""` | Override unit (`metric` or `imperial`) |

### Usage

```html
[weather_widget]
[weather_widget location="Kandy,LK"]
[weather_widget unit="imperial"]
```

### Output

Renders a weather card showing:
- Location name and country
- Weather icon (from OpenWeatherMap)
- Current temperature with unit (C or F)
- Weather condition description
- Humidity percentage
- Wind speed with unit
- "Feels like" temperature
- Last updated timestamp
- Refresh button

### Caching

Weather data is cached using WordPress transients:
- Primary cache key: `realestate_weather_{md5(location+unit)}`
- Cache duration: 30 minutes (configurable in settings)
- Fallback cache: 90 minutes

### Error States

- **Missing API key**: Shows message to configure Weather Settings
- **Invalid API key**: Shows authentication error
- **Location not found**: Shows location error
- **Network error**: Shows temporary unavailability message
- **Incomplete data**: Shows data error

All error states display a Retry button.

### Settings Page

Available at **Settings > Weather Settings**:

| Setting | Default | Range |
|---------|---------|-------|
| API Key | (empty) | Required |
| Location | `Colombo,LK` | Any valid city,country |
| Unit | `metric` | `metric` or `imperial` |
| Cache Duration | `1800` | 300-86400 seconds |

---

## 6. `[datetime_display]`

**Plugin:** realestate-datetime
**File:** `plugins/realestate-datetime/includes/class-datetime-display.php`

Renders a live date and time display.

### Parameters

None.

### Usage

```html
[datetime_display]
```

### Output

Renders a container element with:
- Current date
- Live updating clock (updates every second)
- Timezone matches WordPress settings

The JavaScript component (`datetime-clock.js`) handles the live updating.

---

## 7. `[realestate_stats]`

**Plugin:** realestate-analytics
**File:** `plugins/realestate-analytics.php:58`

Renders animated statistics counters.

### Parameters

| Parameter | Type | Default | Description |
|-----------|------|---------|-------------|
| `show` | string | `all` | What to show: `all`, `visitors`, `pageviews`, `properties`, `sold`, `customers` |

### Usage

```html
[realestate_stats]
[realestate_stats show="all"]
[realestate_stats show="visitors"]
[realestate_stats show="properties,sold"]
```

### Output

Renders animated counters with labels:
- **Total Visitors** — Unique visitors (hashed IP)
- **Page Views** — Total page views
- **Properties Listed** — Published posts across property/post types
- **Properties Sold** — Properties with "sold" status
- **Happy Customers** — Configurable value (set in admin)

The JavaScript component (`analytics-counter.js`) animates numbers from 0 to target.

### Data Source

Stats are fetched from the REST API endpoint:

```
GET /wp-json/realestate-analytics/v1/stats
```

Results are cached for 5 minutes using WordPress transients.

---

## Elementor Usage Guide

All shortcodes work in Elementor using the **Shortcode** widget:

1. Open the page in Elementor
2. Search for **Shortcode** in the widget panel
3. Drag the Shortcode widget onto the page
4. Paste the shortcode into the widget

### Recommended Elementor Page Layout

**Homepage:**
```
Section 1: Hero (HTML widget with hero markup)
Section 2: [property_search]
Section 3: [datetime_display] + [weather_widget] (side by side)
Section 4: [realestate_stats show="all"]
Section 5: Property Grid (via front-page.php)
```

**Properties Page:**
```
Section 1: [property_search show_title="true" layout="horizontal"]
Section 2: [property_results]
```

**Property Detail Page:**
```
Section 1: Property content (from single-property.php)
Section 2: [property_enquiry_form]
```

**Submit Property Page:**
```
Section 1: [submit_property_form]
```

---

## Shortcode File Locations

| Shortcode | Defined In | Line |
|-----------|-----------|------|
| `[property_search]` | `plugins/realestate-core/includes/class-property-search.php` | 9 |
| `[property_results]` | `plugins/realestate-core/includes/class-property-search.php` | 10 |
| `[property_enquiry_form]` | `plugins/realestate-enquiry/includes/class-enquiry-form.php` | 9 |
| `[submit_property_form]` | `plugins/realestate-submit-property/includes/class-submit-form.php` | 9 |
| `[weather_widget]` | `plugins/realestate-weather/includes/class-weather-widget.php` | 19 |
| `[datetime_display]` | `plugins/realestate-datetime/includes/class-datetime-display.php` | via `init` |
| `[realestate_stats]` | `plugins/realestate-analytics/realestate-analytics.php` | 106 |
