<?php get_header(); ?>

<?php if( is_home() || is_front_page() ){ ?>
<div class="hero-unit">
	<div class="row">
		<div class="span12">
			<div id="event-logo">
				<a class="left" href="http://www.campus-party.com.co/" target="_blank"><img class="alignleft" src="<?php bloginfo('stylesheet_directory') ?>/img/campus-party-feed.png" /></a>
				<h2><?php bloginfo( 'title' ); ?></h2>
				<h3><?php bloginfo( 'description' ); ?></h3>
			</div>
			<!-- <p class="hashtags">
				<strong>Aporta contenidos usando los #HASHTAGS</strong><br />
				#CPCO5 | #SOMETHINGBETTER | #CPCO5BLOG | #CPCO5CREA | #CPCO5MOD | #CPCO5DEV | #CPCO5OCIO
			</p> -->
		</div>
	</div>
</div>
<?php } ?>
<div class="navbar">
	<ul class="acenter nav" id="cat-menu">
		<?php wp_list_categories( 'title_li=&hide_empty=0&exclude=15' ); ?>
	</ul>
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
			array_push($post_types, 'post');
			array_reverse($post_types);
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

<div id="counter_wrapper">
	<span  id="counter_label">Actualización en</span>
	<span  id="ajax_counter">3:00</span>
</div>

<div id="notify_new">-</div>
<?php
	global $wp_query;
	$args = array_merge( $wp_query->query, array( 'posts_per_page' => 20 ) );//esondemos 6 y mostramos 10
	query_posts( $args );

	$hidden_num = $wp_query->post_count - 10;
	if ($hidden_num > 0){//deben existir mas de 10 para que se muestre el div de los escondidos
		echo '<div id="hidden_articles">';
	}else{
		echo '<section class="row" id="timeline">';
	}

	$i = 1;
	while (have_posts()) : the_post();

		display_article();

		if ($i == $hidden_num){//cuando llega al ultimo articulo escondido
			echo '</div>';
			echo '<section class="row" id="timeline">';
		}

		$i++;
	endwhile;

	// Reset Query
	wp_reset_query();
?>

</section>

<div class="paging acenter">
	<?php colaborativo_content_nav(); ?>

	<?php
	global $wp_query;
	$current_cat = $wp_query->queried_object_id;
	$current_type = $wp_query->query['post_type'];
	?>
	<a href="#" class="" id="load-more" data-type="<? echo $current_type; ?>" data-cat="<? echo $current_cat; ?>">
		<?php _e('Cargar más contenidos','colaborativo'); ?>
	</a>
</div>
<?php get_footer(); ?>
