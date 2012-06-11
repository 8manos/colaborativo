<?php
/**
 * @package YD_feedwordpress_content_filter
 * @author Yann Dubois
 * @version 0.2.0
 */

/*
 Plugin Name: YD Feedwordpress content filter
 Plugin URI: http://www.yann.com/en/wp-plugins/yd-feedwordpress-content-filter
 Description: Automatically filters the content of syndicated posts. | Funded by <a href="http://www.abc.fr">ABC.fr</a>
 Version: 0.2.0
 Author: Yann Dubois
 Author URI: http://www.yann.com/
 License: GPL2
 */

/**
 * @copyright 2010  Yann Dubois  ( email : yann _at_ abc.fr )
 *
 *  Original development of this plugin was kindly funded by http://www.abc.fr
 *  
 *  Additional developments kindly provided by Alessandro Nuzzo ( http://www.e-one.it )
 *
 *  This program is free software; you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation; either version 2 of the License, or
 *  (at your option) any later version.
 *
 *  This program is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  You should have received a copy of the GNU General Public License
 *  along with this program; if not, write to the Free Software
 *  Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
 */

/**
 Revision 0.1.0:
 - Original beta release
 Revision 0.2.0:
 - Refactored using the YD Plugins Framework
 - Added some options
 - Added image attachment hack contributed by Alessandro Nuzzo
 - Added redirected URL resolution
 - Got rid of unused code
 - Small bug fixes
 */

include_once( 'inc/yd-widget-framework.inc.php' );

$junk = new YD_Plugin( 
	array(
		'name' 				=> 'YD Feedwordpress Content Filter',
		'version'			=> '0.2.0',
		'has_option_page'	=> true,
		'has_shortcode'		=> false,
		'has_widget'		=> false,
		'widget_class'		=> '',
		'has_cron'			=> false,
		'crontab'			=> array(
			'daily'			=> array( 'yd_fwp_filter', 'daily_update' ),
			'hourly'		=> array( 'yd_fwp_filter', 'hourly_update' )
		),
		'has_stylesheet'	=> false,
		'stylesheet_file'	=> 'css/yd.css',
		'has_translation'	=> false,
		'translation_domain'=> '', // must be copied in the widget class!!!
		'translations'		=> array(
			array( 'English', 'Yann Dubois', 'http://www.yann.com/' )
		),
		'initial_funding'	=> array( 'Yann.com', 'http://www.yann.com' ),
		'additional_funding'=> array(),
		'form_blocks'		=> array(
			'Main options' => array( 
				'google_filter'	=> 'bool',
				'yahoo_filter'	=> 'bool',
				'wikio_filter'	=> 'bool',
				'local_images'	=> 'bool',
				'fix_attach'	=> 'bool',
				'fix_first'		=> 'bool',
				'fix_redirect'	=> 'bool'
			)
		),
		'option_field_labels'=>array(
				'google_filter'	=> 'Apply Google News item filter',
				'yahoo_filter'	=> 'Apply Yahoo News item filter',
				'wikio_filter'	=> 'Apply Wikio News item filter',
				'local_images'	=> 'Import images as local attachments',
				'fix_attach'	=> 'Fix attachment IDs',
				'fix_first'		=> 'Fix repeated title on first line',
				'fix_redirect'	=> 'Resolve redirected source URL'
		),
		'option_defaults'	=> array(
				'google_filter'	=> 1,
				'yahoo_filter'	=> 1,
				'wikio_filter'	=> 1,
				'local_images'	=> 1,
				'fix_attach'	=> 1,
				'fix_first'		=> 1,
				'fix_redirect'	=> 1
		),
		'form_add_actions'	=> array(
			// no custom actions
		),
		'has_cache'			=> false,
		'option_page_text'	=> '',
		'backlinkware_text' => 'Featuring YD Feedwordpress Content Filter Plugin',
		'plugin_file'		=> __FILE__
 	)
);

class yd_fwp_filter {
	const option_key = 'yd-feedwordpress-content-filter';
	
