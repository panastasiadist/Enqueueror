=== Enqueueror ===
Contributors: panastasiadist
Tags: theme, development, enqueue, javascript, css, stylesheet, script, wp_enqueue_script
Requires at least: 4.6
Tested up to: 5.9
Stable tag: 1.1.1
Requires PHP: 7.1
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Assisted WordPress Asset Preprocessing & Enqueueing

== Description ==

Enqueueror is a plugin assisting WordPress developers in loading JavaScript and CSS code in a well organized and efficient way. The plugin enables WordPress developers to organize script and stylesheet assets in directories, using file naming conventions, enabling the automatic inclusion of the assets depending on the requested content. In addition, Enqueueror supports generating and outputting JavaScript and CSS code using PHP, using the PHP programming language as a preprocessor.

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
You may read the guide at [GitHub](https://github.com/panastasiadist/Enqueueror)

== Other Notes ==

= Usage =

* Install Enqueueror
* Consult the guide at [GitHub](https://github.com/panastasiadist/Enqueueror)

== Screenshots ==

1. Enqueue assets globally as external files, regardless of the requested content.
2. Enqueue assets as external files, based on the slug or ID of the page.
3. Enqueue assets as external files, based on the slug or ID of the post.
4. Enqueue assets as external files, based on the slug or ID of content of the "product" post type.
5. Enqueue assets as external files, based on the slug or ID of the content, regardless of the latter's post type.
6. Enqueue assets as external files, based on the slug or ID of a term belonging in the "category" taxonomy.
7. Enqueue assets as external files, when an arbitrary term in the "category" taxonomy is requested.
8. Enqueue assets as external files, based on the slug or ID of a specific term in the "product_cat" taxonomy.
9. Organize assets in directories by post type and taxonomy.
10. Enqueue assets as external files, when the Greek (WPML based) translation of an arbitrary page is requested.
11. Dynamically generate CSS and JavaScript code to be enqueued as external files, for every requested page, using PHP as a preprocessor.
12. Enqueue JavaScript assets as external files, specifying that their script tags should be outputted in the body HTML section, for every requested page.
13. Outputting JavaScript code contained in the .js file as raw code (internally) in the body HTML section, for every requested page.
14. Outputting code contained in the .js and .css files as raw code (internally) in the head HTML section, for every requested page.
15. Dynamically generate CSS & JavaScript code to be outputted as raw code (internally) in the head HTML section, for every requested page.
16. Mix of scenarios.

== Changelog ==
= 1.1.1 =
* Fixed: Handling of multiple dependencies.
= 1.1.0 =
* Added: Introduced support for header in assets.
* Added: Introduced support for asset dependencies.
* Fixed: Asset order rules not always respected.
* Fixed: Error if WPML is activated but not set up.
= 1.0.0 =
* First release
