<?php

namespace WAFlow\Automations;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Triggers {
	public static function matches( $trigger, $context ) {
		if ( empty( $trigger['type'] ) ) {
			return false;
		}

		if ( isset( $trigger['form_id'] ) && '' !== $trigger['form_id'] ) {
			if ( empty( $context['form_id'] ) || (string) $context['form_id'] !== (string) $trigger['form_id'] ) {
				return false;
			}
		}

		if ( isset( $trigger['from_status'] ) && '' !== $trigger['from_status'] ) {
			if ( empty( $context['from_status'] ) || $context['from_status'] !== $trigger['from_status'] ) {
				return false;
			}
		}

		if ( isset( $trigger['to_status'] ) && '' !== $trigger['to_status'] ) {
			if ( empty( $context['to_status'] ) || $context['to_status'] !== $trigger['to_status'] ) {
				return false;
			}
		}

		return true;
	}
}
