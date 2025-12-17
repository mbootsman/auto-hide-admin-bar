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
	 * @param array $args {
	 *     Arguments used to create the settings section.
	 *
	 *     @type int    $default         Default value.
	 *     @type string $description     Description for the field.
	 *     @type string $option_name     The main name of the option.
	 *     @type string $sub_option_name The key of the item within the option array.
	 * }
	 */
	public static function render_input_number( array $args ): void {
		$default         = $args['default'] ?? null;
		$desciption      = $args['description'] ?? null;
		$option_name     = $args['option_name'] ?? '';
		$sub_option_name = $args['sub_option_name'] ?? '';

		$options = \get_option( $option_name );

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

	/**
	 * Render a radio field
	 *
	 * @param array $args {
	 *     Arguments used to create the settings section.
	 *
	 *     @type int    $default         Default value.
	 *     @type string $description     Description for the field.
	 *     @type string $option_name     The main name of the option.
	 *     @type string $sub_option_name The key of the item within the option array.
	 *     @type array  $values          Array of value => label pairs for the radio options.
	 * }
	 */
	public static function render_input_radio( array $args ): void {
		$default         = $args['default'] ?? null;
		$desciption      = $args['description'] ?? null;
		$option_name     = $args['option_name'] ?? '';
		$sub_option_name = $args['sub_option_name'] ?? '';
		$values          = $args['values'] ?? array();

		$options = \get_option( $option_name );

		$current_value = $default;
		if ( ! empty( ( $options[ $sub_option_name ] ) ) ) {
			$current_value = $options[ $sub_option_name ];
		}

		echo '<fieldset>';
		foreach ( $values as $value => $label ) {
			$input_attributes = array(
				'id'    => 'ahab_setting_' . \esc_attr( $sub_option_name ) . '_' . \esc_attr( (string) $value ),
				'name'  => \esc_attr( $option_name ) . '[' . \esc_attr( $sub_option_name ) . ']',
				'type'  => 'radio',
				'value' => \esc_attr( (string) $value ),
			);
			$input_html       = '<input ';
			foreach ( $input_attributes as $attribute => $attr_value ) {
				$input_html .= $attribute . '="' . $attr_value . '" ';
			}
			$input_html .= \checked( $value, $current_value, false );
			$input_html .= '/>';

			$input_html = '<label for="' . \esc_attr( $input_attributes['id'] ) . '">' . $input_html . \esc_html( $label ) . '</label>';

			echo $input_html . '<br/>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		}

		if ( $desciption ) {
			echo '<p class="description">' . \esc_html( $desciption ) . '</p>';
		}
		echo '</fieldset>';
	}

	/**
	 * Render the checkboxes with Roles.
	 *
	 * @param array $args {
	 *     Arguments used to create the settings section.
	 *     @type string $description     Description for the field.
	 *     @type string $option_name     The main name of the option.
	 * }
	 */
	public static function render_roles_checkboxes( array $args ): void {
		$option_name = $args['option_name'] ?? '';
		$desciption  = $args['description'] ?? null;
		$options     = \get_option( $option_name );

		$wp_roles = \wp_roles()->roles;

		echo '<fieldset>';
		foreach ( $wp_roles as $role_key => $role ) {
			$input_attributes = array(
				'name'  => \esc_attr( $option_name ) . '[disabled_user_roles_' . \esc_attr( $role_key ) . ']',
				'type'  => 'checkbox',
				'value' => \esc_attr( (string) $role_key ),
			);
			$input_html       = '<input ';
			foreach ( $input_attributes as $attribute => $attr_value ) {
				$input_html .= $attribute . '="' . $attr_value . '" ';
			}

			$checked = false;
			if ( ! empty( $options[ 'disabled_user_roles_' . $role_key ] ) && $options[ 'disabled_user_roles_' . $role_key ] === $role_key ) {
				$checked = true;
			}
			$input_html .= \checked( true, $checked, false );
			$input_html .= '/>';

			$input_html = '<label>' . $input_html . \esc_html( \translate_user_role( $role['name'] ) ) . '</label>';

			echo $input_html . '<br/>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		}
		if ( $desciption ) {
			echo '<p class="description">' . \esc_html( $desciption ) . '</p>';
		}
		echo '</fieldset>';
	}

	/**
	 * Render the checkboxes.
	 *
	 * @param array $args {
	 *     Arguments used to create the settings section.
	 *     @type string $description     Description for the field.
	 *     @type string $option_name     The main name of the option.
	 * }
	 */
	public static function render_shortcut_checkboxes( array $args ): void {
		$desciption  = $args['description'] ?? null;
		$option_name = $args['option_name'] ?? '';
		$options     = \get_option( $option_name );

		echo '<fieldset>';
		foreach ( array( 'Ctrl', 'Alt', 'Shift' ) as $value ) {
			if ( $value === 'char' ) {
				continue;
			}
			$ahab_keyboard_shortcut_key = '';
			if ( ! empty( $options[ 'keyboard_shortcut_' . $value ] ) ) {
				$ahab_keyboard_shortcut_key = $options[ 'keyboard_shortcut_' . $value ];
			}
			$input_attributes = array(
				'name'  => \esc_attr( $option_name ) . '[keyboard_shortcut_' . \esc_attr( $value ) . ']',
				'type'  => 'checkbox',
				'value' => \esc_attr( $value ),
			);
			$input_html       = '<input ';
			foreach ( $input_attributes as $attribute => $attr_value ) {
				$input_html .= $attribute . '="' . $attr_value . '" ';
			}

			$checked = false;
			if ( ! empty( $options[ 'keyboard_shortcut_' . $value ] ) && $options[ 'keyboard_shortcut_' . $value ] === $value ) {
				$checked = true;
			}
			$input_html .= \checked( true, $checked, false );
			$input_html .= '/>';

			$input_html = '<label>' . $input_html . \esc_html( $value ) . '</label>';

			echo $input_html . '<br/>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		}

		$keyboard_char = '';
		if ( ! empty( $options['keyboard_shortcut_char'] ) ) {
			$keyboard_char = \sanitize_text_field( \substr( $options['keyboard_shortcut_char'], 0, 1 ) );
		}

		$input_html_key = '<input size="2" type="text" maxlength="1" name="ahab_plugin_options[keyboard_shortcut_char]" value="' . \esc_attr( $keyboard_char ) . '"/>';

		// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		echo '<label>' . $input_html_key . ' ' . \esc_html__( ' Character', 'auto-hide-admin-bar' ) . '</label>';
		if ( $desciption ) {
			echo '<p class="description">' . \esc_html( $desciption ) . '</p>';
		}
		echo '</fieldset>';
	}
}
