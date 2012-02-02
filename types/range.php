<?php

postyper_register_field_type('PostypeRange');

class PostypeRange extends PostypeField {
	var $type = 'range';
	
	function admin_enqueue_scripts() {
		wp_enqueue_script('jquery-ui-slider');
	}

	function output($post_id) { ?>
		
		<?php
			$name = "postype[$this->name]";
			$id = "postype_range_$this->name";
			$value = $this->output_value($post_id);
			$min = isset($this->options['min']) ? $this->options['min'] : 0;
			$max = isset($this->options['max']) ? $this->options['max'] : 100;
			$low = isset($value['low']) ? $value['low'] : $min;
			$high = isset($value['high']) ? $value['high'] : $max;
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
			type="hidden"
			name="<?php echo $name; ?>[low]"
			id="<?php echo $id; ?>_low"
			value="<?php echo $low; ?>"
		/>

		<input
			type="hidden"
			name="<?php echo $name; ?>[high]"
			id="<?php echo $id; ?>_high"
			value="<?php echo $high; ?>"
		/>
		
		<div class="postyper_range_values" id="<?php echo $id; ?>_values"><?php echo "$low-$high"; ?></div>

		<div class="postyper_range" rel="<?php echo $id; ?>"></div>

		<?php echo $this->output_description(); ?>
		
	<?php }
	
	function output_value($post_id) {
		return unserialize(get_post_meta($post_id, $this->name, true));
	}
	
	function field_type_output() { ?>
		<style>
			.postyper_range_values {
				width: 50px;
				float: left;
			}
			
			.postyper_range {
				margin: 6px 0 0 60px;
			}
		</style>
		
		<script>
			jQuery(function($) {
				$('.postyper_range').each(function() {
					var rel = $(this).attr('rel');
					var low_input = $('#' + rel + '_low');
					var high_input = $('#' + rel + '_high');
					var min_input = $('#' + rel + '_min');
					var max_input = $('#' + rel + '_max');
					var output = $('#' + rel + '_values');

					$(this).slider({
						range: true,
						min: parseInt(min_input.val(), 10),
						max: parseInt(max_input.val(), 10),
						values: [low_input.val(), high_input.val()],
						slide: function(event, ui) {
							low_input.val(ui.values[0]);
							high_input.val(ui.values[1]);
							output.text(ui.values[0] + '-' + ui.values[1]);
						}
					});
				});
			});
		</script>
	<?php }
	
	function new_value() {
		$value = parent::new_value();
		return serialize(array(
			'low' => $value['low'],
			'high' => $value['high'],
		));
	}
}