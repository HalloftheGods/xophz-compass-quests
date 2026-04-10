<?php

/**
 * Register all REST API endpoints for Questbook
 *
 * @package    Xophz_Compass_Quests
 * @subpackage Xophz_Compass_Quests/includes
 */

class Xophz_Compass_Quests_API {

	public function register_endpoints() {
		add_action( 'rest_api_init', function () {
            // Contacts
			register_rest_route( 'questbook/v1', '/contacts', array(
				'methods'  => WP_REST_Server::READABLE,
				'callback' => array( $this, 'get_contacts' ),
				'permission_callback' => array( $this, 'check_permissions' )
			) );

			register_rest_route( 'questbook/v1', '/contacts', array(
				'methods'  => WP_REST_Server::CREATABLE,
				'callback' => array( $this, 'create_contact' ),
				'permission_callback' => array( $this, 'check_permissions' )
			) );
            
            // Further endpoints to be implemented: Update Contact, Get Quests, Log Quest...
		} );
	}

	public function check_permissions() {
		return current_user_can( 'manage_options' ); // Adjust as needed
	}

	public function get_contacts( WP_REST_Request $request ) {
		$args = array(
			'post_type'      => 'questbook_contact',
			'posts_per_page' => -1,
		);
		$query = new WP_Query( $args );

		$contacts = array();
		if ( $query->have_posts() ) {
			while ( $query->have_posts() ) {
				$query->the_post();
				$id = get_the_ID();
				
				$qb_user_id = get_post_meta( $id, '_qb_user_id', true );
				
				$name = '';
				$email = '';

				if ( $qb_user_id ) {
					$user_info = get_userdata( $qb_user_id );
					if ( $user_info ) {
						$name = $user_info->display_name;
						$email = $user_info->user_email;
					}
				} else {
					$name = get_post_meta( $id, '_qb_raw_name', true );
					$email = get_post_meta( $id, '_qb_raw_email', true );
				}

				$contacts[] = array(
					'id'            => $id,
					'wp_user_id'    => $qb_user_id,
					'name'          => $name,
					'email'         => $email,
					'status'        => get_post_meta( $id, '_qb_status', true ) ?: 'New',
					'phone'         => get_post_meta( $id, '_qb_phone', true ),
					'assigned_to'   => get_post_meta( $id, '_qb_assigned_to', true ),
                    'date_created'  => get_the_date( 'c' )
				);
			}
			wp_reset_postdata();
		}

		return rest_ensure_response( $contacts );
	}

	public function create_contact( WP_REST_Request $request ) {
		$parameters = $request->get_json_params();
		
		$name = isset( $parameters['name'] ) ? sanitize_text_field( $parameters['name'] ) : '';
		$email = isset( $parameters['email'] ) ? sanitize_email( $parameters['email'] ) : '';
		$status = isset( $parameters['status'] ) ? sanitize_text_field( $parameters['status'] ) : 'New';
        $wp_user_id = isset( $parameters['wp_user_id'] ) ? absint( $parameters['wp_user_id'] ) : 0;

		$post_id = wp_insert_post( array(
			'post_title'    => $name ? $name : 'Unnamed Contact',
			'post_status'   => 'publish',
			'post_type'     => 'questbook_contact',
		) );

		if ( is_wp_error( $post_id ) ) {
			return new WP_Error( 'cant-create', __( 'Cannot create contact.', 'xophz-compass-quests' ), array( 'status' => 500 ) );
		}

        if ( $wp_user_id ) {
            update_post_meta( $post_id, '_qb_user_id', $wp_user_id );
        } else {
            update_post_meta( $post_id, '_qb_raw_name', $name );
		    update_post_meta( $post_id, '_qb_raw_email', $email );
        }
		
		update_post_meta( $post_id, '_qb_status', $status );

		return rest_ensure_response( array( 'id' => $post_id, 'message' => 'Contact created' ) );
	}
}
