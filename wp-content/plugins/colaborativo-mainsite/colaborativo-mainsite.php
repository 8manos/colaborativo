<?php

/*
Plugin Name: Colaborativo Main Site
Author: 8manos S.A.S
Author URI: http://8manos.com
*/

class Colaborativomainsite {

	public static function init() {

		#Taxonomies meta
		add_filter( 'kc_post_settings', array(__CLASS__, 'metadata_pages') );

	}

	public static function metadata_pages( $groups ) {

		$my_group = array(
			'page' => array( // TODO: Change this to the desired taxonomy name
				array(
					'id'     => 'page-settings',
					'title'  => 'Settings de pagina',
					'role'   => array('administrator', 'editor'),
					'fields' => array(
							array(
								'id'     => 'equipo',
								'title'  => 'Miembros del equipo',
								'desc'   => 'Complela la informacion una vez por cada miembro, usa "add" para agregar nuevos',
								'type'   => 'multiinput', 
								'subfields' => array(
									array(
										'id'    => 'nombre',
										'title' => 'Nombre',
										'type'  => 'text'
									),
									array(
										'id'    => 'subtitulo',
										'title' => 'Subtitulo',
										'desc'  => 'se muestra bajo el nombre (enlace opcional)',
										'type'  => 'text'
									),
									array(
										'id'    => 'url',
										'title' => 'Url de enlace para subtitulo',
										'desc'  => 'Abre en una nueva ventana',
										'type'  => 'text'
									),
									array(
										'id'    => 'thumb-equipo',
										'title' => 'Foto',
										'type'  => 'file',
										'mode'  => 'single'
									)
								)
							),
							array(
								'id'     => 'respaldo',
								'title'  => 'Miembros del respaldo',
								'desc'   => 'Complela la informacion una vez por cada miembro, usa "add" para agregar nuevos',
								'type'   => 'multiinput', 
								'subfields' => array(
									array(
										'id'    => 'thumb-respaldo',
										'title' => 'Foto',
										'type'  => 'file',
										'mode'  => 'single'
									),
									array(
										'id'    => 'nombre-respaldo',
										'title' => 'Nombre',
										'type'  => 'text'
									),
									array(
										'id'    => 'subtitulo-respaldo',
										'title' => 'se muestra bajo el nombre (enlace opcional)',
										'type'  => 'text'
									),
									array(
										'id'    => 'url-respaldo',
										'title' => 'Url de enlace para subtitulo',
										'desc'  => 'Abre en una nueva ventana',
										'type'  => 'text'
									)
								)
							)
						)
				)
			)
		);

		$groups[] = $my_group;
		return $groups;

	}
}

Colaborativomainsite::init();

class colaborativoShortcodes {
	public static function init() {
		add_filter( 'widget_text', 'do_shortcode' );

		$methods = get_class_methods( __CLASS__ );
		foreach ( $methods as $m )
			if ( $m != __METHOD__ )
				add_shortcode( "colaborativo-{$m}", array(__CLASS__, $m) );
	}

	public static function equipo() {

		global $post;

		$output = '';

		$equipo = get_post_meta( $post->ID , '_equipo', true );

		$output.= '<div class="row-fluid"><ul class="thumbnails">';

		foreach($equipo as $miembro) {

			$nombre = $miembro['nombre'];
			$subtitulo = $miembro['subtitulo'];
			$url = $miembro['url'];
			$thumb = wp_get_attachment_image( $miembro['thumb-equipo'] );

			$output .= '<li class="span3"><div class="thumbnail">';

				$output .= $thumb;

				$output .= '<div class="caption">';

					$output .= '<h3>'.$nombre.'</h3>';

					$output .= '<h4>';

					if( $url ){
						$output .= '<a href="'.$url.'" target="_blank">';
					}
						$output .= $subtitulo;

					if( $url ){
						$output .= '</a>';
					}

					$output .= '</h4>';

				$output .= '</div>';

			/*
			<div class="video video-<?php echo $class_number; ?>">
				<a href="#" data-vid-url="<?php echo $video['url']; ?>" title="<?php echo $video['titulo']; ?>" data-desc="<?php echo $video['descripcion']; ?>">
					<h3><?php echo $video['titulo']; ?></h3>
					<span class="video-thumb-wrapper">
						<?php
						if ($video['thumb'] ) {
							echo wp_get_attachment_image( $video['thumb'] );
						}else{
							echo '<img src="http://placehold.it/200x200" />';
						}
						?>
						<span class="back"></span>
					</span>
				</a>
			</div>
			*/
			$output .= '</div></li>';
	
		}
		$output .= '</ul></div>';

		return $output;
	}


	public static function animalario( $atts ) {
		extract( shortcode_atts(array('cat' => ''), $atts) );
		$out = '<img src="'.get_template_directory_uri().'/i/animalario.png" />';
		return $out;
	}

	public static function comic() {
		global $post;

		/* Arguments for get_children(). */
		$children = array(
			'post_parent' => $post->ID,
			'post_status' => 'inherit',
			'post_type' => 'attachment',
			'post_mime_type' => 'image',
			'order' => 'ASC',
			'orderby' => 'menu_order ID',
			'exclude' => '',
			'include' => '',
			'numberposts' => -1,
			'offset' => '',
		);

		/* Get image attachments. If none, return. */
		$attachments = get_children( $children );

		if ( empty( $attachments ) )
			return '';

		$out = '';
		/* Loop through each attachment. */
		foreach ( $attachments as $id => $attachment ) {
			$vineta = wp_get_attachment_image_src( $id , 'original' );
			$out .= '<img
    data-src="'.$vineta[0].'"
    src=data:image/gif;base64,R0lGODlhAQABAAAAACH5BAEKAAEALAAAAAABAAEAAAICTAEAOw==
    onload=lzld(this) onerror=lzld(this) width="'.$vineta[1].'" height="'.$vineta[2].'" />';
		}

		return $out;
	}

	public static function subpagescuerda() {

		$padre = 1046;

		$sub_query = new WP_Query( 'orderby=menu_order&order=ASC&posts_per_page=-1&post_type=page&post_parent='.$padre );
			if($sub_query->have_posts()){
				echo "<div class='subpaginas cruzadas'>";
				while ($sub_query->have_posts()) : $sub_query->the_post();
		?>
					<h2><?php the_title(); ?></h2>
					<?php the_post_thumbnail( 'medium' ); ?>
					<?php the_content(); ?>
		<?php
				endwhile;
				echo "</div>";
		}
	}

}
add_action( 'init', array('colaborativoShortcodes', 'init') );