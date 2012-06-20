<?php get_header(); ?>
<div class="hero-unit">
	<div class="row">
		<div class="span6">
			<h1>Hello, world!</h1>
			<h2>This is a template for event covering and real-time social network agregation with a cool style.</h2>
		</div>
		<div class="span4 aright">
			Event icon
		</div>
	</div>
</div>

<div class="subnav" id="filters">
	<div class="row">
		<div class="span3">
			<h3>Filtre el contenido por:</h3>
		</div>
		
		<div class="span6">
			<div class="btn-group" id="filters-buttons">
			<?php 
			$args=array(
			  'public'   => true,
			  '_builtin' => false
			); 
			$output = 'names'; // names or objects, note names is the default
			$operator = 'and'; // 'and' or 'or'
			$post_types=get_post_types($args,$output,$operator); 
				  foreach ($post_types  as $post_type ) {
				  	$link = add_query_arg( 'post_type', $post_type );
				    echo '<a class="ir sprite tipo-'. $post_type .'" href="'.$link.'" class="btn'. $post_type .'">'. $post_type. '</a>';
				  }
			?>
			</div>
		</div>

		<div class="span3 aright">
			<form action="<?php bloginfo('url'); ?>" class="inline-form" method="get">
				<input class="input-medium search-query" id="s" name="s" placeholder="<?php _e('Buscar','colaborativo'); ?>" type="text" />	
			</form>
		</div>
	</div>
</div>

<section class="row" id="timeline">

<?php 
	$i = 1;
	while (have_posts()) : the_post(); 
		display_article();
		$i++;
	endwhile; 
?>

</section>

<div class="paging acenter">
	<?php colaborativo_content_nav(); ?>
</div>
<?php get_footer(); ?>