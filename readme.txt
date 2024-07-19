=== Enqueueror ===
Contributors: panastasiadist
Tags: theme, development, enqueue, javascript, css, stylesheet, script, wp_enqueue_script, wp_enqueue_style
Requires at least: 5.0
Tested up to: 6.6
Stable tag: 1.4.0
Requires PHP: 7.1
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Supercharged CSS & JS Coding for WordPress

== Description ==

Enqueueror empowers WordPress developers to manage and develop their CSS & JavaScript files efficiently. It facilitates conditional CSS & JavaScript loading through the use of naming conventions and provides numerous features to enhance the code development workflow.

= Quick Start Examples =

* Load a stylesheet globally
Code a CSS file named **global.css** in the **stylesheets** directory.

* Load a stylesheet only when viewing the page with id = 1
Code a CSS file named **type-page-id-1.css** or **type-id-1.css** in the **stylesheets** directory.

* Load a stylesheet only when viewing the page with slug = 'example-page'
Code a CSS file named **type-page-slug-example-page.css** or **type-slug-example-page.css** in the **stylesheets** directory.

* Load a stylesheet only when viewing the category term with id = 1
Code a CSS file named **tax-category-term-id-1.css** or **term-id-1.css** in the **stylesheets** directory.

* Load a stylesheet only when viewing the category term with slug = 'category1'
Code a CSS file named **tax-category-term-slug-category1.css** or **term-slug-category1.css** in the **stylesheets** directory.

= Guide =
You may read the guide at [GitHub](https://panastasiadist.github.io/Enqueueror/).

== Other Notes ==

= Usage =

* Install Enqueueror.
* Create the **scripts** and **stylesheets** directories under the active theme's root directory.
* Consult the guide at [GitHub](https://panastasiadist.github.io/Enqueueror/).

== Screenshots ==

1. Load assets "globally" as external files, for every kind of content.
2. Load assets as external files, based on a page's slug or ID.
3. Load assets as external files, based on a post's slug or ID.
4. Load assets as external files, based on the slug or ID of content provided by the "product" post type.
5. Load assets as external files, based on the slug or ID of the content, irrespectively of the content's post type.
6. Load assets as external files, based on the slug or ID of a term belonging in the "category" taxonomy.
7. Load assets as external files, when an arbitrary term in the "category" taxonomy is requested.
8. Load assets as external files, based on the slug or ID of a specific term in the "product_cat" taxonomy.
9. Organize assets in directories by post type and taxonomy.
10. Load assets as external files, when the Greek (WPML based) translation of an arbitrary page is requested.
11. Dynamically generate CSS and JavaScript code to be enqueued as external files, for every requested page, using PHP as a preprocessor.
12. Load JavaScript assets as external files before the closing </body> tag, for every requested page.
13. Output JavaScript code contained in the .js file internally before the closing </body> tag, for every requested page.
14. Output code contained in the .js and .css files internally within the <head> HTML section, for every requested page.
15. Dynamically generate CSS & JavaScript code to be loaded internally within the <head> HTML section, for every requested page.
16. Mix of scenarios.

== Changelog ==
= 1.4.0 =
* Added: Official, direct support for **Polylang**.
* Added: Support for **async** and **defer** script loading through the new **Loading** flag.
* Changed: WordPress 6.6 compatibility update.
* Fixed: Preprocessed, PHP-based assets will now be updated when their source files are updated.
= 1.3.1 =
* Fixed: Non string language codes returned by WPML filters are now properly handled.
= 1.3.0 =
* Added: Support for loading CSS internally before the </body> closing tag.
* Added: Support for loading CSS externally before the </body> closing tag.
* Added: Support for using internal CSS & JavaScript assets as dependencies both in <head> and <body>.
* Added: Support for using external CSS assets as dependencies within <body>.
* Added: Support for mixing internal and external assets within the asset dependency chain.
* Changed: Better support for loading assets before the </body> closing tag.
= 1.2.0 =
* Added: Local assets and external script and stylesheets may be used as dependencies.
* Added: Direct access to PHP preprocessed assets is prevented using .htaccess rules.
* Changed: Preprocessed assets are now served from /wp-content/uploads/enqueueror.
= 1.1.1 =
* Fixed: Handling of multiple dependencies.
= 1.1.0 =
* Added: Introduced support for header in assets.
* Added: Introduced support for asset dependencies.
* Fixed: Asset order rules not always respected.
* Fixed: Error if WPML is activated but not set up.
= 1.0.0 =
* First release
