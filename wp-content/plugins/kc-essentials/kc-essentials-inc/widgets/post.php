<?php

/**
 * @package KC_Essentials
 * @version 0.1
 */


class kc_widget_post extends WP_Widget {

	var $defaults;

	function __construct() {
		$widget_ops = array( 'classname' => 'kcw_post', 'description' => __('Query posts as you wish', 'kc-essentials') );
		$control_ops = array( 'width' => 200, 'height' => 350 );
		$this->defaults = array(
			'title'             => '',
			'post_type'         => array(),
			'post_status'       => array('publish'),
			'posts_per_page'    => get_option('posts_per_page'),
			'post_parent'       => '',
			'include'           => '',
			'exclude'           => '',
			'offset'            => '',
			'posts_order'       => 'DESC',
			'posts_orderby'     => 'date',
			'meta_key'          => '',
			'tax_query'         => array(
				'relation' => '',
				array(
					'taxonomy' => '',
					'terms'    => '',
					'field'    => 'slug',
					'operator' => ''
				)
			),
			'meta_query'        => array(
				array(
					'key'     => '',
					'value'   => '',
					'type'    => 'CHAR',
					'compare' => '='
				)
			),
			'posts_wrapper'     => '',
			'posts_class'       => '',
			'entry_wrapper'     => 'div',
			'entry_class'       => '',
			'title_src'         => 'default',
			'title_meta'        => '',
			'title_tag'         => 'h4',
			'title_link'        => 'default',
			'title_link_meta'   => '',
			'title_class'       => 'title',
			'content_src'       => 'excerpt',
			'content_wrapper'   => '',
			'content_class'     => '',
			'content_meta'      => '',
			'thumb_size'        => '',
			'thumb_src'         => '',
			'thumb_meta'        => '',
			'thumb_link'        => 'post',
			'thumb_link_meta'   => '',
			'thumb_link_custom' => '',
			'more_link'         => '',
			'index_link'        => '',
			'action_id'         => '',
			'debug'             => false,
			'txt_before_loop'   => '',
			'txt_after_loop'    => '',
			'txt_autop'         => 0
		);

		parent::__construct( 'kcw_post', 'KC Posts', $widget_ops, $control_ops );
	}


	function update( $new, $old ) {
		# Numberposts
		if ( !is_numeric($new['posts_per_page']) )
			$new['posts_per_page'] = get_option('posts_per_page');

		# Post parent
		if ( strlen($new['post_parent']) && $new['post_parent'] !== '%current%' ) {
			$parent_id = absint( $new['post_parent'] );
			$new['post_parent'] = $parent_id ? $parent_id : '';
		}

		# Post type
		if ( !isset($new['post_type']) )
			$new['post_type'] = array();

		# Offset
		if ( !isset($new['offset']) ) {
			$offset = absint( $new['offset'] );
			$new['offset'] = $offset ? $offset : '';
		}

		# Post status
		## Media/attachment needs the 'inherit' status so force it when the 'attachment' post type is checked
		if ( !empty($new['post_type']) && in_array('attachment', $new['post_type']) && !in_array('inherit', $new['post_status']) )
			$new['post_status'][] = 'inherit';

		if ( empty($new['post_status']) )
			$new['post_status'] = array('publish');

		# Tax query
		$tax_query = $new['tax_query'];
		$tax_rel = $tax_query['relation'];
		unset( $tax_query['relation'] );

		$tax_queries = array();
		foreach ($tax_query as $tq ) {
			if ( !empty($tq['taxonomy']) && !empty($tq['terms']) )
				$tax_queries[] = $tq;
		}
		if ( empty($tax_queries) ) {
			$new['tax_query'] = array(
				'relation' => '',
				array(
					'taxonomy' => '',
					'terms'    => '',
					'field'    => 'slug',
					'operator' => ''
				)
			);
		}
		else {

			if ( count($tax_queries) > 1 && empty($tax_rel) )
				$tax_queries['relation'] = 'OR';
			else
				$tax_queries['relation'] = $tax_rel;

			$new['tax_query'] = $tax_queries;
		}

		# Meta query
		$meta_query = array();
		foreach ( $new['meta_query'] as $mq )
			if ( !empty($mq['key']) )
				$meta_query[] = $mq;
		if ( !empty($meta_query) )
			$new['meta_query'] = $meta_query;
		else
			$new['meta_query'] = array(
				array(
					'key'     => '',
					'value'   => '',
					'type'    => 'CHAR',
					'compare' => '='
				)
			);

		# Fix class names
		foreach ( array('posts', 'entry', 'title', 'content') as $el )
			if ( isset($new["{$el}_class"]) && !empty($new["{$el}_class"]) )
				$new["{$el}_class"] = kc_essentials_sanitize_html_classes( $new["{$el}_class"] );

		return $new;
	}


