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
        if ( ! isset( $entry->entry_id ) ) {
            return;
        }

        $entry_id = $entry->entry_id;
        
        // Fetch mappings from DB
        $mappings = get_option( 'questbook_form_mappings', array() );
        $form_mapping = isset( $mappings[ absint( $module_id ) ] ) ? $mappings[ absint( $module_id ) ] : null;

        // If a mapping exists but is explicitly disabled, do not sync this form
        if ( $form_mapping && empty( $form_mapping['enabled'] ) ) {
            return;
        }

        // Determine field keys (use mapping if available, otherwise fall back to defaults)
        $email_key = $form_mapping && !empty($form_mapping['fields']['_qb_raw_email']) ? $form_mapping['fields']['_qb_raw_email'] : 'email-1';
        $name_key  = $form_mapping && !empty($form_mapping['fields']['first_name']) ? $form_mapping['fields']['first_name'] : 'name-1';
        $phone_key = $form_mapping && !empty($form_mapping['fields']['_qb_phone']) ? $form_mapping['fields']['_qb_phone'] : 'phone-1';
        
        $use_unverified = $form_mapping && isset($form_mapping['useUnverified']) ? $form_mapping['useUnverified'] : true;

        $email = '';
        $name = '';
        $phone = '';

        foreach ( $field_data_array as $field ) {
            if ( $field['name'] === $email_key || (empty($form_mapping) && strpos( $field['name'], 'email-' ) === 0 && empty($email)) ) {
                $email = $field['value'];
            }
            if ( $field['name'] === $name_key || (empty($form_mapping) && strpos( $field['name'], 'name-' ) === 0 && empty($name)) ) {
                $name = $field['value'];
            }
            if ( $field['name'] === $phone_key || (empty($form_mapping) && strpos( $field['name'], 'phone-' ) === 0 && empty($phone)) ) {
                $phone = $field['value'];
            }
        }

        if ( empty( $email ) ) {
            return; // Needs at least an email to be a valid CRM lead
        }

        if ( empty( $name ) ) {
            $name = 'New Lead (' . $email . ')';
        }

        $user_id = get_current_user_id();
        $is_logged_in = ( $user_id > 0 );

        if ( $is_logged_in ) {
            // Find existing contact by user ID
            $existing_contact = $this->find_contact_by_user_id( $user_id );
            
            if ( ! $existing_contact ) {
                // If the logged-in user doesn't have a contact yet, create one
                $existing_contact = wp_insert_post( array(
                    'post_title'  => sanitize_text_field( $name ),
                    'post_type'   => 'questbook_contact',
                    'post_status' => 'publish',
                ) );
                if ( ! is_wp_error( $existing_contact ) ) {
                    update_post_meta( $existing_contact, '_qb_user_id', $user_id );
                    update_post_meta( $existing_contact, '_qb_raw_email', sanitize_email( $email ) );
                }
            }

            if ( $existing_contact && ! is_wp_error( $existing_contact ) ) {
                // Save the entry id directly since they are authenticated
                add_post_meta( $existing_contact, '_qb_forminator_entry', $entry_id );
                
                if ( ! empty( $phone ) ) {
                    update_post_meta( $existing_contact, '_qb_phone', sanitize_text_field( $phone ) );
                }
            }
        } else {
            // Logged out submission
            $existing_contact = $this->find_contact_by_email( $email );

            if ( $existing_contact ) {
                // Email exists (either a WP User or an existing raw lead). 
                if ( $use_unverified ) {
                    // This data is UNVERIFIED. Do not overwrite core fields immediately.
                    // Just log the entry ID as unverified so the claim flow can pick it up.
                    add_post_meta( $existing_contact, '_qb_unverified_entry', $entry_id );
                } else {
                    // Unverified protocol disabled. Treat as verified and update core fields.
                    add_post_meta( $existing_contact, '_qb_forminator_entry', $entry_id );
                    if ( ! empty( $phone ) ) {
                        update_post_meta( $existing_contact, '_qb_phone', sanitize_text_field( $phone ) );
                    }
                }
            } else {
                // Completely new lead
                $post_id = wp_insert_post( array(
                    'post_title'  => sanitize_text_field( $name ),
                    'post_type'   => 'questbook_contact',
                    'post_status' => 'publish',
                ) );

                if ( ! is_wp_error( $post_id ) ) {
                    $existing_contact = $post_id;
                    update_post_meta( $post_id, '_qb_raw_email', sanitize_email( $email ) );
                    if ( ! empty( $phone ) ) {
                        update_post_meta( $post_id, '_qb_phone', sanitize_text_field( $phone ) );
                    }
                    update_post_meta( $post_id, '_qb_lead_status', 'New Lead' );
                    update_post_meta( $post_id, '_qb_source', 'Forminator Form #' . absint( $module_id ) );
                    
                    // Safe to attach since it's a brand new lead
                    add_post_meta( $post_id, '_qb_forminator_entry', $entry_id );
                }
            }
        }

        // Finalize contact ID
        $final_contact_id = $existing_contact && ! is_wp_error( $existing_contact ) ? $existing_contact : false;

        // Inject into Comm-Link
        if ( $final_contact_id ) {
            $form_details = array();
            foreach ( $field_data_array as $field ) {
                $val = is_array($field['value']) ? implode(', ', $field['value']) : $field['value'];
                $form_details[] = $field['name'] . ': ' . $val;
            }
            $payload = "Form Submitted: Forminator Module #" . absint( $module_id ) . "\n\n" . implode("\n", $form_details);

            $log_id = wp_insert_post( array(
                'post_title'   => 'Inbound Webform',
                'post_type'    => 'questbook_log',
                'post_status'  => 'publish',
            ) );
            
            if ( ! is_wp_error( $log_id ) ) {
                update_post_meta( $log_id, '_qb_contact_id', $final_contact_id );
                update_post_meta( $log_id, '_qb_log_type', 'webform' );
                update_post_meta( $log_id, '_qb_direction', 'inbound' );
                update_post_meta( $log_id, '_qb_is_internal', 'no' );
                update_post_meta( $log_id, '_qb_message_payload', $payload );
                update_post_meta( $log_id, '_qb_is_read', 'no' );
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
            $existing_contact = $post_id;
        }

        // Inject Hustle opt-in to Comm-Link
        $final_contact_id = $existing_contact && ! is_wp_error( $existing_contact ) ? $existing_contact : false;
        if ( $final_contact_id ) {
            $payload = "Opt-in Received: Hustle Module #" . absint( $module_id );
            $log_id = wp_insert_post( array(
                'post_title'   => 'Inbound Opt-in',
                'post_type'    => 'questbook_log',
                'post_status'  => 'publish',
            ) );
            
            if ( ! is_wp_error( $log_id ) ) {
                update_post_meta( $log_id, '_qb_contact_id', $final_contact_id );
                update_post_meta( $log_id, '_qb_log_type', 'webform' );
                update_post_meta( $log_id, '_qb_direction', 'inbound' );
                update_post_meta( $log_id, '_qb_is_internal', 'no' );
                update_post_meta( $log_id, '_qb_message_payload', $payload );
                update_post_meta( $log_id, '_qb_is_read', 'no' );
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

    /**
     * Find a CRM contact by user ID
     */
    private function find_contact_by_user_id( $user_id ) {
        $args = array(
            'post_type'  => 'questbook_contact',
            'meta_key'   => '_qb_user_id',
            'meta_value' => $user_id,
            'fields'     => 'ids',
            'numberposts' => 1
        );
        $posts = get_posts( $args );
        
        if ( ! empty( $posts ) ) {
            return $posts[0];
        }
        return false;
    }
}
