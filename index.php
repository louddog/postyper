<?php
/*
Plugin Name: Postyper
Description: Create custom post types through the admin system.
Author: Loud Dog
Version: 0.1
Author URI: http://postyper.louddog.com/
*/

define('POSTYPER_PATH', dirname(__FILE__));
define('POSTYPER_URL', plugin_dir_url(__FILE__));

new Postyper();
class Postyper {
	function __construct() {
		global $wpdb;
		$wpdb->postypes = $wpdb->prefix.'postypes';
		
		register_activation_hook(__FILE__, array($this, 'activate'));
		register_deactivation_hook(__FILE__, array($this, 'deactivate'));

		add_action('admin_menu', array(&$this, 'admin_menu'));
		
		include POSTYPER_PATH.'/postype.php';
		
		new Postype(array(
			'slug' => 'employee',
			'archive' => 'employees',
			'singular' => "Employee",
			'plural' => "Employees",
		));
		
		new Postype(array(
			'slug' => 'product',
			'archive' => 'products',
			'singular' => "Products",
			'plural' => "Products",
		));
	}
	
	function activate() {
		global $wpdb;
		$wpdb->query(
			"CREATE TABLE $wpdb->postypes (
				postype_id bigint(20) unsigned NOT NULL auto_increment,
				slug varchar(255) default NULL,
				PRIMARY KEY  (postype_id),
				KEY slug (slug)
			) $wpdb->collate;"
		);
	}
	
	function deactivate() {
		global $wpdb;
		$wpdb->query("DROP TABLE $wpdb->postypes");
	}
	
	function admin_menu() {
		add_options_page(
			_x("Postyper Settings", 'admin settings page title'),
			_x("Postyper", 'admin menu title'),
			'manage_options',
			'postyper',
			array($this, 'settings_page')
		);
	}
	
	function settings_page() {
		include POSTYPER_PATH.'/settings_page.php';
	}
}