# Asset Flags

Flags determine where in the HTML document the code of an asset should be outputted and how the browser should handle its loading and evaluation.

Each flag supports a specific set of predefined values. These values define how a particular aspect of an asset should be handled by Enqueueror and the browser.

A flag value is part of an asset's filename as illustrated below. Furthermore, multiple values from different flags are supported, provided that they are separated by the **dot** character.

> `[name]`(-`[language_code]`)(.**`[flag_values_seperated_by_dot]`**)`[file_extension]`

## All Assets

### The Source Flag

The **Source** flag determines whether the code of an asset will be loaded from an external file or directly included as raw code at the intended position in the HTML document.

| Value        | Default | Description                                                                      |
|--------------|---------|----------------------------------------------------------------------------------|
| **external** | ✓       | The asset's code will be sourced from an external file.                          |
| **internal** |         | The asset's code will be embedded directly within the HTML document as raw code. |

### The Location Flag

The **Location** flag indicates where in the HTML document an asset's raw or loading code will be located.

| Value      | Default | Description                                                                                                 |
|------------|---------|-------------------------------------------------------------------------------------------------------------|
| **head**   | ✓       | The asset's code will be executed in the **head** section of the HTML document.                             |
| **footer** |         | The asset's code will be executed in the **body** section of the HTML document, near the **`</body>`** tag. |

## External Script-Only Assets

### The Loading Flag

The **Loading** flag applies exclusively to external script assets; it dictates how the browser will fetch and evaluate the script file.

| Value     | Default | Description                                                                                                    |
|-----------|---------|----------------------------------------------------------------------------------------------------------------|
| none      | ✓       | The script will be fetched and evaluated as usual, in such a way that it blocks the parser.                    |
| **async** |         | The script will be fetched in parallel to parsing and will be evaluated as soon as it is available.            |
| **defer** |         | The script will be fetched in parallel to parsing and will be evaluated immediately after parsing is complete. |

:::info 
Enqueueror 1.4 or later and WordPress 6.3 or later are required to use the **Loading** flag. Specifying it while running older versions of Enqueueror or WordPress will have no effect.
:::

:::warning
- In scenarios where dependencies are involved, marking script assets with these flags only designate them as eligible for utilizing the respective attributes.
- Indeed, WordPress may not necessarily respect these attributes in an effort to minimize the risk of code breakage. This is due to the nature of these attributes, which could result in a dependency not being ready when another asset requires it.
:::

---

::: info
- If a flag value has not been set at the filename level, the flag's default value will be used.
- If multiple values are assigned to the same flag, only the final value will be considered.
:::

:::tip EXAMPLES
- An asset named **`global.body.external.js`** or simply **`global.body.js`** (the term **`external`** can be omitted since it is the default value of the **Source** flag) will be fetched from an external file placed near the end of the **`<body>`** section of the HTML document.
- An asset named **`global.head.internal.css`** or simply **`global.internal.css`** (the term **`head`** can be omitted since it is the default value of the **Location** flag) will be embedded as raw code directly within the **`<head>`** section of the HTML document.
:::