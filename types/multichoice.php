<?php

postyper_register_field_type('PostypeMultiChoice');

class PostypeMultiChoice extends PostypeField {
	static $type = 'multi-choice';
	
	function output($post_id) { ?>

		<?php
			$name = "postype[$this->postype_field_id]";
			$value = $this->output_value($post_id);
		?>
	
		<?php if (count($this->options) < 6) { ?>
			
			<?php $count = 0; ?>
			<?php foreach ($this->options as $val => $text) { ?>
				<?php if ($count) echo "<br />"; ?>
				<?php $id = "postyper_{$this->postype_field_id}_".$count++; ?>
				<input
					type="radio"
					name="<?php echo $name; ?>"
					id="<?php echo $id; ?>"
					value="<?php echo esc_attr($val); ?>"
					<?php if ($val == $value) echo 'checked'; ?>
				/>
				<label for="<?php echo $id; ?>"><?php echo $text; ?></label>
			<?php } ?>
			
		<?php } else { ?>
		
			<select name="<?php echo $name; ?>">
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
		
		<?php } ?>

		<?php $this->output_description(); ?>
		
	<?php }
}