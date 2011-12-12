<?php

class Postype {
	var $slug = 'postype';
	var $archive_slug = 'postypes';
	var $singular = "Item";
	var $plural = "Items";
	var $menu_position = 20;
	
	var $meta = array();
	
	function __construct($options) {
		foreach ($options as $option => $value) {
			if (property_exists($this, $option)) {
				$this->$option = $value;
			}
		}
		
		add_action('init', array(&$this, 'register_post_type'));
		add_action('admin_init', array(&$this, 'meta_boxes'));
	}
	
	function register_post_type() {
		register_post_type($this->slug, array(
			'labels' => array(
				'name' => $this->plural,
				'singular_name' => $this->singular,
				'add_new' => "Add New $this->singular",
				'add_new_item' => "Add New $this->singular",
				'edit_item' => "Edit $this->singular",
				'new_item' => "New $this->singular",
				'view_item' => "View $this->singular",
				'search_items' => "Search $this->plural",
				'not_found' => "No $this->plural found",
				'not_found_in_trash' => "No $this->plural found in Trash",
			),
			'public' => true,
			'menu_position' => $this->menu_position,
			'capability_type' => 'post',
			'hierarchical' => false,
			'supports' => array('title', 'editor', 'excerpt', 'thumbnail', 'custom-fields', 'comments', 'revisions'),
			'has_archive' => $this->archive_slug,
			'rewrite' => array(
				'slug' => $this->archive_slug,
				'with_front' => false,
			),
		));
	}
	
	function meta_boxes() {
		$contexts = array();
		foreach ($this->meta as $name => $field) {
			$context = isset($field['context']) ? $field['context'] : 'normal';
			$contexts[$context][$name] = $field;
		}
		
		foreach ($contexts as $context => $fields) {
			add_meta_box(
				$this->slug."-options-$context",
				_x("$this->singular Options", 'postyper options box title'),
				array($this, 'meta_box'),
				$this->slug,
				$context,
				$context == 'normal' ? 'high' : 'core',
				$fields
			);
		}
	}
	
	function meta_box($post, $metabox) {
		$fields = $metabox['args'];
		$db_meta = $this->get_meta($post->ID);
		wp_nonce_field(plugin_basename(__FILE__), 'postyper_nonce');
		include POSTYPER_PATH.'/meta_box.php';
	}
	
	function get_meta($post_id) {
		if (is_object($post_id) && isset($post->ID)) $post_id = $post->ID;
		
		$meta = array();
		
		foreach (get_post_custom($post_id) as $key => $value) {
			if (strpos($key, 'postyper_') !== 0) continue;
			$key = substr($key, strlen('postyper_'));
			$tmp = unserialize($value[0]);
			$value = $tmp ? $tmp : $value[0];
			$meta[$key] = $value;
		}
	}
}