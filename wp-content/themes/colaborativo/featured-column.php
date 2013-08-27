<div id="featured" class="boxes row">
	<h3>Artículos</h3>

	<?php
		$featured_query = new WP_Query(
										array(
											'posts_per_page' 	=> 6,
											'order'				=> 'ASC',
											'post_status' 		=> 'publish',
											'post__in'			=> get_option( 'sticky_posts' ),
											'post_type' 		=> array(
																	'post',
																	'video',
																	'imagen',
																	'tweet',
																	'sonido',
																	'descarga'
																)
										)
									);
	?>
	<!-- Carousel items -->
	<?php
		$i = 1;

		echo '<div class="clearfix">';

		if ( $featured_query->have_posts() ) : while ( $featured_query->have_posts() ) : $featured_query->the_post();

			if($i >= 6){ $i++; continue; }

			display_article();

		$i++; endwhile; endif;

		echo '</div>';
	?>	


</div>