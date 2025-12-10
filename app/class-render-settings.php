<?php
declare( strict_types=1 );
namespace AHAB\App;

/**
 * Class Class_Render_Settings
 *
 * Render settings.
 *
 * @package AHAB\App
 */
class Render_Settings {

	/**
	 * Render section.
	 *
	 * @param array $args {
	 *     Arguments used to create the settings section.
	 *
	 *     @type string $before_section HTML content to prepend to the section's HTML output.
	 *                                  Receives the section's class name as `%s`. Default empty.
	 *     @type string $after_section  HTML content to append to the section's HTML output. Default empty.
	 *     @type string $section_class  The class name to use for the section. Default empty.
	 *     @type string $description    Text description for the section. Default empty.
	 * }
	 */
	public static function render_section( array $args ): void {
		if ( ! empty( $args['description'] ) ) {
			echo '<p>' . \esc_html( $args['description'] ) . '</p>';
		}
	}

	/**
	 * Render a number input field.
	 *
	 * @param array $args
	 */
	public static function render_input_number( array $args ) {
		$default         = $args['default'] ?? null;
		$desciption      = $args['description'] ?? null;
		$option_name     = $args['option_name'] ?? '';
		$sub_option_name = $args['sub_option_name'] ?? '';

		$options       = \get_option( $option_name );
		$current_value = $default;

		if ( isset( ( $options[ $sub_option_name ] ) ) ) { // 0 is a valid value.
			$current_value = \absint( $options[ $sub_option_name ] );
		}

		$input_attributes = array(
			'id'          => 'ahab_setting_' . \esc_attr( $sub_option_name ),
			'name'        => \esc_attr( $option_name ) . '[' . \esc_attr( $sub_option_name ) . ']',
			'type'        => 'number',
			'value'       => \esc_attr( (string) $current_value ),
			'min'         => '0', // No negative values.
			'max'         => '60000', // 60 seconds max.
			'placeholder' => (string) $default,
		);
		$input_html       = '<input ';
		foreach ( $input_attributes as $attribute => $value ) {
			$input_html .= $attribute . '="' . $value . '" ';
		}
		$input_html .= '/>';
		echo $input_html; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		if ( $desciption ) {
			echo '<p class="description">' . \esc_html( $desciption ) . '</p>';
		}
	}
}
