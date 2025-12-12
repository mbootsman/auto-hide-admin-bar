<?php
declare( strict_types=1 );
namespace AHAB\App;

/**
 * Class Options
 *
 * Handle the options page for Hide admin bar.
 *
 * @package AHAB\App
 */
class Options {

	public const OPTION_NAME       = 'ahab_plugin_options';
	public const DEFAULT_SPEED     = 200;
	public const DEFAULT_DELAY     = 1500;
	public const DEFAULT_INTERVAL  = 100;
	public const DEFAULT_MOBILE    = 1;
	public const DEFAULT_TOGGLE    = 1;
	public const DEFAULT_ARROW     = 1;
	public const DEFAULT_ARROW_POS = 'left';

	/**
	 * Register the main setting.
	 */
	public static function register_settings(): void {
		// Register the setting.
		\register_setting(
			self::OPTION_NAME,
			self::OPTION_NAME,
			array(
				// @todo sanitize callback.
				'show_in_rest' => false,
				'default'      => array(), // @TODO fill with the defaults.
			)
		);

		self::section_speed();
		self::section_visual();
		self::section_other();
	}

	/**
	 * Handle the speed section.
	 */
	protected static function section_speed(): void {
		\add_settings_section(
			'ahab_section_speed',
			\__( 'Set speed', 'auto-hide-admin-bar' ),
			array( Render_Settings::class, 'render_section' ),
			'ahab_plugin'
		);

		// Add speed setting.
		\add_settings_field(
			'ahab_plugin_option_speed',
			\__( 'Animation speed:', 'auto-hide-admin-bar' ),
			array( Render_Settings::class, 'render_input_number' ),
			'ahab_plugin',
			'ahab_section_speed',
			array(
				'default'         => self::DEFAULT_SPEED,
				'description'     => \__( 'This option allows you to set the animation speed of the hiding/unhiding process. If a non-number is provided, the default value will be used. Provide a number in milliseconds. Default is: 200', 'auto-hide-admin-bar' ),
				'option_name'     => self::OPTION_NAME,
				'sub_option_name' => 'speed',
				'label_for'       => 'ahab_setting_speed',
			)
		);

		// Delay setting.
		\add_settings_field(
			'ahab_plugin_option_delay',
			\__( 'Delay:', 'auto-hide-admin-bar' ),
			array( Render_Settings::class, 'render_input_number' ),
			'ahab_plugin',
			'ahab_section_speed',
			array(
				'default'         => self::DEFAULT_DELAY,
				'description'     => \__( 'This option allows you to set the delay of the hiding process. This makes sure your Toolbar doesn\'t go haywire when moving quickly in the top of your site. If a non-number is provided, the default value will be used. Provide a number in milliseconds. Default is: 1500', 'auto-hide-admin-bar' ),
				'option_name'     => self::OPTION_NAME,
				'sub_option_name' => 'delay',
				'label_for'       => 'ahab_setting_delay',
			)
		);

		// Mouse polling setting.
		\add_settings_field(
			'ahab_plugin_option_interval',
			\__( 'Interval:', 'auto-hide-admin-bar' ),
			array( Render_Settings::class, 'render_input_number' ),
			'ahab_plugin',
			'ahab_section_speed',
			array(
				'default'         => self::DEFAULT_INTERVAL,
				'description'     => \__( 'The number of milliseconds Auto Hide Admin Bar waits between reading/comparing mouse coordinates. When the user\'s mouse first enters the element its coordinates are recorded. Setting the polling interval higher will increase the delay before the Toolbar gets hidden. If a non-number is provided, the default value will be used. Provide a number in milliseconds. Default is: 100', 'auto-hide-admin-bar' ),
				'option_name'     => self::OPTION_NAME,
				'sub_option_name' => 'interval',
				'label_for'       => 'ahab_setting_interval',
			)
		);
	}

