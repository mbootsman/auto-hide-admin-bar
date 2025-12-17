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
				Settings::DEFAULT_SPEED,
				array( Sanitize::class, 'number' ),
				array( Render_Settings::class, 'render_input_number' ),
				'ahab_section_speed',
				\__( 'Animation speed:', 'auto-hide-admin-bar' ),
				\__( 'This option allows you to set the animation speed of the hiding/unhiding process. If a non-number is provided, the default value will be used. Provide a number in milliseconds. Default is: 200', 'auto-hide-admin-bar' ),
			),
			'delay'              => new Option(
				'delay',
				Settings::DEFAULT_DELAY,
				array( Sanitize::class, 'number' ),
				array( Render_Settings::class, 'render_input_number' ),
				'ahab_section_speed',
				\__( 'Delay:', 'auto-hide-admin-bar' ),
				\__( 'This option allows you to set the delay of the hiding process. This makes sure your Toolbar doesn\'t go haywire when moving quickly in the top of your site. If a non-number is provided, the default value will be used. Provide a number in milliseconds. Default is: 1500', 'auto-hide-admin-bar' ),
			),
			'interval'           => new Option(
				'interval',
				Settings::DEFAULT_INTERVAL,
				array( Sanitize::class, 'number' ),
				array( Render_Settings::class, 'render_input_number' ),
				'ahab_section_speed',
				\__( 'Interval:', 'auto-hide-admin-bar' ),
				\__( 'The number of milliseconds Auto Hide Admin Bar waits between reading/comparing mouse coordinates. When the user\'s mouse first enters the element its coordinates are recorded. Setting the polling interval higher will increase the delay before the Toolbar gets hidden. If a non-number is provided, the default value will be used. Provide a number in milliseconds. Default is: 100', 'auto-hide-admin-bar' ),
			),
			'toggle'             => new Option(
				'toggle',
				Settings::DEFAULT_TOGGLE,
				array( Sanitize::class, 'radio' ),
				array( Render_Settings::class, 'render_input_radio' ),
				'ahab_section_visual',
				\__( 'Show or hide the toggle button:', 'auto-hide-admin-bar' ),
				\__( 'Enable or disable the display of a toggle button that allows users to temporarily prevent the admin bar from hiding.', 'auto-hide-admin-bar' ),
				array(
					1 => \__( 'Hide toggle button for locking the admin bar', 'auto-hide-admin-bar' ),
					2 => \__( 'Show toggle button for locking the admin bar', 'auto-hide-admin-bar' ),
				)
			),
			'arrow'              => new Option(
				'arrow',
				Settings::DEFAULT_ARROW,
				array( Sanitize::class, 'radio' ),
				array( Render_Settings::class, 'render_input_radio' ),
				'ahab_section_visual',
				\__( 'Show or hide an arrow:', 'auto-hide-admin-bar' ),
				null,
				array(
					1 => \__( 'No arrow', 'auto-hide-admin-bar' ),
					2 => \__( 'Show an arrow', 'auto-hide-admin-bar' ),
				)
			),
			'arrow_position'     => new Option(
				'arrow_position',
				Settings::DEFAULT_ARROW_POS,
				array( Sanitize::class, 'radio' ),
				array( Render_Settings::class, 'render_input_radio' ),
				'ahab_section_visual',
				\__( 'Arrow position:', 'auto-hide-admin-bar' ),
				null,
				array(
					'left'  => \__( 'Left', 'auto-hide-admin-bar' ),
					'right' => \__( 'Right', 'auto-hide-admin-bar' ),
				)
			),
			'mobile'             => new Option(
				'mobile',
				Settings::DEFAULT_MOBILE,
				array( Sanitize::class, 'radio' ),
				array( Render_Settings::class, 'render_input_radio' ),
				'ahab_plugin_section_other',
				\__( 'Show or hide on small screens:', 'auto-hide-admin-bar' ),
				\__(
					'This option allows you to enable or disable the plugin, when on small screens (< 782px). The
    Default is "Hide the Toolbar". The behaviour of the Toolbar in larger screens will not be affected by this option.', // This weird linebreak is needed for translations.
					'auto-hide-admin-bar'
				),
				array(
					1 => \__( 'Hide the Toolbar', 'auto-hide-admin-bar' ),
					2 => \__( 'Always show the Toolbar', 'auto-hide-admin-bar' ),
				)
			),
			'roles'              => new Option(
				'roles',
				array(),
				array( Sanitize::class, 'checkbox' ),
				array( Render_Settings::class, 'render_checkboxes' ),
				'ahab_plugin_section_other',
				\__( 'Disable for user role:', 'auto-hide-admin-bar' ),
				null,
				self::get_formatted_roles()
			),
			'shortcut_mod'       => new Option(
				'shortcut_mod',
				array(),
				array( Sanitize::class, 'checkbox' ),
				array( Render_Settings::class, 'render_checkboxes_shortcut' ),
				'ahab_plugin_section_other',
				\__( 'Set keyboard shortcut to:', 'auto-hide-admin-bar' ),
				\__( 'Set a keyboard shortcut to hide/show the Toolbar', 'auto-hide-admin-bar' ),
				array(
					'ctrl'  => 'Ctrl',
					'alt'   => 'Alt',
					'shift' => 'Shift',
				)
			),
			'shortcut_character' => new Option(
				'shortcut_character',
				'', // @todo
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
}
