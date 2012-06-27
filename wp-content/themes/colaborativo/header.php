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
			  
			  <div class="span10" id="cat-menu">
			  	<ul class="acenter nav">
					<?php wp_list_categories('title_li=&hide_empty=0&exclude=15'); ?>
				</ul>
			  </div>
		    </div>
	    </div>
	  </div>
	</div>

	<div class="container">

		<div class="row" id="meta-colaborativo">
			<span class="ir sprite" id="beta">
				Estamos en beta
			</span>

			<a href="mailto:info@colaborativo.co" class="ir sprite" id="feedback">
				Bienvenido el feedback
			</a>
		</div>