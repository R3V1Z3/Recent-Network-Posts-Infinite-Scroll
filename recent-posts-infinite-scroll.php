<?php
/**
 * Plugin Name: Recent Posts with Infinite Scroll
 * Plugin URI: http://premium.wpmudev.org
 * Description: An extension of Recent Network Posts plugin to provide infinite scroll capabilities
 * Version: 1.0
 * Author: Barry (Incsub), glocare, DavidM
 * Author URI: http://premium.wpmudev.org
 * License: GPL2
 */

// This plugin is designed to work with and requires WPMU DEV's Post Indexer and Recent Network Posts plugins

function display_recent_posts_paginated_shortcode( $atts ) {
	extract( shortcode_atts( array(
		'number' => 5,
		'title_characters' => 250,
		'content_characters' => 0,
		'title_content_divider' => '',
		'title_before' => '',
		'title_after' => '',
		'global_before' => ' <ul>',
		'global_after' => '</ul> ',
		'before' => ' <li class="post">',
		'after' => '</li> ',
		'title_link' => 'yes',
		'show_avatars' => 'no',
		'avatar_size' => 16,
		'posttype' => 'post'
	), $atts ) );
	wp_enqueue_script('jquery');
	wp_enqueue_script('jquery-infinitescroll', plugins_url('js/jquery.infinitescroll.min.js', __FILE__), array('jquery'), '1.0', true);
	wp_enqueue_script('recent-posts-infinitescroll', plugins_url('js/recent-posts-infinitescroll.js', __FILE__), array('jquery'), '1.0', true);

	display_recent_posts_paginated ($number, $title_characters, $content_characters, $title_content_divider, $title_before, $title_after, $global_before, $global_after,$before, $after,$title_link, $show_avatars, $avatar_size, $posttype, true);
	return;
}
add_shortcode( 'globalrecentposts-paginated', 'display_recent_posts_paginated_shortcode' );

function display_recent_posts_paginated ($tmp_number,$tmp_title_characters,$tmp_content_characters,$tmp_title_content_divider,$tmp_title_before,$tmp_title_after,$tmp_global_before,$tmp_global_after,$tmp_before,$tmp_after,$tmp_title_link,$tmp_show_avatars, $tmp_avatar_size, $posttype, $output) {

	global $network_query, $network_post;
	$network_query = network_query_posts( array( 'post_type' => $posttype, 'posts_per_page' => $tmp_number, 'paged' => get_query_var('page')
			   ) );
	?>
	<style>
		div#recent-network-posts ul {
			list-style: none;
		}

		div#recent-network-posts li {
			list-style: none;
			padding-right: 20px;
			padding-bottom: 10px;
			width: 165px;
		}
		.active_page {
			font-weight:bold;
		}

	</style>
	<?php

	$html = '<div id="recent-network-posts">';

	if( network_have_posts() ) {

		$html .= $tmp_global_before;
		$default_avatar = get_option('default_avatar');

		while( network_have_posts()) {

			network_the_post();
			$posttype = $network_post->post_type;
			$the_content = $network_post->post_content;
			$string = $the_content;

			$html .= $tmp_before;
			$the_title = network_get_the_title();
			if ( $tmp_title_characters > 0 ) {
				$html .= $tmp_title_before;
				if ( $tmp_show_avatars == 'yes' ) {
					$the_author = network_get_the_author_id();
					// $html .= get_avatar( $the_author, $tmp_avatar_size, $default_avatar) . ' ';
					// Change Avatar to desired image source with link
					$html .='<a href="' . network_get_permalink() . '" ><img title="'. $the_title .'"  width="160" height="101" class="thumb" src="'. $img_src .'" /></a>';
				}					

				if ( $tmp_title_link == 'no' ) {
					$html .= substr($the_title,0,$tmp_title_characters);
				} else {
					$html .= '<br /><a title="'. $the_title .'" href="' . network_get_permalink() . '" >' . substr($the_title,0,$tmp_title_characters) . '</a>';
				}

				$html .= $tmp_title_after;
			}
			$html .= $tmp_title_content_divider;

			if ( $tmp_content_characters > 0 ) {
				$the_content = network_get_the_content();
				$html .= substr(strip_tags($the_content),0,$tmp_content_characters);
			}
			$html .= $tmp_after;
		}

	}
		$html .= $tmp_global_after;
		// Pagination
		global $page;
		$network_query_count = network_query_posts( array( 'post_type' => $posttype, 'posts_per_page' => -1, 'paged' => get_query_var('page')) );
		$total_count = count($network_query_count);
		$pages = ceil($total_count / $tmp_number);	

		$paginarea = '<nav id="nav-below">';
		if (($page) != 1) { //Previous Page Link
			$paginarea .= '<a href="?' . $filterstr . '&page=' . ($page - 1) .'" class="previous"><</a>';
		}
		if (($page) < $pages) { //Next Page Link
			$paginarea .= '<a href="?' . $filterstr . '&page=' . ($page + 1) .' " class="next">></a>';
		}
		$paginarea .= '</nav>';
		$html .= $paginarea;
		$html .= '</div>';
		// End Pagination
		if($output) {
			echo $html;
		} else {
			return $html;
		}
}
?>
