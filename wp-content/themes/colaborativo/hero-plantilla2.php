<?php 
	$logo = kc_get_option('colasite_', 'front', 'logo');
	$logo = wp_get_attachment_image_src( $logo );
	$logo2 = kc_get_option('colasite_', 'front', 'logo2');
	$logo2 = wp_get_attachment_image_src( $logo2 , 'full' );
?>
<div class="hero-unit row" style="background-image:url('<?php echo $logo2[0]; ?>')">

	<div class="span12">

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

</div>

<div class="row" id="hero-footer">
	<h2><?php bloginfo( 'title' ); ?></h2>
	<h3><?php bloginfo( 'description' ); ?></h3>
</div>