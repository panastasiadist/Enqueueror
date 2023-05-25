# Getting Started

## Installation
Enqueueror is available from the official [WordPress Plugin Repository](https://wordpress.org/plugins/enqueueror/) as a free plugin. You may search, install and activate it from within your WordPress installation or download, upload and activate it from the plugin repository.

## Required Directories
Enqueueror takes into account CSS & JavaScript assets located under two special directories in the active theme's directory. The directories should be created by the developer:

| Directory Path                                              | Usage                      |
|-------------------------------------------------------------|----------------------------|
| /wp-content/themes/<active_theme_directory>/**stylesheets** | Contains CSS assets        |
| /wp-content/themes/<active_theme_directory>/**scripts**     | Contains JavaScript assets |

::: info
* The aforementioned directory paths are relative to the WordPress installation directory.
* The `<active_theme_directory>` part of the directory path maps to the name of the active theme's directory. Both parent and child themes are supported.
:::

## Quick-Start Examples

::: info
The following examples require that the `scripts` and `stylesheets` directories have been created.
:::

#### Load a CSS file "globally", that is, irrespectively of the requested content
* Create an appropriately named "global" CSS file:
> **`/wp-content/themes/<active_theme_directory>/stylesheets/global.css`**
* Fill it with the following code:
```css
/* This CSS rule will be applied everywhere in the client-facing part of the website */
body {
    background-color: blue
}
```
* Navigate to any page of your WordPress website and the background should be blue.

___

#### Load a CSS file when a page with slug `home` is requested

* Create a page setting **`home`** as its slug.
* Create an appropriately named CSS file:
> **`/wp-content/themes/<active_theme_directory>/stylesheets/type-page-slug-home.css`**
* Fill it with the following code:
```css
/* This style will be applied only when viewing the page with slug "home" */
body {
    letter-spacing: 2px;
}
```
* Navigate to the created page and its text should have increased space among the letters.

___

::: tip
The CSS rules shown in the examples may not produce the desired result. It depends on your theme and any other CSS applied on your website.
:::

___

#### Load a JavaScript file only when a post is requested
* Create a post if you don't already have one.
* Create an appropriately named JavaScript file:
> **`/wp-content/themes/<active_theme_directory>/scripts/type-post.js`**
* Fill it with the following code:
```js
// This message should appear in the browser console
console.log('I am loaded by Enqueueror for every post');
```
* Navigate to any post in your WordPress website and the message should appear in your browser's console.

___

#### Load a JavaScript file only when a specific post is requested

* Create a post if you don't already have one and write down its unique ID.
* Create an appropriately named JavaScript file replacing **`<id>`** with the ID of the post:
> **`/wp-content/themes/<active_theme_directory>/scripts/type-post-id-<id>.js`**
* Fill it with the following code:
```js
// This message should appear in the browser console
console.log('I was loaded by Enqueueror for this post only');
```
* Navigate to this post in your WordPress website and the message should appear in your browser's console.