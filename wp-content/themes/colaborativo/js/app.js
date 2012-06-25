(function($)
{
	$(function(){

		function formatSeconds(time)
		{
			var minutes = Math.floor(time / 60);
			var seconds = time % 60;
			var output = minutes + ':';
			if (seconds < 10){
				output = output + '0';
			}
			output = output + seconds;
			return output;
		}

		function countdownTrigger()
		{
			count_t = setTimeout(countdownTrigger, 1000);

			if(countdown_number > 1) {
				countdown_number--;
				if ( $('#ajax_counter').text() ){//cuando no hay nada, lo deja vacio
					$('#ajax_counter').text( formatSeconds(countdown_number) );
				}
			}else{
				$('#counter_label').text( 'Actualizando...' );
				$('#ajax_counter').text( '' );
				countdown_number = 180;//reinicia el contador

				getAjax( $('#load-more'), 'prepend' );//hace la petición ajax
			}
		}

		/*function getNewer()
		{
			getAjax( $('#load-more'), 'prepend' );

			ajax_t = setTimeout(getNewer, 180000);
		}*/

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
				batch_t = setTimeout( function(){ displayBatch($articles, start-length, length) }, 10000 );
			}

			var $batch = $articles.slice(start, start+length);

			$('#timeline').prepend( $batch ).isotope( 'reloadItems' ).isotope({ sortBy: 'original-order' });
		}

		function prependArticles($articles)
		{
			//maximo se tiene 15 tandas cada 10 segundos, lo cual duraría en total 2:30
			var batch_number = Math.ceil($articles.length / 15);
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

			//relojito
			if (op == 'prepend'){
				$('#counter_label').html( 'Actualización en' );
				$('#ajax_counter').text( formatSeconds(countdown_number)  );
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
					$(document).ready(function(){
						$("a[rel^='prettyPhoto']").prettyPhoto({
								social_tools: '',
								changepicturecallback: function(){
									if (window.addthis){
										window.addthis.ost = 0;
										window.addthis.ready();
									}
								}
						});
					});
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

		$("a[rel^='prettyPhoto']").prettyPhoto({
			social_tools: '',
			changepicturecallback: function(){
				if (window.addthis){
					window.addthis.ost = 0;
					window.addthis.ready();
				}
			}
		});

		$(window).load(function(){

			// window ready

			$('#timeline').isotope({
				itemSelector : 'article'
			});

			//ajax_t = setTimeout(getNewer, 180000);

			countdown_number = 180;
			count_t = setTimeout(countdownTrigger, 1000);

			prependArticles( $('#hidden_articles article') );
		});
	});
})(jQuery);
