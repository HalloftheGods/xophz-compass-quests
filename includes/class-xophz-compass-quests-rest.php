<?php

/**
 * REST API handling for Questbook CRM Contacts
 *
 * @package    Xophz_Compass_Quests
 * @subpackage Xophz_Compass_Quests/includes
 */

class Xophz_Compass_Quests_REST {

	public function register_routes() {
		add_action( 'rest_api_init', function () {
            register_rest_route( 'questbook/v1', '/contacts', array(
                array(
                    'methods'  => WP_REST_Server::READABLE,
                    'callback' => array( $this, 'get_contacts' ),
                    'permission_callback' => array( $this, 'check_permission' ),
                ),
                array(
                    'methods' => WP_REST_Server::CREATABLE,
                    'callback' => array( $this, 'create_contact' ),
                    'permission_callback' => array( $this, 'check_permission' ),
                ),
            ) );

            register_rest_route( 'questbook/v1', '/contacts/(?P<id>\d+)', array(
                array(
                    'methods'  => WP_REST_Server::READABLE,
                    'callback' => array( $this, 'get_contact' ),
                    'permission_callback' => array( $this, 'check_permission' ),
                ),
                array(
                    'methods' => WP_REST_Server::EDITABLE,
                    'callback' => array( $this, 'update_contact' ),
                    'permission_callback' => array( $this, 'check_permission' ),
                ),
                array(
                    'methods' => WP_REST_Server::DELETABLE,
                    'callback' => array( $this, 'delete_contact' ),
                    'permission_callback' => array( $this, 'check_permission' ),
                ),
            ) );

            register_rest_route( 'questbook/v1', '/contacts/(?P<id>\d+)/assets', array(
                array(
                    'methods'  => WP_REST_Server::READABLE,
                    'callback' => array( $this, 'get_contact_assets' ),
                    'permission_callback' => array( $this, 'check_permission' ),
                ),
            ) );

            register_rest_route( 'questbook/v1', '/contacts/(?P<id>\d+)/entries', array(
                array(
                    'methods'  => WP_REST_Server::READABLE,
                    'callback' => array( $this, 'get_contact_entries' ),
                    'permission_callback' => array( $this, 'check_permission' ),
                ),
            ) );

            register_rest_route( 'questbook/v1', '/contacts/(?P<id>\d+)/unverified', array(
                array(
                    'methods'  => WP_REST_Server::READABLE,
                    'callback' => array( $this, 'get_unverified_entries' ),
                    'permission_callback' => array( $this, 'check_permission' ),
                ),
            ) );

            register_rest_route( 'questbook/v1', '/contacts/(?P<id>\d+)/claim', array(
                array(
                    'methods'  => WP_REST_Server::CREATABLE,
                    'callback' => array( $this, 'claim_entry' ),
                    'permission_callback' => array( $this, 'check_permission' ),
                ),
            ) );

            register_rest_route( 'questbook/v1', '/contacts/(?P<id>\d+)/logs', array(
                array(
                    'methods'  => WP_REST_Server::READABLE,
                    'callback' => array( $this, 'get_contact_logs' ),
                    'permission_callback' => array( $this, 'check_permission' ),
                ),
                array(
                    'methods' => WP_REST_Server::CREATABLE,
                    'callback' => array( $this, 'create_contact_log' ),
                    'permission_callback' => array( $this, 'check_permission' ),
                )
            ) );

            // Webhooks
            register_rest_route( 'questbook/v1', '/webhooks/twilio', array(
                array(
                    'methods'  => WP_REST_Server::CREATABLE,
                    'callback' => array( $this, 'handle_twilio_webhook' ),
                    'permission_callback' => '__return_true', 
                ),
            ) );

            register_rest_route( 'questbook/v1', '/webhooks/email', array(
                array(
                    'methods'  => WP_REST_Server::CREATABLE,
                    'callback' => array( $this, 'handle_email_webhook' ),
                    'permission_callback' => '__return_true',
                ),
            ) );

            register_rest_route( 'questbook/v1', '/templates', array(
                array(
                    'methods'  => WP_REST_Server::READABLE,
                    'callback' => array( $this, 'get_templates' ),
                    'permission_callback' => array( $this, 'check_permission' ),
                ),
            ) );

            register_rest_route( 'questbook/v1', '/settings', array(
                array(
                    'methods'  => WP_REST_Server::READABLE,
                    'callback' => array( $this, 'get_settings' ),
                    'permission_callback' => array( $this, 'check_permission' ),
                ),
                array(
                    'methods'  => WP_REST_Server::EDITABLE,
                    'callback' => array( $this, 'update_settings' ),
                    'permission_callback' => array( $this, 'check_permission' ),
                ),
            ) );

            register_rest_route( 'questbook/v1', '/logs/(?P<log_id>\d+)/promote', array(
                array(
                    'methods'  => WP_REST_Server::CREATABLE,
                    'callback' => array( $this, 'promote_log_to_quest' ),
                    'permission_callback' => array( $this, 'check_permission' ),
                ),
            ) );

            register_rest_route( 'questbook/v1', '/inbox', array(
                array(
                    'methods'  => WP_REST_Server::READABLE,
                    'callback' => array( $this, 'get_global_inbox' ),
                    'permission_callback' => array( $this, 'check_permission' ),
                ),
            ) );

            register_rest_route( 'questbook/v1', '/contacts/(?P<id>\d+)/read', array(
                array(
                    'methods'  => WP_REST_Server::EDITABLE,
                    'callback' => array( $this, 'mark_contact_logs_read' ),
                    'permission_callback' => array( $this, 'check_permission' ),
                ),
            ) );
		});
	}

