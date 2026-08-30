=== Hierarchical HTML Sitemap ===
Contributors: avovk, egolacrima, wppuzzle
Tags: sitemap, html sitemap, seo, hierarchical sitemap, posts, posts list, pages, pages list, shortcode
Requires at least: 5.8
Tested up to: 7.1
Stable tag: 1.4.0
Requires PHP: 7.4
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

A lightweight HTML sitemap that groups posts by hierarchical categories and can optionally list pages.

== Description ==

Hierarchical HTML Sitemap offers visitors an easy and intuitive way to navigate your blog.

This plugin generates an HTML sitemap of your WordPress blog. It displays a list of posts grouped by hierarchically sorted categories. By using a shortcode, you can display it on a page or anywhere else that supports shortcodes, such as a widget.

The output does not include CSS or JavaScript files. It is pure HTML, ready to inherit or receive your theme's styles.

= Features =

* Short and easy shortcode: `[htmlmap]`.
* Categories sorted hierarchically.
* Displays posts and/or pages.
* Can exclude specified posts, pages, or categories.
* Displays category navigation above the posts list.
* Displays post publication dates.
* Clean HTML without scripts.
* Does not enqueue unnecessary JavaScript or CSS files.
* No ads or author links in the output.

= Usage and options =

The `[htmlmap]` shortcode accepts these optional attributes:

* `exclude`: post or page IDs to omit, for example `[htmlmap exclude="445,446"]`.
* `exclude_cat`: category IDs to omit, for example `[htmlmap exclude_cat="1,34"]`.
* `showpages`: display the list of pages, for example `[htmlmap showpages]`.
* `hidecloud`: hide category navigation, for example `[htmlmap hidecloud]`.
* `showdescription`: display category descriptions, for example `[htmlmap showdescription]`.
* `hidedate`: hide post publication dates, for example `[htmlmap hidedate]`.
* `hidecount`: hide post counts in category headings, for example `[htmlmap hidecount]`.

You can combine attributes, for example:

`[htmlmap exclude="3546,7398" exclude_cat="1,34" showpages hidecloud]`

= HTML structure and CSS classes =

The shortcode preserves existing category heading IDs (`cat_<term-id>`), the
category navigation class (`cat`), and the empty-category class (`null`).

Additional classes are available for theme customisation:

* `htmlmap-post-list`: each category's post list.
* `htmlmap-post`: each post list item.
* `htmlmap-children`: the list item containing child categories.
* `htmlmap-empty-category`: added alongside the legacy `null` class.
* `htmlmap-category-description`: a displayed category description.
* `htmlmap-page-list`: the optional pages list.
* `htmlmap-page`: each page list item.

Child categories are rendered after their parent category's posts inside a
dedicated list item, rather than inside the final post item.

= Extra =

* [GitHub repository](https://github.com/avovkdesign/hierarchical-html-sitemap) for issues and contributions.

== Installation ==

= Automatic installation =

1. Log in to your WordPress admin interface.
2. Go to **Plugins → Add New**.
3. Search for **Hierarchical HTML Sitemap**.
4. Select **Install Now**, then activate the plugin.

= Manual installation =

1. [Download the plugin ZIP](https://downloads.wordpress.org/plugin/hierarchical-html-sitemap.zip) and unzip it.
2. Upload the `hierarchical-sitemap` folder to `/wp-content/plugins/`.
3. In WordPress admin, go to **Plugins → Installed Plugins**.
4. Activate **Hierarchical HTML Sitemap**.

= Usage =

= Classic Editor =

1. Create or edit a page named, for example, “Sitemap”.
2. Add `[htmlmap]` to the page content.
3. Publish or update the page, then view it to see your sitemap.

= Block Editor =

1. Create or edit a page named, for example, “Sitemap”.
2. Add a **Shortcode** block.
3. Enter `[htmlmap]` in the block.
4. Publish or update the page, then view it to see your sitemap.

== Screenshots ==

1. Example of a sitemap.

== Changelog ==

= 1.4.0 =

* feat: add CSS classes for post, child-category, and page list elements.
* feat: add the `hierarchicalsitemap_shortcode_html` filter for the complete shortcode output.
* fix: sanitize shortcode ID lists and escape generated titles, descriptions, and URLs.
* fix: correct invalid nested list markup when rendering child categories.
* fix: handle empty shortcode attributes and empty category results safely.
* chore: test plugin syntax with PHP 8.3.
* chore: test plugin integration against WordPress 7.1.
* chore: update documentation and metadata.

= 1.3 =

* chore: test up to WordPress 4.6.9.
* feature: add the `hidecount` option that disables post counts in category names.
* feature: add filter hooks:
  * `hierarchicalsitemap_category_title_html`
  * `hierarchicalsitemap_posts_list_html`
  * `hierarchicalsitemap_pages_list_html`

= 1.2 =

* chore: test up to WordPress 4.7.

= 1.1 =

* fix: display empty categories that have non-empty child categories.
* feature: add `showdescription`, which displays category descriptions.
* feature: add `hidedate`, which hides post publication dates.

= 1.0 =

* feature: initial release.
