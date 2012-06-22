(function($)
{
	$(function(){

		function getNewer()
		{
			getAjax( $('#load-more'), 'prepend' );

			t = setTimeout(getNewer, 30000);
		}

		function insertResults(results, op)
		{
			if (op == 'append'){
				if(results == 0){
					$('#load-more').html("no more for now");
				}else{
					$('#timeline').isotope( 'insert',$(results));
					//$('#load-more').attr('data-page', parseInt(tid)+1)
					$('#load-more').html("Cargar más contenidos");
				}
			}else{
				if(results !== 0){
					$('#timeline').prepend( $(results) ).isotope( 'reloadItems' ).isotope({ sortBy: 'original-order' });
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

			//t = setTimeout(getNewer, 3000);
		});
	});
})(jQuery);
