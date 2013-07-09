<?php 
	$logo = kc_get_option('colasite_', 'front', 'logo');
	$logo = wp_get_attachment_image_src( $logo , 'full' );
	$logo2 = kc_get_option('colasite_', 'front', 'logo2');
	$logo2 = wp_get_attachment_image_src( $logo2 );
?>
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