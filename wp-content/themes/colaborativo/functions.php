<?php // Colaborativo functions file

remove_action('wp_head', 'wp_generator');

/**
 * Setup the theme
 */
function setup_colaborativo(){
	add_theme_support( 'post-thumbnails' );
    add_image_size( 'article-thumb', 280, 280, true);
    add_image_size( 'article-large', 600, 8000, false);
}
add_action( 'after_setup_theme', 'setup_colaborativo' );

// enqueue scripts
function colaborativo_scripts_method() {

    wp_register_script('colaborativo_modernizr',get_template_directory_uri() . '/js/modernizr.js','','',false);
    wp_enqueue_script( 'colaborativo_modernizr' );

    wp_register_script('colaborativo_plugins',get_template_directory_uri() . '/js/plugins.js','','',true);
    wp_enqueue_script( 'colaborativo_plugins' );

    wp_register_script('colaborativo_app',get_template_directory_uri() . '/js/app.js','','',true);
    wp_enqueue_script( 'colaborativo_app' );

    wp_enqueue_script( 'jquery' );

}

add_action('wp_enqueue_scripts', 'colaborativo_scripts_method');

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
  register_post_type( 'galeria', $args_galeria );

}
add_action( 'init', 'colaborativo_post_types' );

function colaborativo_rewrite_flush() {
    flush_rewrite_rules();
}
add_action( 'after_switch_theme', 'colaborativo_rewrite_flush' );

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
        'title'  => __('Front page', 'cola'),
        'fields' => array(
            array(
                'id'    => 'video-url',
                'title' => __('Video URL', 'cola'),
                'type'  => 'text'
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
?>
	<article id="post-<?php the_ID(); ?>" <?php post_class('span4'); ?> data-date="<?php the_time('Y-m-d H:i:s'); ?>" data-id="<?php the_ID(); ?>">
        <div class="article-content">
            <?php
                if(get_post_type() == "imagen"){
                $enclosure = get_post_meta(get_the_ID(), $key = 'enclosure', $single = true);
                $enclosure = apply_filters( 'the_title', $enclosure);
                $enclosure_array = explode('
', $enclosure);
            ?>


        		<?php if(has_post_thumbnail() || $enclosure){ ?>
        			<a class="thumbnail" href="<?php the_permalink(); ?>?ajax=true&width=940&height=90%" rel="prettyPhoto[<?php echo get_post_type() ?>]">
                        <?php
                            if(has_post_thumbnail()) {
                                the_post_thumbnail('article-thumb');
                            }else{
                        ?>
        				    <img width="280" height="280" src="<?php bloginfo('template_directory'); ?>/img/timthumb.php?src=<?php echo($enclosure_array[0]); ?>&w=280&h=280" />
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
                        <img width="280" height="280" src="<?php bloginfo('template_directory'); ?>/img/timthumb.php?src=http://img.youtube.com/vi/<?php echo $video_id ?>/hqdefault.jpg&w=280&h=280" />
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

            <a class="overlay" href="<?php the_permalink(); ?>?ajax=true&width=940&height=90%" rel="prettyPhoto[<?php echo get_post_type() ?>]"><?php _e('ver ', 'colaborativo'); echo get_post_type(); ?></a>
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
                if(get_post_type() == "imagen"){
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
                            <img width="600" height="600" src="<?php bloginfo('template_directory'); ?>/img/timthumb.php?src=<?php echo($enclosure_array[0]); ?>&w=600&h=600" />
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
                    <iframe width="480" height="320" src="http://www.youtube.com/embed/<?php echo $video_id; ?>?wmode=opaque" frameborder="0" allowfullscreen></iframe>
                </div>
            <?php }elseif(get_post_type() == "tweet"){ ?>
                <h2><?php echo(make_clickable(get_the_title())); ?></h2>
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
                    $enclosure = apply_filters( 'the_title', $enclosure);
                    $enclosure_array = explode('
', $enclosure);
                ?>
                    <div class="avatar right">
                        <a href="<?php the_syndication_permalink(); ?>" target="_blank"><img src="<?php echo $enclosure_array[0]; ?>" /></a>
                    </div>
                <?php } ?>
                <small>POR</small><br />
                <?php the_author(); ?>
            </div>
            <div class="social">
                <!-- AddThis Button BEGIN -->
                <div class="addthis_toolbox addthis_default_style ">
                    <a addthis:url="<?php the_permalink(); ?>" addthis:title="<?php the_title(); ?>" class="addthis_button_facebook_like" fb:like:layout="button_count"></a><br />
                    <a addthis:url="<?php the_permalink(); ?>" addthis:title="<?php the_title(); ?>" class="addthis_button_tweet"></a><br />
                    <a addthis:url="<?php the_permalink(); ?>" addthis:title="<?php the_title(); ?>" class="addthis_button_google_plusone" g:plusone:size="medium"></a><br />
                    <a addthis:url="<?php the_permalink(); ?>" addthis:title="<?php the_title(); ?>" class="addthis_counter addthis_pill_style"></a>
                </div>
                <!-- AddThis Button END -->
            </div>
        </div>
    </article>
<?php
}

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
        $colores .= ".pp_inline article.category-".$categoria->slug." .autor { background-color: ".$color."; } ";
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
    }

    if($type){ /* Si tenemos un type en el request, else, todos los tipos. */
        $post_types = array( $type );
        if( $type == "imagen" ){
            $post_types = array_merge( $post_types , "galeria");
        }
    }else{
        $post_types = get_post_types( array('public' => true, '_builtin' => false), 'names' );
        $post_types = array_merge( $post_types, array('post') );
    }

    //parametros para wp_query
    $params = array(
        'post_type' => $post_types,
        'ignore_sticky_posts' => 1
    );
    if($cat){
        $params['cat'] = $cat;
    }
    $params['posts_per_page'] = $operacion=='append' ? 13 : -1;

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

add_action('wp_ajax_agregarboxes', 'agregador_cajas');
add_action('wp_ajax_nopriv_agregarboxes', 'agregador_cajas');

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

add_action('wp_ajax_agregarboxes', 'load_content_box');
add_action('wp_ajax_nopriv_agregarboxes', 'load_content_box');

function filter_where($where='')
{
    global $time_for_filter, $op_for_filter;
    $where .= " AND wp_posts.post_date ";
    $where .= $op_for_filter=='append' ? "<= " : ">= ";
    $where .= "'$time_for_filter'";
    return $where;
}
