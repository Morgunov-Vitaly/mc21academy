<?php
/**
 * The Template for displaying all single posts.
 *
 * @package WordPress
 * @subpackage Boss
 * @since Boss 1.0.0
 */
get_header(); ?>
<?php $all_keys = get_post_custom_keys(); ?>
<?php if(is_array($all_keys) && ( in_array('_badgeos_points', get_post_custom_keys()) || in_array('_badgeos_hidden', get_post_custom_keys()) )) : ?>

    <?php if ( is_active_sidebar('sensei-default') || is_active_sidebar('learndash-default') ) : ?>
        <div class="page-right-sidebar">
    <?php else : ?>
        <div class="page-full-width">
    <?php endif; ?>
        <div id="primary" class="site-content single-badgeos">

            <div id="content" role="main">

            <?php while ( have_posts() ) : the_post(); ?>

                <article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
                    <div class="table">
                        <div class="badgeos-single-image table-cell">
                            <?php //echo badgeos_get_achievement_post_thumbnail( get_the_ID(), 'medium' ) ?>
                        <?php 
                            if ( has_post_thumbnail() ){ 
                                $id = get_post_thumbnail_id($post->ID, 'medium'); 
                                echo '<img src="' . wp_get_attachment_url($id) . '" />';
                            } 
                        ?>
                        </div>
                    </div>

                    <div class="entry-content">
                        <?php
                            /* translators: %s: Name of current post */
                            the_content( sprintf(
                                __( 'Continue reading %s <span class="meta-nav">&rarr;</span>', 'boss' ),
                                the_title( '<span class="screen-reader-text">"', '"</span>', false )
                            ) );
                        ?>

                        <?php
                            wp_link_pages( array(
                                'before' => '<div class="page-links">' . __( 'Pages:', 'boss' ),
                                'after'  => '</div>',
                            ) );
                        ?>
                    </div><!-- .entry-content -->

                    <footer class="entry-footer">
                    </footer><!-- .entry-footer -->
                </article><!-- #post-## -->

                <?php the_post_navigation(); ?>

                <?php
                    // If comments are open or we have at least one comment, load up the comment template
                    if ( comments_open() || get_comments_number() ) :
                        comments_template();
                    endif;
                ?>

            <?php endwhile; // end of the loop. ?>

            </div><!-- #main -->
        </div><!-- #primary -->
        <?php if ( is_active_sidebar('sensei-default') || is_active_sidebar('learndash-default') ) : ?>
            <!-- default WordPress sidebar -->
            <div id="secondary" class="widget-area" role="complementary">
                <?php 
                    if ( is_active_sidebar('sensei-default') ){
                        dynamic_sidebar( 'sensei-default' ); 
                    } else {
                        dynamic_sidebar( 'learndash-default' ); 
                    }
                ?>
            </div><!-- #secondary -->
        <?php endif; ?>
    </div>
<?php else: ?>
    <?php while ( have_posts() ) : the_post(); ?>

        <?php
        if ( has_post_thumbnail() && boss_get_option( 'boss_cover_blog' ) ) {
			$thumb	        = get_post_thumbnail_id();
			$image_url	    = buddyboss_resize( $thumb, '', 2.5, null, null, true );
			$style = 'style="background-image: url(' . $image_url . '); background-position: center;background-size: cover;background-repeat: none;" data-photo="yes"';
		}
        if ( is_active_sidebar( 'sidebar' ) ) :
            echo '<div class="page-right-sidebar">';
        else :
            echo '<div class="page-full-width">';
        endif;
        ?>

        <div id="primary" class="site-content">
            <div id="content" role="main"><!-- Yes it is mine page template -->
                <!--  <?php var_dump(get_post_format());  ?> -->
                <?php get_template_part( 'program-content', get_post_format() ); ?>

                <?php comments_template( '', true ); ?>

            </div><!-- #content -->
        </div><!-- #primary -->

        <?php
    endwhile;

    if ( is_active_sidebar( 'sidebar' ) ) :
        get_sidebar( 'sidebar' );
    endif;
    ?>
    </div><!-- page-right-sidebar/page-full-width -->
<?php endif; ?>

<?php get_footer();
