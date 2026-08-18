<?php
/**
 * 11AA Real Estate — Page Setup Utility
 *
 * Creates all website pages, sets front page, and configures navigation menus.
 * Place in wp-content/mu-plugins/ to auto-load, OR run once via:
 *   wp eval 'require ABSPATH . "../wp-content/page-setup.php"; realestate_setup_all_pages();'
 *
 * @package 11AA_RealEstate
 * @since   1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Master setup — creates pages, menus, and reading settings.
 */
function realestate_setup_all_pages() {
    if ( ! current_user_can( 'manage_options' ) ) {
        wp_die( 'You need administrator privileges to run this setup.' );
    }

    $created = realestate_create_pages();
    realestate_set_reading_settings();
    realestate_setup_menus();

    // Admin notice.
    add_action( 'admin_notices', function () use ( $created ) {
        $count = count( $created );
        echo '<div class="notice notice-success is-dismissible"><p>';
        printf(
            '11AA Real Estate setup complete! %d pages created. <a href="%s">View Site</a>',
            $count,
            esc_url( home_url( '/' ) )
        );
        echo '</p></div>';
    } );
}

/**
 * ---------------------------------------------------------------------------
 * PAGE CREATION
 * ---------------------------------------------------------------------------
 */
function realestate_create_pages() {
    $pages = array(
        array(
            'title'       => 'Home',
            'slug'        => 'home',
            'content'     => realestate_home_content(),
            'template'    => '',
            'set_front'   => true,
        ),
        array(
            'title'       => 'Properties',
            'slug'        => 'properties',
            'content'     => realestate_properties_content(),
            'template'    => '',
        ),
        array(
            'title'       => 'About Us',
            'slug'        => 'about-us',
            'content'     => realestate_about_content(),
            'template'    => '',
        ),
        array(
            'title'       => 'Services',
            'slug'        => 'services',
            'content'     => realestate_services_content(),
            'template'    => '',
        ),
        array(
            'title'       => 'Contact Us',
            'slug'        => 'contact-us',
            'content'     => realestate_contact_content(),
            'template'    => '',
        ),
        array(
            'title'       => 'Submit Your Property',
            'slug'        => 'submit-your-property',
            'content'     => realestate_submit_content(),
            'template'    => '',
        ),
        array(
            'title'       => 'Customer Enquiry',
            'slug'        => 'customer-enquiry',
            'content'     => realestate_enquiry_content(),
            'template'    => '',
        ),
        array(
            'title'       => 'Privacy Policy',
            'slug'        => 'privacy-policy',
            'content'     => realestate_privacy_content(),
            'template'    => '',
        ),
        array(
            'title'       => 'Terms & Conditions',
            'slug'        => 'terms-conditions',
            'content'     => realestate_terms_content(),
            'template'    => '',
        ),
        array(
            'title'       => 'Thank You',
            'slug'        => 'thank-you',
            'content'     => realestate_thankyou_content(),
            'template'    => '',
        ),
    );

    $created = array();

    foreach ( $pages as $page ) {
        $existing = get_page_by_path( $page['slug'] );

        if ( $existing ) {
            $created[] = array(
                'title' => $page['title'],
                'id'    => $existing->ID,
                'slug'  => $page['slug'],
                'note'  => 'already exists',
            );
            continue;
        }

        $page_id = wp_insert_post( array(
            'post_title'   => $page['title'],
            'post_name'    => $page['slug'],
            'post_content' => $page['content'],
            'post_status'  => 'publish',
            'post_type'    => 'page',
            'post_author'  => get_current_user_id(),
        ) );

        if ( $page_id && ! is_wp_error( $page_id ) ) {
            if ( ! empty( $page['template'] ) ) {
                update_post_meta( $page_id, '_wp_page_template', $page['template'] );
            }

            $created[] = array(
                'title' => $page['title'],
                'id'    => $page_id,
                'slug'  => $page['slug'],
                'note'  => 'created',
            );
        }
    }

    return $created;
}

/**
 * ---------------------------------------------------------------------------
 * READING SETTINGS — set front page
 * ---------------------------------------------------------------------------
 */
function realestate_set_reading_settings() {
    $front_page = get_page_by_path( 'home' );
    if ( $front_page ) {
        update_option( 'show_on_front', 'page' );
        update_option( 'page_on_front', $front_page->ID );
    }
}

