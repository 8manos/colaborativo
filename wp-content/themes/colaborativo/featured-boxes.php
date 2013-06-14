<div id="featured" class="boxes row">
	<h3>Cubrimiento</h3>

	<?php
		$featured_query = new WP_Query(
										array(
											'posts_per_page' 	=> 3,
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
	<div id="featured_carousel" class="carousel slide">
		<!-- Carousel items -->
		<div class="carousel-inner">

		<?php
			$i = 1;

			echo '<div class="clearfix item active">';

			if ( $featured_query->have_posts() ) : while ( $featured_query->have_posts() ) : $featured_query->the_post();

				display_article();

			    if($i % 3 == 0) {echo '</div><div class="clearfix item">';}

			$i++; endwhile; endif;

			echo '</div>';
		?>	

		</div>
		<!-- Carousel nav -->
		<a class="carousel-control left" href="#featured_carousel" data-slide="prev">&lsaquo;</a>
		<a class="carousel-control right" href="#featured_carousel" data-slide="next">&rsaquo;</a>
	</div>
</div>