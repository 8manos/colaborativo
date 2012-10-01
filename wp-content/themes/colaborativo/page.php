<?php
	get_header();
	$blog_id = $current_site->blog_id;
	$logo = kc_get_option('colasite_', 'front', 'logo');
?>

<section>

<?php

	while (have_posts()) : the_post();

?>

	<div <?php post_class('row'); ?>>

		<div class="span4">
			<div class="page-sidebar">
				<ul>
					<li><a href="http://colaborativo.co/que-es/">Que es colaborativo</a></li>
					<li><a href="http://colaborativo.co/equipo/">Equipo</a></li>
					<li><a href="http://colaborativo.co/colabora/">Colabora</a></li>
				</ul>
			</div>
		</div>

		<div class="span8">
			<h2><?php the_title(); ?></h2>

			<div class="page-content">
				<?php the_content(); ?>
			</div>
		</div>

	</div>

<?php

	endwhile;

	// Reset Query
	wp_reset_query();
?>

</section>

<?php get_footer(); ?>