/**
 * ---------------------------------------------------------------------------
 * NAVIGATION MENUS
 * ---------------------------------------------------------------------------
 */
function realestate_setup_menus() {
    // Create menus if they don't exist.
    $primary = get_term_by( 'name', 'Primary Menu', 'nav_menu' );
    if ( ! $primary ) {
        wp_insert_nav_menu_item( 0, array(
            'menu-item-title'  => 'Home',
            'menu-item-url'    => home_url( '/' ),
            'menu-item-status' => 'publish',
        ) );
        $menu_id = wp_create_nav_menu( 'Primary Menu', array( 'theme_location' => 'primary-menu' ) );
    } else {
        $menu_id = $primary->term_id;
    }

    // Clear existing items and rebuild.
    $menu_items = wp_get_nav_menu_items( $menu_id );
    if ( $menu_items ) {
        foreach ( $menu_items as $item ) {
            wp_delete_post( $item->DB_ID, true );
        }
    }

    $menu_items_data = array(
        array( 'title' => 'Home',          'url' => home_url( '/' ) ),
        array( 'title' => 'Properties',    'url' => home_url( '/properties/' ) ),
        array( 'title' => 'Services',      'url' => home_url( '/services/' ) ),
        array( 'title' => 'About Us',      'url' => home_url( '/about-us/' ) ),
        array( 'title' => 'Contact Us',    'url' => home_url( '/contact-us/' ) ),
        array( 'title' => 'Submit Property','url' => home_url( '/submit-your-property/' ) ),
    );

    $menu_order = 0;
    foreach ( $menu_items_data as $item_data ) {
        wp_update_nav_menu_item( $menu_id, 0, array(
            'menu-item-title'     => $item_data['title'],
            'menu-item-url'       => $item_data['url'],
            'menu-item-status'    => 'publish',
            'menu-item-position'  => $menu_order++,
        ) );
    }

    // Assign to theme location.
    $locations = get_theme_mod( 'nav_menu_locations' );
    $locations['primary-menu'] = $menu_id;
    set_theme_mod( 'nav_menu_locations', $locations );

    // Footer menu.
    $footer = get_term_by( 'name', 'Footer Menu', 'nav_menu' );
    if ( ! $footer ) {
        $footer_id = wp_create_nav_menu( 'Footer Menu', array( 'theme_location' => 'footer-menu' ) );
    } else {
        $footer_id = $footer->term_id;
    }

    $footer_items = wp_get_nav_menu_items( $footer_id );
    if ( $footer_items ) {
        foreach ( $footer_items as $item ) {
            wp_delete_post( $item->DB_ID, true );
        }
    }

    $footer_links = array(
        array( 'title' => 'Home',            'url' => home_url( '/' ) ),
        array( 'title' => 'Properties',      'url' => home_url( '/properties/' ) ),
        array( 'title' => 'About Us',        'url' => home_url( '/about-us/' ) ),
        array( 'title' => 'Services',        'url' => home_url( '/services/' ) ),
        array( 'title' => 'Contact Us',      'url' => home_url( '/contact-us/' ) ),
        array( 'title' => 'Privacy Policy',  'url' => home_url( '/privacy-policy/' ) ),
        array( 'title' => 'Terms & Conditions', 'url' => home_url( '/terms-conditions/' ) ),
    );

    $footer_order = 0;
    foreach ( $footer_links as $fl ) {
        wp_update_nav_menu_item( $footer_id, 0, array(
            'menu-item-title'     => $fl['title'],
            'menu-item-url'       => $fl['url'],
            'menu-item-status'    => 'publish',
            'menu-item-position'  => $footer_order++,
        ) );
    }

    $locations['footer-menu'] = $footer_id;
    set_theme_mod( 'nav_menu_locations', $locations );
}

/**
 * ---------------------------------------------------------------------------
 * PAGE CONTENT — shortcodes from our custom plugins
 * ---------------------------------------------------------------------------
 */

/**
 * Home page content.
 */
