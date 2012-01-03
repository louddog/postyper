<?php

class Postype {
	protected static $postypes = array();
	
	var $id = 0;
	var $slug = 'postype';
	var $archive = 'postypes';
	var $singular = "Item";
	var $plural = "Items";
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
				
				$this->fields = $wpdb->get_results("SELECT * FROM $wpdb->postype_fields WHERE postype_id = $this->id");
				
				foreach ($this->fields as $ndx => $field) {
					$this->fields[$ndx]->options = unserialize($field->options);
				}
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
		wp_register_script('postyper_meta', POSTYPER_URL.'js/meta.js', array('jquery-ui-datepicker', 'jquery-ui-slider'), '0.1', true);
		wp_enqueue_script('postyper_meta');
		
		wp_register_style('postyper_meta', POSTYPER_URL.'css/meta.css', false, '0.1');
		wp_enqueue_style('postyper_meta');
	}

	function meta_boxes() {
		$contexts = array();
		foreach ($this->fields as $field) {
			$context = isset($field->context) ? $field->context : 'normal';
			$contexts[$context][$field->name] = $field;
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
				$name = "postyper_field_$field->postype_field_id";
				$value = get_post_meta($post->ID, "postyper_$field->name", true);
				$description = isset($field->description) && !empty($field->description)
					? "<br /><span class='description'>$field->description</span>"
					: '';
				?>
				<tr>
					<th>
						<label for="<?php echo $name; ?>">
							<?php echo $field->label; ?>
						</label>
					</th>
					<td class="input">
						<?php include POSTYPER_PATH.'/inputs.php'; ?>
					</td>
				</tr>
			<?php } ?>
		</table>
		
		<?php wp_nonce_field(plugin_basename(__FILE__), 'postyper_meta_nonce'); ?>

	<?php }
	
	function save() {
		global $wpdb;
		
		$data = array(
			'slug' => $this->slug,
			'archive' => $this->archive,
			'singular' => $this->singular,
			'plural' => $this->plural,
		);

		if ($this->id) {
			$wpdb->update($wpdb->postypes, $data, array('postype_id' => $this->id));
		} else {
			$wpdb->insert($wpdb->postypes, $data);
			$this->id = $wpdb->insert_id;
		}
		
		foreach ($this->fields as $ndx => $field) {
			$data = array(
				'name' => $field->name,
				'type' => $field->type,
				'label' => $field->label,
				'description' => $field->description,
				'options' => serialize($field->options),
			);
			
			if ($field->postype_field_id) {
				$wpdb->update($wpdb->postype_fields, $data, array('postype_field_id' => $field->postype_field_id));
			} else {
				$data['postype_id'] = $this->id;
				$wpdb->insert($wpdb->postype_fields, $data);
				$this->fields[$ndx]->postype_field_id = $wpdb->insert_id;
			}
		}
		
		// TODO: delete any fields no longer present
	}
	
	function save_post($post_id) {
		if (!isset($_POST['postyper_meta_nonce'])) return $post_id;
		if (!wp_verify_nonce($_POST['postyper_meta_nonce'], plugin_basename(__FILE__))) return $post_id;
	    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return $post_id;
		if ($_POST['post_type'] != $this->slug) return $post_id;
		if (!current_user_can('edit_post', $post_id)) return $post_id;

	    foreach ($this->fields as $field) {
			$name = "postyper_field_$field->postype_field_id";
			
			switch ($field->type) {
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
					
				case 'range':
					$new = serialize(array(
						'low' => $_POST[$name]['low'],
						'high' => $_POST[$name]['high'],
					));
					break;
					
				default:
					$new = isset($_POST[$name]) ? trim($_POST[$name]) : '';
			}

	        $old = get_post_meta($post_id, $field->name, true);
	        if ($new != $old) update_post_meta($post_id, $field->name, $new);
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