# Enqueueror - Assisted WordPress Asset Preprocessing & Enqueueing
Enqueueror is a plugin assisting WordPress developers in loading JavaScript and CSS code in a well organized and efficient way. The plugin enables WordPress developers to organize script and stylesheet assets in directories, using file naming conventions, enabling the automatic inclusion of the assets depending on the requested content. In addition, Enqueueror supports generating and outputting JavaScript and CSS code using PHP, using the PHP programming language as a preprocessor.

<br>

# How it works
Enqueueror searches in the **scripts** and **stylesheets** directories located under a parent/child theme's root directory for assets to be included in WordPress' output. The assets' filenames must follow naming conventions which take into account details such as post types, slugs, or IDs. By comparing details of the requested content to the filenames of the available assets, Enqueueror decides on the assets to include in WordPress' output. In addition, an asset's filename may include special words calls **Flags** which instruct Enqueueror on how the assets should be loaded.

<br>

# Usage
## 1. Install Enqueueror
You may install the plugin from the WordPress plugin repository or by installing a release zip published on GitHub. 

## 2. Create the required directories
Enqueueror requires the **scripts** and **stylesheets** directories to be present in the root directory of the parent or child theme of your WordPress website. 

## 3. Try the Quick Start Examples or consult the Guide
The Quick Start Examples should get you up and running in no time. The Guide will help you get the most out of Enqueueror.

<br>

# Quick Start Examples

## Load a stylesheet globally
Code a CSS file named **global.css** in the **stylesheets** directory.

## Load a stylesheet only when viewing the page with id = 1
Code a CSS file named **type-page-id-1.css** or **type-id-1.css** in the **stylesheets** directory.

## Load a stylesheet only when viewing the page with slug = 'example-page'
Code a CSS file named **type-page-slug-example-page.css** or **type-slug-example-page.css** in the **stylesheets** directory.

## Load a stylesheet only when viewing the category term with id = 1
Code a CSS file named **tax-category-term-id-1.css** or **term-id-1.css** in the **stylesheets** directory.

## Load a stylesheet only when viewing the category term with slug = 'category1'
Code a CSS file named **tax-category-term-slug-category1.css** or **term-slug-category1.css** in the **stylesheets** directory.

<br>

# Guide

Enqueueror decides on the assets to load by comparing details of the requested content to the filenames of the assets found under the **scripts** and **stylesheets** directories of the active theme's root directory (depending on whether the parent or child theme is enabled). However, the plugin builds upon this concept, supporting the following additional features:

* Usage of PHP as a preprocessor for asset code.
* Different ways of loading an asset's code by utilizing the mechanism of **Flags**.
* Support for WPML based multilingual websites.

This guide is structured as follows:

1. Asset Types
2. Asset Contexts
3. Asset Directories
4. Asset Order
5. Asset Naming
   - Global Context Assets
   - Current Context Assets
6. WPML - Multilingual Support
7. Asset Flags
8. Preprocessors
9. Asset Header

<br>

## Asset Types
Enqueueror supports two types of assets: **scripts** and **stylesheets**:
* Script assets are meant to load JavaScript code.
* Stylesheet assets are meant to load CSS code.

Each asset type is characterized by a set of file extensions:
### Script asset file extensions:
- **.js** -> should contain raw JavaScript code.
- **.js.php** -> should contain raw JavaScript code or PHP code which outputs valid JavaScript code.
### Stylesheet asset file extensions:
- **.css** -> should contain raw CSS code.
- **.css.php** -> should contain raw CSS code or PHP code which outputs valid CSS code.

<br>

## Asset Contexts
Enqueueror supports two contexts: **global** and **current**:
- Global context assets are meant to be loaded globally regardless of the content requested.
- Current context assets are meant to be loaded only if applicable to the content requested.

<br>

## Asset Directories
The supported asset directory structure is as following:

> `<installation_directory>/wp-content/themes/<theme_directory>` <br>
-- `scripts` (required if using script assets) <br>
---- *optionally more directories of arbitrary organization, naming and depth* <br>
-- `stylesheets` (required if using stylesheet assets) <br>
---- *optionally more directories of arbitrary organization, naming and depth* <br>

You may create an arbitrary directory hierarchy under **scripts** and **stylesheets** directories to organize your assets in an convenient way. Enqueueror will recursively discover your assets and include them in WordPress' output according to their filenames, their flags, and a few sorting rules as explained in the next section.

<br>

## Asset Order

