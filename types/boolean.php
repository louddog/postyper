<?php

class PostypeBoolean extends PostypeField {
	var $type = 'boolean';
	
	function output($post_id) { ?>
		
		<input
			type="checkbox"
			name="postype[<?php echo $this->postype_field_id; ?>]"
			id="postype_field_<?php echo $this->postype_field_id; ?>"
			<?php if ($this->output_value($post_id)) echo "checked"; ?>
		/>

		<label for="postype_field_<?php echo $this->postype_field_id; ?>">
			<?php echo empty($this->description) ? $this->label : $this->description; ?>
		</label>
		
	<?php }
	
	function new_value() {
		return isset($_POST['postype'][$this->postype_field_id]);
	}
}