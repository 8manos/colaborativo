<?php 
/*
 *	Template Name: Page-form
 */
?>
<?php get_header(); ?>
<?php

	while (have_posts()) : the_post();

		$post_type = get_post_type();

		get_template_part('content', $post_type);

	endwhile;
?>
<?php get_footer(); ?>