<?php

function postyper_register($postype) {
	global $postyper; 
	$postyper->register_postype($postype);
}

function postyper_register_field_type($className) {
	global $postyper;
	$postyper->register_field_type($className);
}