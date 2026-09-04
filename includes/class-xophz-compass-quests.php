<?php

/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @link       https://youmeos.com
 * @since      1.0.0
 *
 * @package    Xophz_Compass_Quests
 * @subpackage Xophz_Compass_Quests/includes
 */

/**
 * The core plugin class.
 *
 * This is used to define internationalization, admin-specific hooks, and
 * public-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @since      1.0.0
 * @package    Xophz_Compass_Quests
 * @subpackage Xophz_Compass_Quests/includes
 * @author     Your Name
 */
if ( ! class_exists( 'Xophz_Compass_Plugin_Base' ) ) {
	$core_plugin_base = dirname( dirname( __DIR__ ) ) . '/xophz-compass/includes/core/class-compass-plugin-base.php';
	if ( file_exists( $core_plugin_base ) ) {
		require_once $core_plugin_base;
	}
}

class Xophz_Compass_Quests extends Xophz_Compass_Plugin_Base {

	/**
	 * The loader that's responsible for maintaining and registering all hooks that power
	 * the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      Xophz_Compass_Quests_Loader    $loader    Maintains and registers all hooks for the plugin.
	 */
	protected $loader;

	/**
	 * The unique identifier of this plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $plugin_name    The string used to uniquely identify this plugin.
	 */
	protected $plugin_name;

	/**
	 * The current version of the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $version    The current version of the plugin.
	 */
	protected string $version;

	/**
	 * Define the core functionality of the plugin.
	 *
	 * Set the plugin name and the plugin version that can be used throughout the plugin.
	 * Load the dependencies, define the locale, and set the hooks for the admin area and
	 * the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function __construct( ?string $param1 = null, ?string $version = null, string $param3 = '' ) {
		if ( null === $param1 ) {
			$file = dirname( __DIR__ ) . '/xophz-compass-quests.php';
			$ver  = defined( 'XOPHZ_COMPASS_QUESTS_VERSION' ) ? XOPHZ_COMPASS_QUESTS_VERSION : '1.0.0';
			parent::__construct( $file, $ver, 'xophz-compass-quests' );
		} else {
			parent::__construct( $param1, $version ?? '1.0.0', $param3 );
		}
		$this->plugin_name = $this->text_domain;
		$this->loader = $this;

		$this->load_dependencies();
		$this->set_locale();
		$this->define_admin_hooks();
		$this->define_public_hooks();

	}

	/**
	 * Load the required dependencies for this plugin.
	 *
	 * Include the following files that make up the plugin:
	 *
	 * - Xophz_Compass_Quests_Loader. Orchestrates the hooks of the plugin.
	 * - Xophz_Compass_Quests_i18n. Defines internationalization functionality.
	 * - Xophz_Compass_Quests_Admin. Defines all hooks for the admin area.
	 * - Xophz_Compass_Quests_Public. Defines all hooks for the public side of the site.
	 *
	 * Create an instance of the loader which will be used to register the hooks
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function load_dependencies() {

		/**
		 * The class responsible for orchestrating the actions and filters of the
		 * core plugin.
		 */

		/**
		 * The class responsible for defining internationalization functionality
		 * of the plugin.
		 */

