<?php

/**
 * @package KC_Essentials
 * @version 0.1
 */


class kc_widget_shortcode extends WP_Widget {
	var $defaults;

	function __construct() {
		$widget_ops = array( 'classname' => 'kcw_shortcode', 'description' => __('Shortcode', 'kc-essentials') );
		$control_ops = array( 'width' => 200, 'height' => 350 );
		parent::__construct( 'kcw_shortcode', 'KC Shortcode', $widget_ops, $control_ops );
		$this->defaults = array(
			'title'     => '',
			'shortcode' => '',
			'debug'     => 0
		);
	}


	function update( $new, $old ) {
		return $new;
	}


	function form( $instance ) {
		$instance = wp_parse_args( (array) $instance, $this->defaults );
		$title    = strip_tags( $instance['title'] );
	?>
		<p>
			<label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:') ?></label>
			<?php echo kcForm::input(array(
				'attr'    => array('id' => $this->get_field_id('title'), 'name' => $this->get_field_name('title'), 'class' => 'widefat'),
				'current' => $title
			)) ?>
		</p>
		<p>
			<label for="<?php echo $this->get_field_id('shortcode'); ?>"><?php _e('Shortcode', 'kc-essentials') ?></label>
			<?php echo kcForm::textarea(array(
				'attr'    => array('id' => $this->get_field_id('shortcode'), 'name' => $this->get_field_name('shortcode'), 'class' => 'widefat'),
				'current' => $instance['shortcode']
			)) ?>
		</p>
	<?php }


	function widget( $args, $instance ) {
		if ( !$instance['shortcode'] )
			return;

		$output  = $args['before_widget'];
		if ( $title = apply_filters( 'widget_title', $instance['title'] ) )
			$output .= $args['before_title'] . $title . $args['after_title'];
		$output .= do_shortcode( $instance['shortcode'] );
		$output .= $args['after_widget'];

		echo $output;
	}


	public static function kcml_fields( $widgets ) {
		$widgets['widget_kcw_shortcode'] = array(
			array(
				'id'    => 'title',
				'type'  => 'text',
				'label' => __('Title')
			),
			array(
				'id'    => 'shortcode',
				'type'  => 'textarea',
				'label' => __('Shortcode', 'kc-essentials')
			)
		);

		return $widgets;
	}
}
add_filter( 'kcml_widget_fields' , array('kc_widget_shortcode', 'kcml_fields') );

?>
