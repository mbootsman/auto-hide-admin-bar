<?php
declare( strict_types=1 );
namespace AHAB\App;

/**
 * Class Options
 *
 * Define the opions.
 *
 * @package AHAB\App
 */
class Options {

	public const OPTION_NAME = 'ahab_plugin_options';

	/**
	 * Hold the options.
	 *
	 * @var Option[]
	 */
	protected static array $options = array();

	/**
	 * Fill the options.
	 */
	protected static function fill_options(): void {
		self::$options = array(
			'speed'              => new Option(
				'speed',
				200,
				array( Sanitize::class, 'number' ),
				array( Render_Settings::class, 'input_number' ),
				'ahab_section_speed',
				\__( 'Animation speed', 'auto-hide-admin-bar' ),
				\__( 'This option allows you to set the animation speed of the hiding/unhiding process.', 'auto-hide-admin-bar' ),
			),
			'delay'              => new Option(
				'delay',
				1500,
				array( Sanitize::class, 'number' ),
				array( Render_Settings::class, 'input_number' ),
				'ahab_section_speed',
				\__( 'Delay', 'auto-hide-admin-bar' ),
				\__( 'This option allows you to set the delay of the hiding process. This makes sure your Toolbar doesn\'t go haywire when moving quickly in the top of your site.', 'auto-hide-admin-bar' ),
			),
			'interval'           => new Option(
				'interval',
				100,
				array( Sanitize::class, 'number' ),
				array( Render_Settings::class, 'input_number' ),
				'ahab_section_speed',
				\__( 'Interval', 'auto-hide-admin-bar' ),
				\__( 'The number of milliseconds Auto Hide Admin Bar waits between reading/comparing mouse coordinates. When the user\'s mouse first enters the element its coordinates are recorded. Setting the polling interval higher will increase the delay before the Toolbar gets hidden.', 'auto-hide-admin-bar' ),
			),
			'toggle'             => new Option(
				'toggle',
				1,
				array( Sanitize::class, 'radio' ),
				array( Render_Settings::class, 'input_radio' ),
				'ahab_section_visual',
				\__( 'Show or hide the toggle button', 'auto-hide-admin-bar' ),
				null,
				array(
					1 => \__( 'Hide toggle button for locking the Toolbar', 'auto-hide-admin-bar' ),
					2 => \__( 'Show toggle button for locking the Toolbar', 'auto-hide-admin-bar' ),
				)
			),
			'arrow'              => new Option(
				'arrow',
				1,
				array( Sanitize::class, 'radio' ),
				array( Render_Settings::class, 'input_radio' ),
				'ahab_section_visual',
				\__( 'Show or hide an arrow', 'auto-hide-admin-bar' ),
				null,
				array(
					1 => \__( 'No arrow', 'auto-hide-admin-bar' ),
					2 => \__( 'Show an arrow', 'auto-hide-admin-bar' ),
				)
			),
			'arrow_position'     => new Option(
				'arrow_position',
				'left',
				array( Sanitize::class, 'radio' ),
				array( Render_Settings::class, 'input_radio' ),
				'ahab_section_visual',
				\__( 'Arrow position', 'auto-hide-admin-bar' ),
				null,
				array(
					'left'  => \__( 'Left', 'auto-hide-admin-bar' ),
					'right' => \__( 'Right', 'auto-hide-admin-bar' ),
				)
			),
			'mobile'             => new Option(
				'mobile',
				1,
				array( Sanitize::class, 'radio' ),
				array( Render_Settings::class, 'input_radio' ),
				'ahab_section_visual',
				\__( 'Hide on small screens', 'auto-hide-admin-bar' ),
				\__(
					'The behaviour of the Toolbar in larger screens will not be affected by this option.', // This weird linebreak is needed for translations.
					'auto-hide-admin-bar'
				),
				array(
					1 => \__( 'Hide the Toolbar on smaller screens (< 782px)', 'auto-hide-admin-bar' ),
					2 => \__( 'Always show the Toolbar', 'auto-hide-admin-bar' ),
				)
			),
			'roles'              => new Option(
				'roles',
				array(),
				array( Sanitize::class, 'checkbox' ),
				array( Render_Settings::class, 'checkboxes' ),
				'ahab_plugin_section_other',
				\__( 'Always show for roles', 'auto-hide-admin-bar' ),
				\__( 'The Toolbar will always be visable for the selected roles.', 'auto-hide-admin-bar' ),
				self::get_formatted_roles()
			),
			'shortcut_mod'       => new Option(
				'shortcut_mod',
				array(),
				array( Sanitize::class, 'checkbox' ),
				array( Render_Settings::class, 'checkboxes_shortcut' ),
				'ahab_plugin_section_other',
				\__( 'Keyboard shortcut', 'auto-hide-admin-bar' ),
				\__( 'Set a keyboard shortcut to hide/show the Toolbar', 'auto-hide-admin-bar' ),
				array(
					'ctrl'  => 'Ctrl',
					'alt'   => 'Alt',
					'shift' => 'Shift',
				)
			),
			'shortcut_character' => new Option(
				'shortcut_character',
				'',
				array( Sanitize::class, 'keyboard_character' ),
			),
		);
	}

