<?php
/**
 * Plugin Name: Hierarchical HTML Sitemap
 * Plugin URI:  https://avovk.dev/hierarchical-html-sitemap/
 * Description: Generates hierarchical HTML sitemap wich displays hierarchically sorted categories with posts links. You need add <code>[htmlmap]</code> shortcode at any page. It's working easy and fast. Super lightweight PHP code, without external CSS/JS files.
 * Version:     1.4.0
 * Requires at least: 5.8
 * Requires PHP: 7.4
 * Author:      Alexis Vovk
 * Author URI:  https://avovk.dev
 * License:     GPL-2.0-or-later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: hierarchical-html-sitemap
 * Domain Path: /languages
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Load plugin translations.
 */
function hierarchicalsitemap_load_textdomain() {
	load_plugin_textdomain( 'hierarchical-html-sitemap', false, dirname( plugin_basename( __FILE__ ) ) . '/languages' );

	__( 'Hierarchical HTML Sitemap', 'hierarchical-html-sitemap' );
	__( 'Generates hierarchical HTML sitemap wich displays hierarchically sorted categories with posts links. You need add <code>[htmlmap]</code> shortcode at any page. It\'s working easy and fast. Super lightweight PHP code, without external CSS/JS files.', 'hierarchical-html-sitemap' );
}
add_action( 'init', 'hierarchicalsitemap_load_textdomain' );

/**
 * Determine whether a flag-style shortcode attribute is present.
 *
 * WordPress stores bare attributes such as `[htmlmap hidecloud]` as numeric
 * array values. Supporting keyed truthy values keeps programmatic shortcode
 * calls compatible as well.
 *
 * @param array  $atts Shortcode attributes.
 * @param string $name Attribute name.
 * @return bool
 */
function hierarchicalsitemap_has_flag( $atts, $name ) {
	if ( in_array( $name, $atts, true ) ) {
		return true;
	}

	if ( ! array_key_exists( $name, $atts ) ) {
		return false;
	}

	return ! in_array( strtolower( (string) $atts[ $name ] ), array( '', '0', 'false', 'no', 'off' ), true );
}

/**
 * Render the [htmlmap] shortcode.
 *
 * @param array|string $atts   Shortcode attributes.
 * @param string|null  $content Enclosed content (unused).
 * @return string
 */
function hierarchicalsitemap_shortcode_htmlmap( $atts, $content = null ) { // phpcs:ignore Generic.CodeAnalysis.UnusedFunctionParameter.FoundAfterLastUsed
	$atts = is_array( $atts ) ? $atts : array();

	$hidecloud       = hierarchicalsitemap_has_flag( $atts, 'hidecloud' );
	$showpages       = hierarchicalsitemap_has_flag( $atts, 'showpages' );
	$showdescription = hierarchicalsitemap_has_flag( $atts, 'showdescription' );
	$hidedate        = hierarchicalsitemap_has_flag( $atts, 'hidedate' );
	$hidecount       = hierarchicalsitemap_has_flag( $atts, 'hidecount' );

	$atts = shortcode_atts(
		array(
			'exclude'     => '',
			'exclude_cat' => '',
		),
		$atts,
		'htmlmap'
	);

	$exclude     = wp_parse_id_list( $atts['exclude'] );
	$exclude_cat = wp_parse_id_list( $atts['exclude_cat'] );
	$html        = hierarchicalsitemap_hierarchical_category_tree(
		0,
		$exclude,
		$exclude_cat,
		array(
			'hidecloud'       => $hidecloud,
			'showdescription' => $showdescription,
			'hidedate'        => $hidedate,
			'hidecount'       => $hidecount,
		)
	);

	$showcloud = ! $hidecloud && '' !== $html['cloud'];
	$cloud     = $showcloud ? sprintf( '<p id="htmlmap_cats">%s</p>', $html['cloud'] ) : '';
	$posts     = sprintf( '<div id="htmlmap_posts">%s</div>', $html['posts'] );
	$pages     = $showpages ? hierarchicalsitemap_get_pages_list( $exclude ) : '';

	/**
	 * Filters the category navigation and posts list HTML.
	 *
	 * @since 1.3
	 * @param string $posts Category navigation and posts list HTML.
	 */
	$posts = apply_filters( 'hierarchicalsitemap_posts_list_html', $cloud . $posts );

	/**
	 * Filters the full shortcode HTML.
	 *
	 * @since 1.4.0
	 * @param string $output Full shortcode HTML.
	 */
	return apply_filters( 'hierarchicalsitemap_shortcode_html', $posts . $pages );
}
add_shortcode( 'htmlmap', 'hierarchicalsitemap_shortcode_htmlmap' );

/**
 * Recursively build the category and post lists.
 *
 * @param int   $category_id Parent category ID.
 * @param int[] $exclude     Post IDs to exclude.
 * @param int[] $exclude_cat Category IDs to exclude.
 * @param array $args        Display options.
 * @param array $out         Accumulated cloud and post HTML.
 * @return array{cloud:string,posts:string}
 */
