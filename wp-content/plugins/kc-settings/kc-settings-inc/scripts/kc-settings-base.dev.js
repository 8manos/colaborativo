/**
 * KC Settings Base
 */


// Credit: http://stackoverflow.com/questions/1584370/how-to-merge-two-arrays-in-javascript
Array.prototype.unique = function() {
	var a = this.concat();
	for(var i=0; i<a.length; ++i) {
		for(var j=i+1; j<a.length; ++j) {
			if(a[i] === a[j])
				a.splice(j, 1);
		}
	}

	return a;
};

var win = window.dialogArguments || opener || parent || top;

(function($, document) {
	// File (multiple)
	win.kcFileMultiple = function( files ) {
		var $target = win.kcSettings.upload.target,
		    current = $target.data('currentFiles'),
		    $last   = $target.children().last(),
		    $items  = $(),
		    $nu     = null;

		for ( var item in files ) {
			if ( !files.hasOwnProperty(item) || $.inArray(files[item].id, current) > -1 )
				continue;

			$nu = $last.clone().removeClass('hidden');

			$nu.find('img').attr('src', files[item].img);
			$nu.find('input').val(files[item].id).prop('checked', false);
			$nu.find('.title').text(files[item].title);

			$items = $items.add( $nu );
		}

		$target.append( $items );
		if ( $last.is('.hidden') ) {
			$items.show();
			$last.remove();
		}

		$target.show().prev('.info').show();
	};

	// File (single)
	win.kcFileSingle = function( data ) {
		var $target = win.kcSettings.upload.target,
		    $title  = $target.find('span').text(data.title),
		    $img    = $target.find('img').attr('src', data.img);

		$target.removeAttr('data-type');
		$target.find('input').val(data.id);
		$target.children('a.up').hide();
		$target.find('p').fadeIn().children('a.up').show().siblings('a.rm').show();

		if ( data.type == 'image' ) {
			$target.attr('data-type', data.type);
			$title.hide();

			// Replace preview image
			var thumbSize = $target.data('size');
			if ( thumbSize !== 'thumbnail' ) {
				$.ajax({
					type: 'POST',
					url: ajaxurl,
					data: { action: 'kc_get_image_url', id: data.id, size: thumbSize },
					success: function( response ) {
						if ( response ) {
							$img.attr('src', response);
						}
					}
				});
			}
		}
		else {
			$title.show();
		}
	};


	var $_doc = $(document);
	// File
	$_doc.on('click', '.kcs-file a.rm', function(e) {
		e.preventDefault();
		var $item = $(this).closest('.row');

		$item.addClass('removing').fadeOut('slow', function() {
			// am I the only one?
			if ( $item.siblings().length ) {
				$item.remove();
			}
			// No?
			else {
				$item.removeClass('removing')
					.addClass('hidden')
					.find(':input')
						.val('')
						.prop('checked', false);

				// Disable the field so it won't get saved upon submission
				$('input.fileID', $item).prop('disabled', true);

				// Hide the list and info
				$item.parent().hide().prev('.info').hide();
			}
		});

	});


	// Add files button
	$_doc.on('click', 'a.kcsf-upload', function(e) {
		e.preventDefault();
		var $el     = $(this),
		    $target = $el.siblings('.kc-rows'),
		    $solo   = $target.find('.row.hidden'),
				current = [];

		// If there's currently only one row and it's hidden, enable the field
		if ( $solo.length ) {
			$('input.fileID', $solo).prop('disabled', false);
		}
		else {
			$('input.fileID', $target).each(function() {
				current.push( this.value );
			});
		}

		win.kcSettings.upload.target = $target.data('currentFiles', current);
		tb_show( '', $el.attr('href') );
	});


	// Single file: remove
	// Set height
	$_doc.on('click', '.kcs-file-single a.rm', function(e) {
		e.preventDefault();
		$(this).fadeOut()
			.closest('div')
				.find('p.current').fadeOut(function() {
					$(this).siblings('a.up').show()
						.siblings('input').val('');
				});
	});

	// Single file: open popup to select/upload files
	$_doc.on('click', '.kcs-file-single a.up', function(e) {
		e.preventDefault();
		var $el = $(this);

		win.kcSettings.upload.target = $el.closest('div');
		tb_show( '', $el.attr('href') );
	});

})(jQuery, document);