	/**
	 * Get available roles formatted for the allowed option.
	 */
	protected static function get_formatted_roles(): array {
		$roles          = \wp_roles()->roles;
		$formatted_rols = array();
		foreach ( $roles as $role_key => $role ) {
			$formatted_rols[ $role_key ] = \translate_user_role( $role['name'] );
		}
		return $formatted_rols;
	}

	/**
	 * Get the options.
	 *
	 * @return Option[]
	 */
	public static function get_options(): array {
		if ( empty( self::$options ) ) {
			self::fill_options();
			self::patch_option();
		}
		return self::$options;
	}

	/**
	 * Get a singular option.
	 *
	 * @param string $slug The option slug.
	 */
	public static function get_option( string $slug ): Option {
		$options = self::get_options();
		if ( ! isset( $options[ $slug ] ) ) {
			\wp_die( 'Option not found: ' . \esc_html( $slug ) );
		}
		return $options[ $slug ];
	}

	/**
	 * Get the value of a single option.
	 *
	 * @param string $slug The option slug.
	 * @return mixed
	 */
	public static function get_option_value( string $slug ) {
		$option = self::get_option( $slug );
		return $option->get_current_value();
	}

	/**
	 * Check if the option is formatted correctly.
	 */
	protected static function patch_option(): void {
		$current_option = \get_option( self::OPTION_NAME );
		// Keeping track of the plugin version was not done in the past.
		if ( empty( $current_option['version'] ) ) {
			$options           = self::get_options();
			$formattted_option = array(
				'version'        => AHAB_VERSION,
				'speed'          => (int) ( $current_option['speed'] ?? $options['speed']->get_current_value() ),
				'delay'          => (int) ( $current_option['delay'] ?? $options['delay']->get_current_value() ),
				'interval'       => (int) ( $current_option['interval'] ?? $options['interval']->get_current_value() ),
				'toggle'         => (int) ( $current_option['toggle'] ?? $options['toggle']->get_current_value() ),
				'arrow'          => (int) ( $current_option['arrow'] ?? $options['arrow']->get_current_value() ),
				'arrow_position' => $current_option['arrow_pos'] ?? $options['arrow_position']->get_current_value(),
				'mobile'         => (int) ( $current_option['mobile'] ?? $options['mobile']->get_current_value() ),
			);

			// Map shortcut options.
			if ( ! empty( $current_option['keyboard_shortcut_char'] ) ) {
				$formattted_option['shortcut_character'] = $current_option['keyboard_shortcut_char'];
			}
			if ( ! empty( $current_option['keyboard_shortcut_Ctrl'] ) ) {
				$formattted_option['shortcut_mod'][] = 'ctrl';
			}
			if ( ! empty( $current_option['keyboard_shortcut_Alt'] ) ) {
				$formattted_option['shortcut_mod'][] = 'alt';
			}
			if ( ! empty( $current_option['keyboard_shortcut_Shift'] ) ) {
				$formattted_option['shortcut_mod'][] = 'shift';
			}

			// Map roles.
			$roles = \array_keys( self::get_formatted_roles() );
			foreach ( $roles as $role ) {
				if ( ! empty( $current_option[ 'disabled_user_roles_' . $role ] ) ) {
					$formattted_option['roles'][] = $role;
				}
			}

			\update_option( self::OPTION_NAME, $formattted_option, false );

			$test = \get_option( self::OPTION_NAME ); // Refresh the options after patching.
			$test = 1;
		}
	}
}
