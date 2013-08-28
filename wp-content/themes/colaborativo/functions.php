<?php // Colaborativo functions file

remove_action('wp_head', 'wp_generator');

/**
 * Setup the theme
 */
function setup_colaborativo(){
	add_theme_support( 'post-thumbnails' );
    add_image_size( 'article-thumb', 280, 280, true);
    add_image_size( 'article-large', 600, 8000, false);

    /*
     * This theme supports custom background color and image, and here
     * we also set up the default background color.
     */
    add_theme_support( 'custom-background', array(
        'default-color' => 'F6F6F6',
    ) );
}
add_action( 'after_setup_theme', 'setup_colaborativo' );

// enqueue scripts
function colaborativo_scripts_method() {

    $version = "0.8.2";

    wp_register_script('colaborativo_modernizr',get_template_directory_uri() . '/js/modernizr.js','', $version ,false);
    wp_enqueue_script( 'colaborativo_modernizr' );

    wp_register_script('colaborativo_plugins',get_template_directory_uri() . '/js/plugins.js','', $version ,true);
    wp_enqueue_script( 'colaborativo_plugins' );

    wp_register_script('colaborativo_app',get_template_directory_uri() . '/js/app.js','', $version ,true);
    wp_enqueue_script( 'colaborativo_app' );

    wp_enqueue_script( 'jquery' );

    /* Obtener personalizaciones de plantilla */
    $plantilla = kc_get_option('colasite_', 'front', 'plantilla');
    $fuente_titular = kc_get_option('colasite_', 'front', 'fuente-titular');
    $fuente_contenidos = kc_get_option('colasite_', 'front', 'fuente-contenidos');

    if( $plantilla == "plantilla0" || !$plantilla ){

        if( !$fuente_titular ){ $fuente_titular = "Oswald:400,700"; }
        if( !$fuente_contenidos ){ $fuente_contenidos = "Open+Sans:400,600"; }

    }elseif( $plantilla == "plantilla1" ){

        if( !$fuente_titular ){ $fuente_titular = "Museo500"; }
        if( !$fuente_contenidos ){ $fuente_contenidos = "Museo300"; }  
             
    }

    if( $fuente_titular || $fuente_contenidos ){

        if( $fuente_titular ){
            if( $fuente_titular == "Museo500"){
                wp_enqueue_style( "colaborativo_fuentes_museo500", get_template_directory_uri() . '/font/museo500.css', false, null, 'all' ); 
            }else{
                $fuentes_google = $fuente_titular;
            }
        }

        if( $fuente_contenidos == "Museo300" ){
            wp_enqueue_style( "colaborativo_fuentes_museo300", get_template_directory_uri() . '/font/museo300.css', false, null, 'all' ); 
        }elseif( $fuente_contenidos && $fuentes_google ){
            $fuentes_google .= "|".$fuente_contenidos;
        }elseif( $fuente_contenidos ){
            $fuentes_google = $fuente_contenidos;
        }

        if( $fuentes_google ){
            wp_enqueue_style( "colaborativo_fuentes_google", "http://fonts.googleapis.com/css?family=$fuentes_google", false, null, 'all' );
        }

    }
}

add_action('wp_enqueue_scripts', 'colaborativo_scripts_method');

function colaborativo_print_fonts() {
    /* Obtener personalizaciones de plantilla */
    $plantilla = kc_get_option('colasite_', 'front', 'plantilla');
    $fuente_titular = kc_get_option('colasite_', 'front', 'fuente-titular');
    $fuente_contenidos = kc_get_option('colasite_', 'front', 'fuente-contenidos');

    if( $plantilla == "plantilla1" ){

        if( !$fuente_titular ){ $fuente_titular = "Museo500"; }
        if( !$fuente_contenidos ){ $fuente_contenidos = "Museo300"; }  
             
    }

    /* Selectores para cada fuente */
    $titulares = "
        body.page .type-page h2 ,
        body.page .type-page .page-sidebar,
        footer .nav ,
        footer .nav li ,
        #cat-menu ,
        #featured h3 ,
        #filters h3 ,
        #filters-buttons a ,
        #hero-header h1 ,
        #load-more ,
        .boxes article h2 ,
        .colaborated ,
        .colaborated h3 ,
        .hero-unit h3 ,
        .hero-unit p.hashtags strong ,
        .hero-unit #event-logo ,
        .navbar-fixed-top ,
        .wpcf7-submit ";

    $contenidos ="
        body ,
        .boxes article.type-tweet h2 ,
        .single .entry-content h2, 
        .modal-body .entry-content h2 ";

    if( $fuente_titular || $fuente_contenidos ){
        $output_css = "<style> ";

        if( $fuente_titular ){
            if( $fuente_titular == "Museo500"){
                $titular_family = "Museo500Regular";
            }else{
                $titular_family = restore_font_name( $fuente_titular );
            }
        }

        if( $fuente_contenidos == "Museo300" ){
            $contenidos_family = "Museo300Regular";
        }elseif( $fuente_contenidos ){
            $contenidos_family = restore_font_name( $fuente_contenidos );
        }

        if( $titular_family ){
            $output_css .= "$titulares { font-family: '$titular_family'; } ";
        }

        if( $contenidos_family ){
            $output_css .= "$contenidos { font-family: '$contenidos_family'; } ";
        }

        $output_css .= " </style>";
        echo $output_css;
    }
}

add_action('wp_head', 'colaborativo_print_fonts');

/*
| -------------------------------------------------------------------
| Registering Top Navigation Bar
| -------------------------------------------------------------------
| Adds custom menu with wp_page_menu fallback
| */

if ( function_exists( 'register_nav_menu' ) ) {
register_nav_menu( 'main-menu', 'Main Menu' );
}


/**
 * Get our wp_nav_menu() fallback, wp_page_menu(), to show a home link.
 */
