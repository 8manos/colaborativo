jQuery(document).ready(function ($) {

	// document ready
	$('body').removeClass('no-js').addClass('js');


	// Async load more
	$('#load-more').click(function(e){

		$('#load-more').html("Cargando ...");

		var cid = $(this).attr('data-cat');
		var dop = $(this).attr('data-op');
		var lastid = $('#timeline article:last-child').attr('data-timestamp');
		var posttype = $(this).attr('data-type');

		$.ajax({
			url : '/wp-admin/admin-ajax.php',
			type : 'POST',
			async : true,
			data : 
			{ 
				action : 'agregarboxes', 
				time : lastid, 
				cat : cid, 
				op : dop,
				type : posttype
			},

			success : function(results){

				if(results == 0){
					$('#load-more').html("no more for now");
				}else{
					$('#timeline').isotope( 'insert',$(results));
					$('#load-more').attr('data-page', parseInt(tid)+1)
					$('#load-more').html("Cargar más contenidos");
				}
			}
		});
		e.preventDefault();
	});

	$(window).load(function(){

		// window ready 
		
		$('#timeline').isotope({
			itemSelector : 'article'
		});
	});
});