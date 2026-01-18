<?php

namespace WAFlow\Automations;

use WAFlow\Database\ActivitiesRepository;
use WAFlow\Database\ContactsRepository;
use WAFlow\Database\DealsRepository;
use WAFlow\Integrations\WhatsAppCloud;
use WAFlow\Helpers;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Actions {
	public static function execute( $automation_id, $contact, $context, $actions ) {
		$activities = new ActivitiesRepository();
		$contacts_repo = new ContactsRepository();
		$deals_repo = new DealsRepository();
		$payload = array();
		$message = 'Actions executed.';

		foreach ( $actions as $action ) {
			$type = $action['type'] ?? '';
			$value = $action['value'] ?? '';
			$extra = $action['extra'] ?? '';

			switch ( $type ) {
				case 'add_tag':
					if ( $contact ) {
						$tags = array_filter( array_map( 'trim', explode( ',', $contact->tags ?? '' ) ) );
						if ( $value && ! in_array( $value, $tags, true ) ) {
							$tags[] = $value;
							$contacts_repo->update( $contact->id, array( 'tags' => implode( ',', $tags ), 'updated_at' => current_time( 'mysql' ) ) );
							$activities->add( $contact->id, 'tag_added', array( 'tag' => $value ) );
						}
					}
					break;
				case 'assign_user':
					if ( $contact ) {
						$user_id = self::resolve_assignment( $value, $extra );
						if ( $user_id ) {
							$contacts_repo->update( $contact->id, array( 'assigned_user_id' => $user_id, 'updated_at' => current_time( 'mysql' ) ) );
							$activities->add( $contact->id, 'assigned', array( 'user_id' => $user_id ) );
						}
					}
					break;
				case 'add_note':
					if ( $contact ) {
						$activities->add( $contact->id, 'note', array( 'note' => $value ) );
					}
					break;
				case 'create_deal':
					if ( $contact ) {
						$deal_id = $deals_repo->create(
							array(
								'contact_id' => $contact->id,
								'title' => $value ?: __( 'New Deal', 'waflow' ),
								'stage' => $extra ?: 'Lead',
							)
						);
						$activities->add( $contact->id, 'deal_created', array( 'deal_id' => $deal_id ) );
					}
					break;
				case 'move_deal_stage':
					if ( ! empty( $context['deal_id'] ) ) {
						$deals_repo->update_stage( $context['deal_id'], $value );
						if ( $contact ) {
							$activities->add( $contact->id, 'deal_stage', array( 'deal_id' => $context['deal_id'], 'stage' => $value ) );
						}
					}
					break;
				case 'send_message':
					if ( $contact ) {
						$mode = $extra ?: 'click_to_chat';
						$template = Helpers::interpolate_message( $value, array(
							'name' => $contact->name,
							'phone' => $contact->phone,
							'email' => $contact->email,
							'order_id' => $context['payload']['order_id'] ?? '',
							'order_total' => $context['payload']['order_total'] ?? '',
							'product_names' => $context['payload']['product_names'] ?? '',
						) );
						if ( 'cloud_api' === $mode ) {
							$cloud = new WhatsAppCloud();
							$response = $cloud->send_message( $contact->phone, $template );
							if ( $response['success'] ) {
								$activities->add( $contact->id, 'message_sent', $response );
							} else {
								$activities->add( $contact->id, 'message_failed', $response );
								$payload['cloud_error'] = $response;
							}
						} else {
							$link = Helpers::build_whatsapp_link( $contact->phone, $template );
							$activities->add( $contact->id, 'message_sent', array( 'mode' => 'click_to_chat', 'link' => $link ) );
						}
					}
					break;
			}
		}

		return array(
			'status' => empty( $payload['cloud_error'] ) ? 'success' : 'fail',
			'message' => $message,
			'payload' => $payload,
		);
	}

	private static function resolve_assignment( $value, $extra ) {
		if ( 'round_robin' === $value ) {
			$user_ids = array();
			if ( $extra ) {
				$user_ids = array_filter( array_map( 'absint', explode( ',', $extra ) ) );
			}
			if ( empty( $user_ids ) ) {
				$user_ids = array_map( 'absint', get_users( array( 'fields' => 'ID', 'capability' => 'manage_waflow_crm' ) ) );
			}
			if ( empty( $user_ids ) ) {
				return 0;
			}

			$index = (int) get_option( 'waflow_round_robin_index', 0 );
			$user_id = $user_ids[ $index % count( $user_ids ) ];
			update_option( 'waflow_round_robin_index', $index + 1 );
			return $user_id;
		}

		return absint( $value );
	}
}
