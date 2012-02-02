<?php

postyper_register_field_type('PostypeBoolean');

class PostypeBoolean extends PostypeField {
	var $type = 'boolean';
	
	function output($post_id) { ?>
		
		<input
			type="checkbox"
			name="postype[<?php echo $this->name; ?>]"
			id="postype_field_<?php echo $this->name; ?>"
			<?php if ($this->output_value($post_id)) echo "checked"; ?>
		/>

		<label for="postype_field_<?php echo $this->name; ?>">
			<?php echo empty($this->description) ? $this->label : $this->description; ?>
		</label>
		
	<?php }
	
	function new_value() {
		return strlen(parent::new_value()) ? true : false;
	}
}