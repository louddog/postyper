<?php

class PostypeBoolean extends PostypeField {
	var $type = 'boolean';
	
	function output($post_id) { ?>
		
		<input
			type="checkbox"
			name="postype[<?php echo $this->postype_field_id; ?>]"
			<?php if ($this->output_value($post_id)) echo "checked"; ?>
		/>

		<?php $this->output_description(); ?>
		
	<?php }
	
	function new_value() {
		return isset($_POST['postype'][$this->postype_field_id]);
	}
}