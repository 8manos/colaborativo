<?php 
	$is_ajax = $_GET['ajax'];
	
	if( !$is_ajax ){
		get_header(); 
	}

	while (have_posts()) : the_post();

		$post_type = get_post_type();

		get_template_part('content', $post_type);

	endwhile;

	if( !$is_ajax ){
		get_footer(); 
	}
?>