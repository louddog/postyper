<?php

class PostypeDate extends PostypeField {
	var $type = 'date';
	
	function output_value($post_id) {
		if ($value = get_post_meta($post_id, $this->name, true)) {
			$value = date('n/j/Y', $value);
		}
		return $value;
	}
	
	function new_value() {
		if ($value = strtotime($_POST['postype'][$this->postype_field_id])) {
			$d = getdate($value);
			$value = mktime(0, 0, 0, $d['mon'], $d['mday'], $d['year']);
		}
		return $value;
	}
}