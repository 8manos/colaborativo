(function($)
{
	$(function(){

		var autoupdate = true;

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
				countdown_number = 45;//reinicia el contador

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

			if ( ! autoupdate ){
				notifyArticles();
			}
		}

		function notifyArticles()
		{
			var $articles = $('#hidden_articles article');
			if ($articles.length > 0){
				$('#notify_new').text('Nuevo contenido ( '+$articles.length+' )');
				$('#notify_new').addClass('has-new');
			}else{
				$('#notify_new').text('-');
				$('#notify_new').removeClass('has-new');
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
						if (autoupdate){
							displayNew();
						}else{
							notifyArticles();
						}
					}
				}
			}

			//relojito
			if (op == 'prepend'){
				$('#counter_label').html( 'Actualización en' );
				$('#ajax_counter').text( formatSeconds(countdown_number)  );
			}
		}

		var addthis_config = addthis_config||{};
		addthis_config.data_track_addressbar = false;

		function getAjaxFeatured()
		{
			var offset = $( '#featured .clearfix article' ).length;
			console.log (offset );

			$.ajax({
				url : '/wp-admin/admin-ajax.php',
				type : 'POST',
				async : true,
				data :
				{
					action : 'agregarboxesfeatured',
					offset : offset
				},

				success : function(results){
					$( '#featured .clearfix' ).append( results );
				}
			});
		}

		function getAjax($button, dop)
		{
			var cid = $button.attr('data-cat');
			if (dop == 'append'){
				var date_time = $('#timeline article:last-child').attr('data-date');
			}else{
				var date_time = $('#timeline article:first-child').attr('data-date');
				var from = $('#timeline article:first-child').data('id');
			}
			var posttype = $button.attr('data-type');

			if( from > 0 ){

			}else{
				from = 0;
			}

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
					type : posttype,
					from : from
				},

				success : function(results){
					insertResults(results, dop);
				}
			});
		}

		autoupdate = $('#load-more').attr('data-autoupdate') == 'true' ? true : false;

		// Async load more
		$('#load-more').click(function(e)
		{
			e.preventDefault();

			$('#load-more').html("Cargando ...");

			getAjax( $(this), 'append' );

			if( $('#boxes_container #featured').length ){
				getAjaxFeatured();
			}
		});

		// Hide post if authorized
		$('.hide-button').live( 'click', function(e){
			e.preventDefault();

			var this_id = $(this).data('hide');
			var confirmar = confirm( "Realmente deseas ocultar este box: "+this_id );

			if( confirmar ){
				$.ajax({
					url : '/wp-admin/admin-ajax.php',
					type : 'POST',
					async : true,
					data :
					{
						action : 'hidebox',
						id : this_id
					},

					success : function(results){
						// alert( results );
						var $removable = $('#timeline').find( $('#posted-'+this_id) );
	        			$('#timeline').isotope( 'remove', $removable );
					}
				});
			}else{
				return false;
			}
		});

		/* Modal magic */

		$("a[rel^='prettyPhoto']").live( 'click', function(e) {

			e.preventDefault();

			var remoteid = $(this).parent().parent().attr( 'id' );
			var id_post = remoteid.match(/\d+/) | 0;
			window.location.hash = "ver-"+id_post;

			$.ajax({
				url : '/wp-admin/admin-ajax.php',
				type : 'POST',
				async : true,
				data :
				{
					action : 'contentajax',
					cual : id_post
				},

				success : function(results){
					$('#myModal .modal-body').html(results);
					$('#myModal').modal('show');
					// console.log(results);
				}
			});

		});


		$("a[rel^='postPhoto']").live( 'click', function(e) {

			e.preventDefault();

			$('#myModal .modal-body').empty();
			$('#myModal .modal-body').html('<h2>Un momento...</h2>');

			var remoteid = $(this).attr( 'id' );
			var id_post = remoteid.match(/\d+/) | 0;
			window.location.hash = "ver-"+id_post;

			//alert(id_post);

			$.ajax({
				url : '/wp-admin/admin-ajax.php',
				type : 'POST',
				async : true,
				data :
				{
					action : 'contentajax',
					cual : id_post
				},

				success : function(results){
					$('#myModal .modal-body').empty();
					$('#myModal .modal-body').html(results);
					addthis.toolbox('.addthis_toolbox');
					addthis.counter('.addthis_counter');
					//console.log(results);
				}
			});

		});

		if( window.location.hash ){

			console.log(window.location.hash);
			if( window.location.hash.match(/post-/) || window.location.hash.match(/ver-/) || window.location.hash.match(/posted-/) ){
				var id_post = window.location.hash.match(/\d+/) | 0;

				$.ajax({
					url : '/wp-admin/admin-ajax.php',
					type : 'POST',
					async : true,
					data :
					{
						action : 'contentajax',
						cual : id_post
					},

					success : function(results){
						$('#myModal .modal-body').html(results);
						$('#myModal').modal('show');
						// console.log(results);
					}
				});
			}
		}

		$('#myModal').on('hidden', function () {

		  window.location.hash = '';
		  $('#myModal .modal-body').html('<p>un momento…</p>');

		});

		$('#myModal').on('shown', function(){
			addthis.toolbox('.addthis_toolbox');
			addthis.counter('.addthis_counter');
		})

		/* Ajax update */
		
		$('#notify_new').on('click', displayNew);

		/* Carousel */

		$('.carousel .item:empty').remove(); 
		
		$('.carousel').carousel({
			interval: false
		});

		$(window).load(function(){

			// window ready

			$('#timeline').isotope({
				itemSelector : 'article'
			}).addClass('boxes');

			countdown_number = 45;
			count_t = setTimeout(countdownTrigger, 1000);

			if (autoupdate){
				displayNew();
			}else{
				notifyArticles();
			}
		});
	});
})(jQuery);
