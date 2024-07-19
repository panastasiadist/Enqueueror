# Asset Dependencies

An asset (the dependent) can specify its dependencies using the **`Requires`** header key. 

The value of this key should consist of one or more comma-separated handles, relative paths, or URLs linking to other assets (the dependencies). 

Provided there's no additional code interfering with this process (for instance, optimization plugins), WordPress will enqueue the dependencies prior to the dependent asset.

## Handle based dependencies

In WordPress, CSS or JavaScript files loaded using its built-in mechanisms are assigned a name, known as a **handle**. Each resource loaded via WordPress has a unique handle. A handle could either be provided by WordPress itself (for instance, jquery) if the resource comes bundled with the core WordPress installation, or it might be provided by third-party code, such as plugins.

Benefiting from WordPress' mechanisms, Enqueueror provides developers the ability to specify assets/resources as dependencies by supplying a comma-separated list of their handles as the value for the **`Requires`** header key.

The examples provided below will clarify the use of handles as dependency references:

#### Requiring a script dependency by its handle

```javascript
/*
 * Requires: jquery
 */

/* The build-in jQuery library will be loaded by WordPress before this asset has been loaded, so the jQuery function will be available on time. */
jQuery(document).ready(function(){
   console.log('Document is ready');
});
```

#### Requiring a stylesheet dependency by its handle

```css
/*
 * Requires: wp-block-library-css
 */

.block {
   padding: 10px;
}
```

:::warning
If non-existent handles are referenced, the dependent asset won't be loaded by WordPress.
:::

## Path based dependencies

Assets can specify other assets as dependencies as long as they are located under the asset directories that Enqueueror recognizes. For script and stylesheet assets, paths relative to the **`scripts`** and **`stylesheets`** directories respectively can be used:

#### Requiring a script dependency by its relative path

```javascript
/*
 * Requires: /requirement1.js
 */

call_function_implemented_in_requirement1_js();
```

#### Requiring a PHP script dependency by its relative path

```javascript
/*
 * Requires: /requirement2.js.php
 */

call_function_implemented_in_requirement2_js_php();
```

#### Requiring a stylesheet dependency by its relative path

```css
/*
 * Requires: /requirement1.css
 */

.heading1 {
   font-size: 18px;
}
```

#### Requiring a PHP stylesheet dependency by its relative path

```css
/*
 * Requires: /requirement2.css.php
 */

.heading2 {
   font-size: 18px;
}
```

:::info
- If an asset is marked for the **`body`** HTML section but is required by an asset meant for the **`head`** HTML section, the former will be loaded in the **`head`** HTML section prior to the dependent asset.
- Assets that are only used as dependencies are not subject to the naming conventions usually required.
:::

:::warning
If an asset being used as a dependency does not exist, the dependent asset will not be loaded.
:::

## URL based dependencies

To designate external scripts or stylesheets as dependencies, their URLs can be used as demonstrated below:

#### Requiring a script dependency by its URL

```javascript
/*
 * Requires: https://cdn.example.com/script.js
 */

call_function_implemented_in_cdn_script();
```

#### Requiring a stylesheet dependency by its URL

```css
/*
 * Requires: https://cdn.example.com/style.css
 */

.heading1 {
    font-size: 18px;
}
```
:::warning
Note: If a URL doesn't resolve to a valid script or stylesheet resource, WordPress will still load the dependent asset, but its execution might fail.
:::

## Multiple dependencies

An asset can require multiple dependencies, separated by a comma (,). The dependencies can be a combination of handles, path-based, or URL-based resources:

#### Requiring multiple script dependencies

```javascript
/*
 * Requires: jquery, /requirement1.js, /requirement2.js.php, https://cdn.example.com/script.js
 */

// Provided by the jQuery script represented by the "jquery" handle
jQuery(document).ready(function(){
   call_function_implemented_in_requirement1_js();
   call_function_implemented_in_requirement2_js_php();
   call_function_implemented_in_url_script();
});
```

#### Requiring multiple stylesheet dependencies

```css
/*
 * Requires: wp-block-library-css, /requirement1.css, /requirement2.css.php, https://cdn.example.com/style.css
 */

.heading {
   font-size: 18px;
}
```

## Dependency chain and caveats

Dependencies can require other dependencies, resulting in a dependency chain. As long as **a.** all resources in the dependency chain exist, **b.** there are no circular dependencies, and **c.** no third-party code interferes with WordPress's enqueueing mechanism, all dependencies will be loaded in the correct order.

It's quite common for two or more assets to require the same dependencies. This scenario is also supported, and it results in the common dependencies being loaded before the dependent assets.

:::warning
When specifying dependencies, developers need to be careful to avoid creating any circular dependencies, as illustrated by the following scenario:
- A requires B
- B requires C
- C requires A

This is an example of a circular dependency chain, which will result in WordPress halting with an error.
:::



