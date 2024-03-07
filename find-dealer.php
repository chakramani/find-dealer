<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://chakramanijoshi.com.np
 * @since             1.0.0
 * @package           Find_Dealer
 *
 * @wordpress-plugin
 * Plugin Name:       Find Dealer
 * Plugin URI:        https://find-dealer.com
 * Description:       This plugin is used for marking the dealer in the USA on Google Maps.
 * Version:           1.0.0
 * Author:            Chakramani
 * Author URI:        https://chakramanijoshi.com.np/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       find-dealer
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Currently plugin version.
 * Start at version 1.0.0 and use SemVer - https://semver.org
 * Rename this for your plugin and update it as you release new versions.
 */
define( 'FIND_DEALER_VERSION', '1.0.0' );

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-find-dealer-activator.php
 */
function activate_find_dealer() {
	global $wpdb;
    $prefix = $wpdb->prefix;
    $form_db = $prefix . "dealers";
    $charset_collate = $wpdb->get_charset_collate();
    //Check if table exists. In case it's false we create it
    if ($wpdb->get_var("SHOW TABLES LIKE '$form_db'") !== $form_db) {
        
        $sql = "CREATE TABLE " . $form_db . "(
            sales_force_id VARCHAR(30) NOT NULL,
            country_name VARCHAR(50) NULL,
            dealer_name VARCHAR(100) NULL,
            dealer_address VARCHAR(100) NULL,
            email VARCHAR(100) NULL,
            city VARCHAR(100) NULL,
            state VARCHAR(50) NULL,
            zipcode int(15) NULL,
            phone_number int(50) NULL,
            url varchar(100) NULL,
            latitude point NULL,
            longitude point NULL,
            PRIMARY KEY  (sales_force_id)
            ) $charset_collate;";
        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql);
    }
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-find-dealer-activator.php';
	Find_Dealer_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-find-dealer-deactivator.php
 */
function deactivate_find_dealer() {
	// global $wpdb;
    // $table_name = $wpdb->prefix.'dealers';
    // $sql = "DROP TABLE IF EXISTS $table_name";
    // $wpdb->query($sql);
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-find-dealer-deactivator.php';
	Find_Dealer_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_find_dealer' );
register_deactivation_hook( __FILE__, 'deactivate_find_dealer' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-find-dealer.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_find_dealer() {

	$plugin = new Find_Dealer();
	$plugin->run();

}
run_find_dealer();