Enqueueror loads assets' code based on a few rules, taking into account the asset context (global or current), the language (for multilingual websites based on WPML), the filenames of the assets, and any directory hierarchy available under the required directories:

> -- Global context assets (language agnostic for multilingual websites) <br>
---- Assets in ascending directory depth (if a subdirectory hierarchy exists) <br>
------ Assets in the same directory depth are sorted in ascending order according to their filenames <br>
-- Global context assets for the current language (for multilingual websites) <br>
---- Assets in ascending directory depth (if a subdirectory hierarchy exists) <br>
------ Assets in the same directory depth are sorted in ascending order according to their filenames <br>
-- Current context assets (language agnostic for multilingual websites) <br>
---- Assets in ascending directory depth (if a subdirectory hierarchy exists) <br>
------ Assets in the same directory depth are sorted in ascending order according to their filenames <br>
-- Current context assets for the current language (for multilingual websites) <br>
---- Assets in ascending directory depth (if a subdirectory hierarchy exists) <br>
------ Assets in the same directory depth are sorted in ascending order according to their filenames <br>

*Please keep in mind that when assets' code is loaded using external files, there is no guarantee that their code will be executed according to the aforementioned rules.*

<br>

## Asset Naming

An asset's filename is the combination of the following parts (parts in parentheses are conditional / optional):
> **<asset_name>**(-**<wpml_language_code>**)(.**<flags_seperated_by_dot>**)**<supported_file_extension>**

- The required **<asset_name>** part follows a naming convention meant to inform Enqueueror when and for which content the asset should be included in the WordPress' output.

- The optional **<wpml_language_code>** part informs Enqueueror about the WPML enabled version of content the asset is applicable to. Lack of a language code specifier, makes the asset applicable to the targeted content, irrespectively of the language of the latter.

- The optional **<flags_seperated_by_dot>** part informs Enqueueror how to output the asset's code.

- The required **<supported_file_extension>** instructs Enqueueror how to process an asset's code.

<br>

### Global Context Assets
The **<asset_name>** part of assets meant to act globally is required to be the word **global**.

<br>

### Current Context Assets
List of supported rules regarding the **<asset_name>** part for various content scenarios:

Scenario|<asset_name>|Example
--------|------------|-------
Content of arbitrary post type|**`type`**|*N/A*
Content of the builtin **post** post type|**`type-post`**|*N/A*
Content of the builtin **page** post type|**`type-page`**|*N/A*
Content by post id|**`type-id-<id>`**|*type-id-1*
Content by post slug|**`type-slug-<slug>`**|*type-slug-home*
Content of specific post type|**`type-<post_type>`**|*type-event*
Content of specific post type by id|**`type-<post_type>-id-<id>`**|*type-event-id-1*
Content of specific post type by slug|**`type-<post_type>-slug-<slug>`**|*type-event-id-event1*
Term archive of arbitrary taxonomy|**`term`**|*N/A*
Term archive of the builtin **category** taxonomy|**`tax-category`**|*N/A*
Term archive by term id|**`term-id-<term_id>`**|*term-id-1*
Term archive by term slug|**`term-slug-<slug>`**|*term-slug-category1*
Term archive of a specific taxonomy|**`tax-<taxonomy>`**|*tax-event-category*
Term archive of a specific taxonomy by term id|**`tax-<taxonomy>-term-id-<id>`**|*tax-category-term-id-1*
Term archive of a specific taxonomy by term slug|**`tax-<taxonomy>-term-slug-<slug>`**|*tax-category-term-slug-category1*
Content page about an arbitrary user|**`user`**|*N/A*
Content page by user id|**`user-id-<user_id>`**|*user-id-1*
Every type of archive|**`archive`**|*N/A*
Date archive|**`archive-date`**|*N/A*
Builtin **post** post type archive|**`archive-type-post`**|*N/A*
Specific post type archive|**`archive-type-<post_type>`**|*archive-type-event*
Search page|**`search`**|*N/A*
Not found page|**`not-found`**|*N/A*

<br>

## WPML - Multilingual Support
Enqueueror supports WPML enabled multilingual websites by being able to conditionally load assets depending on the language of the content being viewed. The language specifier for an asset refers to the **<wpml_language_code>** part of the full asset's filename. If an asset's filename does not specify a WPML language code, then the asset will be used regardless of the language. Examples:

