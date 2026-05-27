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

            register_rest_route( 'questbook/v1', '/inbox/link', array(
                array(
                    'methods'  => WP_REST_Server::CREATABLE,
                    'callback' => array( $this, 'link_inbox_submission' ),
                    'permission_callback' => array( $this, 'check_permission' ),
                ),
            ) );

            register_rest_route( 'questbook/v1', '/inbox/create-contact', array(
                array(
                    'methods'  => WP_REST_Server::CREATABLE,
                    'callback' => array( $this, 'create_contact_from_submission' ),
                    'permission_callback' => array( $this, 'check_permission' ),
                ),
            ) );

            register_rest_route( 'questbook/v1', '/contacts/(?P<id>\d+)/create-user', array(
                array(
                    'methods'  => WP_REST_Server::CREATABLE,
                    'callback' => array( $this, 'create_wp_user_from_contact' ),
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

            register_rest_route( 'questbook/v1', '/events', array(
                array(
                    'methods'  => WP_REST_Server::READABLE,
                    'callback' => array( $this, 'get_events' ),
                    'permission_callback' => array( $this, 'check_permission' ),
                ),
                array(
                    'methods' => WP_REST_Server::CREATABLE,
                    'callback' => array( $this, 'create_event' ),
                    'permission_callback' => array( $this, 'check_permission' ),
                ),
            ) );

            register_rest_route( 'questbook/v1', '/events/(?P<id>\d+)', array(
                array(
                    'methods' => WP_REST_Server::EDITABLE,
                    'callback' => array( $this, 'update_event' ),
                    'permission_callback' => array( $this, 'check_permission' ),
                ),
                array(
                    'methods' => WP_REST_Server::DELETABLE,
                    'callback' => array( $this, 'delete_event' ),
                    'permission_callback' => array( $this, 'check_permission' ),
                ),
            ) );

            register_rest_route( 'questbook/v1', '/boards', array(
                array(
                    'methods'  => WP_REST_Server::READABLE,
                    'callback' => array( $this, 'get_boards' ),
                    'permission_callback' => array( $this, 'check_permission' ),
                ),
                array(
                    'methods' => WP_REST_Server::CREATABLE,
                    'callback' => array( $this, 'create_board' ),
                    'permission_callback' => array( $this, 'check_permission' ),
                ),
            ) );

            register_rest_route( 'questbook/v1', '/boards/(?P<id>\d+)', array(
                array(
                    'methods' => WP_REST_Server::EDITABLE,
                    'callback' => array( $this, 'update_board' ),
                    'permission_callback' => array( $this, 'check_permission' ),
                ),
                array(
                    'methods' => WP_REST_Server::DELETABLE,
                    'callback' => array( $this, 'delete_board' ),
                    'permission_callback' => array( $this, 'check_permission' ),
                ),
            ) );
            register_rest_route( 'questbook/v1', '/quests', array(
                array(
                    'methods'  => WP_REST_Server::READABLE,
                    'callback' => array( $this, 'get_quests' ),
                    'permission_callback' => array( $this, 'check_permission' ),
                ),
                array(
                    'methods' => WP_REST_Server::CREATABLE,
                    'callback' => array( $this, 'create_quest' ),
                    'permission_callback' => array( $this, 'check_permission' ),
                ),
            ) );

            register_rest_route( 'questbook/v1', '/quests/(?P<id>\d+)', array(
                array(
                    'methods' => WP_REST_Server::EDITABLE,
                    'callback' => array( $this, 'update_quest' ),
                    'permission_callback' => array( $this, 'check_permission' ),
                ),
                array(
                    'methods' => WP_REST_Server::DELETABLE,
                    'callback' => array( $this, 'delete_quest' ),
                    'permission_callback' => array( $this, 'check_permission' ),
                ),
            ) );

            register_rest_route( 'questbook/v1', '/contacts/(?P<id>\d+)/quests', array(
                array(
                    'methods'  => WP_REST_Server::READABLE,
                    'callback' => array( $this, 'get_contact_quests' ),
                    'permission_callback' => array( $this, 'check_permission' ),
                ),
                array(
                    'methods' => WP_REST_Server::CREATABLE,
                    'callback' => array( $this, 'assign_quest_to_contact' ),
                    'permission_callback' => array( $this, 'check_permission' ),
                ),
            ) );

            register_rest_route( 'questbook/v1', '/contacts/(?P<id>\d+)/quests/(?P<quest_id>\d+)', array(
                array(
                    'methods' => WP_REST_Server::EDITABLE,
                    'callback' => array( $this, 'update_contact_quest' ),
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
        $page = $request->get_param( 'page' ) ? absint( $request->get_param( 'page' ) ) : 1;
        $per_page = $request->get_param( 'per_page' ) ? absint( $request->get_param( 'per_page' ) ) : 10;
        $search = $request->get_param( 'search' );
        
        $args = array(
            'post_type'      => 'questbook_contact',
            'paged'          => $page,
            'posts_per_page' => $per_page,
            'post_status'    => 'publish',
        );

        if ( ! empty( $search ) ) {
            $args['s'] = sanitize_text_field( $search );
            // Note: Native 's' searches post_title and post_content.
            // A more robust search might hook into posts_where to search meta,
            // but this covers basic name searches for now.
        }

        $query = new WP_Query( $args );
        $formatted_contacts = array();

        foreach ( $query->posts as $contact ) {
            $formatted_contacts[] = $this->format_contact( $contact );
        }

        $response = new WP_REST_Response( $formatted_contacts );
        $response->header( 'X-WP-Total', $query->found_posts );
        $response->header( 'X-WP-TotalPages', $query->max_num_pages );

        return $response;
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
        );
        
        if ( isset( $params['name'] ) ) {
            $post_data['post_title'] = sanitize_text_field( $params['name'] );
        }

        $post_id = wp_update_post( $post_data );

        if ( is_wp_error( $post_id ) ) {
            return $post_id;
        }

        $this->update_contact_meta( $post_id, $params );

        if ( isset( $params['board_stages'] ) && is_array( $params['board_stages'] ) ) {
            update_post_meta( $post_id, '_qb_board_stages', wp_json_encode( $params['board_stages'] ) );
        }

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

        $board_stages_json = get_post_meta( $contact->ID, '_qb_board_stages', true );
        $board_stages = empty( $board_stages_json ) ? array() : json_decode( $board_stages_json, true );

        return array(
            'id'           => $contact->ID,
            'user_id'      => $user_id,
            'name'         => $name,
            'email'        => $email,
            'phone'        => get_post_meta( $contact->ID, '_qb_phone', true ),
            'lead_status'  => get_post_meta( $contact->ID, '_qb_lead_status', true ),
            'source'       => get_post_meta( $contact->ID, '_qb_source', true ),
            'board_stages' => $board_stages,
            'created_at'   => $contact->post_date,
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

    // --- Boards CRUD --- //
    public function get_boards( WP_REST_Request $request ) {
        $boards = get_posts( array(
            'post_type'      => 'questbook_board',
            'posts_per_page' => -1,
            'post_status'    => 'publish'
        ) );

        $formatted = array();
        foreach ( $boards as $board ) {
            $stages_json = get_post_meta( $board->ID, '_qb_stages', true );
            $stages = empty( $stages_json ) ? array() : json_decode( $stages_json, true );
            $formatted[] = array(
                'id'     => $board->ID,
                'title'  => $board->post_title,
                'stages' => $stages
            );
        }
        return rest_ensure_response( $formatted );
    }

    public function create_board( WP_REST_Request $request ) {
        $params = $request->get_json_params();
        $title = isset( $params['title'] ) ? sanitize_text_field( $params['title'] ) : 'New Board';
        $stages = isset( $params['stages'] ) ? $params['stages'] : array();

        $board_id = wp_insert_post( array(
            'post_title'  => $title,
            'post_type'   => 'questbook_board',
            'post_status' => 'publish'
        ) );

        if ( is_wp_error( $board_id ) ) return $board_id;

        update_post_meta( $board_id, '_qb_stages', wp_json_encode( $stages ) );

        return rest_ensure_response( array(
            'id'     => $board_id,
            'title'  => $title,
            'stages' => $stages
        ) );
    }

    public function update_board( WP_REST_Request $request ) {
        $id = $request->get_param( 'id' );
        $params = $request->get_json_params();

        if ( isset( $params['title'] ) ) {
            wp_update_post( array(
                'ID'         => $id,
                'post_title' => sanitize_text_field( $params['title'] )
            ) );
        }

        if ( isset( $params['stages'] ) ) {
            update_post_meta( $id, '_qb_stages', wp_json_encode( $params['stages'] ) );
        }

        return rest_ensure_response( array( 'success' => true ) );
    }

    public function delete_board( WP_REST_Request $request ) {
        $id = $request->get_param( 'id' );
        wp_delete_post( $id, true );
        return rest_ensure_response( array( 'deleted' => true ) );
    }

    public function get_contact_entries( WP_REST_Request $request ) {
        $id = $request->get_param( 'id' );
        $entry_ids = get_post_meta( $id, '_qb_forminator_entry' );

        if ( empty( $entry_ids ) || ! class_exists( 'Forminator_API' ) ) {
            return rest_ensure_response( array() );
        }

        $entries = array();
        foreach ( $entry_ids as $entry_id ) {
            $entry = class_exists('Forminator_Form_Entry_Model') ? new Forminator_Form_Entry_Model( absint( $entry_id ) ) : null;
            if ( ! is_wp_error( $entry ) && $entry && isset($entry->entry_id) ) {
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
            $entry = class_exists('Forminator_Form_Entry_Model') ? new Forminator_Form_Entry_Model( absint( $entry_id ) ) : null;
            if ( ! is_wp_error( $entry ) && $entry && isset($entry->entry_id) ) {
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

    public function create_wp_user_from_contact( WP_REST_Request $request ) {
        $id = $request->get_param( 'id' );
        $contact = get_post( $id );
        if ( ! $contact || $contact->post_type !== 'questbook_contact' ) {
            return new WP_Error( 'not_found', 'Contact not found', array( 'status' => 404 ) );
        }
        
        $email = get_post_meta( $id, '_qb_raw_email', true );
        $name = $contact->post_title;
        
        if ( empty( $email ) ) {
            return new WP_Error( 'missing_email', 'Contact must have an email to create a WP User', array( 'status' => 400 ) );
        }
        
        if ( email_exists( $email ) ) {
            $user_id = get_user_by( 'email', $email )->ID;
            update_post_meta( $id, '_qb_user_id', $user_id );
            return rest_ensure_response( array( 'success' => true, 'user_id' => $user_id, 'message' => 'User already existed, linked to contact.' ) );
        }
        
        $username = sanitize_user( current( explode( '@', $email ) ), true );
        if ( username_exists( $username ) ) {
            $username = $username . '_' . wp_rand( 1000, 9999 );
        }
        
        $random_password = wp_generate_password( 12, false );
        $user_id = wp_create_user( $username, $random_password, $email );
        
        if ( is_wp_error( $user_id ) ) {
            return $user_id;
        }
        
        $name_parts = explode( ' ', $name, 2 );
        $first_name = $name_parts[0];
        $last_name = isset( $name_parts[1] ) ? $name_parts[1] : '';
        
        wp_update_user( array(
            'ID' => $user_id,
            'first_name' => $first_name,
            'last_name' => $last_name,
            'display_name' => $name
        ) );
        
        update_post_meta( $id, '_qb_user_id', $user_id );
        
        // Send WP welcome email with password reset link
        $key = get_password_reset_key( get_userdata( $user_id ) );
        if ( ! is_wp_error( $key ) ) {
            $reset_link = network_site_url("wp-login.php?action=rp&key=$key&login=" . rawurlencode($username), 'login');
            $subject = 'Welcome! Please set your password';
            $message = "Hi $first_name,\n\n";
            $message .= "Your account has been created. Please click the link below to set your password and access your dashboard:\n\n";
            $message .= $reset_link . "\n\n";
            $message .= "Welcome aboard!";
            wp_mail( $email, $subject, $message );
        }
        
        return rest_ensure_response( array( 'success' => true, 'user_id' => $user_id ) );
    }

    public function link_inbox_submission( WP_REST_Request $request ) {
        $params = $request->get_json_params();
        $entry_id = isset( $params['entry_id'] ) ? absint( $params['entry_id'] ) : 0;
        $contact_id = isset( $params['contact_id'] ) ? absint( $params['contact_id'] ) : 0;
        
        if ( ! $entry_id || ! $contact_id ) {
            return new WP_Error( 'missing_data', 'Missing entry_id or contact_id', array( 'status' => 400 ) );
        }
        
        add_post_meta( $contact_id, '_qb_forminator_entry', $entry_id );
        
        return rest_ensure_response( array( 'success' => true, 'contact_id' => $contact_id ) );
    }

    public function create_contact_from_submission( WP_REST_Request $request ) {
        $params = $request->get_json_params();
        $entry_id = isset( $params['entry_id'] ) ? absint( $params['entry_id'] ) : 0;
        $create_user = isset( $params['create_user'] ) ? (bool) $params['create_user'] : false;
        
        if ( ! $entry_id ) {
            return new WP_Error( 'missing_data', 'Missing entry_id', array( 'status' => 400 ) );
        }
        
        if ( ! class_exists( 'Forminator_Form_Entry_Model' ) ) {
            return new WP_Error( 'no_forminator', 'Forminator API not available', array( 'status' => 500 ) );
        }
        
        $entry = new Forminator_Form_Entry_Model( $entry_id );
        if ( ! $entry || ! isset( $entry->entry_id ) ) {
            return new WP_Error( 'not_found', 'Entry not found', array( 'status' => 404 ) );
        }
        
        $email = '';
        $name = 'New Lead';
        $phone = '';
        
        foreach ( $entry->meta_data as $meta_key => $meta ) {
            $meta_name = isset( $meta['name'] ) ? $meta['name'] : $meta_key;
            $meta_val  = isset( $meta['value'] ) ? $meta['value'] : '';
            if ( is_array( $meta_val ) ) {
                $meta_val = implode( ', ', $meta_val );
            }
            if ( strpos( $meta_name, 'email' ) !== false && empty( $email ) ) $email = $meta_val;
            if ( strpos( $meta_name, 'name' ) !== false && $name === 'New Lead' ) $name = $meta_val;
            if ( strpos( $meta_name, 'phone' ) !== false && empty( $phone ) ) $phone = $meta_val;
        }
        
        $post_id = wp_insert_post( array(
            'post_title'  => sanitize_text_field( $name ),
            'post_type'   => 'questbook_contact',
            'post_status' => 'publish',
        ) );
        
        if ( is_wp_error( $post_id ) ) return $post_id;
        
        if ( ! empty( $email ) ) update_post_meta( $post_id, '_qb_raw_email', sanitize_email( $email ) );
        if ( ! empty( $phone ) ) update_post_meta( $post_id, '_qb_phone', sanitize_text_field( $phone ) );
        update_post_meta( $post_id, '_qb_lead_status', 'New Lead' );
        update_post_meta( $post_id, '_qb_source', 'Forminator Form #' . $entry->form_id );
        add_post_meta( $post_id, '_qb_forminator_entry', $entry_id );
        
        if ( $create_user && ! empty( $email ) ) {
            $req = new WP_REST_Request( 'POST' );
            $req->set_param( 'id', $post_id );
            $this->create_wp_user_from_contact( $req );
        }
        
        return rest_ensure_response( array( 'success' => true, 'contact_id' => $post_id ) );
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

            if ( $to_phone ) {
                if ( class_exists( 'Xophz_Compass_Twilio_API' ) ) {
                    $response = Xophz_Compass_Twilio_API::send_sms( $to_phone, $content );
                    if ( is_wp_error( $response ) ) {
                        error_log( 'Questbook Twilio Error: ' . $response->get_error_message() );
                    }
                } else {
                    error_log('Questbook Twilio Error: Xophz_Compass_Twilio_API not found.');
                }
            } else {
                error_log('Questbook Twilio Error: Missing Contact Phone Number.');
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
            'templates'            => get_option( 'qb_communication_templates', array(
                array( 'title' => 'Welcome Message', 'content' => 'Hi {{contact.name}}, thanks for reaching out!' ),
                array( 'title' => 'Follow Up', 'content' => 'Just checking in on our previous conversation.' ),
            ) ),
            'lead_stages'          => get_option( 'qb_lead_stages', array( 'New', 'Contacted', 'Qualified', 'Won', 'Lost' ) )
        );
        return rest_ensure_response( $settings );
    }

    public function update_settings( WP_REST_Request $request ) {
        $params = $request->get_json_params();
        
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

        if ( isset( $params['lead_stages'] ) && is_array( $params['lead_stages'] ) ) {
            $clean_stages = array_map( 'sanitize_text_field', $params['lead_stages'] );
            update_option( 'qb_lead_stages', $clean_stages );
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

        // Fetch recent Forminator entries natively to ensure the inbox shows them
        // even if the webhook failed or they are older than the CRM plugin.
        if ( class_exists( 'Forminator_API' ) ) {
            global $wpdb;
            $table = $wpdb->prefix . 'frmt_form_entry';
            if ( $wpdb->get_var( "SHOW TABLES LIKE '$table'" ) === $table ) {
                $recent_entries = $wpdb->get_results( "SELECT entry_id, form_id, date_created FROM $table ORDER BY date_created DESC LIMIT 50" );
                
                foreach ( $recent_entries as $entry ) {
                    // Check if this entry is already linked as a questbook log (to avoid duplicates)
                    // We identify duplicates if a log has the exact same payload or if we just want to show raw entries.
                    // Actually, the webhook creates a log. If the log exists, we don't need to duplicate.
                    // However, we'll format it as an inbox item.
                    
                    $form_id = $entry->form_id;
                    $full_entry = class_exists('Forminator_Form_Entry_Model') ? new Forminator_Form_Entry_Model( $entry->entry_id ) : null;
                    
                    $email = 'Unknown';
                    $name = 'Form Submission';
                    $content_parts = array();
                    
                    if ( ! is_wp_error( $full_entry ) && $full_entry && ! empty( $full_entry->meta_data ) ) {
                        foreach ( $full_entry->meta_data as $meta_key => $meta ) {
                            $meta_name = isset( $meta['name'] ) ? $meta['name'] : $meta_key;
                            $meta_val  = isset( $meta['value'] ) ? $meta['value'] : '';

                            if ( is_array( $meta_val ) ) {
                                $meta_val = implode( ', ', $meta_val );
                            }

                            if ( strpos( $meta_name, 'email' ) !== false ) {
                                $email = $meta_val;
                            }
                            if ( strpos( $meta_name, 'name' ) !== false && $name === 'Form Submission' ) {
                                $name = $meta_val;
                            }
                            if ( ! empty( $meta_val ) && is_string( $meta_val ) && strpos( $meta_name, '_' ) !== 0 ) {
                                $content_parts[] = ucfirst( str_replace('-', ' ', $meta_name) ) . ': ' . $meta_val;
                            }
                        }
                    }
                    
                    $contact_id = 0;
                    
                    // First check if explicitly linked via meta
                    $args = array(
                        'post_type'  => 'questbook_contact',
                        'meta_key'   => '_qb_forminator_entry',
                        'meta_value' => $entry->entry_id,
                        'fields'     => 'ids',
                        'numberposts' => 1
                    );
                    $matched_by_entry = get_posts($args);
                    if ( ! empty($matched_by_entry) ) {
                        $contact_id = $matched_by_entry[0];
                    } else if ( $email !== 'Unknown' ) {
                        // First check _qb_raw_email
                        $args = array(
                            'post_type'  => 'questbook_contact',
                            'meta_key'   => '_qb_raw_email',
                            'meta_value' => $email,
                            'fields'     => 'ids',
                            'numberposts' => 1
                        );
                        $matched = get_posts($args);
                        if ( ! empty($matched) ) {
                            $contact_id = $matched[0];
                        } else {
                            // Check if a wp_user exists with this email
                            $user = get_user_by( 'email', $email );
                            if ( $user ) {
                                 $args = array(
                                    'post_type'  => 'questbook_contact',
                                    'meta_key'   => '_qb_user_id',
                                    'meta_value' => $user->ID,
                                    'fields'     => 'ids',
                                    'numberposts' => 1
                                );
                                $matched = get_posts( $args );
                                if ( ! empty($matched) ) {
                                    $contact_id = $matched[0];
                                }
                            }
                        }
                    }
                    
                    $logs[] = array(
                        'id'         => 'forminator_' . $entry->entry_id,
                        'contact_id' => $contact_id, // Linked dynamically or 0
                        'contact_name'=> $name . ( $email !== 'Unknown' ? " ($email)" : '' ),
                        'title'      => 'Forminator Form #' . $form_id,
                        'content'    => implode( "\n", $content_parts ),
                        'type'       => 'webform',
                        'direction'  => 'inbound',
                        'internal'   => false,
                        'promoted_to'=> '',
                        'is_read'    => false, // We could track read state if needed
                        'date'       => $entry->date_created,
                    );
                }
            }
        }
        
        // Sort all by date descending
        usort( $logs, function($a, $b) {
            return strtotime($b['date']) - strtotime($a['date']);
        });
        
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

    // --- Calendar Events ---

    public function get_events( WP_REST_Request $request ) {
        $args = array(
            'post_type'      => 'questbook_event',
            'posts_per_page' => -1,
        );

        $contact_id = $request->get_param( 'contact_id' );
        if ( ! empty( $contact_id ) ) {
            $args['meta_query'] = array(
                array(
                    'key'     => '_qb_contact_id',
                    'value'   => $contact_id,
                    'compare' => '='
                )
            );
        }

        $events = get_posts( $args );
        $formatted = array();

        foreach ( $events as $event ) {
            $formatted[] = array(
                'id'          => (string) $event->ID,
                'title'       => $event->post_title,
                'date'        => get_post_meta( $event->ID, '_qb_event_date', true ),
                'time'        => get_post_meta( $event->ID, '_qb_event_time', true ),
                'description' => get_post_meta( $event->ID, '_qb_event_desc', true ),
                'type'        => get_post_meta( $event->ID, '_qb_event_type', true ),
                'contact_id'  => get_post_meta( $event->ID, '_qb_contact_id', true ),
            );
        }

        return rest_ensure_response( $formatted );
    }

    public function create_event( WP_REST_Request $request ) {
        $params = $request->get_json_params();

        $post_data = array(
            'post_title'  => sanitize_text_field( $params['title'] ),
            'post_type'   => 'questbook_event',
            'post_status' => 'publish',
        );

        $post_id = wp_insert_post( $post_data );

        if ( is_wp_error( $post_id ) ) {
            return $post_id;
        }

        $this->update_event_meta( $post_id, $params );

        return rest_ensure_response( array( 'success' => true, 'id' => (string) $post_id ) );
    }

    public function update_event( WP_REST_Request $request ) {
        $id = $request->get_param( 'id' );
        $params = $request->get_json_params();

        if ( isset( $params['title'] ) ) {
            wp_update_post( array(
                'ID'         => $id,
                'post_title' => sanitize_text_field( $params['title'] ),
            ) );
        }

        $this->update_event_meta( $id, $params );

        return rest_ensure_response( array( 'success' => true ) );
    }

    public function delete_event( WP_REST_Request $request ) {
        $id = $request->get_param( 'id' );
        wp_delete_post( $id, true );
        return rest_ensure_response( array( 'deleted' => true ) );
    }

    private function update_event_meta( $post_id, $params ) {
        if ( isset( $params['date'] ) ) {
            update_post_meta( $post_id, '_qb_event_date', sanitize_text_field( $params['date'] ) );
        }
        if ( isset( $params['time'] ) ) {
            update_post_meta( $post_id, '_qb_event_time', sanitize_text_field( $params['time'] ) );
        }
        if ( isset( $params['description'] ) ) {
            update_post_meta( $post_id, '_qb_event_desc', sanitize_textarea_field( $params['description'] ) );
        }
        if ( isset( $params['type'] ) ) {
            update_post_meta( $post_id, '_qb_event_type', sanitize_text_field( $params['type'] ) );
        }
        if ( isset( $params['contact_id'] ) ) {
            update_post_meta( $post_id, '_qb_contact_id', absint( $params['contact_id'] ) );
        }
    }

    // --- Quests CRUD --- //
    public function get_quests( WP_REST_Request $request ) {
        $registered_cpts = get_option( 'xophz_compass_registered_cpts', array() );
        $quest_cpts = array( 'questbook_quest' ); 
        
        $cpt_map = array(); 
        foreach ( $registered_cpts as $schema ) {
            if ( ! empty( $schema['is_quest_type'] ) ) {
                $quest_cpts[] = $schema['slug'];
                $cpt_map[ $schema['slug'] ] = array(
                    'icon'       => isset( $schema['icon'] ) ? $schema['icon'] : 'dashicons-admin-post',
                    'type_label' => isset( $schema['singular_label'] ) ? $schema['singular_label'] : $schema['slug']
                );
            }
        }

        $quests = get_posts( array(
            'post_type'      => $quest_cpts,
            'posts_per_page' => -1,
            'post_status'    => 'publish'
        ) );

        $formatted = array();
        foreach ( $quests as $quest ) {
            $tasks_json = get_post_meta( $quest->ID, '_qb_quest_tasks', true );
            $tasks = empty( $tasks_json ) ? array() : json_decode( $tasks_json, true );
            
            $schema_data = isset( $cpt_map[ $quest->post_type ] ) ? $cpt_map[ $quest->post_type ] : array( 'icon' => 'dashicons-location-alt', 'type_label' => 'Quest' );
            
            $formatted[] = array(
                'id'          => $quest->ID,
                'title'       => $quest->post_title,
                'description' => $quest->post_content,
                'type'        => $quest->post_type,
                'type_label'  => $schema_data['type_label'],
                'icon'        => str_replace( 'dashicons-', 'fas fa-', $schema_data['icon'] ),
                'tasks'       => $tasks,
                'rewards'     => array(
                    'xp' => absint( get_post_meta( $quest->ID, '_qb_reward_xp', true ) ),
                    'ap' => absint( get_post_meta( $quest->ID, '_qb_reward_ap', true ) ),
                    'gp' => absint( get_post_meta( $quest->ID, '_qb_reward_gp', true ) ),
                )
            );
        }
        return rest_ensure_response( $formatted );
    }

    public function create_quest( WP_REST_Request $request ) {
        $params = $request->get_json_params();
        $title = isset( $params['title'] ) ? sanitize_text_field( $params['title'] ) : 'New Quest';
        $desc = isset( $params['description'] ) ? wp_kses_post( $params['description'] ) : '';
        $tasks = isset( $params['tasks'] ) && is_array( $params['tasks'] ) ? $params['tasks'] : array();
        $post_type = isset( $params['post_type'] ) ? sanitize_text_field( $params['post_type'] ) : 'questbook_quest';

        $registered_cpts = get_option( 'xophz_compass_registered_cpts', array() );
        $valid_cpts = array( 'questbook_quest' );
        foreach ( $registered_cpts as $schema ) {
            if ( ! empty( $schema['is_quest_type'] ) ) {
                $valid_cpts[] = $schema['slug'];
            }
        }
        
        if ( ! in_array( $post_type, $valid_cpts, true ) ) {
            return new WP_Error( 'invalid_type', 'Invalid Quest Type', array('status'=>400) );
        }

        $quest_id = wp_insert_post( array(
            'post_title'   => $title,
            'post_content' => $desc,
            'post_type'    => $post_type,
            'post_status'  => 'publish'
        ) );

        if ( is_wp_error( $quest_id ) ) return $quest_id;

        update_post_meta( $quest_id, '_qb_quest_tasks', wp_json_encode( $tasks ) );
        if ( isset( $params['rewards'] ) ) {
            update_post_meta( $quest_id, '_qb_reward_xp', absint( $params['rewards']['xp'] ?? 0 ) );
            update_post_meta( $quest_id, '_qb_reward_ap', absint( $params['rewards']['ap'] ?? 0 ) );
            update_post_meta( $quest_id, '_qb_reward_gp', absint( $params['rewards']['gp'] ?? 0 ) );
        }

        return rest_ensure_response( array( 'id' => $quest_id ) );
    }

    public function update_quest( WP_REST_Request $request ) {
        $id = $request->get_param( 'id' );
        $params = $request->get_json_params();

        $post_data = array( 'ID' => $id );
        if ( isset( $params['title'] ) ) $post_data['post_title'] = sanitize_text_field( $params['title'] );
        if ( isset( $params['description'] ) ) $post_data['post_content'] = wp_kses_post( $params['description'] );
        
        if ( count( $post_data ) > 1 ) {
            wp_update_post( $post_data );
        }

        if ( isset( $params['tasks'] ) && is_array( $params['tasks'] ) ) {
            update_post_meta( $id, '_qb_quest_tasks', wp_json_encode( $params['tasks'] ) );
        }
        if ( isset( $params['rewards'] ) ) {
            update_post_meta( $id, '_qb_reward_xp', absint( $params['rewards']['xp'] ?? 0 ) );
            update_post_meta( $id, '_qb_reward_ap', absint( $params['rewards']['ap'] ?? 0 ) );
            update_post_meta( $id, '_qb_reward_gp', absint( $params['rewards']['gp'] ?? 0 ) );
        }

        return rest_ensure_response( array( 'success' => true ) );
    }

    public function delete_quest( WP_REST_Request $request ) {
        $id = $request->get_param( 'id' );
        wp_delete_post( $id, true );
        return rest_ensure_response( array( 'deleted' => true ) );
    }

    // --- Contact Quests (Active Quests) --- //
    public function get_contact_quests( WP_REST_Request $request ) {
        $contact_id = $request->get_param( 'id' );
        $active_quests_json = get_post_meta( $contact_id, '_qb_active_quests', true );
        $active_quests = empty( $active_quests_json ) ? array() : json_decode( $active_quests_json, true );
        
        $registered_cpts = get_option( 'xophz_compass_registered_cpts', array() );
        $cpt_map = array(); 
        foreach ( $registered_cpts as $schema ) {
            if ( ! empty( $schema['is_quest_type'] ) ) {
                $cpt_map[ $schema['slug'] ] = array(
                    'icon'       => isset( $schema['icon'] ) ? $schema['icon'] : 'dashicons-admin-post',
                    'type_label' => isset( $schema['singular_label'] ) ? $schema['singular_label'] : $schema['slug']
                );
            }
        }

        foreach ( $active_quests as &$aq ) {
            $quest_post = get_post( $aq['quest_id'] );
            if ( $quest_post ) {
                $aq['type'] = $quest_post->post_type;
                $schema_data = isset( $cpt_map[ $quest_post->post_type ] ) ? $cpt_map[ $quest_post->post_type ] : array( 'icon' => 'dashicons-location-alt', 'type_label' => 'Quest' );
                $aq['icon'] = str_replace( 'dashicons-', 'fas fa-', $schema_data['icon'] );
                $aq['type_label'] = $schema_data['type_label'];
            } else {
                $aq['type'] = 'unknown';
                $aq['icon'] = 'fas fa-question';
                $aq['type_label'] = 'Unknown';
            }
        }
        
        return rest_ensure_response( $active_quests );
    }

    public function assign_quest_to_contact( WP_REST_Request $request ) {
        $contact_id = $request->get_param( 'id' );
        $params = $request->get_json_params();
        $quest_id = isset( $params['quest_id'] ) ? absint( $params['quest_id'] ) : 0;

        if ( ! $quest_id ) return new WP_Error( 'invalid_quest', 'Quest ID missing', array('status'=>400) );

        $quest = get_post( $quest_id );
        
        $registered_cpts = get_option( 'xophz_compass_registered_cpts', array() );
        $valid_cpts = array( 'questbook_quest' );
        foreach ( $registered_cpts as $schema ) {
            if ( ! empty( $schema['is_quest_type'] ) ) {
                $valid_cpts[] = $schema['slug'];
            }
        }

        if ( ! $quest || ! in_array( $quest->post_type, $valid_cpts, true ) ) {
            return new WP_Error( 'invalid_quest', 'Quest not found', array('status'=>404) );
        }

        $active_quests_json = get_post_meta( $contact_id, '_qb_active_quests', true );
        $active_quests = empty( $active_quests_json ) ? array() : json_decode( $active_quests_json, true );

        // Avoid duplicates
        foreach( $active_quests as $aq ) {
            if ( $aq['quest_id'] == $quest_id && empty( $aq['completed'] ) ) {
                return new WP_Error( 'already_assigned', 'Quest already assigned and active', array('status'=>400) );
            }
        }

        // Initialize state
        $tasks_json = get_post_meta( $quest_id, '_qb_quest_tasks', true );
        $tasks = empty( $tasks_json ) ? array() : json_decode( $tasks_json, true );
        
        $state_tasks = array();
        foreach( $tasks as $task ) {
            $state_tasks[] = array(
                'id' => isset($task['id']) ? $task['id'] : uniqid(),
                'title' => isset($task['title']) ? $task['title'] : 'Task',
                'completed' => false
            );
        }

        $new_assignment = array(
            'quest_id' => $quest_id,
            'title' => $quest->post_title,
            'assigned_at' => current_time('mysql'),
            'completed' => false,
            'tasks' => $state_tasks
        );

        $active_quests[] = $new_assignment;
        update_post_meta( $contact_id, '_qb_active_quests', wp_json_encode( $active_quests ) );

        return rest_ensure_response( $new_assignment );
    }

    public function update_contact_quest( WP_REST_Request $request ) {
        $contact_id = $request->get_param( 'id' );
        $quest_id = $request->get_param( 'quest_id' );
        $params = $request->get_json_params();

        $active_quests_json = get_post_meta( $contact_id, '_qb_active_quests', true );
        $active_quests = empty( $active_quests_json ) ? array() : json_decode( $active_quests_json, true );

        $found_index = -1;
        // Find the most recent active one if multiple
        for ( $i = count($active_quests) - 1; $i >= 0; $i-- ) {
            if ( $active_quests[$i]['quest_id'] == $quest_id ) {
                $found_index = $i;
                if ( empty( $active_quests[$i]['completed'] ) ) break;
            }
        }

        if ( $found_index === -1 ) {
            return new WP_Error( 'not_assigned', 'Quest not assigned to contact', array('status'=>404) );
        }

        if ( isset( $params['tasks'] ) ) {
            $active_quests[$found_index]['tasks'] = $params['tasks'];
            
            // Check if all completed
            $all_completed = true;
            foreach( $params['tasks'] as $t ) {
                if ( empty( $t['completed'] ) ) {
                    $all_completed = false;
                    break;
                }
            }

            if ( $all_completed && empty( $active_quests[$found_index]['completed'] ) ) {
                $active_quests[$found_index]['completed'] = true;
                $active_quests[$found_index]['completed_at'] = current_time('mysql');
                
                // --- XP Integration Trigger ---
                $wp_user_id = get_post_meta( $contact_id, '_qb_user_id', true );
                do_action( 'questbook_quest_completed', $contact_id, $quest_id, $wp_user_id );
            }
        }

        update_post_meta( $contact_id, '_qb_active_quests', wp_json_encode( $active_quests ) );

        return rest_ensure_response( $active_quests[$found_index] );
    }
}
