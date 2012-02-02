<?php

postyper_register_field_type('PostypeMultiChoice');

class PostypeMultiChoice extends PostypeField {
	var $type = 'multichoice';
	
	function output($post_id) { ?>

		<?php
			$name = "postype[$this->name]";
			$value = $this->output_value($post_id);
		?>
	
		<?php if (count($this->options) < 6) { ?>
			
			<?php $count = 0; ?>
			<?php foreach ($this->options as $text) { ?>
				<?php if ($count) echo "<br />"; ?>
				<?php $id = "postyper_{$this->name}_".$count++; ?>
				<input
					type="radio"
					name="<?php echo $name; ?>"
					id="<?php echo $id; ?>"
					value="<?php echo esc_attr($text); ?>"
					<?php if ($text == $value) echo 'checked'; ?>
				/>
				<label for="<?php echo $id; ?>"><?php echo $text; ?></label>
			<?php } ?>
			
		<?php } else { ?>
			
			<select name="<?php echo $name; ?>">
				<option value=""></option>
				<?php foreach ($this->options as $text) { ?>
					<option
						value="<?php echo esc_attr($text); ?>"
						<?php if ($text == $value) echo 'selected'; ?>
					>
						<?php echo $text; ?>
					</option>
				<?php } ?>
			</select>
		
		<?php } ?>

		<?php $this->output_description(); ?>
		
	<?php }
}