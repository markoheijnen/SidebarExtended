<?php

include 'inc/sidebar-core.php';
include 'inc/sidebar-horizontal.php';

$GLOBALS['sidebar_core'] = new Sidebar_Core();
$GLOBALS['sidebar_horizontal'] = new sidebar_horizontal();

function sidebar_register_default_widget( $sidebar, $class_name, $settings ) {
	global $sidebar_core;

	return $sidebar_core->register_default_widget( $sidebar, $class_name, $settings );
}

function sidebar_register_horizontal_sidebar( $sidebar_id, $columns ) {
	global $sidebar_horizontal;

	return $sidebar_horizontal->register( $sidebar_id, $columns );
}