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

		function displayNew()
		{
			var $articles = $('#hidden_articles article');
			$('#timeline').prepend( $articles ).isotope( 'reloadItems' ).isotope({ sortBy: 'original-order' });
			notifyArticles();
		}

		function notifyArticles()
		{
			var $articles = $('#hidden_articles article');
			if ($articles.length > 0){
				$('#notify_new').text('Nuevo contenido ( '+$articles.length+' )');
				$('#notify_new').css('cursor', 'pointer');
			}else{
				$('#notify_new').text('-');
				$('#notify_new').css('cursor', 'default');
			}
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
						$('#hidden_articles').prepend( $articles );
						notifyArticles();
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
									addthis.toolbox('.addthis_toolbox');
									addthis.counter('.addthis_counter');
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
				addthis.toolbox('.addthis_toolbox');
				addthis.counter('.addthis_counter');
			}
		});

		$('#notify_new').on('click', displayNew);

		$(window).load(function(){

			// window ready

			$('#timeline').isotope({
				itemSelector : 'article'
			});

			countdown_number = 180;
			count_t = setTimeout(countdownTrigger, 1000);

			notifyArticles();
		});
	});
})(jQuery);
