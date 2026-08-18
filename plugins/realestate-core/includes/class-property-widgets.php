<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Property_Widgets {

    public function __construct() {
        add_action( 'widgets_init', array( $this, 'register_widgets' ) );
    }

    public function register_widgets() {
        register_widget( 'Property_Featured_Widget' );
        register_widget( 'Property_Search_Widget' );
        register_widget( 'Property_Recent_Enquiries_Widget' );
    }
}

/**
 * Featured Properties Widget.
 */
class Property_Featured_Widget extends WP_Widget {

    public function __construct() {
        parent::__construct(
            'property_featured',
            __( 'Featured Properties', 'realestate-core' ),
            array( 'description' => __( 'Displays the latest 5 featured properties.', 'realestate-core' ) )
        );
    }

    public function widget( $args, $instance ) {
        $title = ! empty( $instance['title'] ) ? $instance['title'] : __( 'Featured Properties', 'realestate-core' );
        $title = apply_filters( 'widget_title', $title, $instance, $this->id_base );

        echo $args['before_widget']; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped

        if ( $title ) {
            echo $args['before_title'] . esc_html( $title ) . $args['after_title']; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
        }

        $count = ! empty( $instance['count'] ) ? absint( $instance['count'] ) : 5;

        $query = new WP_Query( array(
            'post_type'      => 'property',
            'post_status'    => 'publish',
            'posts_per_page' => $count,
            'orderby'        => 'date',
            'order'          => 'DESC',
        ) );

        if ( $query->have_posts() ) :
            ?>
            <ul class="re-featured-properties">
                <?php while ( $query->have_posts() ) : $query->the_post();
                    $post_id = get_the_ID();
                    $price      = get_post_meta( $post_id, '_property_price', true );
                    $currency   = get_post_meta( $post_id, '_property_currency', true );
                    $bedrooms   = get_post_meta( $post_id, '_property_bedrooms', true );
                    $bathrooms  = get_post_meta( $post_id, '_property_bathrooms', true );
                    $locations  = wp_get_post_terms( $post_id, 'property_location', array( 'fields' => 'names' ) );
                    ?>
                    <li class="re-featured-properties__item">
                        <a href="<?php the_permalink(); ?>" class="re-featured-properties__link">
                            <div class="re-featured-properties__thumb">
                                <?php if ( has_post_thumbnail() ) : ?>
                                    <?php the_post_thumbnail( 'thumbnail', array( 'class' => 're-featured-properties__img' ) ); ?>
                                <?php else : ?>
                                    <div class="re-featured-properties__placeholder"></div>
                                <?php endif; ?>
                            </div>
                            <div class="re-featured-properties__info">
                                <h4 class="re-featured-properties__title"><?php the_title(); ?></h4>
                                <?php if ( ! empty( $locations ) && ! is_wp_error( $locations ) ) : ?>
                                    <span class="re-featured-properties__location">
                                        <span class="dashicons dashicons-location" aria-hidden="true"></span>
                                        <?php echo esc_html( $locations[0] ); ?>
                                    </span>
                                <?php endif; ?>
                                <?php if ( $price ) : ?>
                                    <span class="re-featured-properties__price">
                                        <?php echo esc_html( $currency . ' ' . number_format( (float) $price ) ); ?>
                                    </span>
                                <?php endif; ?>
                                <div class="re-featured-properties__meta">
                                    <?php if ( $bedrooms ) : ?>
                                        <span><span class="dashicons dashicons-admin-home" aria-hidden="true"></span> <?php echo esc_html( $bedrooms ); ?> <?php esc_html_e( 'Beds', 'realestate-core' ); ?></span>
                                    <?php endif; ?>
                                    <?php if ( $bathrooms ) : ?>
                                        <span><span class="dashicons dashicons-car" aria-hidden="true"></span> <?php echo esc_html( $bathrooms ); ?> <?php esc_html_e( 'Baths', 'realestate-core' ); ?></span>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </a>
                    </li>
                <?php endwhile; ?>
            </ul>
            <?php
            wp_reset_postdata();
        else :
            echo '<p>' . esc_html__( 'No properties found.', 'realestate-core' ) . '</p>';
        endif;

        echo $args['after_widget']; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
    }

    public function form( $instance ) {
        $title = ! empty( $instance['title'] ) ? $instance['title'] : __( 'Featured Properties', 'realestate-core' );
        $count = ! empty( $instance['count'] ) ? absint( $instance['count'] ) : 5;
        ?>
        <p>
            <label for="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>"><?php esc_html_e( 'Title:', 'realestate-core' ); ?></label>
            <input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>">
        </p>
        <p>
            <label for="<?php echo esc_attr( $this->get_field_id( 'count' ) ); ?>"><?php esc_html_e( 'Number of properties:', 'realestate-core' ); ?></label>
            <input class="tiny-text" id="<?php echo esc_attr( $this->get_field_id( 'count' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'count' ) ); ?>" type="number" min="1" max="20" value="<?php echo esc_attr( $count ); ?>">
        </p>
        <?php
    }

