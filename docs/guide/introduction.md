# Introduction

## Why Enqueueror?

Enqueueror empowers WordPress developers to manage and develop their CSS & JavaScript files efficiently. It facilitates conditional CSS & JavaScript loading through the use of naming conventions and provides numerous features to enhance the code development workflow.

## A bit of history

Enqueueror was initially conceived in 2016 as (the non-published plugin) AssetLoader. It has always been used to organize CSS & JavaScript files in WordPress websites and allow for their conditional loading based on the requested content. This has eliminated the need for developers to repeatedly write WordPress hook-based PHP code.

Today, Enqueueror is being utilized in a growing number of websites, significantly enhancing the development experience. It also promotes improvements in website loading experience due to its support for content-specific CSS & JavaScript code, as well as other performance-oriented features.

## How it works

Enqueueror monitors the directory of the active theme in a WordPress-based website for code files that contain or generate CSS & JavaScript code. These files need to be pushed to the browser based on the requested content or any other conditions set by the developer.

The aforementioned code files are stored in specific directories within the theme's directory, and they are named according to certain conventions. These conventions allow Enqueueror to determine when and how each code file should be used by the browser.

For instance, it is entirely feasible for a JavaScript code file, if appropriately named, to load only when the homepage is requested.

## Features

* Conditionally load CSS & JavaScript files based on the requested content, setting the stage for an enhanced website experience.
* Enhance code organization and improve modularity and reuse of code by combining code chunks to meet the requirements of the requested content.
* Load code internally or externally as needed.
* Make use of the modern **async** and **defer** script tag attributes for an improved website loading experience.
* Utilize PHP as a preprocessor to generate CSS & JavaScript code depending on the circumstances.

## Requirements
* PHP 7.1 or newer
* WordPress 5.0 or newer