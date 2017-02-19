<?php
/*
 * Plugin Name:       File Time Monitor
 * Plugin URI:        https://www.psdtofinal.com
 * Description:       Running regular imports? This plugin allows you to quickly see when your import / export or other important files were updated!
 * Version:           1.0.0
 * Author:            PSD to Final
 * Author URI:        https://www.psdtofinal.com
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       file-time-monitor
*/

/**
* "File Time Monitor" WordPress Plugin
*
* @author PSD to Final <info@psdtofinal.com>
* @license http://opensource.org/licenses/gpl-license.php GNU Public License
*/

// Avoid direct calls to this file
if (!function_exists('add_action')) {
    header('Status: 403 Forbidden');
    header('HTTP/1.1 403 Forbidden');
    exit();
}

// No need to load this in the front end
if (is_admin()) {
	
	// Define relevant paths
	define('FTM_PATH',trailingslashit(plugin_dir_path(__FILE__)));
	define('FTM_URI',plugin_dir_url(__FILE__));
	define('FTM_TEXT_DOMAIN','file-time-monitor');
	define('FTM_VERSION','0.1');
	
	/**
	 * Enqueue all scripts and styles
	 */
	function ftm_enqueue_stuff() {
		
		// Add the admin styles
		wp_register_style('ftm-style',FTM_URI.'/assets/css/ftm.css',array(),FTM_VERSION);
		wp_enqueue_style('ftm-style');
		
		// Add the admin JavaScript
		wp_enqueue_script('ftm-script',FTM_URI.'/assets/js/ftm.js',array(),FTM_VERSION,TRUE);
	}
	add_action('admin_enqueue_scripts','ftm_enqueue_stuff');
	
	// Include the class file
	require_once FTM_PATH.'lib/FileTimeMonitor.php';
	
	if (class_exists('FileTimeMonitor')) {
		
		// Instantiate the class
		$FileTimeMonitor = new FileTimeMonitor(__FILE__);
	}		
}