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
			  <button class="btn">1</button>
			  <button class="btn">2</button>
			  <button class="btn">3</button>
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

$params = array( 
	'post_type' => array( 'post', 'imagen', 'video', 'sonido', 'tweet', 'descarga'),
	'posts_per_page' => "20",
	// 'cat' => "-6",
	'paged' => $paged
);

$items = new WP_Query($params); ?>


<?php 
	$i = 1;
	while ($items->have_posts()) : $items->the_post(); 
		display_article();
		$i++;
	endwhile; 
?>

</section>

<div class="paging acenter">
	<a href="#" class="btn btn-primary btn-large">
		<?php _e('Cargar más contenidos','colaborativo'); ?>
	</a>
</div>
<?php get_footer(); ?>