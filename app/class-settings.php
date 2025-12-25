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

	/**
	 * Register the main setting.
	 */
	public static function register_settings(): void {
		// Register the setting.
		\register_setting(
			Options::OPTION_NAME,
			Options::OPTION_NAME,
			array(
				'sanitize_callback' => array( self::class, 'save' ),
				'show_in_rest'      => false,
			)
		);

		\add_settings_section(
			'ahab_section_speed',
			\__( 'Set speed', 'auto-hide-admin-bar' ),
			array( Render_Settings::class, 'section' ),
			'ahab_plugin'
		);
		\add_settings_section(
			'ahab_section_visual',
			\__( 'Visual options', 'auto-hide-admin-bar' ),
			array( Render_Settings::class, 'section' ),
			'ahab_plugin',
		);
		\add_settings_section(
			'ahab_plugin_section_other',
			\__( 'Other', 'auto-hide-admin-bar' ),
			array( Render_Settings::class, 'section' ),
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
			$valid_values[ $option->get_slug() ] = $option->sanitize( $values[ $option->get_slug() ] );
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
