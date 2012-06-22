(function($)
{
	$(function(){

		function getNewer()
		{
			getAjax( $('#load-more'), 'prepend' );

			ajax_t = setTimeout(getNewer, 180000);
		}

		function removeDuplicates($results)
		{
			$articles = $results.filter('article');
			$articles.each(function(index) {
				var id = $(this).attr('id');
				if ( $('#'+id).length > 0 ){
					$articles = $articles.not( $(this) );

				}
			});
			return $articles;
		}

		function displayBatch($articles, start, length)
		{
			if (start <= 0){
				length = length + start;//muestra los que hagan falta
				start = 0;
			}else{
				batch_t = setTimeout( function(){ displayBatch($articles, start-length, length) }, 5000 );
			}

			var $batch = $articles.slice(start, start+length);

			$('#timeline').prepend( $batch ).isotope( 'reloadItems' ).isotope({ sortBy: 'original-order' });
		}

		function prependArticles($articles)
		{
			//maximo se tiene 30 tandas cada 5 segundos, lo cual duraría en total 2:30
			var batch_number = Math.ceil($articles.length / 30);
			var start = $articles.length - batch_number;

			displayBatch($articles, start, batch_number)
		}

		function insertResults(results, op)
		{
			if(results == 0){
				if (op == 'append'){
					$('#load-more').html("no more for now");
				}
			}else{
				$articles = removeDuplicates( $(results) );

				if ($articles.length > 0){//puede que todos fueran duplicados
					if (op == 'append'){
						$('#timeline').isotope( 'insert', $articles );
						//$('#load-more').attr('data-page', parseInt(tid)+1)
						$('#load-more').html("Cargar más contenidos");
					}else{
						prependArticles($articles);
					}
				}
			}
		}

		function getAjax($button, dop)
		{
			var cid = $button.attr('data-cat');
			if (dop == 'append'){
				var date_time = $('#timeline article:last-child').attr('data-date');
			}else{
				var date_time = $('#timeline article:first-child').attr('data-date');
			}
			var posttype = $button.attr('data-type');

			$.ajax({
				url : '/wp-admin/admin-ajax.php',
				type : 'POST',
				async : true,
				data :
				{
					action : 'agregarboxes',
					time : date_time,
					cat : cid,
					op : dop,
					type : posttype
				},

				success : function(results){
					insertResults(results, dop);
				}
			});
		}

		// Async load more
		$('#load-more').click(function(e)
		{
			e.preventDefault();

			$('#load-more').html("Cargando ...");

			getAjax( $(this), 'append' );
		});

		$(window).load(function(){

			// window ready

			$('#timeline').isotope({
				itemSelector : 'article'
			});

			ajax_t = setTimeout(getNewer, 180000);
		});
	});
})(jQuery);
