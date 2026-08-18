# Testing Checklist — 11AA Real Estate WordPress Project

Use this checklist for comprehensive testing before launch and after each update.

---

## 1. Page Loading (All 10 Pages)

| Page | URL Slug | Status | Notes |
|------|----------|--------|-------|
| Home | `/` or `/home/` | [ ] | Hero, search, properties, stats, weather, datetime |
| Properties | `/properties/` | [ ] | Property archive with search |
| Property Detail | `/property/{slug}/` | [ ] | Full property details, gallery, map |
| About Us | `/about/` | [ ] | Company information |
| Services | `/services/` | [ ] | Service offerings |
| Contact | `/contact/` | [ ] | Contact information |
| Submit Property | `/submit-property/` | [ ] | Submission form |
| Blog | `/blog/` | [ ] | Blog posts archive |
| Single Post | `/blog/{slug}/` | [ ] | Individual blog post |
| 404 Page | `/nonexistent-page/` | [ ] | Custom 404 page |

### Page Load Verification

For each page, verify:
- [ ] No PHP errors or warnings
- [ ] No broken images
- [ ] No console JavaScript errors
- [ ] Page loads in under 3 seconds
- [ ] HTTPS active (no mixed content warnings)

---

## 2. Navigation

- [ ] Header displays logo and site name
- [ ] Primary menu shows all main pages
- [ ] Active page is highlighted in navigation
- [ ] Menu links navigate to correct pages
- [ ] Mobile hamburger menu appears on tablet/mobile
- [ ] Mobile menu slides in from the right
- [ ] Mobile menu closes when clicking overlay
- [ ] Mobile menu closes when clicking a link
- [ ] Header becomes sticky on scroll
- [ ] Header shadow appears when scrolled
- [ ] Footer menu displays correctly
- [ ] Footer columns (4) render properly

---

## 3. Property Search

### Search Form (Homepage)

- [ ] Buy / Rent / New Projects tabs switch
- [ ] Location input accepts text
- [ ] Property type dropdown has all options (Apartment, House, Villa, etc.)
- [ ] Price range dropdown has all ranges
- [ ] Bedrooms dropdown works
- [ ] Search button submits form
- [ ] Form redirects to properties archive with parameters

### Search Form (Properties Page)

- [ ] Property Type dropdown populated from taxonomy
- [ ] Status (Sale/Rent) dropdown populated
- [ ] Location dropdown populated with Colombo areas
- [ ] Price Range min/max inputs accept numbers
- [ ] Bedrooms dropdown works (1-10+)
- [ ] Bathrooms dropdown works (1-10+)
- [ ] Min Size input accepts numbers
- [ ] Search button submits
- [ ] Reset link clears all filters

### Search Results

- [ ] Results count displays correctly
- [ ] Property cards show in grid layout
- [ ] Sort dropdown works (Newest, Oldest, Price Low-High, Price High-Low)
- [ ] Pagination appears with multiple pages
- [ ] No results message displays when applicable
- [ ] "View All Properties" link works on empty results

---

## 4. Property Listings

- [ ] Property cards display featured image
- [ ] Property cards show title (linked to detail page)
- [ ] Property cards show location with pin icon
- [ ] Property cards show price formatted correctly
- [ ] Property cards show badge (Sale/Rent)
- [ ] Property cards show Property ID (RE-001 format)
- [ ] Property cards show bed/bath/parking/land size
- [ ] "Enquire" button present on each card
- [ ] Hover effect works (card lifts, image zooms)
- [ ] Responsive: 3 columns > 2 columns > 1 column

---

## 5. Property Details Page

- [ ] Property title displays
- [ ] Featured image displays
- [ ] Property ID badge shows (RE-001 format)
- [ ] Price with currency displays
- [ ] All meta fields show (beds, baths, parking, sizes)
- [ ] Address displays
- [ ] Description content renders
- [ ] Property features list displays (pool, garden, etc.)
- [ ] Gallery images display in grid
- [ ] Gallery images open in lightbox on click
- [ ] Google Maps embed loads correctly
- [ ] Property type taxonomy link works
- [ ] Property status taxonomy link works
- [ ] Location taxonomy link works
- [ ] Enquiry form appears on property pages
- [ ] Share buttons function

---

## 6. Enquiry Form Submission

### Form Fields

- [ ] Full Name field present with label
- [ ] Email field present with validation
- [ ] Telephone field present with validation
- [ ] Preferred Contact Method radio buttons (Phone, Email, WhatsApp, Any)
- [ ] Property ID field (pre-filled when applicable)
- [ ] Property Name field (pre-filled when applicable)
- [ ] Enquiry Type dropdown has all options:
  - Property Information
  - Schedule Viewing
  - Purchase
  - Rental
  - Sell My Property
  - General Enquiry
