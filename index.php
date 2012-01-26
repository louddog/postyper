<?php
/*
Plugin Name: Postyper
Description: Create custom post types through the admin system.
Author: Loud Dog
Version: 0.1
Author URI: http://postyper.louddog.com/
*/

define('POSTYPER_VERSION', '0.1');
define('POSTYPER_PATH', dirname(__FILE__));

$postyper = new Postyper();

require_once POSTYPER_PATH.'/functions.php';
require_once POSTYPER_PATH.'/type.php';
require_once POSTYPER_PATH.'/postype.php';
foreach (glob(POSTYPER_PATH.'/types/*') as $path) require_once $path;

class Postyper {
	var $postypes = array();
	var $field_types = array();
	var $postype = null;
	
	function __construct() {
		global $wpdb;
		$wpdb->postypes = $wpdb->prefix.'postypes';
		$wpdb->postype_fields = $wpdb->prefix.'postype_fields';
		
		register_activation_hook(__FILE__, array($this, 'install'));
		if (get_option('postyper_version') != POSTYPER_VERSION) {
			add_action('plugins_loaded', array(&$this, 'install'));
		}

		add_action('init', array(&$this, 'register_saved_postypes'));
		add_action('admin_enqueue_scripts', array(&$this, 'includes'));
		add_action('admin_menu', array(&$this, 'admin_menu'));
		add_action('admin_init', array(&$this, 'save_postype'));
		add_action('admin_notices', array(&$this, 'admin_notices'));
	}
	
	function install() {
		global $wpdb;
		require_once ABSPATH.'wp-admin/includes/upgrade.php';
		
		dbDelta(
			"CREATE TABLE $wpdb->postypes (
				`postype_id` bigint(20) unsigned NOT NULL auto_increment,
				`slug` varchar(256) default NULL,
				`archive` varchar(255) default NULL,
				`singular` varchar(255) default NULL,
				`plural` varchar(255) default NULL,
				PRIMARY KEY  (postype_id),
				KEY slug (slug)
			) $wpdb->collate;"
		);
	