	function form( $instance ) {
		$instance = wp_parse_args( (array) $instance, $this->defaults );
		$title = strip_tags( $instance['title'] );

		# Options
		$post_types = kcSettings_options::$post_types;
		$post_statuses = kcSettings_options::$post_statuses;

		$taxonomies = $terms = array();
		foreach ( get_taxonomies( array('show_ui' => true), 'objects' ) as $t ) {
			$taxonomies[$t->name] = array( 'value' => $t->name, 'label' => $t->label );

			$terms[$t->name] = array();
			if ( $t->name == 'post_format' ) {
				if ( current_theme_supports( 'post-formats' ) ) {
					$formats = get_theme_support('post-formats');
					if ( is_array($formats[0]) ) {
						foreach ( $formats[0] as $format )
							$terms[$t->name][$format] = get_post_format_string( $format );
					}
					else {
						unset( $taxonomies[$t->name] );
						unset( $terms[$t->name] );
					}
				}
				else {
					unset( $taxonomies[$t->name] );
					unset( $terms[$t->name] );
				}
			}
			else {
				$t_terms = get_terms( $t->name );
				if ( !empty($t_terms) ) {
					foreach ( $t_terms as $tt )
						$terms[$t->name][$tt->slug] = array('value' => $tt->slug, 'label' => $tt->name);
				}
			}
		}

		$relations = array(
			array( 'value' => 'AND', 'label'	=> 'AND'),
			array( 'value' => 'OR',  'label'	=> 'OR')
		);
		$operators = array(
			array( 'value' => 'IN',          'label' => 'IN' ),
			array( 'value' => 'NOT IN',      'label' => 'NOT IN' ),
			array( 'value' => 'LIKE',        'label' => 'LIKE' ),
			array( 'value' => 'NOT LIKE',    'label' => 'NOT LIKE' ),
			array( 'value' => 'BETWEEN',     'label' => 'BETWEEN' ),
			array( 'value' => 'NOT BETWEEN', 'label' => 'NOT BETWEEN' )
		);
		$meta_compare = array(
			array( 'value' => '=',  'label' => '=' ),
			array( 'value' => '!=', 'label' => '!=' ),
			array( 'value' => '>',  'label' => '>' ),
			array( 'value' => '>=', 'label' => '>=' ),
			array( 'value' => '<',  'label' => '<' ),
			array( 'value' => '<=', 'label' => '<=' )
		);
		$meta_type = array(
			array( 'value' => 'BINARY',   'label'=> 'Binary' ),
			array( 'value' => 'CHAR',     'label'=> 'Char' ),
			array( 'value' => 'DATE',     'label'=> 'Date' ),
			array( 'value' => 'DATETIME', 'label'=> 'Datetime' ),
			array( 'value' => 'DECIMAL',  'label'=> 'Decimal' ),
			array( 'value' => 'NUMERIC',  'label'=> 'Numeric' ),
			array( 'value' => 'SIGNED',   'label'=> 'Signed' ),
			array( 'value' => 'UNSIGNED', 'label'=> 'Unsigned' ),
			array( 'value' => 'TIME',     'label'=> 'Time' )
		);

		$image_sizes = kcSettings_options::$image_sizes;
		$src_common = array(
			'default' => __('Default', 'kc-essentials'),
			'meta'    => __('Custom field', 'kc-essentials')
		);
		$src_content = array(
			'excerpt' => __('Excerpt', 'kc-essentials'),
			'content' => __('Full Content', 'kc-essentials'),
			'meta'    => __('Custom field', 'kc-essentials')
		);
		$src_thumb_link = array(
			'post'       => __('Post page', 'kc-essentials'),
			'media_page' => __('Attachment page', 'kc-essentials'),
			'media_file' => __('Attachment file', 'kc-essentials'),
			'meta-post'  => __('Post custom field', 'kc-essentials'),
			'meta-att'   => __('Thumb. custom field', 'kc-essentials'),
			'custom'     => __('Custom URL', 'kc-essentials')
		);
		$tags_posts = array(
			'div'     => 'div',
			'section' => 'section',
			'ol'      => 'ol',
			'ul'      => 'ul'
		);
		$tags_title = array(
			'h2'   => 'h2',
			'h3'   => 'h3',
			'h4'   => 'h4',
			'h5'   => 'h5',
			'h6'   => 'h6',
			'p'    => 'p',
			'span' => 'span',
			'div'  => 'div'
		);
		$tags_content = array(
			'div'        => 'div',
			'article'    => 'article',
			'blockquote' => 'blockquote'
		);
		$tags_entry = array(
			'div'     => 'div',
			'article' => 'article',
			'li'      => 'li'
		);
		?>

		<p>
			<label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:'); ?></label>
			<input type="text" class="widefat" id="<?php echo $this->get_field_id('title') ?>" name="<?php echo $this->get_field_name('title') ?>" value="<?php echo $title ?>" />
		</p>

		<details open="true">
			<summary><?php _e('Basic', 'kc-essentials') ?></summary>
			<ul class="kcw-control-block">
				<li>
					<label for="<?php echo $this->get_field_id('posts_per_page'); ?>" title="<?php _e("Use -1 to show all posts") ?>"><?php _e('Count', 'kc-essentials') ?> <small class="impo">(?)</small></label>
					<?php echo kcForm::input(array(
						'attr'    => array('id' => $this->get_field_id('posts_per_page'), 'name' => $this->get_field_name('posts_per_page')),
						'current' => $instance['posts_per_page']
					)) ?>
				</li>
				<li>
					<label for="<?php echo $this->get_field_id('post_parent'); ?>" title="<?php _e("Parent post, use %current% to get currently viewed post ID, or double click to search.") ?>"><?php _e('Parent', 'kc-essentials') ?> <small class="impo">(?)</small></label>
					<?php echo kcForm::input(array(
						'attr'    => array(
							'id'    => $this->get_field_id('post_parent'),
							'name'  => $this->get_field_name('post_parent'),
							'class' => 'kc-find-post unique'
						),
						'current' => $instance['post_parent']
					)) ?>
				</li>
				<li>
					<label for="<?php echo $this->get_field_id('include'); ?>" title="<?php _e('Separate post IDs with commas, double click to search.') ?>"><?php _e('Incl. IDs', 'kc-essentials') ?> <small class="impo">(?)</small></label>
					<?php echo kcForm::input(array(
						'attr'    => array(
							'id'    => $this->get_field_id('include'),
							'name'  => $this->get_field_name('include'),
							'class' => 'kc-find-post'
						),
						'current' => $instance['include']
					)) ?>
				</li>
				<li>
					<label for="<?php echo $this->get_field_id('exclude'); ?>" title="<?php _e('Separate post IDs with commas, double click to search.') ?>"><?php _e('Excl. IDs', 'kc-essentials') ?> <small class="impo">(?)</small></label>
					<?php echo kcForm::input(array(
						'attr'    => array(
							'id'    => $this->get_field_id('exclude'),
							'name'  => $this->get_field_name('exclude'),
							'class' => 'kc-find-post'
						),
						'current' => $instance['exclude']
					)) ?>
				</li>
				<li>
					<label for="<?php echo $this->get_field_id('offset'); ?>"><?php _e('Offset', 'kc-essentials') ?></label>
					<?php echo kcForm::input(array(
						'attr'    => array('id' => $this->get_field_id('offset'), 'name' => $this->get_field_name('offset')),
						'current' => $instance['offset']
					)) ?>
				</li>
				<li>
					<label for="<?php echo $this->get_field_id('posts_order'); ?>"><?php _e('Order', 'kc-essentials') ?></label>
					<?php echo kcForm::select(array(
						'attr'    => array('id' => $this->get_field_id('posts_order'), 'name' => $this->get_field_name('posts_order')),
						'current' => $instance['posts_order'],
						'options' => array(
							array( 'value' => 'DESC', 'label' => __('Descending', 'kc-essentials') ),
							array( 'value' => 'ASC',  'label' => __('Ascending', 'kc-essentials') )
						),
						'none'		=> false
					)) ?>
				</li>
				<li>
					<label for="<?php echo $this->get_field_id('posts_orderby'); ?>"><?php _e('Order by', 'kc-essentials') ?></label>
					<?php echo kcForm::select(array(
						'attr' => array(
							'id'         => $this->get_field_id('posts_orderby'),
							'name'       => $this->get_field_name('posts_orderby'),
							'class'      => 'hasdep',
							'data-child' => '#p-'.$this->get_field_id('meta_key')
						),
						'current' => $instance['posts_orderby'],
						'options' => array(
							array( 'value' => 'date',           'label' => __('Publish date', 'kc-essentials') ),
							array( 'value' => 'ID',             'label' => __('ID', 'kc-essentials') ),
							array( 'value' => 'title',          'label' => __('Title', 'kc-essentials') ),
							array( 'value' => 'author',         'label' => __('Author', 'kc-essentials') ),
							array( 'value' => 'modified',       'label' => __('Modification date', 'kc-essentials') ),
							array( 'value' => 'menu_order',     'label' => __('Menu order', 'kc-essentials') ),
							array( 'value' => 'parent',         'label' => __('Parent', 'kc-essentials') ),
							array( 'value' => 'comment_count',  'label' => __('Comment count', 'kc-essentials') ),
							array( 'value' => 'rand',           'label' => __('Random', 'kc-essentials') ),
							array( 'value' => 'post__in',       'label' => __('Included IDs', 'kc-essentials') ),
							array( 'value' => 'meta_value',     'label' => __('Meta value', 'kc-essentials') ),
							array( 'value' => 'meta_value_num', 'label'	=> __('Meta value num', 'kc-essentials') )
						),
						'none'    => false
					)) ?>
				</li>
				<li id="<?php echo 'p-'.$this->get_field_id('meta_key') ?>" data-dep='["meta_value", "meta_value_num"]'>
					<label for="<?php echo $this->get_field_id('meta_key') ?>" title="<?php _e("Fill this if you select 'Meta value' or 'Meta value num' above", 'kc-essentials') ?>"><?php _e('Meta key', 'kc-essentials') ?> <small class="impo">(?)</small></label>
					<?php echo kcForm::input(array(
						'attr'    => array('id' => $this->get_field_id('meta_key'), 'name' => $this->get_field_name('meta_key') ),
						'current' => $instance['meta_key']
					)) ?>
				</li>
			</ul>
		</details>

		<details<?php if ( !empty($instance['post_type']) ) echo ' open="true"' ?>>
			<summary><?php _e('Post types', 'kc-essentials') ?></summary>
			<div class="checks kcw-control-block post-types">
				<?php echo kcForm::field(array(
					'type'    => 'checkbox',
					'attr'    => array('id' => $this->get_field_id('post_type'), 'name' => $this->get_field_name('post_type').'[]'),
					'current' => $instance['post_type'],
					'options' => $post_types
				)) ?>
			</div>
		</details>

		<details<?php if ( $instance['post_status'] !== $this->defaults['post_status'] ) echo ' open="true"' ?>>
			<summary><?php _e('Post status', 'kc-essentials') ?></summary>
			<div class="checks kcw-control-block post-status">
				<?php echo kcForm::field(array(
					'type'    => 'checkbox',
					'attr'    => array('id' => $this->get_field_id('post_status'), 'name' => $this->get_field_name('post_status').'[]'),
					'current' => $instance['post_status'],
					'options' => $post_statuses
				)) ?>
			</div>
		</details>

		<?php
			$tq_id = $this->get_field_id('tax_query');
			$tq_name = $this->get_field_name('tax_query');

			$tq_values = $instance['tax_query'];
			$tq_rel = $tq_values['relation'];
			unset( $tq_values['relation'] );
		?>
		<details<?php if ( $instance['tax_query'] !== $this->defaults['tax_query'] ) echo ' open="true"' ?>>
			<summary><?php _e('Taxonomies', 'kc-essentials') ?></summary>
			<ul class="kcw-control-block taxonomies">
				<li class="relation">
					<label for="<?php echo "{$tq_id}-relation" ?>"><?php _e('Relation', 'kc-essentials') ?></label>
					<?php echo kcForm::select(array(
						'attr'    => array('id' => "{$tq_id}-relation", 'name' => "{$tq_name}[relation]"),
						'current' => $tq_rel,
						'options' => $relations,
						'none'    => false
					)) ?>
				</li>
				<li>
					<ul class="tax-queries">
						<?php foreach ( $tq_values as $idx => $query ) { ?>
						<li class="row">
							<label for="<?php echo "{$tq_id}-{$idx}-taxonomy" ?>"><?php _e('Taxonomy', 'kc-essentials') ?></label>
							<?php echo kcForm::select(array(
								'attr'    => array(
									'id'         => "{$tq_id}-{$idx}-taxonomy",
									'name'       => "{$tq_name}[{$idx}][taxonomy]",
									'class'      => 'hasdep',
									'data-scope' => 'li.row',
									'data-child' => '.terms'
								),
								'current' => $query['taxonomy'],
								'options' => $taxonomies
							)) ?>
							<label for="<?php echo "{$tq_id}-{$idx}-operator" ?>"><?php _e('Operator', 'kc-essentials') ?></label>
							<?php echo kcForm::select(array(
								'attr'    => array('id' => "{$tq_id}-{$idx}-operator", 'name' => "{$tq_name}[{$idx}][operator]"),
								'current' => $query['operator'],
								'options' => $operators,
								'none'    => false
							)) ?>
							<label><?php _e('Terms', 'kc-essentials') ?></label>
							<p class='checks terms hide-if-js info' data-dep=''><?php _e('Please select a taxonomy above to see its terms.', 'kc-essentials') ?>
							<?php if ( !empty($terms) ) { foreach ( $terms as $tax_name => $tax_terms ) { ?>
							<h6 class='hide-if-js'><?php echo $taxonomies[$tax_name]['label'] ?></h6>
							<div class='checks terms hide-if-js' data-dep='<?php echo $tax_name ?>'>
							<?php  if ( !empty($terms[$tax_name]) ) {
								echo kcForm::checkbox(array(
									'attr'    => array('name' => "{$tq_name}[{$idx}][terms][]", 'class' => 'term'),
									'current' => $query['terms'],
									'options' => $tax_terms
								));
							} else {
								echo "\t<p>".__("This taxonomy doesn't have any term with posts.", 'kc-essentials')."</p>\n\n";
							} ?>
							</div>
							<?php } } ?>
							<a class="hide-if-no-js rm action" rel="tax_query" title="<?php _e('Remove this taxonomy query', 'kc-essentials') ?>"><?php _e('Remove', 'kc-essentials') ?></a>
							<input type='hidden' name="<?php echo "{$tq_name}[{$idx}][field]" ?>" value="slug"/>
						</li>
						<?php } ?>
						<li><a class="hide-if-no-js add action" rel="tax_query" title="<?php _e('Add new taxonomy query', 'kc-essentials') ?>"><?php _e('Add', 'kc-essentials') ?></a></li>
					</ul>
				</li>
			</ul>
		</details>

		<?php
			$mq_name = $this->get_field_name('meta_query');
			$mq_id = $this->get_field_id('meta_query');
		?>
		<details<?php if ( $instance['meta_query'] !== $this->defaults['meta_query'] ) echo ' open="true"' ?>>
			<summary><?php _e('Metadata', 'kc-essentials') ?></summary>
			<ul class="kcw-control-block metadata">
				<li>
					<ul class="meta-queries">
						<?php foreach ( $instance['meta_query'] as $mq_idx => $mq ) { ?>
						<li class="row">
							<label for="<?php echo "{$mq_id}-{$mq_idx}-key" ?>"><?php _e('Key', 'kc-essentials') ?></label>
							<?php echo kcForm::input(array(
								'attr'    => array('id' => "{$mq_id}-{$mq_idx}-key", 'name' => "{$mq_name}[{$mq_idx}][key]"),
								'current' => $mq['key']
							)) ?>
							<label for="<?php echo "{$mq_id}-{$mq_idx}-value" ?>"><?php _e('Value', 'kc-essentials') ?></label>
							<?php echo kcForm::input(array(
								'attr'    => array('id' => "{$mq_id}-{$mq_idx}-value", 'name' => "{$mq_name}[{$mq_idx}][value]"),
								'current' => $mq['value']
							)) ?>
							<label for="<?php echo "{$mq_id}-{$mq_idx}-compare" ?>"><?php _e('Compare', 'kc-essentials') ?></label>
							<?php echo kcForm::select(array(
								'attr'    => array('id' => "{$mq_id}-{$idx}-compare", 'name' => "{$mq_name}[{$mq_idx}][compare]"),
								'current' => $mq['compare'],
								'options' => array_merge($meta_compare, $operators),
								'none'    => false
							)) ?>
							<label for="<?php echo "{$mq_id}-{$mq_idx}-type" ?>"><?php _e('Type', 'kc-essentials') ?></label>
							<?php echo kcForm::select(array(
								'attr'    => array('id' => "{$mq_id}-{$mq_idx}-type", 'name' => "{$mq_name}[{$mq_idx}][type]"),
								'current' => $mq['type'],
								'options' => $meta_type,
								'none'    => false
							)) ?>
						<a class="hide-if-no-js rm action" rel="meta_query" title="<?php _e('Remove this taxonomy query', 'kc-essentials') ?>"><?php _e('Remove', 'kc-essentials') ?></a>
						</li>
						<?php } ?>
						<li><a class="hide-if-no-js add action" rel="meta_query" title="<?php _e('Add new meta query', 'kc-essentials') ?>"><?php _e('Add', 'kc-essentials') ?></a></li>
					</ul>
				</li>
			</ul>
		</details>

		<details<?php if ( $instance['posts_wrapper'] != '' ) echo ' open="true"' ?>>
			<summary><?php _e('Posts wrapper', 'kc-essentials') ?></summary>
			<ul class="kcw-control-block">
				<li>
					<label for="<?php echo $this->get_field_id('posts_wrapper') ?>"><?php _e('Tag', 'kc-essentials') ?></label>
					<?php echo kcForm::field(array(
						'type'    => 'select',
						'attr'    => array(
							'id'         => $this->get_field_id('posts_wrapper'),
							'name'       => $this->get_field_name('posts_wrapper'),
							'class'      => 'hasdep',
							'data-child' => '.chPosts',
							'data-scope' => 'ul'
						),
						'current' => $instance['posts_wrapper'],
						'options' => $tags_posts
					)) ?>
				</li>
				<li class="chPosts" data-dep='<?php echo json_encode(array_keys($tags_posts)) ?>'>
					<label for="<?php echo $this->get_field_id('posts_class') ?>"><?php _e('Class', 'kc-essentials') ?></label>
					<?php echo kcForm::input(array(
						'attr'    => array('id' => $this->get_field_id('posts_class'), 'name' => $this->get_field_name('posts_class')),
						'current' => $instance['posts_class']
					)) ?>
				</li>
			</ul>
		</details>

		<details<?php if ( $instance['entry_wrapper'] != 'div' || $instance['entry_class'] ) echo ' open="true"' ?>>
			<summary><?php _e('Entry wrapper', 'kc-essentials') ?></summary>
			<ul class="kcw-control-block">
				<li>
					<label for="<?php echo $this->get_field_id('entry_wrapper') ?>"><?php _e('Tag', 'kc-essentials') ?></label>
					<?php echo kcForm::field(array(
						'type'    => 'select',
						'attr'    => array(
							'id'         => $this->get_field_id('entry_wrapper'),
							'name'       => $this->get_field_name('entry_wrapper'),
							'class'      => 'hasdep',
							'data-child' => '.chEntry',
							'data-scope' => 'ul'
						),
						'current' => $instance['entry_wrapper'],
						'options' => $tags_entry
					)) ?>
				</li>
				<li class="chEntry" data-dep='<?php echo json_encode(array_keys($tags_entry)) ?>'>
					<label for="<?php echo $this->get_field_id('entry_class') ?>"><?php _e('Class', 'kc-essentials') ?></label>
					<?php echo kcForm::input(array(
						'attr'    => array('id' => $this->get_field_id('entry_class'), 'name' => $this->get_field_name('entry_class')),
						'current' => $instance['entry_class']
					)) ?>
				</li>
			</ul>
		</details>

		<details<?php if ( !$this->value_is_default( array('title_src', 'title_tag', 'title_class', 'title_link'), $instance) ) echo ' open="true"' ?>>
			<summary><?php _e('Entry title', 'kc-essentials') ?></summary>
			<ul class="kcw-control-block">
				<li>
					<label for="<?php echo $this->get_field_id('title_src') ?>"><?php _e('Source', 'kc-essentials') ?></label>
					<?php echo kcForm::field(array(
						'type'    => 'select',
						'attr'    => array(
							'id'         => $this->get_field_id('title_src'),
							'name'       => $this->get_field_name('title_src'),
							'class'      => 'hasdep',
							'data-child' => '.chTitle',
							'data-scope' => 'ul'
						),
						'current' => $instance['title_src'],
						'options' => $src_common
					)) ?>
				</li>
				<li class="chTitle" data-dep='meta'>
					<label for="<?php echo $this->get_field_id('title_meta') ?>" title="<?php _e("Fill this if you select 'Custom field' above", 'kc-essentials') ?>"><?php _e('Meta key', 'kc-essentials') ?> <small class="impo">(?)</small></label>
					<?php echo kcForm::input(array(
						'attr'    => array('id' => $this->get_field_id('title_meta'), 'name' => $this->get_field_name('title_meta')),
						'current' => $instance['title_meta']
					)) ?>
				</li>
				<li class="chTitle" data-dep='<?php echo json_encode(array_keys($src_common) )?>'>
					<label for="<?php echo $this->get_field_id('title_tag') ?>"><?php _e('Tag', 'kc-essentials') ?></label>
					<?php echo kcForm::field(array(
						'type'    => 'select',
						'attr'    => array(
							'id'         => $this->get_field_id('title_tag'),
							'name'       => $this->get_field_name('title_tag'),
							'class'      => 'hasdep',
							'data-child' => '.chTitleTag',
							'data-scope' => 'ul'
						),
						'current' => $instance['title_tag'],
						'options' => $tags_title
					)) ?>
				</li>
				<li class="chTitleTag" data-dep='<?php echo json_encode(array_keys($tags_title)) ?>'>
					<label for="<?php echo $this->get_field_id('title_class') ?>"><?php _e('Class', 'kc-essentials') ?></label>
					<?php echo kcForm::input(array(
						'attr'    => array('id' => $this->get_field_id('title_class'), 'name' => $this->get_field_name('title_class')),
						'current' => $instance['title_class']
					)) ?>
				</li>
				<li class="chTitle" data-dep='<?php echo json_encode(array_keys($src_common) )?>'>
					<label for="<?php echo $this->get_field_id('title_link') ?>"><?php _e('Link', 'kc-essentials') ?></label>
					<?php echo kcForm::field(array(
						'type'    => 'select',
						'attr'    => array(
							'id'         => $this->get_field_id('title_link'),
							'name'       => $this->get_field_name('title_link'),
							'class'      => 'hasdep',
							'data-child' => '.chTitleLink',
							'data-scope' => 'ul'
						),
						'current' => $instance['title_link'],
						'options' => $src_common
					)) ?>
				</li>
				<li class="chTitleLink" data-dep='meta'>
					<label for="<?php echo $this->get_field_id('title_link_meta') ?>" title="<?php _e("Fill this if you select 'Custom field' above", 'kc-essentials') ?>"><?php _e('Meta key', 'kc-essentials') ?> <small class="impo">(?)</small></label>
					<?php echo kcForm::input(array(
						'attr'    => array('id' => $this->get_field_id('title_link_meta'), 'name' => $this->get_field_name('title_link_meta')),
						'current' => $instance['title_link_meta']
					)) ?>
				</li>
			</ul>
		</details>

		<details<?php if ( !$this->value_is_default( array('content_src', 'content_wrapper', 'more_link'), $instance ) ) echo ' open="true"' ?>>
			<summary><?php _e('Entry content', 'kc-essentials') ?></summary>
			<ul class="kcw-control-block">
				<li>
					<label for="<?php echo $this->get_field_id('content_src') ?>"><?php _e('Source', 'kc-essentials') ?></label>
					<?php echo kcForm::field(array(
						'type'    => 'select',
						'attr'    => array(
							'id'         => $this->get_field_id('content_src'),
							'name'       => $this->get_field_name('content_src'),
							'class'      => 'hasdep',
							'data-child' => '.contentSrc',
							'data-scope' => 'ul'
						),
						'current' => $instance['content_src'],
						'options' => $src_content
					)) ?>
				</li>
				<li class="contentSrc" data-dep='<?php echo json_encode(array('excerpt', 'content', 'meta')) ?>'>
					<label for="<?php echo $this->get_field_id('content_wrapper') ?>"><?php _e('Tag', 'kc-essentials') ?></label>
					<?php echo kcForm::field(array(
						'type'    => 'select',
						'attr'    => array(
							'id'         => $this->get_field_id('content_wrapper'),
							'name'       => $this->get_field_name('content_wrapper'),
							'class'      => 'hasdep',
							'data-child' => '.contentClass',
							'data-scope' => 'ul'
						),
						'current' => $instance['content_wrapper'],
						'options' => $tags_content
					)) ?>
				</li>
				<li class="contentClass" data-dep='<?php echo json_encode(array('div', 'article', 'blockquote')) ?>'>
					<label for="<?php echo $this->get_field_id('content_class') ?>"><?php _e('Class', 'kc-essentials') ?></label>
					<?php echo kcForm::input(array(
						'attr'    => array('id' => $this->get_field_id('content_class'), 'name' => $this->get_field_name('content_class')),
						'current' => $instance['content_class']
					)) ?>
				</li>
				<li class="contentSrc" data-dep='meta'>
					<label for="<?php echo $this->get_field_id('content_meta') ?>" title="<?php _e("Fill this if you select 'Custom field' above", 'kc-essentials') ?>"><?php _e('Meta key', 'kc-essentials') ?> <small class="impo">(?)</small></label>
					<?php echo kcForm::input(array(
						'attr'    => array('id' => $this->get_field_id('content_meta'), 'name' => $this->get_field_name('content_meta')),
						'current' => $instance['content_meta']
					)) ?>
				</li>
				<li class="contentSrc" data-dep='<?php echo json_encode(array('excerpt', 'content', 'meta')) ?>'>
					<label for="<?php echo $this->get_field_id('more_link') ?>" title="<?php _e("Fill this with some text if you want to have a 'more link' on each post", 'kc-essentials') ?>"><?php _e('More link', 'kc-essentials') ?> <small class="impo">(?)</small></label>
					<?php echo kcForm::input(array(
						'attr'    => array('id' => $this->get_field_id('more_link'), 'name' => $this->get_field_name('more_link')),
						'current' => $instance['more_link']
					)) ?>
				</li>
			</ul>
		</details>

		<?php if ( !empty($image_sizes) ) { ?>
		<details<?php if ( $instance['thumb_size'] != '' ) echo ' open="true"' ?>>
			<summary><?php _e('Thumbnail', 'kc-essentials') ?></summary>
			<ul class="kcw-control-block">
				<li>
					<label for="<?php echo $this->get_field_id('thumb_size') ?>"><?php _e('Size', 'kc-essentials') ?></label>
					<?php echo kcForm::field(array(
						'type'    => 'select',
						'attr'    => array(
							'id'         => $this->get_field_id('thumb_size'),
							'name'       => $this->get_field_name('thumb_size'),
							'class'      => 'hasdep',
							'data-child' => '.chThumb',
							'data-scope' => 'ul'
						),
						'current' => $instance['thumb_size'],
						'options' => $image_sizes
					)) ?>
				</li>
				<li class="chThumb" data-dep='<?php echo json_encode(array_keys($image_sizes)) ?>'>
					<label for="<?php echo $this->get_field_id('thumb_src') ?>"><?php _e('Source', 'kc-essentials') ?></label>
					<?php echo kcForm::field(array(
						'type'    => 'select',
						'attr'    => array(
							'id'         => $this->get_field_id('thumb_src'),
							'name'       => $this->get_field_name('thumb_src'),
							'class'      => 'hasdep',
							'data-child' => '#p-'.$this->get_field_id('thumb_meta')
						),
						'current' => $instance['thumb_src'],
						'options' => array( '' => __('Default', 'kc-essentials'), 'meta' => __('Custom field', 'kc-settings') ),
						'none'    => false
					)) ?>
				</li>
				<li id='p-<?php echo $this->get_field_id('thumb_meta') ?>' data-dep="meta">
					<label for="<?php echo $this->get_field_id('thumb_meta') ?>" title="<?php _e("Fill this if you select 'Custom field' above", 'kc-essentials') ?>"><?php _e('Meta key', 'kc-essentials') ?> <small class="impo">(?)</small></label>
					<?php echo kcForm::input(array(
						'attr'    => array('id' => $this->get_field_id('thumb_meta'), 'name' => $this->get_field_name('thumb_meta')),
						'current' => $instance['thumb_meta']
					)) ?>
				</li>
				<li class="chThumb" data-dep='<?php echo json_encode(array_keys($image_sizes)) ?>'>
					<label for="<?php echo $this->get_field_id('thumb_link') ?>"><?php _e('Link', 'kc-essentials') ?></label>
					<?php echo kcForm::field(array(
						'type'    => 'select',
						'attr'    => array(
							'id'         => $this->get_field_id('thumb_link'),
							'name'       => $this->get_field_name('thumb_link'),
							'class'      => 'hasdep',
							'data-child' => '.chThumbLink',
							'data-scope' => 'ul'
						),
						'current' => $instance['thumb_link'],
						'options' => $src_thumb_link,
					)) ?>
				</li>
				<li class="chThumbLink" data-dep='["meta-post", "meta-att"]'>
					<label for="<?php echo $this->get_field_id('thumb_link_meta') ?>" title="<?php _e("Fill this if you select 'Custom field' above", 'kc-essentials') ?>"><?php _e('Meta key', 'kc-essentials') ?> <small class="impo">(?)</small></label>
					<?php echo kcForm::input(array(
						'attr'    => array('id' => $this->get_field_id('thumb_link_meta'), 'name' => $this->get_field_name('thumb_link_meta')),
						'current' => $instance['thumb_link_meta']
					)) ?>
				</li>
				<li class="chThumbLink" data-dep='custom'>
					<label for="<?php echo $this->get_field_id('thumb_link_custom') ?>" title="<?php _e("Fill this if you select 'Custom URL' above. ALL thumbnails will link to this URL.", 'kc-essentials') ?>"><?php _e('URL', 'kc-essentials') ?> <small class="impo">(?)</small></label>
					<?php echo kcForm::input(array(
						'attr'    => array('id' => $this->get_field_id('thumb_link_custom'), 'name' => $this->get_field_name('thumb_link_custom')),
						'current' => $instance['thumb_link_custom']
					)) ?>
				</li>
			</ul>
		</details>
		<?php } ?>

		<details<?php if ( $instance['txt_before_loop'] || $instance['txt_after_loop'] ) echo ' open="true"' ?>>
			<summary><?php _e('Additional texts', 'kc-essentials') ?></summary>
			<ul class="kcw-control-block">
				<li>
					<label for="<?php echo $this->get_field_id('txt_before_loop') ?>"><?php _e('Before loop', 'kc-essentials') ?></label>
					<?php echo kcForm::textarea(array(
						'attr'    => array('id' => $this->get_field_id('txt_before_loop'), 'name' => $this->get_field_name('txt_before_loop'), 'class' => 'widefat'),
						'current' => $instance['txt_before_loop']
					)) ?>
				</li>
				<li>
					<label for="<?php echo $this->get_field_id('txt_after_loop') ?>"><?php _e('After loop', 'kc-essentials') ?></label>
					<?php echo kcForm::textarea(array(
						'attr'    => array('id' => $this->get_field_id('txt_after_loop'), 'name' => $this->get_field_name('txt_after_loop'), 'class' => 'widefat'),
						'current' => $instance['txt_after_loop']
					)) ?>
				</li>
				<li>
					<label for="<?php echo $this->get_field_id('txt_autop') ?>" title="<?php _e('Use wpautop() to automatically add paragraphs and new lines', 'kc-essentials') ?>"><?php _e('Format', 'kc-essentials') ?> <small class="impo">(?)</small></label>
					<?php echo kcForm::field(array(
						'type'    => 'select',
						'attr'    => array('id' => $this->get_field_id('txt_autop'), 'name' => $this->get_field_name('txt_autop')),
						'current' => $instance['txt_autop'],
						'options' => kcSettings_options::$yesno,
						'none'    => false
					)) ?>
				</li>
			</ul>
		</details>

		<details<?php if ( $instance['action_id'] || $instance['debug'] ) echo ' open="true"' ?>>
			<summary><?php _e('Advanced', 'kc-essentials') ?></summary>
			<ul class="kcw-control-block">
				<li>
					<label for="<?php echo $this->get_field_id('action_id') ?>" title="<?php _e('Please refer to the documentation about this', 'kc-essentials') ?>"><?php _e('Identifier', 'kc-essentials') ?> <small class="impo">(?)</small></label>
					<?php echo kcForm::input(array(
						'attr'    => array('id' => $this->get_field_id('action_id'), 'name' => $this->get_field_name('action_id')),
						'current' => $instance['action_id']
					)) ?>
				</li>
				<li>
					<label for="<?php echo $this->get_field_id('debug') ?>" title="<?php _e('Select Yes to view the widget options and query parameters on the frontend') ?>"><?php _e('Debug', 'kc-essentials') ?> <small class="impo">(?)</small></label>
					<?php echo kcForm::field(array(
						'type'    => 'select',
						'attr'    => array('id' => $this->get_field_id('debug'), 'name' => $this->get_field_name('debug')),
						'current' => $instance['debug'],
						'options' => kcSettings_options::$yesno,
						'none'    => false
					)) ?>
				</li>
			</ul>
		</details>
	<?php }