- [ ] Preferred Viewing Date picker works
- [ ] Message textarea with character limit
- [ ] Submit button present

### Validation

- [ ] Submitting empty form shows all required field errors
- [ ] Invalid email shows error message
- [ ] Invalid phone number shows error
- [ ] Missing enquiry type shows error
- [ ] Missing message shows error
- [ ] Honeypot field is hidden (invisible to users)

### Successful Submission

- [ ] Success message displays
- [ ] Form clears after submission
- [ ] Loading spinner shows during submission
- [ ] Data saved to database (check `wp_realestate_enquiries` table)

---

## 7. Email Notifications

### Admin Notification

- [ ] Admin receives email when enquiry is submitted
- [ ] Email subject contains site name and property
- [ ] Email body shows: Name, Email, Phone, Message
- [ ] "View Property" button links correctly
- [ ] Reply-To set to customer email
- [ ] Email renders correctly in Gmail/Outlook

### Customer Acknowledgement

- [ ] Customer receives auto-reply email
- [ ] Email subject acknowledges their enquiry
- [ ] Email body thanks them and shows property name
- [ ] "View Property" button links correctly
- [ ] From name shows site name

### Property Published Notification

- [ ] Admin notified when property is published
- [ ] Email shows property details (ID, title, price)

### Property Submitted Notification

- [ ] Customer notified when submission is received
- [ ] Email confirms review process

---

## 8. Submit Property Form

### Form Sections

**Owner Information:**
- [ ] Owner Name field (required, max 100 chars)
- [ ] Telephone field (required)
- [ ] Email field (required, email validation)

**Property Details:**
- [ ] Property Type dropdown (House, Apartment, Land, etc.)
- [ ] Location field (required)
- [ ] Full Address textarea (required)
- [ ] Expected Price with LKR prefix (required)
- [ ] Land Size with unit selector (Perches, Acres, sqft, sqm)
- [ ] Building Size with unit selector (sqft, sqm)
- [ ] Bedrooms number input
- [ ] Bathrooms number input
- [ ] Parking Spaces number input
- [ ] Description textarea (min 50 chars, max 5000)

**Property Images:**
- [ ] Drag-and-drop upload zone works
- [ ] Click-to-browse file picker works
- [ ] Accepted formats: JPG, PNG, WebP only
- [ ] Max 10 files enforced
- [ ] Max 5MB per file enforced
- [ ] Max 50MB total enforced
- [ ] Image previews display
- [ ] Remove image button works
- [ ] Progress bar shows during upload

**Consent:**
- [ ] GDPR consent checkbox required
- [ ] Privacy Policy link present

### Submission

- [ ] Submit button shows loading state
- [ ] Honeypot field prevents bots
- [ ] Success message displays
- [ ] Submission saved as `pending` status
- [ ] Admin notified via email
- [ ] Customer receives acknowledgement

---

## 9. Visitor Counter / Statistics

- [ ] Statistics section displays on homepage
- [ ] Counter numbers animate from 0 to target
- [ ] "Properties Listed" count reflects actual published properties
- [ ] "Happy Clients" value displays
- [ ] "Years Experience" value displays
- [ ] "Team Members" value displays
- [ ] Numbers increment correctly
- [ ] Counter works on page refresh
- [ ] REST API endpoint `/wp-json/realestate-analytics/v1/stats` returns data

---

## 10. Live Date/Time Display

- [ ] Date and time widget renders on homepage
- [ ] Shows current date in correct format
- [ ] Shows current time in correct format
- [ ] Timezone matches WordPress setting (Colombo/GMT+5:30)
- [ ] Time updates every second (live clock)
- [ ] Date changes at midnight
- [ ] Timezone displays correctly

---

## 11. Weather Widget

- [ ] Weather widget renders on homepage
- [ ] Shows current temperature
- [ ] Shows weather condition (Sunny, Cloudy, etc.)
- [ ] Shows weather icon from OpenWeatherMap
- [ ] Shows humidity percentage
- [ ] Shows wind speed
- [ ] Shows "feels like" temperature
- [ ] Shows location name (Colombo)
- [ ] Shows last updated timestamp
- [ ] Refresh button works
- [ ] Error state shows when API key is missing
- [ ] Retry button works on error state
- [ ] Data caches for 30 minutes

---

## 12. Responsive Design

### Desktop (1440px+)

- [ ] Container max-width respected (1280px)
- [ ] 4-column property grid
- [ ] 4-column services grid
- [ ] 4-column footer
- [ ] Full navigation visible