	/**
	 * Handle the visuals section
	 */
	protected static function section_visual(): void {
		\add_settings_section(
			'ahab_section_visual',
			\__( 'Visual options', 'auto-hide-admin-bar' ),
			array( Render_Settings::class, 'render_section' ),
			'ahab_plugin',
			array(
				'description' => \__( 'Use this to set visual options, show an arrow to trigger the showing/hiding of the Toolbar, or add a toggle to temporarily stop the Toolbar from hiding.', 'auto-hide-admin-bar' ),
			)
		);
		\add_settings_field(
			'ahab_plugin_toggle_button',
			\__( 'Show or hide the toggle button:', 'auto-hide-admin-bar' ),
			array( Render_Settings::class, 'render_input_radio' ),
			'ahab_plugin',
			'ahab_section_visual',
			array(
				'default'         => self::DEFAULT_TOGGLE,
				'option_name'     => self::OPTION_NAME,
				'sub_option_name' => 'toggle',
				'values'          => array(
					1 => \__( 'Hide toggle button for locking the admin bar', 'auto-hide-admin-bar' ),
					2 => \__( 'Show toggle button for locking the admin bar', 'auto-hide-admin-bar' ),
				),
			)
		);
		\add_settings_field(
			'ahab_plugin_option_arrow',
			\__( 'Show or hide an arrow:', 'auto-hide-admin-bar' ),
			array( Render_Settings::class, 'render_input_radio' ),
			'ahab_plugin',
			'ahab_section_visual',
			array(
				'default'         => self::DEFAULT_ARROW,
				'option_name'     => self::OPTION_NAME,
				'sub_option_name' => 'arrow',
				'values'          => array(
					1 => \__( 'No arrow', 'auto-hide-admin-bar' ),
					2 => \__( 'Show an arrow', 'auto-hide-admin-bar' ),
				),
			)
		);
		\add_settings_field(
			'ahab_plugin_option_arrow_position',
			\__( 'Arrow position:', 'auto-hide-admin-bar' ),
			array( Render_Settings::class, 'render_input_radio' ),
			'ahab_plugin',
			'ahab_section_visual',
			array(
				'default'         => self::DEFAULT_ARROW_POS,
				'option_name'     => self::OPTION_NAME,
				'sub_option_name' => 'arrow_pos',
				'values'          => array(
					'left'  => \__( 'Left', 'auto-hide-admin-bar' ),
					'right' => \__( 'Right', 'auto-hide-admin-bar' ),
				),
			)
		);
	}

	/**
	 * Single settings which don't belong to a group.
	 */
	protected static function section_other(): void {
		\add_settings_section(
			'ahab_plugin_section_other',
			\__( 'Other', 'auto-hide-admin-bar' ),
			array( Render_Settings::class, 'render_section' ),
			'ahab_plugin'
		);

		\add_settings_field(
			'ahab_plugin_option_mobile',
			\__( 'Show or hide on small screens:', 'auto-hide-admin-bar' ),
			array( Render_Settings::class, 'render_input_radio' ),
			'ahab_plugin',
			'ahab_plugin_section_other',
			array(
				'default'         => self::DEFAULT_MOBILE,
				'option_name'     => self::OPTION_NAME,
				'sub_option_name' => 'mobile',
				'values'          => array(
					1 => \__( 'Hide the Toolbar', 'auto-hide-admin-bar' ),
					2 => \__( 'Always show the Toolbar', 'auto-hide-admin-bar' ),
				),
				'description'     => \__(
					'This option allows you to enable or disable the plugin, when on small screens (< 782px). The
    Default is "Hide the Toolbar". The behaviour of the Toolbar in larger screens will not be affected by this option.', // This weird linebreak is needed for translations.
					'auto-hide-admin-bar'
				),
			)
		);

		\add_settings_field(
			'ahab_plugin_option_user_roles',
			\__( 'Disable for user role:', 'auto-hide-admin-bar' ),
			array( Render_Settings::class, 'render_roles_checkboxes' ),
			'ahab_plugin',
			'ahab_plugin_section_other',
			array(
				'option_name' => self::OPTION_NAME,
			)
		);
	}
}
