<?php
/**
 * The template used for displaying page content in page.php
 *
 * @package WordPress
 * @subpackage Boss
 * @since Boss 1.0.0
 */
?>

	<article id="post-<?php the_ID(); ?>" <?php post_class(); ?> style="padding-top: 0;">
		<div class="entry-content" >
			<?php the_content(); ?>
			<?php wp_link_pages( array( 'before' => '<div class="page-links">' . __( 'Pages:', 'boss' ), 'after' => '</div>' ) ); ?>
		</div><!-- .entry-content -->

		<footer class="entry-meta">
			<?php edit_post_link( __( 'Edit', 'boss' ), '<span class="edit-link">', '</span>' ); ?>
		</footer><!-- .entry-meta -->

	</article><!-- #post -->
