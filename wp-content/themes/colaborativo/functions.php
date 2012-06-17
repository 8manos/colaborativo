<?php // Colaborativo functions file

remove_action('wp_head', 'wp_generator');

/**
 * Setup the theme 
 */
function setup_colaborativo(){
	add_theme_support( 'post-thumbnails' );
}
add_action( 'after_setup_theme', 'setup_colaborativo' );

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
    'name' => _x('Imagenes', 'post type general name'),
    'singular_name' => _x('Imagen', 'post type singular name'),
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
    'name' => _x('Videos', 'post type general name'),
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

  register_post_type('imagen',$args_img);
  register_post_type('video',$args_video);
  register_post_type('tweet',$args_tweet);
  register_post_type('sonido',$args_sonido);
  register_post_type('descarga',$args_descarga);

}
add_action( 'init', 'colaborativo_post_types' );

function colaborativo_rewrite_flush() {
    flush_rewrite_rules();
}
add_action( 'after_switch_theme', 'colaborativo_rewrite_flush' );

function display_article() {
?>
	<article <?php post_class('span3'); ?> data-timestamp="<?php the_time('U'); ?>">
        <div class="article-content">
            <?php 
                if(get_post_type() == "imagen"){ 
                $enclosure = get_post_meta(get_the_ID(), $key = 'enclosure', $single = true);
            ?>


        		<?php if(has_post_thumbnail() || $enclosure){ ?>
        			<a class="thumbnail" href="<?php the_permalink(); ?>">
                        <?php 
                            if(has_post_thumbnail() { 
                                the_post_thumbnail();
                            }else{
                        ?>
        				    <img src="<?php echo($enclosure); ?>" />
                        <?php } ?>
        			</a>
        		<?php } ?>
            <?php } ?>
            <h2><?php echo(make_clickable(get_the_title())); ?></h2>
            <a class="overlay" href="<?php the_permalink(); ?>"><?php _e('ver ', 'colaborativo'); echo get_post_type(); ?></a>
        </div>
		
		<footer class="post-meta">
			<span class="autor"><?php _e('Por:', 'colaborativo'); the_author(); ?></span>
			<span class="categoria"><?php the_category(); ?></span>
		</footer>
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