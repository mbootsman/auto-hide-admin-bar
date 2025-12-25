<?php
/**
 * Plugin Name:       Auto Hide Admin Bar
 * Description:       Automatically hides the Toolbar. Will show the Toolbar when hovering over the top of the site.
 * Author:            Marcel Bootsman
 * Author URI:        https://marcelbootsman.nl
 * Github Plugin URI: https://github.com/mbootsman/auto-hide-admin-bar
 * Primary Branch:    main
 * Text Domain:       auto-hide-admin-bar
 * Domain Path:       /languages/
 * License:           GPLv2
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Requires at least: 3.1
 * Requires PHP:      7.4
 * Version:           1.7.0
 *
 * @package           AHAB
 */

use AHAB\App\Plugin;
use AHAB\App\Admin;
use AHAB\App\Settings;
use AHAB\App\Frontend;

\define( 'AHAB_VERSION', '1.7.0' );
\define( 'AHAB_DIR', \plugin_dir_path( __FILE__ ) ); // Full path with trailing slash.
\define( 'AHAB_URL', \plugin_dir_url( __FILE__ ) ); // With trailing slash.
\define( 'AHAB_SLUG', \basename( __DIR__ ) ); // auto-hide-admin-bar.

if ( ! \defined( 'ABSPATH' ) ) {
	return; // WP not loaded.
}

/**
 * Autoload internal classes.
 */
require_once AHAB_DIR . 'app/class-plugin.php';
\spl_autoload_register( array( Plugin::class, 'autoloader' ) );

\register_uninstall_hook( __FILE__, array( Plugin::class, 'uninstall' ) );


\add_action( 'init', array( Plugin::class, 'load_textdomain' ), 9 );
\add_filter( 'plugin_action_links_' . \plugin_basename( __FILE__ ), array( Plugin::class, 'settings_link' ) );
\add_filter( 'gu_override_dot_org', array( Plugin::class, 'override_dot_org' ) );

// Handle the options page.
\add_action( 'admin_menu', array( Admin::class, 'register_options_page' ) );
\add_action( 'admin_init', array( Settings::class, 'register_settings' ) );

\add_action( 'admin_bar_menu', array( Frontend::class, 'admin_bar_item' ), 0 );
\add_action( 'wp_enqueue_scripts', array( Frontend::class, 'enqueue_scripts' ), 0 );
