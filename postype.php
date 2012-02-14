<?php

class Postype {
	static $postypes = array();
	
	var $id = 0;

	var $slug = false;
	var $singular = false;
	var $plural = false;

	var $public = true;
	var $show_ui = true;
	var $menu_position = 20;
	var $capability_type = 'post';
	var $hierarchical = false;
	var $supports = array('title', 'editor', 'excerpt', 'thumbnail', 'custom-fields', 'comments', 'revisions', 'page-attributes');
	var $has_archive = true;
	var $archive = false;
	var $with_front = false;
	var $feeds = true;
	var $pages = true;
	
	var $fields = array();
	var $taxonomies = array();

	function __construct($options = false) {
		global $wpdb;
		
		if ($options) {
			if (is_array($options)) {
				foreach ($options as $option => $value) {
					if ($option == 'fields') $this->fields = PostypeField::get_fields($value);
					else if (property_exists($this, $option)) $this->$option = $value;
				}
			} else {
				$where = is_numeric($options) ? "postype_id = %d" : "slug = %s";
				$row = $wpdb->get_row($wpdb->prepare("SELECT * FROM $wpdb->postypes WHERE $where", $options));
				if ($row) {
					$this->id = $row->postype_id;
					$this->slug = $row->slug;
					$this->archive = $row->archive;
					$this->singular = $row->singular;
					$this->plural = $row->plural;
					$this->fields = PostypeField::get_fields($row->postype_id);
				}
			}
			
			if ($this->slug) {
				self::$postypes[$this->slug] = $this;

				if (!$this->singular) $this->singular = ucwords($this->slug);
				if (!$this->plural && !empty($this->singular)) $this->plural = $this->singular.'s';

				add_action('init', array(&$this, 'register'));

				add_action('admin_enqueue_scripts', array(&$this, 'includes'));
				add_action('admin_init', array(&$this, 'meta_boxes'));
				add_action('save_post', array(&$this, 'save_post'));
				add_action('trash_post', array(&$this, 'trash_post'));

				add_action('manage_edit-'.$this->slug.'_columns', array(&$this, 'columns'));
				add_action('manage_posts_custom_column', array(&$this, 'column'));
				add_filter('manage_edit'.$this->slug.'_sortable_columns', array(&$this, 'column_sort'));
				add_action('request', array(&$this, 'column_orderby'));
			}
		}
	}

	function register() {
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
			'public' => $this->public,
			'show_ui' => $this->show_ui,
			'menu_position' => $this->menu_position,
			'capability_type' => $this->capability_type,
			'hierarchical' => $this->hierarchical,
			'supports' => $this->supports,
			'has_archive' => $this->has_archive,
			'rewrite' => $this->has_archive || $this->archive
				? array(
					'slug' => $this->archive ? $this->archive : $this->slug,
					'with_front' => $this->with_front,
					'feeds' => $this->feeds,
					'pages' => $this->pages,
				)
				: false,
		));
		
		foreach ($this->taxonomies as $tax) {
			$this->register_taxonomy($tax['name'], $tax);
			// TODO: enable taxonomy meta
		}
	}
	
	function register_taxonomy($tax, $args) {
		extract(shortcode_atts(array(
			'singular' => "Item",
			'plural' => "Items",
			'rewrite' => false,
		), $args));
			
		register_taxonomy($tax, $this->slug, array(
			'labels' => array(
				'name' => $plural,
				'singular_name' => $singular,
				'search_items' => "Search $plural",
				// 'popular_items' => "Popular $plural", // seems to force the appearance of the tag cloud
				'all_items' => "All $plural",
				'parent_item' => "Parent $singular",
				'parent_item_colon' => "Parent $singular:",
				'edit_item' => "Edit $singular", 
				'update_item' => "Update $singular",
				'add_new_item' => "Add New $singular",
				'new_item_name' => "New $singular Name",
				'separate_items_with_commas' => "Separate $plural with commas",
				'add_or_remove_items' => "Add or remove $plural",
				'choose_from_most_used' => "Choose from the most used $plural",
				'menu_name' => $singular,
			),
			'hierarchical' => true,
			'rewrite' => $rewrite ? array(
				'slug' => $rewrite,
				'with_front' => false,
				'hierarchical' => true,
			) : false,
		));
	}
	
	function includes() {
		$dir = plugin_dir_url(__FILE__);
		
		wp_enqueue_script('jquery-ui');
		
		wp_register_style('postyper_meta', $dir.'css/jquery-ui.css', false, POSTYPER_VERSION);
		wp_enqueue_style('postyper_meta');
	}

	function meta_boxes() {
		$metaboxes = array();
		foreach ($this->fields as $field) {
			$metaboxes[$field->context][$field->name] = $field;
		}

		foreach ($metaboxes as $context => $fields) {
			add_meta_box(
				$this->slug."-options-$context",
				_x("$this->singular Options", 'postyper options box title'),
				array($this, 'meta_box'),
				$this->slug,
				$field->context,
				$field->context == 'normal' ? 'high' : 'core',
				$fields
			);
		}
	}

	function meta_box($post, $metabox) { ?>
		
		<?php wp_nonce_field(plugin_basename(__FILE__), 'postyper_meta_nonce'); ?>
		
		<table class="form-table">
			<?php foreach($metabox['args'] as $field) { ?>
				<tr>
					<th>
						<label><?php echo $field->label; ?></label>
					</th>
					<td class="input">
						<?php $field->output($post->ID); ?>
					</td>
				</tr>
			<?php } ?>
		</table>
		
		<?php
			foreach (Postyper::field_types() as $type) {
				$type->field_type_output();
			}
		?>
		
	<?php }
	
	function save_post($post_id) {
		if (!isset($_POST['postyper_meta_nonce'])) return $post_id;
		if (!wp_verify_nonce($_POST['postyper_meta_nonce'], plugin_basename(__FILE__))) return $post_id;
	    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return $post_id;
		if ($_POST['post_type'] != $this->slug) return $post_id;
		if (!current_user_can('edit_post', $post_id)) return $post_id;
		
		foreach ($this->fields as $field) {
			$field->save($post_id);
		}
	}
	
	function trash_post($post_id) {
		// TODO: trash post
	}
	
	// TODO: handle columns
	function columns($columns) {
		return $columns;
	}
	
	function column($column) {
	}

	function column_sort($columns) {
		return $columns;
	}
	
	function column_orderby($vars) {
		return $vars;
	}
	
	public function options($post, $name) {
		$type = get_post_type($post);
		foreach (self::$postypes[$type]->fields as $field) {
			if ($field->name == $name) return $field->options;
		}
		return false;
	}
}