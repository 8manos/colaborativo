<?php

/**
 * @package KC_Essentials
 * @version 0.1
 */


class kcEssentials_widgets {
	public static $sidebars;

	public function init() {
		$settings = kcEssentials::get_data('settings');
		if ( !$settings
		     || !isset($settings['components']['widget'])
		     || empty($settings['components']['widget']) )
			return false;

		# Register sidebars
		if ( in_array('widget_areas', $settings['components']['widget'])
		     && isset($settings['widget_areas'])
		     && !empty($settings['widget_areas']) ) {

			self::$sidebars = $settings['widget_areas'];
			add_action( 'widgets_init', array(__CLASS__, 'register_sidebars'), 99 );
		}


		# Register widgets
		if ( in_array('widget_widgets', $settings['components']['widget'])
		     && isset($settings['widget_widgets']['widgets'])
		     && !empty($settings['widget_widgets']['widgets']) ) {
			foreach ( $settings['widget_widgets']['widgets'] as $widget ) {
				$file = dirname(__FILE__) . "/widgets/{$widget}.php";
				if ( !file_exists($file) || !is_readable($file) )
					continue;

				require_once $file;
				register_widget( "kc_widget_{$widget}" );
			}

			kcSettings::add_page( 'widgets.php' );
			add_action( 'load-widgets.php', array(__CLASS__, '_actions') );
		}
	}


	/**
	 * Register custom sidebars
	 *
	 * This has a low priority to make sidebars from the active
	 * theme got registered first
	 */
	public static function register_sidebars() {
		foreach ( self::$sidebars as $sb )
			register_sidebar( $sb );
	}


	/**
	 * Actions for the widgets admin page
	 */
	public static function _actions() {
		# Scripts n styles for the widget configuration forms
		wp_enqueue_script( 'kc-widgets-admin' );
		wp_enqueue_style( 'kc-widgets-admin' );

		# Add the post finder box
		add_action( 'admin_footer', 'find_posts_div', 99 );
	}


	/**
	 * Widget configuration form
	 *
	 * @param object $widget Widget object
	 * @param array $options Widget options
	 * @param array $config Current widget settings
	 *
	 * @return string Configuration form
	 */
  public static function form( $widget, $options, $config, $list_class = 'kcw-control-normal' ) {
		$form = "<ul class='{$list_class}'>\n";
		foreach ( $options as $id => $args ) {
			$f_id = $widget->get_field_id( $id );
			$f_name = $widget->get_field_name( $id );
			if ( isset($args['name_sfx']) ) {
				$f_name .= $args['name_sfx'];
				unset( $args['name_sfx'] );
			}

			$form .= "\t<li>\n";

			if ( isset($args['label']) && !empty($args['label']) ) {
				$label = "<label for='{$f_id}'>{$args['label']}</label>";
				if ( isset($args['heading']) ) {
					$label = "<h5>{$label}</h5>";
					unset( $args['heading'] );
				}
				unset( $args['label'] );

				$form .= "\t\t{$label}\n";
			}

			if ( !isset($args['current']) )
				$args['current'] = isset($config[$id]) ? $config[$id] : '';
			$args['attr'] = array(
				'id'   => $f_id,
				'name' => $f_name
			);
			$form .= "\t\t".kcForm::field( $args )."\n";
			$form .= "\t</li>\n";
		}
		$form .= "</ul>\n";

		return $form;
  }


	/**
	 * Get widget settings
	 */
	public static function get_setting( $widget_id ) {
		$setting = get_option( 'kc_essentials_we' );

		if ( !$setting || !isset($setting[$widget_id]) )
			return array();
		else
			return $setting[$widget_id];
	}


	/**
	 * Save widget settings
	 */
	public static function save_setting( $widget_id, $value ) {
		$settings = get_option( 'kc_essentials_we' );
		if ( !$settings )
			$settings = array();

		if ( empty($value) )
			unset( $settings[$widget_id] );
		else
			$settings[$widget_id] = $value;

		update_option( 'kc_essentials_we', $settings );

	}
}

?>