    public function check_permission() {
        // For now, require manage_options. Adjust as needed.
        return current_user_can( 'manage_options' );
    }

    public function get_contacts( WP_REST_Request $request ) {
        // Map to questbook_contact CPT and merge with connected wp_users data
        $args = array(
            'post_type'      => 'questbook_contact',
            'posts_per_page' => -1,
        );

        $contacts = get_posts( $args );
        $formatted_contacts = array();

        foreach ( $contacts as $contact ) {
            $formatted_contacts[] = $this->format_contact( $contact );
        }

        return rest_ensure_response( $formatted_contacts );
    }

    public function get_contact( WP_REST_Request $request ) {
        $id = $request->get_param( 'id' );
        $contact = get_post( $id );

        if ( ! $contact || $contact->post_type !== 'questbook_contact' ) {
            return new WP_Error( 'no_contact', 'Invalid contact', array( 'status' => 404 ) );
        }

        return rest_ensure_response( $this->format_contact( $contact ) );
    }

    public function create_contact( WP_REST_Request $request ) {
        $params = $request->get_json_params();
        
        $post_data = array(
            'post_title'   => sanitize_text_field( $params['name'] ),
            'post_type'    => 'questbook_contact',
            'post_status'  => 'publish',
        );

        $post_id = wp_insert_post( $post_data );

        if ( is_wp_error( $post_id ) ) {
            return $post_id;
        }

        // Handle Meta
        $this->update_contact_meta( $post_id, $params );

        return $this->get_contact( new WP_REST_Request( 'GET', '/questbook/v1/contacts/' . $post_id ) );
    }

    public function update_contact( WP_REST_Request $request ) {
        $id = $request->get_param( 'id' );
        $params = $request->get_json_params();

        $post_data = array(
            'ID'           => $id,
            'post_title'   => sanitize_text_field( $params['name'] ),
        );

        $post_id = wp_update_post( $post_data );

        if ( is_wp_error( $post_id ) ) {
            return $post_id;
        }

        $this->update_contact_meta( $post_id, $params );

        return $this->get_contact( new WP_REST_Request( 'GET', '/questbook/v1/contacts/' . $post_id ) );
    }

    public function delete_contact( WP_REST_Request $request ) {
        $id = $request->get_param( 'id' );
        $result = wp_delete_post( $id, true );

        if ( ! $result ) {
            return new WP_Error( 'cant_delete', 'Could not delete contact', array( 'status' => 500 ) );
        }

        return rest_ensure_response( array( 'deleted' => true, 'id' => $id ) );
    }

