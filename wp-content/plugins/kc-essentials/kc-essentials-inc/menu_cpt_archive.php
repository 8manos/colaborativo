<?php

/**
 * @package KC_Essentials
 * @version 0.1
 */


class kcEssentials_menu_cpt_archive {
	private static $data = array(
		'items' => array()
	);


	public static function init() {
		$post_types = get_post_types(array(
			'_builtin'          => false,
			'show_in_nav_menus' => true,
			'has_archive'       => true
		), 'objects');
		if ( empty($post_types) )
			return false;

		self::$data['post_types'] = $post_types;
		foreach ( $post_types as $post_type )
			add_filter( 'nav_menu_items_' . $post_type->name, array(__CLASS__, '_add' ), 10, 3 );

		add_filter( 'nav_menu_css_class', array(__CLASS__, '_set_class'), 10, 3 );
		add_filter( 'walker_nav_menu_start_el', array(__CLASS__, '_set_path'), 10, 4 );
	}


	public static function _add( $posts, $args, $post_type ) {
		global $_nav_menu_placeholder;
		$_nav_menu_placeholder = ( 0 > $_nav_menu_placeholder ) ? intval($_nav_menu_placeholder) - 1 : -1;

		array_unshift( $posts, (object) array(
			'ID'           => 0,
			'object_id'    => $_nav_menu_placeholder,
			'post_content' => '',
			'post_excerpt' => '',
			'post_parent'  => '',
			'post_title'   => $post_type['args']->label,
			'post_type'    => 'nav_menu_item',
			'type'         => 'custom',
			'url'          => "###cca###{$post_type['args']->name}###"
		) );

		return $posts;
	}


	public static function _set_class( $classes, $item, $args ) {
		if ( $item->type != 'custom' || !preg_match( '/^###cca###(\w+)###/', $item->url, $matches ) || !post_type_exists($matches[1])  )
			return $classes;

		if ( is_post_type_archive($matches[1]) )
			$classes[] = 'current-menu-item';

		if ( is_singular($matches[1]) )
			$classes[] = 'current-menu-ancestor';

		self::$data['items'][$item->ID] = array( 'post_type' => $matches[1] );
		return $classes;
	}


	public static function _set_path( $item_output, $item, $depth, $args ) {
		if ( !isset(self::$data['items'][$item->ID]) )
			return $item_output;

		$post_type_object = get_post_type_object( self::$data['items'][$item->ID]['post_type'] );
		$url = get_post_type_archive_link(self::$data['items'][$item->ID]['post_type']);

		$item_output = preg_replace('/href=".*?"/', 'href="'.$url.'"', $item_output );
		self::$data['items'][$item->ID]['path'] = $url;
		return $item_output;
	}
}
kcEssentials_menu_cpt_archive::init();

?>