function realestate_home_content() {
    return '
<!-- HERO SECTION — built with Elementor for full design control -->
<!-- Search form: [property_search] -->
<!-- DateTime: [datetime_display] -->
<!-- Weather: [weather_widget] -->
<!-- Stats: [realestate_stats show="all"] -->
<!-- Featured properties load via WP_Query in the theme template -->

[datetime_display]

[weather_widget]

[realestate_stats show="all"]

[property_search layout="horizontal"]

<!-- Elementor note: Import the json/home-page.json template for the full design -->
<!-- The above shortcodes will render inside Elementor shortcode widgets -->
';
}

/**
 * Properties page content.
 */
function realestate_properties_content() {
    return '
<!-- PROPERTY SEARCH & GRID -->
<!-- Full search form with filters -->
[property_search show_title="true" layout="horizontal"]

<!-- Property results grid (generated from the search query) -->
[property_results]

<!-- Elementor note: Import json/home-page.json uses [property_search] -->
<!-- On this page the shortcode handles the full search + results flow -->
';
}

/**
 * About Us page content.
 */
function realestate_about_content() {
    return '
<!-- ABOUT 11AA REAL ESTATE -->

<h2>Welcome to 11AA Real Estate</h2>
<p>With over a decade of experience in the Sri Lankan real estate market, 11AA Real Estate has established itself as a trusted name in property sales, rentals, and management. Our team of dedicated professionals is committed to helping you find the perfect property that matches your lifestyle and investment goals.</p>

<h3>Our Mission</h3>
<p>To provide exceptional real estate services that exceed client expectations through integrity, innovation, and deep market expertise. We strive to make every property transaction seamless and rewarding.</p>

<h3>Our Vision</h3>
<p>To be Sri Lanka\'s most trusted and innovative real estate company, known for our commitment to client satisfaction, market knowledge, and ethical business practices.</p>

<h3>Core Values</h3>
<ul>
<li><strong>Integrity:</strong> We conduct all business with the highest ethical standards.</li>
<li><strong>Excellence:</strong> We pursue excellence in every aspect of our service.</li>
<li><strong>Client Focus:</strong> Our clients\' needs drive every decision we make.</li>
<li><strong>Innovation:</strong> We embrace modern tools and strategies to deliver better results.</li>
<li><strong>Transparency:</strong> We believe in open, honest communication at all times.</li>
</ul>

<h3>Why Choose 11AA Real Estate?</h3>
<ul>
<li>12+ years of market expertise in Sri Lanka</li>
<li>500+ successful property transactions</li>
<li>Professional, multilingual team</li>
<li>Comprehensive market analysis and valuation</li>
<li>End-to-end service from search to closing</li>
</ul>

[realestate_stats show="all"]

<h3>Our Team</h3>
<p>Our experienced team includes licensed real estate agents, property managers, marketing specialists, and legal advisors — all dedicated to providing you with a world-class real estate experience.</p>

<p><a href="' . esc_url( home_url( '/contact-us/' ) ) . '">Get in Touch with Our Team</a></p>
';
}

/**
 * Services page content.
 */
function realestate_services_content() {
    return '
<!-- OUR SERVICES -->

<h2>Comprehensive Real Estate Solutions</h2>
<p>At 11AA Real Estate, we offer a full spectrum of real estate services designed to meet the needs of buyers, sellers, investors, and property owners across Sri Lanka.</p>

<h3>1. Property Sales</h3>
<p>Whether you\'re buying your first home or investing in commercial property, our expert agents guide you through every step. We leverage market data, professional photography, and strategic marketing to ensure the best outcomes.</p>

<h3>2. Property Rentals</h3>
<p>Find the perfect rental property or let us manage your rental portfolio. We handle tenant screening, lease agreements, rent collection, and property maintenance for hassle-free rental management.</p>

<h3>3. Property Valuation</h3>
<p>Get accurate, data-driven property valuations for买卖, insurance, or investment purposes. Our certified valuers use comparable market analysis and property inspection to determine true market value.</p>

<h3>4. Marketing & Advertising</h3>
<p>Maximize your property\'s exposure with our multi-channel marketing approach including professional photography, virtual tours, social media campaigns, and listing on top property portals.</p>

<h3>5. Property Management</h3>
<p>From routine maintenance to emergency repairs, our property management team ensures your investment is well-maintained and profitable. We handle everything so you don\'t have to.</p>

<h3>6. Investment Advisory</h3>
<p>Make informed investment decisions with our comprehensive market analysis, ROI projections, and portfolio management services. We help you identify opportunities that align with your financial goals.</p>

<p><a href="' . esc_url( home_url( '/contact-us/' ) ) . '">Contact Us for a Free Consultation</a></p>
<p><a href="' . esc_url( home_url( '/submit-your-property/' ) ) . '">Submit Your Property for Listing</a></p>
';
}