    public function get_contact_assets( WP_REST_Request $request ) {
        $id = $request->get_param( 'id' );
        $user_id = get_post_meta( $id, '_qb_user_id', true );

        $registered_cpts = get_option( 'xophz_compass_registered_cpts', array() );
        $cpt_slugs = wp_list_pluck( $registered_cpts, 'slug' );

        if ( empty( $cpt_slugs ) ) {
            return rest_ensure_response( array() );
        }

        $posts_by_author = array();
        if ( $user_id ) {
            $posts_by_author = get_posts( array(
                'post_type'      => $cpt_slugs,
                'author'         => $user_id,
                'posts_per_page' => -1,
            ) );
        }

        $posts_by_meta = get_posts( array(
            'post_type'      => $cpt_slugs,
            'meta_key'       => '_qb_contact_id',
            'meta_value'     => $id,
            'posts_per_page' => -1,
        ) );

        $merged = array();
        foreach ( $posts_by_author as $p ) {
            $merged[ $p->ID ] = $p;
        }
        foreach ( $posts_by_meta as $p ) {
            $merged[ $p->ID ] = $p;
        }

        // Map slug to icon and label for the UI
        $cpt_map = array();
        foreach ( $registered_cpts as $schema ) {
            $cpt_map[ $schema['slug'] ] = array(
                'icon'       => isset( $schema['icon'] ) ? $schema['icon'] : 'dashicons-admin-post',
                'type_label' => isset( $schema['singular_label'] ) ? $schema['singular_label'] : $schema['slug']
            );
        }

        $formatted_assets = array();
        foreach ( $merged as $p ) {
            $schema_data = isset( $cpt_map[ $p->post_type ] ) ? $cpt_map[ $p->post_type ] : array( 'icon' => 'dashicons-admin-post', 'type_label' => $p->post_type );
            $formatted_assets[] = array(
                'id'         => $p->ID,
                'title'      => $p->post_title,
                'type'       => $p->post_type,
                'date'       => $p->post_date,
                'icon'       => str_replace( 'dashicons-', 'fas fa-', $schema_data['icon'] ), // Convert dashicons prefix loosely for UI
                'type_label' => $schema_data['type_label']
            );
        }

        return rest_ensure_response( array_values( $formatted_assets ) );
    }

    private function format_contact( $contact ) {
        $user_id = get_post_meta( $contact->ID, '_qb_user_id', true );
        $email = get_post_meta( $contact->ID, '_qb_raw_email', true );
        $name = $contact->post_title;

        if ( $user_id ) {
            $user = get_userdata( $user_id );
            if ( $user ) {
                $email = $user->user_email;
                $name = $user->display_name;
            }
        }

        return array(
            'id'          => $contact->ID,
            'user_id'     => $user_id,
            'name'        => $name,
            'email'       => $email,
            'phone'       => get_post_meta( $contact->ID, '_qb_phone', true ),
            'lead_status' => get_post_meta( $contact->ID, '_qb_lead_status', true ),
            'source'      => get_post_meta( $contact->ID, '_qb_source', true ),
            'created_at'  => $contact->post_date,
        );
    }

    private function update_contact_meta( $post_id, $params ) {
        if ( isset( $params['user_id'] ) ) {
            update_post_meta( $post_id, '_qb_user_id', absint( $params['user_id'] ) );
        }
        if ( isset( $params['email'] ) ) {
            update_post_meta( $post_id, '_qb_raw_email', sanitize_email( $params['email'] ) );
        }
        if ( isset( $params['phone'] ) ) {
            update_post_meta( $post_id, '_qb_phone', sanitize_text_field( $params['phone'] ) );
        }
        if ( isset( $params['lead_status'] ) ) {
            update_post_meta( $post_id, '_qb_lead_status', sanitize_text_field( $params['lead_status'] ) );
        }
        if ( isset( $params['source'] ) ) {
            update_post_meta( $post_id, '_qb_source', sanitize_text_field( $params['source'] ) );
        }
    }

