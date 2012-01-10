<?php

postyper_register_field_type('PostypeMultiChoice');

class PostypeMultiChoice extends PostypeField {
	static $type = 'multi-choice';
	
	function output($post_id) { ?>

		<?php $value = $this->output_value($post_id); ?>
		
		<select name="postype[<?php echo $this->postype_field_id; ?>]">
			<option value=""></option>
			<?php foreach ($this->options as $val => $text) { ?>
				<option
					value="<?php echo esc_attr($val); ?>"
					<?php if ($val == $value) echo 'selected'; ?>
				>
					<?php echo $text; ?>
				</option>
			<?php } ?>
		</select>

		<?php $this->output_description(); ?>
		
	<?php }
}