Scenario|<wpml_language_code>|Asset Name
--------|------------------------|----------
Global asset - All languages|None|*global.css*
Global asset - English only|**`-en`**|*global-en.css*
Global asset - Greek only|**`-el`**|*global-el.css*
Content of post with id 1 - All languages|None|*type-post-id-1.css*
Content of post with id 1 - English only |**`-en`**|*type-post-id-1-en.css*
Content of post with id 1 - Greek only|**`-el`**|*type-post-id-1-el.css*

<br>

## Flags
An asset's filename may contain one or more special words called **flags** which set out how Enqueueror should handle the asset's code in the context of the HTML being served to the browser, in order to coordinate the way the code is parsed and executed by the latter. The flag portion of an asset's filename refers to the **<flags_seperated_by_dot>** part. The following tables explain the supported flags:

Type|Values|Default|Description
----|------|-------|-----------
Location|**`head`**, **`body`**|**`head`**|Specifies the location in the HTML document that an asset's invocation code will be outputted.
Source|**`external`**, **`internal`**|**`external`**|Specifies if an asset's code will be loaded from an external file or if it will outputted as raw code in the designated location in the HTML document.

*Note: If a flag value has not been set on filename level, then the default value for each flag type will be used.*

Flag|Type|Description
------------|----|-----------
**`head`**|Location|Asset's raw or invocation code will be outputted in the **head** section of the HTML document.
**`footer`**|Location|Asset's raw or invocation code will be outputted in the **body** section of the HTML document.
**`internal`**|Source|Asset's code will be outputted in raw format in the designated location.
**`external`**|Source|Asset's code will be loaded from an external file and the invocation code will be outputted in the designated location.

In addition, the following table explains the allowed mix of flag values for the different asset types supported:

Asset Types/Extensions|Location Flag|Compatible Source Flags
----------------------|-------------|-----------------------
.css, .css.php|**`head`**|**`external`**, **`internal`**
.js, .js.php|**`head`**|**`external`**, **`internal`**
.js, .js.php|**`body`**|**`external`**, **`internal`**

<br>

## PHP Preprocessors

Enqueueror supports the concept of asset preprocessors acting similarly to SASS or LESS but using the PHP programming language. That means that you are able to use PHP code to produce CSS or JavaScript code to be used by a website.

### How to use the PHP Preprocessor for JavaScript
- Create assets using the rules and naming conventions already mentioned using the **.js.php** file extension.
- Implement PHP code which outputs valid JavaScript code or use PHP as a template language getting in or out of the PHP execution context using the **```<?php```** and **```?>```** tags as required.
- You may optionally use **```<script>```** tag when not in the PHP execution context.

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

**Example of using PHP as template engine**

```code
<script>
   console.log('hello');
   <?php for ($i = 0; $i < 10; $i++): ?>
   console.log('Hello World <?php echo $i; ?>');
   <?php endfor; ?>
</script>
```

### How to use the PHP Preprocessor for CSS
- Create assets using the rules and naming conventions already mentioned using the **.css.php** file extension.
- Implement PHP code which outputs valid CSS code in the same manner as for JavaScript code.
- You may optionally use **```<style>```** tag when not in the PHP execution context.

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

**Example of using PHP as template engine**

```html
<style>
   .element { margin: 0 }
   
   <?php for ($i = 0; $i < 10; $i++): ?>
   .element<?php echo $i; ?> { padding: <?php echo $i * 10; ?> }
   <?php endfor; ?>
</style>
```

<br>

## Header

An asset may contain a header, that is, a block comment specifying details about the asset, in key-value format. The header should appear first, before any other code. The format of the header is as following:

```
/*
 * Key1: Value1
 * Key2: Value2
 */
```

Currently, the only supported Header key is **Depends**, used to inform Enqueueror about the dependencies required by the asset.

### Specifying dependencies

An asset may specify scripts or stylesheets it depends on, by using the **Depends** Header key. Its associated value must contain one or more (comma separated) handles of scripts or stylesheets on which the asset depends. WordPress will enqueue the dependencies before the dependent asset. The dependencies must be either built-in (ex. jquery) or they should be registered by the developer. Dependencies are supported only for external assets, that is, assets whose filename **does not** contain an **.internal** part.

**Specifying a dependency in a script asset**

```javascript
/*
 * Requires: jquery
 */

jQuery(document).ready(function(){
   console.log('Document is ready');
});
```

**Specifying a dependency in a stylesheet asset**

```css
/*
 * Requires: wp-block-library-css
 */

.heading {
   font-size: 18px;
}
```

<br>

# Acknowledgments
Thanks to Konstantinos Petsis for testing the initial release of Enqueueror.