/**
 * Contact Us page content.
 */
function realestate_contact_content() {
    return '
<!-- CONTACT US -->

<h2>Get In Touch</h2>
<p>We\'d love to hear from you. Reach out to us using any of the methods below, or fill out the enquiry form and we\'ll get back to you within 24 hours.</p>

<h3>Contact Information</h3>
<p><strong>Address:</strong> 123 Business Avenue, Suite 100, Colombo, Sri Lanka</p>
<p><strong>Phone:</strong> +94 11 234 5678</p>
<p><strong>Email:</strong> info@11aarealestate.com</p>
<p><strong>Working Hours:</strong> Mon - Fri: 9:00 AM - 6:00 PM | Sat: 9:00 AM - 1:00 PM</p>

[property_enquiry_form]

<h3>Find Us on the Map</h3>
<!-- Google Maps embed placeholder — configure in Elementor -->
<p>Visit our office at 123 Business Avenue, Colombo.</p>
';
}

/**
 * Submit Your Property page content.
 */
function realestate_submit_content() {
    return '
<!-- SUBMIT YOUR PROPERTY -->

<h2>List Your Property With Us</h2>
<p>Have a property you want to sell or rent? Fill out the form below and our team will review your submission and get in touch with you shortly.</p>

[submit_property_form]

<p><strong>Note:</strong> All submissions are reviewed within 48 hours. Once approved, your property will be listed on our website and partner portals for maximum exposure.</p>
';
}

/**
 * Customer Enquiry page content.
 */
function realestate_enquiry_content() {
    return '
<!-- CUSTOMER ENQUIRY -->

<h2>How Can We Help You?</h2>
<p>Whether you have a question about a property, need a valuation, or want to schedule a viewing, our team is here to help. Fill out the form below and we\'ll respond promptly.</p>

[property_enquiry_form]

<h3>Other Ways to Reach Us</h3>
<p><strong>Phone:</strong> +94 11 234 5678</p>
<p><strong>Email:</strong> info@11aarealestate.com</p>
<p><strong>WhatsApp:</strong> +94 77 123 4567</p>
';
}

/**
 * Privacy Policy page content.
 */
function realestate_privacy_content() {
    return '
<h2>Privacy Policy</h2>
<p><em>Last updated: August 2026</em></p>

<h3>1. Information We Collect</h3>
<p>When you use our website or services, we may collect personal information including but not limited to: name, email address, phone number, property preferences, and browsing behavior.</p>

<h3>2. How We Use Your Information</h3>
<p>We use your personal information to: respond to your enquiries, provide property recommendations, process property submissions, send market updates (with your consent), and improve our services.</p>

<h3>3. Data Sharing</h3>
<p>We do not sell or rent your personal information to third parties. We may share your data with: property listing partners (with your consent), service providers who assist in our operations, and legal authorities when required by law.</p>

<h3>4. Data Security</h3>
<p>We implement industry-standard security measures to protect your personal information from unauthorized access, disclosure, or misuse.</p>

<h3>5. Cookies</h3>
<p>Our website uses cookies to enhance your browsing experience, analyze site traffic, and personalize content. You can control cookie settings through your browser preferences.</p>

<h3>6. Your Rights</h3>
<p>You have the right to: access your personal data, request correction of inaccurate data, request deletion of your data, and opt-out of marketing communications.</p>

<h3>7. Contact Us</h3>
<p>For privacy-related enquiries, please contact us at: <strong>info@11aarealestate.com</strong></p>
';
}

/**
 * Terms & Conditions page content.
 */
