# Asset Preprocessing

Drawing inspiration from SASS and LESS, Enqueueror empowers developers to use PHP for generating CSS or JavaScript code to be served to the browser. The preprocessed versions of these assets are served from the **`/wp-content/uploads/enqueueror`** directory.

## Preprocessing CSS

- Create asset files adhering to the necessary naming conventions and use the **`.css.php`** file extension.
- Write PHP code that generates valid CSS code.
- If not within the PHP execution context, you may optionally use **`style`** tags to take advantage of any CSS features your IDE supports.

#### Plain CSS code without utilizing PHP

```css
/* global.css.php */
.element { margin: 0 }
```

#### Plain CSS code using `style` tags without utilizing PHP

```html
<style>
    /* global.css.php */
   .element { margin: 0 }
</style>
```

#### Using PHP to generate valid CSS code

```html
<style>
    /* global.css.php */  
    .element { margin: 0 }
    
    <?php for ($i = 0; $i < 10; $i++): ?> 
    .element<?php echo $i; ?> { padding: <?php echo $i * 10; ?> }  
    <?php endfor; ?>
</style>
```

## Preprocessing JavaScript

- Create asset files adhering to the necessary naming conventions and use the **`.js.php`** file extension.
- Write PHP code that generates valid JavaScript code.
- If not within the PHP execution context, you may optionally use **`script`** tags to take advantage of any JavaScript features your IDE supports.

#### Plain JavaScript code without utilizing PHP

```javascript
/* global.js.php */
console.log('Hello World');
```

#### Plain JavaScript code using `script` tags without utilizing PHP

```html
<script>
    /* global.js.php */
    console.log('Hello World!');
</script>
```

#### Using PHP to generate valid JavaScript code

```html
<script>
    /* global.js.php */
    console.log('Hello World!');
    
    <?php for ($i = 0; $i < 10; $i++): ?>
    console.log('Hello World <?php echo $i; ?> !');
    <?php endfor; ?>
</script>
```

## Security & Performance

PHP assets that are preprocessed are regular PHP files, intended solely for use by Enqueueror. 

To prevent security or performance issues, it is advisable for developers to restrict direct file access execution of their code, that is, execution outside of WordPress' context. Developers can do so by starting their PHP-based assets with the following line:

```php
<?php
defined( 'ABSPATH' ) || exit;

// Next lines containing PHP code that generate JavaScript or CSS code
```

As a final line of defense, Enqueueror employs **.htaccess** rules to prevent direct access to any PHP-based assets located under the active theme. However, this approach relies on web servers that support **.htaccess** rules, such as **Apache**.

Furthermore, when a new theme is activated, Enqueueror will automatically update the **.htaccess** file, leaving any previously active theme exposed. Therefore, developers are strongly recommended to incorporate the aforementioned code line in their practice, even if the web server respects **.htaccess** rules.