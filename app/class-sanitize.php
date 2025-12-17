<?php
declare( strict_types=1 );
namespace AHAB\App;

/**
 * Class Sanitize
 *
 * Holds the sanitization functions.
 *
 * @package AHAB\App
 */
class Sanitize {

	/**
	 * Validate numbers
	 *
	 * @param int|string $value The value to sanitize.
	 * @param Option     $option The option being sanitized.
	 */
	public static function number( $value, Option $option ): int {
		$number = \filter_var( $value, \FILTER_VALIDATE_INT );
		if ( ! \is_int( $number ) ) {
			return (int) $option->get_default_value();
		}
		// Kinda ugly; All numbers are miliseconds so limit between 0 and 60seconds.
		if ( $number < 0 || $number > 60000 ) {
			return (int) $option->get_default_value();
		}
		return $number;
	}

	/**
	 * Validate radion options.
	 *
	 * @param int|string $value The value to sanitize.
	 * @param Option     $option The option being sanitized.
	 * @return int|string
	 */
	public static function radio( $value, Option $option ) {
		$allowed_values = $option->get_allowed_values();

		// Cast numberic values to int.
		if ( \is_numeric( $value ) ) {
			$value = (int) $value;
		}

		if ( \array_key_exists( $value, $allowed_values ) ) {
			return $value;
		}
		$default = $option->get_default_value();
		if ( \is_numeric( $default ) ) {
			return (int) $default;
		}
		return (string) $default; // @phpstan-ignore-line
	}

	/**
	 * Validate checkbox options.
	 *
	 * @param array  $values The values to sanitize.
	 * @param Option $option The option being sanitized.
	 */
	public static function checkbox( array $values, Option $option ): array {
		$allowed_values = $option->get_allowed_values();
		$valid_values   = array();

		foreach ( $allowed_values as $key => $label ) {
			if ( \in_array( $key, $values, false ) ) { // phpcs:ignore WordPress.PHP.StrictInArray.FoundNonStrictFalse
				$valid_values[] = $key;
			}
		}

		return $valid_values;
	}

	/**
	 * Validate keyboard character.
	 *
	 * @param string|int $value The values to sanitize.
	 * @param Option     $option The option being sanitized.
	 */
	public static function keyboard_character( $value, Option $option ): string {
		// Should be a single character.
		if ( ! \is_string( $value ) || \strlen( $value ) !== 1 ) {
			return (string) $option->get_default_value();
		}
		return $value; // This is not the best sanitization, but validating for every possible keyboard character is hard.
	}
}
