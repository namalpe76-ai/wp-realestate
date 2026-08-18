<?php
/**
 * 11AA Real Estate — Elementor Widgets Map
 *
 * Documents which Elementor widgets and shortcodes to use on each page.
 * Use this as a reference when building pages in Elementor or importing
 * the provided JSON templates.
 *
 * @package 11AA_RealEstate
 * @since   1.0.0
 */

/*
|--------------------------------------------------------------------------
| AVAILABLE SHORTCODES (from custom plugins)
|--------------------------------------------------------------------------
|
| [property_search]            — Full property search form with filters
|   Attributes: show_title (bool), layout (horizontal|vertical)
|
| [property_results]           — Property search results grid
|
| [datetime_display]           — Live date/time widget (Sri Lanka timezone)
|   Attributes: timezone, date_fmt, time_fmt, variant (default|dark)
|
| [weather_widget]             — Weather card (OpenWeatherMap)
|   Attributes: location, unit
|
| [realestate_stats]           — Analytics counter with animated numbers
|   Attributes: show (all|visitors|pageviews|properties|sold|customers)
|
| [property_enquiry_form]      — Customer enquiry form
|   Attributes: property_id, property_name
|
| [submit_property_form]       — Public property submission form
|
|--------------------------------------------------------------------------
| AVAILABLE ELEMENTOR WIDGETS (use these in Elementor editor)
|--------------------------------------------------------------------------
|
| elementor-widget-shortcode    — Wraps any shortcode above
| elementor-widget-heading      — Section headings
| elementor-widget-text-editor  — Rich text content
| elementor-widget-image        — Images and icons
| elementor-widget-button       — Call-to-action buttons
| elementor-widget-video        — Embedded videos
| elementor-widget-google_maps  — Google Maps embed
| elementor-widget-icon-list    — Feature lists with icons
| elementor-widget-divider      — Section dividers
| elementor-widget-spacer       — Vertical spacing
| elementor-widget-html         — Custom HTML blocks
| elementor-widget-columns      — Multi-column layouts
| elementor-widget-toggle       — Accordion/FAQ
| elementor-widget-tabs         — Tabbed content
| elementor-widget-counter      — Animated number counters
| elementor-widget-testimonial  — Client testimonials
| elementor-widget-icon-box     — Icon with text box
|
|--------------------------------------------------------------------------
| PAGE MAP — HOME
|--------------------------------------------------------------------------
|
| Section 1: Hero
|   - Background: Full-width image (assets/images/hero-bg.jpg)
|   - Widget: elementor-widget-heading — "Find Your Perfect Property"
|   - Widget: elementor-widget-text-editor — subtitle
|   - Widget: elementor-widget-button — "Browse Properties" + "Contact Us"
|
| Section 2: Property Search (floating box overlapping hero)
|   - Widget: elementor-widget-shortcode
|     Content: [property_search layout="horizontal"]
|
| Section 3: Date & Time + Weather (side by side)
|   - Column A: elementor-widget-shortcode
|     Content: [datetime_display]
|   - Column B: elementor-widget-shortcode
|     Content: [weather_widget]
|
| Section 4: Statistics Counters
|   - Widget: elementor-widget-shortcode
|     Content: [realestate_stats show="all"]
|   OR use elementor-widget-counter (x5) with static values:
|     500+ Properties Listed | 350+ Happy Clients | 12+ Years | 45+ Team | 150+ Sold
|
| Section 5: Featured Properties
|   - Widget: elementor-widget-heading — "Explore Our Top Picks"
|   - Widget: elementor-widget-posts (query: post type = property, count = 6)
|   - Widget: elementor-widget-button — "View All Properties"
|
| Section 6: Services Overview (4 cards)
|   - Widget: elementor-widget-heading — "What We Offer"
|   - Widget: elementor-widget-icon-box (x4):
|     1. Property Buying — house icon
|     2. Property Selling — briefcase icon
|     3. Property Leasing — box icon
|     4. Investment Advisory — dollar icon
|
| Section 7: Testimonials
|   - Widget: elementor-widget-heading — "What Our Clients Say"
|   - Widget: elementor-widget-testimonial (x3):
|     James Rodriguez — Home Buyer
|     Sarah Chen — Property Seller
|     Michael Kim — Investor
|
| Section 8: CTA Banner
|   - Background: Color or image
|   - Widget: elementor-widget-heading — "Ready to Find Your Dream Property?"
|   - Widget: elementor-widget-button — "Browse Properties" + "Schedule Consultation"
|
|--------------------------------------------------------------------------
| PAGE MAP — PROPERTIES
|--------------------------------------------------------------------------
|
| Section 1: Page Header
|   - Widget: elementor-widget-heading — "Browse Our Properties"
|   - Widget: elementor-widget-text-editor — subtitle
|
| Section 2: Search Filters
|   - Widget: elementor-widget-shortcode
|     Content: [property_search show_title="true" layout="horizontal"]
|
| Section 3: Property Grid
|   - Widget: elementor-widget-shortcode
|     Content: [property_results]
|
|--------------------------------------------------------------------------
| PAGE MAP — ABOUT US
|--------------------------------------------------------------------------
|
| Section 1: Hero
|   - Background: Image
|   - Widget: elementor-widget-heading — "About 11AA Real Estate"
|   - Widget: elementor-widget-text-editor — intro paragraph
|
| Section 2: Mission & Vision (2 columns)
|   - Column A: elementor-widget-icon-box — Mission statement
|   - Column B: elementor-widget-icon-box — Vision statement
|
| Section 3: Core Values (3-column grid)
|   - Widget: elementor-widget-icon-box (x5):
|     Integrity | Excellence | Client Focus | Innovation | Transparency
|
| Section 4: Why Choose Us
|   - Widget: elementor-widget-icon-list (x5 items):
|     12+ years | 500+ transactions | Professional team | Market analysis | End-to-end service
|
| Section 5: Statistics
|   - Widget: elementor-widget-shortcode
|     Content: [realestate_stats show="all"]
|
| Section 6: CTA
|   - Widget: elementor-widget-heading — "Ready to Work With Us?"
|   - Widget: elementor-widget-button — "Contact Us" / "View Properties"
|
|--------------------------------------------------------------------------
| PAGE MAP — SERVICES
|--------------------------------------------------------------------------
|
| Section 1: Hero
|   - Widget: elementor-widget-heading — "Our Services"
|   - Widget: elementor-widget-text-editor — intro text
|
| Section 2: Service Cards (2x3 or 3x2 grid)
|   - Widget: elementor-widget-icon-box (x6):
|     1. Property Sales    — home icon — "Expert guidance through every step..."
|     2. Property Rentals  — key icon — "Find the perfect rental or let us manage..."
|     3. Property Valuation — chart icon — "Accurate, data-driven property valuations..."
|     4. Marketing         — megaphone icon — "Multi-channel marketing approach..."
|     5. Property Mgmt     — settings icon — "From routine maintenance to emergency..."
|     6. Investment        — trending-up icon — "Make informed investment decisions..."
|
| Section 3: CTA
|   - Widget: elementor-widget-heading — "Need a Custom Solution?"
|   - Widget: elementor-widget-button — "Contact Us" / "Submit Property"
|
|--------------------------------------------------------------------------
| PAGE MAP — CONTACT US
|--------------------------------------------------------------------------
|
| Section 1: Contact Info Cards (3 columns)
|   - Column A: elementor-widget-icon-box — Address
|   - Column B: elementor-widget-icon-box — Phone + Email
|   - Column C: elementor-widget-icon-box — Working Hours
|
| Section 2: Map + Form (2 columns)
|   - Column A: elementor-widget-google_maps — office location
|   - Column B: elementor-widget-shortcode
|     Content: [property_enquiry_form]
|
| Section 3: Social Links
|   - Widget: elementor-widget-icon-list — Facebook, Twitter, Instagram, LinkedIn, WhatsApp
|
|--------------------------------------------------------------------------
| PAGE MAP — SUBMIT YOUR PROPERTY
|--------------------------------------------------------------------------
|
| Section 1: Page Header
|   - Widget: elementor-widget-heading — "List Your Property"
|   - Widget: elementor-widget-text-editor — instructions
|
| Section 2: Submission Form
|   - Widget: elementor-widget-shortcode
|     Content: [submit_property_form]
|
|--------------------------------------------------------------------------
| PAGE MAP — CUSTOMER ENQUIRY
|--------------------------------------------------------------------------
|
| Section 1: Page Header
|   - Widget: elementor-widget-heading — "How Can We Help You?"
|
| Section 2: Enquiry Form
|   - Widget: elementor-widget-shortcode
|     Content: [property_enquiry_form]
|
| Section 3: Alternative Contact
|   - Widget: elementor-widget-icon-list — Phone, Email, WhatsApp
|
|--------------------------------------------------------------------------
| PAGE MAP — THANK YOU
|--------------------------------------------------------------------------
|
| Section 1: Confirmation
|   - Widget: elementor-widget-heading — "Thank You!"
|   - Widget: elementor-widget-text-editor — confirmation message
|   - Widget: elementor-widget-icon-list — "What Happens Next" steps
|   - Widget: elementor-widget-button — "Browse Properties"
|
|--------------------------------------------------------------------------
| PAGE MAP — PRIVACY POLICY
|--------------------------------------------------------------------------
|
| - Widget: elementor-widget-text-editor — full privacy policy content
|   (Use the content from page-setup.php realestate_privacy_content())
|
|--------------------------------------------------------------------------
| PAGE MAP — TERMS & CONDITIONS
|--------------------------------------------------------------------------
|
| - Widget: elementor-widget-text-editor — full T&C content
|   (Use the content from page-setup.php realestate_terms_content())
|
|--------------------------------------------------------------------------
| ELEMENTOR IMPORT INSTRUCTIONS
|--------------------------------------------------------------------------
|
| 1. Open WordPress Admin > Templates > Import Templates
| 2. Upload the .json file from the json/ directory
| 3. The template will appear under "My Templates"
| 4. Edit the target page with Elementor
| 5. Click "Add Template" > "My Templates" > Insert the imported template
| 6. Replace shortcode widget content with actual shortcodes if not pre-filled
| 7. Adjust images, colors, and spacing to match your brand
|
| SHORTCUT: Use the page-setup.php utility to create pages with shortcodes
| already embedded. Then use Elementor to visually enhance each page.
|
|--------------------------------------------------------------------------
*/
