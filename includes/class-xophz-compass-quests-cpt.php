<?php

/**
 * Register all Custom Post Types for Questbook
 *
 * @package    Xophz_Compass_Quests
 * @subpackage Xophz_Compass_Quests/includes
 */

class Xophz_Compass_Quests_CPT {

	/**
	 * Register the Contact and Quest CPTs
	 */
	public function register_cpts() {

		// 1. Register Contact CPT
		$contact_labels = array(
			'name'               => _x( 'Contacts', 'post type general name', 'xophz-compass-quests' ),
			'singular_name'      => _x( 'Contact', 'post type singular name', 'xophz-compass-quests' ),
			'menu_name'          => _x( 'Contacts', 'admin menu', 'xophz-compass-quests' ),
			'name_admin_bar'     => _x( 'Contact', 'add new on admin bar', 'xophz-compass-quests' ),
			'add_new'            => _x( 'Add New', 'contact', 'xophz-compass-quests' ),
			'add_new_item'       => __( 'Add New Contact', 'xophz-compass-quests' ),
			'new_item'           => __( 'New Contact', 'xophz-compass-quests' ),
			'edit_item'          => __( 'Edit Contact', 'xophz-compass-quests' ),
			'view_item'          => __( 'View Contact', 'xophz-compass-quests' ),
			'all_items'          => __( 'All Contacts', 'xophz-compass-quests' ),
			'search_items'       => __( 'Search Contacts', 'xophz-compass-quests' ),
			'parent_item_colon'  => __( 'Parent Contacts:', 'xophz-compass-quests' ),
			'not_found'          => __( 'No contacts found.', 'xophz-compass-quests' ),
			'not_found_in_trash' => __( 'No contacts found in Trash.', 'xophz-compass-quests' )
		);

		$contact_args = array(
			'labels'             => $contact_labels,
			'description'        => __( 'Questbook CRM Contacts.', 'xophz-compass-quests' ),
			'public'             => false,
			'publicly_queryable' => false,
			'show_ui'            => false, // We'll build our own UI in Vue
			'show_in_menu'       => false,
			'query_var'          => true,
			'rewrite'            => array( 'slug' => 'questbook-contact' ),
			'capability_type'    => 'post',
			'has_archive'        => false,
			'hierarchical'       => false,
			'menu_position'      => null,
			'supports'           => array( 'title' )
		);

		register_post_type( 'questbook_contact', $contact_args );

		// 1.5 Register Contact Type Taxonomy
		$taxonomy_labels = array(
			'name'              => _x( 'Contact Types', 'taxonomy general name', 'xophz-compass-quests' ),
			'singular_name'     => _x( 'Contact Type', 'taxonomy singular name', 'xophz-compass-quests' ),
			'search_items'      => __( 'Search Contact Types', 'xophz-compass-quests' ),
			'all_items'         => __( 'All Contact Types', 'xophz-compass-quests' ),
			'parent_item'       => __( 'Parent Contact Type', 'xophz-compass-quests' ),
			'parent_item_colon' => __( 'Parent Contact Type:', 'xophz-compass-quests' ),
			'edit_item'         => __( 'Edit Contact Type', 'xophz-compass-quests' ),
			'update_item'       => __( 'Update Contact Type', 'xophz-compass-quests' ),
			'add_new_item'      => __( 'Add New Contact Type', 'xophz-compass-quests' ),
			'new_item_name'     => __( 'New Contact Type Name', 'xophz-compass-quests' ),
			'menu_name'         => __( 'Contact Type', 'xophz-compass-quests' ),
		);

		$taxonomy_args = array(
			'hierarchical'      => true,
			'labels'            => $taxonomy_labels,
			'show_ui'           => false,
			'show_admin_column' => false,
			'query_var'         => true,
			'show_in_rest'      => true,
			'rewrite'           => array( 'slug' => 'qb-contact-type' ),
		);

		register_taxonomy( 'qb_contact_type', array( 'questbook_contact' ), $taxonomy_args );

		// 2. Register Quest CPT
		$quest_labels = array(
			'name'               => _x( 'Questbook', 'post type general name', 'xophz-compass-quests' ),
			'singular_name'      => _x( 'Quest', 'post type singular name', 'xophz-compass-quests' ),
			'menu_name'          => _x( 'Questbook', 'admin menu', 'xophz-compass-quests' ),
			'name_admin_bar'     => _x( 'Questbook', 'add new on admin bar', 'xophz-compass-quests' ),
			'add_new'            => _x( 'Add New', 'quest', 'xophz-compass-quests' ),
			'add_new_item'       => __( 'Add New Quest', 'xophz-compass-quests' ),
			'new_item'           => __( 'New Quest', 'xophz-compass-quests' ),
			'edit_item'          => __( 'Edit Quest', 'xophz-compass-quests' ),
			'view_item'          => __( 'View Quest', 'xophz-compass-quests' ),
			'all_items'          => __( 'All Quests', 'xophz-compass-quests' ),
			'search_items'       => __( 'Search Quests', 'xophz-compass-quests' ),
			'parent_item_colon'  => __( 'Parent Quests:', 'xophz-compass-quests' ),
			'not_found'          => __( 'No quests found.', 'xophz-compass-quests' ),
			'not_found_in_trash' => __( 'No quests found in Trash.', 'xophz-compass-quests' )
		);

		$quest_args = array(
			'labels'             => $quest_labels,
			'description'        => __( 'Questbook Journey Quests.', 'xophz-compass-quests' ),
			'public'             => false,
			'publicly_queryable' => false,
			'show_ui'            => false, // Custom Vue UI
			'show_in_menu'       => false,
			'query_var'          => true,
			'rewrite'            => array( 'slug' => 'questbook-quest' ),
			'capability_type'    => 'post',
			'has_archive'        => false,
			'hierarchical'       => false,
			'menu_position'      => null,
			'supports'           => array( 'title', 'editor' )
		);

		register_post_type( 'questbook_quest', $quest_args );

		// 3. Register Journey Log CPT
		$log_labels = array(
			'name'               => _x( 'Journey Logs', 'post type general name', 'xophz-compass-quests' ),
			'singular_name'      => _x( 'Journey Log', 'post type singular name', 'xophz-compass-quests' )
		);

		$log_args = array(
			'labels'             => $log_labels,
			'description'        => __( 'Questbook Journey Logs.', 'xophz-compass-quests' ),
			'public'             => false,
			'publicly_queryable' => false,
			'show_ui'            => false,
			'show_in_menu'       => false,
			'query_var'          => false,
			'capability_type'    => 'post',
			'has_archive'        => false,
			'hierarchical'       => false,
			'supports'           => array( 'title' )
		);

		register_post_type( 'questbook_log', $log_args );
	}

	/**
	 * Autopilot Workflow Engine: Automate pipeline stages based on logs
	 */
	public function handle_workflow_triggers( $post_id, $post, $update ) {
		// Prevent infinite loops during updates or revisions
		if ( wp_is_post_revision( $post_id ) ) return;
		
		$contact_id = get_post_meta( $post_id, '_qb_contact_id', true );
		$direction = get_post_meta( $post_id, '_qb_direction', true );
		
		// Autopilot Rule: If a fresh inbound message arrives, automatically bump lead status to 'Contacted'
		if ( $contact_id && $direction === 'inbound' && ! $update ) {
			$current_status = get_post_meta( $contact_id, '_qb_lead_status', true );
			
			if ( empty($current_status) || strtolower($current_status) === 'new' || strtolower($current_status) === 'lost' ) {
				update_post_meta( $contact_id, '_qb_lead_status', 'Contacted' );
				// TODO: Fire Magic Cloak notification to the assigned agent
			}
		}
	}
}