function bootstrapwp_page_menu_args( $args ) {
  $args['show_home'] = true;
  return $args;
}
add_filter( 'wp_page_menu_args', 'bootstrapwp_page_menu_args' );

/**
 * Custom Walker to change submenu class items from default "sub-menu" to ""
 */
class Bootstrapwp_Walker_Nav_Menu extends Walker_Nav_Menu {
  function start_lvl(&$output, $depth) {
    $indent = str_repeat("\t", $depth);
    $output .= "\n$indent<ul class=\"dropdown-menu\">\n";
  }
}

/**
 * Register post types and flush rewrite rules on theme activation
 */
function colaborativo_post_types() {
  $labels_img = array(
    'name' => __('Imagenes ', 'post type general name'),
    'singular_name' => _x('Imagen ', 'post type singular name'),
    'add_new' => _x('Agregar nueva', 'book'),
    'add_new_item' => __('Agregar nueva imagen'),
    'edit_item' => __('Editar imagen'),
    'new_item' => __('Nueva imagen'),
    'all_items' => __('Todas las Imagenes'),
    'view_item' => __('Ver imagen'),
    'search_items' => __('Buscar imagenes'),
    'not_found' =>  __('No se encontraron imagenes'),
    'not_found_in_trash' => __('No hay imagenes en la basura'),
    'parent_item_colon' => '',
    'menu_name' => 'Imagenes'
  );

  $args_img = array(
    'labels' => $labels_img,
    'public' => true,
    'publicly_queryable' => true,
    'show_ui' => true,
    'show_in_menu' => true,
    'query_var' => true,
    'rewrite' => true,
    'capability_type' => 'post',
    'has_archive' => true,
    'hierarchical' => false,
    'menu_position' => 5,
    'supports' => array( 'title', 'editor', 'author', 'thumbnail', 'excerpt', 'comments', 'custom-fields' ),
    'taxonomies' => array( 'category' )
  );

  $labels_video = array(
    'name' => __('Videos', 'post type general name'),
    'singular_name' => _x('Video', 'post type singular name'),
    'add_new' => _x('Agregar nuevo', 'book'),
    'add_new_item' => __('Agregar nuevo video'),
    'edit_item' => __('Editar video'),
    'new_item' => __('Nuevo video'),
    'all_items' => __('Todos los Videos'),
    'view_item' => __('Ver video'),
    'search_items' => __('Buscar Videos'),
    'not_found' =>  __('No se encontraron Videos'),
    'not_found_in_trash' => __('No hay Videos en la basura'),
    'parent_item_colon' => '',
    'menu_name' => 'Videos'
  );

  $args_video = array(
    'labels' => $labels_video,
    'public' => true,
    'publicly_queryable' => true,
    'show_ui' => true,
    'show_in_menu' => true,
    'query_var' => true,
    'rewrite' => true,
    'capability_type' => 'post',
    'has_archive' => true,
    'hierarchical' => false,
    'menu_position' => 6,
    'supports' => array( 'title', 'editor', 'author', 'thumbnail', 'excerpt', 'comments', 'custom-fields' ),
    'taxonomies' => array( 'category' )
  );

  $labels_tweet = array(
    'name' => _x('Tweets', 'post type general name'),
    'singular_name' => _x('tweet', 'post type singular name'),
    'add_new' => _x('Agregar nuevo', 'book'),
    'add_new_item' => __('Agregar nuevo tweet'),
    'edit_item' => __('Editar tweet'),
    'new_item' => __('Nuevo tweet'),
    'all_items' => __('Todos los Tweets'),
    'view_item' => __('Ver tweet'),
    'search_items' => __('Buscar Tweets'),
    'not_found' =>  __('No se encontraron Tweets'),
    'not_found_in_trash' => __('No hay Tweets en la basura'),
    'parent_item_colon' => '',
    'menu_name' => 'Tweets'
  );

  $args_tweet = array(
    'labels' => $labels_tweet,
    'public' => true,
    'publicly_queryable' => true,
    'show_ui' => true,
    'show_in_menu' => true,
    'query_var' => true,
    'rewrite' => true,
    'capability_type' => 'post',
    'has_archive' => true,
    'hierarchical' => false,
    'menu_position' => 7,
    'supports' => array( 'title', 'author', 'comments', 'custom-fields' ),
    'taxonomies' => array( 'category' )
  );

  $labels_sonido = array(
    'name' => _x('Sonidos', 'post type general name'),
    'singular_name' => _x('sonido', 'post type singular name'),
    'add_new' => _x('Agregar nuevo', 'book'),
    'add_new_item' => __('Agregar nuevo sonido'),
    'edit_item' => __('Editar sonido'),
    'new_item' => __('Nuevo sonido'),
    'all_items' => __('Todos los Sonidos'),
    'view_item' => __('Ver sonido'),
    'search_items' => __('Buscar Sonidos'),
    'not_found' =>  __('No se encontraron Sonidos'),
    'not_found_in_trash' => __('No hay Sonidos en la basura'),
    'parent_item_colon' => '',
    'menu_name' => 'Sonidos'
  );

  $args_sonido = array(
    'labels' => $labels_sonido,
    'public' => true,
    'publicly_queryable' => true,
    'show_ui' => true,
    'show_in_menu' => true,
    'query_var' => true,
    'rewrite' => true,
    'capability_type' => 'post',
    'has_archive' => true,
    'hierarchical' => false,
    'menu_position' => 8,
    'supports' => array( 'title', 'author', 'excerpt', 'comments', 'custom-fields' ),
    'taxonomies' => array( 'category' )
  );

  $labels_descarga = array(
    'name' => _x('Descargas', 'post type general name'),
    'singular_name' => _x('descarga', 'post type singular name'),
    'add_new' => _x('Agregar nuevo', 'book'),
    'add_new_item' => __('Agregar nuevo descarga'),
    'edit_item' => __('Editar descarga'),
    'new_item' => __('Nuevo descarga'),
    'all_items' => __('Todos los Descargas'),
    'view_item' => __('Ver descarga'),
    'search_items' => __('Buscar Descargas'),
    'not_found' =>  __('No se encontraron Descargas'),
    'not_found_in_trash' => __('No hay Descargas en la basura'),
    'parent_item_colon' => '',
    'menu_name' => 'Descargas'
  );

  $args_descarga = array(
    'labels' => $labels_descarga,
    'public' => true,
    'publicly_queryable' => true,
    'show_ui' => true,
    'show_in_menu' => true,
    'query_var' => true,
    'rewrite' => true,
    'capability_type' => 'post',
    'has_archive' => true,
    'hierarchical' => false,
    'menu_position' => 8,
    'supports' => array( 'title', 'author', 'excerpt', 'comments', 'custom-fields' ),
    'taxonomies' => array( 'category' )
  );

  $labels_facebook = array(
    'name' => _x('FB Post', 'post type general name'),
    'singular_name' => _x('fb post', 'post type singular name'),
    'add_new' => _x('Agregar nuevo', 'fbpost'),
    'add_new_item' => __('Agregar nuevo FB Post'),
    'edit_item' => __('Editar FB Post'),
    'new_item' => __('Nuevo FB post'),
    'all_items' => __('Todos los FB posts'),
    'view_item' => __('Ver FB Post'),
    'search_items' => __('Buscar FB Post'),
    'not_found' =>  __('No se encontraron FB posts'),
    'not_found_in_trash' => __('No hay FB posts en la basura'),
    'parent_item_colon' => '',
    'menu_name' => 'FB Posts'
  );

  $args_facebook = array(
    'labels' => $labels_facebook,
    'public' => true,
    'publicly_queryable' => true,
    'show_ui' => true,
    'show_in_menu' => true,
    'query_var' => true,
    'rewrite' => true,
    'capability_type' => 'post',
    'has_archive' => true,
    'hierarchical' => false,
    'menu_position' => 8,
    'supports' => array( 'title', 'editor', 'author', 'excerpt', 'comments', 'custom-fields', 'thumbnail' ),
    'taxonomies' => array( 'category' )
  );

  $labels_galeria = array(
    'name' => _x('Galerias', 'post type general name'),
    'singular_name' => _x('galeria', 'post type singular name'),
    'add_new' => _x('Agregar nuevo', 'book'),
    'add_new_item' => __('Agregar nuevo galeria'),
    'edit_item' => __('Editar galeria'),
    'new_item' => __('Nuevo galeria'),
    'all_items' => __('Todos los Galerias'),
    'view_item' => __('Ver galeria'),
    'search_items' => __('Buscar Galerias'),
    'not_found' =>  __('No se encontraron Galerias'),
    'not_found_in_trash' => __('No hay Galerias en la basura'),
    'parent_item_colon' => '',
    'menu_name' => 'Galerias'
  );

  $args_galeria = array(
    'labels' => $labels_galeria,
    'public' => true,
    'publicly_queryable' => true,
    'show_ui' => true,
    'show_in_menu' => true,
    'query_var' => true,
    'rewrite' => true,
    'capability_type' => 'post',
    'has_archive' => true,
    'hierarchical' => false,
    'menu_position' => 8,
    'supports' => array( 'title', 'editor', 'author', 'excerpt', 'comments', 'custom-fields', 'thumbnail' ),
    'taxonomies' => array( 'category' )
  );

  register_post_type( 'imagen', $args_img );
  register_post_type( 'video', $args_video );
  register_post_type( 'tweet', $args_tweet );
  register_post_type( 'sonido', $args_sonido );
  register_post_type( 'descarga', $args_descarga );
  register_post_type( 'facebook', $args_facebook );
  register_post_type( 'galeria', $args_galeria );

}
add_action( 'init', 'colaborativo_post_types' );

