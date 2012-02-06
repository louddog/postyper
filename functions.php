<?php

if (!function_exists('deep_trim')) {
	function deep_trim($var) {
		if (is_array($var)) {
			$array = array();
			foreach ($var as $key => $value) {
				$array[$key] = deep_trim($value);
			}
			return $array;
		} else if (is_string($var)) {
			return trim($var);
		} else return $var;
	}
}

if (!function_exists('debug')) {
	function debug($var) {
		echo "<pre style='background-color: #EEE; padding: 5px;'>";
		print_r($var);
		echo "</pre>";
	}
}