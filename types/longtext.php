<?php

postyper_register_field_type('PostypeLongText');

class PostypeLongText extends PostypeField {
	static $type = 'long-text';
	
	function output($post_id) { ?>
		
		<textarea class="postyper_longtext" name="postype[<?php echo $this->postype_field_id; ?>]"><?php
			echo $this->output_value($post_id);
		?></textarea>

		<?php $this->output_description(); ?>
		
	<?php }
	
	static function field_type_output() { ?>
		<style>
			.postyper_longtext {
				width: 100%;
				height: 8em;
			}
		</style>
	<?php }
}