<?php

namespace WAFlow;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Capabilities {
	const MANAGE_CRM = 'manage_waflow_crm';

	public static function add_caps() {
		$role = get_role( 'administrator' );
		if ( $role ) {
			$role->add_cap( self::MANAGE_CRM );
		}
	}
}
