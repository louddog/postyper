<?php

class Postype {
	var $slug = 'postype';
	var $archive_slug = 'postypes';
	var $singular = "Item";
	var $plural = "Items";
	var $menu_position = 20;
	
	function __construct($options) {
		foreach ($options as $option => $value) {
			if (property_exists($this, $option)) {
				$this->$option = $value;
			}
		}
		
		add_action('init', array(&$this, 'register_post_type'));
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
}