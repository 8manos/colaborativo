<?php

/**
 * @package KC_Essentials
 * @version 0.1
 */


class kcEssentials_mediatax {
	private static $data;


	public static function init( $taxonomies ) {
		self::$data['taxonomies'] = array();
		$media_taxonomies = get_object_taxonomies( 'attachment' );
		foreach ( $taxonomies as $tax ) {
			self::$data['taxonomies'][$tax] = get_taxonomy( $tax );

			# Register the taxonomy for attachment post type
			if ( !in_array($tax, $media_taxonomies) )
				register_taxonomy_for_object_type( $tax, 'attachment' );
		}


		# Add submenu under 'Media'
		add_action( 'admin_menu', array(__CLASS__, '_create_menu') );

		# Modify attachment edit form
		add_filter( 'attachment_fields_to_edit', array(__CLASS__, '_attachment_fields'), 10, 2 );

		# Modify posted attachment data and save terms for attachment.
		add_action( 'init', array(__CLASS__, '_save_terms'), 110 );
	}


	/**
	 * Add submenus under 'Media' for attachments' taxonomies
	 */
	public static function _create_menu() {
		foreach ( self::$data['taxonomies'] as $tax )
			add_submenu_page( 'upload.php', $tax->labels->name, $tax->labels->menu_name, $tax->cap->manage_terms, 'edit-tags.php?taxonomy=' . $tax->name );
	}


	/**
	 * Modify attachment edit form
	 *
	 * @param array $fields Attachment form fields
	 * @param object $post Attachment post object
	 * @return array $fields Modified attachment form fields
	 */
	public static function _attachment_fields( $fields, $post ) {
		foreach ( self::$data['taxonomies'] as $tax ) {
			if ( !isset($tax->args) )
				$tax->args = array();

			$post_terms = get_object_term_cache( $post->ID, $tax->name );
			if ( empty($post_terms) )
				$post_terms = wp_get_object_terms( $post->ID, $tax->name, $tax->args );

			$att_terms = array();
			if ( !empty($post_terms) )
				foreach ( $post_terms as $post_term )
					$att_terms[$post_term->term_id] = $post_term->name;

			$tax_terms = get_terms( $tax->name, array('hide_empty' => false) );

			$html = "<ul class='attachment-terms-list'>\n";

			if ( !empty($tax_terms) )
				foreach ( $tax_terms as $term )
					# Existing terms
					$html .= "\t<li><label><input type='checkbox' name='attachments[{$post->ID}][{$tax->name}][]' value='".esc_attr($term->name)."' ".checked(array_key_exists($term->term_id, $att_terms), true, false)." /> {$term->name}</label></li>\n";

			# New term
			$html .= "\t<li><input type='text' name='attachments[{$post->ID}][{$tax->name}][]' /></label></li>\n";

			$html .= "</ul>\n";

			$fields[$tax->name]['label']	= $tax->label;
			$fields[$tax->name]['input']	= 'html';
			$fields[$tax->name]['html']		= $html;
			$fields[$tax->name]['helps']	= sprintf(__( 'Check/uncheck existing %s, or add new one(s), separated by commas.', 'kc-essentials'), $fields[$tax->name]['label'] );
		}

		return $fields;
	}


	public static function _save_terms() {
		if ( empty($_POST['attachments']) )
			return;

		foreach ( $_POST['attachments'] as $id => $data ) {
			$taxonomies = get_attachment_taxonomies( $id );
			if ( empty($taxonomies) )
				continue;

			foreach ( $taxonomies as $tax ) {
				if ( isset($data[$tax]) && !empty($data[$tax]) )
					$_POST['attachments'][$id][$tax] = trim( join( ',', array_unique($data[$tax]) ) );
			}
		}
	}
}


if ( $taxonomies = kcEssentials::get_data('settings', 'taxonomy_media', 'taxonomies') )
	kcEssentials_mediatax::init( $taxonomies );

?>
