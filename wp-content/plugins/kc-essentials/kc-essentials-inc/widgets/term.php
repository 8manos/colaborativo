<?php

/**
 * @package KC_Essentials
 * @version 0.1
 */


class kc_widget_term extends WP_Widget {
	var $defaults;

	function __construct() {
		$widget_ops = array( 'classname' => 'kcw_term', 'description' => __('Display a list of taxonomy terms', 'kc-essentials') );
		$control_ops = array( 'width' => 275 );
		parent::__construct( 'kcw_term', 'KC Terms', $widget_ops, $control_ops );
		$this->defaults = array(
			'title'       => '',
			'taxonomy'    => 'category',
			'orderby'     => 'name',
			'order'       => 'ASC',
			'misc'        => array( 'hierarchical', 'hide_empty' ),
			'debug'       => 0
		);
	}


	function update( $new, $old ) {
		//echo '<pre>'.print_r( $new, true).'</pre>';exit;
		return $new;
	}


	function form( $instance ) {
		$instance = wp_parse_args( (array) $instance, $this->defaults );
		$title    = strip_tags( $instance['title'] );
	?>
		<p>
			<label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:'); ?></label>
			<input type="text" class="widefat" id="<?php echo $this->get_field_id('title') ?>" name="<?php echo $this->get_field_name('title') ?>" value="<?php echo $title ?>" />
		</p>

		<ul class="kcw-control-block">
			<li>
				<label for="<?php echo $this->get_field_id('taxonomy'); ?>"><?php _e('Taxonomy', 'kc-essentials') ?></label>
				<?php echo kcForm::field(array(
					'type'    => 'select',
					'attr'    => array('id' => $this->get_field_id('taxonomy'), 'name' => $this->get_field_name('taxonomy')),
					'options' => kcSettings_options::$taxonomies,
					'current' => $instance['taxonomy'],
					'none'    => false
				)) ?>
			</li>
			<li>
				<label for="<?php echo $this->get_field_id('orderby'); ?>"><?php _e('Order by', 'kc-essentials') ?></label>
				<?php echo kcForm::field(array(
					'type'    => 'select',
					'attr'    => array('id' => $this->get_field_id('orderby'), 'name' => $this->get_field_name('orderby')),
					'options' => array( 'name' => __('Name', 'kc-essentials'), 'ID' => 'ID'),
					'current' => $instance['orderby'],
					'none'    => false
				)) ?>
			</li>
			<li>
				<label for="<?php echo $this->get_field_id('order'); ?>"><?php _e('Order', 'kc-essentials') ?></label>
				<?php echo kcForm::field(array(
					'type'    => 'select',
					'attr'    => array('id' => $this->get_field_id('order'), 'name' => $this->get_field_name('order')),
					'options' => array( 'name' => __('Ascending', 'kc-essentials'), 'ID' => __('Descending', 'kc-essentials') ),
					'current' => $instance['order'],
					'none'    => false
				)) ?>
			</li>
			<li>
				<label><?php _e('Misc.', 'kc-essentials') ?></label>
				<div class="checks">
					<?php echo kcForm::field(array(
						'type'    => 'checkbox',
						'attr'    => array('id' => $this->get_field_id('misc'), 'name' => $this->get_field_name('misc').'[]'),
						'options' => array(
							'hierarchical'       => __('Hierarchical', 'kc-essentials'),
							'hide_empty'         => __('Hide empty', 'kc-essentials'),
							'show_count'         => __('Show count', 'kc-essentials'),
							'use_desc_for_title' => __('Use desc. for title', 'kc-essentials')
						),
						'current' => $instance['misc']
					)) ?>
				</div>
			</li>
		</ul>
	<?php }


	function widget( $args, $instance ) {
		$misc = array(
			'hierarchical'       => false,
			'hide_empty'         => false,
			'show_count'         => false,
			'use_desc_for_title' => false,
			'echo'               => false,
			'title_li'           => ''
		);
		if ( isset($instance['misc']) && !empty($instance['misc']) ) {
			$_misc = $instance['misc'];
			unset( $instance['misc'] );

			foreach ( $_misc as $m )
				$misc[$m] = true;
		}
		$instance += $misc;

		$list = wp_list_categories( $instance );
		if ( !$list )
			return;

		$output  = $args['before_widget'];
		if ( $title = apply_filters( 'widget_title', $instance['title'] ) )
			$output .= $args['before_title'] . $title . $args['after_title'];
		$output .= "<ul>\n{$list}</ul>\n";
		$output .= $args['after_widget'];

		echo $output;
	}


	public static function kcml_fields( $widgets ) {
		$widgets['widget_kcw_term'] = array(
			array(
				'id'    => 'title',
				'type'  => 'text',
				'label' => __('Title')
			)
		);

		return $widgets;
	}
}
add_filter( 'kcml_widget_fields', array('kc_widget_term', 'kcml_fields') );

?>
