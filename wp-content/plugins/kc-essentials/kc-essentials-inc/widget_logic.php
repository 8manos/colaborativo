<?php

/**
 * @package KC_Essentials
 * @version 0.1
 */


class kcEssentials_widget_logic {
	private static $data = array();

	public static function init() {
		# Custom widget ID & classes
		# 0. Add fields on widget configuration form
		add_filter( 'widget_form_callback', array(__CLASS__, '_fields'), 10, 2 );

		# 1. Update widget options
		add_filter( 'widget_update_callback', array(__CLASS__, '_save'), 10, 4 );

		# 2. Remove widgets from sidebars as needed
		add_filter( 'sidebars_widgets', array(__CLASS__, '_filter_widgets') );

		# Admin scripts & styles
		add_action( 'load-widgets.php', array(__CLASS__, '_sns') );
	}



	/**
	 * Add logic fields to widget configuration form
	 *
	 */
	public static function _fields( $instance, $widget ) {
		$f_id			= $widget->get_field_id('kc-logic');
		$f_name		= $widget->get_field_name('kc-logic');
		$setting	= kcEssentials_widgets::get_setting( $widget->id );

		$output  = "<div class='kcwe'>\n";
		$output .= "\t<p>\n";
		$output .= "\t\t<label for='".$widget->get_field_id('kc-logic-enable')."'>".__('Logic', 'kc-essentials')."</label>\n";
		$output .= "\t\t<select id='".$widget->get_field_id('kc-logic-enable')."' name='".$widget->get_field_name('kc-logic-enable')."' class='hasdep kc-logic-enable' data-child='#{$f_id}-logics'>\n";

		$output .= "\t\t\t<option";
		if ( !isset($setting['kc-logic']) )
			$output .= " selected='true'";
		$output .= ">".__('Disable', 'kc-essentials')."</option>\n";

		$output .= "\t\t\t<option";
		if ( isset($setting['kc-logic']) && count($setting['kc-logic']) )
			$output .= " selected='true'";
		$output .= " value='y'> ".__('Enable', 'kc-essentials')."</option>\n";
		$output .= "\t\t</select>\n";
		$output .= "\t</p>\n";


		$logics = array(
			array(
				'label' => __('Homepage', 'kc-essentials'),
				'key'   => 'is_home',
				'value' => true
			),
			array(
				'label' => __('Static front page', 'kc-essentials'),
				'key'   => 'is_front_page',
				'value' => true
			),
			array(
				'label' => __('Singular', 'kc-essentials'),
				'key'   => 'is_singular',
				'value' => true,
				'args'  => true
			),
			array(
				'label' => __('Page', 'kc-essentials'),
				'key'   => 'is_page',
				'value' => true,
				'args'  => true
			),
			array(
				'label' => __('Custom page template', 'kc-essentials'),
				'key'   => 'is_page_template',
				'value' => true,
				'args'  => true
			),
			array(
				'label' => __('Single post', 'kc-essentials'),
				'key'   => 'is_single',
				'value' => true,
				'args'  => true
			),
			array(
				'label' => __('Sticky post', 'kc-essentials'),
				'key'   => 'is_sticky',
				'value' => true,
				'args'  => true
			),
			array(
				'label' => __('Attachment', 'kc-essentials'),
				'key'   => 'is_attachment',
				'value' => true,
				'args'  => true
			),
			array(
				'label' => __('Archive', 'kc-essentials'),
				'key'   => 'is_archive',
				'value' => true
			),
			array(
				'label' => __('Post type archive', 'kc-essentials'),
				'key'   => 'is_post_type_archive',
				'value' => true,
				'args'  => true
			),
			array(
				'label' => __('Category', 'kc-essentials'),
				'key'   => 'is_category',
				'value' => true,
				'args'  => true
			),
			array(
				'label' => __('Tag', 'kc-essentials'),
				'key'   => 'is_tag',
				'value' => true,
				'args'  => true
			),
			array(
				'label' => __('Taxonomy', 'kc-essentials'),
				'key'   => 'is_tax',
				'value' => true
			),
			array(
				'label' => __('Author', 'kc-essentials'),
				'key'   => 'is_tax',
				'value' => true,
				'args'  => true
			),
			array(
				'label' => __('404', 'kc-essentials'),
				'key'   => 'is_404',
				'value' => true
			),
			array(
				'label' => __('Search page', 'kc-essentials'),
				'key'   => 'is_search',
				'value' => true
			),
			array(
				'label' => __('Paged archive', 'kc-essentials'),
				'key'   => 'is_paged',
				'value' => true
			),
			array(
				'label' => __('Year archive', 'kc-essentials'),
				'key'   => 'is_year',
				'value' => true
			),
			array(
				'label' => __('Month archive', 'kc-essentials'),
				'key'   => 'is_month',
				'value' => true
			),
			array(
				'label' => __('Date archive', 'kc-essentials'),
				'key'   => 'is_date',
				'value' => true
			),
			array(
				'label' => __('Day archive', 'kc-essentials'),
				'key'   => 'is_day',
				'value' => true
			),
			array(
				'label' => __('New day', 'kc-essentials'),
				'key'   => 'is_new_day',
				'value' => true
			),
			array(
				'label' => __('Time archive', 'kc-essentials'),
				'key'   => 'is_time',
				'value' => true
			),
			array(
				'label' => __('Preview page', 'kc-essentials'),
				'key'   => 'is_preview',
				'value' => true
			),
			array(
				'label' => __('Logged in user', 'kc-essentials'),
				'key'   => 'is_preview',
				'value' => true
			)
		);

		$output .= "\t<ul id='{$f_id}-logics' data-dep='y' class='logics'>";
		foreach ( $logics as $c ) {
			$output .= "\t\t<li>\n";
			$output .= "\t\t<label><input type='checkbox' name='{$f_name}[{$c['key']}]' value='{$c['value']}' ";
			if ( isset($setting['kc-logic'][$c['key']]) )
			$output .= " checked='true'";
				$output .= "/> {$c['label']}</label>\n";
			$output .= "\t\t</li>\n";
		}

		$output .= "\t</ul>\n";
		$output .= "</div>\n";

		echo $output;
		return $instance;
	}


