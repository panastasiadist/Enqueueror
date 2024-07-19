# Getting Started

## Installation
Enqueueror is available as a free plugin from the official [WordPress Plugin Repository](https://wordpress.org/plugins/enqueueror/). 

You can search for, install, and activate it directly from your WordPress installation, or download it from the plugin repository, upload, and then activate it.

## Required Directories
Enqueueror processes CSS & JavaScript assets stored in two specific directories within the active theme's directory. These directories need to be created by the developer:

| Directory Path                                              | Usage                      |
|-------------------------------------------------------------|----------------------------|
| /wp-content/themes/<active_theme_directory>/**stylesheets** | Contains CSS assets        |
| /wp-content/themes/<active_theme_directory>/**scripts**     | Contains JavaScript assets |

::: info
* The aforementioned directory paths are relative to the WordPress installation directory.
* The **`<active_theme_directory>`** portion of the directory path corresponds to the name of the active theme's directory. Both parent and child themes are supported.
:::

## Quick-Start Examples

::: info
The examples provided below assume that the **`scripts`** and **`stylesheets`** directories have been created.
:::

#### Load a CSS file "globally", that is, irrespectively of the requested content
* Create an appropriately named "global" CSS file:
> **`/wp-content/themes/<active_theme_directory>/stylesheets/global.css`**
* Fill it with the following code:
```css
/* This CSS rule will be applied across all client-facing parts of the website. */
body {
    background-color: blue
}
```
* Navigate to any page on your WordPress website and the background should now be blue.

___

#### Load a CSS file when a page with the slug `home` is requested

* Create a page with **`home`** set as its slug.
* Create an appropriately named CSS file:
> **`/wp-content/themes/<active_theme_directory>/stylesheets/type-page-slug-home.css`**
* Fill it with the following code:
```css
/* This style will be applied when viewing the page with the slug "home". */
body {
    letter-spacing: 2px;
}
```
* Navigate to the newly created page, and you should observe increased spacing between the letters in the text.

___

::: tip
The CSS rules presented in the examples might not yield the desired outcome. The result depends on your theme and any other CSS included in your website.
:::

___

#### Load a JavaScript file when a post is requested
* Create a post.
* Create an appropriately named JavaScript file:
> **`/wp-content/themes/<active_theme_directory>/scripts/type-post.js`**
* Fill it with the following code:
```js
// This message should appear in the browser console
console.log('I will be loaded by Enqueueror for every post');
```
* Visit any post on your WordPress website, and the message should appear in your browser's console.

___

#### Load a JavaScript file when a specific post is requested

* Create a post and note its unique ID.
* Create an appropriately named JavaScript file replacing **`<id>`** portion of the filename with the ID of the post:
> **`/wp-content/themes/<active_theme_directory>/scripts/type-post-id-<id>.js`**
* Fill it with the following code:
```js
// This message should appear in the browser console
console.log('I have been loaded by Enqueueror for this post only');
```
* Visit this post on your WordPress website, and the message should appear in your browser's console.