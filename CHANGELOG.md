# Changelog

## 1.4.0 - July 19, 2024

### Added
- Official, direct support for Polylang.
- Support for async and defer script loading through the new Loading flag.

### Changed
- WordPress 6.6 compatibility update.

### Fixed
- Preprocessed, PHP-based assets will now be updated when their source files are updated.

## 1.3.1 - July 25, 2023

### Fixed
- Non string language codes returned by WPML filters are now properly handled.

## 1.3.0 - May 25, 2023

### Added
- Support for loading CSS internally before the **`</body>`** closing tag.
- Support for loading CSS externally before the **`</body>`** closing tag.
- Support for using internal CSS & JavaScript assets as dependencies both in **`<head>`** and **`<body>`**.
- Support for using external CSS assets as dependencies within **`<body>`**.
- Support for mixing internal and external assets within the asset dependency chain.

### Changed
- Better support for loading assets before the **`</body>`** closing tag.

## 1.2.0 - May 04, 2022

### Added
- Local assets and external script and stylesheets may be used as dependencies.
- Direct access to PHP preprocessed assets is prevented using .htaccess rules.

### Changed
- Preprocessed assets are now served from /wp-content/uploads/enqueueror.

## 1.1.1 - January 19, 2022

### Fixed
- Handling of multiple dependencies.

## 1.1.0 - January 15, 2022

### Added
- Introduced support for header in assets.
- Introduced support for asset dependencies.

### Fixed
- Asset order rules not always respected.
- Error if WPML is activated but not set up.

## 1.0.0 - January 9, 2022
First release.