	public static function _save( $instance, $new, $old, $widget ) {
		$setting = kcEssentials_widgets::get_setting( $widget->id );

		if ( $new['kc-logic-enable'] != 'y' || !isset($new['kc-logic']) || !count($new['kc-logic']) )
			unset( $setting['kc-logic'] );
		else
			$setting['kc-logic'] = $new['kc-logic'];

		kcEssentials_widgets::save_setting( $widget->id, $setting );

		return $instance;
	}


	public static function _filter_widgets( $sidebars_widgets ) {
		if ( is_admin() )
			return $sidebars_widgets;

		$settings = get_option( 'kc_essentials_we' );
		if ( !$settings )
			return $sidebars_widgets;

		foreach ( $sidebars_widgets as $sidebar => $widgets ) {
			if ( $sidebar == 'wp_inactive_widgets' )
				continue;

			foreach ( $widgets as $idx => $widget ) {
				if ( !isset($settings[$widget]['kc-logic']) )
					continue;

				foreach ( $settings[$widget]['kc-logic'] as $func => $arg ) {
					if ( call_user_func($func) === true )
						continue 2;
				}

				unset( $widgets[$idx] );
			}
			$sidebars_widgets[$sidebar] = $widgets;
		}

		return $sidebars_widgets;
	}


	/**
	 * Scripts n styles for the widget configuration forms
	 */
	public static function _sns() {
		wp_enqueue_script( 'kc-widgets-admin' );
		wp_enqueue_style( 'kc-widgets-admin' );
	}
}

kcEssentials_widget_logic::init();

?>