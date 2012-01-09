<?php

class PostypeRange extends PostypeField {
	var $type = 'range';
	
	function output($post_id) { ?>
		
		<?php
			$name = "postype[$this->postype_field_id]";
			$id = "postype_range_$this->postype_field_id";
			$value = $this->output_value($post_id);
			$min = isset($this->options['min']) ? $this->options['min'] : 0;
			$max = isset($this->options['max']) ? $this->options['max'] : 100;
		?>
		
		<input
			type="hidden"
			name="<?php echo $name; ?>[min]"
			id="<?php echo $id; ?>_min"
			value="<?php echo $min; ?>"
		/>

		<input
			type="hidden"
			name="<?php echo $name; ?>[max]"
			id="<?php echo $id; ?>_max"
			value="<?php echo $max; ?>"
		/>

		<input
			type="text"
			name="<?php echo $name; ?>[low]"
			id="<?php echo $id; ?>_low"
			value="<?php echo isset($value['low']) ? $value['low'] : $this->options['min']; ?>"
		/>

		<input
			type="text"
			name="<?php echo $name; ?>[high]"
			id="<?php echo $id; ?>_high"
			value="<?php echo isset($value['high']) ? $value['high'] : $this->options['max']; ?>"
		/>

		<div class="postyper_range" rel="<?php echo $id; ?>"></div>

		<?php echo $this->output_description(); ?>
		
	<?php }
	
	function output_value($post_id) {
		return unserialize(get_post_meta($post_id, $this->name, true));
	}
	
	function new_value() {
		return serialize(array(
			'low' => $_POST['postype'][$this->postype_field_id]['low'],
			'high' => $_POST['postype'][$this->postype_field_id]['high'],
		));
	}
}