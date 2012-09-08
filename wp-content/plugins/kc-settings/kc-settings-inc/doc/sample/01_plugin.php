<?php

/**
 * Sample plugin/theme options.
 *
 * @TODO Make sure to use unique and only alphanumerics/dashes/underscores string for the prefix and section field IDs!
 * @see 00_fields.php for complete field types.
 *
 */


add_filter( 'kc_plugin_settings', 'myplugin_options' );
function myplugin_options( $settings ) {
	/**
	 * TODO: Please copy/paste/edit the fields you need, then remove the require_once line.
	 * This is only for simplifying the development.
	 */
	require_once dirname(__FILE__) . '/00_fields.php';

	$my_settings = array(
		/**
		 * Only alphanumerics, dashes and underscores are allowed here.
		 */
		'prefix' => 'anything',
		/**
		 * Optional. This is the location where the menu will appear.
		 * - Dashboard: index.php
		 * - Posts: edit.php
		 * - Media: upload.php
		 * - Links: link-manager.php
		 * - Comments: edit-comments.php
		 * - Appearance: themes.php
		 * - Plugins: plugins.php
		 * - Users: users.php
		 * - Tools tools.php
		 * - Settings: options-general.php (default)
		 */
		'menu_location' => 'options-general.php',
		'menu_title'    => 'My Plugin',
		'page_title'    => 'My Plugin Settings Page',
		/**
		 * Optional. You can either use 'metabox' or 'plain' here.
		 */
		'display'       => 'metabox',
		/**
		 * Optional. This is the default metabox config.
		 * context: normal | advanced | side
		 * priority: default | high | low
		 */
		'metabox'       => array(
			'context'   => 'normal',
			'priority'  => 'default',
		),
		'options'       => array(
			array(
				'id'     => 'sample_section',
				'title'  => 'Sample Options',
				'desc'   => '<p>Some description about this options group</p>',
				'fields' => kc_sample_fields() // TODO: See 00_fields.php
			)
			// You can add more sections here...
		),
		/**
		 * Optional. Here goes the contextual helps.
		 */
		'help'          => array(
			array(
				'id'      => 'help_1',
				'title'   => 'Help title',
				'content' => 'Something....'
			),
			array(
				'id'      => 'help_2',
				'title'   => 'Another Help',
				'content' => 'Something the user needs to know....'
			)
		)
	);

	$settings[] = $my_settings;
	return $settings;
}

?>
