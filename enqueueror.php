<?php

/**
 * Enqueueror
 *
 * Plugin Name:         Enqueueror
 * Description:         Supercharged CSS & JS Coding for WordPress
 * Version:             1.4.0
 * Author:              Panagiotis (Panos) Anastasiadis
 * Author URI:          https://anastasiadis.me
 * License:             GPLv2 or later
 * License URI:         http://www.gnu.org/licenses/gpl-2.0.txt
 * Requires at least:   5.0
 * Tested up to:        6.6
 * Requires PHP:        7.1
 */

defined( 'ABSPATH' ) || exit;

require_once( plugin_dir_path( __FILE__ ) . '/vendor/autoload.php' );

new \panastasiadist\Enqueueror\Core( __FILE__ );
