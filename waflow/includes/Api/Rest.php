<?php

namespace WAFlow\Api;

use WAFlow\Database\ContactsRepository;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Rest {
	public function register() {
		add_action( 'rest_api_init', array( $this, 'routes' ) );
	}

	public function routes() {
		register_rest_route(
			'waflow/v1',
			'/contacts',
			array(
				'methods' => 'GET',
				'callback' => array( $this, 'list_contacts' ),
				'permission_callback' => function () {
					return current_user_can( 'manage_waflow_crm' );
				},
			)
		);
	}

	public function list_contacts() {
		$repo = new ContactsRepository();
		$contacts = $repo->query( array( 'per_page' => 50, 'offset' => 0 ) );
		return rest_ensure_response( $contacts );
	}
}
