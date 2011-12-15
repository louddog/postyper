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
		?>
		
		<style>
			.postyper_fields textarea {
				width: 98%;
			}
		</style>
		
		<fieldset class="postyper_fields">
			<table class="form-table">
				<?php foreach($fields as $field) { ?>
					<?php
						$name = "postyper_".$field['name'];
						$value = false;
						if (isset($db_meta[$field['name']])) $value = $db_meta[$field['name']];
						else if (isset($field['default']) && $field['default']) $value = $field['default'];
					?>

					<tr>
						<th>
							<label for="<?php echo $name; ?>">
								<?php echo $field['label']; ?>
							</label>
						</th>
						<td class="input">
							<?php $this->output_field($field, $name, $value); ?>
						</td>
					</tr>
				<?php } ?>
			</table>
		</fieldset>
		
		<?php
	}
	
	function output_field($field, $name, $value) {
		
		$desc = isset($field['desc']) && !empty($field['desc'])
			? '<br /><span class="description">'.$field['desc'].'</span>'
			: '';
		
		if ($field['type'] == 'text') { ?>
			
			<input
				class="text"
				type="text"
				name="<?php echo $name; ?>"
				id="<?php echo $name; ?>"
				value="<?php echo esc_attr($value); ?>"
			/>
			
			<?php echo $desc; ?>
			
		<?php } else if ($field['type'] == 'int') { ?>
			
			<input
				class="int"
				type="text"
				name="<?php echo $name; ?>"
				id="<?php echo $name; ?>"
				value="<?php echo esc_attr($value); ?>"
			/>
			
			<?php echo $desc; ?>
			
		<?php } else if ($field['type'] == 'date-time') { ?>
			
			<input
				class="date"
				type="text"
				name="<?php echo $name; ?>[date]"
				id="<?php echo $name.'_date'; ?>"
				value="<?php if ($value) echo esc_attr(date('n/j/Y', $value)); ?>"
				placeholder="mm/dd/yyyy"
			/>
			<input
				class="time"
				type="text"
				name="<?php echo $name; ?>[time]"
				id="<?php echo $name.'_time'; ?>"
				value="<?php if ($value) echo esc_attr(date('g:ia', $value)); ?>"
				placeholder="hh:mm am"
			/>

			<?php echo $desc; ?>
			
		<?php } else if ($field['type'] == 'money') { ?>
			
			<input
				class="money"
				type="text"
				name="<?php echo $name; ?>"
				id="<?php echo $name; ?>"
				value="<?php echo esc_attr($value); ?>"
			/>

			<?php echo $desc; ?>
			
		<?php } else if ($field['type'] == 'select') { ?>
			
			<select
				name="<?php echo $name; ?>"
				id="<?php echo $name; ?>"
			>
				<option value=""></option>
				<?php foreach ($field['options'] as $val => $text) { ?>
					<option
						value="<?php echo esc_attr($val); ?>"
						<?php if ($val == $value) echo 'selected'; ?>
					>
						<?php echo $text; ?>
					</option>
				<?php } ?>
			</select>
			
			<?php echo $desc; ?>
			
		<?php } else if ($field['type'] == 'radio') { ?>
			
			<?php $first = true; ?>
			
			<?php foreach ($field['options'] as $val => $text) { ?>
				<?php
					if ($first) $first = false;
					else echo "<br />";
				?>
				
				<input
					type="radio"
					class="radio"
					name="<?php echo $name; ?>"
					id="<?php echo esc_attr($name.'_'.$val); ?>"
					value="<?php echo esc_attr($val); ?>"
				/>
				<label for="<?php echo esc_attr($name.'_'.$val); ?>">
					<?php echo $text; ?>
				</label>		
			<?php } ?>
						
			<?php echo $desc; ?>
			
		<?php } else if ($field['type'] == 'textarea') { ?>
			
			<textarea
				name="<?php echo $name; ?>"
				id="<?php echo $name; ?>"
				cols="60" rows="4"
			><?php echo $value; ?></textarea>
			
			<?php echo $desc; ?>
			
		<?php } else if ($field['type'] == 'boolean') { ?>
			
			<input
				type="checkbox"
				name="<?php echo $name; ?>"
				id="<?php echo $name; ?>"
				<?php if ($value) echo 'checked'; ?>
			/>		

			<?php if (isset($field['desc']) && !empty($field['desc'])) { ?>
				<label for="<?php echo $name; ?>">
					<?php echo $field['desc']; ?>
				</label>
			<?php } ?>

		<?php }
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