	function _sort_query_by_post_in( $sortby, $query ) {
		if ( isset($query->query['post__in']) && !empty($query->query['post__in']) && isset($query->query['orderby']) && $query->query['orderby'] == 'post__in' )
			$sortby = "find_in_set(ID, '" . implode( ',', $query->query['post__in'] ) . "')";

		return $sortby;
	}


	function widget( $args, $instance ) {
		$af_IDs = array( '', "-{$this->id}" );
		if ( $instance['action_id'] )
			$af_IDs[] = "-{$instance['action_id']}";
		$instance['af_IDs'] = $af_IDs;

		foreach ( $af_IDs as $af_id ) {
			$args['before_widget'] = apply_filters( "kc_widget-{$af_id}", $args['before_widget'], 'before_widget', 'widget_post' );
			$args['after_widget'] = apply_filters( "kc_widget-{$af_id}", $args['after_widget'], 'after_widget', 'widget_post' );
		}
		extract( $args );

		$debug  = "<h4>".__('KC Posts debug', 'kc-essentials')."</h4>\n";
		$debug .= "<h5>".__('Widget object', 'kc-essentials')."</summary>\n";
		$debug .= "<pre>".var_export($this, true)."</pre>";
		$debug .= "<h5>".__('Widget options', 'kc-essentials')."</summary>\n";
		$debug .= "<pre>".var_export($instance, true)."</pre>";
		$debug .= "<h5>".__('Query parameters', 'kc-essentials')."</summary>\n";

		$q_args = array(
			'posts_per_page' => $instance['posts_per_page'],
			'order'          => $instance['posts_order'],
			'orderby'        => $instance['posts_orderby']
		);

		# post parent
		if ( isset($instance['post_parent']) && $instance['post_parent'] ) {
			if ( $instance['post_parent'] === '%current%' && is_singular() )
				$parent_id = get_queried_object_id();
			else
				$parent_id = absint($instance['post_parent']);

			$parent_type = get_post_type( $parent_id );
			if ( $parent_id && isset(kcSettings_options::$post_types[$parent_type]) ) {
				$instance['post_parent'] = $parent_id;
				$instance['post_type'] = array( $parent_type );
			}
			else {
				# Invalid post parent, abort.
				if ( $instance['debug'] ) {
					$debug .= "<pre>".var_export($q_args, true)."</pre>";
					$debug .= '<p><strong>'.__('Invalid post parent, aborted.').'</strong></p>';
					echo $debug;
				}

				return;
			}

			$q_args['post_parent'] = $instance['post_parent'];
		}

		# Offset
		if ( isset($instance['offset']) && $instance['offset'] )
			$q_args['offset'] = $instance['offset'];

		# post orderby
		if ( $instance['posts_orderby'] == 'post__in' )
			add_filter( 'posts_orderby', array(&$this, '_sort_query_by_post_in'), 10, 2 );

		# Post type
		if ( $instance['post_type'] )
			$q_args['post_type'] = $instance['post_type'];

		# Post status
		$q_args['post_status'] = implode( ',', $instance['post_status'] );

		# Included IDs
		if ( $instance['include'] )
			$q_args['post__in'] = explode( ',', str_replace(' ', '', $instance['include']) );

		# Excluded IDs
		if ( $instance['exclude'] )
			$q_args['post__not_in'] = explode( ',', str_replace(' ', '', $instance['exclude']) );

		# meta_query
		# Apply shortcodes for the values
		$meta_queries = array();
		foreach ( $instance['meta_query'] as $mq ) {
			if ( !empty($mq['key']) ) {
				$meta_queries[] = $mq;
			}
		}
		if ( !empty($meta_queries) )
			$q_args['meta_query'] = $meta_queries;

		# Taxonomies
		$tax_query_args = $instance['tax_query'];
		$tax_rel = $tax_query_args['relation'];
		unset( $tax_query_args['relation'] );
		$tax_queries = array();
		foreach ( $tax_query_args as $tq ) {
			if ( !empty($tq['taxonomy']) && !empty($tq['taxonomy']) ) {
				if ( empty($tq['operator']) )
					unset($tq['operator']);
				$tax_queries[] = $tq;
			}
		}
		if ( !empty($tax_queries) ) {
			if ( count($tax_queries) > 2 ) {
				if ( empty($tax_rel) )
					$tax_rel = 'OR';
			}
			$tax_queries['relation'] = $tax_rel;
			$q_args['tax_query'] = $tax_queries;
		}

		$q_args = apply_filters( 'kc_widget_post-query_args', $q_args, $instance, $this );
		foreach ( $af_IDs as $af_id )
			$q_args = apply_filters( "kc_widget_post-query_args-{$af_id}", $q_args, $instance, $this );

		$debug .= "<pre>".var_export($q_args, true)."</pre>";

		$wp_query = new WP_Query($q_args);
		$output = '';

		if ( $wp_query->have_posts() ) {

			# Before widget
			$output .= $before_widget;

			$title = apply_filters( 'widget_title', $instance['title'] );
			foreach ( $af_IDs as $af_id )
				$title = apply_filters( "kc_widget_post-widget_title{$af_id}", $title, $instance, $this );

			# Widget title
			if ( $title )
				$output .= $before_title . $title . $after_title;

			# Action: Before loop
			foreach ( $af_IDs as $af_id )
				do_action( "kc_widget_post-before_loop{$af_id}", $instance, $this );

			# Text before loop
			if ( isset($instance['txt_before_loop']) && !empty($instance['txt_before_loop']) ) {
				if ( isset($instance['txt_autop']) && $instance['txt_autop'] )
					$output .= wpautop( $instance['txt_before_loop'] );
				else
					$output .= $instance['txt_before_loop'];
			}

			# Posts wrapper (open)
			if ( $instance['posts_wrapper'] ) {
				$output .= "<{$instance['posts_wrapper']}";
				if ( isset($instance['posts_class']) && $instance['posts_class'] )
					$output .= " class='{$instance['posts_class']}'";
				$output .= ">\n";
			}

			while ( $wp_query->have_posts() ) {
				$wp_query->the_post();
				$post_id = get_the_ID();

				# Action: Before entry
				foreach ( $af_IDs as $af_id )
					do_action( "kc_widget_post-before_entry{$af_id}", $post_id, $instance, $this );

				# Wrapper (open)
				if ( $instance['entry_wrapper'] ) {
					$output .= "<{$instance['entry_wrapper']}";
					$entry_class = ( isset($instance['entry_class']) ) ? $instance['entry_class'] : '';
					$output .= " class='".join( ' ', get_post_class( $entry_class, $post_id ) )."'";
					$output .= ">\n";
				}

				# Title
				$entry_title = ( $instance['title_src'] ) ? $this->_kc_get_title( $post_id, $instance ) : '';
				foreach ( $af_IDs as $af_id )
					$entry_title = apply_filters( "kc_widget_post-entry_title{$af_id}", $entry_title, $post_id, $instance, $this );
				$output .= $entry_title;

				# Thumbnail
				$entry_thumbnail = ( $instance['thumb_size'] ) ? $this->_kc_get_thumbnail( $post_id, $instance ) : '';
				foreach ( $af_IDs as $af_id )
					$entry_thumbnail = apply_filters( "kc_widget_post-entry_thumbnail{$af_id}", $entry_thumbnail, $post_id, $instance, $this );
				$output .= $entry_thumbnail;

				# Content
				$entry_content = ( $instance['content_src'] ) ? $this->_kc_get_content( $post_id, $instance ) : '';
				foreach ( $af_IDs as $af_id )
					$entry_content = apply_filters( "kc_widget_post-entry_content{$af_id}", $entry_content, $post_id, $instance, $this );
				$output .= $entry_content;

				# Wrapper (open)
				if ( $instance['entry_wrapper'] )
					$output .= "</{$instance['entry_wrapper']}>\n";

				# Action: After entry
				foreach ( $af_IDs as $af_id )
					do_action( "kc_widget_post-after_entry{$af_id}", $post_id, $instance, $this );
			}

			# Posts wrapper (close)
			if ( $instance['posts_wrapper'] )
				$output .= "</{$instance['posts_wrapper']}>\n";

			# Text before loop
			if ( isset($instance['txt_after_loop']) && !empty($instance['txt_after_loop']) ) {
				if ( isset($instance['txt_autop']) && $instance['txt_autop'] )
					$output .= wpautop( $instance['txt_after_loop'] );
				else
					$output .= $instance['txt_after_loop'];
			}

			# Action: After loop
			foreach ( $af_IDs as $af_id )
				do_action( "kc_widget_post-after_loop{$af_id}", $instance, $this );

			$output .= "{$after_widget}\n";
		}
		$wp_query = null;
		wp_reset_query();
		remove_filter( 'posts_orderby', array(&$this, '_sort_query_by_post_in') );

		echo $output;

		# Debug info
		if ( $instance['debug'] )
			echo $debug;
	}


