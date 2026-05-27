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

		// 4. Register Event CPT (Scheduler)
		$event_labels = array(
			'name'               => _x( 'Events', 'post type general name', 'xophz-compass-quests' ),
			'singular_name'      => _x( 'Event', 'post type singular name', 'xophz-compass-quests' )
		);

		$event_args = array(
			'labels'             => $event_labels,
			'description'        => __( 'Questbook Calendar Events.', 'xophz-compass-quests' ),
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

		register_post_type( 'questbook_event', $event_args );

		// 5. Register Board CPT (Pipeline)
		$board_labels = array(
			'name'               => _x( 'Boards', 'post type general name', 'xophz-compass-quests' ),
			'singular_name'      => _x( 'Board', 'post type singular name', 'xophz-compass-quests' )
		);

		$board_args = array(
			'labels'             => $board_labels,
			'description'        => __( 'Questbook Pipeline Boards.', 'xophz-compass-quests' ),
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

		register_post_type( 'questbook_board', $board_args );
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
				
				// Fire Magic Cloak notification to the assigned agent
				$agent_id = get_post_meta( $contact_id, '_qb_assigned_agent', true );
				if ( ! $agent_id ) {
					$agent_id = get_post_field( 'post_author', $contact_id );
				}
				
				if ( $agent_id ) {
					$payload = array(
						'id' => 'lead_reply_' . $contact_id . '_' . time(),
						'title' => 'New Lead Reply',
						'content' => get_the_title( $contact_id ) . ' just replied to your message.',
						'icon' => 'fas fa-comment-dots',
						'color' => 'success',
						'actionLabel' => 'View Lead',
						'actionRoute' => 'questbook-profile',
						'actionParams' => array( 'id' => $contact_id )
					);
					do_action( 'magic_cloak_queue_direct_hint', $agent_id, $payload );
				}
			}
		}
	}

	/**
	 * Hook triggered when a Contact completes a Quest
	 * Grants XP/AP/GP to the linked WordPress user account
	 */
	public function handle_quest_completion( $contact_id, $quest_id, $wp_user_id ) {
		if ( ! $wp_user_id ) {
			return; // No linked WP user, no XP granted
		}

		$xp_reward = absint( get_post_meta( $quest_id, '_qb_reward_xp', true ) );
		$ap_reward = absint( get_post_meta( $quest_id, '_qb_reward_ap', true ) );
		$gp_reward = absint( get_post_meta( $quest_id, '_qb_reward_gp', true ) );

		if ( $xp_reward > 0 ) {
			$current_xp = (int) get_user_meta( $wp_user_id, '_xp_total_xp', true );
			update_user_meta( $wp_user_id, '_xp_total_xp', $current_xp + $xp_reward );
		}

		if ( $ap_reward > 0 ) {
			$current_ap = (int) get_user_meta( $wp_user_id, '_xp_total_ap', true );
			update_user_meta( $wp_user_id, '_xp_total_ap', $current_ap + $ap_reward );
		}

		if ( $gp_reward > 0 ) {
			$current_gp = (int) get_user_meta( $wp_user_id, '_xp_total_gp', true );
			update_user_meta( $wp_user_id, '_xp_total_gp', $current_gp + $gp_reward );
		}
		
		// If xp_log is registered, record it in the XP Action Log
		if ( post_type_exists( 'xp_log' ) ) {
			
			$contact_name = get_the_title( $contact_id );
			$quest_name = get_the_title( $quest_id );

			$payload = array(
				'type'         => 'quest_completed',
				'quest_id'     => $quest_id,
				'quest_title'  => $quest_name,
				'contact_id'   => $contact_id,
				'contact_name' => $contact_name,
				'rewards' => array(
					'xp' => $xp_reward,
					'ap' => $ap_reward,
					'gp' => $gp_reward,
				),
				'date'         => current_time('mysql'),
			);

			// Provide a gamification identifier string
			$metric_key = 'questbook_quest_completed';

			// Call the xp_log recording hook
			do_action( 'xophz_compass_record_action', $metric_key, $wp_user_id, $payload );
		}
	}

	/**
	 * Hook triggered when a Gamification Goal is won.
	 * Checks if the user has any active Quests that require this Goal, and auto-completes them.
	 */
	public function handle_goal_won( $wp_user_id, $goal_id, $goal_title ) {
		if ( ! $wp_user_id ) return;

		// 1. Find the Contact associated with this WP User
		$contacts = get_posts( array(
			'post_type' => 'questbook_contact',
			'meta_key' => '_qb_user_id',
			'meta_value' => $wp_user_id,
			'posts_per_page' => 1
		) );

		if ( empty( $contacts ) ) return;
		$contact_id = $contacts[0]->ID;

		// 2. Fetch their active quests
		$active_quests_json = get_post_meta( $contact_id, '_qb_active_quests', true );
		$active_quests = empty( $active_quests_json ) ? array() : json_decode( $active_quests_json, true );
		if ( empty( $active_quests ) ) return;

		$quests_updated = false;

		// 3. Scan Quests for tasks tied to this goal
		foreach ( $active_quests as &$quest_data ) {
			if ( ! empty( $quest_data['completed'] ) ) continue; // Skip already finished quests

			$tasks = $quest_data['tasks'] ?? [];
			$all_completed = true;
			$task_updated = false;

			foreach ( $tasks as &$task ) {
				if ( empty( $task['completed'] ) ) {
					// Is this task tied to the Goal that was just won?
					if ( isset( $task['goal_id'] ) && (int) $task['goal_id'] === (int) $goal_id ) {
						$task['completed'] = true;
						$task_updated = true;
						$quests_updated = true;
					} else {
						$all_completed = false; // Still missing at least one task
					}
				}
			}

			// If we updated a task, and all tasks are now complete, mark quest complete
			if ( $task_updated ) {
				$quest_data['tasks'] = $tasks;
				if ( $all_completed ) {
					$quest_data['completed'] = true;
					$quest_data['completed_at'] = current_time('mysql');
					// Trigger Quest Completion Hook!
					do_action( 'questbook_quest_completed', $contact_id, $quest_data['quest_id'], $wp_user_id );
				}
			}
		}

		// 4. Save updates if any auto-completions occurred
		if ( $quests_updated ) {
			update_post_meta( $contact_id, '_qb_active_quests', wp_json_encode( $active_quests ) );
		}
	}
}
