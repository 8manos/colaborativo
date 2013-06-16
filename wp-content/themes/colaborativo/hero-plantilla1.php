<?php 
	$logo = kc_get_option('colasite_', 'front', 'logo');
	$logo = wp_get_attachment_image_src( $logo );
	$logo2 = kc_get_option('colasite_', 'front', 'logo2');
	$logo2 = wp_get_attachment_image_src( $logo2 );
?>
<div class="aright row" id="hero-header">
	<div class="span12">
		<h1 class="right"><?php bloginfo( 'title' ); ?></h1>
	</div>
</div>

<div class="hero-unit row">
	<div class="row-fluid">
		<div class="span3 acenter">
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

			</div>
		</div>

		<div class="acenter span6">
			<h3><?php bloginfo( 'description' ); ?></h3>
		</div>

		<div class="aright span3">
			<?php if( $logo2 ) { ?>
				<img class="alignleft" src="<?php echo $logo2[0];  ?>" />
			<?php } ?>
		</div>
	</div>

	<div id="plantilla1-search" class="row">
		<div class="acenter span12">
			<form action="<?php bloginfo('url'); ?>" class="inline-form" method="get">
				<input class="input-medium search-query" id="s" name="s" placeholder="<?php _e('Buscar','colaborativo'); ?>" type="text" />
			</form>
		</div>
	</div>

</div>