	function _kc_get_title( $post_id, $instance ) {
		$title = '';
		switch ( $instance['title_src'] ) {
			case 'meta' :
				if ( isset($instance['title_meta']) && $instance['title_meta'] && $meta = get_post_meta($post_id, $instance['title_meta'], true) )
					$title = $meta;
			break;
			default :
				$title = get_the_title();
			break;
		}

		# Link
		if ( isset($instance['title_link']) && $instance['title_link'] ) {
			$link = '';
			switch ( $instance['title_link'] ) {
				case 'meta' :
					if ( isset($instance['title_link_meta']) && $instance['title_link_meta'] && $meta = get_post_meta($post_id, $instance['title_link_meta'], true) )
						$link = $meta;
				break;
				default :
					$link = get_permalink();
				break;
			}

			if ( $link )
				$title = "<a href='{$link}' title='".the_title_attribute( array('echo' => false) )."'>{$title}</a>";
		}

		if ( !isset($instance['title_tag']) || !$instance['title_tag'] )
			return $title;

		$output = "<{$instance['title_tag']}";
		if ( $instance['title_class'] )
			$output .= " class='{$instance['title_class']}'";
		$output .= ">{$title}</{$instance['title_tag']}>\n";

		return $output;
	}


	function _kc_get_thumbnail( $post_id, $instance ) {
		$thumb_id = ( get_post_type() == 'attachment' ) ? $post_id : '';

		switch ( $instance['thumb_src'] ) {
			case 'meta' :
				if ( $meta = get_post_meta($post_id, $instance['thumb_meta'], true) )
					$thumb_id = $meta;
			break;
			default :
				if ( current_theme_supports('post-thumbnails') && has_post_thumbnail() )
					$thumb_id = get_post_thumbnail_id( $post_id );
			break;
		}

		if ( !$thumb_id )
			return;

		$thumb_size = apply_filters( 'post_thumbnail_size', $instance['thumb_size'] );
		$thumb_img = wp_get_attachment_image($thumb_id, $thumb_size);
		if ( !$thumb_img )
			return;

		if ( !$instance['thumb_link'] )
			return "<span class='post-thumb'>{$thumb_img}</span>\n";

		$thumb_link = '';
		switch ( $instance['thumb_link'] ) {
			case 'post' :
				$thumb_link = get_permalink();
			break;
			case 'media_page' :
				$thumb_link = get_permalink( $thumb_id );
			break;
			case 'media_file' :
				$thumb_link = wp_get_attachment_url( $thumb_id );
			break;
			case 'meta-post' :
				if ( isset($instance['thumb_link_meta']) && $instance['thumb_link_meta'] && $meta = get_post_meta($post_id, $instance['thumb_link_meta'], true) )
					$thumb_link = $meta;
			break;
			case 'meta-att' :
				if ( isset($instance['thumb_link_meta']) && $instance['thumb_link_meta'] && $meta = get_post_meta($thumb_id, $instance['thumb_link_meta'], true) )
					$thumb_link = $meta;
			break;
			case 'custom' :
				if ( isset($instance['thumb_link_custom']) && $instance['thumb_link_custom'] )
					$thumb_link = $instance['thumb_link_custom'];
			break;
		}

		if ( $thumb_link )
			return "<a href='{$thumb_link}' class='post-thumb'>{$thumb_img}</a>\n";
		else
			return "<span class='post-thumb'>{$thumb_img}</span>\n";
	}


