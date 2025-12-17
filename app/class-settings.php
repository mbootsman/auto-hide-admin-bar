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
class Settings {

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
				'sanitize_callback' => array( self::class, 'save' ),
				'show_in_rest'      => false,
				'default'           => array(), // @TODO fill with the defaults.
			)
		);

		\add_settings_section(
			'ahab_section_speed',
			\__( 'Set speed', 'auto-hide-admin-bar' ),
			array( Render_Settings::class, 'render_section' ),
			'ahab_plugin'
		);
		\add_settings_section(
			'ahab_section_visual',
			\__( 'Visual options', 'auto-hide-admin-bar' ),
			array( Render_Settings::class, 'render_section' ),
			'ahab_plugin',
			array(
				'description' => \__( 'Use this to set visual options, show an arrow to trigger the showing/hiding of the Toolbar, or add a toggle to temporarily stop the Toolbar from hiding.', 'auto-hide-admin-bar' ),
			)
		);
		\add_settings_section(
			'ahab_plugin_section_other',
			\__( 'Other', 'auto-hide-admin-bar' ),
			array( Render_Settings::class, 'render_section' ),
			'ahab_plugin'
		);

		foreach ( Options::get_options() as $option ) {
			if ( $option->get_render_callback() === null || $option->get_render_section() === null ) {
				continue; // There is no render for this option.
			}
			\add_settings_field(
				'ahab_plugin_option_' . $option->get_slug(),
				$option->get_render_title() ?? '',
				$option->get_render_callback(),
				'ahab_plugin',
				$option->get_render_section(),
				array(
					'option'    => $option,
					'label_for' => 'ahab_setting_' . $option->get_slug(),
				)
			);
		}
	}

	/**
	 * Validate and save the values.
	 *
	 * @param mixed $values The values that are send to be saved.
	 */
	public static function save( $values ): array {
		if ( ! \is_array( $values ) ) {
			$values = array();
		}

		$valid_values = array(
			'version' => AHAB_VERSION,
		);
		$options      = Options::get_options();
		foreach ( $options as $option ) {
			if ( ! isset( $values[ $option->get_slug() ] ) ) {
				// No value saved, use the default.
				$valid_values[ $option->get_slug() ] = $option->get_default_value();
				continue;
			}

			$sanitize_callback = $option->get_sanitize_callback();
			if ( \is_callable( $sanitize_callback ) ) {
				$valid_values[ $option->get_slug() ] = \call_user_func( $sanitize_callback, $values[ $option->get_slug() ] );
			} else {
				$valid_values[ $option->get_slug() ] = $values[ $option->get_slug() ];
			}
		}
		/**
		 * Allow filtering of the values.
		 *
		 * @param array $valid_values The validated values.
		 * @param array $values The original values.
		 */
		return \apply_filters( 'ahab_validate_input', $valid_values, $values );
	}
}