    public function get_contact_entries( WP_REST_Request $request ) {
        $id = $request->get_param( 'id' );
        $entry_ids = get_post_meta( $id, '_qb_forminator_entry' );

        if ( empty( $entry_ids ) || ! class_exists( 'Forminator_API' ) ) {
            return rest_ensure_response( array() );
        }

        $entries = array();
        foreach ( $entry_ids as $entry_id ) {
            $entry = Forminator_API::get_entry( absint( $entry_id ) );
            if ( ! is_wp_error( $entry ) && $entry ) {
                $entries[] = array(
                    'id'      => $entry->entry_id,
                    'form_id' => $entry->form_id,
                    'date'    => $entry->date_created ?? '',
                    'meta'    => $entry->meta_data ?? array(),
                );
            }
        }

        return rest_ensure_response( $entries );
    }

    public function get_unverified_entries( WP_REST_Request $request ) {
        $id = $request->get_param( 'id' );
        $entry_ids = get_post_meta( $id, '_qb_unverified_entry' );

        if ( empty( $entry_ids ) || ! class_exists( 'Forminator_API' ) ) {
            return rest_ensure_response( array() );
        }

        $entries = array();
        foreach ( $entry_ids as $entry_id ) {
            $entry = Forminator_API::get_entry( absint( $entry_id ) );
            if ( ! is_wp_error( $entry ) && $entry ) {
                $entries[] = array(
                    'id'      => $entry->entry_id,
                    'form_id' => $entry->form_id,
                    'date'    => $entry->date_created ?? '',
                    'meta'    => $entry->meta_data ?? array(),
                );
            }
        }

        return rest_ensure_response( $entries );
    }

    public function claim_entry( WP_REST_Request $request ) {
        $id = $request->get_param( 'id' );
        $params = $request->get_json_params();
        $entry_id = isset( $params['entry_id'] ) ? absint( $params['entry_id'] ) : 0;
        $action = isset( $params['action'] ) ? sanitize_text_field( $params['action'] ) : '';

        if ( ! $entry_id || ! in_array( $action, array( 'approve', 'reject' ), true ) ) {
            return new WP_Error( 'invalid_params', 'Missing entry_id or invalid action', array( 'status' => 400 ) );
        }

        $unverified_ids = get_post_meta( $id, '_qb_unverified_entry' );
        $is_pending = in_array( (string) $entry_id, array_map( 'strval', $unverified_ids ), true );

        if ( ! $is_pending ) {
            return new WP_Error( 'not_found', 'Entry is not pending verification for this contact', array( 'status' => 404 ) );
        }

        delete_post_meta( $id, '_qb_unverified_entry', $entry_id );

        if ( $action === 'approve' ) {
            add_post_meta( $id, '_qb_forminator_entry', $entry_id );
            return rest_ensure_response( array( 'success' => true, 'message' => 'Entry approved and linked.' ) );
        }

        return rest_ensure_response( array( 'success' => true, 'message' => 'Entry rejected and removed.' ) );
    }

    public function get_contact_logs( WP_REST_Request $request ) {
        $id = $request->get_param( 'id' );
        
        $args = array(
            'post_type'      => 'questbook_log',
            'posts_per_page' => -1,
            'meta_query'     => array(
                array(
                    'key'     => '_qb_contact_id',
                    'value'   => $id,
                    'compare' => '='
                )
            ),
            'orderby'        => 'date',
            'order'          => 'ASC'
        );
        
        $logs = get_posts( $args );
        $formatted_logs = array();
        
        foreach ( $logs as $log ) {
            $formatted_logs[] = array(
                'id'         => $log->ID,
                'title'      => $log->post_title,
                'content'    => get_post_meta( $log->ID, '_qb_message_payload', true ),
                'type'       => get_post_meta( $log->ID, '_qb_log_type', true ),
                'direction'  => get_post_meta( $log->ID, '_qb_direction', true ),
                'internal'   => get_post_meta( $log->ID, '_qb_is_internal', true ) === 'yes',
                'promoted_to'=> get_post_meta( $log->ID, '_qb_promoted_to', true ),
                'is_read'    => get_post_meta( $log->ID, '_qb_is_read', true ) !== 'no',
                'date'       => $log->post_date,
            );
        }
        
        return rest_ensure_response( $formatted_logs );
    }

