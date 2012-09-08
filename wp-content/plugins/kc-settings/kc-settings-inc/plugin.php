<?php

class kcSettings_plugin {
	var $url;
	var $group;
	var $metabox;

	# Add settings menus and register the options
	function __construct( $group ) {
		$this->group = $group;

		# Register the menus to WP
		add_action( 'admin_menu', array(&$this, 'create_menu'));
		# Register the options
		add_action( 'admin_init', array(&$this, 'register_options'), 11 );
		# Plugin setting link
		add_filter( 'plugin_row_meta', array(&$this, 'setting_link'), 10, 3 );
	}


	# Create the menu
	function create_menu() {
		extract( $this->group, EXTR_OVERWRITE );

		$this->page = add_submenu_page( $menu_location, $page_title, $menu_title, 'manage_options', "kc-settings-{$prefix}", array(&$this, 'settings_page') );
		$this->url = admin_url( "{$menu_location}?page=kc-settings-{$prefix}" );
		kcSettings::add_page( $this->page );

		# Help
		if ( isset($help) )
			kcSettings::add_help( $this->page, $help );

		if ( $display == 'metabox' ) {
			require_once( dirname(__FILE__) . '/plugin-metabox.php' );
			$this->metabox = new kcSettings_plugin_metabox( $this );
		}

		if ( isset($load_actions) && is_callable($load_actions) )
			add_action( "load-{$this->page}", $load_actions, 99 );
	}


	# Register settings sections and fields
	function register_options() {
		extract( $this->group, EXTR_OVERWRITE );

		if ( is_array($options) && !empty($options) ) {

			# register our options, unique for each theme/plugin
			register_setting( "{$prefix}_settings", "{$prefix}_settings", array(&$this, 'validate') );

			foreach ( $options as $section ) {
				$section_title = ( isset($section['title']) ) ? $section['title'] : "{$prefix}-section-{$section['id']}";
				# Add sections
				add_settings_section( $section['id'], $section_title, '', "{$prefix}_settings" );

				# Skip fields for sections with custom callbacks
				if ( !isset($section['fields']) )
					continue;

				foreach ( $section['fields'] as $field ) {
					# add fields on each sections
					$args = array(
						'mode'    => 'plugin',
						'prefix'  => $prefix,
						'section' => $section['id'],
						'field'   => $field,
						'echo'    => true,
						'tabled'  => true
					);
					if ( !in_array($field['type'], array('checkbox', 'radio', 'multiinput')) )
						$args['label_for'] = "{$section['id']}__{$field['id']}";

					add_settings_field( $field['id'], $field['title'], '_kc_field', "{$prefix}_settings", $section['id'], $args );
				}
			}

		}
	}


	# Setting link on the plugins listing page
	function setting_link( $plugin_meta, $plugin_file, $plugin_data ) {
		//echo '<pre>'.print_r( $this->group, true).'</pre>';
		if ( $plugin_data['Name'] == $this->group['menu_title'] )
			$plugin_meta[] = '<a href="'.$this->url.'">'.__('Settings', 'kc-settings').'</a>';

		return $plugin_meta;
	}


	# Create settings page content/wrapper
	function settings_page() {
		extract( $this->group, EXTR_OVERWRITE ); ?>

	<div class="wrap">
		<?php screen_icon(); ?>
		<h2><?php echo $page_title ?></h2>
		<?php do_action( "{$prefix}_kc_settings_page_before", $this->group ) ?>
		<form action="options.php" method="post" id="kc-settings-form">
			<?php
				# The hidden fields
				settings_fields( "{$prefix}_settings" );

				switch ( $this->group['display'] ) {
					case 'metabox' :
						$this->metabox->display();
					break;
					case 'plain' :
						foreach ( $this->group['options'] as $section ) {
							echo "<h3>{$section['title']}</h3>\n";
							$this->settings_section( $section );
						}
						echo "<p class='submit'><input class='button-primary' name='submit' type='submit' value='".esc_attr( __('Save Changes') )."' /></p>\n";
					break;
				}
			?>
		</form>
		<?php do_action( "{$prefix}_kc_settings_page_after", $this->group ) ?>
	</div>
	<?php }


	function settings_section( $section ) {
		if ( isset($section['desc']) && !empty($section['desc']) ) {
			echo "<div class='section-desc'>\n";
			echo wpautop( $section['desc'] );
			echo "</div>\n";
		}

		do_action( 'kc_settings_section_before', $this->group['prefix'], $section );

		# Call user callback function for displaying the section ( if set )
		if ( isset($section['cb']) && is_callable($section['cb']) ) {
			$section = array_merge( $section, array(
				'field_id'   => "{$this->group['prefix']}_settings__{$section['id']}",
				'field_name' => "{$this->group['prefix']}_settings[{$section['id']}]"
			) );
			$cb_args = isset($section['args']) ? $section['args'] : '';
			if ( $cb_args && is_callable($cb_args) )
				$cb_args = call_user_func_array( $cb_args, array( 'args' => $section) );
			call_user_func_array( $section['cb'], array('args' => $section, 'cb_args' => $cb_args) );
		}
		# Defaults to WordPress' Settings API
		else {
			echo "<table class='form-table'>\n";
			do_settings_fields( "{$this->group['prefix']}_settings", $section['id'] );
			echo "</table>\n";
		}

		# Wanna do something after the options table?
		do_action( 'kc_settings_section_after', $this->group['prefix'], $section );
	}


	# Setting field validation callback
	function validate( $user_val ) {
		$prefix = $this->group['prefix'];
		$options = $this->group['options'];

		# apply validation/sanitation filter(s) on the new values
		# prefix-based filter
		$user_val = apply_filters( "kcv_settings_{$prefix}", $user_val );
		if ( empty($user_val) )
			return apply_filters( "kc_psv", $user_val );

		$nu_val = array();
		foreach ( $user_val as $section_id => $section_value ) {
			# section-based filter
			$nu_val[$section_id] = apply_filters( "kcv_setting_{$prefix}_{$section_id}", $section_value );

			if ( !isset($options[$section_id]['fields']) )
				continue;

			foreach ( $nu_val[$section_id] as $field_id => $field_value ) {
				$type = $options[$section_id]['fields'][$field_id]['type'];

				# default sanitation
				$field_value = _kc_sanitize_value( $field_value, $type );

				# type-based filter
				$field_value = apply_filters( "kcv_setting_{$prefix}_{$type}", $field_value );

				# field-based filter
				$field_value = apply_filters( "kcv_setting_{$prefix}_{$section_id}_{$field_id}", $field_value );

				# insert the filtered value to our new array
				$nu_val[$section_id][$field_id] = $field_value;
			}
		}

		return apply_filters( "kc_psv", $nu_val );
	}
}

?>
