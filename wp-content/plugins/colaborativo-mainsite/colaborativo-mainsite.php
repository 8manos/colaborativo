<?php

/*
Plugin Name: Colaborativo Main Site
Author: 8manos S.A.S
Author URI: http://8manos.com
*/

class Colaborativomainsite {

	public static function init() {

		#Pages meta
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

		if( $equipo ) {

			$output.= '<div class="row-fluid"><ul class="thumbnails">';

			foreach($equipo as $miembro) {

				$nombre = $miembro['nombre'];
				$subtitulo = $miembro['subtitulo'];
				$url = $miembro['url'];
				$thumb = wp_get_attachment_image( $miembro['thumb-equipo'], array( 145, 145) );

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

				$output .= '</div></li>';
		
			}
			$output .= '</ul></div>';

			return $output;

		}else{

			return false;

		}
	}

	public static function respaldo() {

		global $post;

		$output = '';

		$respaldo = get_post_meta( $post->ID , '_respaldo', true );

		if( $respaldo ) {

			$output.= '<div class="row-fluid"><h2>Respaldo</h2><ul class="thumbnails">';

			foreach($respaldo as $miembro) {

				$nombre = $miembro['nombre-respaldo'];
				$subtitulo = $miembro['subtitulo-respaldo'];
				$url = $miembro['url-respaldo'];
				$thumb = wp_get_attachment_image( $miembro['thumb-respaldo'] );

				$output .= '<li class="span4"><div class="thumbnail">';

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

				$output .= '</div></li>';
		
			}
			$output .= '</ul></div>';

			return $output;

		}else{

			return false;

		}

	}

}
add_action( 'init', array('colaborativoShortcodes', 'init') );