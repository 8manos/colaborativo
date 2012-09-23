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

		$my_group = array(
			'category' => array( // TODO: Change this to the desired taxonomy name
				array(
					'id'     => 'categoria-settings',
					'title'  => 'Settings de categoria',
					'desc'   => '<p>Configuración específica para esta categoria</p>',
					'role'   => array('administrator', 'editor'),
					'fields' => array(
							array(
								'id'      => 'categoria-color',
								'title'   => 'Color',
								'type'    => 'color',
								'default' => '#000000',
								'desc'    => 'Format: <code>#000000</code>'
							)
						)
				)
			)
		);

		$groups[] = $my_group;
		return $groups;

	}
}

Colaborativo::init();