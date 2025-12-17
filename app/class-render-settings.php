<?php
declare( strict_types=1 );
namespace AHAB\App;

/**
 * Class Render_Settings
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
	public static function section( array $args ): void {
		if ( ! empty( $args['description'] ) ) {
			echo '<p>' . \esc_html( $args['description'] ) . '</p>';
		}
	}

	/**
	 * Render checkboxes.
	 *
	 * @param array $args {
	 *     Passing on the option object.
	 *     @type Option    $option       The option being rendered.
	 * }
	 */
	public static function input_text( array $args ): void {
		if ( ! isset( $args['option'] ) || ! ( $args['option'] instanceof Option ) ) {
			\wp_die( 'Invalid option provided to ' . __METHOD__ );
		}
		/**
		 * The current option.
		 *
		 * @var Option $option
		 */
		$option = $args['option'];

		$input_html = self::generate_input_tag(
			array(
				'type'        => 'text',
				'id'          => \esc_attr( 'ahab_setting_' . $option->get_slug() ),
				'name'        => \esc_attr( Options::OPTION_NAME ) . '[' . \esc_attr( $option->get_slug() ) . ']',
				'value'       => \esc_attr( (string) $option->get_current_value() ),
				'placeholder' => (string) $option->get_default_value(),
				'maxlength'   => 1, // Ugly, but only used for 1 field.
				'size'        => 1,
			)
		);
		echo $input_html; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		if ( $option->get_render_description() ) {
			echo '<p class="description">' . \esc_html( $option->get_render_description() ) . '</p>';
		}
	}

	/**
	 * Render a number input field.
	 *
	 * @param array $args {
	 *     Passing on the option object.
	 *     @type Option    $option       The option being rendered.
	 * }
	 */
	public static function input_number( array $args ): void {
		if ( ! isset( $args['option'] ) || ! ( $args['option'] instanceof Option ) ) {
			\wp_die( 'Invalid option provided to ' . __METHOD__ );
		}
		/**
		 * The current option.
		 *
		 * @var Option $option
		 */
		$option = $args['option'];

		$input_html = self::generate_input_tag(
			array(
				'type'        => 'number',
				'min'         => '0', // No negative values.
				'max'         => '60000', // 60 seconds max.
				'id'          => \esc_attr( 'ahab_setting_' . $option->get_slug() ),
				'name'        => \esc_attr( Options::OPTION_NAME ) . '[' . \esc_attr( $option->get_slug() ) . ']',
				'value'       => \esc_attr( (string) $option->get_current_value() ),
				'placeholder' => (string) $option->get_default_value(),
			)
		);
		echo $input_html . '<span> ' . \_x( 'miliseconds', 'Displays after the input field', 'auto-hide-admin-bar' ) . '</span>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		if ( $option->get_render_description() ) {
			echo '<p class="description">' . \esc_html( $option->get_render_description() ) . '</p>';
		}
	}

	/**
	 * Render a radio field
	 *
	 * @param array $args {
	 *     Passing on the option object.
	 *     @type Option    $option       The option being rendered.
	 * }
	 */
	public static function input_radio( array $args ): void {
		if ( ! isset( $args['option'] ) || ! ( $args['option'] instanceof Option ) ) {
			\wp_die( 'Invalid option provided to ' . __METHOD__ );
		}
		/**
		 * The current option.
		 *
		 * @var Option $option
		 */
		$option = $args['option'];

		echo '<fieldset>';
		foreach ( $option->get_allowed_values() as $value => $label ) {
			$input_html = self::generate_input_tag(
				array(
					'type'  => 'radio',
					'id'    => \esc_attr( 'ahab_setting_' . $option->get_slug() . '_' . $value ),
					'name'  => \esc_attr( Options::OPTION_NAME ) . '[' . \esc_attr( $option->get_slug() ) . ']',
					'value' => \esc_attr( (string) $value ),
					0       => \checked( $value, $option->get_current_value(), false ),
				),
				\esc_html( $label )
			);
			echo $input_html . '<br/>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		}
		if ( $option->get_render_description() ) {
			echo '<p class="description">' . \esc_html( $option->get_render_description() ) . '</p>';
		}
		echo '</fieldset>';
	}

	/**
	 * Render checkboxes.
	 *
	 * @param array $args {
	 *     Passing on the option object.
	 *     @type Option    $option       The option being rendered.
	 * }
	 */
	public static function checkboxes( array $args ): void {
		if ( ! isset( $args['option'] ) || ! ( $args['option'] instanceof Option ) ) {
			\wp_die( 'Invalid option provided to ' . __METHOD__ );
		}
		/**
		 * The current option.
		 *
		 * @var Option $option
		 */
		$option = $args['option'];
		echo '<fieldset>';
		foreach ( $option->get_allowed_values() as $value => $label ) {
			$checked    = \in_array( $value, (array) $option->get_current_value(), true );
			$input_html = self::generate_input_tag(
				array(
					'type'  => 'checkbox',
					'id'    => \esc_attr( 'ahab_setting_' . $option->get_slug() . '_' . $value ),
					'name'  => \esc_attr( Options::OPTION_NAME ) . '[' . \esc_attr( $option->get_slug() ) . '][]',
					'value' => \esc_attr( (string) $value ),
					0       => \checked( $checked, true, false ),
				),
				\esc_html( $label )
			);
			echo $input_html . '<br/>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		}
		\do_action( 'ahab_render_checkboxes_fieldset', $option );
		if ( $option->get_render_description() ) {
			echo '<p class="description">' . \esc_html( $option->get_render_description() ) . '</p>';
		}
		echo '</fieldset>';
	}


	/**
	 * Render checkboxes.
	 *
	 * @param array $args {
	 *     Passing on the option object.
	 *     @type Option    $option       The option being rendered.
	 * }
	 */
	public static function checkboxes_shortcut( array $args ): void {
		if ( ! isset( $args['option'] ) || ! ( $args['option'] instanceof Option ) ) {
			\wp_die( 'Invalid option provided to ' . __METHOD__ );
		}

		\add_action(
			'ahab_render_checkboxes_fieldset',
			function () {
				$character_option = Options::get_option( 'shortcut_character' );
				self::input_text( array( 'option' => $character_option ) );
			}
		);
		self::checkboxes( $args );
	}

	/**
	 * Render a generic input field.
	 *
	 * @param array   $attributes Attributes for the input field.
	 * @param ?string $label_text Optional label text.
	 */
	protected static function generate_input_tag( array $attributes = array(), string $label_text = null ): string {
		$input_html = '<input ';
		foreach ( $attributes as $attribute => $value ) {
			if ( \is_string( $attribute ) ) {
				$input_html .= $attribute . '="' . $value . '" ';
			} else {
				// Used for unnamed attributes like 'checked'.
				$input_html .= $value . ' ';
			}
		}
		$input_html .= '/>';

		if ( $label_text !== null ) {
			$for_attr = '';
			if ( ! empty( $attributes['id'] ) ) {
				$for_attr = ' for="' . \esc_attr( $attributes['id'] ) . '"';
			}
			$input_html = '<label' . $for_attr . '>' . $input_html . $label_text . '</label>';
		}

		return $input_html;
	}
}
