// Based on https://gist.github.com/854622
(function(window,undefined){

	// Prepare our Variables
	var
		History = window.History,
		$ = window.jQuery,
		document = window.document;

	// Check to see if History.js is enabled for our Browser
	if ( !History.enabled ) {
		return false;
	}

	// Wait for Document
	$(function(){
		// Prepare Variables
		var
			$clicked,
			/* Application Specific Variables */
			contentSelector = window.kcAjaxify.el_content,
			$content = $(contentSelector).filter(':first'),
			contentNode = $content.get(0),
			$menus = $(window.kcAjaxify.el_menu),
			menuChildrenSelector = window.kcAjaxify.el_menu_children,
			$updateNonMenu = ( window.kcAjaxify.el_active_others !== undefined && window.kcAjaxify.el_active_others.length && window.kcAjaxify.class_active_others !== undefined && window.kcAjaxify.class_active_others.length ) ? true : false,
			/* Application Generic Variables */
			$body = $(document.body),
			rootUrl = History.getRootUrl(),
			scrollOptions = {
				duration: 800,
				easing:'swing'
			},
			url_excludes = false;

			if ( window.kcAjaxify.hasOwnProperty('url_excludes') ) {
				url_excludes = window.kcAjaxify.url_excludes.split(',');
			}


		// Ensure Content
		if ( $content.length === 0 ) {
			$content = $body;
		}

		// Internal Helper
		$.expr[':'].internal = function(obj, index, meta, stack){
			// Prepare
			var
				$this = $(obj),
				url = $this.attr('href')||'',
				isInternalLink;

			// Check link
			isInternalLink = url.substring(0,rootUrl.length) === rootUrl || url.indexOf(':') === -1;

			// Ignore or Keep
			return isInternalLink;
		};

		// HTML Helper
		var documentHtml = function(html){
			// Prepare
			var result = String(html)
				.replace(/<\!DOCTYPE[^>]*>/i, '')
				.replace(/<(html|head|body|title|meta|script)([\s\>])/gi,'<div class="document-$1"$2')
				.replace(/<\/(html|head|body|title|meta|script)\>/gi,'</div>')
			;

			// Return
			return result;
		};

		// Ajaxify Helper
		$.fn.ajaxify = function(){
			// Prepare
			var $this = $(this);

			// Ajaxify
			$this.find('a:internal:not('+window.kcAjaxify.el_excludes+')').click(function(event){
				event.preventDefault();
				// Prepare
				var
					$this = $(this),
					url = $this.attr('href'),
					title = $this.attr('title')||null;

				if (url == '' || url == 'undefined' || typeof url == 'undefined')
					return;

				if ( url_excludes !== false ) {
					for ( var i = 0; i < url_excludes.length; i++ )
						if ( url.match( new RegExp(url_excludes[i]) ) )
							return;
				}

				// Continue as normal for cmd clicks etc
				if ( event.which == 2 || event.metaKey ) { return true; }

				// Ajaxify this link
				History.pushState(null,title,url);
				event.preventDefault();
				$clicked = $this;
				//return false;
			});

			// Chain
			return $this;
		};

		// Ajaxify our Internal Links
		$body.ajaxify();

		// Hook into State Changes
		$(window).bind('statechange',function(){
			// Prepare Variables
			var
				State = History.getState(),
				url = State.url,
				relativeUrl = url.replace(rootUrl,'');

			// Set Loading
			$body.addClass('loading');

			// Start Fade Out
			// Animating to opacity to 0 still keeps the element's height intact
			// Which prevents that annoying pop bang issue when loading in new content
			$content.animate({opacity:0},800);

			// Ajax Request the Traditional Page
			$.ajax({
				url: url,
				success: function(data, textStatus, jqXHR){
					// Prepare
					var
						$data = $(documentHtml(data)),
						$dataBody = $data.find('.document-body:first'),
						$dataContent = $dataBody.find(contentSelector).filter(':first'),
						$menuChildren, contentHtml, $scripts;

					// Fetch the scripts
					$scripts = $dataContent.find('.document-script');
					if ( $scripts.length ) {
						$scripts.detach();
					}

					// Fetch the content
					contentHtml = $dataContent.html()||$data.html();
					if ( !contentHtml ) {
						document.location.href = url;
						return false;
					}

					// Update clicked element class, ONLY if it's NOT inside the ajax content
					/*
					if ( !$clicked.closest( $content ).length ) {
						// Menu item?
						if ( $menus.length ) {
							$menus.find(window.kcAjaxify.el_active_menu).removeClass(window.kcAjaxify.class_active_menu);
							$clicked.blur().parent('li').addClass(window.kcAjaxify.class_active_menu);
						}
						// Non menu item
						else if ( $updateNonMenu ) {
							$body.find(window.kcAjaxify.el_active_others).removeClass(window.kcAjaxify.class_active_others);
							$clicked.blur().addClass(window.kcAjaxify.class_active_others);
						}

					}
					*/

					// Update the menu
					/*
					$menuChildren = $menu.find(menuChildrenSelector);
					$menuChildren.filter(activeSelector).removeClass(activeClass);
					$menuChildren = $menuChildren.has('a[href^="'+relativeUrl+'"],a[href^="/'+relativeUrl+'"],a[href^="'+url+'"]');
					if ( $menuChildren.length === 1 ) { $menuChildren.addClass(activeClass); }
					*/

					// Update the content
					$content.stop(true,true);
					$content.html(contentHtml).ajaxify().css('opacity',100).show(); /* you could fade in here if you'd like */

					// Update the title
					document.title = $data.find('.document-title:first').text();
					try {
						document.getElementsByTagName('title')[0].innerHTML = document.title.replace('<','&lt;').replace('>','&gt;').replace(' & ',' &amp; ');
					}
					catch ( Exception ) { }

					// Add the scripts
					$scripts.each(function(){
						var $script = $(this), scriptText = $script.text(), scriptNode = document.createElement('script');
						scriptNode.appendChild(document.createTextNode(scriptText));
						contentNode.appendChild(scriptNode);
					});

					// Complete the change
					if ( $body.ScrollTo||false ) { $body.ScrollTo(scrollOptions); } /* http://balupton.com/projects/jquery-scrollto */
					$body.removeClass('loading');

					// Replace body class & id
					$.each( ['id', 'class'], function(idx, attr) {
						var val = $body.attr( attr );
						if (typeof val !== 'undefined' && val !== false)
							$body.attr(attr, ( data.match( new RegExp('body.*'+attr+'=\["\'\](.*)\["|\'\]') ) || val )[1]  );
					});

					// Inform Google Analytics of the change
					if ( typeof window.pageTracker !== 'undefined' ) {
						window.pageTracker._trackPageview(relativeUrl);
					}

					// Inform ReInvigorate of a state change
					if ( typeof window.reinvigorate !== 'undefined' && typeof window.reinvigorate.ajax_track !== 'undefined' ) {
						reinvigorate.ajax_track(url);
						// ^ we use the full url here as that is what reinvigorate supports
					}

				},
				error: function(jqXHR, textStatus, errorThrown){
					document.location.href = url;
					return false;
				}
			}); // end ajax

		}); // end onStateChange

	}); // end onDomLoad
})(window); // end closure