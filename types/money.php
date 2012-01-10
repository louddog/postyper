<?php

postyper_register_field_type('PostypeMoney');

class PostypeMoney extends PostypeField {
	static $type = 'money';
	
	function new_value() {
		return floatVal(parent::new_value());
	}
}