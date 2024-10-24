<?php get_header(); ?>
<div class="container">
    <div class="single-travel-package-container">
        <h1 class="package-title"><?php the_title(); ?></h1>
        <div class="package-photo"><?php the_post_thumbnail(); ?></div>
        <div class="divider"></div>
        <div class="package-content"><?php the_content(); ?></div>
        <div class="divider"></div>
        <div class="package-details">
            <p><strong><?php _e( 'Price:', 'text_domain' ); ?></strong> <?php echo esc_html( get_post_meta( get_the_ID(), '_package_price', true ) ); ?></p>
            <p><strong><?php _e( 'Availability:', 'text_domain' ); ?></strong> <?php echo esc_html( get_post_meta( get_the_ID(), '_package_availability', true ) ); ?></p>
        </div>
    </div>
</div>
<?php get_footer(); ?>
