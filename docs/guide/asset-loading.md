# Asset Loading

Normally, barring any interference from third-party code or plugins, asset raw or invocation (loading) code is fetched (if necessary) and evaluated in the order it appears within the HTML document. Please refer to the [Asset Ordering](/guide/asset-ordering) section of the guide for more information on how Enqueueror organizes assets within an HTML document.

As far as external script files are concerned, the modern web has provided us with two significant attributes, **`async`** and **`defer`**. A detailed discussion of their operation is beyond the scope of this guide, but you are welcome to consult [MDN on the subject](https://developer.mozilla.org/en-US/docs/Web/HTML/Element/script) for more information.

Enqueueror leverages support for the **`async`** and **`defer`** attributes to enhance website loading experiences. For more details, see the [Asset Flags](/guide/asset-flags) section.

:::info
- Loading and evaluation of assets marked with the **`async`** or **`defer`** attribute deviate from the typical processing stages outlined at the beginning of this section, due to the intrinsic nature of these attributes. As a result, particularly when using these attributes, there is no guarantee that the code of assets marked with these attributes will execute in the order they appear within the HTML document.
- In the context of WordPress, in scenarios where dependencies are involved, marking script assets with these attributes only makes them eligible for utilizing the respective attributes. Actually, WordPress may not necessarily honor these attributes and may even modify them as needed, to minimize the risk of code breakage due to the possible unavailability of a dependency when it is required by another asset.
:::