function kcCountObj( obj ) {
	var count = 0;
	for (var k in obj) {
		if ( obj.hasOwnProperty(k) ) {
			++count;
		}
	}
	return count;
}


function kcsbSlug( str ) {
	strNu = str.replace(/^\-+/, '');
	strNu = strNu.replace(/^_+/, '');
	strNu = strNu.replace(/[^A-Za-z0-9\-_]/g, '');

	if ( strNu.match(/^\-+/) || strNu.match(/^_+/) )
		strNu = kcsbSlug( strNu );

	return strNu;
}


function invertColor( color ) {
	inverted = new RGBColor(color);
	if ( inverted.ok ) {
		color = 'rgb(' + (255 - inverted.r) + ', ' + (255 - inverted.g) + ', ' + (255 - inverted.b) + ')';
	}

	return color;
}


/* Post Finder dialog */
(function($, document) {
	var
	func = 'kcPostFinder',
	active = false,
	$_doc = $(document),
	selectors = ['.kc-find-post'],
	$_box, $_input, $_response, $_submit, $_close,
	getSelectors = function() {
		return selectors.join( ', ');
	}
	activate = function() {
		$_input = $('#find-posts-input');
		$_response = $('#find-posts-response');
		$_submit = $('#find-posts-submit');
		$_close = $('#find-posts-close');

		// Insert
		$_submit.on('click.kcPostFinder', function(e) {
			e.preventDefault();

			// Be nice!
			if ( !$_box.data('kcTarget') )
				return;

			var $selected = $_response.find('input:checked');
			if ( !$selected.length )
				return false;

			var $target = $_box.data('kcTarget'),
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
		$_doc.on('dblclick.kcPostFinder', 'input[name="found_post_id"]', function() {
			$_submit.trigger('click.kcPostFinder');
		});

		// Close
		$_doc.on('click.kcPostFinder', '#find-posts-close', function() {
			$_input.val('');
			$_box.removeData('kcTarget');
		});

		active = true;
	},
	deactivate = function() {
		unbind();
		$_submit.off('click.kcPostFinder');
		$_doc.off('dblclick.kcPostFinder');
		$_doc.off('click.kcPostFinder');
		$_box = $_input = $_response = $_submit = $_close = null;
		active = false;
	},
	action = function(e) {
		$_box.data('kcTarget', $(this));
		findPosts.open();
	},
	bind = function() {
		$_doc.on( 'dblclick.kcPostFinder', getSelectors(), action );
	},
	unbind = function() {
		$_doc.off( 'dblclick.kcPostFinder', getSelectors(), action );
	},
	publicMethod = $[func] = function( sel ) {
		var $this = this;

		if ( active ) {
			if ( !sel )
				return;

			unbind();
		}
		else {
			$_box = $('#find-posts');
			if ( !$_box.length )
				return;

			activate();
		}

		if ( sel )
			selectors = selectors.concat( sel.split(',') );

		bind();

		return $this;
	};

	publicMethod.destroy = function() {
		deactivate();
	};
}(jQuery, document));


/* Form row cloner */
(function($, document) {
	var
	func = 'kcRowCloner',
	active = false,
	$_doc = $(document),
	textInputs = ['text', 'textarea', 'color', 'date', 'datetime', 'datetime-local', 'month', 'week', 'email', 'password', 'number', 'tel', 'time', 'url'],
	callbacks = {
		add: [],
		afterAdd: [],
		del: []
	},

	activate = function() {
		bind();
		active = true;
	},

	deactivate = function() {
		unbind();
		active = false;
		callbacks = {
			add: [],
			afterAdd: [],
			del: []
		};
	},

	action = function(e) {
		var $anchor = $(e.target), func;

		if ( $anchor.is('a.add') ) {
			func = add;
		}
		else if ( $anchor.is('a.del') ) {
			func = del;
		}
		else if ( $anchor.is('a.clear') ) {
			clear( $(this) );
			e.stopPropagation();
			return;
		}
		else
			return;

		e.preventDefault();
		var $item  = $(this),
		    isLast = !$item.next('.row').length,
		    $block = $item.parent();

		func.call( e, {
			'anchor': $anchor,
			'item': $item,
			'mode': $item.data('mode'),
			'isLast': isLast,
			'block': $block
		} );

		e.stopPropagation();
	},

	add = function( args ) {
		var e = this,
		    nu = clear( args.item.clone(false).addClass('adding').hide() );

		$('.hasdep', nu).kcFormDep();
		args.nuItem = nu.insertAfter( args.item );
		args.block = args.block.kcReorder( args.mode, true );
		doCallbacks( 'add', e, args );

		args.nuItem.fadeIn('fast', function() {
			doCallbacks( 'afterAdd', e, args );
			setTimeout(function() {
				args.nuItem.removeClass('adding');
			}, 1500);
		});
	},

	del = function( args ) {
		var e = this;

		if ( !args.item.siblings('.row').length ) {
			args.item = clear( args.item );
			args.item.find('.hasdep').trigger('change');
			args.removed = false;
			doCallbacks( 'del', e, args );
		}
		else {
			args.removed = true;
			args.item.addClass('removing').fadeOut('slow', function() {
				args.item.remove();
				if ( !args.isLast )
					args.block = args.block.kcReorder( args.mode, true );
				delete args.item;
				doCallbacks( 'del', e, args );
			});
		}
	},

	clear = function( item ) {
		item.find(':input').each(function() {
			var $input = $(this),
			    type   = this.type;

			if ( $input.data('nocleanup') === true )
				return;

			if ( $.inArray(type, textInputs) > -1 )
				$input.removeAttr('style').val('');
			else if ( type === 'checkbox' || type === 'radio' )
				$input.prop('checked', this.checked);

		});

		return item;
	},

	doCallbacks = function( mode, e, args ) {
		for ( var i=0; i < callbacks[mode].length; i++ )
			callbacks[mode][i].call( e, args );
	},

	bind = function() {
		$_doc.on( 'click.kcRowCloner', 'li.row', action );
	},

	unbind = function() {
		$_doc.off( 'click.kcRowCloner', 'li.row', action );
	},

	publicMethod = $[func] = function( ) {
		var $this = this;

		if ( active )
			return;

		activate();
		return $this;
	};

	publicMethod.destroy = function() {
		deactivate();
	};

	publicMethod.addCallback = function( mode, callback ) {
		if ( callbacks.hasOwnProperty(mode) && $.isFunction(callback) )
			callbacks[mode].push( callback );
	};
})(jQuery, document);


(function($) {
	var $doc = $(document);


	$.fn.kcGoto = function( opts ) {
		defaults = {
			offset: -20,
			speed: 800
		};
		opts = $.extend( {}, defaults, opts );

		return this.each(function() {
			var $target = $(this);

			$target.fadeIn(function() {
				$('html, body').stop().animate({
					scrollTop: ( $target.offset().top + opts.offset )
				}, opts.speed );
			});
		});
	};


	$.fn.kcFormDep = function( opts ) {
		var defaults = {
		      disable: true,
		      callback: function() {}
		    },
		    opts = $.extend({}, defaults, opts),
		    onChange = function( e ) {
					var $el = $(e.target),
							val = $el.val();

					$el.data('depTargets').each(function() {
						var $c = $(this);
						if ( e.kcfdInit === true ) {
							if ( $c.data('kcfdInit') )
								return;
							else
								$c.data('kcfdInit', true);
						}

						var depon = $c.data('dep'),
						    show  = false;

						if ( !$el.prop('disabled') && (((typeof depon === 'string' || typeof depon === 'number') && depon == val) || (typeof depon === 'object' && $.inArray(val, depon) > -1)) )
							show = true;

						$c.toggle( show );
						if ( opts.disable === true ) {
							$c.find(':input').prop('disabled', !show).trigger('change');
						}
					});
				};

		return this.each(function() {
			var $el      = $(this),
			    val      = $el.val(),
			    $targets = ( $el.data('scope') !== undefined ) ?
			                 $el.closest( $el.data('scope') ).children( $el.data('child') ) :
			                 $( $el.data('child') );

			if ( $targets.length )
				$el.data('depTargets', $targets)
					.on('change', onChange).trigger( {type: 'change', kcfdInit: true} );
		});
	};


	$.fn.kcReorder = function( mode, all ) {
		var rgx1 = new RegExp(mode+'\\]\\[(\\d+)'),
		    rgx2 = new RegExp(mode+'\\-(\\d+)'),
		    $el  = $(this);

		if ( all === true ) {
			var $els = $el.children(),
			    i    = 0;
		}
		else {
			var $els = $el,
			    i    = $el.index();
		}

		$els.each(function() {
			var $x = $(this);
			$x.find(':input').each(function() {
				this.name = this.name.replace(rgx1, function(str, p1) {
					return mode + '][' + i;
				});

				if ( this.id !== '' ) {
					this.id = this.id.replace(rgx2, function(str, p1) {
						return mode + '-' + i;
					});
				}
			});

			$x.find('label').each(function() {
				var $label 	= $(this),
						atFor		= $label.attr('for');

				if ( atFor !== '' && atFor !== undefined ) {
					$label.attr( 'for', atFor.replace(rgx2, function(str, p1) {
						return mode + '-' + i;
					}) );
				}
			});

			i++;
		});

		return this;
	};


	$.fn.kcTabs = function() {
		var switchPanel = function(e) {
			e.preventDefault();
			var $anchor = $(e.currentTarget),
			    $parent = $anchor.parent();

			if ( $parent.hasClass('tabs') )
				return;

			$anchor.closest('ul').data('kcTabsPanels').hide().filter($anchor.data('kcTabsPanel')).show();
			$parent.addClass('tabs').siblings().removeClass('tabs');
		};

		return this.each(function() {
			var $list = $(this),
			    $anchors = $();
			    $panels = $();

			$list.children().each(function(idx) {
				var $anchor = $(this).children('a').first();
				if ( !$anchor.length )
					return;

				var $panel = $( $anchor.attr('href') );
				if ( !$panel.length )
					return;

				$panels = $panels.add($panel);
				$anchors = $anchors.add($anchor);
				$anchor.data('kcTabsPanel', $panel)
					.on('click', switchPanel);
			});

			$list.data({
				'kcTabsPanels': $panels,
				'kcTabsAnchors': $anchors
			});
			$anchors.first().trigger('click');
		});
	};


	/* Component metabox toggler */
	$.fn.kcMetaboxDeps = function() {
		var	$kcForm = $(this),
		    $mBoxRoot = $kcForm.find('div.metabox-holder');

		if ( !$mBoxRoot.length )
			return $kcForm;

		var prefix = $mBoxRoot.attr('id'),
		    $checks = $kcForm.find(':checkbox');

		if ( !$checks.length )
			return $kcForm;

		var $secTogglers = $();

		$checks.each(function() {
			var $sectBox = $( '#'+prefix+'-'+this.value );
			if ( !$sectBox.length )
				return;

			var $check = $(this),
			    $target = $('#'+prefix+'-'+this.value+'-hide');

			$check.data( 'sectHider', $target ).data( 'sectBox', $sectBox );
			if ( !(this.checked === $target[0].checked) ) {
				$target.prop('checked', this.checked).triggerHandler('click');
			}

			$secTogglers = $secTogglers.add( $check );
		});

		if ( !$secTogglers.length )
			return $kcForm;

		$secTogglers.change(function() {
			var $el = $(this);
			$el.data('sectHider').prop('checked', this.checked).triggerHandler('click');

			// Scroll to
			if ( this.checked )
				$el.data('sectBox').kcGoto( {offset: -40, speed: 'slow'} );
		});
	};


	/* Misc. */
	// Help trigger
	$doc.on('click', 'a.kc-help-trigger', function(e) {
		e.preventDefault();

		$('#contextual-help-link').click();
		$('#screen-meta').kcGoto();
	});


	/* Polyfills */
	if ( !Modernizr.details ) {
		$doc.on('click', 'summary', function(e) {
			if ( $(e.target).is('a') )
				return;

			var $summary = $(this),
					$details = $summary.parent();

			if ( $details.attr('open') )
				$details.removeAttr('open');
			else
				$details.attr('open', 'open');
		});
	}
})(jQuery);