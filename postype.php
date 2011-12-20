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
		add_action('admin_enqueue_scripts', array(&$this, 'includes'));
		add_action('admin_init', array(&$this, 'meta_boxes'));
		add_action('save_post', array(&$this, 'save_post'));
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
	
	function includes() {
		wp_register_script('postyper_meta', POSTYPER_URL.'js/meta.js', array('jquery-ui-datepicker', 'jquery-ui-slider'), '0.1', true);
		wp_enqueue_script('postyper_meta');
		
		wp_register_style('postyper_meta', POSTYPER_URL.'css/meta.css', false, '0.1');
		wp_enqueue_style('postyper_meta');
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

	function meta_box($post, $metabox) { ?>

		<table class="form-table">
			<?php foreach($metabox['args'] as $field) {
				$name = "postyper_".$field['name'];
				$value = get_post_meta($post->ID, $name, true);
				$desc = isset($field['desc']) && !empty($field['desc'])
					? '<br /><span class="description">'.$field['desc'].'</span>'
					: '';
				?>
				<tr>
					<th>
						<label for="postyper_<?php echo $field['name']; ?>">
							<?php echo $field['label']; ?>
						</label>
					</th>
					<td class="input">
						<?php include POSTYPER_PATH.'/inputs.php'; ?>
					</td>
				</tr>
			<?php } ?>
		</table>
		
		<?php wp_nonce_field(plugin_basename(__FILE__), 'postyper_nonce'); ?>

	<?php }
	
	function save_post($post_id) {
		if (!isset($_POST['postyper_nonce'])) return $post_id;
		if (!wp_verify_nonce($_POST['postyper_nonce'], plugin_basename(__FILE__))) return $post_id;
	    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return $post_id;
		if ($_POST['post_type'] != $this->slug) return $post_id;
		if (!current_user_can('edit_post', $post_id)) return $post_id;

	    foreach ($this->meta as $field) {
			$name = 'postyper_'.$field['name'];
			
			switch ($field['type']) {
				case 'checkbox':
					$new = isset($_POST[$name]);
					break;
					
				case 'date':
					$new = strtotime($_POST[$name]);
					if ($new) {
						$d = getdate($new);
						$new = mktime(0, 0, 0, $d['mon'], $d['mday'], $d['year']);
					}
					break;
					
				case 'time':
					$new = strtotime($_POST[$name]);
					break;
					
				case 'date-time':
					$date = strtotime($_POST[$name]['date']);
					$time = strtotime($_POST[$name]['time']);
					$new = 0;
					if ($date && $time) {
						$d = getdate($date);
						$t = getdate($time);
						$new = mktime($t['hours'], $t['minutes'], $t['seconds'], $d['mon'], $d['mday'], $d['year']);
					}
					break;
					
				case 'time-range':
					$new = array(
						'starts' => strtotime($_POST[$name]['starts']),
						'ends' => strtotime($_POST[$name]['ends']),
					);
					break;
					
				case 'int':
					$new = intval($_POST[$name]);
					break;
					
				case 'money':
					$new = floatval($_POST[$name]);
					break;
					
				default:
					$new = isset($_POST[$name]) ? trim($_POST[$name]) : '';
			}

	        $old = get_post_meta($post_id, $name, true);
	        if ($new != $old) update_post_meta($post_id, $name, $new);
	    }
	}
}