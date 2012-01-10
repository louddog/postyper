<?php

postyper_register_field_type('PostypeTimeRange');

class PostypeTimeRange extends PostypeField {
	static $type = 'time-range';
	
	function output($post_id) { ?>
		
		<?php
			$name = "postype[$this->postype_field_id]";
			$id = "postype_range_$this->postype_field_id";
			$value = $this->output_value($post_id);
			$min = isset($this->options['min']) ? $this->options['min'] : 0;
			$max = isset($this->options['max']) ? $this->options['max'] : 100;
		?>
		
		<input
			type="text"
			name="<?php echo $name; ?>[starts]"
			value="<?php if (isset($value['starts']) && is_numeric($value['starts'])) echo esc_attr(date('g:ia', $value['starts'])); ?>"
			placeholder="hh:mm am"
		/>
		<input
			type="text"
			name="<?php echo $name; ?>[ends]"
			value="<?php if (isset($value['ends']) && is_numeric($value['ends'])) echo esc_attr(date('g:ia', $value['ends'])); ?>"
			placeholder="hh:mm am"
		/>

		<?php echo $this->output_description(); ?>
		
	<?php }
	
	function new_value() {
		return serialize(array(
			'starts' => $_POST['postype'][$this->postype_field_id]['starts'],
			'ends' => $_POST['postype'][$this->postype_field_id]['ends'],
		));
	}
}