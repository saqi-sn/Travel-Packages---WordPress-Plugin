<?php
/**
 * Plugin Name: Travel Packages
 * Description: A plugin to add and display travel packages
 * Version: 1.0
 * Author: Ali Sahafi
 * Author URI: https://alisahafi.com
 */

// Add CPT
function travel_packages_post_type() {

    $labels = array(
        'name'                  => _x( 'Travel Packages', 'Post Type General Name', 'text_domain' ),
        'singular_name'         => _x( 'Travel Package', 'Post Type Singular Name', 'text_domain' ),
        'menu_name'             => __( 'Travel Packages', 'text_domain' ),
        'name_admin_bar'        => __( 'Travel Package', 'text_domain' ),
    );

    $args = array(
        'label'                 => __( 'Travel Package', 'text_domain' ),
        'description'           => __( 'Post Type for Travel Packages', 'text_domain' ),
        'labels'                => $labels,
        'supports'              => array( 'title', 'editor', 'thumbnail' ),
        'public'                => true,
        'show_ui'               => true,
        'show_in_menu'          => true,
        'menu_position'         => 20,
        'show_in_admin_bar'     => true,
        'show_in_nav_menus'     => true,
        'can_export'            => true,
        'has_archive'           => true,
        'rewrite'               => array( 'slug' => 'travel-packages' ),
        'capability_type'       => 'post',
    );

    register_post_type( 'travel_package', $args );
}
add_action( 'init', 'travel_packages_post_type', 0 );

// Add Cities
function travel_packages_register_taxonomy() {
    $labels = array(
        'name'              => _x( 'Cities', 'taxonomy general name', 'text_domain' ),
        'singular_name'     => _x( 'City', 'taxonomy singular name', 'text_domain' ),
        'search_items'      => __( 'Search Cities', 'text_domain' ),
        'all_items'         => __( 'All Cities', 'text_domain' ),
        'edit_item'         => __( 'Edit City', 'text_domain' ),
        'update_item'       => __( 'Update City', 'text_domain' ),
        'add_new_item'      => __( 'Add New City', 'text_domain' ),
        'new_item_name'     => __( 'New City Name', 'text_domain' ),
        'menu_name'         => __( 'City', 'text_domain' ),
    );

    $args = array(
        'hierarchical'      => true, // Like categories
        'labels'            => $labels,
        'show_ui'           => true,
        'show_admin_column' => true,
        'query_var'         => true,
        'rewrite'           => array( 'slug' => 'city' ),
    );

    register_taxonomy( 'city', array( 'travel_package' ), $args );
}
add_action( 'init', 'travel_packages_register_taxonomy' );

// Price and Availability Meta Fields
function travel_packages_meta_boxes() {
    add_meta_box( 'travel_package_meta', __( 'Package Details', 'text_domain' ), 'travel_package_meta_callback', 'travel_package' );
}

add_action( 'add_meta_boxes', 'travel_packages_meta_boxes' );

function travel_package_meta_callback( $post ) {
    $price = get_post_meta( $post->ID, '_package_price', true );
    $availability = get_post_meta( $post->ID, '_package_availability', true );
    ?>
    <label for="package_price"><?php _e( 'Price', 'text_domain' ); ?></label>
    <input type="number" id="package_price" name="package_price" value="<?php echo esc_attr( $price ); ?>" /><br>

    <label for="package_availability"><?php _e( 'Availability', 'text_domain' ); ?></label>
    <input type="text" id="package_availability" name="package_availability" value="<?php echo esc_attr( $availability ); ?>" />
    <?php
}

function save_travel_package_meta( $post_id ) {
    if ( isset( $_POST['package_price'] ) ) {
        update_post_meta( $post_id, '_package_price', sanitize_text_field( $_POST['package_price'] ) );
    }

    if ( isset( $_POST['package_availability'] ) ) {
        update_post_meta( $post_id, '_package_availability', sanitize_text_field( $_POST['package_availability'] ) );
    }
}
add_action( 'save_post', 'save_travel_package_meta' );

// Add Styles and Scripts
function travel_packages_enqueue_styles() {
    wp_enqueue_style( 'travel-packages-style', plugins_url( 'assets/style.css', __FILE__ ) );

    // Ajax Filter Script
    wp_enqueue_script( 'travel-packages-ajax', plugins_url( 'assets/filter.js', __FILE__ ), array( 'jquery' ), null, true );
    wp_localize_script( 'travel-packages-ajax', 'ajax_params', array(
        'ajax_url' => admin_url( 'admin-ajax.php' ),
    ) );
}
add_action( 'wp_enqueue_scripts', 'travel_packages_enqueue_styles' );



// Ajax Filter
function travel_packages_filter_ajax() {
    $city = isset( $_POST['city'] ) ? sanitize_text_field( $_POST['city'] ) : '';

    $args = array(
        'post_type' => 'travel_package',
        'posts_per_page' => -1,
    );

    if ( !empty( $city ) ) {
        $args['tax_query'] = array(
            array(
                'taxonomy' => 'city',
                'field'    => 'slug',
                'terms'    => $city,
            ),
        );
    }
	// Query
    $query = new WP_Query( $args );

    if ( $query->have_posts() ) :
        while ( $query->have_posts() ) : $query->the_post(); ?>
            <div class="package-item">
                <a href="<?php the_permalink(); ?>" class="package-link">
                    <div class="package-thumbnail"><?php the_post_thumbnail(); ?></div>
                    <h2><?php the_title(); ?></h2>
                </a>
                <div class="package-description"><?php the_excerpt(); ?></div>
                <a href="<?php the_permalink(); ?>" class="package-link-archive"><?php _e( 'View Details', 'text_domain' ); ?></a>
            </div>
        <?php endwhile;
    else :
        echo '<p>' . __( 'No travel packages found.', 'text_domain' ) . '</p>';
    endif;

    wp_die();
}
add_action( 'wp_ajax_filter_travel_packages', 'travel_packages_filter_ajax' );
add_action( 'wp_ajax_nopriv_filter_travel_packages', 'travel_packages_filter_ajax' );

// Load My templates
function travel_packages_template_include( $template ) {
    if ( is_post_type_archive( 'travel_package' ) ) {
        $new_template = plugin_dir_path( __FILE__ ) . 'templates/archive-travel_package.php';
        if ( file_exists( $new_template ) ) {
            return $new_template;
        }
    }
    
    if ( is_singular( 'travel_package' ) ) {
        $new_template = plugin_dir_path( __FILE__ ) . 'templates/single-travel_package.php';
        if ( file_exists( $new_template ) ) {
            return $new_template;
        }
    }

    return $template;
}
add_filter( 'template_include', 'travel_packages_template_include' );