    public function update( $new_instance, $old_instance ) {
        $instance                = array();
        $instance['title']       = sanitize_text_field( $new_instance['title'] );
        $instance['count']       = absint( $new_instance['count'] );
        return $instance;
    }
}

/**
 * Property Search Widget.
 */
class Property_Search_Widget extends WP_Widget {

    public function __construct() {
        parent::__construct(
            'property_search',
            __( 'Property Search', 'realestate-core' ),
            array( 'description' => __( 'Displays a property search form in the sidebar.', 'realestate-core' ) )
        );
    }

    public function widget( $args, $instance ) {
        $title = ! empty( $instance['title'] ) ? $instance['title'] : __( 'Search Properties', 'realestate-core' );
        $title = apply_filters( 'widget_title', $title, $instance, $this->id_base );

        echo $args['before_widget']; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped

        if ( $title ) {
            echo $args['before_title'] . esc_html( $title ) . $args['after_title']; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
        }

        $type_options     = $this->get_taxonomy_options( 'property_type' );
        $status_options   = $this->get_taxonomy_options( 'property_status' );
        $location_options = $this->get_taxonomy_options( 'property_location' );

        $selected_type     = isset( $_GET['type'] ) ? sanitize_text_field( wp_unslash( $_GET['type'] ) ) : '';
        $selected_status   = isset( $_GET['status'] ) ? sanitize_text_field( wp_unslash( $_GET['status'] ) ) : '';
        $selected_location = isset( $_GET['location'] ) ? sanitize_text_field( wp_unslash( $_GET['location'] ) ) : '';
        ?>
        <form method="get" action="<?php echo esc_url( get_post_type_archive_link( 'property' ) ); ?>" class="re-widget-search">
            <div class="re-widget-search__field">
                <label for="ws-type-<?php echo esc_attr( $this->id ); ?>"><?php esc_html_e( 'Type', 'realestate-core' ); ?></label>
                <select id="ws-type-<?php echo esc_attr( $this->id ); ?>" name="type" class="widefat">
                    <option value=""><?php esc_html_e( 'All Types', 'realestate-core' ); ?></option>
                    <?php foreach ( $type_options as $value => $label ) : ?>
                        <option value="<?php echo esc_attr( $value ); ?>" <?php selected( $selected_type, $value ); ?>><?php echo esc_html( $label ); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="re-widget-search__field">
                <label for="ws-status-<?php echo esc_attr( $this->id ); ?>"><?php esc_html_e( 'Status', 'realestate-core' ); ?></label>
                <select id="ws-status-<?php echo esc_attr( $this->id ); ?>" name="status" class="widefat">
                    <option value=""><?php esc_html_e( 'All', 'realestate-core' ); ?></option>
                    <?php foreach ( $status_options as $value => $label ) : ?>
                        <option value="<?php echo esc_attr( $value ); ?>" <?php selected( $selected_status, $value ); ?>><?php echo esc_html( $label ); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="re-widget-search__field">
                <label for="ws-location-<?php echo esc_attr( $this->id ); ?>"><?php esc_html_e( 'Location', 'realestate-core' ); ?></label>
                <select id="ws-location-<?php echo esc_attr( $this->id ); ?>" name="location" class="widefat">
                    <option value=""><?php esc_html_e( 'All Locations', 'realestate-core' ); ?></option>
                    <?php foreach ( $location_options as $value => $label ) : ?>
                        <option value="<?php echo esc_attr( $value ); ?>" <?php selected( $selected_location, $value ); ?>><?php echo esc_html( $label ); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="re-widget-search__field">
                <label for="ws-min-<?php echo esc_attr( $this->id ); ?>"><?php esc_html_e( 'Min Price', 'realestate-core' ); ?></label>
                <input type="number" id="ws-min-<?php echo esc_attr( $this->id ); ?>" name="min_price" class="widefat" min="0" placeholder="<?php esc_attr_e( 'Min Price', 'realestate-core' ); ?>">
            </div>
            <div class="re-widget-search__field">
                <label for="ws-max-<?php echo esc_attr( $this->id ); ?>"><?php esc_html_e( 'Max Price', 'realestate-core' ); ?></label>
                <input type="number" id="ws-max-<?php echo esc_attr( $this->id ); ?>" name="max_price" class="widefat" min="0" placeholder="<?php esc_attr_e( 'Max Price', 'realestate-core' ); ?>">
            </div>
            <div class="re-widget-search__submit">
                <button type="submit" class="button button-primary widefat"><?php esc_html_e( 'Search', 'realestate-core' ); ?></button>
            </div>
        </form>
        <?php
        echo $args['after_widget']; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
    }