	function _kc_get_content( $post_id, $instance ) {
		$output = '';
		switch ( $instance['content_src'] ) {
			case 'content' :
				$output = get_the_content();
			break;
			case 'excerpt' :
				$output = get_the_excerpt();
			break;
			case 'meta' :
				if ( !empty($instance['content_meta']) )
					$output = get_post_meta( $post_id, $instance['content_meta'], true );
			break;
		}

		if ( !$output )
			return;

		$output = apply_filters( 'the_content', $output );

		# More link
		if ( isset($instance['more_link']) && $instance['more_link'] )
			$output .= "<a href='".get_permalink()."' class='more-link'><span>{$instance['more_link']}</span></a>\n";

		if ( !isset($instance['content_wrapper']) || !$instance['content_wrapper'] )
			return $output;

		$wrap_tag = $instance['content_wrapper'];
		if ( isset($instance['content_class']) && $instance['content_class'] )
			$wrap_tag .= " class='{$instance['content_class']}'";

		$output = "<{$wrap_tag}>\n{$output}\n</{$instance['content_wrapper']}>\n";

		return $output;
	}


	private function value_is_default( $fields, $instance ) {
		$result = true;
		foreach ( (array) $fields as $field ) {
			if ( !isset($instance[$field]) || $instance[$field] !== $this->defaults[$field] ) {
				$result = false;
				break;
			}
		}

		return $result;
	}


	public static function kcml_fields( $widgets ) {
		$widgets['widget_kcw_post'] = array(
			array(
				'id'    => 'title',
				'type'  => 'text',
				'label' => __('Title')
			),
			array(
				'id'    => 'txt_before_loop',
				'type'  => 'textarea',
				'label' => __('Before loop', 'kc-essentials')
			),
			array(
				'id'    => 'txt_after_loop',
				'type'  => 'textarea',
				'label' => __('After loop', 'kc-essentials')
			)
		);

		return $widgets;
	}
}
add_filter( 'kcml_widget_fields', array('kc_widget_post', 'kcml_fields') );

?>
