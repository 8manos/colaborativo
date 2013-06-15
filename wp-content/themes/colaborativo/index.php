<?php
	get_header();
	$blog_id = $current_site->blog_id;
	$logo = kc_get_option('colasite_', 'front', 'logo');
	$logo = wp_get_attachment_image_src( $logo[selected][0] );
	$hashtags = kc_get_option('colasite_', 'front', 'hashtags');
	$elink = kc_get_option('colasite_', 'front', 'evento-link');
	$ocultar_cubrimiento = kc_get_option('colasite_', 'front', 'oculta_cubrimiento');
?>

<ul class="row" id="top-banners">
<?php
	dynamic_sidebar( 'banners-top' );
?>
</ul>

<?php // if( is_home() || is_front_page() ){ ?>
<div class="hero-unit row">

	<div class="span8">
		<div id="event-logo">

			<?php if( $elink ){ ?>
			<a class="left" href="<?php echo $elink; ?>" target="_blank">
			<?php }else{ ?>
			<a class="left" href="<?php bloginfo('url'); ?>">
			<?php } ?>
				<?php if( $logo ) { ?>
					<img class="alignleft" src="<?php echo $logo[0];  ?>" />
				<?php } ?>
			</a>

			<h2><?php bloginfo( 'title' ); ?></h2>
			<h3><?php bloginfo( 'description' ); ?></h3>
		</div>
	</div>

	<div class="span3">
		<form action="<?php bloginfo('url'); ?>" class="inline-form" method="get">
			<input class="input-medium search-query" id="s" name="s" placeholder="<?php _e('Buscar','colaborativo'); ?>" type="text" />
		</form>
	</div>

</div>
<?php // } ?>

<div class="navbar row" id="subheader-nav">
	<ul class="acenter nav" id="cat-menu">
		<li><a href="<?php bloginfo('url'); ?>" title="<?php bloginfo('title'); ?>">Inicio</a></li>
		<?php wp_list_categories( 'title_li=&hide_empty=0&exclude=15' ); ?>
	</ul>
</div>


<?php
	if( $hashtags ){
?>

	<div class="row" id="hashtags-container">
		<p class="hashtags span12">


					<strong>#HASHTAGS PARA CUBRIMIENTO:</strong> <?php echo ( $hashtags ); ?>
			
		</p>
	</div>

<?php } ?>

<?php 
	if( !$ocultar_cubrimiento[0] && !is_search() ){  
		get_template_part( 'featured', 'boxes' );
	} 
?>

<div class="row colaborated">
	<h3 class="span12">Contenido generado por los asistentes</h3>
</div>

<div class="subnav row" id="filters">
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

	</div>
</div>

<div id="counter_wrapper">
	<span  id="counter_label">Actualización en</span>
	<span  id="ajax_counter">3:00</span>
</div>

<div id="notify_new">-</div>
<?php
	global $wp_query;
	$args = array_merge( $wp_query->query, array(
		'posts_per_page' => 20 ,
		'post_status' => 'publish',
		'post__not_in' => get_option( 'sticky_posts' )
	) );//escondemos 10 y mostramos 10

	query_posts( $args );

	$hidden_num = $wp_query->post_count - 10;
	if ($hidden_num > 0){//deben existir mas de 10 para que se muestre el div de los escondidos
		echo '<div id="hidden_articles">';
	}else{
		echo '<section class="row boxes" id="timeline">';
	}

	$i = 1;
	while (have_posts()) : the_post();

		display_article();

		if ($i == $hidden_num){//cuando llega al ultimo articulo escondido
			echo '</div>';
			echo '<section class="row boxes" id="timeline">';
		}

		$i++;
	endwhile;

	// Reset Query
	wp_reset_query();
?>

</section>

<div class="paging acenter row">
	<?php colaborativo_content_nav(); ?>

	<?php
	global $wp_query;
	$current_cat = $wp_query->queried_object_id;
	$current_type = $wp_query->query['post_type'];
	?>
	<a href="#" class="" id="load-more" data-type="<? echo $current_type; ?>" data-cat="<? echo $current_cat; ?>" data-autoupdate="<?php getAutoUpdate(); ?>">
		<?php _e('Cargar más contenidos','colaborativo'); ?>
	</a>
</div>

<ul class="row" id="bottom-banners">
<?php 
	dynamic_sidebar( 'banners-bottom' );
?>
</ul>

<?php get_footer(); ?>
