<?php

Postyper::register_field_type('PostypeTimeRange');

class PostypeTimeRange extends PostypeField {
	var $type = 'timerange';
	
	function output($post_id) { ?>
		
		<?php
			$name = "postype[$this->name]";
			$id = "postype_range_$this->name";
			$value = $this->output_value($post_id);
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
	
	function output_value($post_id) {
		return unserialize(get_post_meta($post_id, $this->name, true));
	}

	function new_value() {
		$value = parent::new_value();
		return serialize(array(
			'starts' => strtotime($value['starts']),
			'ends' => strtotime($value['ends']),
		));
	}
}