<?php

Postyper::register_field_type('PostypeDateTime');

class PostypeDateTime extends PostypeField {
	var $type = 'datetime';
	
	function output($post_id) { ?>
		
		<?php
			$name = "postype[$this->name]";
			$value = $this->output_value($post_id);
		?>
		
		<input
			type="text"
			class="postyper_date_time"
			name="<?php echo $name; ?>[date]"
			value="<?php if (is_numeric($value)) echo esc_attr(date('n/j/Y', $value)); ?>"
			placeholder="mm/dd/yyyy"
		/>
		<input
			type="text"
			name="<?php echo $name; ?>[time]"
			value="<?php if (is_numeric($value)) echo esc_attr(date('g:ia', $value)); ?>"
			placeholder="hh:mm am"
		/>
		

		<?php $this->output_description(); ?>
		
	<?php }
		
	function new_value() {
		$value = parent::new_value();
		$date = strtotime($value['date']);
		$time = strtotime($value['time']);

		if (!$date || !$time) return 0;

		$d = getdate($date);
		$t = getdate($time);
		return mktime(
			$t['hours'], $t['minutes'], $t['seconds'],
			$d['mon'], $d['mday'], $d['year']
		);
	}
	
	function field_type_output() { ?>
		<script>
			jQuery(function($) {
				$('.postyper_date_time').datepicker({
					changeMonth: true,
					changeYear: true
				});
			});
		</script>
	<?php }
}