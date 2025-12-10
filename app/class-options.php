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

	const OPTION_NAME       = 'ahab_plugin_options';
	const DEFAULT_SPEED     = 200;
	const DEFAULT_DELAY     = 1500;
	const DEFAULT_INTERVAL  = 100;
	const DEFAULT_MOBILE    = 1;
	const DEFAULT_TOGGLE    = 1;
	const DEFAULT_ARROW     = 1;
	const DEFAULT_ARROW_POS = 'left';

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
	}

	/**
	 * Handle the speed section.
	 */
	protected static function section_speed(): void {
		\add_settings_section(
			'ahab_settings_speed',
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
			'ahab_settings_speed',
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
			'ahab_settings_speed',
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
			'ahab_settings_speed',
			array(
				'default'         => self::DEFAULT_INTERVAL,
				'description'     => \__( 'The number of milliseconds Auto Hide Admin Bar waits between reading/comparing mouse coordinates. When the user\'s mouse first enters the element its coordinates are recorded. Setting the polling interval higher will increase the delay before the Toolbar gets hidden. If a non-number is provided, the default value will be used. Provide a number in milliseconds. Default is: 100', 'auto-hide-admin-bar' ),
				'option_name'     => self::OPTION_NAME,
				'sub_option_name' => 'interval',
				'label_for'       => 'ahab_setting_interval',
			)
		);
	}
}
