<?php

namespace WAFlow;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Autoloader {
	public static function register() {
		spl_autoload_register( array( __CLASS__, 'autoload' ) );
	}

	public static function autoload( $class ) {
		if ( 0 !== strpos( $class, __NAMESPACE__ . '\\' ) ) {
			return;
		}

		$base_dir = WAFLOW_PLUGIN_DIR . 'includes/';
		$relative_class = substr( $class, strlen( __NAMESPACE__ ) + 1 );
		$relative_path = str_replace( '\\', '/', $relative_class );
		$file = $base_dir . $relative_path . '.php';

		if ( file_exists( $file ) ) {
			require $file;
		}
	}
}
