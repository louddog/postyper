<?php

postyper_register_field_type('PostypeInt');

class PostypeInt extends PostypeField {
	static $type = 'int';
	
	function new_value() {
		return intval(parent::new_value());
	}
}