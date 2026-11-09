<?php
declare(strict_types=1);

/**
 * Plugin Name:       Cooper Bold Logo Wrangler
 * Plugin URI:        https://github.com/CooperBold/cooper-bold-logo-wrangler
 * Description:       Display client and partner logos in a balanced strip. Normalization powered by the open-source Sanity Labs logo-soup library.
 * Version:           1.5.0
 * Requires at least: 6.4
 * Requires PHP:      7.4
 * Author:            Cooper Bold
 * Author URI:        https://cooperbold.com
 * License:           GPL-2.0-or-later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       cooper-bold-logo-wrangler
 *
 * @package CooperBoldLogoWrangler
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Never load a second copy of the plugin (for example, an old copy left in another folder).
if ( defined( 'COOPBOLO_FILE' ) ) {
	return;
}

define( 'COOPBOLO_VERSION', '1.5.0' );
define( 'COOPBOLO_FILE', __FILE__ );
define( 'COOPBOLO_PATH', plugin_dir_path( __FILE__ ) );
define( 'COOPBOLO_URL', plugin_dir_url( __FILE__ ) );

/**
 * Clear the collections schema version when the plugin is deactivated, so a later
 * reactivation re-runs the migration (needed if an older version ran in between).
 *
 * @return void
 */
function coopbolo_deactivate(): void {
	if ( class_exists( 'COOPBOLO_Collections' ) ) {
		delete_option( COOPBOLO_Collections::SCHEMA_OPTION );
	}
}

if ( version_compare( PHP_VERSION, '7.4', '<' ) ) {
	/**
	 * Admin notice when PHP is too old (includes are not loaded).
	 *
	 * @return void
	 */
	function coopbolo_php_version_notice(): void {
		if ( ! current_user_can( 'activate_plugins' ) ) {
			return;
		}
		echo '<div class="notice notice-error"><p>';
		echo esc_html__(
			'Cooper Bold Logo Wrangler requires PHP 7.4 or newer.',
			'cooper-bold-logo-wrangler'
		);
		echo '</p></div>';
	}
	add_action( 'admin_notices', 'coopbolo_php_version_notice' );

	/**
	 * Refuse activation on PHP below 7.4.
	 *
	 * @return void
	 */
	function coopbolo_activate_legacy(): void {
		deactivate_plugins( plugin_basename( COOPBOLO_FILE ) );
		wp_die(
			esc_html__(
				'Cooper Bold Logo Wrangler requires PHP 7.4 or newer.',
				'cooper-bold-logo-wrangler'
			),
			esc_html__( 'Plugin activation failed', 'cooper-bold-logo-wrangler' ),
			array( 'back_link' => true )
		);
	}
	register_activation_hook( COOPBOLO_FILE, 'coopbolo_activate_legacy' );
	return;
}

require_once COOPBOLO_PATH . 'includes/class-coopbolo-plugin.php';

/**
 * Bootstrap the plugin singleton.
 *
 * @return void
 */
function coopbolo_init(): void {
	COOPBOLO_Plugin::instance();
}
add_action( 'plugins_loaded', 'coopbolo_init' );

/**
 * Refuse activation on PHP below 7.4.
 *
 * @return void
 */
function coopbolo_activate(): void {
	if ( version_compare( PHP_VERSION, '7.4', '<' ) ) {
		deactivate_plugins( plugin_basename( COOPBOLO_FILE ) );
		wp_die(
			esc_html__(
				'Cooper Bold Logo Wrangler requires PHP 7.4 or newer.',
				'cooper-bold-logo-wrangler'
			),
			esc_html__( 'Plugin activation failed', 'cooper-bold-logo-wrangler' ),
			array( 'back_link' => true )
		);
	}
}
register_activation_hook( COOPBOLO_FILE, 'coopbolo_activate' );
register_deactivation_hook( COOPBOLO_FILE, 'coopbolo_deactivate' );
