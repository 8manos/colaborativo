(function($) {

	$(document).ready(function($) {
		// Deps
		$('.widgets-sortables .hasdep').kcFormDep();
		$('.widgets-sortables').ajaxSuccess(function() {
			$('.hasdep', this).kcFormDep();
			$('details', this).details();
		});

		// Delete tax/meta query row
		$('.kcw-control-block .rm').live('click', function(e) {
			e.preventDefault();

			var $el    = $(this),
			    $item  = $el.parent(),
			    $block = $item.parent(),
			    $next  = $item.next('.row');

			$item.slideUp(function() {
				if ( !$item.siblings('.row').length ) {
					$item.find('input[type="text"]').val('');
					$item.find('input[type="checkbox"]').prop('checked', false);
					$item.find('.hasdep').trigger('change');
				} else {
					$item.remove();
					if ( $next.length )
						$block.kcReorder( $el.attr('rel'), true );
				}
			});
		});


		// Add tax/meta query row
		$('.kcw-control-block .add').live('click', function(e) {
			e.preventDefault();

			var $el   = $(this),
			    $item = $el.parent().prev('.row');

			if ( $item.is(':hidden') ) {
				$item.slideDown();
			}
			else {
				$nu = $item.clone(true).hide();
				$item.after( $nu );
				$nu.slideDown()
					.kcReorder( $el.attr('rel'), false )
					.find('.hasdep').kcFormDep();
			}
		});
	});


	// Find posts
	var $findBox = $('#find-posts'),
      $found   = $('#find-posts-response'),
	    $findBoxSubmit = $('#find-posts-submit');

	// Open
	$('input.kc-find-post').live('dblclick', function() {
		$findBox.data('kcTarget', $(this));
		findPosts.open();
	});

	// Insert
	$findBoxSubmit.click(function(e) {
		e.preventDefault();

		// Be nice!
		if ( !$findBox.data('kcTarget') )
			return;

		var $selected = $found.find('input:checked');
		if ( !$selected.length )
			return false;

		var $target = $findBox.data('kcTarget'),
		    current = $target.val(),
		    current = current === '' ? [] : current.split(','),
		    newID   = $selected.val();

		if ( $target.is('.unique') ) {
			$target.val( newID );
		}
		else if ( $.inArray(newID, current) < 0 ) {
			current.push(newID);
			$target.val( current.join(',') );
		}
	});

	// Double click on the radios
	$('input[name="found_post_id"]', $findBox).live('dblclick', function() {
		$findBoxSubmit.trigger('click');
	});

	// Close
	$( '#find-posts-close' ).click(function() {
		$findBox.removeData('kcTarget');
	});
})(jQuery);
