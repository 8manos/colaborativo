<?php 
	$redirect_url = get_bloginfo('url')."#post-".get_the_ID();
	wp_redirect( $redirect_url , $status = 302)
?>