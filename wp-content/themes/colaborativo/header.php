<!DOCTYPE html>
<!--[if lt IE 7]> <html class="no-js lt-ie9 lt-ie8 lt-ie7" <?php language_attributes(); ?>> <![endif]-->
<!--[if IE 7]>    <html class="no-js lt-ie9 lt-ie8" <?php language_attributes(); ?>> <![endif]-->
<!--[if IE 8]>    <html class="no-js lt-ie9" <?php language_attributes(); ?>> <![endif]-->
<!--[if gt IE 8]><!--> <html class="no-js" <?php language_attributes(); ?>> <!--<![endif]-->
<head>
    <meta charset="<?php bloginfo('charset'); ?>">

    <title><?php wp_title(''); ?></title>

    <meta name="description" content="<?php bloginfo('description'); ?>">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- base styles -->
    <link rel="stylesheet" href="<?php bloginfo('template_directory'); ?>/css/bootstrap.css">

    <!-- app styles -->
    <link rel="stylesheet" href="<?php bloginfo('stylesheet_url'); ?>">
	<link href='http://fonts.googleapis.com/css?family=Oswald:400,700|Open+Sans:400,600' rel='stylesheet' type='text/css'>    

    <!--[if lt IE 9]>
      <script src="http://html5shim.googlecode.com/svn/trunk/html5.js"></script>
    <![endif]-->

    <?php 
    	wp_head(); 
    	colores_cats();
    ?>
</head>

<body <?php body_class(); ?>>
<!--[if lt IE 7]><p class="chromeframe">Your browser is <em>ancient!</em> <a href="http://browsehappy.com/">Upgrade to a different browser</a> or <a href="http://www.google.com/chromeframe/?redirect=true">install Google Chrome Frame</a> to experience this site.</p><![endif]-->

	<div class="navbar navbar-fixed-top">

	  <div class="navbar-inner">

	    <div class="container">

		    <div class="row-fluid">	

		      <div class="span2">
			      <a class="brand ir sprite" href="<?php bloginfo('url'); ?>" id="branding"><?php bloginfo('title'); ?></a>
			  </div>
			  
			  <div class="span10">

				<span class="ir sprite" id="beta">
					<?php _e('Estamos en beta', 'colaborativo'); ?>
				</span>

				<div class="aright right" id="menu-social">
					<label><?php _e('Síguenos', 'colaborativo'); ?></label>
					<a target="_blank" class="sprite ir facebook" href="https://www.facebook.com/pages/Colaborativoco/385044858220704">Facebook</a>
					<a target="_blank" class="sprite ir twitter" href="http://twitter.com/colaborativo_co">Twitter</a>
					<a target="_blank" class="sprite ir googleplus" href="https://plus.google.com/u/0/103569849344851752659/">Google+</a>
					<a target="_blank" class="sprite ir youtube" href="http://youtube.com/colaborativoco">YouTube</a>				
				</div>


				<div id="about-dropdown" class="dropdown">
					<a class="dropdown-toggle" data-toggle="dropdown" href="#">Acerca de</a>
					<ul class="dropdown-menu" role="menu" aria-labelledby="dLabel">
						<li><a href="#">Acerca</a></li>
						<li><a href="#">Acerca</a></li>
					</ul>
				</div>
				<a href="/contacto?ajax=true&width=940&height=90%" rel="prettyPhoto[feedback]" class="ir sprite right" id="feedback">
					<?php _e('Bienvenido el feedback', 'colaborativo'); ?>
				</a>

			  </div>

		    </div>

	    </div>

	  </div>

	</div>

	<div class="container">