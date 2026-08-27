<?php

/**
 * REST API handling for Questbook CRM Contacts
 *
 * @package    Xophz_Compass_Quests
 * @subpackage Xophz_Compass_Quests/includes
 */

class Xophz_Compass_Quests_REST {

	public function register_routes() {
		add_filter( 'compass_abilities_registry', array( $this, 'register_quests_abilities' ) );
		add_action( 'wp_abilities_init', array( $this, 'register_wp_abilities' ) );
		add_filter( 'compass_perform_widgets', array( $this, 'register_perform_widgets' ) );

		add_action( 'rest_api_init', function () {
            register_rest_route( 'questbook/v1', '/summary', array(
                array(
                    'methods'  => WP_REST_Server::READABLE,
                    'callback' => array( $this, 'get_summary' ),
                    'permission_callback' => array( $this, 'check_permission' ),
                ),
            ) );

            register_rest_route( 'questbook/v1', '/organizations', array(
                array(
                    'methods'  => WP_REST_Server::READABLE,
                    'callback' => array( $this, 'get_organizations' ),
                    'permission_callback' => array( $this, 'check_permission' ),
                ),
                array(
                    'methods' => WP_REST_Server::CREATABLE,
                    'callback' => array( $this, 'create_organization' ),
                    'permission_callback' => array( $this, 'check_permission' ),
                ),
            ) );

            register_rest_route( 'questbook/v1', '/organizations/(?P<id>\d+)', array(
                array(
                    'methods'  => WP_REST_Server::READABLE,
                    'callback' => array( $this, 'get_organization' ),
                    'permission_callback' => array( $this, 'check_permission' ),
                ),
                array(
                    'methods' => WP_REST_Server::EDITABLE,
                    'callback' => array( $this, 'update_organization' ),
                    'permission_callback' => array( $this, 'check_permission' ),
                ),
                array(
                    'methods' => WP_REST_Server::DELETABLE,
                    'callback' => array( $this, 'delete_organization' ),
                    'permission_callback' => array( $this, 'check_permission' ),
                ),
            ) );

            register_rest_route( 'questbook/v1', '/organizations/(?P<id>\d+)/contacts', array(
                array(
                    'methods'  => WP_REST_Server::READABLE,
                    'callback' => array( $this, 'get_organization_contacts' ),
                    'permission_callback' => array( $this, 'check_permission' ),
                ),
            ) );

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

            register_rest_route( 'questbook/v1', '/contacts/sync-wp-users', array(
                array(
                    'methods'  => WP_REST_Server::CREATABLE,
                    'callback' => array( $this, 'sync_wp_users' ),
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

            // Deals CRUD
            register_rest_route( 'questbook/v1', '/deals', array(
                array(
                    'methods'  => WP_REST_Server::READABLE,
                    'callback' => array( $this, 'get_deals' ),
                    'permission_callback' => array( $this, 'check_permission' ),
                ),
                array(
                    'methods' => WP_REST_Server::CREATABLE,
                    'callback' => array( $this, 'create_deal' ),
                    'permission_callback' => array( $this, 'check_permission' ),
                ),
            ) );

            register_rest_route( 'questbook/v1', '/deals/(?P<id>\d+)', array(
                array(
                    'methods' => WP_REST_Server::EDITABLE,
                    'callback' => array( $this, 'update_deal' ),
                    'permission_callback' => array( $this, 'check_permission' ),
                ),
                array(
                    'methods' => WP_REST_Server::DELETABLE,
                    'callback' => array( $this, 'delete_deal' ),
                    'permission_callback' => array( $this, 'check_permission' ),
                ),
            ) );

            // Skip Tracing Intelligence & Contact Enrichment
            register_rest_route( 'questbook/v1', '/skiptrace/query', array(
                array(
                    'methods'             => WP_REST_Server::CREATABLE,
                    'callback'            => array( $this, 'skiptrace_query' ),
                    'permission_callback' => array( $this, 'check_permission' ),
                ),
            ) );

            register_rest_route( 'questbook/v1', '/skiptrace/audit', array(
                array(
                    'methods'             => WP_REST_Server::READABLE,
                    'callback'            => array( $this, 'skiptrace_get_audit' ),
                    'permission_callback' => array( $this, 'check_permission' ),
                ),
            ) );

            register_rest_route( 'questbook/v1', '/contacts/(?P<id>\d+)/enrich', array(
                array(
                    'methods'             => WP_REST_Server::CREATABLE,
                    'callback'            => array( $this, 'skiptrace_enrich_contact' ),
                    'permission_callback' => array( $this, 'check_permission' ),
                ),
            ) );

            register_rest_route( 'questbook/v1', '/contacts/(?P<id>\d+)/skiptrace', array(
                array(
                    'methods'             => WP_REST_Server::READABLE,
                    'callback'            => array( $this, 'skiptrace_get_contact_dossier' ),
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

            register_rest_route( 'questbook/v1', '/me', array(
                array(
                    'methods'  => WP_REST_Server::READABLE,
                    'callback' => array( $this, 'get_me_client_data' ),
                    'methods' => WP_REST_Server::EDITABLE,
                    'callback' => array( $this, 'update_contact_quest' ),
                    'permission_callback' => array( $this, 'check_permission' ),
                ),
            ) );
		});
	}

    public function check_permission() {
        return true;
    }

    public function register_perform_widgets( $widgets ) {
        $widgets[] = array(
            'key'           => 'questbook-crm-summary',
            'plugin'        => 'xophz-compass-quests',
            'title'         => 'CRM Performance Summary',
            'icon'          => 'fad fa-chart-line',
            'color'         => '#62c9ff',
            'component'     => 'questbook-crm-summary',
            'data_endpoint' => '/wp-json/questbook/v1/summary',
            'size'          => 'md',
            'order'         => 10,
        );
        $widgets[] = array(
            'key'           => 'questbook-pipeline-summary',
            'plugin'        => 'xophz-compass-quests',
            'title'         => 'Pipeline & Deals Velocity',
            'icon'          => 'fad fa-stream',
            'color'         => '#00e676',
            'component'     => 'questbook-pipeline-summary',
            'data_endpoint' => '/wp-json/questbook/v1/deals',
            'size'          => 'md',
            'order'         => 11,
        );
        $widgets[] = array(
            'key'           => 'questbook-inbox-activity',
            'plugin'        => 'xophz-compass-quests',
            'title'         => 'Inbound Stream',
            'icon'          => 'fad fa-inbox',
            'color'         => '#ff9100',
            'component'     => 'questbook-inbox-activity',
            'data_endpoint' => '/wp-json/questbook/v1/inbox',
            'size'          => 'md',
            'order'         => 12,
        );
        $widgets[] = array(
            'key'           => 'questbook-calendar-events',
            'plugin'        => 'xophz-compass-quests',
            'title'         => 'Upcoming Appointments',
            'icon'          => 'fad fa-calendar-alt',
            'color'         => '#b388ff',
            'component'     => 'questbook-calendar-events',
            'data_endpoint' => '/wp-json/questbook/v1/events',
            'size'          => 'md',
            'order'         => 13,
        );
        return $widgets;
    }

    public function get_summary( WP_REST_Request $request ) {
        global $wpdb;
        $contacts_tbl = $wpdb->prefix . 'xophz_qb_contacts';
        $deals_tbl    = $wpdb->prefix . 'xophz_qb_deals';
        $logs_tbl     = $wpdb->prefix . 'xophz_qb_logs';
        $events_tbl   = $wpdb->prefix . 'xophz_qb_events';

        $total_contacts = (int) $wpdb->get_var("SELECT COUNT(*) FROM {$contacts_tbl}");
        $new_contacts_week = (int) $wpdb->get_var("SELECT COUNT(*) FROM {$contacts_tbl} WHERE created_at >= DATE_SUB(NOW(), INTERVAL 7 DAY)");

        $total_deals = (int) $wpdb->get_var("SELECT COUNT(*) FROM {$deals_tbl}");
        $deals_revenue = (float) $wpdb->get_var("SELECT SUM(amount) FROM {$deals_tbl} WHERE stage != 'Lost'");

        $unread_inbox = 0;
        $logs = $wpdb->get_results("SELECT meta_data FROM {$logs_tbl}");
        foreach ($logs as $l) {
            $meta = json_decode($l->meta_data, true) ?: array();
            if (($meta['direction'] ?? '') === 'inbound' && ($meta['is_read'] ?? 'yes') === 'no') {
                $unread_inbox++;
            }
        }

        $today = current_time('Y-m-d');
        $today_events = (int) $wpdb->get_var($wpdb->prepare("SELECT COUNT(*) FROM {$events_tbl} WHERE start_date LIKE %s", $today . '%'));

        return rest_ensure_response(array(
            'total_contacts'    => $total_contacts,
            'new_contacts_week' => $new_contacts_week,
            'total_deals'       => $total_deals,
            'deals_revenue'     => $deals_revenue,
            'unread_inbox'      => $unread_inbox,
            'today_events'      => $today_events,
        ));
    }

    public function sync_wp_users( WP_REST_Request $request ) {
        global $wpdb;
        $contacts_table = $wpdb->prefix . 'xophz_qb_contacts';

        $users = get_users( array( 'number' => 1000 ) );
        $synced_count = 0;

        foreach ( $users as $user ) {
            $wp_first = get_user_meta( $user->ID, 'first_name', true );
            $wp_last  = get_user_meta( $user->ID, 'last_name', true );
            
            if ( empty( $wp_first ) && empty( $wp_last ) ) {
                $display_name = trim( $user->display_name );
                if ( ! empty( $display_name ) && strpos( $display_name, '@' ) === false ) {
                    $parts = explode( ' ', $display_name, 2 );
                    $wp_first = $parts[0];
                    $wp_last  = $parts[1] ?? '';
                } else {
                    $wp_first = $user->user_login;
                    $wp_last  = '';
                }
            }

            $existing = $wpdb->get_row( $wpdb->prepare(
                "SELECT id, first_name, last_name, email, wp_user_id FROM {$contacts_table} WHERE wp_user_id = %d OR (email = %s AND email != '')",
                $user->ID,
                $user->user_email
            ) );

            if ( $existing ) {
                $update_data = array();
                if ( ! $existing->wp_user_id ) {
                    $update_data['wp_user_id'] = $user->ID;
                }
                $current_name = trim( $existing->first_name . ' ' . $existing->last_name );
                if ( empty( $current_name ) || $current_name === $existing->email || strpos( $current_name, '@' ) !== false ) {
                    $update_data['first_name'] = $wp_first;
                    $update_data['last_name']  = $wp_last;
                }
                if ( ! empty( $update_data ) ) {
                    $wpdb->update( $contacts_table, $update_data, array( 'id' => $existing->id ) );
                    $synced_count++;
                }
            } else {
                $wpdb->insert(
                    $contacts_table,
                    array(
                        'wp_user_id'  => $user->ID,
                        'first_name'  => $wp_first,
                        'last_name'   => $wp_last,
                        'email'       => $user->user_email,
                        'lead_status' => 'Customer',
                        'source'      => 'WP User',
                        'created_at'  => current_time( 'mysql' ),
                        'updated_at'  => current_time( 'mysql' ),
                    )
                );
                $synced_count++;
            }
        }

        return rest_ensure_response( array(
            'success'      => true,
            'synced_count' => $synced_count,
            'total_users'  => count( $users ),
        ) );
    }

    public function get_contacts( WP_REST_Request $request ) {
        global $wpdb;
        $table = $wpdb->prefix . 'xophz_qb_contacts';

        $page = $request->get_param( 'page' ) ? absint( $request->get_param( 'page' ) ) : 1;
        $per_page = $request->get_param( 'per_page' ) ? absint( $request->get_param( 'per_page' ) ) : 50;
        $search = $request->get_param( 'search' );
        
        $offset = ($page - 1) * $per_page;
        $where = "WHERE 1=1";
        $params = array();

        if ( ! empty( $search ) ) {
            $where .= " AND (first_name LIKE %s OR last_name LIKE %s OR email LIKE %s OR company LIKE %s)";
            $like = '%' . $wpdb->esc_like( $search ) . '%';
            $params[] = $like;
            $params[] = $like;
            $params[] = $like;
            $params[] = $like;
        }

        $query = "SELECT * FROM {$table} {$where} ORDER BY updated_at DESC LIMIT %d OFFSET %d";
        $params[] = $per_page;
        $params[] = $offset;

        $results = $wpdb->get_results( $wpdb->prepare( $query, $params ) );
        
        // Get total count for pagination
        $count_query = "SELECT COUNT(id) FROM {$table} {$where}";
        $total_items = $wpdb->get_var( empty($params) ? $count_query : $wpdb->prepare( $count_query, array_slice($params, 0, -2) ) );
        $total_pages = ceil($total_items / $per_page);

        $formatted_contacts = array();
        $existing_emails = array();

        foreach ( $results as $contact ) {
            $formatted = $this->format_contact( $contact );
            $formatted_contacts[] = $formatted;
            if ( ! empty( $formatted['email'] ) ) {
                $existing_emails[] = strtolower( $formatted['email'] );
            }
        }

        // Bridge WP Users into search results if searching and matches WP User table
        if ( ! empty( $search ) && strlen( trim( $search ) ) >= 2 ) {
            $wp_user_matches = get_users( array(
                'search'         => '*' . esc_attr( $search ) . '*',
                'search_columns' => array( 'user_login', 'user_nicename', 'user_email', 'display_name' ),
                'number'         => 10,
            ) );

            foreach ( $wp_user_matches as $wp_user ) {
                $email = strtolower( $wp_user->user_email );
                if ( ! in_array( $email, $existing_emails, true ) ) {
                    $formatted_contacts[] = array(
                        'id'          => 'wp_' . $wp_user->ID,
                        'wp_user_id'  => $wp_user->ID,
                        'name'        => $wp_user->display_name ?: $wp_user->user_login,
                        'email'       => $wp_user->user_email,
                        'phone'       => '',
                        'company'     => '',
                        'lead_status' => 'WP User',
                        'source'      => 'WordPress User',
                        'is_wp_user'  => true,
                        'created_at'  => $wp_user->user_registered,
                    );
                    $existing_emails[] = $email;
                }
            }
        }

        $response = new WP_REST_Response( $formatted_contacts );
        $response->header( 'X-WP-Total', $total_items );
        $response->header( 'X-WP-TotalPages', $total_pages );

        return $response;
    }

    public function get_contact( WP_REST_Request $request ) {
        global $wpdb;
        $table = $wpdb->prefix . 'xophz_qb_contacts';
        $id = $request->get_param( 'id' );
        
        $contact = $wpdb->get_row( $wpdb->prepare("SELECT * FROM {$table} WHERE id = %d", $id) );

        if ( ! $contact ) {
            return new WP_Error( 'no_contact', 'Invalid contact', array( 'status' => 404 ) );
        }

        return rest_ensure_response( $this->format_contact( $contact ) );
    }

    public function create_contact( WP_REST_Request $request ) {
        global $wpdb;
        $table = $wpdb->prefix . 'xophz_qb_contacts';
        $params = $request->get_json_params();
        
        $name_parts = explode(' ', sanitize_text_field($params['name'] ?? 'New Lead'), 2);
        
        $data = array(
            'first_name' => $name_parts[0],
            'last_name' => $name_parts[1] ?? '',
            'email' => sanitize_email($params['email'] ?? ''),
            'phone' => sanitize_text_field($params['phone'] ?? ''),
            'source' => sanitize_text_field($params['source'] ?? 'Manual'),
            'lead_status' => sanitize_text_field($params['stage'] ?? 'New Lead'),
            'meta_data' => wp_json_encode(array(
                'notes' => sanitize_textarea_field($params['notes'] ?? ''),
                'company' => sanitize_text_field($params['company'] ?? ''),
            ))
        );

        if (isset($params['user_id'])) $data['wp_user_id'] = absint($params['user_id']);
        if (isset($params['company_id'])) $data['company_id'] = absint($params['company_id']);

        $result = $wpdb->insert($table, $data);

        if ( ! $result ) {
            return new WP_Error( 'insert_failed', 'Could not create contact', array( 'status' => 500 ) );
        }

        $req = new WP_REST_Request( 'GET', '/questbook/v1/contacts/' . $wpdb->insert_id );
        $req->set_param( 'id', $wpdb->insert_id );
        return $this->get_contact( $req );
    }

    public function update_contact( WP_REST_Request $request ) {
        global $wpdb;
        $table = $wpdb->prefix . 'xophz_qb_contacts';
        
        $id = $request->get_param( 'id' );
        $params = $request->get_json_params();

        // Get existing to merge meta_data
        $existing = $wpdb->get_row($wpdb->prepare("SELECT * FROM {$table} WHERE id = %d", $id));
        if ( ! $existing ) return new WP_Error( 'not_found', 'Contact not found', array('status' => 404) );

        $data = array();
        
        if (isset($params['name'])) {
            $name_parts = explode(' ', sanitize_text_field($params['name']), 2);
            $data['first_name'] = $name_parts[0];
            $data['last_name'] = $name_parts[1] ?? '';
        }
        if (isset($params['email'])) $data['email'] = sanitize_email($params['email']);
        if (isset($params['phone'])) $data['phone'] = sanitize_text_field($params['phone']);
        if (isset($params['stage'])) $data['lead_status'] = sanitize_text_field($params['stage']);
        if (isset($params['source'])) $data['source'] = sanitize_text_field($params['source']);
        if (isset($params['user_id'])) $data['wp_user_id'] = absint($params['user_id']);
        if (array_key_exists('company_id', $params)) $data['company_id'] = absint($params['company_id']);
        
        $meta = json_decode($existing->meta_data, true) ?: array();
        if (isset($params['notes'])) $meta['notes'] = sanitize_textarea_field($params['notes']);
        if (isset($params['company'])) $meta['company'] = sanitize_text_field($params['company']);
        if (isset($params['servicePackage'])) $meta['servicePackage'] = sanitize_text_field($params['servicePackage']);
        if (isset($params['paymentStatus'])) $meta['paymentStatus'] = sanitize_text_field($params['paymentStatus']);
        if (isset($params['retainer'])) $meta['retainer'] = sanitize_text_field($params['retainer']);
        if (isset($params['board_stages'])) $meta['board_stages'] = $params['board_stages'];

        $data['meta_data'] = wp_json_encode($meta);

        $wpdb->update($table, $data, array('id' => $id));

        $req = new WP_REST_Request( 'GET', '/questbook/v1/contacts/' . $id );
        $req->set_param( 'id', $id );
        return $this->get_contact( $req );
    }

    public function delete_contact( WP_REST_Request $request ) {
        global $wpdb;
        $table = $wpdb->prefix . 'xophz_qb_contacts';
        $id = $request->get_param( 'id' );
        
        $result = $wpdb->delete($table, array('id' => $id));

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
        $meta = json_decode($contact->meta_data, true) ?: array();
        $name = trim($contact->first_name . ' ' . $contact->last_name);
        $email = $contact->email;

        if ( $contact->wp_user_id ) {
            $user = get_userdata( $contact->wp_user_id );
            if ( $user ) {
                if ( empty( $email ) ) $email = $user->user_email;

                $wp_first = get_user_meta( $user->ID, 'first_name', true );
                $wp_last  = get_user_meta( $user->ID, 'last_name', true );
                $wp_full  = trim( $wp_first . ' ' . $wp_last );

                if ( empty( $wp_full ) ) {
                    $wp_full = ( $user->display_name && strpos( $user->display_name, '@' ) === false ) ? $user->display_name : $user->user_login;
                }

                if ( empty( $name ) || $name === $email || strpos( $name, '@' ) !== false ) {
                    $name = $wp_full;
                }
            }
        }

        return array(
            'id'              => (string) $contact->id,
            'user_id'         => $contact->wp_user_id,
            'name'            => $name,
            'email'           => $email,
            'phone'           => $contact->phone,
            'stage'           => $contact->lead_status,
            'company_id'      => (int) $contact->company_id,
            'company'         => $meta['company'] ?? '',
            'servicePackage'  => $meta['servicePackage'] ?? 'COMPASS Executive Consulting',
            'paymentStatus'   => $meta['paymentStatus'] ?? 'Paid',
            'retainer'        => isset($meta['retainer']) ? (float) $meta['retainer'] : 2500,
            'notes'           => $meta['notes'] ?? '',
            'source'          => $contact->source ?: 'mycompassconsulting.com',
            'board_stages'    => $meta['board_stages'] ?? array(),
            'createdDate'     => $contact->created_at,
        );
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
        global $wpdb;
        $table = $wpdb->prefix . 'xophz_qb_logs';
        $id = $request->get_param( 'id' );
        
        $logs = $wpdb->get_results( $wpdb->prepare("SELECT * FROM {$table} WHERE contact_id = %d ORDER BY created_at ASC", $id) );
        $formatted_logs = array();
        
        foreach ( $logs as $log ) {
            $meta = json_decode($log->meta_data, true) ?: array();
            $formatted_logs[] = array(
                'id'         => (string) $log->id,
                'title'      => $meta['title'] ?? ucfirst( $log->action_type ) . ' Log',
                'content'    => $log->description,
                'type'       => $log->action_type,
                'direction'  => $meta['direction'] ?? 'outbound',
                'internal'   => !empty($meta['internal']) && $meta['internal'] === 'yes',
                'promoted_to'=> $meta['promoted_to'] ?? '',
                'is_read'    => !isset($meta['is_read']) || $meta['is_read'] !== 'no',
                'date'       => $log->created_at,
            );
        }
        
        return rest_ensure_response( $formatted_logs );
    }

    public function create_contact_log( WP_REST_Request $request ) {
        global $wpdb;
        $table = $wpdb->prefix . 'xophz_qb_logs';
        $contacts_table = $wpdb->prefix . 'xophz_qb_contacts';
        
        $id = $request->get_param( 'id' );
        $params = $request->get_json_params();
        
        $type = isset( $params['type'] ) ? sanitize_text_field( $params['type'] ) : 'note';
        $content = isset( $params['content'] ) ? sanitize_textarea_field( $params['content'] ) : '';
        $internal = isset( $params['internal'] ) && $params['internal'] ? 'yes' : 'no';
        
        if ( empty( $content ) ) {
            return new WP_Error( 'empty_content', 'Log content cannot be empty', array( 'status' => 400 ) );
        }

        $meta = array(
            'title' => ucfirst( $type ) . ' Log',
            'direction' => 'outbound',
            'internal' => $internal,
            'is_read' => 'yes'
        );

        $data = array(
            'contact_id' => absint($id),
            'action_type' => $type,
            'description' => $content,
            'created_by' => get_current_user_id(),
            'meta_data' => wp_json_encode($meta)
        );

        $result = $wpdb->insert($table, $data);
        
        if ( ! $result ) {
            return new WP_Error( 'insert_failed', 'Could not create log', array( 'status' => 500 ) );
        }
        $log_id = $wpdb->insert_id;
        
        if ( $type === 'sms' && $internal === 'no' ) {
            $contact = $wpdb->get_row($wpdb->prepare("SELECT phone FROM {$contacts_table} WHERE id = %d", $id));
            $to_phone = $contact ? $contact->phone : '';

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
            $contact = $wpdb->get_row($wpdb->prepare("SELECT email FROM {$contacts_table} WHERE id = %d", $id));
            $to_email = $contact ? $contact->email : '';
            if ( $to_email ) {
                $subject = "Message from Compass Support";
                wp_mail( $to_email, $subject, $content );
            }
        }
        
        return rest_ensure_response( array( 'success' => true, 'log_id' => (string)$log_id ) );
    }

    public function handle_twilio_webhook( WP_REST_Request $request ) {
        global $wpdb;
        $contacts_table = $wpdb->prefix . 'xophz_qb_contacts';
        $logs_table = $wpdb->prefix . 'xophz_qb_logs';
        
        $params = $request->get_body_params();
        $from = isset( $params['From'] ) ? sanitize_text_field( $params['From'] ) : '';
        $body = isset( $params['Body'] ) ? sanitize_textarea_field( $params['Body'] ) : '';
        
        if ( empty( $from ) || empty( $body ) ) {
            return new WP_Error( 'missing_data', 'Missing From or Body', array( 'status' => 400 ) );
        }
        
        $contact = $wpdb->get_row($wpdb->prepare("SELECT id FROM {$contacts_table} WHERE phone = %s LIMIT 1", $from));
        
        if ( ! $contact ) {
            $wpdb->insert($contacts_table, array(
                'first_name' => 'Unknown',
                'last_name' => "($from)",
                'phone' => $from,
                'lead_status' => 'New Lead',
                'source' => 'Twilio SMS'
            ));
            $contact_id = $wpdb->insert_id;
        } else {
            $contact_id = $contact->id;
        }
        
        $meta = array(
            'title' => 'Inbound SMS',
            'direction' => 'inbound',
            'internal' => 'no',
            'is_read' => 'no'
        );

        $wpdb->insert($logs_table, array(
            'contact_id' => $contact_id,
            'action_type' => 'sms',
            'description' => $body,
            'meta_data' => wp_json_encode($meta)
        ));
        
        $response = new WP_REST_Response( '<Response></Response>' );
        $response->header( 'Content-Type', 'text/xml' );
        return $response;
    }

    public function handle_email_webhook( WP_REST_Request $request ) {
        global $wpdb;
        $contacts_table = $wpdb->prefix . 'xophz_qb_contacts';
        $logs_table = $wpdb->prefix . 'xophz_qb_logs';

        $params = $request->get_params();
        $from = isset( $params['from'] ) ? sanitize_text_field( $params['from'] ) : '';
        $text = isset( $params['text'] ) ? sanitize_textarea_field( $params['text'] ) : '';
        
        if ( empty( $from ) ) return new WP_Error( 'missing_data', 'Missing from', array('status' => 400) );
        
        preg_match( '/<([^>]+)>/', $from, $matches );
        $raw_email = isset( $matches[1] ) ? sanitize_email($matches[1]) : sanitize_email($from);
        
        $contact = $wpdb->get_row($wpdb->prepare("SELECT id FROM {$contacts_table} WHERE email = %s LIMIT 1", $raw_email));
        
        if ( ! $contact ) {
             $wpdb->insert($contacts_table, array(
                'first_name' => 'Unknown',
                'last_name' => "($raw_email)",
                'email' => $raw_email,
                'lead_status' => 'New Lead',
                'source' => 'Email Webhook'
            ));
            $contact_id = $wpdb->insert_id;
        } else {
            $contact_id = $contact->id;
        }
        
        $meta = array(
            'title' => 'Inbound Email',
            'direction' => 'inbound',
            'internal' => 'no',
            'is_read' => 'no'
        );

        $wpdb->insert($logs_table, array(
            'contact_id' => $contact_id,
            'action_type' => 'email',
            'description' => $text,
            'meta_data' => wp_json_encode($meta)
        ));
        
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
        global $wpdb;
        $logs_table = $wpdb->prefix . 'xophz_qb_logs';
        
        $log_id = $request->get_param( 'log_id' );
        $log = $wpdb->get_row( $wpdb->prepare("SELECT * FROM {$logs_table} WHERE id = %d", $log_id) );
        
        if ( ! $log ) {
            return new WP_Error( 'not_found', 'Log not found', array( 'status' => 404 ) );
        }
        
        $contact_id = $log->contact_id;
        $content = $log->description;
        
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
        
        if ( $contact_id ) {
            update_post_meta( $quest_id, '_qb_contact_id', $contact_id );
        }
        
        // Mark log as promoted via meta_data
        $meta = json_decode($log->meta_data, true) ?: array();
        $meta['promoted_to'] = $quest_id;
        $wpdb->update($logs_table, array('meta_data' => wp_json_encode($meta)), array('id' => $log_id));
        
        return rest_ensure_response( array( 'success' => true, 'quest_id' => $quest_id ) );
    }

    public function get_global_inbox( WP_REST_Request $request ) {
        global $wpdb;
        $logs_table = $wpdb->prefix . 'xophz_qb_logs';
        $contacts_table = $wpdb->prefix . 'xophz_qb_contacts';
        
        $logs_data = $wpdb->get_results("SELECT l.*, c.first_name, c.last_name, c.email as contact_email FROM {$logs_table} l LEFT JOIN {$contacts_table} c ON l.contact_id = c.id ORDER BY l.created_at DESC LIMIT 100");
        
        $logs = array();
        foreach ( $logs_data as $log ) {
            $meta = json_decode($log->meta_data, true) ?: array();
            $contact_name = trim(($log->first_name ?? '') . ' ' . ($log->last_name ?? ''));
            if (empty($contact_name)) $contact_name = $log->contact_email ?: 'Unknown';
            
            $logs[] = array(
                'id'         => (string) $log->id,
                'contact_id' => (string) $log->contact_id,
                'contact_name'=> $contact_name,
                'title'      => $meta['title'] ?? ucfirst($log->action_type) . ' Log',
                'content'    => $log->description,
                'type'       => $log->action_type,
                'direction'  => $meta['direction'] ?? 'outbound',
                'internal'   => !empty($meta['internal']) && $meta['internal'] === 'yes',
                'promoted_to'=> $meta['promoted_to'] ?? '',
                'is_read'    => !isset($meta['is_read']) || $meta['is_read'] !== 'no',
                'date'       => $log->created_at,
            );
        }

        // Fetch recent Forminator entries to show in inbox even without webhook
        if ( class_exists( 'Forminator_API' ) ) {
            $frmt_table = $wpdb->prefix . 'frmt_form_entry';
            $frmt_exists = $wpdb->get_var( "SHOW TABLES LIKE '{$frmt_table}'" ) === $frmt_table;
            
            if ( $frmt_exists ) {
                $recent_entries = $wpdb->get_results( "SELECT entry_id, form_id, date_created FROM {$frmt_table} ORDER BY date_created DESC LIMIT 50" );
                
                foreach ( $recent_entries as $entry ) {
                    $form_id = $entry->form_id;
                    $full_entry = class_exists('Forminator_Form_Entry_Model') ? new Forminator_Form_Entry_Model( $entry->entry_id ) : null;
                    
                    $email = 'Unknown';
                    $name = 'Form Submission';
                    $content_parts = array();
                    
                    if ( ! is_wp_error( $full_entry ) && $full_entry && ! empty( $full_entry->meta_data ) ) {
                        foreach ( $full_entry->meta_data as $meta_key => $meta ) {
                            $meta_name = isset( $meta['name'] ) ? $meta['name'] : $meta_key;
                            $meta_val  = isset( $meta['value'] ) ? $meta['value'] : '';
                            if ( is_array( $meta_val ) ) $meta_val = implode( ', ', $meta_val );
                            if ( strpos( $meta_name, 'email' ) !== false ) $email = $meta_val;
                            if ( strpos( $meta_name, 'name' ) !== false && $name === 'Form Submission' ) $name = $meta_val;
                            if ( ! empty( $meta_val ) && is_string( $meta_val ) && strpos( $meta_name, '_' ) !== 0 ) {
                                $content_parts[] = ucfirst( str_replace('-', ' ', $meta_name) ) . ': ' . $meta_val;
                            }
                        }
                    }
                    
                    // Match contact via custom table by email or wp_user_id
                    $contact_id = 0;
                    if ( $email !== 'Unknown' ) {
                        $matched = $wpdb->get_var( $wpdb->prepare("SELECT id FROM {$contacts_table} WHERE email = %s LIMIT 1", $email) );
                        if ( $matched ) {
                            $contact_id = (int) $matched;
                        } else {
                            $user = get_user_by( 'email', $email );
                            if ( $user ) {
                                $matched = $wpdb->get_var( $wpdb->prepare("SELECT id FROM {$contacts_table} WHERE wp_user_id = %d LIMIT 1", $user->ID) );
                                if ( $matched ) $contact_id = (int) $matched;
                            }
                        }
                    }
                    
                    $logs[] = array(
                        'id'         => 'forminator_' . $entry->entry_id,
                        'contact_id' => (string) $contact_id,
                        'contact_name'=> $name . ( $email !== 'Unknown' ? " ($email)" : '' ),
                        'title'      => 'Forminator Form #' . $form_id,
                        'content'    => implode( "\n", $content_parts ),
                        'type'       => 'webform',
                        'direction'  => 'inbound',
                        'internal'   => false,
                        'promoted_to'=> '',
                        'is_read'    => false,
                        'date'       => $entry->date_created,
                    );
                }
            }
        }
        
        usort( $logs, function($a, $b) {
            return strtotime($b['date']) - strtotime($a['date']);
        });
        
        return rest_ensure_response( $logs );
    }

    public function mark_contact_logs_read( WP_REST_Request $request ) {
        global $wpdb;
        $logs_table = $wpdb->prefix . 'xophz_qb_logs';
        $id = $request->get_param( 'id' );
        
        // Find all unread inbound logs for this contact
        $unread = $wpdb->get_results($wpdb->prepare(
            "SELECT id, meta_data FROM {$logs_table} WHERE contact_id = %d",
            $id
        ));
        
        $marked = 0;
        foreach ( $unread as $log ) {
            $meta = json_decode($log->meta_data, true) ?: array();
            $isInboundUnread = ($meta['direction'] ?? '') === 'inbound' && ($meta['is_read'] ?? 'yes') === 'no';
            if ( $isInboundUnread ) {
                $meta['is_read'] = 'yes';
                $wpdb->update($logs_table, array('meta_data' => wp_json_encode($meta)), array('id' => $log->id));
                $marked++;
            }
        }

        return rest_ensure_response( array( 'success' => true, 'marked' => $marked ) );
    }

    // --- Deals ---

    public function get_deals( WP_REST_Request $request ) {
        global $wpdb;
        $deals_table = $wpdb->prefix . 'xophz_qb_deals';
        $contacts_table = $wpdb->prefix . 'xophz_qb_contacts';

        $page = max(1, (int) $request->get_param('page') ?: 1);
        $per_page = min(100, max(1, (int) $request->get_param('per_page') ?: 25));
        $offset = ($page - 1) * $per_page;
        $search = sanitize_text_field( $request->get_param('search') ?: '' );
        $stage = sanitize_text_field( $request->get_param('stage') ?: '' );

        $where = '1=1';
        $params = array();

        if ( $search ) {
            $where .= ' AND (d.title LIKE %s OR c.first_name LIKE %s OR c.last_name LIKE %s)';
            $like = '%' . $wpdb->esc_like($search) . '%';
            $params[] = $like;
            $params[] = $like;
            $params[] = $like;
        }
        if ( $stage ) {
            $where .= ' AND d.stage = %s';
            $params[] = $stage;
        }

        $count_sql = "SELECT COUNT(*) FROM {$deals_table} d LEFT JOIN {$contacts_table} c ON d.contact_id = c.id WHERE {$where}";
        $total = $wpdb->get_var( $params ? $wpdb->prepare($count_sql, ...$params) : $count_sql );

        $sql = "SELECT d.*, CONCAT(c.first_name, ' ', c.last_name) as contact_name, c.email as contact_email 
                FROM {$deals_table} d LEFT JOIN {$contacts_table} c ON d.contact_id = c.id 
                WHERE {$where} ORDER BY d.created_at DESC LIMIT %d OFFSET %d";
        $params[] = $per_page;
        $params[] = $offset;

        $deals = $wpdb->get_results( $wpdb->prepare($sql, ...$params) );

        $formatted = array();
        foreach ($deals as $deal) {
            $formatted[] = array(
                'id' => (int) $deal->id,
                'title' => $deal->title,
                'contact_id' => (int) $deal->contact_id,
                'contact_name' => trim($deal->contact_name),
                'contact_email' => $deal->contact_email,
                'company_id' => (int) $deal->company_id,
                'amount' => (float) $deal->amount,
                'stage' => $deal->stage,
                'description' => $deal->description,
                'created_at' => $deal->created_at,
                'updated_at' => $deal->updated_at,
            );
        }

        $response = rest_ensure_response($formatted);
        $response->header('X-WP-Total', (int) $total);
        $response->header('X-WP-TotalPages', ceil($total / $per_page));
        return $response;
    }

    public function create_deal( WP_REST_Request $request ) {
        global $wpdb;
        $deals_table = $wpdb->prefix . 'xophz_qb_deals';
        $params = $request->get_json_params();

        $data = array(
            'title'       => sanitize_text_field( $params['title'] ?? '' ),
            'contact_id'  => absint( $params['contact_id'] ?? 0 ),
            'company_id'  => absint( $params['company_id'] ?? 0 ),
            'amount'      => floatval( $params['amount'] ?? 0 ),
            'stage'       => sanitize_text_field( $params['stage'] ?? 'New' ),
            'description' => sanitize_textarea_field( $params['description'] ?? '' ),
            'created_at'  => current_time('mysql'),
            'updated_at'  => current_time('mysql'),
        );

        if ( empty($data['title']) ) {
            return new WP_Error('missing_title', 'Deal title is required', array('status' => 400));
        }

        $inserted = $wpdb->insert($deals_table, $data);
        if ( false === $inserted ) {
            // Attempt self-healing schema upgrade via dbDelta if column or table was missing
            Xophz_Compass_Quests_Schema::create_tables();
            $inserted = $wpdb->insert($deals_table, $data);
        }

        if ( false === $inserted ) {
            return new WP_Error('db_error', 'Failed to insert deal into database: ' . $wpdb->last_error, array('status' => 500));
        }

        $data['id'] = (int) $wpdb->insert_id;
        return rest_ensure_response($data);
    }

    public function update_deal( WP_REST_Request $request ) {
        global $wpdb;
        $deals_table = $wpdb->prefix . 'xophz_qb_deals';
        $id = (int) $request->get_param('id');
        $params = $request->get_json_params();

        $existing = $wpdb->get_row( $wpdb->prepare("SELECT id FROM {$deals_table} WHERE id = %d", $id) );
        if ( ! $existing ) {
            return new WP_Error('not_found', 'Deal not found', array('status' => 404));
        }

        $update = array( 'updated_at' => current_time('mysql') );
        if ( isset($params['title']) )       $update['title']       = sanitize_text_field($params['title']);
        if ( array_key_exists('contact_id', $params) )  $update['contact_id']  = absint($params['contact_id']);
        if ( array_key_exists('company_id', $params) )  $update['company_id']  = absint($params['company_id']);
        if ( isset($params['amount']) )      $update['amount']      = floatval($params['amount']);
        if ( isset($params['stage']) )       $update['stage']       = sanitize_text_field($params['stage']);
        if ( isset($params['description']) ) $update['description'] = sanitize_textarea_field($params['description']);

        $wpdb->update($deals_table, $update, array('id' => $id));

        return rest_ensure_response(array('success' => true, 'id' => $id));
    }

    public function delete_deal( WP_REST_Request $request ) {
        global $wpdb;
        $deals_table = $wpdb->prefix . 'xophz_qb_deals';
        $id = (int) $request->get_param('id');

        $wpdb->delete($deals_table, array('id' => $id));
        return rest_ensure_response(array('success' => true));
    }

    // --- Calendar Events ---

    public function get_events( WP_REST_Request $request ) {
        global $wpdb;
        $table = $wpdb->prefix . 'xophz_qb_tasks';
        
        $contact_id = $request->get_param( 'contact_id' );
        $where = "WHERE 1=1";
        $params = array();

        if ( ! empty( $contact_id ) ) {
            $where .= " AND contact_id = %d";
            $params[] = $contact_id;
        }

        $query = "SELECT * FROM {$table} {$where} ORDER BY due_date ASC";
        $tasks = $wpdb->get_results( empty($params) ? $query : $wpdb->prepare( $query, $params ) );
        
        $formatted = array();
        foreach ( $tasks as $task ) {
            $formatted[] = array(
                'id'          => (string) $task->id,
                'title'       => $task->title,
                'date'        => date('Y-m-d', strtotime($task->due_date)),
                'time'        => date('H:i:s', strtotime($task->due_date)),
                'description' => '', // Tasks table could be expanded to have desc, for now empty
                'type'        => $task->status,
                'contact_id'  => (string) $task->contact_id,
            );
        }

        return rest_ensure_response( $formatted );
    }

    public function create_event( WP_REST_Request $request ) {
        global $wpdb;
        $table = $wpdb->prefix . 'xophz_qb_tasks';
        $params = $request->get_json_params();

        $due_date = null;
        if ( !empty($params['date']) && !empty($params['time']) ) {
            $due_date = date('Y-m-d H:i:s', strtotime($params['date'] . ' ' . $params['time']));
        }

        $data = array(
            'title' => sanitize_text_field( $params['title'] ?? 'New Task' ),
            'contact_id' => absint( $params['contact_id'] ?? 0 ),
            'status' => sanitize_text_field( $params['type'] ?? 'Pending' ),
            'due_date' => $due_date
        );

        $result = $wpdb->insert( $table, $data );

        if ( ! $result ) {
            return new WP_Error( 'insert_failed', 'Could not create task', array( 'status' => 500 ) );
        }

        return rest_ensure_response( array( 'success' => true, 'id' => (string) $wpdb->insert_id ) );
    }

    public function update_event( WP_REST_Request $request ) {
        global $wpdb;
        $table = $wpdb->prefix . 'xophz_qb_tasks';
        
        $id = $request->get_param( 'id' );
        $params = $request->get_json_params();

        $data = array();
        if ( isset( $params['title'] ) ) $data['title'] = sanitize_text_field( $params['title'] );
        if ( isset( $params['contact_id'] ) ) $data['contact_id'] = absint( $params['contact_id'] );
        if ( isset( $params['type'] ) ) $data['status'] = sanitize_text_field( $params['type'] );
        
        if ( isset($params['date']) && isset($params['time']) ) {
            $data['due_date'] = date('Y-m-d H:i:s', strtotime($params['date'] . ' ' . $params['time']));
        }

        if ( !empty($data) ) {
            $wpdb->update( $table, $data, array( 'id' => $id ) );
        }

        return rest_ensure_response( array( 'success' => true ) );
    }

    public function delete_event( WP_REST_Request $request ) {
        global $wpdb;
        $table = $wpdb->prefix . 'xophz_qb_tasks';
        $id = $request->get_param( 'id' );
        
        $wpdb->delete( $table, array( 'id' => $id ) );
        return rest_ensure_response( array( 'deleted' => true ) );
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

    public function get_me_client_data( WP_REST_Request $request ) {
        global $wpdb;
        $contacts_table = $wpdb->prefix . 'xophz_qb_contacts';
        
        $user_id = get_current_user_id();
        if ( ! $user_id ) {
            return new WP_Error( 'not_logged_in', 'User is not logged in.', array( 'status' => 401 ) );
        }

        $contact = $wpdb->get_row( $wpdb->prepare("SELECT * FROM {$contacts_table} WHERE wp_user_id = %d LIMIT 1", $user_id) );

        if ( ! $contact ) {
            return rest_ensure_response( array(
                'contact' => null,
                'quests' => array(),
            ) );
        }

        $formatted_contact = $this->format_contact( $contact );

        $request->set_param( 'id', $contact->id );
        $quests_response = $this->get_contact_quests( $request );
        $quests = array();
        if ( ! is_wp_error( $quests_response ) && method_exists( $quests_response, 'get_data' ) ) {
            $quests = $quests_response->get_data();
        } elseif ( ! is_wp_error( $quests_response ) && is_array( $quests_response ) ) {
            $quests = $quests_response;
        }

        return rest_ensure_response( array(
            'contact' => $formatted_contact,
            'quests' => $quests,
        ) );
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

	public function register_quests_abilities( $abilities ) {
		if ( ! is_array( $abilities ) ) {
			$abilities = array();
		}

		$abilities[] = array(
			'id'          => 'compass/query_quests',
			'name'        => 'Query Quests & CRM Contacts',
			'plugin'      => 'xophz-compass-quests',
			'category'    => 'CRM',
			'description' => 'Retrieves active quests, contact status, and task progression.',
			'parameters'  => array(
				'contact_id' => array( 'type' => 'integer', 'required' => false, 'description' => 'Optional contact ID' ),
			),
		);

		$abilities[] = array(
			'id'          => 'compass/complete_quest',
			'name'        => 'Complete Quest Action',
			'plugin'      => 'xophz-compass-quests',
			'category'    => 'CRM',
			'description' => 'Marks a quest task or full quest as completed for a contact.',
			'parameters'  => array(
				'contact_id' => array( 'type' => 'integer', 'required' => true, 'description' => 'Contact ID' ),
				'quest_id'   => array( 'type' => 'string', 'required' => true, 'description' => 'Quest ID' ),
			),
		);

		return $abilities;
	}

	public function register_wp_abilities() {
		if ( function_exists( 'wp_register_ability' ) ) {
			wp_register_ability( 'compass/query_quests', array(
				'label'       => __( 'Query Quests & Contacts', 'xophz-compass-quests' ),
				'description' => __( 'Retrieves active quests and contact status.', 'xophz-compass-quests' ),
				'category'    => 'crm',
			) );

			wp_register_ability( 'compass/complete_quest', array(
				'label'       => __( 'Complete Quest Action', 'xophz-compass-quests' ),
				'description' => __( 'Marks a quest completed for a contact.', 'xophz-compass-quests' ),
				'category'    => 'crm',
			) );
		}
	}
    // --- Organizations (Companies) CRUD --- //

    public function get_organizations( WP_REST_Request $request ) {
        global $wpdb;
        $table = $wpdb->prefix . 'xophz_qb_companies';
        $search = $request->get_param( 'search' );
        
        $where = "WHERE 1=1";
        $params = array();

        if ( ! empty( $search ) ) {
            $where .= " AND (name LIKE %s OR domain LIKE %s OR industry LIKE %s)";
            $like = '%' . $wpdb->esc_like( $search ) . '%';
            $params[] = $like;
            $params[] = $like;
            $params[] = $like;
        }

        $query = "SELECT * FROM {$table} {$where} ORDER BY updated_at DESC";
        $results = empty($params) ? $wpdb->get_results( $query ) : $wpdb->get_results( $wpdb->prepare( $query, $params ) );
        
        $formatted = array();
        foreach ( $results as $org ) {
            $formatted[] = array(
                'id' => (int) $org->id,
                'name' => $org->name,
                'domain' => $org->domain,
                'industry' => $org->industry,
                'created_at' => $org->created_at
            );
        }

        return rest_ensure_response($formatted);
    }

    public function get_organization( WP_REST_Request $request ) {
        global $wpdb;
        $table = $wpdb->prefix . 'xophz_qb_companies';
        $id = (int) $request->get_param('id');
        
        $org = $wpdb->get_row( $wpdb->prepare("SELECT * FROM {$table} WHERE id = %d", $id) );
        if ( ! $org ) return new WP_Error( 'not_found', 'Organization not found', array('status' => 404) );

        return rest_ensure_response(array(
            'id' => (int) $org->id,
            'name' => $org->name,
            'domain' => $org->domain,
            'industry' => $org->industry,
            'created_at' => $org->created_at
        ));
    }

    public function create_organization( WP_REST_Request $request ) {
        global $wpdb;
        $table = $wpdb->prefix . 'xophz_qb_companies';
        $params = $request->get_json_params();
        
        $data = array(
            'name' => sanitize_text_field($params['name'] ?? 'New Organization'),
            'domain' => sanitize_text_field($params['domain'] ?? ''),
            'industry' => sanitize_text_field($params['industry'] ?? ''),
            'created_at' => current_time('mysql'),
            'updated_at' => current_time('mysql')
        );

        $inserted = $wpdb->insert($table, $data);
        if ( ! $inserted ) return new WP_Error( 'insert_failed', 'Could not create organization', array('status' => 500) );

        $req = new WP_REST_Request( 'GET', '/questbook/v1/organizations/' . $wpdb->insert_id );
        $req->set_param( 'id', $wpdb->insert_id );
        return $this->get_organization( $req );
    }

    public function update_organization( WP_REST_Request $request ) {
        global $wpdb;
        $table = $wpdb->prefix . 'xophz_qb_companies';
        $id = (int) $request->get_param('id');
        $params = $request->get_json_params();

        $existing = $wpdb->get_row( $wpdb->prepare("SELECT id FROM {$table} WHERE id = %d", $id) );
        if ( ! $existing ) return new WP_Error( 'not_found', 'Organization not found', array('status' => 404) );

        $data = array( 'updated_at' => current_time('mysql') );
        if ( isset($params['name']) ) $data['name'] = sanitize_text_field($params['name']);
        if ( isset($params['domain']) ) $data['domain'] = sanitize_text_field($params['domain']);
        if ( isset($params['industry']) ) $data['industry'] = sanitize_text_field($params['industry']);

        $wpdb->update($table, $data, array('id' => $id));

        $req = new WP_REST_Request( 'GET', '/questbook/v1/organizations/' . $id );
        $req->set_param( 'id', $id );
        return $this->get_organization( $req );
    }

    public function delete_organization( WP_REST_Request $request ) {
        global $wpdb;
        $table = $wpdb->prefix . 'xophz_qb_companies';
        $id = (int) $request->get_param('id');
        
        $wpdb->delete($table, array('id' => $id));
        return rest_ensure_response(array('deleted' => true, 'id' => $id));
    }

    public function get_organization_contacts( WP_REST_Request $request ) {
        global $wpdb;
        $table = $wpdb->prefix . 'xophz_qb_contacts';
        $id = (int) $request->get_param('id');
        
        $contacts = $wpdb->get_results( $wpdb->prepare("SELECT * FROM {$table} WHERE company_id = %d ORDER BY created_at DESC", $id) );
        
        $formatted = array();
        foreach ( $contacts as $contact ) {
            $formatted[] = $this->format_contact( $contact );
        }

        return rest_ensure_response($formatted);
    }

    /**
     * Skip trace intelligence search endpoint.
     */
    public function skiptrace_query( WP_REST_Request $request ) {
        $params = $request->get_json_params();
        if ( empty( $params ) ) {
            $params = $request->get_params();
        }
        $engine = new Xophz_Compass_Quests_Skiptrace();
        $result = $engine->execute_query( $params );
        if ( is_wp_error( $result ) ) {
            return $result;
        }
        return rest_ensure_response( $result );
    }

    /**
     * 1-Click enrich a Questbook contact.
     */
    public function skiptrace_enrich_contact( WP_REST_Request $request ) {
        $contact_id = (int) $request['id'];
        $params     = $request->get_json_params();
        $purpose    = ! empty( $params['permissiblePurpose'] ) ? sanitize_text_field( $params['permissiblePurpose'] ) : 'legal_due_diligence';

        $engine = new Xophz_Compass_Quests_Skiptrace();
        $result = $engine->enrich_contact( $contact_id, $purpose );
        if ( is_wp_error( $result ) ) {
            return $result;
        }
        return rest_ensure_response( $result );
    }

    /**
     * Get stored skip trace dossier for a contact.
     */
    public function skiptrace_get_contact_dossier( WP_REST_Request $request ) {
        global $wpdb;
        $contact_id = (int) $request['id'];
        $table      = $wpdb->prefix . 'xophz_qb_contacts';
        $contact    = $wpdb->get_row( $wpdb->prepare( "SELECT meta_data FROM {$table} WHERE id = %d", $contact_id ) );

        if ( $contact ) {
            $meta = json_decode( $contact->meta_data, true ) ?: array();
            if ( ! empty( $meta['skiptrace_dossier'] ) ) {
                return rest_ensure_response( array(
                    'hasDossier' => true,
                    'dossier'    => $meta['skiptrace_dossier'],
                    'updatedAt'  => $meta['skiptrace_updated_at'] ?? current_time( 'mysql' ),
                    'confidence' => $meta['skiptrace_confidence'] ?? 95,
                ) );
            }
        }

        $dossier_raw = get_post_meta( $contact_id, '_qb_skiptrace_dossier', true );
        if ( ! empty( $dossier_raw ) ) {
            $dossier = json_decode( $dossier_raw, true );
            return rest_ensure_response( array(
                'hasDossier' => true,
                'dossier'    => $dossier,
                'updatedAt'  => get_post_meta( $contact_id, '_qb_skiptrace_updated_at', true ),
                'confidence' => get_post_meta( $contact_id, '_qb_skiptrace_confidence', true ),
            ) );
        }

        return rest_ensure_response( array(
            'hasDossier' => false,
            'dossier'    => null,
        ) );
    }

    /**
     * Get cryptographic audit trail logs.
     */
    public function skiptrace_get_audit( WP_REST_Request $request ) {
        $engine = new Xophz_Compass_Quests_Skiptrace();
        return rest_ensure_response( $engine->get_audit_trail() );
    }
}