function colaborativo_rewrite_flush() {
    flush_rewrite_rules();
}
add_action( 'after_switch_theme', 'colaborativo_rewrite_flush' );

register_sidebar(array(
    'id'            => 'banners-top',
    'name'          => 'banners top',
    'before_widget' => '<aside id="%1$s" class="widget %2$s">'."\n",
    'after_widget'  => '</aside>'."\n",
    'before_title'  => '<!-- <h3 class="widget-title">',
    'after_title'   => '</h3> -->'
));

register_sidebar(array(
    'id'            => 'banners-bottom',
    'name'          => 'banners bottom',
    'before_widget' => '<aside id="%1$s" class="widget %2$s">'."\n",
    'after_widget'  => '</aside>'."\n",
    'before_title'  => '<!-- <h3 class="widget-title">',
    'after_title'   => '</h3> -->'
));

function theme_settings( $groups ) {
    $blog_id = $current_site->blog_id;

    $sections = array();

    if ( $blog_id == 1 ) {
        /* $sections[] = array(
            'id'     => 'general',
            'title'  => __('General', 'cola'),
            'fields' => array(
                array(
                    'id'    => 'text_footer',
                    'title' => __('Footer text', 'cola'),
                    'type'  => 'textarea'
                )
            )
        ); */
    }
    else {
        /* $sections[] = array(
            'id'     => 'front',
            'title'  => __('Front page', 'cola'),
            'fields' => array(
                array(
                    'id'    => 'video-url',
                    'title' => __('Video URL', 'cola'),
                    'type'  => 'text'
                )
            )
        ); */
    }

    $sections[] = array(
        'id'     => 'front',
        'title'  => __('Settings de plantilla', 'cola'),
        'fields' => array(
            array(
                'id'    => 'logo',
                'desc'  => 'Sube el logo del evento',
                'title' => __('Logo de evento', 'cola'),
                'type'  => 'file',
                'mode'  => 'single'
            ),
            array(
                'id'    => 'evento-link',
                'desc'  => '(formato: http://link.com)',
                'title' => __('Link del evento', 'cola'),
                'type'  => 'text'
            ),
            array(
                'id'    => 'logo2',
                'desc'  => 'Sube la imagen secundaria del evento',
                'title' => __('Imagen secundaria', 'cola'),
                'type'  => 'file',
                'mode'  => 'single'
            ),
            array(
                'id'    => 'evento-linke',
                'desc'  => '(formato: http://link.com)',
                'title' => __('Link secundario', 'cola'),
                'type'  => 'text'
            ),
            array(
                'id'    => 'hashtags',
                'desc'  => 'Escribe aca los hashtags que se mostrarán en la descripción del evento',
                'title' => __('Hashtags para cubrimiento', 'cola'),
                'type'  => 'text'
            ),
            array(
                'id'      => 'plantilla',
                'title'   => 'Plantilla a usar',
                'desc'    => 'Escoje el tipo de layout que deseas',
                'type'    => 'select',
                'options' => array(
                    'plantilla0' => 'Plantilla básica', // Default
                    'plantilla1' => 'Plantilla evento rock', // Rock al Parque 2013 (Virgin1)
                    'plantilla2' => 'Plantilla marca personal', // Colombia (Mariana Pajon)
                    'plantilla3' => 'Plantilla 4 columnas',
                    'plantilla4' => 'Plantilla en desarrollo',
                    'plantilla5' => 'Plantilla en desarrollo',
                    'plantilla6' => 'Plantilla en desarrollo'
                ),
                'default' => 'plantilla0'
            ),
            array(
                'id'      => 'fuente-titular',
                'title'   => 'Fuente titular',
                'desc'    => 'Usada en menu de categorias, titulos, y similar',
                'type'    => 'select',
                'options' => array(
                    'Oswald:400,700' => 'Oswald',
                    'Happy+Monkey' => 'Happy Monkey',
                    'Pompiere' => 'Pompiere',
                    'Rambla:400,700' => 'Rambla',
                    'Antic+Slab' => 'Antic Slab',
                    'Museo500' => 'Museo',
                    'Open+Sans:400,700' => 'Open Sans',
                    'Raleway:400,700' => 'Raleway'
                ),
                'default' => 'Oswald:400,700'
            ),
            array(
                'id'      => 'fuente-contenidos',
                'title'   => 'Fuente contenidos',
                'desc'    => 'Usada en autores y contenidos',
                'type'    => 'select',
                'options' => array(
                    'Open+Sans:400,600' => 'Open Sans',
                    'Muli' => 'Muli',
                    'Quattrocento+Sans:400,700' => 'Quattrocento Sans',
                    'Noto+Sans:400,700' => 'Noto Sans',
                    'Karla:400,700' => 'Karla',
                    'Museo300' => 'Museo 300',
                    'Raleway:400,700' => 'Raleway'
                ),
                'default' => 'Open+Sans:400,600'
            ),
            array(
                'id'      => 'autoupdate',
                'desc'    => 'Selecciona para que los contenidos se actualicen automaticamente',
                'title'   => __('Autoupdate', 'cola'),
                'type'    => 'checkbox',
                'options' => array(
                    'true' => __('Automatico', 'cola'),
                ),
            ),
            array(
                'id'      => 'oculta_cubrimiento',
                'desc'    => 'Selecciona para ocultar la sección de cubrimiento (los post sticky quedarán ocultos)',
                'title'   => __('Ocultar Cubrimiento', 'cola'),
                'type'    => 'checkbox',
                'options' => array(
                    'true' => __('Ocultar', 'cola'),
                ),
            )
        )
    );

    if ( $sections ) {
        $groups[] = array(
            'prefix'        => "colasite_{$blog_id}",
            'menu_location' => 'themes.php',
            'menu_title'    => __('Theme settings', 'cola'),
            'page_title'    => __('Theme settings', 'cola'),
            'options'       => $sections
        );
    }

    return $groups;
}
add_filter( 'kc_plugin_settings', 'theme_settings' );

