<?php

/*
Plugin Name: Colaborativo
Author: 8manos S.A.S
Author URI: http://8manos.com
*/

class Colaborativo {

	public static function init() {

		#Taxonomies meta
		add_filter( 'kc_term_settings', array(__CLASS__, 'metadata_taxonomies') );

	}

	public static function metadata_taxonomies( $groups ) {

		$groups[] = array(
			# Categories
			'category' => array(
				array(
					'id'	=> 'categoria-colors',
					'title'	=> 'Metadata',
					'fields'=> array(
						'id'	=> 'color-categoria',
						'title'	=> 'Colores',
						'type'	=> 'color'
					)
				)
			)
		);

		return $groups;

	}
}

Colaborativo::init();