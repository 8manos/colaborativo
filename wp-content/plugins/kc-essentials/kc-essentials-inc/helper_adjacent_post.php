<?php

class KC_Adjacent_Post {
	protected static $data;


	public static function get( $args = array() ) {
		$defaults = array(
			'current'     => '',
			'previous'    => true,
			'in_same_tax' => false
		);

		$args = wp_parse_args( (array) $args, $defaults );
		extract( $args, EXTR_OVERWRITE );

		# Get the current post
		if ( !$current ) {
			global $post;
			$current = $post;
		}
		elseif ( is_int($current) ) {
			$current = get_post( $current );
		}

		# Abort?
		if ( !is_object($current) )
				return false;

		self::$data['date_current'] = $current->post_date;
		if ( $previous ) {
			self::$data['date_compare'] = '<';
			$order = 'DESC';
			$pos = 'prev';
		} else {
			self::$data['date_compare'] = '>';
			$order = 'ASC';
			$pos = 'next';
		}

		$q_args = array(
			'order'          => $order,
			'orderby'        => 'date',
			'post_type'      => $current->post_type,
			'post_status'    => ( $current->post_type == 'attachment' ) ? 'inherit' : 'publish',
			'posts_per_page' => 1
		);

		# tax query
		$taxonomies = get_post_taxonomies( $current->ID );
		if ( !empty($taxonomies) && $in_same_tax ) {
			$tax_query = array( 'relation' => 'AND' );
			foreach ( $taxonomies as $tax ) {
				$terms = wp_get_object_terms( $current->ID, $tax, array('fields' => 'ids') );
				if ( empty($terms) )
					continue;

				$tax_query[] = array(
					'taxonomy' => $tax,
					'operator' => 'IN',
					'field'    => 'id',
					'terms'    => $terms
				);
			}

			if ( !empty($tax_query) ) {
				$tax_query['relation'] = 'AND';
				$q_args['tax_query'] = $tax_query;
			}
		}

		add_filter( 'posts_where', array(__CLASS__, '_filter_date') );
		$query = new WP_Query( $q_args );
		remove_filter( 'posts_where', array(__CLASS__, '_filter_date') );

		$output = array();
		if ( $query->have_posts() ) {
			while ( $query->have_posts() ) {
				$query->the_post();
				$output[] = array(
					'id'    => get_the_ID(),
					'title' => get_the_title(),
					'url'   => get_permalink(),
					'pos'   => $pos
				);
			}
		}
		wp_reset_query();

		return $output;
	}


	public static function _filter_date( $where) {
		$where .= " AND post_date ".self::$data['date_compare']." '".self::$data['date_current']."'";
		return $where;
	}
}

?>
