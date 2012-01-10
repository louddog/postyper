<?php

postyper_register_field_type('PostypeSlider');

class PostypeSlider extends PostypeField {
	static $type = 'slider';
	
	function admin_enqueue_scripts() {
		wp_enqueue_script('jquery-ui-slider');
	}
	
	function output($post_id) { ?>
		
		<?php
			$name = "postype[$this->postype_field_id]";
			$id = "postype_range_$this->postype_field_id";
			$value = get_post_meta($post_id, $this->name, true);
			$min = isset($this->options['min']) ? $this->options['min'] : 0;
			$max = isset($this->options['max']) ? $this->options['max'] : 100;
		?>
		
		<input
			type="hidden"
			name="<?php echo $name; ?>_min"
			id="<?php echo $id; ?>_min"
			value="<?php echo $min; ?>"
		/>

		<input
			type="hidden"
			name="<?php echo $name; ?>_max"
			id="<?php echo $id; ?>_max"
			value="<?php echo $max; ?>"
		/>

		<input
			type="text"
			name="<?php echo $name; ?>"
			id="<?php echo $id; ?>"
			value="<?php echo empty($value) ? $min : $value; ?>"
		/>

		<div class="postyper_slider" rel="<?php echo $id; ?>"></div>

		<?php echo $this->output_description(); ?>
		
	<?php }
	
	static function field_type_output() { ?>
		<script>
			jQuery(function($) {
				$('.postyper_slider').each(function() {
					var rel = $(this).attr('rel');
					var input = $('#' + rel);

					$(this).slider({
						min: parseInt($('#' + rel + '_min').val(), 10),
						max: parseInt($('#' + rel + '_max').val(), 10),
						value: input.val(),
						slide: function (event, ui) {
							input.val(ui.value);
						}
					});
				});
			});
		</script>
	<?php }
}