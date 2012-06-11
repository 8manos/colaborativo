<?php

/**
 * @package KC_Essentials
 * @version 0.1
 */


class kcEssentials_options {
	private static $db_value;


	public static function init() {
		add_filter( 'kc_plugin_settings', array(__CLASS__, 'register') );
		add_filter( 'kcv_setting_kc_essentials_widget_areas', array(__CLASS__, 'sanitize_widget_areas') );
		add_filter( 'kcv_setting_kc_essentials_widget_attr_id', 'kc_essentials_sanitize_html_classes' );
		add_filter( 'kcv_setting_kc_essentials_widget_attr_class', 'kc_essentials_sanitize_html_classes' );
		add_filter( 'kcv_setting_kc_essentials_image_adaptive_sizes', 'kc_essentials_sanitize_numbers' );
		add_filter( 'kcv_setting_kc_essentials_image_adaptive_default', 'kc_essentials_sanitize_numbers' );
	}


	public static function register( $settings ) {
		$sections = array(
			array(
				'id'      => 'components',
				'title'   => __('General Settings', 'kc-essentials'),
				'desc'    => sprintf( __('<b>Please</b> read the <a%s>guide</a> before using this plugin!', 'kc-essentials'), ' href="#" class="kc-help-trigger"' ),
				'fields'  => array(
					array(
						'id'      => 'taxonomy',
						'title'   => __('Taxonomies', 'kc-essentials'),
						'type'    => 'checkbox',
						'options' => array(
							'taxonomy_unique' => __('Unique taxonomies', 'kc-essentials'),
							'taxonomy_media'  => __('Media taxonomies', 'kc-essentials')
						)
					),
					array(
						'id'      => 'widget',
						'title'   => __('Widgets', 'kc-essentials'),
						'type'    => 'checkbox',
						'options' => array(
							'widget_widgets' => __('Additional widgets', 'kc-essentials'),
							'widget_areas'   => __('Register additional sidebars/widget areas', 'kc-essentials'),
							'widget_logic'   => __('Conditional widgets, <em>a.k.a.</em> widget logic', 'kc-essentials'),
							'widget_attr'    => __('Custom widget ID &amp; classes', 'kc-essentials')
						)
					),
					array(
						'id'      => 'image',
						'title'   => __('Images', 'kc-essentials'),
						'type'    => 'checkbox',
						'options' => array(
							'image_adaptive' => __('Adaptive images', 'kc-essentials'),
							'image_insert'   => __('Insert images with custom sizes into post editor', 'kc-essentials')
						)
					),
					array(
						'id'      => 'menu',
						'title'   => __('Menu', 'kc-essentials'),
						'type'    => 'checkbox',
						'options' => array(
							'menu_cpt_archive' => __('Custom post type archive menu', 'kc-essentials')
						)
					),
					array(
						'id'      => 'enhancement',
						'title'   => __('Enhancements', 'kc-essentials'),
						'type'    => 'checkbox',
						'options' => array(
							'enhc_history_js'    => sprintf( __('Ajaxify the whole site using <a href="%s">history.js</a> and jQuery, <em>(experimental)</em>', 'kc-essentials'), 'https://github.com/balupton/History.js/' )
						)
					),
					array(
						'id'      => 'helper',
						'title'   => __('Helper functions', 'kc-essentials'),
						'type'    => 'checkbox',
						'options' => array(
							'helper_adjacent_post' => __('Get adjacent posts', 'kc-essentials') . ' (<code>KC_Adjacent_Post</code>)'
						)
					)
				)
			)
		);


		# Taxonomies
		$taxonomies = kcSettings_options::$taxonomies;
		if ( !empty($taxonomies) ) {

			# Media/attachment taxonomies
			$sections[] = array(
				'id'     => 'taxonomy_media',
				'title'  => __('Media taxonomies', 'kc-essentials'),
				'fields' => array(
					array(
						'id'      => 'taxonomies',
						'title'   => __('Taxonomies', 'kc-essentials'),
						'type'    => 'checkbox',
						'options' => $taxonomies
					)
				),
				'metabox' => array(
					'context'  => 'side',
					'priority' => 'default'
				)
			);


			# Unique taxonomies
			foreach ( array_keys($taxonomies) as $tax )
				if ( !is_taxonomy_hierarchical($tax) )
					unset( $taxonomies[$tax] );


			if ( !empty($taxonomies) ) {
				$sections[] = array(
					'id'     => 'taxonomy_unique',
					'title'  => __('Unique taxonomies', 'kc-essentials'),
					'fields' => array(
						array(
							'id'      => 'taxonomies',
							'title'   => __('Taxonomies', 'kc-essentials'),
							'type'    => 'checkbox',
							'options' => $taxonomies
						)
					),
					'metabox' => array(
						'context'  => 'side',
						'priority' => 'default'
					)
				);
			}

		}

		# Custom widget areas
		$sections[] = array(
			'id'      => 'widget_areas',
			'title'   => __('Additional sidebars/widget areas', 'kc-essentials'),
			'cb'      => array(__CLASS__, 'cb_section_widget_areas'),
			'metabox' => array(
				'context'  => 'side',
				'priority' => 'default'
			)
		);

		# Additional widgets
		$sections[] = array(
			'id'     => 'widget_widgets',
			'title'  => __('Additional widgets', 'kc-essentials'),
			'fields' => array(
				array(
					'id'      => 'widgets',
					'title'   => __('Widgets', 'kc-essentials'),
					'type'    => 'checkbox',
					'options' => array(
						'post'      => __('KC Posts', 'kc-essentials'),
						'term'      => __('KC Terms', 'kc-essentials'),
						'menu'      => __('KC Custom Menu', 'kc-essentials'),
						'sbanner'   => __('KC Simple Banner', 'kc-essentials'),
						'twitter'   => __('KC Twitter Timeline', 'kc-essentials'),
						'shortcode' => __('KC Shortcode', 'kc-essentials')
					)
				)
			),
			'metabox' => array(
				'context'  => 'side',
				'priority' => 'default'
			)
		);

		# Widget enhancements
		$sections[] = array(
			'id'     => 'widget_attr',
			'title'  => __('Custom widget ID &amp; classes', 'kc-essentials'),
			'fields' => array(
				array(
					'id'    => 'id',
					'title' => __('Custom widget IDs', 'kc-essentials'),
					'type'  => 'text',
					'attr'  => array('style' => 'width:98%' ),
					'desc'  => __('Predefined widget IDs (optional, separate with spaces)', 'kc-essentials'),
				),
				array(
					'id'    => 'class',
					'title' => __('Custom widget classes', 'kc-essentials'),
					'type'  => 'text',
					'attr'  => array('style' => 'width:98%' ),
					'desc'  => __('Predefined widget classes (optional, separate with spaces)', 'kc-essentials')
				)
			),
			'metabox' => array(
				'context'  => 'side',
				'priority' => 'default'
			)
		);

		# Adaptive images
		$rgt_link = class_exists( 'RegenerateThumbnails' ) ? admin_url('tools.php?page=regenerate-thumbnails') : 'http://wordpress.org/extend/plugins/regenerate-thumbnails/';
		$sections[] = array(
			'id'     => 'image_adaptive',
			'title'  => __('Adaptive Images', 'kc-essentials'),
			'fields' => array(
				array(
					'id'    => 'sizes',
					'title' => __('Image sizes', 'kc-essentials'),
					'type'  => 'text',
					'attr'  => array( 'style' => 'width:98%' ),
					'desc'  => sprintf( __('Comma separated list of image widths. <span class="impo">Don&#39;t forget to <a href="%s">regenerate the thumbnails</a></span>.', 'kc-essentials'), $rgt_link )
				),
				array(
					'id'      => 'default',
					'title'   => __('Default size', 'kc-essentials'),
					'type'    => 'text',
					'default' => 1280
				)
			)
		);


		# History.js
		$sections[] = array(
			'id'      => 'enhc_history_js',
			'title'   => __('History.js', 'kc-essentials'),
			'desc'    => __('Leave each field empty for default values. Separate <em>selectors</em> with commas, and <em>classes</em> with spaces.', 'kc-essentials'),
			'metabox' => array(
				'context'   => 'advanced',
				'priority'  => 'default'
			),
			'fields'	=> array(
				array(
					'id'      => 'el_excludes',
					'title'   => __('Excluded link selectors', 'kc-essentials'),
					'type'    => 'text',
					'default' => '#comment-popup-link',
					'desc'    => __('Default: <code>#comment-popup-link</code>', 'kc-essentials')
				),
				array(
					'id'      => 'url_excludes',
					'title'   => __('Excluded URLs', 'kc-essentials'),
					'type'    => 'text',
					'default' => '/wp-admin/, /feed/',
					'desc'    => __('Default: <code>/wp-admin/, /feed/</code>', 'kc-essentials')
				),
				array(
					'id'      => 'el_content',
					'title'   => __('Content selectors', 'kc-essentials'),
					'type'    => 'text',
					'default' => '#main, #content, article:first, .article:first, .post:first',
					'desc'    => __('Default: <code>#main, #content, article:first, .article:first, .post:first</code>', 'kc-essentials')
				),
				array(
					'id'      => 'el_menu',
					'title'   => __('Menu selectors', 'kc-essentials'),
					'type'    => 'text',
					'default' => '.menu, nav',
					'desc'    => __('Default: <code>.menu, nav</code>', 'kc-essentials')
				),
				array(
					'id'      => 'el_menu_children',
					'title'   => __('Children menu selectors', 'kc-essentials'),
					'type'    => 'text',
					'default' => '.children, .sub-menu',
					'desc'    => __('Default: <code>.children, .sub-menu</code>', 'kc-essentials')
				),
				array(
					'id'      => 'el_active_menu',
					'title'   => __('Active menu item selectors', 'kc-essentials'),
					'type'    => 'text',
					'default' => '.current-menu-item, .current_page_item',
					'desc'    => __('Default: <code>.current-menu-item, .current_page_item</code>', 'kc-essentials')
				),
				array(
					'id'      => 'class_active_menu',
					'title'   => __('Active menu item classes', 'kc-essentials'),
					'type'    => 'text',
					'default' => 'current-menu-item current_page_item',
					'desc'    => __('Default: <code>current-menu-item current_page_item</code>', 'kc-essentials')
				),
				array(
					'id'      => 'el_active_others',
					'title'   => __('Active non-menu link selectors', 'kc-essentials'),
					'type'    => 'text',
					'default' => '.current, .active',
					'desc'    => __('Default: <code>.current, .active</code>', 'kc-essentials')
				),
				array(
					'id'      => 'class_active_others',
					'title'   => __('Active non-menu link classes', 'kc-essentials'),
					'type'    => 'text',
					'default' => 'current active',
					'desc'    => __('Default: <code>current active</code>', 'kc-essentials')
				)
			)
		);



		# The entry for KC Settings
		$kcss_settings = array(
			'prefix'       => 'kc_essentials',
			'menu_title'   => 'KC Essentials',
			'page_title'   => __('KC Essentials Settings', 'kc-essentials'),
			'display'      => 'metabox',
			'options'      => $sections,
			'load_actions' => array(__CLASS__, 'load_actions'),
			'help'         => array(
				array(
					'id'      => 'history_js',
					'title'   => __('History.js', 'kc-essentials'),
					'content' => '
						<h4>'.__('Excluded link selectors', 'kc-essentials').'</h4>
						<p>'.__('Comma separated list of jQuery selectors for link elements that shouldn&#39;t be ajaxified, default is <code>#comment-popup-link</code>', 'kc-essentials').'</p>
						<h4>'.__('Excluded URLs', 'kc-essentials').'</h4>
						<p>'.__('Comma separated list of URL parts that shouldn&#39;t be ajaxified, default is <code>wp-admin, feed</code>', 'kc-essentials').'</p>
						<h4>'.__('Content selectors', 'kc-essentials').'</h4>
						<p>'.__('Comma separated list of jQuery selectors for the content element, default is <code>#page, #main, #content, article:first, .article:first, .post:first</code>', 'kc-essentials').'</p>
						<h4>'.__('Menu selectors', 'kc-essentials').'</h4>
						<p>'.__('Comma separated list of jQuery selectors for the menu elements, default is <code>.menu, nav</code>', 'kc-essentials').'</p>
						<h4>'.__('Children menu selectors', 'kc-essentials').'</h4>
						<p>'.__('Comma separated list of jQuery selectors for the children menu elements, default is <code>.sub-menu, .children</code>.', 'kc-essentials').'</p>
						<h4>'.__('Active menu item selectors', 'kc-essentials').'</h4>
						<p>'.__('Comma separated list of additional jQuery selectors for active menu item, default is <code>.current-menu-item</code>.', 'kc-essentials').'</p>
						<h4>'.__('Active menu item classes', 'kc-essentials').'</h4>
						<p>'.__('<b>Space</b> separated list of additional active menu classes. default is <code>current-menu-item</code>.', 'kc-essentials').'</p>
						<h4>'.__('Active non-menu link selectors', 'kc-essentials').'</h4>
						<p>'.__('Comma separated list of jQuery selectors for active non-menu link elements, default is <code>.current, .active</code>.', 'kc-essentials').'</p>
						<h4>'.__('Active non-menu link classes', 'kc-essentials').'</h4>
						<p>'.__('<b>Space</b> separated list of active non-menu item classes.', 'kc-essentials').'</p>
					'
				)
			)
		);

		$settings[] = $kcss_settings;
		return $settings;
	}


