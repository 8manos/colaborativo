jQuery(document).ready(function ($) {

	// Async load more
	$('#load-more').click(function(e)
	{
		e.preventDefault();

		$('#load-more').html("Cargando ...");

		var cid = $(this).attr('data-cat');
		var dop = $(this).attr('data-op');
		if (dop == 'append'){
			var classes = $('#timeline article:last-child').attr('class');
			var lastid = classes.slice(5, classes.indexOf(" "));
		}else{
			var classes = $('#timeline article:first-child').attr('data-timestamp');
			var lastid = classes.slice(5, classes.indexOf(" "));
		}
		var posttype = $(this).attr('data-type');

		$.ajax({
			url : '/wp-admin/admin-ajax.php',
			type : 'POST',
			async : true,
			data :
			{
				action : 'agregarboxes',
				id : lastid,
				cat : cid,
				op : dop,
				type : posttype
			},

			success : function(results){

				if(results == 0){
					$('#load-more').html("no more for now");
				}else{
					$('#timeline').isotope( 'insert',$(results));
					//$('#load-more').attr('data-page', parseInt(tid)+1)
					$('#load-more').html("Cargar más contenidos");
				}
			}
		});
	});

	$(window).load(function(){

		// window ready

		$('#timeline').isotope({
			itemSelector : 'article'
		});
	});
});
