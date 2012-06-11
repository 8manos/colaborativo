<?php

/**
 * @package KC_Essentials
 * @version 0.1
 */


function kc_essentials_insert_custom_image_sizes( $fields, $post ) {
	if ( !isset($fields['image-size']['html']) || substr($post->post_mime_type, 0, 5) != 'image' )
		return $fields;

	$_sizes = kcSettings_options::$image_sizes_custom;
	if ( empty($_sizes) )
		return $fields;

	$items = array();
	foreach ( array_keys($_sizes) as $size ) {
		$img = image_get_intermediate_size( $post->ID, $size );
		if ( !$img )
			continue;

		$css_id = "image-size-{$size}-{$post->ID}";
		$html  = "<div class='image-size-item'>";
		$html .= "<input type='radio' name='attachments[{$post->ID}][image-size]' id='{$css_id}' value='{$size}' />";
		$html .= "<label for='{$css_id}'>{$size}</label>";
		$html .= "<label for='{$css_id}' class='help'>" . sprintf( "(%d&nbsp;&times;&nbsp;%d)", $img['width'], $img['height'] ). "</label>";
		$html .= "</div>";

		$items[] = $html;
	}

	$items = join( "\n", $items );
	$fields['image-size']['html'] = "{$fields['image-size']['html']}\n{$items}";

	return $fields;
}

add_filter( 'attachment_fields_to_edit', 'kc_essentials_insert_custom_image_sizes', 11, 2 );



?>
