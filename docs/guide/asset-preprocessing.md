# Asset Preprocessing

Enqueueror inspires from SASS and LESS, enabling developers to use PHP as a way to generate CSS or JavaScript code to be served to the browser. The preprocessed versions of the assets are served from the **`/wp-content/uploads/enqueueror`** directory.

## Preprocessing CSS

- Create asset files following the required naming conventions and the **`.css.php`** file extension.
- Implement PHP code which outputs valid CSS code.
- Optionally use **`style`** tags when not within the PHP execution context, to benefit from any CSS features supported by your IDE.

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

- Create asset files following the required naming conventions and the **`.js.php`** file extension.
- Implement PHP code which outputs valid JavaScript code.
- Optionally use **`script`** tags when not within the PHP execution context, to benefit from any CSS features supported by your IDE.

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

PHP assets getting preprocessed are ordinary PHP files meant to be used by Enqueueror only. It is recommended that developers prevent their code from being executed due to direct file access, that is, outside of WordPress' context, in order to avoid security or performance issues. To do so, developers may begin their PHP based assets using the following line:

```php
<?php
defined( 'ABSPATH' ) || exit;

// Next lines containing PHP code that generate JavaScript or CSS code
```

As a measure of last resort, Enqueueror utilizes **.htaccess** rules to prevent direct access to any PHP based assets living under the active theme. However, this measure requires web servers that support **.htaccess** rules such as **Apache** or **Litespeed**. 

In addition, Enqueueror will automatically update the **.htaccess** file when switching to a new theme, leaving any previously active theme unprotected. Consequently, developers are encouraged to implement the use the aforementioned code line, even if **.htaccess** rules are taken into account by the web server.