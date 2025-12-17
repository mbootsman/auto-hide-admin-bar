<?php
declare( strict_types=1 );
namespace AHAB\app;

use WP_Admin_Bar;

/**
 * Class Frontend
 *
 * Handle the rendering of the admin bar on the frontend.
 *
 * @package AHAB\app
 */
class Frontend {

	/**
	 * Add the admin bar lock item.
	 *
	 * @param \WP_Admin_Bar $admin_bar Admin bar object.
	 */
	public static function admin_bar_item( \WP_Admin_Bar $admin_bar ): void {
		if ( \is_admin() ) {
			return; // Do nothing in the wp-admin.
		}
		$ahab_options = \get_option( 'ahab_plugin_options' );
		if ( empty( $ahab_options['toggle'] ) || (int) $ahab_options['toggle'] !== 2 ) {
			return; // The toggle is not enabled.
		}

		$title_aria_label = \esc_attr__( 'Toggle lock for the Admin bar', 'auto-hide-admin-bar' );
		$title_html       = '<div class="ahab"><label class="switch" title="' . $title_aria_label . '">' .
			'<input id="toggle-checkbox" name="ahab_toggle" type="checkbox" aria-label="' . $title_aria_label . '">' .
			'<span class="slider round"></span>' .
			'</label></div>';

		$admin_bar->add_menu(
			array(
				'id'     => 'ahab-toggle',
				'parent' => null,
				'group'  => null,
				'title'  => $title_html,
				'href'   => '',
			)
		);
	}
}
