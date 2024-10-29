<?php
/*
 * Plugin Name: AS Sitemap
 * Plugin URI: 
 * Description: Simple html sitemap for your website
 * Version: 1.0
 * Author: Sullivan ATATRI
 * Author URI:
 * Text Domain: as-sitemap
 * Domain Path: /languages/
 * Depends: Timber
 * Namespace : AS_Sitemap
 * License: GPL v3
 */
require_once('Sitemap.php');

new AS_Sitemap\Sitemap();

register_activation_hook( __FILE__, array( 'AS_Sitemap\Sitemap', 'on_plugin_activation' ) );
?>
