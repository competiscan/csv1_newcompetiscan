<?php
/**
 * Template Name: CMS Template
 * Description: Custom CMS Template using ACF Flexible Content
 */

get_header();
?>

<div class="cs-x1">
    <?php
    if ( have_posts() ) :
        while ( have_posts() ) :
            the_post();

            // Flexible Content Field
            if ( have_rows( 'cms_content' ) ) :

                while ( have_rows( 'cms_content' ) ) :
                    the_row();

                    $layout = get_row_layout();
                    $template = 'acf-layouts/' . $layout;

                    if ( locate_template( $template . '.php' ) ) {
                        get_template_part( $template );
                    } else {
                        ?>
                        <div class="layout-error">
                            <?php echo esc_html( 'Missing layout file: ' . $template . '.php' ); ?>
                        </div>
                        <?php
                    }

                endwhile;

            else :

                // Optional: Display page content if no ACF layouts exist.
                the_content();

            endif;

        endwhile;
    endif;
    ?>
</div>

<?php get_footer(); ?>