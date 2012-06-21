jQuery(document).ready(function ($) {

	// document ready
	$('body').removeClass('no-js').addClass('js');

	$('#load-more').click(function(e){
		$('#load-more').html("Cargando<br />...");
		var tid = $(this).attr('data-page');
		var cid = $(this).attr('data-cat');
		$.ajax({
			url : '/wp-admin/admin-ajax.php',
			type : 'POST',
			async : true,
			data : { action : 'agregarboxes', pagina : tid, cat : cid },
			success : function(results){
				//alert(results);
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