=== Enqueueror ===
Contributors: panastasiadist
Tags: theme, development, enqueue, javascript, css, stylesheet, script, wp_enqueue_script
Requires at least: 4.6
Tested up to: 5.8
Stable tag: 1.0.0
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

== Changelog ==
= 1.0.0 =
* First release
