<?php

class Sidebar_Core {
	private $custom_settings = array();
	private $widget_counter = array();

	function __construct() {

	}

	public function register_default_widget( $sidebar, $widgets ) {
		$sidebar_widgets = get_option( 'sidebars_widgets', array() );
	}

	public function set_widget( $sidebar, $class_name, $settings ) {
		global $_wp_sidebars_widgets, $wp_widget_factory;

		if( empty( $_wp_sidebars_widgets ) ) 
			wp_get_sidebars_widgets();

		if( empty( $_wp_sidebars_widgets[ $sidebar ] ) ) {
			if( isset( $wp_widget_factory->widgets[ $class_name ] ) ) {
				$class = $wp_widget_factory->widgets[ $class_name ];

				if( ! isset( $this->custom_settings[ $class->option_name ] ) ) { 
					$this->custom_settings[ $class->option_name ] = array();
					add_filter( 'option_' . $class->option_name, array( &$this, '_register_default_widgets_settings' ) );
				}

				$id = 100 + count( $this->custom_settings[ $class->option_name ] );
				$this->custom_settings[ $class->option_name ][ $id ] =  $settings;
				$_wp_sidebars_widgets[ $sidebar ][] = $class->id_base . '-' . $id;

				return true;
			}
		}

		return false;
	}

	function _register_default_widgets_settings( $value ) {
		$filter = substr( current_filter(), 7);

		if( isset( $this->custom_settings[ $filter ] ) ) {
			$value = $value + $this->custom_settings[ $filter ];
		}

		return $value;
	}
}