	function __construct() {
		$this->yd_fwp_filter();
	}
	function yd_fwp_filter() {
		$options = get_option( self::option_key );
		add_action( 'syndicated_post', array( &$this, 'filter' ), 10, 1 );
		if( $options['fix_attach'] ) {
			add_action( 'post_syndicated_item', array( &$this, 'fixAttach' ), 20, 1 );
			add_action( 'update_syndicated_item', array( &$this, 'fixAttach' ), 20, 1 );
		}
	}
	function filter( $post ) {
		if( !$post ) return $post;
		$options = get_option( self::option_key );
		
		$content = $post['post_content'];
		$title = $post['post_title'];
		
		$stripped_content = strip_tags( $content, '<i><sup><sub><strong><u><br><p><img>' ); //<b><em>
		$stripped_content = preg_replace( '|^(<[^>]*>)*(<br\s*\/?>)+|i', "$1", $stripped_content ); // no <br> at beginning (Google)
		$stripped_content = preg_replace( '|<img[^src]+>|i', '', $stripped_content ); // no sourceless <img> (Google)
		$stripped_content = preg_replace( '|(<p\s*>)*\s*et plus encore&nbsp;&raquo;\s*(</p\s*>)*|ims', '', $stripped_content );
		
		if( $options['google_filter'] ) {
			// strip Google news source
			if( preg_match( '|^http://news\.google\.fr|', $post['meta']['syndication_source_uri'] ) ) {
				preg_match( '/\s\-\s(.*?)$/', $title, $matches );
				if( $matches[1] ) {
					$expr = '<br\s*/?>\s*' . quotemeta( $matches[1] ) . '\s*<br\s*/?>';
					if( preg_match( '|' . $expr . '|uims', $stripped_content ) ) {
						$title = preg_replace( '|' . quotemeta( $matches[0] ) . '$|u', '', $title );
						$original_source = $matches[1];
						$post['tax_input']['post_tag'][] = $original_source;
						$stripped_content = preg_replace( '|' . quotemeta( $matches[1] ) . '\s*<br\s*/?>|uims', '', $stripped_content );
						$stripped_content .= '<p>Source : ' . $original_source . '</p>';
					}
				}
			}
			if( $options['fix_redirect'] ) {
				$pl = $post['meta']['syndication_permalink'];
				if( preg_match( '|^http://news\.google\.com/.*?&(?:amp;)?url=(http://.*)$|', $pl, $matches ) ) {
					//echo 'match<br/>';
					$post['meta']['syndication_permalink'] = $matches[1];
				}
			}
		}
		if( $options['wikio_filter'] ) {
			// strip Wikio source
			if( preg_match( '|^http://www\.wikio\.fr|', $post['meta']['syndication_source_uri'] ) ) {
				preg_match( '/\s\((.*?)\)\s*$/', $title, $matches );
				if( $matches[1] ) {
					$expr = '<p\s*>\s*Source\s+:\s+' . quotemeta( $matches[1] ) . '\s*</p\s*>';
					if( preg_match( '|' . $expr . '|uims', $stripped_content ) ) {
						$title = preg_replace( '|' . quotemeta( $matches[0] ) . '$|u', '', $title );
						$original_source = $matches[1];
						$post['tax_input']['post_tag'][] = $original_source;
					}
				}
			}
		}
		
		if( $options['fix_first'] ) {
			// don't repeat title at beginning of content
			$expr2 = '^\s*(?:<br\s*\/?>)*\s*(<img[^>]*>)?\s*(?:<br\s*\/?>)*\s*' 
				. preg_replace( '|/|', '\/', quotemeta( html_entity_decode( preg_replace( "/'/", '&#39;', $title ) ) ) ) 
				. '\s*(?:<br\s*\/?>)*\s*';
			//echo 'qm: ' . $expr2 . '<br/>';
			$stripped_content = preg_replace( '/' . $expr2 . '/ims', "$1", html_entity_decode( $stripped_content ) );
		}
		
		if( $options['local_images'] ) {
			/** image fetching **/
			if( preg_match( '/<img[^>]+src\s*=\s*["\']?([^"\' ]+)[^>]*>/', $stripped_content, $matches ) ) {
				$imgsrc = $matches[1];
				if( preg_match( '/\.(gif|jpeg|jpg|png)$/i', $imgsrc, $matches ) ) {
					$imgext = strtolower( $matches[1] );
					require_once( ABSPATH . 'wp-includes/class-snoopy.php' );
					$snoopy = new Snoopy();
					$result = $snoopy->fetch( $imgsrc );
					if( $result ) {
						$upload_dir = wp_upload_dir();
						$filename = sanitize_title( $title );
						$filename = preg_replace( '/[^a-zA-Z0-9_-]/', '-', $filename );
						$filename .= '.' . $imgext;
						$outfile = $upload_dir['path'] . '/' . $filename;

						if( strlen( $snoopy->results ) > 512 && !file_exists( $outfile ) ) {
							if( $h = fopen( $outfile, 'w' ) ) {
								fwrite( $h, $snoopy->results );
								fclose( $h );
							} else {
								//echo 'File write error!<br>';
							}
							$outurl = $upload_dir['url'] . '/' . $filename;
							$stripped_content = str_replace( $imgsrc, $outurl, $stripped_content );
							
							// taken from codex doc @ http://codex.wordpress.org/Function_Reference/wp_insert_attachment
							$wp_filetype = wp_check_filetype( basename( $outfile ), null );
							$attachment = array(
								'post_mime_type' => $wp_filetype['type'],
								'post_title' => $title,
								'post_content' => '',
								'post_status' => 'inherit'
							);
							$attach_id = wp_insert_attachment( $attachment, $outfile ); // no post_id :-(
							// you must first include the image.php file
							// for the function wp_generate_attachment_metadata() to work
							require_once( ABSPATH . "wp-admin" . '/includes/image.php' );
							$attach_data = wp_generate_attachment_metadata( $attach_id, $outfile );
							wp_update_attachment_metadata( $attach_id,  $attach_data );
						}
					}
				}
			}
		}
		if( $options['fix_redirect'] ) {
			/** Get final location of 30x redirected links **/
			/*
			 * 	meta content:
				var_dump( $post['meta'] );
				result sample:
			*
			array(7) {
			  ["syndication_source"]=>
			  string(31) ""accident" - Google Actualités"
			  ["syndication_source_uri"]=>
			  string(66) "http://news.google.fr/news?ned=fr&hl=fr&q=%22accident%22&scoring=n"
			  ["syndication_source_id"]=>
			  string(93) "http://news.google.fr/news?hl=fr&tab=wn&ned=fr&ie=UTF-8&scoring=n&q=%22accident%22&output=rss"
			  ["syndication_feed"]=>
			  string(93) "http://news.google.fr/news?hl=fr&tab=wn&ned=fr&ie=UTF-8&scoring=n&q=%22accident%22&output=rss"
			  ["syndication_feed_id"]=>
			  string(2) "11"
			  ["syndication_permalink"]=>
			  string(266) "http://news.google.com/news/url?sa=t&fd=R&usg=AFQjCNE7fPrmenBga0omsTLQbc0_n2y1XA&url=http://www.lefigaro.fr/politique/2010/11/20/01002-20101120ARTFIG00004-vge-entre-l-histoire-reelle-et-l-histoire-revee-il-n-y-a-que-la-minceur-d-une-feuille-de-papier.php"
			  ["syndication_item_hash"]=>
			  string(32) "ec58bb3e84fb20cb067761f3cdc0a8ab"
			}
			*/
			/*
			 * snoopy will do it :) except for Google :(
			 */
			require_once( ABSPATH . 'wp-includes/class-snoopy.php' );
			$snoopy = new Snoopy();
			$snoopy->maxredirs = 1;
			$result = $snoopy->fetch( $post['meta']['syndication_permalink'] );

			if( $snoopy->_redirectaddr ) $post['meta']['syndication_permalink'] = $snoopy->_redirectaddr;
				// accessing a private property here. It's ugly but no other choice to get it out of Snoopy
		}

		$post['post_title'] = $title;
		$post['post_content'] = $stripped_content;
		return $post;
	}
	
    /* LITTLE MOD: fix post parent attachment */
	/* kindly contributed by Alessandro Nuzzo ( http://www.e-one.it ) */
    function fixAttach( $postid ) {
        $mypost = get_post( $postid );
        if( strlen( $mypost->post_name ) > 0 ) {
            $attachments = query_posts(
            	array(
                	'name' => $mypost->post_name,
                	'post_type' => 'attachment'
            	)
            );
            foreach ( $attachments as $attachment ) {
                // set post_parent to post id
                $my_att = array();
                $my_att['ID'] = $attachment->ID;
                $my_att['post_parent'] = $postid;
                $ret = wp_update_post( $my_att );
            }
        }
    }
}
$yd_fwp_filter = new yd_fwp_filter;
?>