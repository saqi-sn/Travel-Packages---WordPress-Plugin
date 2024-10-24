<?php get_header(); ?>
<div class="container">
    <h1 class="page-title"><?php _e( 'Travel Packages', 'text_domain' ); ?></h1>
    <!-- City filter -->
    <div class="filter-wrap">
        <label for="filter-city"><?php _e( 'Filter by City:', 'text_domain' ); ?></label>
        <select id="filter-city">
            <option value=""><?php _e( 'All Cities', 'text_domain' ); ?></option>
            <?php
            $cities = get_terms( array( 'taxonomy' => 'city', 'hide_empty' => true ) );
            if ( !empty( $cities ) && !is_wp_error( $cities ) ) {
                foreach ( $cities as $city ) {
                    echo '<option value="' . esc_attr( $city->slug ) . '">' . esc_html( $city->name ) . '</option>';
                }
            }
            ?>
        </select>
    </div>
    <!-- Packages -->
    <div class="travel-packages-list">
        <?php if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>
            <div class="package-item">
                <a href="<?php the_permalink(); ?>" class="package-link">
                    <div class="package-thumbnail"><?php the_post_thumbnail(); ?></div>
                    <h2><?php the_title(); ?></h2>
                </a>
                <div class="package-description"><?php the_excerpt(); ?></div>
                <a href="<?php the_permalink(); ?>" class="package-link-archive"><?php _e( 'View Details', 'text_domain' ); ?></a>
            </div>
        <?php endwhile; else: ?>
            <p><?php _e( 'No travel packages found.', 'text_domain' ); ?></p>
        <?php endif; ?>
    </div>
<?php get_footer(); ?>