### Tablet Landscape (1024px)

- [ ] Container adjusts (1200px)
- [ ] 2-column property grid
- [ ] 2-column services grid
- [ ] 2-column footer
- [ ] Navigation still horizontal

### Tablet Portrait (768px)

- [ ] Hamburger menu appears
- [ ] Single-column property grid
- [ ] Single-column services grid
- [ ] Single-column footer
- [ ] Search form goes single column
- [ ] Hero title size reduces
- [ ] Date/weather widgets stack vertically

### Mobile (480px)

- [ ] Content padding reduces
- [ ] Hero height adjusts (85vh)
- [ ] Stats grid: 2 columns
- [ ] Buttons stack vertically
- [ ] CTA actions stack vertically

### Small Mobile (360px)

- [ ] Hero title further reduced
- [ ] Buttons smaller padding
- [ ] Search box padding reduced
- [ ] Stats grid: 1 column

### Touch Targets

- [ ] All buttons are at least 44x44px
- [ ] Navigation links have adequate spacing
- [ ] Form inputs are touch-friendly

---

## 13. Form Validation

### Enquiry Form

- [ ] Required fields: Full Name, Email, Telephone, Enquiry Type, Message
- [ ] Email validation (proper format)
- [ ] Phone validation (7-30 characters, digits and +()-)
- [ ] Max length: Name (100), Message (2000)
- [ ] Error messages display below each invalid field
- [ ] Error styling (red border/text)
- [ ] Valid form submits successfully

### Submit Property Form

- [ ] Required fields enforced
- [ ] Email validation on owner email
- [ ] Price must be positive number
- [ ] Description minimum 50 characters
- [ ] GDPR consent required
- [ ] File type validation (JPG, PNG, WebP only)
- [ ] File size validation (5MB per file, 50MB total)
- [ ] Max file count validation (10 files)

---

## 14. Security

### Nonce Verification

- [ ] Enquiry form includes nonce field (`ree_enquiry_nonce`)
- [ ] Submit property form includes nonce (`resp_submit_nonce`)
- [ ] Admin AJAX calls verify nonce
- [ ] Invalid nonce returns error

### File Upload Security

- [ ] File type whitelist enforced (JPG, JPEG, PNG, WebP)
- [ ] File size limits enforced
- [ ] Uploaded files stored in WordPress media library
- [ ] File names are sanitized

### SQL Injection Prevention

- [ ] All database queries use `$wpdb->prepare()`
- [ ] User input is sanitized with `sanitize_text_field()`
- [ ] Numeric input validated with `absint()`
- [ ] URLs sanitized with `esc_url_raw()`

### XSS Prevention

- [ ] Output escaped with `esc_html()`, `esc_attr()`, `esc_url()`
- [ ] `wp_kses_post()` used for HTML content
- [ ] Admin user capabilities checked before actions

### Spam Prevention

- [ ] Honeypot field in enquiry form
- [ ] Honeypot field in submit property form
- [ ] Bot detection (user agent check in analytics)

---

## 15. Performance

### Page Load Times

- [ ] Homepage loads in under 3 seconds
- [ ] Properties archive loads in under 3 seconds
- [ ] Property detail page loads in under 3 seconds
- [ ] Images are lazy loaded (`loading="lazy"` attribute)
- [ ] CSS files are minified
- [ ] JavaScript files load in footer

### Caching

- [ ] Weather data cached (30-minute transient)
- [ ] Analytics stats cached (5-minute transient)
- [ ] No unnecessary database queries on page load

### Image Optimization

- [ ] Featured images use appropriate sizes
- [ ] Property gallery images optimized
- [ ] Placeholder images used when no image available

---

## 16. Cross-Browser Testing

| Browser | Desktop | Mobile | Status |
|---------|---------|--------|--------|
| Chrome | [ ] | [ ] | |
| Firefox | [ ] | [ ] | |
| Safari | [ ] | [ ] | |
| Edge | [ ] | [ ] | |
| Samsung Internet | N/A | [ ] | |
| iOS Safari | N/A | [ ] | |

### Browser-Specific Checks

- [ ] CSS Grid renders correctly
- [ ] CSS custom properties work
- [ ] Flexbox layouts display properly
- [ ] Backdrop filter (header blur) works
- [ ] Smooth scroll behavior works
- [ ] Font loading is smooth
- [ ] SVG icons render correctly

---

## Test Execution Record

| Date | Tester | Version | Pass/Fail | Notes |
|------|--------|---------|-----------|-------|
| | | | | |
| | | | | |
| | | | | |
