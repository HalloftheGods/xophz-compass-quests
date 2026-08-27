<?php
/**
 * Register custom Connectors for Questbook CRM Skip Tracing and Contact Intelligence.
 *
 * Hooking into the WP 7.0+ wp_connectors_init action to register API keys
 * into the centralized Settings -> Connectors UI specifically for Questbook CRM.
 *
 * @package    Xophz_Compass_Quests
 * @subpackage Xophz_Compass_Quests/includes
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Xophz_Compass_Quests_Connectors {

	/**
	 * Initialize connector hooks.
	 */
	public static function init() {
		add_action( 'wp_connectors_init', array( __CLASS__, 'register_connectors' ) );
	}

	/**
	 * Register Questbook CRM environment variables and API connectors.
	 *
	 * @param WP_Connector_Registry $registry The connector registry instance.
	 */
	public static function register_connectors( $registry ) {
		if ( ! is_object( $registry ) || ! method_exists( $registry, 'register' ) ) {
			return;
		}

		// ---------------------------------------------------------
		// SmartyStreets / Smarty (USPS CASS Address Verification)
		// ---------------------------------------------------------
		$registry->register( 'questbook_smarty_auth_id', array(
			'name'           => __( 'Smarty Auth ID', 'xophz-compass-quests' ),
			'description'    => __( 'Smarty (SmartyStreets) Auth ID for live USPS CASS address standardization.', 'xophz-compass-quests' ),
			'type'           => 'intelligence',
			'authentication' => array(
				'method'          => 'api_key',
				'credentials_url' => 'https://www.smarty.com/docs/cloud/authentication',
				'setting_name'    => 'questbook_smarty_auth_id',
			),
		) );

		$registry->register( 'questbook_smarty_auth_token', array(
			'name'           => __( 'Smarty Auth Token', 'xophz-compass-quests' ),
			'description'    => __( 'Smarty Auth Token for live USPS CASS address verification.', 'xophz-compass-quests' ),
			'type'           => 'intelligence',
			'authentication' => array(
				'method'          => 'api_key',
				'credentials_url' => 'https://www.smarty.com/docs/cloud/authentication',
				'setting_name'    => 'questbook_smarty_auth_token',
			),
		) );

		// ---------------------------------------------------------
		// Hunter.io (Email Deliverability & Domain Intelligence)
		// ---------------------------------------------------------
		$registry->register( 'questbook_hunter_api_key', array(
			'name'           => __( 'Hunter.io API Key', 'xophz-compass-quests' ),
			'description'    => __( 'Hunter.io API key for professional email verification and domain search.', 'xophz-compass-quests' ),
			'type'           => 'intelligence',
			'authentication' => array(
				'method'          => 'api_key',
				'credentials_url' => 'https://hunter.io/api-keys',
				'setting_name'    => 'questbook_hunter_api_key',
			),
		) );

		// ---------------------------------------------------------
		// ZeroBounce (Email Validation & Scoring)
		// ---------------------------------------------------------
		$registry->register( 'questbook_zerobounce_api_key', array(
			'name'           => __( 'ZeroBounce API Key', 'xophz-compass-quests' ),
			'description'    => __( 'ZeroBounce API key for live SMTP handshake verification and spam trap scoring.', 'xophz-compass-quests' ),
			'type'           => 'intelligence',
			'authentication' => array(
				'method'          => 'api_key',
				'credentials_url' => 'https://www.zerobounce.net/members/api-keys/',
				'setting_name'    => 'questbook_zerobounce_api_key',
			),
		) );

		// ---------------------------------------------------------
		// Twilio Lookup (Carrier, Line Type, & Caller ID)
		// ---------------------------------------------------------
		$registry->register( 'questbook_twilio_lookup_sid', array(
			'name'           => __( 'Twilio Lookup Account SID', 'xophz-compass-quests' ),
			'description'    => __( 'Twilio Account SID for carrier line-type and CNAM caller-id enrichment.', 'xophz-compass-quests' ),
			'type'           => 'communication',
			'authentication' => array(
				'method'          => 'api_key',
				'credentials_url' => 'https://console.twilio.com/',
				'setting_name'    => 'questbook_twilio_lookup_sid',
			),
		) );

		$registry->register( 'questbook_twilio_lookup_token', array(
			'name'           => __( 'Twilio Lookup Auth Token', 'xophz-compass-quests' ),
			'description'    => __( 'Twilio Auth Token for live phone carrier lookup.', 'xophz-compass-quests' ),
			'type'           => 'communication',
			'authentication' => array(
				'method'          => 'api_key',
				'credentials_url' => 'https://console.twilio.com/',
				'setting_name'    => 'questbook_twilio_lookup_token',
			),
		) );

		// ---------------------------------------------------------
		// Whitepages Pro / Enformion (Public Records & Identity)
		// ---------------------------------------------------------
		$registry->register( 'questbook_whitepages_api_key', array(
			'name'           => __( 'Whitepages Pro API Key', 'xophz-compass-quests' ),
			'description'    => __( 'Whitepages Pro API key for identity verification and public records correlation.', 'xophz-compass-quests' ),
			'type'           => 'intelligence',
			'authentication' => array(
				'method'          => 'api_key',
				'credentials_url' => 'https://pro.whitepages.com/',
				'setting_name'    => 'questbook_whitepages_api_key',
			),
		) );
	}

	/**
	 * Helper to get a connector value (checks option, env, and COMPASS globals).
	 *
	 * @param string $key Connector key.
	 * @param string $default Default value.
	 * @return string
	 */
	public static function get( $key, $default = '' ) {
		$val = get_option( $key, '' );
		if ( ! empty( $val ) ) {
			return $val;
		}

		$env_key = strtoupper( $key );
		$env_val = getenv( $env_key );
		if ( ! empty( $env_val ) ) {
			return $env_val;
		}

		// Check COMPASS parent settings fallback
		$parent_key = str_replace( 'questbook_', 'compass_', $key );
		$parent_val = get_option( $parent_key, '' );
		if ( ! empty( $parent_val ) ) {
			return $parent_val;
		}

		return $default;
	}
}
