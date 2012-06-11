jQuery(document).ready(function($) {
	$('.kcsse-cwa').find('.add').on('click', function() {
		var $item = $(this).parent().siblings('ul');
		if ( $item.is(':hidden') ) {
			$item.slideDown(function() {
				$(this).removeClass('hidden');
			})
			return false;
		}
	});


	$('.kcsse-cwa').find('.del').on('click', function() {
		var $item = $(this).parent().siblings('ul');
		if ( !$item.parent().siblings().length ) {
			$item.slideUp(function() {
				$(this).addClass('hidden').find('input.check').val('');
			});
		}
	});
});