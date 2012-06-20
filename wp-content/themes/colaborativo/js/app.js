jQuery(document).ready(function ($) {

	// document ready

	$(window).load(function(){

		// window ready 
		
		$('#timeline').isotope({
			itemSelector : 'article'
		});
	});
});