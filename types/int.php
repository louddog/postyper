<?php

Postyper::register_field_type('PostypeInt');

class PostypeInt extends PostypeField {
	var $type = 'int';
	
	function new_value() {
		return intval(parent::new_value());
	}
}