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
}