function realestate_terms_content() {
    return '
<h2>Terms & Conditions</h2>
<p><em>Last updated: August 2026</em></p>

<h3>1. Acceptance of Terms</h3>
<p>By accessing and using the 11AA Real Estate website and services, you agree to be bound by these Terms and Conditions. If you do not agree, please do not use our services.</p>

<h3>2. Services</h3>
<p>11AA Real Estate provides property listing, search, valuation, and advisory services. All property information is provided in good faith, but we do not guarantee the accuracy of third-party data.</p>

<h3>3. Property Listings</h3>
<p>Property listings on our website are for informational purposes only. Prices, availability, and specifications are subject to change without notice. We recommend verifying all details directly with the property owner or our agents.</p>

<h3>4. User Responsibilities</h3>
<p>Users must provide accurate information when submitting enquiries or property listings. False or misleading information may result in removal from our platform.</p>

<h3>5. Intellectual Property</h3>
<p>All content on this website, including text, images, logos, and design, is the property of 11AA Real Estate and protected by copyright law. Reproduction without permission is prohibited.</p>

<h3>6. Limitation of Liability</h3>
<p>11AA Real Estate shall not be liable for any direct, indirect, or consequential damages arising from the use of our website or services.</p>

<h3>7. Governing Law</h3>
<p>These terms are governed by the laws of the Democratic Socialist Republic of Sri Lanka.</p>
';
}

/**
 * Thank You page content.
 */
function realestate_thankyou_content() {
    return '
<!-- THANK YOU PAGE -->

<h2>Thank You!</h2>
<p>Your enquiry has been submitted successfully. Our team will review your submission and get back to you within 24 hours.</p>

<h3>What Happens Next?</h3>
<ol>
<li>Our team will review your enquiry within 24 hours</li>
<li>A property specialist will be assigned to assist you</li>
<li>We\'ll contact you via your preferred contact method</li>
<li>We\'ll schedule a viewing or provide the information you requested</li>
</ol>

<p>In the meantime, feel free to <a href="' . esc_url( home_url( '/properties/' ) ) . '">browse our available properties</a>.</p>

<p>Need immediate assistance? Call us at <strong>+94 11 234 5678</strong></p>
';
}

/**
 * ---------------------------------------------------------------------------
 * MU-PLUGIN AUTO-LOAD (optional)
 * ---------------------------------------------------------------------------
 * If placed in mu-plugins, this runs automatically once.
 * After setup, rename or remove this file to prevent re-running.
 */
if ( defined( 'WPINC' ) && basename( __FILE__ ) === 'page-setup.php' ) {
    add_action( 'admin_init', function () {
        if ( isset( $_GET['realestate_run_setup'] ) && current_user_can( 'manage_options' ) ) {
            realestate_setup_all_pages();
            wp_safe_redirect( admin_url( 'options-general.php?page=realestate-setup&done=1' ) );
            exit;
        }
    } );

    add_action( 'admin_menu', function () {
        add_options_page(
            '11AA Real Estate Setup',
            '11AA RE Setup',
            'manage_options',
            'realestate-setup',
            function () {
                echo '<div class="wrap">';
                echo '<h1>11AA Real Estate — Page Setup</h1>';

                if ( isset( $_GET['done'] ) ) {
                    echo '<div class="notice notice-success"><p>Setup completed successfully! <a href="' . esc_url( home_url( '/' ) ) . '">View Site</a></p></div>';
                }

                echo '<p>Click the button below to create all website pages, set the front page, and configure navigation menus.</p>';
                echo '<p><strong>Note:</strong> This will not overwrite existing pages. Safe to run multiple times.</p>';
                echo '<a class="button button-primary button-hero" href="' . esc_url( wp_nonce_url( admin_url( 'options-general.php?page=realestate-setup&realestate_run_setup=1' ), 'realestate_setup_nonce' ) ) . '">Run Page Setup</a>';
                echo '<hr>';
                echo '<h2>Pages That Will Be Created</h2>';
                echo '<ul>';
                echo '<li>Home (front page)</li>';
                echo '<li>Properties</li>';
                echo '<li>About Us</li>';
                echo '<li>Services</li>';
                echo '<li>Contact Us</li>';
                echo '<li>Submit Your Property</li>';
                echo '<li>Customer Enquiry</li>';
                echo '<li>Privacy Policy</li>';
                echo '<li>Terms & Conditions</li>';
                echo '<li>Thank You</li>';
                echo '</ul>';
                echo '</div>';
            }
        );
    } );
}
