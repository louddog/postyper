<?php

define('POSTYPE_PATH', dirname(__FILE__));
require_once POSTYPE_PATH.'/type.php';
foreach (glob(POSTYPE_PATH.'/types/*') as $path) {
	require_once $path;
}

class Postype {
	protected static $postypes = array();
	
	var $id = 0;
	var $slug = '';
	var $archive = '';
	var $singular = "";
	var $plural = "";
	var $menu_position = 20;
	var $fields = array();

	function __construct($options) {
		global $wpdb;
		$register = true;
		$where = false;
		
		if (is_numeric($options)) $where = "postype_id = %d";
		else if (is_string($options)) $where = "slug = %s";
		
		if ($where) {
			if ($postype = $wpdb->get_row($wpdb->prepare("SELECT * FROM $wpdb->postypes WHERE $where", $options))) {
				$this->id = $postype->postype_id;
				$this->slug = $postype->slug;
				$this->archive = $postype->archive;
				$this->singular = $postype->singular;
				$this->plural = $postype->plural;
				$this->fields = PostypeField::get_fields($this->id);
			} else $register = false;
		} else {
			foreach ($options as $option => $value) {
				if (property_exists($this, $option)) {
					$this->$option = $value;
				}
			}
		}
		
		if ($register) {
			self::$postypes[$postype->slug] = $this;
			add_action('init', array(&$this, 'register'));
			add_action('admin_enqueue_scripts', array(&$this, 'includes'));
			add_action('admin_init', array(&$this, 'meta_boxes'));
			add_action('save_post', array(&$this, 'save_post'));
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
			'public' => true,
			'menu_position' => $this->menu_position,
			'capability_type' => 'post',
			'hierarchical' => false,
			'supports' => array('title', 'editor', 'excerpt', 'thumbnail', 'custom-fields', 'comments', 'revisions'),
			'has_archive' => $this->archive,
			'rewrite' => array(
				'slug' => $this->archive,
				'with_front' => false,
			),
		));
	}
	
	function includes() {
		$dir = plugin_dir_url(__FILE__);
		
		wp_enqueue_script('jquery-ui');
		
		wp_register_style('postyper_meta', $dir.'css/jquery-ui.css', false, '0.1');
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
			global $postyper;
			foreach ($postyper->field_types as $type => $attrs) {
				call_user_func(array($attrs['className'], 'field_type_output'));
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
	
	public function options($post, $name) {
		$type = get_post_type($post);
		foreach (self::$postypes[$type]->fields as $field) {
			if ($field->name == $name) return $field->options;
		}
		return false;
	}
	
	public function label($post, $name, $alt = 'n/a') {
		$options = self::options($post, $name);
		$key = get_post_meta($post->ID, $name, true);
		return $options && isset($options[$key]) ? $options[$key] : $alt;
	}
}