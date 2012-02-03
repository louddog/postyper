<?php

abstract class PostypeField {
	var $postype_field_id = false;
	var $type = false;
	var $label = '';
	var $name = '';
	var $description = '';
	var $context = 'normal';
	var $options = false; // options are for lists of possible values, it subclass expects options, set to default (maybe empty array?)
	var $settings = false; // settings are for specific named settings (set to array of defaults in subclass)
	
	function __construct($options = array()) {	
		foreach ($options as $option => $value) {
			if ($option == 'options') {
				if (is_array($this->options)) $this->options = $value;
			} else if ($option == 'settings') {
				if ($this->settings) {
					foreach ($this->settings as $key => $setting) {
						$this->settings[$key]['value'] = $value[$key];
					}
				}
			} else if (property_exists($this, $option)) {
				$this->$option = $value;
			}
		}
		
		add_action('admin_enqueue_scripts', array(&$this, 'admin_enqueue_scripts'));
	}
	
	/*	Factory method for creating an array of PostypeFields
		If $args is a number, the function pulls fields for the postype with the ID $args
		Otherwise, an array of field definitions is expected */
	static function get_fields($args) {
		global $postyper;
		
		if (is_numeric($args)) {
			global $wpdb;
			
			$fields = $wpdb->get_results($wpdb->prepare(
				"SELECT * FROM $wpdb->postype_fields WHERE postype_id = %d",
				$args
			), ARRAY_A);
			
			foreach ($fields as $ndx => $field) {
				$fields[$ndx]['options'] = unserialize($field['options']);
				$fields[$ndx]['settings'] = unserialize($field['settings']);
			}			
		} else if (!is_array($args)) $fields = array();
		
		
		$return_fields = array();
		foreach ($fields as $field) {
			if (!array_key_exists($field['type'], $postyper->field_types)) continue;
			$className = get_class($postyper->field_types[$field['type']]);
			$return_fields[] = new $className($field);
		}
		
		return $return_fields;
	}
	
	function admin_enqueue_scripts() {
		// do nothing
	}

	function output($post_id) { ?>
		
		<input
			type="text"
			name="postype[<?php echo $this->name; ?>]"
			value="<?php echo esc_attr($this->output_value($post_id)); ?>"
		/>
		
		<?php $this->output_description(); ?>
		
	<?php }
	
	function output_value($post_id) {
		return get_post_meta($post_id, $this->name, true);
	}
	
	function field_type_output() {
		// do nothing
	}
	
	function save($post_id) {
		$old = get_post_meta($post_id, $this->name, true);
		$new = $this->new_value();
        if ($new != $old) update_post_meta($post_id, $this->name, $new);
	}
	
	function new_value() {
		return isset($_POST['postype'][$this->name]) ? deep_trim($_POST['postype'][$this->name]) : '';
	}
	
	function output_description() {
		if (empty($this->description)) return;
		echo "<br /><span class='description'>$this->description</span>";
	}
}