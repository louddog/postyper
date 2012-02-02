<?php

postyper_register_field_type('PostypeDate');

class PostypeDate extends PostypeField {
	var $type = 'date';
	
	function admin_enqueue_scripts() {
		wp_enqueue_script('jquery-ui-datepicker');
	}
	
	function output($post_id) { ?>
		
		<input
			type="text"
			class="postyper_date"
			name="postype[<?php echo $this->name; ?>]"
			value="<?php echo esc_attr($this->output_value($post_id)); ?>"
		/>

		<?php $this->output_description(); ?>
		
	<?php }
	
	
	function output_value($post_id) {
		if ($value = get_post_meta($post_id, $this->name, true)) {
			$value = date('n/j/Y', $value);
		}
		return $value;
	}
	
	function new_value() {
		if ($value = strtotime(parent::new_value())) {
			$d = getdate($value);
			$value = mktime(0, 0, 0, $d['mon'], $d['mday'], $d['year']);
		}
		return $value;
	}
	
	function field_type_output() { ?>
		<script>
			jQuery(function($) {
				$('.postyper_date').datepicker({
					changeMonth: true,
					changeYear: true
				});
			});
		</script>
	<?php }
}