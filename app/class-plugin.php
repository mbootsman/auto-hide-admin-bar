<?php
declare(strict_types=1);

namespace AHAB\App;

/**
 * Class Plugin
 *
 * Handle internal plugin functionality.
 * Like translations, activation, deactivation, settings links.
 * Functionality that is linked to WordPress and plugin itself.
 *
 * @package AHAB\App
 */
class Plugin {

	/**
	 * Autoload classes.
	 *
	 * @param string $class_name The class name to autoload.
	 */
	public static function autoloader( string $class_name ): void {
		if ( ! \str_starts_with( $class_name, __NAMESPACE__ ) ) {
			return; // Not in the plugin namespace, don't check.
		} elseif ( \str_starts_with( $class_name, __NAMESPACE__ . '\Vendor' ) ) {
			return; // 3rd party, prefixed class, composer autoloaders should handle these.
		}
		// lowercase, Remove NAMESPACE, Replace \\ → /   _ → - .
		$class_path = \strtolower( \str_replace( array( __NAMESPACE__, '\\', '_' ), array( '', \DIRECTORY_SEPARATOR, '-' ), $class_name ) );
		$class_path = AHAB_DIR . 'app' . \dirname( $class_path ) . \DIRECTORY_SEPARATOR . 'class-' . \basename( $class_path ) . '.php';
		if ( \file_exists( $class_path ) ) {
			require_once $class_path;
			return;
		}
		$trait_path = \str_replace( 'class-', 'trait-', $class_path );
		if ( \file_exists( $trait_path ) ) {
			require_once $trait_path;
			return;
		}
		\wp_die( "<h1>Can't find class</h1><pre><code>Class: {$class_name}<br/>Path: {$class_path}</code></pre>" ); // phpcs:ignore WordPress.Security.EscapeOutput
	}

	/**
	 * Add a settings link to the the plugin on the plugin page
	 *
	 * @param array $links An array of plugin action links.
	 */
	public static function settings_link( array $links ): array {
		$href          = \admin_url( 'options-general.php?page=' . AHAB_SLUG );
		$settings_link = '<a href="' . $href . '">' . \__( 'Settings' ) . '</a>'; // phpcs:ignore WordPress.WP.I18n.MissingArgDomain
		\array_unshift( $links, $settings_link );

		return $links;
	}

	/**
	 * Override w.org updates for Git-updater.
	 *
	 * This is just addapted when the code rewrite was done.
	 * And is untested.
	 *
	 * @param array $overrides Existing overrides.
	 */
	public static function override_dot_org( array $overrides ): array {
		return \array_merge(
			$overrides,
			array(
				AHAB_SLUG . '/' . AHAB_SLUG . '.php',
			)
		);
	}

	/**
	 * Load the translations for the plugin.
	 */
	public static function load_textdomain(): void {
		\load_plugin_textdomain( AHAB_SLUG, false, \plugin_basename( AHAB_DIR ) . '/languages/' );
	}

	/**
	 * Run when the plugin is uninstalled. Remove all traces of this plugin.
	 */
	public static function uninstall(): void {
		\delete_option( Options::OPTION_NAME );
	}
}
