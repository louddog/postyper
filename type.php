<?php

abstract class PostypeField {
	static $type = false;

	var $postype_field_id = false;
	var $label = '';
	var $name = '';
	var $description = '';
	var $context = 'normal';
	var $options = array();
	
	function __construct($field) {
		print_r(self::$type);

		$this->postype_field_id = $field->postype_field_id;
		$this->name = $field->name;
		$this->label = $field->label;
		$this->description = $field->description;
		$this->context = $field->context;
		$this->options = unserialize($field->options);
		
		add_action('admin_enqueue_scripts', array(&$this, 'admin_enqueue_scripts'));
	}
	
	function admin_enqueue_scripts() {
		// do nothing
	}
	
	static function get_fields($postype_id) {
		global $wpdb;
		$rows = $wpdb->get_results($wpdb->prepare("SELECT * FROM $wpdb->postype_fields WHERE postype_id = %d", $postype_id));
		
		$fields = array();
		foreach ($rows as $row) {
			if (class_exists($row->type)) { 
				$fields[] = new $row->type($row);
			}
		}
		
		return $fields;
	}

	function output($post_id) { ?>
		
		<input
			type="text"
			name="postype[<?php echo $this->postype_field_id; ?>]"
			value="<?php echo esc_attr($this->output_value($post_id)); ?>"
		/>
		
		<?php $this->output_description(); ?>
		
	<?php }
	
	function output_value($post_id) {
		return get_post_meta($post_id, $this->name, true);
	}
	
	static function field_type_output() {
		echo "<p>".self::$type."</p>";
	}
	
	function save($post_id) {
		$old = get_post_meta($post_id, $this->name, true);
		$new = $this->new_value();;
        if ($new != $old) update_post_meta($post_id, $this->name, $new);
	}
	
	function new_value() {
		return isset($_POST['postype'][$this->postype_field_id]) ? trim($_POST['postype'][$this->postype_field_id]) : '';
	}
	
	function output_description() {
		if (empty($this->description)) return;
		echo "<br /><span class='description'>$this->description</span>";
	}
}