    public function create_contact_log( WP_REST_Request $request ) {
        $id = $request->get_param( 'id' );
        $params = $request->get_json_params();
        
        $type = isset( $params['type'] ) ? sanitize_text_field( $params['type'] ) : 'note';
        $content = isset( $params['content'] ) ? sanitize_textarea_field( $params['content'] ) : '';
        $internal = isset( $params['internal'] ) && $params['internal'] ? 'yes' : 'no';
        
        if ( empty( $content ) ) {
            return new WP_Error( 'empty_content', 'Log content cannot be empty', array( 'status' => 400 ) );
        }
        
        $post_data = array(
            'post_title'   => ucfirst( $type ) . ' Log',
            'post_type'    => 'questbook_log',
            'post_status'  => 'publish',
        );
        
        $log_id = wp_insert_post( $post_data );
        
        if ( is_wp_error( $log_id ) ) {
            return $log_id;
        }
        
        update_post_meta( $log_id, '_qb_contact_id', $id );
        update_post_meta( $log_id, '_qb_log_type', $type );
        update_post_meta( $log_id, '_qb_direction', 'outbound' ); 
        update_post_meta( $log_id, '_qb_is_internal', $internal );
        update_post_meta( $log_id, '_qb_message_payload', $content );
        update_post_meta( $log_id, '_qb_is_read', 'yes' );
        
        if ( $type === 'sms' && $internal === 'no' ) {
            $to_phone = get_post_meta( $id, '_qb_phone', true );
            $sid = get_option( 'qb_twilio_account_sid' );
            $token = get_option( 'qb_twilio_auth_token' );
            $from_num = get_option( 'qb_twilio_phone_number' );

            if ( $to_phone && $sid && $token && $from_num ) {
                $twilio_url = "https://api.twilio.com/2010-04-01/Accounts/$sid/Messages.json";
                $args = array(
                    'headers' => array(
                        'Authorization' => 'Basic ' . base64_encode( "$sid:$token" )
                    ),
                    'body' => array(
                        'To'   => $to_phone,
                        'From' => $from_num,
                        'Body' => $content
                    )
                );
                $response = wp_remote_post( $twilio_url, $args );
                if ( is_wp_error( $response ) ) {
                    error_log( 'Questbook Twilio Error: ' . $response->get_error_message() );
                }
            } else {
                error_log('Questbook Twilio Error: Missing API keys or Contact Phone Number.');
            }
        } elseif ( $type === 'email' && $internal === 'no' ) {
            $to_email = get_post_meta( $id, '_qb_raw_email', true );
            if ( $to_email ) {
                // Future enhancement: Fetch from_email and subject from settings
                $subject = "Message from Compass Support";
                wp_mail( $to_email, $subject, $content );
            }
        }
        
        return rest_ensure_response( array( 'success' => true, 'log_id' => $log_id ) );
    }

    public function handle_twilio_webhook( WP_REST_Request $request ) {
        $params = $request->get_body_params();
        $from = isset( $params['From'] ) ? sanitize_text_field( $params['From'] ) : '';
        $body = isset( $params['Body'] ) ? sanitize_textarea_field( $params['Body'] ) : '';
        
        if ( empty( $from ) || empty( $body ) ) {
            return new WP_Error( 'missing_data', 'Missing From or Body', array( 'status' => 400 ) );
        }
        
        $contacts = get_posts( array(
            'post_type'  => 'questbook_contact',
            'meta_key'   => '_qb_phone',
            'meta_value' => $from,
            'numberposts'=> 1
        ) );
        
        if ( empty( $contacts ) ) {
            $contact_id = wp_insert_post( array(
                'post_title'  => 'Unknown (' . $from . ')',
                'post_type'   => 'questbook_contact',
                'post_status' => 'publish'
            ) );
            update_post_meta( $contact_id, '_qb_phone', $from );
        } else {
            $contact_id = $contacts[0]->ID;
        }
        
        $log_id = wp_insert_post( array(
            'post_title'   => 'Inbound SMS',
            'post_type'    => 'questbook_log',
            'post_status'  => 'publish',
        ) );
        
        update_post_meta( $log_id, '_qb_contact_id', $contact_id );
        update_post_meta( $log_id, '_qb_log_type', 'sms' );
        update_post_meta( $log_id, '_qb_direction', 'inbound' );
        update_post_meta( $log_id, '_qb_is_internal', 'no' );
        update_post_meta( $log_id, '_qb_message_payload', $body );
        update_post_meta( $log_id, '_qb_is_read', 'no' );
        
        $response = new WP_REST_Response( '<Response></Response>' );
        $response->header( 'Content-Type', 'text/xml' );
        return $response;
    }