function hierarchicalsitemap_hierarchical_category_tree(
	$category_id,
	$exclude,
	$exclude_cat,
	$args = array(),
	$out = array(
		'cloud' => '',
		'posts' => '',
	)
) {
	$args = wp_parse_args(
		$args,
		array(
			'hidecloud'       => true,
			'showdescription' => false,
			'hidedate'        => false,
			'hidecount'       => false,
		)
	);

	$out['posts'] .= hierarchicalsitemap_render_category_list( $category_id, $exclude, $exclude_cat, $args, $out['cloud'] );

	return $out;
}

/**
 * Render a nested category list and the posts in each category.
 *
 * @param int   $category_id Parent category ID.
 * @param int[] $exclude     Post IDs to exclude.
 * @param int[] $exclude_cat Category IDs to exclude.
 * @param array $args        Display options.
 * @param string $cloud      Accumulated category navigation HTML.
 * @return string
 */
function hierarchicalsitemap_render_category_list( $category_id, $exclude, $exclude_cat, $args, &$cloud ) {
	$categories = get_categories(
		array(
			'hide_empty' => false,
			'orderby'    => 'name',
			'order'      => 'ASC',
			'parent'     => absint( $category_id ),
			'exclude'    => $exclude_cat,
		)
	);

	if ( empty( $categories ) ) {
		return '';
	}

	$html = '';

	foreach ( $categories as $category ) {
		$term_id = absint( $category->term_id );
		$count   = absint( $category->count );
		$name    = esc_html( $category->name );

		if ( ! $args['hidecloud'] ) {
			$cloud .= sprintf(
				'%s<span class="cat"><a href="#cat_%d">%s</a> <small>[%d]</small></span>',
				'' === $cloud ? '' : ', ',
				$term_id,
				$name,
				$count
			);
		}

		$tag           = 0 === absint( $category_id ) ? 'h2' : 'h3';
		$count_html    = $args['hidecount'] ? '' : sprintf( ' <small>[%d]</small>', $count );
		$to_cloud_link = $args['hidecloud'] ? '' : sprintf( ' <a href="#htmlmap_cats" aria-label="%s">&uarr;</a>', esc_attr__( 'Back to categories', 'hierarchical-html-sitemap' ) );
		$title_html    = sprintf( '<%s id="cat_%d">%s</%s>', $tag, $term_id, $name . $count_html . $to_cloud_link, $tag );

		/**
		 * Filters the category title HTML.
		 *
		 * @since 1.3
		 * @param string  $title_html Category title HTML.
		 * @param string  $name       Unescaped category name.
		 * @param WP_Term $category   Category term object.
		 */
		$html .= apply_filters( 'hierarchicalsitemap_category_title_html', $title_html, $category->name, $category );

		if ( $args['showdescription'] && '' !== $category->description ) {
			$html .= sprintf( '<p class="htmlmap-category-description">%s</p>', wp_kses_post( $category->description ) );
		}

		$posts = get_posts(
			array(
				'posts_per_page' => -1,
				'orderby'        => 'date',
				'order'          => 'DESC',
				'category__in'   => array( $term_id ),
				'exclude'        => $exclude,
			)
		);

		$html .= "<ul class=\"htmlmap-post-list\">\n";
		if ( ! empty( $posts ) ) {
			foreach ( $posts as $post ) {
				$date_html = $args['hidedate'] ? '' : '<small>' . esc_html( get_the_date( 'Y-m-d', $post ) ) . '</small>&nbsp;';

				$html .= sprintf(
					'<li class="htmlmap-post">%s<a href="%s">%s</a></li>',
					$date_html,
					esc_url( get_permalink( $post ) ),
					esc_html( get_the_title( $post ) )
				);
			}
		}

		$children = hierarchicalsitemap_render_category_list( $term_id, $exclude, $exclude_cat, $args, $cloud );
		if ( ! empty( $posts ) && '' !== $children ) {
			$html .= '<li class="htmlmap-children" style="list-style:none">' . $children . '</li>';
		} elseif ( empty( $posts ) ) {
			$html .= '<li class="null htmlmap-empty-category" style="list-style:none">' . $children . '</li>';
		}

		$html .= "</ul>\n";
	}

	return $html;
}

/**
 * Build the optional pages list.
 *
 * @param int[] $exclude Page IDs to exclude.
 * @return string
 */
function hierarchicalsitemap_get_pages_list( $exclude ) {
	$html  = '<ul id="htmlmap_pages" class="htmlmap-page-list">';
	$pages = get_pages( array( 'exclude' => $exclude ) );

	foreach ( $pages as $page ) {
		$title = get_the_title( $page );
		$link  = apply_filters( 'the_permalink', get_permalink( $page ), $page );
		$html .= sprintf(
			'<li class="htmlmap-page"><a href="%s" title="%s">%s</a></li>',
			esc_url( $link ),
			esc_attr( $title ),
			esc_html( $title )
		);
	}

	$html .= '</ul>';

	/**
	 * Filters the pages list HTML.
	 *
	 * @since 1.3
	 * @param string $html Full pages list HTML.
	 */
	return apply_filters(
		'hierarchicalsitemap_pages_list_html',
		'<h2>' . esc_html__( 'All Pages', 'hierarchical-html-sitemap' ) . '</h2>' . $html
	);
}
