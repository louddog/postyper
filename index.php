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

require_once(POSTYPER_PATH.'/postype.php');

new Postyper();
class Postyper {
	var $postypes = array();
	
	public static $types = array(
		'text',
		'int',
		'date',
		'time',
		'date-time',
		'time-range',
		'money',
		'radio',
		'select',
		'checkbox',
		'textarea',
		'slider',
		'range'
	);
	
	function __construct() {
		global $wpdb;
		$wpdb->postypes = $wpdb->prefix.'postypes';
		$wpdb->postype_fields = $wpdb->prefix.'postype_fields';
		
		$postype_ids = $wpdb->get_col("SELECT postype_id FROM $wpdb->postypes ORDER BY singular");
		foreach ($postype_ids as $postype_id) {
			$this->postypes[] = new Postype($postype_id);
		}
		
		register_activation_hook(__FILE__, array($this, 'activate'));
		register_deactivation_hook(__FILE__, array($this, 'deactivate'));
		add_action('admin_menu', array(&$this, 'admin_menu'));
	}
	
	function activate() {
		global $wpdb;
		
		$wpdb->query(
			"CREATE TABLE $wpdb->postypes (
				postype_id bigint(20) unsigned NOT NULL auto_increment,
				slug varchar(255) default NULL,
				archive varchar(255) default NULL,
				singular varchar(255) default NULL,
				plural varchar(255) default NULL,
				PRIMARY KEY  (postype_id),
				KEY slug (slug)
			) $wpdb->collate;"
		);
		
		$wpdb->query(
			"CREATE TABLE $wpdb->postype_fields (
				postype_field_id bigint(20) unsigned NOT NULL auto_increment,
				postype_id bigint(20) unsigned NOT NULL,
				name varchar(255) default NULL,
				type varchar(255) default NULL,
				label varchar(255) default NULL,
				description longtext NOT NULL,
				options longtext NOT NULL,
				PRIMARY KEY  (postype_field_id),
				KEY postype_id (postype_id)
			) $wpdb->collate;"
		);
	}
	
	function deactivate() {
		global $wpdb;
		$wpdb->query("DROP TABLE $wpdb->postypes");
		$wpdb->query("DROP TABLE $wpdb->postype_fields");
	}
	
	function admin_menu() {
		add_menu_page(
			_x("Postyper Settings", 'admin settings page title'),
			_x("Postyper", 'postyper admin menu title'),
			'manage_options',
			'postyper',
			array($this, 'settings')
		);

		add_submenu_page(
			'postyper',
			"Postyper | New Post Type",
			"New Post Type",
			'manage_options',
			'postyper-new',
			array($this, 'postype_settings')
		);
		
		foreach ($this->postypes as $postype) {
			add_submenu_page(
				'postyper',
				"Postyper | ".$postype->singular,
				$postype->singular,
				'manage_options',
				'postyper_'.$postype->slug,
				array($this, 'postype_settings')
			);
		}
	}
	
	function settings() {
		include POSTYPER_PATH.'/settings.php';
	}
	
	function postype_settings() {
		include POSTYPER_PATH.'/postype_settings.php';
	}
}