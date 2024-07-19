# Asset Naming

## Filename Structure

An asset's filename is composed of the following components (parts in parentheses are conditional or optional):

> **`[name]`**(-**`[language_code]`**)(.**`[flag_values_seperated_by_dot]`**)**`[file_extension]`**

- The **`[name]`** component describes the content that the asset applies to.
- The **`[language_code]`** component indicates the language version of the content that the asset applies to. A missing language code means the asset applies to all languages on the website.
- The **`[flag_values_seperated_by_dot]`** component contains flag values separated by dots.
- The **`[file_extension]`** component informs Enqueueror about the type of the asset and how it should be processed.

## Content Targeting

The table below contains a list of patterns pertaining to the **`[name]`** component of an asset's filename. These patterns encompass all content targeting scenarios that Enqueueror supports:

| Scenario                                                                               | Name Pattern                        | Example                          |
|----------------------------------------------------------------------------------------|-------------------------------------|----------------------------------|
| Every kind of content                                                                  | **global**                          | global                           |
| Content of any post type                                                               | **type**                            | type                             |
| Specific content by ID                                                                 | **type-id-[id]**                    | type-id-1                        |
| Specific content by slug                                                               | **type-slug-[slug]**                | type-slug-home                   |
| Content of a specific post type                                                        | **type-[post_type]**                | type-post                        |
| Content of a specific post type identified by post ID                                  | **type-[post_type]-id-[id]**        | type-post-id-1                   |
| Content of a specific post type identified by post slug                                | **type-[post_type]-slug-[slug]**    | type-post-id-post1               |
| Archive of any taxonomy                                                                | **term**                            | term                             |
| Specific term archive identified by term ID                                            | **term-id-[term_id]**               | term-id-1                        |
| Specific term archive identified by term slug                                          | **term-slug-[slug]**                | term-slug-category1              |
| Any term archive of a specific taxonomy                                                | **tax-[taxonomy]**                  | tax-category                     |
| Specific term archive of a specific taxonomy identified by taxonomy name and term ID   | **tax-[taxonomy]-term-id-[id]**     | tax-category-term-id-1           |
| Specific term archive of a specific taxonomy identified by taxonomy name and term slug | **tax-[taxonomy]-term-slug-[slug]** | tax-category-term-slug-category1 |
| Archive of an arbitrary user                                                           | **user**                            | user                             |
| Archive of a specific user identified by user ID                                       | **user-id-[user_id]**               | user-id-1                        |
| Every type of archive                                                                  | **archive**                         | archive                          |
| Date archive                                                                           | **archive-date**                    | archive-date                     |
| Archive of a specific post type                                                        | **archive-type-[post_type]**        | archive-type-post                |
| Search page                                                                            | **search**                          | search                           |
| Not found page                                                                         | **not-found**                       | not-found                        |

## Flags

An asset's filename may include one or more special keywords, referred to as "flag values", which dictate how Enqueueror delivers the asset's code in the context of the HTML being served to the browser.   

The flag component of the filename pertains to the **`[flag_values_separated_by_dot]`** component.  

For more information please refer to the [Asset Flags](/guide/asset-flags) section of the Guide.

## Multilingual Support

Enqueueror provides support for multilingual websites that use WPML or Polylang, by conditionally delivering assets based on the language of the content.  

The language specifier for an asset corresponds to the **`[language_code]`** component of an asset's filename.

**Examples:**

| Scenario                       | Language Code | Filename                |
|--------------------------------|---------------|-------------------------|
| Global asset - All languages   | None          | *global.css*            |
| Global asset - English only    | **en**        | *global-en.css*         |
| Global asset - Greek only      | **el**        | *global-el.css*         |
| Post with ID 1 - All languages | None          | *type-post-id-1.css*    |
| Post with ID 1 - English only  | **en**        | *type-post-id-1-en.css* |
| Post with ID 1 - Greek only    | **el**        | *type-post-id-1-el.css* |