    public function handle_email_webhook( WP_REST_Request $request ) {
        $params = $request->get_params();
        $from = isset( $params['from'] ) ? sanitize_text_field( $params['from'] ) : '';
        $text = isset( $params['text'] ) ? sanitize_textarea_field( $params['text'] ) : '';
        
        if ( empty( $from ) ) return new WP_Error( 'missing_data', 'Missing from', array('status' => 400) );
        
        preg_match( '/<([^>]+)>/', $from, $matches );
        $raw_email = isset( $matches[1] ) ? $matches[1] : $from;
        
        $contacts = get_posts( array(
            'post_type'  => 'questbook_contact',
            'meta_key'   => '_qb_raw_email',
            'meta_value' => $raw_email,
            'numberposts'=> 1
        ) );
        
        $contact_id = ! empty( $contacts ) ? $contacts[0]->ID : 0;
        if ( ! $contact_id ) {
             $contact_id = wp_insert_post( array(
                'post_title'  => 'Unknown (' . $raw_email . ')',
                'post_type'   => 'questbook_contact',
                'post_status' => 'publish'
            ) );
            update_post_meta( $contact_id, '_qb_raw_email', $raw_email );
        }
        
        $log_id = wp_insert_post( array(
            'post_title'   => 'Inbound Email',
            'post_type'    => 'questbook_log',
            'post_status'  => 'publish',
        ) );
        
        update_post_meta( $log_id, '_qb_contact_id', $contact_id );
        update_post_meta( $log_id, '_qb_log_type', 'email' );
        update_post_meta( $log_id, '_qb_direction', 'inbound' );
        update_post_meta( $log_id, '_qb_is_internal', 'no' );
        update_post_meta( $log_id, '_qb_message_payload', $text );
        update_post_meta( $log_id, '_qb_is_read', 'no' );
        
        return rest_ensure_response( array('success' => true) );
    }

    public function get_templates( WP_REST_Request $request ) {
        $templates = get_option( 'qb_communication_templates', array(
            array( 'title' => 'Welcome Message', 'content' => 'Hi {{contact.name}}, thanks for reaching out!' ),
            array( 'title' => 'Follow Up', 'content' => 'Just checking in on our previous conversation.' ),
        ) );
        
        return rest_ensure_response( $templates );
    }

    public function get_settings( WP_REST_Request $request ) {
        $settings = array(
            'twilio_account_sid'   => get_option( 'qb_twilio_account_sid', '' ),
            'twilio_auth_token'    => get_option( 'qb_twilio_auth_token', '' ),
            'twilio_phone_number'  => get_option( 'qb_twilio_phone_number', '' ),
            'templates'            => get_option( 'qb_communication_templates', array(
                array( 'title' => 'Welcome Message', 'content' => 'Hi {{contact.name}}, thanks for reaching out!' ),
                array( 'title' => 'Follow Up', 'content' => 'Just checking in on our previous conversation.' ),
            ) )
        );
        return rest_ensure_response( $settings );
    }

    public function update_settings( WP_REST_Request $request ) {
        $params = $request->get_json_params();
        
        if ( isset( $params['twilio_account_sid'] ) ) {
            update_option( 'qb_twilio_account_sid', sanitize_text_field( $params['twilio_account_sid'] ) );
        }
        if ( isset( $params['twilio_auth_token'] ) ) {
            update_option( 'qb_twilio_auth_token', sanitize_text_field( $params['twilio_auth_token'] ) );
        }
        if ( isset( $params['twilio_phone_number'] ) ) {
            update_option( 'qb_twilio_phone_number', sanitize_text_field( $params['twilio_phone_number'] ) );
        }
        if ( isset( $params['templates'] ) && is_array( $params['templates'] ) ) {
            $clean_templates = array();
            foreach( $params['templates'] as $t ) {
                $clean_templates[] = array(
                    'title'   => sanitize_text_field( $t['title'] ),
                    'content' => sanitize_textarea_field( $t['content'] )
                );
            }
            update_option( 'qb_communication_templates', $clean_templates );
        }
        
        return rest_ensure_response( array( 'success' => true ) );
    }

