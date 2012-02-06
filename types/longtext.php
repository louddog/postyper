<?php

Postyper::register_field_type('PostypeLongText');

class PostypeLongText extends PostypeField {
	var $type = 'longtext';
	
	function output($post_id) { ?>
		
		<textarea class="postyper_longtext" name="postype[<?php echo $this->name; ?>]"><?php
			echo $this->output_value($post_id);
		?></textarea>

		<?php $this->output_description(); ?>
		
	<?php }
	
	function field_type_output() { ?>
		<style>
			.postyper_longtext {
				width: 100%;
				height: 8em;
			}
		</style>
	<?php }
}