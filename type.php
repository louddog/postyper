<?php

abstract class PostypeField {
	var $type = false;

	var $postype_field_id = false;
	var $label = '';
	var $name = '';
	var $description = '';
	var $context = 'normal';
	var $options = array();
	
	function __construct($field) {
		if (!$this->type) $this->type = $field->type;
		if (!$this->type) die("Postyper error: no type provided");

		$this->postype_field_id = $field->postype_field_id;
		$this->name = $field->name;
		$this->label = $field->label;
		$this->description = $field->description;
		$this->context = $field->context;
		$this->options = unserialize($field->options);
	}
	
	static function get_fields($post_id) {
		global $wpdb;
		$rows = $wpdb->get_results($wpdb->prepare("SELECT * FROM $wpdb->postype_fields WHERE postype_id = %d", $post_id));
		
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