		/**
		 * The class responsible for defining all actions that occur in the admin area.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-xophz-compass-quests-admin.php';

		/**
		 * The class responsible for defining all actions that occur in the public-facing
		 * side of the site.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-xophz-compass-quests-public.php';

		/**
		 * The class responsible for defining all Custom Post Types.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-xophz-compass-quests-cpt.php';

        /**
		 * The class responsible for defining all REST API Endpoints.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-xophz-compass-quests-rest.php';

        /**
		 * The class responsible for WPMU DEV Forminator/Hustle integrations.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-xophz-compass-quests-wpmudev.php';

        /**
		 * The class responsible for Skip Tracing & Contact Enrichment.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-xophz-compass-quests-skiptrace.php';

        /**
		 * The class responsible for Questbook CRM Connectors (Settings -> Connectors).
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-xophz-compass-quests-connectors.php';
		Xophz_Compass_Quests_Connectors::init();

        /**
         * Database schema and migrations.
         */
        require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-xophz-compass-quests-schema.php';
        Xophz_Compass_Quests_Schema::install();


	}

	/**
	 * Define the locale for this plugin for internationalization.
	 *
	 * Uses the Xophz_Compass_Quests_i18n class in order to set the domain and to register the hook
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function set_locale() {
		// Localization handled by Xophz_Compass_Plugin_Base on init priority 5
	}

	/**
	 * Register all of the hooks related to the admin area functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_admin_hooks() {

		$plugin_admin = new Xophz_Compass_Quests_Admin( $this->get_xophz_compass_quests(), $this->get_version() );
        $plugin_cpt = new Xophz_Compass_Quests_CPT();
        $plugin_rest = new Xophz_Compass_Quests_REST();
        $plugin_wpmudev = new Xophz_Compass_Quests_WPMUDEV();

		// $this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_styles' );
		// $this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts' );
		$this->loader->add_action( 'admin_menu', $plugin_admin, 'addToMenu' );
        
        $this->loader->add_action( 'init', $plugin_cpt, 'register_cpts' );
        $plugin_rest->register_routes();
        $plugin_wpmudev->init_hooks();

		$this->loader->add_action( 'add_meta_boxes', $plugin_admin, 'add_questbook_assignment_meta_box', 10, 2 );
		$this->loader->add_action( 'save_post', $plugin_admin, 'save_questbook_assignment_meta', 10, 2 );

	
        // Gamification / XP Hook
		$this->loader->add_action( 'questbook_quest_completed', $plugin_cpt, 'handle_quest_completion', 10, 3 );
        $this->loader->add_action( 'xophz_compass_goal_won', $plugin_cpt, 'handle_goal_won', 10, 3 );
	}

	/**
	 * Register all of the hooks related to the public-facing functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_public_hooks() {

		$plugin_public = new Xophz_Compass_Quests_Public( $this->get_xophz_compass_quests(), $this->get_version() );

		$this->loader->add_action( 'user_register', $this, 'sync_new_wp_user' );

	}

	public function sync_new_wp_user( $user_id ) {
		global $wpdb;
		$contacts_table = $wpdb->prefix . 'xophz_qb_contacts';
		$user = get_userdata( $user_id );
		if ( ! $user ) return;

		$existing = $wpdb->get_var( $wpdb->prepare(
			"SELECT id FROM {$contacts_table} WHERE wp_user_id = %d OR email = %s",
			$user_id,
			$user->user_email
		) );

		if ( ! $existing ) {
			$wp_first = get_user_meta( $user_id, 'first_name', true );
			$wp_last  = get_user_meta( $user_id, 'last_name', true );

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

			$wpdb->insert(
				$contacts_table,
				array(
					'wp_user_id'  => $user_id,
					'first_name'  => $wp_first,
					'last_name'   => $wp_last,
					'email'       => $user->user_email,
					'lead_status' => 'Customer',
					'source'      => 'WP User',
					'created_at'  => current_time( 'mysql' ),
					'updated_at'  => current_time( 'mysql' ),
				)
			);
		}
	}

	/**
	 * Run the loader to execute all of the hooks with WordPress.
	 *
	 * @since    1.0.0
	 */
	public function run(): void {
		$this->run_hooks();
	}

	/**
	 * The name of the plugin used to uniquely identify it within the context of
	 * WordPress and to define internationalization functionality.
	 *
	 * @since     1.0.0
	 * @return    string    The name of the plugin.
	 */
	public function get_xophz_compass_quests() {
		return $this->plugin_name;
	}

	/**
	 * The reference to the class that orchestrates the hooks with the plugin.
	 *
	 * @since     1.0.0
	 * @return    Xophz_Compass_Quests_Loader    Orchestrates the hooks of the plugin.
	 */
	public function get_loader(): self {
		return $this;
	}

	/**
	 * Retrieve the version number of the plugin.
	 *
	 * @since     1.0.0
	 * @return    string    The version number of the plugin.
	 */
	public function get_version(): string {
		return $this->version;
	}

}
