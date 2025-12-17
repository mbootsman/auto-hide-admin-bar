<?php
declare( strict_types=1 );
namespace AHAB\App;

/**
 * Class Admin
 *
 * Handle the admin side.
 *
 * @package AHAB\App
 */
class Admin {

	/**
	 * Register the options page.
	 */
	public static function register_options_page(): void {
		// Implementation for adding options page.
		\add_options_page(
			\__( 'Auto Hide Admin Bar Options', 'auto-hide-admin-bar' ),
			\__( 'Auto Hide Admin Bar', 'auto-hide-admin-bar' ),
			'manage_options',
			AHAB_SLUG,
			array( self::class, 'render_options_page' )
		);
	}

	/**
	 * Render the basic settings page.
	 */
	public static function render_options_page(): void {
		?>
		<div class="wrap">
			<h2><?php \esc_html_e( 'Auto Hide Admin Bar Options', 'auto-hide-admin-bar' ); ?></h2>
			<form action="options.php" method="post">
				<?php \settings_fields( Options::OPTION_NAME ); ?>
				<?php \do_settings_sections( 'ahab_plugin' ); ?>
				<input name="Submit" type="submit" class="button button-primary save" value="<?php \esc_html_e( 'Save Changes', 'auto-hide-admin-bar' ); ?>" />
			</form>
			<p><?php \esc_html_e( 'Version: ', 'auto-hide-admin-bar' ); ?><?php echo \esc_html( AHAB_VERSION ); ?>
		</div>
		<?php
	}
}
