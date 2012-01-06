<?php

class PostypeTime extends PostypeField {
	var $type = 'time';
	
	function output_value($post_id) {
		if ($value = get_post_meta($post_id, $this->name, true)) {
			$value = date('g:ia', $value);
		}
		return $value;
	}
	
	function new_value() {
		return strtotime($_POST['postype'][$this->postype_field_id]);
	}
}