# Asset Dependencies

An asset (the dependent) may specify other assets it depends on by using the **`Requires`** header key. The value of the key should contain one or more comma separated handles, relative paths, or URLs to other assets (the dependencies). WordPress will enqueue the dependencies before the dependent asset, provided that no other code intervenes in this process (ex. optimization plugins).

## Handle based dependencies

CSS or JavaScript files being loaded using WordPress' built-in mechanisms are given a name by WordPress. This name is called a **handle**, and it is unique to each resource being loaded using WordPress' facilities. A handle may be provided by WordPress itself (ex. jquery) due to a resource being bundled by the core WordPress installation or may be provided by third-party code such as plugins.

By exploiting WordPress mechanisms, Enqueueror allows developers to specify assets/resources as dependencies by providing a comma-separated list of their handles as the value of the **`Requires`** header key.

The following examples will demystify the usage of handles as dependency references:

#### Require a script dependency by its handle

```javascript
/*
 * Requires: jquery
 */

/* The build-in jQuery library will be loaded by WordPress before this asset is loaded, so the jQuery function is available in time. */
jQuery(document).ready(function(){
   console.log('Document is ready');
});
```

#### Require a stylesheet dependency by its handle

```css
/*
 * Requires: wp-block-library-css
 */

.block {
   padding: 10px;
}
```

:::warning
If non-existent handles are used, the dependent asset won't be loaded by WordPress.
:::

## Path based dependencies

Assets may specify other assets as dependencies, provided that they are located under the asset directories taken into account by Enqueueror. Paths relative to the **`scripts`** and **`stylesheets`** directories may be used for script and stylesheet assets respectively:

#### Require a script dependency by its relative path

```javascript
/*
 * Requires: /requirement1.js
 */

call_function_implemented_in_requirement1_js();
```

#### Require a PHP script dependency by its relative path

```javascript
/*
 * Requires: /requirement2.js.php
 */

call_function_implemented_in_requirement2_js_php();
```

#### Require a stylesheet dependency by its relative path

```css
/*
 * Requires: /requirement1.css
 */

.heading1 {
   font-size: 18px;
}
```

#### Require a PHP stylesheet dependency by its relative path

```css
/*
 * Requires: /requirement2.css.php
 */

.heading2 {
   font-size: 18px;
}
```

:::info
- If an asset is designated for the **`body`** HTML section, but it is required by an asset intended for the **`head`** HTML section, then the former will be loaded in the **`head`** HTML section before the dependent asset.
- Assets used exclusively as dependencies are not bound by the required naming conventions.
:::

:::warning
If an asset used as a dependency does not exist, the dependent asset will not be loaded.
:::

## URL based dependencies

To specify external scripts or stylesheets as dependencies, their URLs may be used as shown below:

#### Require a script dependency by its URL

```javascript
/*
 * Requires: https://cdn.example.com/script.js
 */

call_function_implemented_in_cdn_script();
```

#### Require a stylesheet dependency by its URL

```css
/*
 * Requires: https://cdn.example.com/style.css
 */

.heading1 {
    font-size: 18px;
}
```
:::warning
Note: If a URL does not result to a valid script or stylesheet resource, the dependent asset will be loaded by WordPress, but it may fail to execute properly.
:::

## Multiple dependencies

An asset may require multiple dependencies using the comma (,) character as a separator. The dependencies may be a mix of handles, path based or URL based resources:

#### Require multiple script dependencies

```javascript
/*
 * Requires: jquery, /requirement1.js, /requirement2.js.php, https://cdn.example.com/script.js
 */

// provided by the jQuery script represented by jquery handle
jQuery(document).ready(function(){
   call_function_implemented_in_requirement1_js();
   call_function_implemented_in_requirement2_js_php();
   call_function_implemented_in_url_script();
});
```

#### Require multiple stylesheet dependencies

```css
/*
 * Requires: wp-block-library-css, /requirement1.css, /requirement2.css.php, https://cdn.example.com/style.css
 */

.heading {
   font-size: 18px;
}
```

## Dependency chain and caveats

Dependencies may require other dependencies resulting in a dependency chain. Provided that **a.** all resources in the dependency chain exist, **b.** there are no circular dependencies, **c.** no third party code intervenes in WordPress enqueueing mechanism, all dependencies will be loaded in the correct order.

It is not unusual that two or more assets require the same dependencies. This scenario is also supported, resulting in the common dependencies to be loaded before the dependent assets.

:::warning
When specifying dependencies, the developer should be careful to avoid any circular dependencies as described by the following scenario: 
- A requires B
- B requires C
- C requires A

This is a case of circular dependency chain that will result in WordPress halting with an error.
:::



