<?php

/**
 * @package KC_Essentials
 * @version 0.1
 */

/*
Plugin name: KC Essentials
Plugin URI: http://kucrut.org/
Description: The essentials
Version: 0.1
Author: Dzikri Aziz
Author URI: http://kucrut.org/
License: GPL v2
*/


class kcEssentials {
	protected static $data = array(
		'version'	=> '0.1'
	);


	static function init() {
		$paths = kcSettings::_paths( __FILE__ );
		if ( !is_array($paths) )
			return false;

		self::$data['paths'] = $paths;

		$settings = kc_get_option( 'kc_essentials' );
		self::$data['settings'] = $settings;

		# i18n
		$mo_file = $paths['inc'].'/languages/kc-essentials-'.get_locale().'.mo';
		if ( is_readable($mo_file) )
			load_textdomain( 'kc-essentials', $mo_file );

		# Settings
		require_once "{$paths['inc']}/_options.php";

		require_once "{$paths['inc']}/_helpers.php";

		# Auto update
		//self::check_update();

		# Scripts n styles
		add_action( 'init', array(__CLASS__, '_sns_register'), 100 );

		require_once "{$paths['inc']}/widget_widgets.php";
		add_action( 'widgets_init', array('kcEssentials_widgets', 'init') );

		register_deactivation_hook( $paths['p_file'], array(__CLASS__, '_deactivate') );

		# Components
		if ( !isset($settings['components']) || empty($settings['components']) )
			return false;

		add_action( 'init', array(__CLASS__, '_component_activation'), 100 );
	}


	public static function _sns_register() {
		if ( !defined('KC_ESSENTIALS_SNS_DEBUG') )
			define( 'KC_ESSENTIALS_SNS_DEBUG', false );

		$suffix = KC_ESSENTIALS_SNS_DEBUG ? '.dev' : '';

		wp_register_script( 'kc-essentials', self::$data['paths']['scripts']."/settings{$suffix}.js", array('kc-settings-base'), self::$data['version'], true );
		wp_register_script( 'kc-widgets-admin', self::$data['paths']['scripts']."/widgets{$suffix}.js", array('kc-settings-base', 'media', 'wp-ajax-response'), self::$data['version'], true );

		wp_register_style(  'kc-widgets-admin', self::$data['paths']['styles']."/widgets{$suffix}.css", false, self::$data['version'] );
		wp_register_style(  'kc-essentials', self::$data['paths']['styles']."/settings{$suffix}.css", false, self::$data['version'] );
	}


	public static function _component_activation() {
		foreach ( self::$data['settings']['components'] as $group )
			foreach ( $group as $component )
				if ( file_exists( self::$data['paths']['inc'] . "/{$component}.php") )
					require_once self::$data['paths']['inc'] . "/{$component}.php";
	}


	public static function get_data() {
		if ( !func_num_args() )
			return self::$data;

		$args = func_get_args();
		return kc_array_multi_get_value( self::$data, $args );
	}


	# Register to KC Settings
	public static function _activate() {
		if ( version_compare(get_bloginfo('version'), '3.3', '<') )
			wp_die( 'Please upgrade your WordPress to version 3.3 before using this plugin.' );

		if ( !class_exists('kcSettings') )
			wp_die( 'Please install and activate <a href="http://wordpress.org/extend/plugins/kc-settings/">KC Settings</a> before activating this plugin.<br /> <a href="'.wp_get_referer().'">&laquo; Go back</a> to plugins page.' );

		$kcs = kcSettings::get_data('status');
		$kcs['kids']['kc_essentials'] = array(
			'name' => 'KC Essentials',
			'type' => 'plugin',
			'file' => kc_plugin_file( __FILE__ )
		);
		update_option( 'kc_settings', $kcs );
	}


	# Unregister from KC Settings
	public static function _deactivate() {
		$kcs = kcSettings::get_data('status');
		unset( $kcs['kids']['kc_essentials'] );
		update_option( 'kc_settings', $kcs );
	}


	public static function check_update() {
		if ( !class_exists('kcUpdate') )
			require_once self::$data['paths']['inc'] . '/_update.php';
		new kcUpdate( '0.1', 'http://repo.kucrut.org/api.php', self::$data['paths']['p_file'] );
	}
}
add_action( 'plugins_loaded', array('kcEssentials', 'init') );


# A hack for symlinks
if ( !function_exists('kc_plugin_file') ) {
	function kc_plugin_file( $file ) {
		if ( !file_exists($file) )
			return $file;

		$file_info = pathinfo( $file );
		$parent = basename( $file_info['dirname'] );

		$file = ( $parent == $file_info['filename'] ) ? "{$parent}/{$file_info['basename']}" : $file_info['basename'];

		return $file;
	}
}

register_activation_hook( kc_plugin_file( __FILE__ ), array('kcEssentials', '_activate') );

?>
