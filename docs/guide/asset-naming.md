# Asset Naming

## Filename Structure

An asset's filename is the combination of the following parts (parts in parentheses are conditional or optional):

> `[name]`(-`[language_code]`)(.`[flags_seperated_by_dot]`)`[file_extension]`

- The **`[name]`** part describes the content the asset is applicable to.
- The **`[language_code]`** part signifies the language version of the content the asset is applicable to. Lack of a language code makes the asset applicable to every language of the website. Currently only WPML based multilingual websites are supported.
- The **`[flags_seperated_by_dot]`** part contains flag values separated by dots.
- The **`[file_extension]`** part informs Enqueueror about the type of the asset and how it should process it.

## Content Targeting

The following table contains a list of patterns regarding the **`[name]`** part of an asset's filename covering all content targeting scenarios supported by Enqueueror:

| Scenario                                                  | Name Pattern                        | Example                          |
|-----------------------------------------------------------|-------------------------------------|----------------------------------|
| Every kind of content                                     | **global**                          | global                           |
| Content of arbitrary post type                            | **type**                            | type                             |
| Specific content by ID                                    | **type-id-[id]**                    | type-id-1                        |
| Specific content by slug                                  | **type-slug-[slug]**                | type-slug-home                   |
| Content of a specific post type                           | **type-[post_type]**                | type-post                        |
| Content of a specific post type by post ID                | **type-[post_type]-id-[id]**        | type-post-id-1                   |
| Content of a specific post type by post slug              | **type-[post_type]-slug-[slug]**    | type-post-id-post1               |
| Archive of arbitrary taxonomy                             | **term**                            | term                             |
| Specific term archive by term ID                          | **term-id-[term_id]**               | term-id-1                        |
| Specific term archive by term slug                        | **term-slug-[slug]**                | term-slug-category1              |
| Arbitrary term archive of a specific taxonomy             | **tax-[taxonomy]**                  | tax-category                     |
| Specific term archive of a specific taxonomy by term ID   | **tax-[taxonomy]-term-id-[id]**     | tax-category-term-id-1           |
| Specific term archive of a specific taxonomy by term slug | **tax-[taxonomy]-term-slug-[slug]** | tax-category-term-slug-category1 |
| Archive of an arbitrary user                              | **user**                            | user                             |
| Archive of a specific user by ID                          | **user-id-[user_id]**               | user-id-1                        |
| Every type of archive                                     | **archive**                         | archive                          |
| Date archive                                              | **archive-date**                    | archive-date                     |
| Archive of a specific post type                           | **archive-type-[post_type]**        | archive-type-post                |
| Search page                                               | **search**                          | search                           |
| Not found page                                            | **not-found**                       | not-found                        |

## Flags

An asset's filename may contain one or more special keywords called **flags** which set out how Enqueueror delivers the asset's code in the context of the HTML being served to the browser. The flag part of the filename refers to the **[flags_seperated_by_dot]** part. The following tables explain the supported flags:

| Flag     | Values                     | Default      | Description                                                                                                                                             |
|----------|----------------------------|--------------|---------------------------------------------------------------------------------------------------------------------------------------------------------|
| Location | **head**, **body**         | **head**     | Specifies the location in the HTML document that an asset's raw or invocation code will be delivered.                                                   |
| Source   | **external**, **internal** | **external** | Specifies if an asset's code will be loaded from an external file or if it will be outputted as raw code in the intended location in the HTML document. |

| Flag     | Value        | Default | Description                                                                                          |
|----------|--------------|---------|------------------------------------------------------------------------------------------------------|
| Location | **head**     | ✓       | Asset code will be executed in the **head** section of the HTML document.                            |
| Location | **footer**   |         | Asset code will be executed in the **body** section of the HTML document near the **`</body>`** tag. |
| Source   | **external** | ✓       | Asset code will be loaded from an external file.                                                     |
| Source   | **internal** |         | Asset code will be outputted internally in the HTML document as raw code.                            |

::: info
If a flag value has not been set on filename level, the default value of each flag will be used.
:::

:::tip EXAMPLES
- An asset named **`global.body.external.js`** or simply **`global.body.js`** (**`external`** may be omitted due to being the default value of the **Source** flag) will be loaded from an external file near the end of **`<body>`** HTML document section.
- An asset named **`global.head.internal.css`** or simply **`global.internal.css`** (**`head`** may be omitted due to being the default value of the **Location** flag) will appear as raw code internally within the **`<head>`** HTML document section.
:::

## WPML - Multilingual Support

Enqueueror supports WPML based multilingual websites by delivering assets conditionally, depending on the language of the content. The language specifier for an asset refers to the **`[language_code]`** part of an asset's filename. 

Examples:

| Scenario                                  | WPML Language Code | Filename                |
|-------------------------------------------|--------------------|-------------------------|
| Global asset - All languages              | None               | *global.css*            |
| Global asset - English only               | **en**             | *global-en.css*         |
| Global asset - Greek only                 | **el**             | *global-el.css*         |
| Content of post with ID 1 - All languages | None               | *type-post-id-1.css*    |
| Content of post with ID 1 - English only  | **en**             | *type-post-id-1-en.css* |
| Content of post with ID 1 - Greek only    | **el**             | *type-post-id-1-el.css* |