    private function get_taxonomy_options( $taxonomy ) {
        $terms = get_terms( array(
            'taxonomy'   => $taxonomy,
            'hide_empty' => false,
        ) );
        $options = array();
        if ( ! is_wp_error( $terms ) ) {
            foreach ( $terms as $term ) {
                $options[ $term->slug ] = $term->name;
            }
        }
        return $options;
    }

    public function form( $instance ) {
        $title = ! empty( $instance['title'] ) ? $instance['title'] : __( 'Search Properties', 'realestate-core' );
        ?>
        <p>
            <label for="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>"><?php esc_html_e( 'Title:', 'realestate-core' ); ?></label>
            <input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>">
        </p>
        <?php
    }

    public function update( $new_instance, $old_instance ) {
        $instance          = array();
        $instance['title'] = sanitize_text_field( $new_instance['title'] );
        return $instance;
    }
}

/**
 * Recent Enquiries Widget (Admin).
 */
class Property_Recent_Enquiries_Widget extends WP_Widget {

    public function __construct() {
        parent::__construct(
            'property_recent_enquiries',
            __( 'Recent Enquiries', 'realestate-core' ),
            array( 'description' => __( 'Displays recent property enquiries for admin.', 'realestate-core' ) )
        );
    }

    public function widget( $args, $instance ) {
        if ( ! current_user_can( 'manage_options' ) ) {
            return;
        }

        $title = ! empty( $instance['title'] ) ? $instance['title'] : __( 'Recent Enquiries', 'realestate-core' );
        $title = apply_filters( 'widget_title', $title, $instance, $this->id_base );

        echo $args['before_widget']; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped

        if ( $title ) {
            echo $args['before_title'] . esc_html( $title ) . $args['after_title']; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
        }

        $count = ! empty( $instance['count'] ) ? absint( $instance['count'] ) : 5;

        // Look for enquiry post type if the enquiry plugin creates one, otherwise show recent comments/notes.
        $enquiries = get_posts( array(
            'post_type'      => 'property_enquiry',
            'post_status'    => 'publish',
            'posts_per_page' => $count,
            'orderby'        => 'date',
            'order'          => 'DESC',
        ) );

        if ( ! empty( $enquiries ) ) :
            ?>
            <ul class="re-recent-enquiries">
                <?php foreach ( $enquiries as $enquiry ) : ?>
                    <li class="re-recent-enquiries__item">
                        <a href="<?php echo esc_url( get_edit_post_link( $enquiry->ID ) ); ?>">
                            <strong><?php echo esc_html( $enquiry->post_title ); ?></strong>
                            <span class="re-recent-enquiries__date"><?php echo esc_html( get_the_date( 'M j, Y', $enquiry->ID ) ); ?></span>
                        </a>
                    </li>
                <?php endforeach; ?>
            </ul>
            <p><a href="<?php echo esc_url( admin_url( 'edit.php?post_type=property_enquiry' ) ); ?>"><?php esc_html_e( 'View All Enquiries', 'realestate-core' ); ?></a></p>
        <?php else :
            echo '<p>' . esc_html__( 'No enquiries yet.', 'realestate-core' ) . '</p>';
        endif;

        echo $args['after_widget']; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
    }

    public function form( $instance ) {
        $title = ! empty( $instance['title'] ) ? $instance['title'] : __( 'Recent Enquiries', 'realestate-core' );
        $count = ! empty( $instance['count'] ) ? absint( $instance['count'] ) : 5;
        ?>
        <p>
            <label for="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>"><?php esc_html_e( 'Title:', 'realestate-core' ); ?></label>
            <input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>">
        </p>
        <p>
            <label for="<?php echo esc_attr( $this->get_field_id( 'count' ) ); ?>"><?php esc_html_e( 'Number to show:', 'realestate-core' ); ?></label>
            <input class="tiny-text" id="<?php echo esc_attr( $this->get_field_id( 'count' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'count' ) ); ?>" type="number" min="1" max="20" value="<?php echo esc_attr( $count ); ?>">
        </p>
        <?php
    }

    public function update( $new_instance, $old_instance ) {
        $instance          = array();
        $instance['title'] = sanitize_text_field( $new_instance['title'] );
        $instance['count'] = absint( $new_instance['count'] );
        return $instance;
    }
}
