<?php
namespace TrackPulse;

use TrackPulse\Database\Schema;
use TrackPulse\Scheduler\Cron;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Activator {
	public static function activate() {
		Schema::create_tables();
		self::seed_couriers();
		Cron::schedule();
	}

	private static function seed_couriers() {
		if ( get_option( 'trackpulse_couriers', false ) ) {
			return;
		}

		$couriers = array(
			array(
				'id'       => uniqid( 'tp_', true ),
				'name'     => 'DHL',
				'template' => 'https://www.dhl.com/global-en/home/tracking/tracking-express.html?submit=1&tracking-id={tracking_number}',
				'country'  => '',
				'active'   => true,
			),
			array(
				'id'       => uniqid( 'tp_', true ),
				'name'     => 'FedEx',
				'template' => 'https://www.fedex.com/fedextrack/?trknbr={tracking_number}',
				'country'  => '',
				'active'   => true,
			),
			array(
				'id'       => uniqid( 'tp_', true ),
				'name'     => 'UPS',
				'template' => 'https://wwwapps.ups.com/WebTracking/track?track=yes&trackNums={tracking_number}',
				'country'  => '',
				'active'   => true,
			),
			array(
				'id'       => uniqid( 'tp_', true ),
				'name'     => 'USPS',
				'template' => 'https://tools.usps.com/go/TrackConfirmAction?tLabels={tracking_number}',
				'country'  => '',
				'active'   => true,
			),
		);

		update_option( 'trackpulse_couriers', $couriers, false );
	}
}