		dbDelta(
			"CREATE TABLE $wpdb->postype_fields (
				`postype_field_id` bigint(20) unsigned NOT NULL auto_increment,
				`postype_id` bigint(20) unsigned NOT NULL,
				`name` varchar(255) default NULL,
				`type` varchar(255) default NULL,
				`context{% if  condition %}
				 
				{% else %}
				 what to do else
				{% endif %}
				` varchar(255) default NULL,
				`label` varchar(255) default NULL,
				`description` longtext NOT NULL,
				`options` longtext NOT NULL,
				PRIMARY KEY  (postype_field_id),
				KEY postype_id (postype_id)
			) $wpdb->collate;"
		);
		
		update_option('postyper_version', POSTYPER_VERSION);
	}
	
	function register_field_type($className) {
		$type = new ReflectionClass($className);
		$this->field_types[$type->getStaticPropertyValue('type')] = array(
			'className' => $className,
		);
	}
	
	function register_saved_postypes() {
		global $wpdb;
		$postype_ids = $wpdb->get_col("SELECT postype_id FROM $wpdb->postypes ORDER BY singular");
		foreach ($postype_ids as $postype_id) {
			$postype = new Postype($postype_id);
			$postype->register();
			$this->postypes[] = $postype;
		}
	}
	
	function includes() {
		$dir = plugin_dir_url(__FILE__);
		
		wp_register_script('postyper_settings', $dir.'js/settings.js', array('jquery'), '0.1', true);
		wp_enqueue_script('postyper_settings');
		
		wp_register_style('postyper_settings', $dir.'css/settings.css', false, '0.1');
		wp_enqueue_style('postyper_settings');
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
		$nonce = plugin_basename(__FILE__);
		include POSTYPER_PATH.'/editor.php'; 
	}
	
	function save_postype() {
		if (!isset($_POST['postyper_save_nonce'])) return;
		if (!wp_verify_nonce($_POST['postyper_save_nonce'], plugin_basename(__FILE__))) die('test');

		global $wpdb;

		$post = stripslashes_deep($_POST);
		$post['fields'] = $this->trim_deep($post['fields']);
		$postype = new Postype($post['postype_id']);
		
		if (isset($_POST['delete'])) {
			$postype_id = $_POST['postype_id'];
			if (!is_numeric($postype_id)) return;
			
			$wpdb->query($wpdb->prepare("DELETE FROM $wpdb->postypes WHERE postype_id = %d", $postype_id));
			$wpdb->query($wpdb->prepare("DELETE FROM $wpdb->postype_fields WHERE postype_id = %d", $postype_id));
			
			$this->add_admin_notice($postype->singular." postype deleted.");
			wp_redirect(admin_url("admin.php?page=postyper"), 302);
		} else {
			
			$postype_data = array(
				'slug' => trim($post['slug']),
				'archive' => trim($post['archive']),
				'singular' => trim($post['singular']),
				'plural' => trim($post['plural']),
			);

			if ($postype->id) {
				$wpdb->update($wpdb->postypes, $postype_data, array('postype_id' => $postype->id));
			} else {
				$wpdb->insert($wpdb->postypes, $postype_data);
				$postype->id = $wpdb->insert_id;
				$postype->slug = $postype_data['slug'];
			}
			
			$fields = array();
			$field_names = array();
			$field_ids_kept = array();
			if (is_array($post['fields'])) {
				foreach ($post['fields'] as $field) {
					if (empty($field['name'])) continue;
					if (in_array($field['name'], $field_names)) continue;
					$field_names[] = $field['name'];
					
					$options = array();
					if (isset($field['options'])) {
						foreach ($field['options'] as $option) {
							$option = trim($option);
							if (!empty($option)) $options[] = $option;
						}
					}
					
					$field_data = array(
						'label' => $field['label'],
						'name' => $field['name'],
						'type' => $field['type'],
						'description' => $field['description'],
						'options' => serialize($options),
						'context' => 'normal',
					);
					
					if (is_numeric($field['postype_field_id'])) {
						$field_ids_kept[] = $field['postype_field_id'];
						$wpdb->update($wpdb->postype_fields, $field_data, array('postype_field_id' => $field['postype_field_id']));
					} else {
						$field_data['postype_id'] = $postype->id;
						$wpdb->insert($wpdb->postype_fields, $field_data);
					}
				}
			}
			
			$delete_field_ids = array();
			foreach ($postype->fields as $field) {
				if (!in_array($field->postype_field_id, $field_ids_kept)) {
					$delete_field_ids[] = $field->postype_field_id;
				}
			}
			
			if (count($delete_field_ids)) {
				$wpdb->query("DELETE FROM $wpdb->postype_fields WHERE postype_field_id IN (".implode(',', $delete_field_ids).")");
				// TODO: Should we delete all meta data using these fields?  I'm thinking no, for now.
			}

			$this->add_admin_notice($postype_data['singular']." postype saved.");
			wp_redirect(admin_url("admin.php?page=postyper_".$postype_data['slug']), 302);
		}
	}
	
	function add_admin_notice($notice) {
		$notices = get_option('postyper_notices', array());
		$notices[] = $notice;
		update_option('postyper_notices', $notices);
	}
	
	function admin_notices() {
		if (isset($_POST['postyper_save_nonce'])) return;
		$notices = get_option('postyper_notices', array());
		if (count($notices)) {
			foreach ($notices as $notice) {
				echo "<div class='updated'><p>$notice</p></div>";
			}
			delete_option('postyper_notices');
		}
	}
	
	function trim_deep($var) {
		if (is_array($var)) {
			$array = array();
			foreach ($var as $key => $value) {
				$array[$key] = $this->trim_deep($value);
			}
			return $array;
		} else if (is_string($var)) {
			return trim($var);
		} else return $var;
	}
}