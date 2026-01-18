<?php

namespace WAFlow\Automations;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Conditions {
	public static function passes( $conditions, $contact, $context ) {
		if ( empty( $conditions ) ) {
			return true;
		}

		foreach ( $conditions as $condition ) {
			$field = $condition['field'] ?? '';
			$operator = $condition['operator'] ?? 'equals';
			$value = $condition['value'] ?? '';

			$actual = self::resolve_field( $field, $contact, $context );

			switch ( $operator ) {
				case 'contains':
					if ( false === strpos( strtolower( (string) $actual ), strtolower( (string) $value ) ) ) {
						return false;
					}
					break;
				case 'greater_than':
					if ( (float) $actual <= (float) $value ) {
						return false;
					}
					break;
				case 'has_tag':
					$tags = array_filter( array_map( 'trim', explode( ',', $contact->tags ?? '' ) ) );
					if ( ! in_array( $value, $tags, true ) ) {
						return false;
					}
					break;
				case 'not_has_tag':
					$tags = array_filter( array_map( 'trim', explode( ',', $contact->tags ?? '' ) ) );
					if ( in_array( $value, $tags, true ) ) {
						return false;
					}
					break;
				case 'equals':
				default:
					if ( (string) $actual !== (string) $value ) {
						return false;
					}
					break;
			}
		}

		return true;
	}

	private static function resolve_field( $field, $contact, $context ) {
		if ( ! $field ) {
			return '';
		}

		if ( 0 === strpos( $field, 'form_field:' ) ) {
			$key = substr( $field, strlen( 'form_field:' ) );
			return $context['payload']['fields'][ $key ] ?? '';
		}

		switch ( $field ) {
			case 'source':
				return $contact->source ?? '';
			case 'order_total':
				return $context['payload']['order_total'] ?? 0;
			case 'product_id':
				return $context['payload']['product_id'] ?? '';
			case 'country':
				return $context['payload']['country'] ?? '';
			default:
				return '';
		}
	}
}
