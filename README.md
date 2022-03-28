# Enqueueror - Assisted WordPress Asset Preprocessing & Enqueueing

Enqueueror facilitates WordPress developers with the development of content-specific JavaScript & CSS code, through the use of file naming conventions and a bunch of convenient features which augment their code development workflow.

# Usage

### 1. Install Enqueueror
You may install the plugin from the [WordPress Plugin Repository](https://wordpress.org/plugins/enqueueror/).

### 2. Create the required directories
Enqueueror requires the **scripts** and **stylesheets** directories in the root directory of the active parent/child theme, depending on which theme is the active one.

### 3. Try the Quick Start Examples or consult the Guide
The Quick Start Examples should get you up and running in no time. The Guide will help you get the most out of Enqueueror.

# Quick Start Examples

### Load a stylesheet globally, irrespectively of the content requested.
Code a CSS file named **global.css** in the **stylesheets** directory.

### Load a stylesheet only when viewing the page with id = 1
Code a CSS file named **type-page-id-1.css** or **type-id-1.css** in the **stylesheets** directory.

### Load a stylesheet only when viewing the page with slug = 'example-page'
Code a CSS file named **type-page-slug-example-page.css** or **type-slug-example-page.css** in the **stylesheets** directory.

### Load a stylesheet only when viewing the category term with id = 1
Code a CSS file named **tax-category-term-id-1.css** or **term-id-1.css** in the **stylesheets** directory.

### Load a stylesheet only when viewing the category term with slug = 'category1'
Code a CSS file named **tax-category-term-slug-category1.css** or **term-slug-category1.css** in the **stylesheets** directory.

# Guide

Enqueueror enables developers to organize script and stylesheet assets in the **scripts** and **stylesheets** directories, respectively, located in the root directory of the parent/child theme. By making use of file naming conventions, Enqueueror is able to infer the assets applicable to the requested content. However, Enqueueror builds upon this concept, providing WordPress developers with tools which augment JavaScript & CSS code development and delivery as following:
* Usage of PHP as a preprocessor to produce script/stylesheet code.
* Different ways of loading an asset's code by utilizing **Flags**.
* Support for WPML based multilingual websites.
* Support for asset dependencies.

This guide is structured as following:
1. Required directory structure
2. Types of assets
3. Asset contexts
4. Filename conventions for assets
      - Global context assets
      - Current context assets
5. Loading order of assets
6. WPML - Multilingual Support
7. Flags
8. PHP preprocessors
      - How to use the PHP preprocessor for JavaScript
      - How to use the PHP preprocessor for CSS
9. Header
10. Dependencies
      - Using handles to specify dependencies
      - Using local resource paths to specify dependencies
      - Using URL based resources to specify dependencies
      - Specifying multiple dependencies for an asset
      - Chain of dependencies and caveats
11. Acknowledgments

## Required directory structure

Enqueueror requires the **scripts** and **stylesheets** directories to be created in the active theme's root directory. If a child theme is in use, then its directory is considered the root directory, that is, the required directories should be created in the root directory of the child theme. In addition, Enqueueror allows for arbitrary subdirectories under the required asset directories, resembling the following directory structure:

> `<installation_directory>/wp-content/themes/<theme_directory>` <br>
-- `scripts` (required if using script assets) <br>
---- *optionally more directories of arbitrary organization, naming and depth* <br>
-- `stylesheets` (required if using stylesheet assets) <br>
---- *optionally more directories of arbitrary organization, naming and depth* <br>

Developers may create an arbitrary directory hierarchy under the required asset directories, to organize their asset files in a convenient way. Enqueueror will recursively discover any assets applicable to the requested content, ultimately delivering their code as part of WordPress' response.

## Types of assets

Enqueueror supports two types of assets: **scripts** and **stylesheets**:
* Script assets are files located under the **scripts** directory and they are meant to deliver JavaScript code, either by containing raw JavaScript code, or by implementing PHP code which generates JavaScript code.
* Stylesheet assets are files located under the **stylesheets** directory and they are meant to deliver CSS code, either by containing raw CSS code, or by implementing PHP code which generates CSS code.

Enqueueror decides how each asset file should be processed by considering its file extension. As a result, each asset type is characterized by a set of file extensions as following:

**File extensions supported for script assets:**
- **.js** -> should contain raw JavaScript code.
- **.js.php** -> should contain raw JavaScript code or PHP code which outputs JavaScript code.

**File extensions supported for stylesheet assets:**
- **.css** -> should contain raw CSS code.
- **.css.php** -> should contain raw CSS code or PHP code which outputs CSS code.

## Asset contexts

In Enqueueror's terminology, the **context** sets out which assets may be considered candidate to be delivered as part of WordPress' response, for the requested content. The available contexts are **global** and **current**. Consequently, each asset is assigned either to the **global** context or to the **current** context, as following:
- Assets assigned to the **global** context are delivered "globally", that is, irrispectively of the requested content.
- Assets assigned to the **current** context are delivered conditionally, that is, provided that their filename signifies their applicability to the requested content.

## Filename conventions for assets

An asset's filename is the combination of the following parts (parts in parentheses are conditional / optional):
> **<asset_name>**(-**<wpml_language_code>**)(.**<flags_seperated_by_dot>**)**<supported_file_extension>**

- The required **<asset_name>** part follows a naming convention meant to inform Enqueueror when and for which content the asset's code should be loaded.

- The optional **<wpml_language_code>** part informs Enqueueror about the WPML based language code of the content the asset is applicable to. Lack of a language code specifier, sets the asset as applicable to the targeted content, irrespectively of the language of the latter.

- The optional **<flags_seperated_by_dot>** part informs Enqueueror how to output an asset's code.

- The required **<supported_file_extension>** instructs Enqueueror how to process an asset's code.

### Global context assets

The **<asset_name>** part of assets meant to act globally, that is to be delivered irrispectively of the requested content, is strictly required to be the word **global**.

### Current context assets

For assets acting non globally, that is, being delivered conditionally, depending on the requested content, the **<asset_name>** part is driven by the following list of rules serving various content scenarios:

Scenario|<asset_name>|Example
--------|------------|-------
Content of arbitrary post type|**`type`**|*N/A*
Content of the built-in **post** post type|**`type-post`**|*N/A*
Content of the built-in **page** post type|**`type-page`**|*N/A*
Content by post id|**`type-id-<id>`**|*type-id-1*
Content by post slug|**`type-slug-<slug>`**|*type-slug-home*
Content of specific post type|**`type-<post_type>`**|*type-event*
Content of specific post type by id|**`type-<post_type>-id-<id>`**|*type-event-id-1*
Content of specific post type by slug|**`type-<post_type>-slug-<slug>`**|*type-event-id-event1*
Term archive of arbitrary taxonomy|**`term`**|*N/A*
Term archive of the built-in **category** taxonomy|**`tax-category`**|*N/A*
Term archive by term id|**`term-id-<term_id>`**|*term-id-1*
Term archive by term slug|**`term-slug-<slug>`**|*term-slug-category1*
Term archive of a specific taxonomy|**`tax-<taxonomy>`**|*tax-event-category*
Term archive of a specific taxonomy by term id|**`tax-<taxonomy>-term-id-<id>`**|*tax-category-term-id-1*
Term archive of a specific taxonomy by term slug|**`tax-<taxonomy>-term-slug-<slug>`**|*tax-category-term-slug-category1*
Content page about an arbitrary user|**`user`**|*N/A*
Content page by user id|**`user-id-<user_id>`**|*user-id-1*
Every type of archive|**`archive`**|*N/A*
Date archive|**`archive-date`**|*N/A*
Built-in **post** post type archive|**`archive-type-post`**|*N/A*
Specific post type archive|**`archive-type-<post_type>`**|*archive-type-event*
Search page|**`search`**|*N/A*
Not found page|**`not-found`**|*N/A*

## Loading order of assets

The order according to which a browser executes chunks of code, is crucial for a developer to ensuring the intented user experience. Enqueueror considers each asset's location in the subdirectory hierarchy, its context, its filename and its targeted language, to decide on the order according to which, each asset's code is pushed to the browser. The following textual diagram sets out the loading order of the assets:
> -- Global context assets (language agnostic for multilingual websites) <br>
---- Assets in ascending directory depth (if a subdirectory hierarchy exists) <br>
------ Assets in the same directory are delivered in filename ascending order<br>
-- Global context assets for the current language (for multilingual websites) <br>
---- Assets in ascending directory depth (if a subdirectory hierarchy exists) <br>
------ Assets in the same directory are delivered in filename ascending order<br>
-- Current context assets (language agnostic for multilingual websites) <br>
---- Assets in ascending directory depth (if a subdirectory hierarchy exists) <br>
------ Assets in the same directory are delivered in filename ascending order<br>
-- Current context assets for the current language (for multilingual websites) <br>
---- Assets in ascending directory depth (if a subdirectory hierarchy exists) <br>
------ Assets in the same directory are delivered in filename ascending order

*Note: The above loading order of assets won't be respected for assets acting as dependencies. For example, if Asset 1 would normally be delivered before Asset 2, but Asset 2 is a dependency of Asset 1, Asset 2 will be delivered before Asset 1.*

## WPML - Multilingual Support

Enqueueror supports WPML based multilingual websites by delivering assets conditionally, depending on the language of the requested content. The language specifier for an asset refers to the **<wpml_language_code>** part of an asset's filename. If a filename does not specify a WPML language code, then the asset is considered to be applicable to every language version of the content targeted by the asset. Examples:

Scenario|<wpml_language_code>|Filename
--------|------------------------|----------
Global asset - All languages|None|*global.css*
Global asset - English only|**`-en`**|*global-en.css*
Global asset - Greek only|**`-el`**|*global-el.css*
Content of post with id 1 - All languages|None|*type-post-id-1.css*
Content of post with id 1 - English only |**`-en`**|*type-post-id-1-en.css*
Content of post with id 1 - Greek only|**`-el`**|*type-post-id-1-el.css*

## Flags

An asset's filename may contain one or more special words called **Flags** which set out how Enqueueror delivers the asset's code in the context of the HTML being served to the browser, in order to coordinate the way the code is taken into account by the latter. The flag portion of a filename refers to the **<flags_seperated_by_dot>** part. The following tables explain the supported Flags:

Type|Values|Default|Description
----|------|-------|-----------
Location|**`head`**, **`body`**|**`head`**|Specifies the location in the HTML document that an asset's raw or invocation code will be delivered.
Source|**`external`**, **`internal`**|**`external`**|Specifies if an asset's code will be loaded from an external file or if it will be outputted as raw code in the intented location in the HTML document.

*Note: If a Flag value has not been set on filename level, Enqueueror will take into account the default value of each Flag type.*

Flag|Type|Description
------------|----|-----------
**`head`**|Location|Asset's raw or invocation code will be delivered in the **head** section of the HTML document.
**`footer`**|Location|Asset's raw or invocation code will be delivered in the **body** section of the HTML document.
**`internal`**|Source|Asset's code will be delivered in raw format in the intented location.
**`external`**|Source|Asset's code will be loaded from an external file and the invocation code will be delivered in the intented location.

In addition, the following table explains the allowed mix of Flag values for the different types of assets:

Asset types/extensions|Location Flag|Compatible source Flags
----------------------|-------------|-----------------------
.css, .css.php|**`head`**|**`external`**, **`internal`**
.js, .js.php|**`head`**|**`external`**, **`internal`**
.js, .js.php|**`body`**|**`external`**, **`internal`**

## PHP preprocessors

Enqueueror inspires from SASS and LESS, supporting PHP as a preprocessor for assets, enabling developers to use PHP to produce JavaScript or CSS code to be used by a website. The preprocessed versions of the assets are served from **wp-content/uploads/enqueueror** directory. The preprocessed assets are stored by reproducing the subdirectory hierarchy containing the original non preprocessed asset files.

### How to use the PHP preprocessor for JavaScript

- Create asset files using the rules and naming conventions already mentioned, using the **.js.php** file extension.
- Implement PHP code which outputs JavaScript code.
- You may optionally use **```<script>```** tag when not within the PHP execution context.

Examples:

**Example of plain JavaScript code without utilizing PHP**
```javascript
console.log('hello world');
```

**Example of plain JavaScript code using script tags without utilizing PHP**
```html
<script>
   console.log('hello world');
</script>
```

**Example of PHP being used as a template engine to produce JavaScript code**
```code
<script>
   console.log('hello');
   <?php for ($i = 0; $i < 10; $i++): ?>
   console.log('Hello World <?php echo $i; ?>');
   <?php endfor; ?>
</script>
```

### How to use the PHP preprocessor for CSS
- Create asset files using the rules and naming conventions already mentioned using the **.css.php** file extension.
- Implement PHP code which outputs CSS code.
- You may optionally use **```<style>```** tag when not within the PHP execution context.

Examples:

**Example of plain CSS code without utilizing PHP**
```css
.element { margin: 0 }
```

**Example of plain CSS code using style tags without utilizing PHP**
```html
<style>
   .element { margin: 0 }
</style>
```

**Example of PHP being used as a template engine to produce CSS code**
```html
<style>
   .element { margin: 0 }
   
   <?php for ($i = 0; $i < 10; $i++): ?>
   .element<?php echo $i; ?> { padding: <?php echo $i * 10; ?> }
   <?php endfor; ?>
</style>
```

## Header

An asset may contain a header, that is, a block comment specifying details about the asset, in **key:value** format. The header should appear first, before any other code. The format of the header is as following:

```
/*
 * Key1: Value1
 * Key2: Value2
 */
```

Currently, the only supported Header key is **Requires**, which is used to inform Enqueueror about the dependencies required by the asset.

## Dependencies

An asset may specify scripts or stylesheets it depends on by using the **Requires** Header key. The key's associated value should contain one or more, comma separated handles, relative paths to local script/stylesheet assets, or URLs to external script/stylesheet assets. WordPress will enqueue the dependencies before the dependent asset, provided that no other code intervenes in this process (ex. optimization plugins). 

*Notes:*
- *Dependencies are supported for external assets only.*
- *A script asset may require script dependencies only.*
- *A stylesheet asset may require stylesheet dependencies only.*

### Using handles to specify dependencies

WordPress features a mechanism used to enqueue scripts and stylesheets (resources) to be loaded according to the order they have been enqueued. Each resource delivered using this mechanism, is characterized by a unique name, that is, a "handle" associated only to the resource's URL. These handles (names) enable WordPress to load specific resources before alternative resources making use of the former. Enqueueror makes use of these WordPress facilities to enable an asset to "require", that is, to inform WordPress about other scripts and stylesheets it depends on, by referring to the handles assigned to them.

A resource/handle may be supported by WordPress itself (such as jQuery - handle: jquery) or by third-party code. An asset may require dependencies by specifying their unique names, that is, by specifying their handles. However, only existent handles should be used as dependencies. 

**Specifying a dependency in a script asset using a handle**

```javascript
/*
 * Requires: jquery
 */

jQuery(document).ready(function(){
   console.log('Document is ready');
});
```

**Specifying a dependency in stylesheet asset using a handle**

```css
/*
 * Requires: wp-block-library-css
 */

.block {
   padding: 10px;
}
```

*Note: If non-existent handles are used, the dependent asset won't be loaded by WordPress.*

### Using local resource paths to specify dependencies

Scripts and stylesheets located under the asset root directories of Enqueueror, support requiring other (local) scripts and stylesheets (respectively) located under the asset root directories.

To specify local scripts or stylesheets as dependencies, one should specify their path relative to their respective asset root directory, starting with a slash (/). If the local script/stylesheet specified by the relative path does not exist, the dependent asset won't be loaded by WordPress. 

Examples:

**Specifying a dependency in a script asset using a local .js file**

```javascript
/*
 * Requires: /requirement1.js
 */

call_function_implemented_in_requirement1(); // /requirement1.js
```

**Specifying a dependency in a script asset using a local PHP preprocessed .js.php file**

```javascript
/*
 * Requires: /requirement2.js.php
 */

call_function_implemented_in_requirement2(); // /requirement2.js.php
```

**Specifying a dependency in a stylesheet asset using a local .css file**

```css
/*
 * Requires: /requirement1.css
 */

.heading1 {
   font-size: 18px;
}
```

**Specifying a dependency in a stylesheet asset using a local PHP preprocessed .css.php file**

```css
/*
 * Requires: /requirement2.css.php
 */

.heading2 {
   font-size: 18px;
}
```

*Notes:*
- *Only external assets may be used as dependencies.*
- *If a dependency asset intented for the **body** (footer) HTML section is used by a dependent asset intented for the **head** HTML section, then the dependency asset will be loaded in the **head** HTML section, before the dependent asset.*
- *If the dependency asset specified by the relative path does not exist, the dependent asset will not be loaded by WordPress.*
- *Dependency assets are not bound by the naming conventions presented in the relevant section.*

### Using URL based resources to specify dependencies

To specify external scripts or stylesheets as dependencies, their URLs may be used as following:

**Specifying a dependency in a script asset using a URL to a script file**

```javascript
/*
 * Requires: https://cdn.example.com/script.js
 */

call_function_implemented_in_cdn_script(); // https://cdn.example.com/script.js
```

**Specifying a dependency in a stylesheet asset using a URL to a stylesheet file**

```javascript
/*
 * Requires: https://cdn.example.com/style.css
 */

.heading1 {
   font-size: 18px;
}
```

*Note: If a URL does not result to a valid script or stylesheet (for example a not-found error is encountered), the dependent asset will be loaded by WordPress but it may fail to run properly.*

### Specifying multiple dependencies for an asset

Multiple dependencies may be specified for an asset using the comma (,) character to separate the dependencies. The dependencies may be a mix of handles, local script/stylesheet assets or URL based script/stylesheet files. 

Examples:

**Specifying multiple dependencies in a script asset**

```javascript
/*
 * Requires: jquery, /requirement1.js, /requirement2.js.php, https://cdn.example.com/script.js
 */

// provided by the jQuery script represented by jquery handle
jQuery(document).ready(function(){
   call_function_implemented_in_requirement1(); // /requirement1.js
   call_function_implemented_in_requirement2(); // /requirement2.js.php
   call_function_implemented_in_url_script(); // https://cdn.example.com/script.js
});
```

**Specifying multiple dependencies in a stylesheet asset**

```css
/*
 * Requires: wp-block-library-css, /requirement1.css, /requirement2.css.php, https://cdn.example.com/style.css
 */

.heading {
   font-size: 18px;
}
```

### Chain of dependencies and caveats

It is possible that an asset's dependencies depend on other dependencies and so on, resulting in a chain of dependencies. Provided that a. all dependencies exist, b. there are no circular dependencies, c. no third party code intervenes in WordPress enqueueing mechanism, all dependencies will be loaded in the correct order.

In addition, it is not uncommon that two or more dependent assets, require the same dependencies. This scenario is also supported, ultimatelly resulting in the common dependencies to be loaded before the dependent assets.

However, when specifying dependencies, the developer should be careful to avoid any circular dependencies as following: A requires B, B requires C, C requires A. This is a case of circular dependency which will result in WordPress halting with an error.

# Acknowledgments
Thanks to Konstantinos Petsis for testing the initial release of Enqueueror.