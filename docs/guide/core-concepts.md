# Core Concepts

## Asset

An asset refers to any file located within the **`stylesheets`** or **`scripts`** directories that results in the delivery of CSS or JavaScript code to the browser by the Enqueueror.

An asset file can contain raw CSS or JavaScript code ready to be evaluated by the browser, or PHP code that generates CSS or JavaScript code that will eventually be evaluated by the browser.

There are two types of assets:
- **Scripts** are files located under the **`scripts`** directory that deliver JavaScript code to the browser. These are identified by their **`.js`** and **`.js.php`** extensions.
- **Stylesheets** are files located under the **`stylesheets`** directory that deliver CSS code to the browser. These are identified by their **`.css`** and **`.css.php`** extensions.

## Asset Flags

Flags dictate where in the final HTML document an asset's code will be positioned, as well as how an asset's code should be fetched & evaluated by the browser.

Each flag supports predefined values that instruct the Enqueueror on how to handle an asset. As a result, every asset has all flags set to a certain value, either a default one, or a specific one assigned by the developer.

A developer can assign a flag value using special keywords incorporated into an asset's filename.

## Asset Header

An asset may include an optional header. This is a region preceding any actual code that contains details about the asset, thereby enabling additional functionality to be activated on a per-asset basis.

## Asset Processor

Each asset is distinguished by a file extension that signifies the asset's type and its handling mechanism. 

An asset processor is tasked with managing one or more file extensions to deliver CSS or JavaScript code to the browser. 

Consequently, selecting the correct asset file extension is critically important for delivering valid CSS or JavaScript code that is ready to be executed by the browser.

## Asset Naming

Enqueueror requires an asset's filename to adhere to certain conventions, which are based on specific keywords that describe the content the asset applies to. 

Enqueueror considers information about the requested content, like IDs, slugs, and post types, in order to identify and process assets whose filenames reference the requested content.