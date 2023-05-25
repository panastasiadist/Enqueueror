# Core Concepts

## Asset

An asset is every file under the **`stylesheets`** or **`scripts`** directories which results to CSS or JavaScript code being delivered to the browser by Enqueueror. An asset file may contain raw CSS or JavaScript code to be executed by the browser, or PHP code which produces CSS or JavaScript code that will be pushed to the browser.

There are two types of assets:
- **Scripts** that deliver JavaScript code to the browser, located under the **`scripts`** directory and recognized by the **`.js`** and **`.js.php`** file extensions.
- **Stylesheets** that deliver CSS code to the browser, located under the **`stylesheets`** directory and recognized by the **`.css`** and **`.css.php`** file extensions.

## Asset Flags

Flags set out where in the final HTML document an asset's code will be located, as well as, how an asset's code should be loaded by the browser. 

Every flag supports predefined values instructing Enqueueror how to act regarding an asset. Consequently, every asset has all flags set to some value, either a default one, or a specific one set by the developer. A flag value may be set by the developer using special keywords as part of an asset's filename.

## Asset Header

An asset may contain an optional header, that is, a region before any actual code which contains details about the asset, allowing for additional functionality to be enabled on a per-asset basis.

## Asset Processor

Each asset is characterized by a file extension indicating the type of the asset and how it should be handled. An asset processor is charged with processing one or more file extensions in order to deliver CSS or JavaScript code to the browser. As a result, usage of the right asset file extension is of utmost importance to delivering valid CSS or JavaScript code ready to be executed by the browser.

## Asset Naming

Enqueueror requires that an asset's filename follows special conventions based on specific keywords, describing the content the asset is applicable to. Enqueueror takes into account details about the requested content, such as IDs, slugs, post types, in order to find and process assets whose filename refers to the requested content.
