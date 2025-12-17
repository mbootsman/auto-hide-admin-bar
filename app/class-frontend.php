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

		$title_aria_label = \esc_attr__( 'Toggle lock for the Toolbar', 'auto-hide-admin-bar' );
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

	/**
	 * Enqueue frontend scripts and styles.
	 */
	public static function enqueue_scripts(): void {
		if ( ! self::is_enabled() ) {
			return; // Do nothing.
		}
		\wp_enqueue_style( 'ahab-style', AHAB_URL . 'css/ahab.css', array(), AHAB_VERSION );

		\wp_register_script( 'ahab-jquery-hoverintent', AHAB_URL . 'js/jquery.hoverIntent.minified.js', array( 'jquery' ), AHAB_VERSION, true );
		\wp_enqueue_script( 'ahab', AHAB_URL . 'js/ahab.js', array( 'jquery', 'ahab-jquery-hoverintent', 'jquery-hotkeys' ), AHAB_VERSION, true );
		\wp_add_inline_script( 'ahab', self::localized_js_variables(), 'before' );
	}

	/**
	 * Check if the AHAB is enabled for the current user.
	 */
	protected static function is_enabled(): bool {
		if ( ! \is_user_logged_in() ) {
			return false; // Don't enable for guests.
		}
		$user           = \wp_get_current_user();
		$disabled_roles = Options::get_option_value( 'roles' );
		foreach ( $user->roles as $role ) {
			// Only need one role to match.
			if ( \in_array( $role, (array) $disabled_roles, true ) ) {
				return false;
			}
		}
		return true;
	}

	/**
	 * Get localized JS variables.
	 */
	protected static function localized_js_variables(): string {
		/**
		 * Theme name check - For now only for Twenty Fourteen
		 * because of the fixed header/menu
		 */
		if ( \function_exists( 'wp_get_theme' ) ) {
			$theme_name = ( \wp_get_theme()->get_template() );
		}

		$shortcut_mods      = (array) Options::get_option_value( 'shortcut_mod' );
		$shortcut_character = Options::get_option_value( 'shortcut_character' );
		if ( ! empty( $shortcut_mods ) && ! empty( $shortcut_character ) ) {
			$shortcut_mods[] = $shortcut_character;
		}

		return 'const ahab = ' . \wp_json_encode(
			array(
				'theme_name' => $theme_name ?? '',
				'anim_speed' => Options::get_option_value( 'speed' ),
				'delay'      => Options::get_option_value( 'delay' ),
				'interval'   => Options::get_option_value( 'interval' ),
				'mobile'     => Options::get_option_value( 'mobile' ),
				'arrow'      => Options::get_option_value( 'arrow' ),
				'arrow_pos'  => Options::get_option_value( 'arrow_position' ),
				'shortcut'   => $shortcut_mods,
			)
		) . ';';
	}
}
