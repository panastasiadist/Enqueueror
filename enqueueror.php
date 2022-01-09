<?php

/**
 * Enqueueror
 * 
 * Plugin Name:         Enqueueror
 * Description:         Assisted WordPress Asset Preprocessing & Enqueueing
 * Version:             1.0.0
 * Author:              Panos Anastasiadis
 * Author URI:          https://anastasiadis.me
 * License:             GPLv2 or later
 * License URI:         http://www.gnu.org/licenses/gpl-2.0.txt
 * Requires at least:   4.6
 * Tested up to:        5.8
 * Requires PHP:        7.1
 */

require_once( plugin_dir_path( __FILE__ ) . '/vendor/autoload.php' );

$core = new \panastasiadist\Enqueueror\Core();