function restore_font_name( $font ){
    $parts = explode(":",$font); 
    if( $parts ){
        $font = $parts['0'];
    }

    $font = str_replace("+", " ", $font);
    return $font;
}

// get all of the images attached to the current post
function cl_get_images($size = 'thumbnail') {
    global $post;
    $thumb_ID = get_post_thumbnail_id( $post->ID );

    $photos = get_children( array('post_parent' => $post->ID, 'post_status' => 'inherit', 'post_type' => 'attachment', 'post_mime_type' => 'image', 'order' => 'ASC', 'orderby' => 'menu_order ID', 'exclude' => $thumb_ID) );

    $results = array();

    if ($photos) {
        foreach ($photos as $photo) {
            // get the correct image html for the selected size
            $results[] = wp_get_attachment_image($photo->ID, $size);
        }
    }

    return $results;
}

// get all of the images urls attached to the current post
function cl_get_images_src($size = 'thumbnail') {
    global $post;
    $thumb_ID = get_post_thumbnail_id( $post->ID );

    $photos = get_children( array('post_parent' => $post->ID, 'post_status' => 'inherit', 'post_type' => 'attachment', 'post_mime_type' => 'image', 'order' => 'ASC', 'orderby' => 'menu_order ID', 'exclude' => $thumb_ID) );

    $results = array();

    if ($photos) {
        foreach ($photos as $photo) {
            // get the correct image html for the selected size
            $results[] = wp_get_attachment_image_src($photo->ID, $size);
        }
    }

    return $results;
}


/*
 * Mostrar cada publicacion
 */
