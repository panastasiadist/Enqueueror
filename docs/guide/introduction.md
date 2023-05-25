# Introduction

## Why Enqueueror?
Enqueueror enables WordPress developers to efficiently develop and manage their CSS & JavaScript code files, having them conditionally loaded, through the use of naming conventions and a bunch of convenient features which augment their code development workflow.

## A bit of history
Enqueueror was conceived in 2016, initially known as (the non-published plugin) AssetLoader, as a way to organize CSS & JavaScript files in WordPress websites, having them conditionally loaded depending on the requested content, freeing the developer from the hassle of repeatedly writing WordPress hook-based PHP code. 

Nowadays, Enqueueror is utilized in an increasing number of websites, greatly empowering the development experience, while pushing for website loading experience improvements due to its support for content-specific CSS & JavaScript code.

## How it works
Enqueueror actively watches the directory of a (WordPress based) website's active theme for code files containing or producing CSS & JavaScript code which should be pushed to the browser depending on the requested content or any other conditions set by the developer. 

The code files are stored in special directories under the theme's directory while named according to specific conventions that allow Enqueueror to know when and how each code file should be pushed to the browser. 

For example, it is perfectly possible that an (appropriately named) JavaScript code file is being loaded only when the homepage is requested.

## Features
* Conditionally load CSS & JavaScript files, depending on the requested content, setting the ground for an improved website experience.
* Empower code organization and improve code modularity & reuse, by mixing and matching code chunks to meet the needs of the requested content.
* Have code loaded internally or externally as required.
* Use PHP as a preprocessor to generate CSS & JavaScript code depending on the circumstances.

## Requirements
* PHP 7.1 or newer
* WordPress 5.0 or newer