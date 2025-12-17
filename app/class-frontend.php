<?php
declare( strict_types=1 );
namespace AHAB\App;

/**
 * Class Frontend
 *
 * Handle the rendering of the admin bar on the frontend.
 *
 * @package AHAB\App
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
		if ( Options::get_option_value( 'toggle' ) !== 2 ) {
			return; // The toggle is not enabled.
		}

		$title_aria_label = \esc_attr__( 'Toggle lock for the Admin bar', 'auto-hide-admin-bar' );
		$title_html       = '<div class="ahab"><label class="switch">' .
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
