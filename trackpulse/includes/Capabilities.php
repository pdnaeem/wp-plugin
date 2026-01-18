<?php
namespace TrackPulse;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Capabilities {
	public static function can_manage() {
		return current_user_can( 'manage_woocommerce' );
	}
}