function display_article() {
    $plantilla = kc_get_option('colasite_', 'front', 'plantilla');

    if( $plantilla == 'plantilla3' ){
        $post_class = 'span3';
    }else{
        $post_class = 'span4';
    }
?>
	<article id="posted-<?php the_ID(); ?>" <?php post_class( $post_class ); ?> data-date="<?php the_time('Y-m-d H:i:s'); ?>" data-id="<?php the_ID(); ?>">
        <div class="article-content">
            <?php
                $link_evento = get_post_meta(get_the_ID(), $key = '_url-evento', $single = true);

                if( get_post_type() == "imagen" || get_post_type() == "facebook" ){
                $enclosure = get_post_meta(get_the_ID(), $key = 'enclosure', $single = true);
                $enclosure = apply_filters( 'the_title', $enclosure);
                $enclosure_array = explode('
', $enclosure);
            ?>


        		<?php if(has_post_thumbnail() || $enclosure){ ?>
        			<a class="thumbnail" href="<?php the_permalink(); ?>?ajax=true&width=940&height=90%" rel="prettyPhoto[<?php echo get_post_type() ?>]" title="<?php the_title(); ?>">
                        <?php
                            if(has_post_thumbnail()) {
                                the_post_thumbnail('article-thumb');
                            }else{
                        ?>
        				    <img width="280" height="280" src="http://colaborativo.co/wp-content/themes/colaborativo/img/timthumb.php?src=<?php echo($enclosure_array[0]); ?>&w=280&h=280" />
                        <?php } ?>
        			</a>
        		<?php } ?>
            <?php
                }elseif(get_post_type() == "video"){
                    $video_link = get_post_meta(get_the_ID(), $key = 'syndication_permalink', $single = true);
                    parse_str( parse_url( $video_link, PHP_URL_QUERY ), $video_vars );
                    $video_id = $video_vars['v'];
            ?>
                    <a class="thumbnail" href="<?php the_permalink(); ?>?ajax=true&width=940&height=90%" rel="prettyPhoto[<?php echo get_post_type() ?>]">
                        <img width="280" height="280" src="http://colaborativo.co/wp-content/themes/colaborativo/img/timthumb.php?src=http://img.youtube.com/vi/<?php echo $video_id ?>/hqdefault.jpg&w=280&h=280" />
                    </a>
            <?php }elseif(get_post_type() == "sonido"){ ?>
                    <a class="thumbnail" href="<?php the_permalink(); ?>?ajax=true&width=940&height=90%" rel="prettyPhoto[<?php echo get_post_type() ?>]">
                        <img width="281" height="144" src="<?php bloginfo('template_directory'); ?>/img/thumb-audio.png" />
                    </a>
            <?php }elseif(get_post_type() == "descarga"){ ?>
                    <a class="thumbnail" href="<?php the_permalink(); ?>?ajax=true&width=940&height=90%" rel="prettyPhoto[<?php echo get_post_type() ?>]">
                        <img width="281" height="144" src="<?php bloginfo('template_directory'); ?>/img/thumb-descarga.png" />
                    </a>
            <?php }elseif(get_post_type() == "tweet"){ ?>
                <h2><?php echo(make_clickable(get_the_title())); ?></h2>
            <?php }elseif(get_post_type() == "post" || get_post_type() == "galeria"){
                if(has_post_thumbnail()){ ?>
                    <a class="thumbnail" href="<?php the_permalink(); ?>?ajax=true&width=940&height=90%" rel="prettyPhoto[<?php echo get_post_type() ?>]">
                    <?php
                        the_post_thumbnail('article-thumb');
                    ?>
                    </a>
                <?php }
            } ?>

            <?php if( !$link_evento ) { ?>
                <a class="overlay" href="<?php the_permalink(); ?>" rel="prettyPhoto[<?php echo get_post_type() ?>]" ><?php _e('ver ', 'colaborativo'); echo get_post_type(); ?></a>
            <?php }else{ ?>
                <a class="overlay" href="<?php echo $link_evento; ?>"></a>
            <?php } ?>
        </div>

		<footer class="post-meta">
            <?php if(get_post_type() != "tweet"){ ?>
               <h2><?php the_title(); ?></h2>
            <?php } ?>
			<span class="autor has-icon"><?php _e('Por: ', 'colaborativo'); the_author(); ?></span>
			<span class="categoria">
                <?php the_category(); ?>
                <!-- <em class="right">
                    <?php the_time('Y-m-d H:i:s'); ?>
                </em> -->
            </span>
		</footer>

        <?php 
            if ( current_user_can( 'edit_post', get_the_ID() ) ) {
        ?>
            <div class="admin-tools">
                <a class="hide-button" data-hide="<?php the_ID(); ?>" href="#" id="hide-<?php the_ID(); ?>">Hide</a>
            </div>
        <?php
            }
        ?>

	</article>
<?php
}


/*
 * Mostrar cada publicacion sola
 */
function display_article_content() {

?>
    <article id="post-<?php the_ID(); ?>" <?php post_class('clearfix'); ?> data-date="<?php the_time('Y-m-d H:i:s'); ?>">
        <span class="categoria"><?php the_category(); ?></span>
        <h2 class="has-icon"><?php if(get_post_type() != "tweet"){ the_title(); } ?></h2>
        <div class="entry-content">
            <?php
                if(get_post_type(get_the_ID()) == "imagen" || get_post_type(get_the_ID()) == "facebook"){
                $enclosure = get_post_meta(get_the_ID(), $key = 'enclosure', $single = true);
                $enclosure = apply_filters( 'the_title', $enclosure);
                $enclosure_array = explode('
', $enclosure);
            ?>

                <?php if(has_post_thumbnail() || $enclosure){ ?>
                    <a href="<?php the_syndication_permalink(); ?>" target="_blank">
                        <?php
                            if(has_post_thumbnail()) {
                                the_post_thumbnail();
                            }else{
                        ?>
                            <img width="600" src="http://colaborativo.co/wp-content/themes/colaborativo/img/timthumb.php?src=<?php echo($enclosure_array[0]); ?>&w=600" />
                        <?php } ?>
                    </a>
                <?php } ?>
            <?php
                }elseif(get_post_type() == "video"){
                    $video_link = get_post_meta(get_the_ID(), $key = 'syndication_permalink', $single = true);
                    parse_str( parse_url( $video_link, PHP_URL_QUERY ), $video_vars );
                    $video_id = $video_vars['v'];
            ?>
                <div class="video-embed">
                    <iframe width="600" height="430" src="http://www.youtube.com/embed/<?php echo $video_id; ?>?wmode=opaque" frameborder="0" allowfullscreen></iframe>
                </div>
            <?php 
                }elseif(get_post_type() == "tweet"){ 

                    $media_content = get_post_meta( get_the_ID(), 'media_content', true );
            ?>
                    <h2><?php echo(make_clickable(get_the_title())); ?></h2>

                    <?php if( $media_content ){ ?>
                             <img width="600" src="http://colaborativo.co/wp-content/themes/colaborativo/img/timthumb.php?src=<?php echo($media_content); ?>&w=600" />
                    <?php } ?>

            <?php }elseif(get_post_type() == "post"){
                the_content();
            }elseif(get_post_type() == "galeria"){ ?>
                <div class="flexslider-container row">
                    <div class="flexslider">
                        <ul class="slides">
                        <?php
                                $photos = cl_get_images_src('article-large');
                                $photos_urls = cl_get_images_src('full');
                                //print_r($photos);
                                for ($i = 0; $i < count($photos); $i++) {
                        ?>
                                    <li>
                                        <img src="<?php echo($photos[$i][0]); ?>" />
                                    </li>

                        <?
                                }
                        ?>
                        </ul>
                    </div>
                </div>
            <?php
               the_content();
            }elseif(get_post_type() == 'sonido'){
                  $txt= $enclosure = get_post_meta(get_the_ID(), $key = 'enclosure', $single = true);

                  $re1='.*?';   # Non-greedy match on filler
                  $re2='(\\d+)';    # Integer Number 1

                  if ($c=preg_match_all ("/".$re1.$re2."/is", $txt, $matches))
                  {
                      $int1=$matches[1][0];
                      //print "$int1 \n";
            ?>
                <iframe width="100%" height="166" scrolling="no" frameborder="no" src="http://w.soundcloud.com/player/?url=http%3A%2F%2Fapi.soundcloud.com%2Ftracks%2F<?php echo $int1; ?>&amp;auto_play=false&amp;show_artwork=true&amp;color=ff7700"></iframe>
            <?php
                  }
            } ?>
        </div>

        <div class="entry-aside">
            <div class="autor clearfix">
                <?php if( get_post_type() == "tweet" ){
                    $enclosure = get_post_meta(get_the_ID(), $key = 'enclosure', $single = true);
                    $media_thumb = get_post_meta( get_the_ID(), 'media_thumbnail', true ); 
                    $enclosure = apply_filters( 'the_title', $enclosure);
                    $enclosure_array = explode('
', $enclosure);
                ?>
                    <div class="avatar right">
                        <?php if( $media_thumb ){ ?>
                            <a href="<?php the_syndication_permalink(); ?>" target="_blank"><img src="<?php echo $media_thumb; ?>" /></a>
                        <?php }elseif( $enclosure_array[0] ){ ?>
                            <a href="<?php the_syndication_permalink(); ?>" target="_blank"><img src="<?php echo $enclosure_array[0]; ?>" /></a>
                        <?php } ?>
                    </div>
                <?php } ?>
                <small>POR</small><br />
                <?php the_author(); ?>
            </div>
            <div class="social">
                <!-- AddThis Button BEGIN -->
                <div class="addthis_toolbox addthis_default_style ">
                    <a addthis:url="<?php bloginfo('url'); ?>/#ver-<?php echo get_the_ID(); ?>" addthis:title="<?php the_title(); ?>" class="addthis_button_facebook_like" fb:like:layout="button_count"></a><br />
                    <a addthis:url="<?php bloginfo('url'); ?>/#ver-<?php echo get_the_ID(); ?>" addthis:title="<?php the_title(); ?>" class="addthis_button_tweet"></a><br />
                    <a addthis:url="<?php bloginfo('url'); ?>/#ver-<?php echo get_the_ID(); ?>" addthis:title="<?php the_title(); ?>" class="addthis_button_google_plusone" g:plusone:size="medium"></a><br />
                    <a addthis:url="<?php bloginfo('url'); ?>/#ver-<?php echo get_the_ID(); ?>" addthis:title="<?php the_title(); ?>" class="addthis_counter addthis_pill_style"></a>
                </div>
                <!-- AddThis Button END -->
            </div>
        </div>
        <nav class="posts_nav">
            <?php 
                if ( function_exists( 'previous_post_link_plus' ) ) {
                    $prev = previous_post_link_plus( array(
                        'post_type' => ' "post","tweet","video","imagen","sonido","descarga","galeria" ',
                        'return' => 'object'
                    ) );
                    // print_r( $prev );
                    if( $prev ){
            ?>
                    <a class="prev" rel="postPhoto[<?php echo get_post_type( $prev->ID ) ?>]"  id="posted-<?php echo $prev->ID; ?>" href="<?php echo $prev->guid; ?>"><span><?php echo limit_text($prev->post_title, 7); ?></span></a>
            <?
                    }
                }

                if ( function_exists( 'next_post_link_plus' ) ) {
                    $next = next_post_link_plus( array(
                        'post_type' => ' "post","tweet","video","imagen","sonido","descarga","galeria" ',
                        'return' => 'object'
                    ) );
                    // print_r( $next );
                    if( $next ){
            ?>
                    <a class="next" rel="postPhoto[<?php echo get_post_type( $prev->ID ) ?>]"  id="posted-<?php echo $next->ID; ?>" href="<?php echo $next->guid; ?>"><span><?php echo limit_text($next->post_title, 7) ?></span></a>
            <?
                    }
                }
           ?>
        </nav>
    </article>
<?php
}

/*
 * Mostrar cada publicacion sola
 */
function display_article_content_ajax() {

    $cual = $_POST['cual'];

    $args = array(
                    'public' => true ,
                    '_builtin' => false
                );
    $output = 'names';
    $operator = 'and';

    $post_types = get_post_types( $args, $output, $operator );

    $post_types = array_merge( $post_types, array( 'post' ,'tweet' ) );

    $q = new WP_Query( 
                        array( 
                            'post_type' => $post_types, 
                            'post__in' => array( $cual )
                        ) 
                    );

    if($q->have_posts()){

        while ($q->have_posts()) : $q->the_post();

           display_article_content();
        
        endwhile;

    }else{
        echo "0";
    }     

    exit;

}

add_action('wp_ajax_contentajax', 'display_article_content_ajax');
add_action('wp_ajax_nopriv_contentajax', 'display_article_content_ajax');

function toRGB($Hex){

    if (substr($Hex,0,1) == "#")

            $Hex = substr($Hex,1);

    $R = substr($Hex,0,2);
    $G = substr($Hex,2,2);
    $B = substr($Hex,4,2);

    $R = hexdec($R);
    $G = hexdec($G);
    $B = hexdec($B);

    $RGB['R'] = $R;
    $RGB['G'] = $G;
    $RGB['B'] = $B;

    return $RGB;
}

function colores_cats() {
    $categorias = get_terms('category', array(
        'orderby'    => 'count',
        'hide_empty' => 0
     ));

    $colores = "<style>";

    foreach ($categorias as $categoria) {
        $color = get_metadata( 'term', $categoria->term_id, 'categoria-color', true );
        $color_rgb = toRGB($color);
        $colores .= "article.category-".$categoria->slug." .overlay { background-color:rgba(".$color_rgb['R']." ,".$color_rgb['G']." ,".$color_rgb['B']." , 0.75) !important; } ";
        $colores .= "article.category-".$categoria->slug." .categoria { background-color: ".$color." !important; } ";
        $colores .= ".single article.category-".$categoria->slug." .autor { background-color: ".$color."; } ";
        $colores .= ".modal-body article.category-".$categoria->slug." .autor { background-color: ".$color."; } ";
        $colores .= "li.cat-item-".$categoria->term_id." a:hover { color: ".$color." !important; } ";
        $colores .= "li.cat-item-".$categoria->term_id.".current-cat a { color: ".$color." !important; } ";
    }

    $colores .= "</style>";
    echo $colores;
}

if ( ! function_exists( 'ucc_pre_get_posts_filter' ) ) {
function ucc_pre_get_posts_filter( $query ) {
    if ( ! is_preview() && ! is_admin() && ! is_singular() && ! is_404() ) {
        if ( $query->is_feed ) {
            // As always, handle your feed post types here.
        } else {
            $my_post_type = get_query_var( 'post_type' );
            if ( empty( $my_post_type ) ) {
                $args = array(
                    'public' => true ,
                    '_builtin' => false
                );
                $output = 'names';
                $operator = 'and';

                // Get all custom post types automatically.
                $post_types = get_post_types( $args, $output, $operator );
                // Or uncomment and edit to explicitly state which post types you want. */
                // $post_types = array( 'event', 'location' );

                // Add 'link' and/or 'page' to array() if you want these included.
                // array( 'post' , 'link' , 'page' ), etc.
                $post_types = array_merge( $post_types, array( 'post' ,'tweet' ) );
                $query->set( 'post_type', $post_types );
            }
        }
    }
} }
add_action( 'pre_get_posts' , 'ucc_pre_get_posts_filter' );

/**
 * Display navigation to next/previous pages when applicable
 */
function colaborativo_content_nav( ) {
    global $wp_query;

    if ( $wp_query->max_num_pages > 1 ) : ?>
        <nav id="prev-next">
            <div class="nav-previous"><?php next_posts_link( __( '<span class="meta-nav">&larr;</span> Older posts', 'twentyeleven' ) ); ?></div>
            <div class="nav-next"><?php previous_posts_link( __( 'Newer posts <span class="meta-nav">&rarr;</span>', 'twentyeleven' ) ); ?></div>
        </nav><!-- #nav-above -->
    <?php endif;
}

/*
 * MAGIA AJAX
 */

function agregador_cajas(){

    if(isset($_POST['time'])){
        $cat = $_POST['cat'];       /* El hashtag */
        $operacion = $_POST['op'];  /* append o prepend, modifica mayor que o menor que el tiempo */
        $time = $_POST['time'];     /* El time del primer o ultimo item en el view del usuario */
        $type = $_POST['type'];     /* Tipos de post */
        $id_for_filter = $_POST['from'];
    }

    if($type){ /* Si tenemos un type en el request, else, todos los tipos. */
        $post_types = array( $type );
        if( $type == "imagen" ){
            $post_types = array_merge( $post_types , array( "galeria" ) );
        }
    }else{
        $post_types = get_post_types( array('public' => true, '_builtin' => false), 'names' );
        $post_types = array_merge( $post_types, array('post') );
    }

    //parametros para wp_query
    $params = array(
        'post_type' => $post_types,
        'ignore_sticky_posts' => 1,
        'post_status' => 'publish'
    );
    if($cat){
        $params['cat'] = $cat;
    }
    $params['posts_per_page'] = $operacion=='append' ? 13 : -1;
    if ( $id_for_filter ){
        $id_for_filter = $id_for_filter;
        $params['post__not_in'] = array($id_for_filter);
    }else{
        $id_for_filter = 0;
    }

    //para poder filtrar por el id se usa un filtro
    //el filtro necesita que le digamos el id y si queremos mas o menos
    global $time_for_filter, $op_for_filter, $id_for_filter;
    $time_for_filter = $time; 
    $op_for_filter = $operacion;

    add_filter( 'posts_where', 'filter_where' );
    //add_filter( 'posts_where', 'filter_since_id' );
    $q = new WP_Query($params);
    remove_filter( 'posts_where', 'filter_where' );
    //remove_filter( 'posts_where', 'filter_since_id' );

    $i = 1;

    if($q->have_posts()){

        while ($q->have_posts()) : $q->the_post();
            display_article();
        endwhile;

    }else{
        echo "0";
    } exit;
}

add_action('wp_ajax_agregarboxes', 'agregador_cajas');
add_action('wp_ajax_nopriv_agregarboxes', 'agregador_cajas');

function agregador_cajas_featured(){

    if(isset($_POST['offset'])){
        $offset = $_POST['offset'];     /* Tipos de post */
    }else{
        $offset = 0;
    }

    if($type){ /* Si tenemos un type en el request, else, todos los tipos. */
        $post_types = array( $type );
        if( $type == "imagen" ){
            $post_types = array_merge( $post_types , array( "galeria" ) );
        }
    }else{
        $post_types = get_post_types( array('public' => true, '_builtin' => false), 'names' );
        $post_types = array_merge( $post_types, array('post') );
    }

    //parametros para wp_query
    $params = array(
        'offset' => $offset,
        'post__in' => get_option( 'sticky_posts' ),
        'post_type' => $post_types,
        'post_status' => 'publish',
        'order' => 'ASC'
    );
    if($cat){
        $params['cat'] = $cat;
    }
    $params['posts_per_page'] = 5;

    //para poder filtrar por el id se usa un filtro
    //el filtro necesita que le digamos el id y si queremos mas o menos
    global $time_for_filter, $op_for_filter;
    $time_for_filter = $time;
    $op_for_filter = $operacion; 

    add_filter( 'posts_where', 'filter_where' );
    $q = new WP_Query($params);
    remove_filter( 'posts_where', 'filter_where' );

    $i = 1;

    if($q->have_posts()){

        while ($q->have_posts()) : $q->the_post();
            display_article();
        endwhile;

    }else{
        echo "0";
    } exit;
}

add_action('wp_ajax_agregarboxesfeatured', 'agregador_cajas_featured');
add_action('wp_ajax_nopriv_agregarboxesfeatured', 'agregador_cajas_featured');

function load_content_box(){

    if(isset($_POST['id'])){
        $cat = $_POST['id'];
    }

    $params['p'] = $id;

    $q = new WP_Query($params);
    if($q->have_posts()){

        while ($q->have_posts()) : $q->the_post();
            display_article();
        endwhile;

    }else{
        echo "0";
    } exit;
}

add_action('wp_ajax_agregarbox', 'load_content_box');
add_action('wp_ajax_nopriv_agregarbox', 'load_content_box');

// Admin helper
function hide_box( $id ){
    $id = $_POST['id'];
    
    if($id){
        if ( current_user_can('edit_post', $id) ) {
            $current_post = get_post( $id, 'ARRAY_A' );
            $current_post['post_status'] = 'draft';
            wp_update_post($current_post);

            echo "hidden";
        }else{
            _e( "No tienes permiso para ocultar este box", 'colabora' );
        }
    }else{
        echo "No post defined";
    }
    exit;
}

add_action('wp_ajax_hidebox', 'hide_box');
add_action('wp_ajax_nopriv_hidebox', 'hide_box');

function filter_where($where='')
{
    global $wpdb, $time_for_filter, $op_for_filter;

    $where .= " AND $wpdb->posts.post_date ";
    $where .= $op_for_filter=='append' ? "<= " : ">= ";
    $where .= "'$time_for_filter'";

    return $where;
}

function filter_since_id($where=''){
    global $wpdb;
    $where .= " AND ID > '$id_for_filter'";
    return $where;
}

function getAutoUpdate()
{
    $get_it = $_GET['autoupdate'];
    if ($get_it){
        echo $get_it;
    }else{
        $theme_au = kc_get_option('colasite_', 'front', 'autoupdate');
        if ($theme_au){
            echo 'true';
        }else{
            echo 'false';
        }
    }
}

function limit_text($text, $limit) {
    if (str_word_count($text, 0) > $limit) {
        $words = str_word_count($text, 2);
        $pos = array_keys($words);
        $text = substr($text, 0, $pos[$limit]) . '...';
    }
        return $text;
}

function span_tags( $string ) {
    global $wp_current_filter;
    $filter = end($wp_current_filter);
    $search = array('#span#', '#/span#');
    $replace = ( in_array($filter, array('wp_title', 'the_title_rss')) || ($filter == 'the_title' && (is_admin() || in_array('wp_head', $wp_current_filter))) ) ? '' : array('<span>', '</span>');
    $string = str_replace( $search, $replace, $string );

    return $string;
}

function clear_span_tags( $string ) {
    global $wp_current_filter;
    $filter = end($wp_current_filter);
    $search = array('#span#', '#/span#');
    $replace = ( in_array($filter, array('wp_title', 'the_title_rss')) || ($filter == 'the_title' && (is_admin() || in_array('wp_head', $wp_current_filter))) ) ? '' : array('', '<');
    $string = str_replace( $search, $replace, $string );

    return $string;
}