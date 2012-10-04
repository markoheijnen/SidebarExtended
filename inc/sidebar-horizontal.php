<?php

class Sidebar_Horizontal {
	public $end_row = '<div class="clearfix"></div>';
	private $sidebars = array();
	private $counter = array();

	public function __construct() {
		add_filter( 'dynamic_sidebar_params', array( &$this, '_sidebar_params' ) );
	}

	public function register( $sidebar_id, $columns ) {
		global $_wp_sidebars_widgets;

		if( ! is_array( $columns ) ) {
			$columns = array( $columns );
		}

		if( ! empty( $wp_registered_sidebars[ $sidebar_id ] ) && ! empty( $_wp_sidebars_widgets[ $sidebar_id ] ) ) {
			$columns = array_map( "absint", $columns );
			$this->sidebars[ $sidebar_id ] = intval( $columns );

			if( count( $columns ) > 1 ) {
				$wp_registered_sidebars[ $sidebar_id ]['columns'] = $columns;
			}

			return true;
		}

		return false;
	}

	function _sidebar_params( $params ) {
		$sidebar_id = $params[0]['id'];

		if( ! isset( $this->sidebars[ $sidebar_id ] ) ) {
			return $params;
		}

		if( ! isset( $this->counter[ $sidebar_id ] ) ) {
			$this->counter[ $sidebar_id ] = 0;
		}

		$this->counter[ $sidebar_id ]++;

		if( is_admin() && $this->counter[ $sidebar_id ] == 1 ) {
			$params[0]['before_widget'] = $this->show_selectbox( $sidebar_id, $params[0]['columns'] ) . $params[0]['before_widget'];
		}
		else if( ! is_admin() ) {
			$amount_columns = $this->get_columns_for_sidebar( $sidebar_id, $this->sidebars[ $sidebar_id ][0] );

			$additional_classes = trim( apply_filters( 'sidebar_horizontal_classes', '', $this->sidebars[ $sidebar_id ], $sidebar_id ) );

			if( $this->counter[ $sidebar_id ] % $this->sidebars[ $sidebar_id ] == 0 ) {
				$additional_classes .= ' last';
				$params[0]['after_widget']  = $params[0]['after_widget'] . $this->end_row;
			}

			$params[0]['before_widget'] = preg_replace( '/class="/', "class=\"" . $additional_classes . " ", $params[0]['before_widget'], 1 );
		}

		return $params;
	}

	function admin_enqueue() {
		$screen = get_current_screen();

		if ( 'widgets' === $screen->base ) {
			wp_enqueue_script( 'sidebar-horizontal', plugins_url( 'js/admin-sidebar.js', dirname( __FILE__ ) ), array( 'jquery' ) );
		}
	}

	function ajax_save_columns() {
		header( "Content-Type: application/json" );

		if( isset( $_POST['sidebar'], $_POST['amount_columns'] ) ) {
			$options = get_option( 'sidebar_columns', array() );
			$options[ esc_attr( $_POST['sidebar'] ) ] = absint( $_POST['amount_columns'] );
			update_option( 'sidebar_columns', $options );

			echo json_encode( array( 'success' => true ) );
		}
		else {
			echo json_encode( array( 'success' => false ) );
		}

		die();
	}




	private function show_selectbox( $sidebar_id, $columns ) {
		$return  = '<div class="sidebar-description" style="margin-top:-10px;">' . __( 'Amount of columns', 'sidebar-extended' ) . ' &nbsp; <select class="sidebar-columns">';

		$default_amount = $columns[0];
		$current_amount = $this->get_columns_for_sidebar( $sidebar_id, $default_amount );
		sort( $columns );

		foreach( $columns as $column ) {
			if( $column == $default_amount ) {
				$column_name = $column . ' (' . __( 'default', 'sidebar-extended' ) . ')';
			}
			else {
				$column_name = $column;
			}

			if( $column == $current_amount ) {
				$return .= '<option value="' . $column . '" selected="selected">' . $column_name . '</option>';
			}
			else {
				$return .= '<option value="' . $column . '">' . $column_name . '</option>';
			}
		}

		$return .= '</select></div>';

		return $return;
	}

	private function get_columns_for_sidebar( $sidebar_id, $default ) {
		$options = get_option( 'sidebar_columns', array() );

		if( isset( $options[ $sidebar_id ] ) ) {
			return absint( $options[ $sidebar_id ] );
		}

		return $default;
	}
}