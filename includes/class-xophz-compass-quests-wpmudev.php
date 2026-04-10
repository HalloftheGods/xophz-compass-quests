<?php

/**
 * WPMU DEV Integrations for Questbook CRM
 *
 * Hooks into Forminator and Hustle to capture leads automatically.
 *
 * @package    Xophz_Compass_Quests
 * @subpackage Xophz_Compass_Quests/includes
 */

class Xophz_Compass_Quests_WPMUDEV {

	public function init_hooks() {
        // Forminator Integration
        add_action( 'forminator_custom_form_submit_before_set_fields', array( $this, 'capture_forminator_submission' ), 10, 3 );
        
        // Hustle Integration
        add_action( 'hustle_after_optin', array( $this, 'capture_hustle_optin' ), 10, 3 );
	}

    /**
     * Capture Forminator submissions into the CRM
     */
    public function capture_forminator_submission( $entry, $module_id, $field_data_array ) {
        // Extract basic info from the standard Forminator field keys
        // These can vary depending on how the user names them, but usually they are `email-1`, `name-1`, `phone-1`
        $email = '';
        $name = '';
        $phone = '';

        foreach ( $field_data_array as $field ) {
            if ( strpos( $field['name'], 'email-' ) === 0 ) {
                $email = $field['value'];
            }
            if ( strpos( $field['name'], 'name-' ) === 0 ) {
                $name = $field['value'];
            }
            if ( strpos( $field['name'], 'phone-' ) === 0 ) {
                $phone = $field['value'];
            }
        }

        if ( empty( $email ) ) {
            return; // Needs at least an email to be a valid CRM lead
        }

        if ( empty( $name ) ) {
            $name = 'New Lead (' . $email . ')';
        }

        // Check if lead already exists based on email
        $existing_contact = $this->find_contact_by_email( $email );

        if ( $existing_contact ) {
            // Update existing contact maybe add a log entry later
            update_post_meta( $existing_contact, '_qb_phone', sanitize_text_field( $phone ) );
        } else {
            // Create a new lead
            $post_id = wp_insert_post( array(
                'post_title'  => sanitize_text_field( $name ),
                'post_type'   => 'questbook_contact',
                'post_status' => 'publish',
            ) );

            if ( ! is_wp_error( $post_id ) ) {
                update_post_meta( $post_id, '_qb_raw_email', sanitize_email( $email ) );
                update_post_meta( $post_id, '_qb_phone', sanitize_text_field( $phone ) );
                update_post_meta( $post_id, '_qb_lead_status', 'New' );
                update_post_meta( $post_id, '_qb_source', 'Forminator Form #' . absint( $module_id ) );
            }
        }
    }

    /**
     * Capture Hustle opt-ins into the CRM
     */
    public function capture_hustle_optin( $module_id, $email, $name ) {
        if ( empty( $email ) ) {
            return;
        }

        if ( empty( $name ) ) {
            $name = 'Hustle Lead (' . $email . ')';
        }

        $existing_contact = $this->find_contact_by_email( $email );

        if ( ! $existing_contact ) {
            $post_id = wp_insert_post( array(
                'post_title'  => sanitize_text_field( $name ),
                'post_type'   => 'questbook_contact',
                'post_status' => 'publish',
            ) );

            if ( ! is_wp_error( $post_id ) ) {
                update_post_meta( $post_id, '_qb_raw_email', sanitize_email( $email ) );
                update_post_meta( $post_id, '_qb_lead_status', 'New Lead' );
                update_post_meta( $post_id, '_qb_source', 'Hustle Opt-in #' . absint( $module_id ) );
            }
        }
    }

    /**
     * Find a CRM contact by raw email meta
     */
    private function find_contact_by_email( $email ) {
        $args = array(
            'post_type'  => 'questbook_contact',
            'meta_key'   => '_qb_raw_email',
            'meta_value' => $email,
            'fields'     => 'ids',
            'numberposts' => 1
        );
        $posts = get_posts( $args );
        
        if ( ! empty( $posts ) ) {
            return $posts[0];
        }

        // Also check if a wp_user exists with this email, and if they are linked
        $user = get_user_by( 'email', $email );
        if ( $user ) {
             $args = array(
                'post_type'  => 'questbook_contact',
                'meta_key'   => '_qb_user_id',
                'meta_value' => $user->ID,
                'fields'     => 'ids',
                'numberposts' => 1
            );
            $posts = get_posts( $args );
            if ( ! empty( $posts ) ) {
                return $posts[0];
            }
        }

        return false;
    }
}
