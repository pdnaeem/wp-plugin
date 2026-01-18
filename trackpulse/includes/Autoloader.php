<?php
namespace TrackPulse;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Autoloader {
	public static function register() {
		spl_autoload_register( array( __CLASS__, 'autoload' ) );
	}

	public static function autoload( $class ) {
		$prefix = 'TrackPulse\\';
		$base_dir = TRACKPULSE_PLUGIN_DIR . 'includes/';

		if ( 0 !== strpos( $class, $prefix ) ) {
			return;
		}

		$relative_class = substr( $class, strlen( $prefix ) );
		$file = $base_dir . str_replace( '\\', '/', $relative_class ) . '.php';

		if ( file_exists( $file ) ) {
			require $file;
		}
	}
}
