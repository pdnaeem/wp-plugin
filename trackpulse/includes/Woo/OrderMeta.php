<?php
namespace TrackPulse\Woo;

use TrackPulse\Helpers;
use TrackPulse\WhatsApp\Sender;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class OrderMeta {
	public function init() {
		add_action( 'trackpulse_tracking_updated', array( $this, 'handle_tracking_updated' ) );
		add_action( 'trackpulse_send_whatsapp_event', array( $this, 'handle_event' ), 10, 3 );
	}

	public function handle_tracking_updated( $order_id ) {
		do_action( 'trackpulse_send_whatsapp_event', $order_id, 'shipped', array( 'source' => 'tracking_meta' ) );
	}

	public function handle_event( $order_id, $event, $args = array() ) {
		$sender = new Sender();
		$sender->queue_event( $order_id, $event, $args );
	}
}
