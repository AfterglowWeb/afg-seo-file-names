<?php defined( 'ABSPATH' ) || exit;

function print_r_e($var, $strict = false) {
	echo '<pre>';
	$strict ? var_dump($var) : print_r($var);
	echo '</pre>';
}