	public static function load_actions() {
		self::$db_value = kc_get_option( 'kc_essentials' );
		wp_enqueue_style( 'kc-essentials' );
		wp_enqueue_script( 'kc-essentials' );
	}


	public static function cb_section_widget_areas( $args ) {
		if ( !isset(self::$db_value['widget_areas']) || empty(self::$db_value['widget_areas']) ) {
			$sidebars = array( array(
				'hidden'        => true,
				'id'            => '',
				'name'          => '',
				'description'   => '',
				'before_widget' => '<div id="%1$s" class="widget %2$s">',
				'after_widget'  => '</div>',
				'before_title'  => '<h3 class="widget-title">',
				'after_title'   => '</h3>',
			) );
		}
		else {
			$sidebars = self::$db_value['widget_areas'];
		}

		$fields = array(
			'id'            => __( 'ID', 'kc-essentials'),
			'name'          => __( 'Name', 'kc-essentials'),
			'description'   => __( 'Description', 'kc-essentials'),
			'before_widget' => __( 'Before widget', 'kc-essentials'),
			'after_widget'  => __( 'After widget', 'kc-essentials'),
			'before_title'  => __( 'Before title', 'kc-essentials'),
			'after_title'   => __( 'After title', 'kc-essentials')
		);

		$out  = "<p>".__('<em>ID</em> and <em>Name</em> are mandatory, everything else is optional. Careful with the before/after args!', 'kc_essentials')."</p>\n";
		$out .= "<ul class='kc-rows kc-sortable kcsse-cwa'>\n";
		foreach ( $sidebars as $idx => $sb ) {
			$out .= "\t<li class='row' data-mode='widget_areas'>\n\t\t<ul";
			if ( isset($sb['hidden']) ) {
				unset($sb['hidden']);
				$out .= " class='hidden'";
			}
			$out .=">\n";
			foreach ( $fields as $field_id => $field_label ) {
				$out .= "\t\t\t<li><label for='{$args['field_id']}-$idx-{$field_id}'>{$field_label}</label>";
				$out .= kcForm::field(array(
					'type'    => ( $field_id == 'description' ) ? 'textarea' : 'text',
					'attr'    => array(
						'id'             => "{$args['field_id']}-$idx-{$field_id}",
						'name'           => "{$args['field_name']}[{$idx}][$field_id]",
						'class'          => in_array($field_id, array('id', 'name')) ? 'check widefat' : 'widefat',
						'data-nocleanup' => !in_array($field_id, array('id', 'name', 'description')) ? 'true' : 'false'
					),
					'current' => isset($sb[$field_id]) ? $sb[$field_id] : ''
				)) . "</li>\n";
			}
			$out .= "\t</ul>\t\t<p class='actions'><a class='add'>".__('Add', 'kc-essentials')."</a> <a class='del'>Remove</a></p>\n</li>\n";
		}
		$out .= "</ul>\n";

		echo $out;
	}


	public static function sanitize_widget_areas( $sidebars ) {
		$checks = array( 'id', 'name' );
		foreach ( $sidebars as $idx => $sb ) {
			foreach ( $checks as $c ) {
				if ( !isset($sb[$c]) || empty($sb[$c]) ) {
					unset( $sidebars[$idx] );
					continue 2;
				}
			}
		}

		return $sidebars;
	}
}
kcEssentials_options::init();

?>
