<?php
declare( strict_types=1 );
namespace AHAB\App;

/**
 * Class Option
 *
 * Regsiter individual Options.
 * phpcs:disable Squiz.Commenting.FunctionComment.Missing
 * phpcs:disable Squiz.Commenting.VariableComment.MissingVar
 *
 * @package AHAB\App
 */
class Option {

	/**
	 * Option slug / name.
	 */
	protected string $slug;

	/**
	 * Default value for the option.
	 *
	 * @var mixed
	 */
	protected $default_value;

	/**
	 * Callback used to sanitize the value on save.
	 *
	 * @var ?callable
	 */
	protected $sanitize_callback;

	/**
	 * Callback used to render the input field.
	 *
	 * @var ?callable
	 */
	protected $render_callback;

	/**
	 * Section where this option is rendered.
	 */
	protected ?string $render_section = null;

	/**
	 * Title shown for this option in the UI.
	 */
	protected ?string $render_title = null;

	/**
	 * Optional description for the option.
	 */
	protected ?string $render_description = null;

	/**
	 * Allowed values for the option (if applicable).
	 */
	protected array $allowed_values = array();

	/**
	 * Register a sub option of AHAB
	 *
	 * @param string    $slug The option slug.
	 * @param mixed     $default_value The default value.
	 * @param ?callable $sanitize_callback Validate the values on save.
	 * @param ?callable $render_callback Render the input field.
	 * @param ?string   $render_section The section where this option is rendered.
	 * @param ?string   $render_title The title of the option.
	 * @param ?string   $render_description Optional description for the option.
	 * @param array     $allowed_values Optional allowed values for the option. Value => Label pairs.
	 */
	public function __construct(
		string $slug,
		$default_value = null,
		?callable $sanitize_callback = null,
		?callable $render_callback = null,
		?string $render_section = null,
		?string $render_title = null,
		?string $render_description = null,
		array $allowed_values = array()
	) {
		$this->slug               = $slug;
		$this->default_value      = $default_value;
		$this->sanitize_callback  = $sanitize_callback;
		$this->render_callback    = $render_callback;
		$this->render_section     = $render_section;
		$this->render_title       = $render_title;
		$this->render_description = $render_description;
		$this->allowed_values     = $allowed_values;
	}

	public function get_slug(): string {
		return $this->slug;
	}

	/**
	 * Get the default value.
	 *
	 * @return array|int|string|bool|null
	 */
	public function get_default_value() {
		return $this->default_value;
	}

	public function get_sanitize_callback(): ?callable {
		return $this->sanitize_callback;
	}

	public function get_render_callback(): ?callable {
		return $this->render_callback;
	}

	public function get_render_section(): ?string {
		return $this->render_section;
	}

	public function get_render_title(): ?string {
		return $this->render_title;
	}

	public function get_render_description(): ?string {
		return $this->render_description;
	}

	public function get_allowed_values(): array {
		return $this->allowed_values;
	}

	/**
	 * Sanitize the given value
	 *
	 * @param mixed $value The value to sanitize.
	 * @return mixed
	 */
	public function sanitize( $value ) {
		if ( \is_callable( $this->sanitize_callback ) ) {
			return \call_user_func( $this->sanitize_callback, $value, $this );
		}
		return $value;
	}

	/**
	 * Get the current value of the option
	 *
	 * @return array|int|string|bool|null
	 */
	public function get_current_value() {
		$options = \get_option( Options::OPTION_NAME );

		if ( ! empty( $options[ $this->slug ] ) ) {
			return $options[ $this->slug ];
		}

		return $this->default_value;
	}
}
