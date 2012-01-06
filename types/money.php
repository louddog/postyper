<?php

class PostypeMoney extends PostypeField {
	var $type = 'money';
	
	function new_value() {
		return floatVal(parent::new_value());
	}
}