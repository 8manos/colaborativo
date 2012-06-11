<?php

/**
 * @package KC_Essentials
 * @version 0.1
 */


class kcEssentials_widget_attr {

	public static function init() {
		# Custom widget ID & classes
		# 0. Add fields on widget configuration form
		add_filter( 'widget_form_callback', array(__CLASS__, '_fields'), 10, 2 );

		# 1. Update widget options
		add_filter( 'widget_update_callback', array(__CLASS__, '_save'), 10, 4 );

		# 2. Modify widget markup
		add_filter( 'dynamic_sidebar_params', array(__CLASS__, '_set') );
	}


	/**
	 * Add custom ID/classes fields to widget configuration form
	 *
	 */
	public static function _fields( $instance, $widget ) {
		$data = kcEssentials::get_data('settings', 'widget_attr');
		$setting = kcEssentials_widgets::get_setting( $widget->id );
		$customs = array(
			'id'	=> array(
				__('Custom ID', 'kc-essentials'),
				__('Custom ID', 'kc-essentials')
			),
			'class'	=> array(
				__('Custom classes', 'kc-essentials'),
				__('Custom classes, <small>(separate with spaces)</small>', 'kc-essentials')
			)
		);

		$output = "<div class='kcwe'>\n";
		foreach ( $customs as $c => $l ) {
			$f_current = isset($setting["custom_{$c}"]) ? $setting["custom_{$c}"] : '';
			$f_name	= "widget-{$widget->id_base}[{$widget->number}][custom_{$c}]";
			$f_id		= "widget-{$widget->id_base}-{$widget->number}-custom_{$c}";

			$output .= "\t<p>\n";


			if ( !isset($data[$c]) || empty($data[$c]) ) {
				$output .= "\t\t<label for='{$f_id}'>{$l[1]}</label>\n";
				$output .= "\t\t<input type='text' name='{$f_name}' id='{$f_id}' class='widefat' value='{$f_current}'/>\n";
			}
			else {
				$f_opt = array();
				foreach ( explode( ' ', $data[$c]) as $o )
					$f_opt[] = array('value' => $o, 'label' => $o);

				if ( $c == 'id' ) {
					$f_type = 'select';
					$f_class = 'widefat';
				} else {
					$f_type = 'checkbox';
					$f_name .= '[]';
					$f_class = '';
					$f_current = explode( ' ', $f_current );
				}

				$output .= "\t\t<label for='{$f_id}'>{$l[0]}</label><br />\n";
				$output .= kcForm::field(array(
					'attr'    => array(
						'id'    => $f_id,
						'name'  => $f_name,
						'class' => $f_class
					),
					'type'    => $f_type,
					'options' => $f_opt,
					'current' => $f_current
				));
			}
			$output .= "\t</p>\n";
		}
		$output .= "</div>\n";

		echo $output;

		return $instance;
	}


	/**
	 * Sanitize and save widget's custom ID and/or classes
	 *
	 */
	public static function _save( $instance, $new, $old, $widget ) {
		$setting = kcEssentials_widgets::get_setting( $widget->id );
		foreach ( array('id', 'class') as $c ) {
			# 0. Add/Update
			if ( isset($new["custom_{$c}"]) && !empty($new["custom_{$c}"]) ) {
				if ( $c == 'id' )
					$setting["custom_{$c}"] = trim( sanitize_html_class($new["custom_{$c}"]) );
				else
					$setting["custom_{$c}"] = trim( kc_essentials_sanitize_html_classes($new["custom_{$c}"]) );
			}

			# 1. Delete
			else {
				unset( $setting["custom_{$c}"] );
			}
		}

		kcEssentials_widgets::save_setting( $widget->id, $setting );
		return $instance;
	}


	/**
	 * Add custom ID/classes to widget's markup
	 *
	 */
	public static function _set( $params ) {
		$setting = kcEssentials_widgets::get_setting( $params[0]['widget_id'] );

		# 0. Custom ID
		if ( isset($setting['custom_id']) )
			$params[0]['before_widget'] = preg_replace( '/id=".*?"/', "id=\"{$setting['custom_id']}\"", $params[0]['before_widget'], 1 );

		# 1. Custom Classes
		if ( isset($setting['custom_class']) )
			$params[0]['before_widget'] = preg_replace( '/class="/', "class=\"{$setting['custom_class']} ", $params[0]['before_widget'], 1 );

		return $params;
	}
}

kcEssentials_widget_attr::init();

?>
