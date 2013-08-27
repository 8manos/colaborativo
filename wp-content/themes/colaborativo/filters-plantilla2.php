	<div class="span3">
		<h3>Filtre el contenido por:</h3>
	</div>

	<div class="span9">
		<div class="btn-group" id="filters-buttons">
		<?php
		$args=array(
		  'public'   => true,
		  '_builtin' => false
		);
		$output = 'names'; // names or objects, note names is the default
		$operator = 'and'; // 'and' or 'or'
		$post_types=get_post_types($args,$output,$operator);
		array_push($post_types, 'post');
		array_reverse($post_types);
			  foreach ($post_types  as $post_type ) {
			  	$link = add_query_arg( 'post_type', $post_type );
				echo '<li class="tipo-'. $post_type .'"><a href="'.$link.'" class="btn'. $post_type .'">'. $post_type. '</a></li>';
			  }
		?>
		</div>
	</div>