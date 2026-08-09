<?php

/**
 * Handles the installation and database schema upgrades for Questbook CRM.
 * 
 * Uses dbDelta to ensure database tables are created and updated properly
 * without overwriting existing data.
 */
class Xophz_Compass_Quests_Schema {

    /**
     * The database version, used to determine if an upgrade is needed.
     * Increment this version whenever the schema changes.
     */
    const DB_VERSION = '1.0.1';

    /**
     * Run the schema installation/upgrade process.
     */
    public static function install() {
        global $wpdb;
        
        $installed_ver = get_option('xophz_qb_db_version');

        if ($installed_ver !== self::DB_VERSION) {
            self::create_tables();
            update_option('xophz_qb_db_version', self::DB_VERSION);
        }
    }

    /**
     * Define and create the Custom Tables for Questbook CRM.
     */
    public static function create_tables() {
        global $wpdb;

        $charset_collate = $wpdb->get_charset_collate();

        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');

        // Contacts Table
        $table_contacts = $wpdb->prefix . 'xophz_qb_contacts';
        $sql_contacts = "CREATE TABLE $table_contacts (
            id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
            wp_user_id bigint(20) unsigned DEFAULT 0,
            first_name varchar(100) DEFAULT '' NOT NULL,
            last_name varchar(100) DEFAULT '' NOT NULL,
            email varchar(100) DEFAULT '' NOT NULL,
            phone varchar(50) DEFAULT '' NOT NULL,
            company varchar(255) DEFAULT '' NOT NULL,
            source varchar(100) DEFAULT '' NOT NULL,
            lead_status varchar(50) DEFAULT 'New Lead' NOT NULL,
            company_id bigint(20) unsigned DEFAULT 0,
            assigned_to bigint(20) unsigned DEFAULT 0,
            meta_data longtext NULL,
            created_at datetime DEFAULT CURRENT_TIMESTAMP NOT NULL,
            updated_at datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP NOT NULL,
            PRIMARY KEY  (id),
            KEY email (email),
            KEY wp_user_id (wp_user_id)
        ) $charset_collate;";
        dbDelta($sql_contacts);

        // Companies Table
        $table_companies = $wpdb->prefix . 'xophz_qb_companies';
        $sql_companies = "CREATE TABLE $table_companies (
            id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
            name varchar(255) DEFAULT '' NOT NULL,
            domain varchar(255) DEFAULT '' NOT NULL,
            industry varchar(100) DEFAULT '' NOT NULL,
            created_at datetime DEFAULT CURRENT_TIMESTAMP NOT NULL,
            updated_at datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP NOT NULL,
            PRIMARY KEY  (id),
            KEY domain (domain)
        ) $charset_collate;";
        dbDelta($sql_companies);

        // Deals (Pipelines) Table
        $table_deals = $wpdb->prefix . 'xophz_qb_deals';
        $sql_deals = "CREATE TABLE $table_deals (
            id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
            title varchar(255) DEFAULT '' NOT NULL,
            contact_id bigint(20) unsigned DEFAULT 0,
            company_id bigint(20) unsigned DEFAULT 0,
            amount decimal(15,2) DEFAULT 0.00 NOT NULL,
            stage varchar(50) DEFAULT 'New' NOT NULL,
            description text NULL,
            assigned_to bigint(20) unsigned DEFAULT 0,
            meta_data longtext NULL,
            created_at datetime DEFAULT CURRENT_TIMESTAMP NOT NULL,
            updated_at datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP NOT NULL,
            PRIMARY KEY  (id),
            KEY contact_id (contact_id),
            KEY company_id (company_id)
        ) $charset_collate;";
        dbDelta($sql_deals);

        // Tasks Table
        $table_tasks = $wpdb->prefix . 'xophz_qb_tasks';
        $sql_tasks = "CREATE TABLE $table_tasks (
            id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
            title varchar(255) DEFAULT '' NOT NULL,
            contact_id bigint(20) unsigned DEFAULT 0,
            deal_id bigint(20) unsigned DEFAULT 0,
            status varchar(50) DEFAULT 'Pending' NOT NULL,
            due_date datetime NULL,
            assigned_to bigint(20) unsigned DEFAULT 0,
            created_at datetime DEFAULT CURRENT_TIMESTAMP NOT NULL,
            updated_at datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP NOT NULL,
            PRIMARY KEY  (id),
            KEY contact_id (contact_id),
            KEY deal_id (deal_id),
            KEY assigned_to (assigned_to)
        ) $charset_collate;";
        dbDelta($sql_tasks);

        // Activity Logs Table
        $table_logs = $wpdb->prefix . 'xophz_qb_logs';
        $sql_logs = "CREATE TABLE $table_logs (
            id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
            contact_id bigint(20) unsigned DEFAULT 0,
            action_type varchar(100) DEFAULT '' NOT NULL,
            description text NOT NULL,
            meta_data longtext NULL,
            created_by bigint(20) unsigned DEFAULT 0,
            created_at datetime DEFAULT CURRENT_TIMESTAMP NOT NULL,
            PRIMARY KEY  (id),
            KEY contact_id (contact_id)
        ) $charset_collate;";
        dbDelta($sql_logs);
    }
}