    public function promote_log_to_quest( WP_REST_Request $request ) {
        $log_id = $request->get_param( 'log_id' );
        $log = get_post( $log_id );
        
        if ( ! $log || $log->post_type !== 'questbook_log' ) {
            return new WP_Error( 'not_found', 'Log not found', array( 'status' => 404 ) );
        }
        
        $contact_id = get_post_meta( $log_id, '_qb_contact_id', true );
        $content = get_post_meta( $log_id, '_qb_message_payload', true );
        
        $quest_data = array(
            'post_title'   => 'Follow up: ' . wp_trim_words( $content, 5 ),
            'post_content' => "<strong>Original Message:</strong><br><br>" . nl2br( esc_html( $content ) ),
            'post_type'    => 'questbook_quest',
            'post_status'  => 'publish', 
        );
        
        $quest_id = wp_insert_post( $quest_data );
        
        if ( is_wp_error( $quest_id ) ) {
            return $quest_id;
        }
        
        // Link quest to contact
        if ( $contact_id ) {
            update_post_meta( $quest_id, '_qb_contact_id', $contact_id );
        }
        
        // Mark log as promoted
        update_post_meta( $log_id, '_qb_promoted_to', $quest_id );
        
        return rest_ensure_response( array( 'success' => true, 'quest_id' => $quest_id ) );
    }

    public function get_global_inbox( WP_REST_Request $request ) {
        $args = array(
            'post_type'      => 'questbook_log',
            'posts_per_page' => 100,
            'orderby'        => 'date',
            'order'          => 'DESC',
            // Retrieve recent logs. We can filter further by inbound if needed.
        );

        $query = new WP_Query( $args );
        $logs = array();
        
        foreach ( $query->posts as $log ) {
            $contact_id = get_post_meta( $log->ID, '_qb_contact_id', true );
            $contact_name = 'Unknown';
            if ( $contact_id ) {
                $contact_name = get_the_title( $contact_id );
            }
            
            $logs[] = array(
                'id'         => $log->ID,
                'contact_id' => $contact_id,
                'contact_name'=> $contact_name,
                'title'      => $log->post_title,
                'content'    => get_post_meta( $log->ID, '_qb_message_payload', true ),
                'type'       => get_post_meta( $log->ID, '_qb_log_type', true ),
                'direction'  => get_post_meta( $log->ID, '_qb_direction', true ),
                'internal'   => get_post_meta( $log->ID, '_qb_is_internal', true ) === 'yes',
                'promoted_to'=> get_post_meta( $log->ID, '_qb_promoted_to', true ),
                'is_read'    => get_post_meta( $log->ID, '_qb_is_read', true ) !== 'no',
                'date'       => $log->post_date,
            );
        }
        
        return rest_ensure_response( $logs );
    }

    public function mark_contact_logs_read( WP_REST_Request $request ) {
        $id = $request->get_param( 'id' );
        $args = array(
            'post_type'      => 'questbook_log',
            'posts_per_page' => -1,
            'meta_query'     => array(
                'relation' => 'AND',
                array(
                    'key'   => '_qb_contact_id',
                    'value' => $id,
                ),
                array(
                    'key'   => '_qb_direction',
                    'value' => 'inbound',
                ),
                array(
                    'key'     => '_qb_is_read',
                    'value'   => 'no',
                    'compare' => '='
                )
            )
        );

        $unread_logs = get_posts( $args );
        foreach ( $unread_logs as $log ) {
            update_post_meta( $log->ID, '_qb_is_read', 'yes' );
        }

        return rest_ensure_response( array( 'success' => true, 'marked' => count($unread_logs) ) );
    }
}
