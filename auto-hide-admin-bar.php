<?php
/**
 * Plugin Name:       Auto Hide Admin Bar
 * Description:       Automatically hides the Toolbar. Will show the Toolbar when hovering over the top of the site.
 * Author:            Marcel Bootsman
 * Author URI:        https://marcelbootsman.nl
 * Github Plugin URI: https://github.com/mbootsman/auto-hide-admin-bar
 * Primary Branch:    main
 * Text Domain:       auto-hide-admin-bar
 * Domain Path:       /languages/
 * License:           GPLv2
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Requires at least: 3.1
 * Requires PHP:      7.4
 * Version:           1.6.6
 *
 * @package           AHAB
 */

\define( 'AHAB_PLUGIN_BASE_VERSION', '1.6.6' );
\define( 'AHAB_PLUGIN_BASE_DIR', \plugin_dir_path( __FILE__ ) ); // Full path with trailing slash.
\define( 'AHAB_PLUGIN_BASE_URL', \plugin_dir_url( __FILE__ ) ); // With trailing slash.
\define( 'AHAB_PLUGIN_BASE_SLUG', \basename( __DIR__ ) ); // auto-hide-admin-bar.

if ( ! \defined( 'ABSPATH' ) ) {
	return; // WP not loaded.
}

/**
 * Autoload internal classes.
 */
require_once AHAB_PLUGIN_BASE_DIR . 'app/class-plugin.php';
\spl_autoload_register( array( AHAB\App\Plugin::class, 'autoloader' ) );

\register_activation_hook( __FILE__, array( AHAB\App\Plugin::class, 'activate' ) );
\register_deactivation_hook( __FILE__, array( AHAB\App\Plugin::class, 'deactivate' ) );
\register_uninstall_hook( __FILE__, array( AHAB\App\Plugin::class, 'uninstall' ) );


\add_action( 'init', array( AHAB\App\Plugin::class, 'load_textdomain' ), 9 );
\add_filter( 'plugin_action_links_' . \plugin_basename( __FILE__ ), array( AHAB\App\Plugin::class, 'settings_link' ) );
\add_filter( 'gu_override_dot_org', array( AHAB\App\Plugin::class, 'override_dot_org' ) );

// Handle the options page.
\add_action( 'admin_menu', array( AHAB\App\Admin::class, 'register_options_page' ) );
\add_action( 'admin_menu', array( AHAB\App\Options::class, 'register_settings' ) );

\add_action( 'admin_bar_menu', array( AHAB\App\Frontend::class, 'admin_bar_item' ), 0 );



/**
 ***********************************************
 * OLD below.
 ***********************************************
 */


/*
----------------------------------------------------------------------------
 *  Global data */
$keyboard_shortcut_fields = array(
	'Ctrl'  => 0,
	'Alt'   => 0,
	'Shift' => 0,
	'char'  => '',
);

/* Define some default values */
\define( 'DEFAULT_SPEED', 200 );
\define( 'DEFAULT_DELAY', 1500 );
\define( 'DEFAULT_INTERVAL', 100 );
\define( 'DEFAULT_MOBILE', 1 );
\define( 'DEFAULT_TOGGLE', 1 );
\define( 'DEFAULT_ARROW', 1 );
\define( 'DEFAULT_ARROW_POS', 'left' );

/* Load CSS files */
function ahab_admin_styles() {
	// only load if a user is logged in
	if ( \is_user_logged_in() ) {
		\wp_enqueue_style( 'admin-styles', \plugin_dir_url( __FILE__ ) . 'css/ahab.css' );
	}
}
\add_action( 'wp_enqueue_scripts', 'ahab_admin_styles' );


/**
 * Check if ahab is disabled (by user role)
 *
 * @author Marcel Bootsman
 */
function is_ahab_disabled(): bool {
	$ahab_disabled = false;

	// Get options
	$options = \get_option( 'ahab_plugin_options' );

	// check if ahab is disabled for current user role
	global $wp_roles, $current_user, $ahab_disabled;

	foreach ( $wp_roles->roles as $role_key => $role ) {
		// disabled user roles are stored as a seperate array element

		if ( $options ) { // only continue if options exists

			if ( ! empty( $options[ 'disabled_user_roles_' . $role_key ] ) ) {
				// check if current user role matches the role

				if ( \in_array( $role_key, $current_user->roles ) ) {
					$ahab_disabled = true;

					// leave the foreach loop
					break;
				}
			} else {
				// no role options set (thanks for updating/installing!), enable ahab for everyone.
				$ahab_disabled = false;
			}
		} else {
			// no options set, enable ahab for everyone.
			$ahab_disabled = false;
		}
	}

	return $ahab_disabled;
}

/**
 * The main function. Build JS code and output it.
 *
 * @author Marcel Bootsman
 */
function auto_hide_admin_bar(): void {
	// Get options
	$options = \get_option( 'ahab_plugin_options' );
	global $keyboard_shortcut_fields;

	if ( ( ! empty( $options['speed'] ) ) && ( \is_numeric( $options['speed'] ) ) ) {
		$ahab_anim_speed = $options['speed'];
	} else {
		$ahab_anim_speed = DEFAULT_SPEED;
	}

	if ( ( ! empty( $options['delay'] ) ) && ( \is_numeric( $options['delay'] ) ) ) {
		$ahab_delay = $options['delay'];
	} else {
		$ahab_delay = DEFAULT_DELAY;
	}

	if ( ( ! empty( $options['interval'] ) ) && ( \is_numeric( $options['interval'] ) ) ) {
		$ahab_interval = $options['interval'];
	} else {
		$ahab_interval = DEFAULT_INTERVAL;
	}

	if ( ( ! empty( $options['arrow'] ) ) && ( \is_numeric( $options['arrow'] ) ) ) {
		$ahab_arrow = $options['arrow'];
	} else {
		$ahab_arrow = DEFAULT_ARROW;
	}

	if ( ( ! empty( $options['arrow_pos'] ) ) && ( \is_string( $options['arrow_pos'] ) ) ) {
		$ahab_arrow_pos = $options['arrow_pos'];
	} else {
		$ahab_arrow_pos = DEFAULT_ARROW_POS;
	}

	if ( ( ! empty( $options['mobile'] ) ) && ( \is_numeric( $options['mobile'] ) ) ) {
		$ahab_mobile = $options['mobile'];
	} else {
		$ahab_mobile = DEFAULT_MOBILE;
	}

	// get keys and prepare to pass to JS
	$ahab_keyboard_shortcut_keys = array();

	foreach ( $keyboard_shortcut_fields as $key => $value ) {
		if ( ! $options ) {
			continue;
		}
		// only continue if options exists
		if ( empty( $options[ 'keyboard_shortcut_' . $key ] ) ) {
			continue;
		}
		if ( '' == $options[ 'keyboard_shortcut_' . $key ] ) {
			continue;
		}
		$ahab_keyboard_shortcut_keys[ $key ] = $options[ 'keyboard_shortcut_' . $key ];
	}

	/**
	 * Theme name check - For now only for Twenty Fourteen
	 * because of the fixed header/menu
	 */
	if ( \function_exists( 'wp_get_theme' ) ) {
		$theme_name = ( \wp_get_theme()->Template );
	}
	?>
	<script type='text/javascript'>
		// For passing the variables to the ahab.js file
		ahab = {
			'theme_name': '<?php echo $theme_name; ?>',
			'ahab_anim_speed': <?php echo $ahab_anim_speed; ?>,
			'ahab_delay': <?php echo $ahab_delay; ?>,
			'ahab_interval': <?php echo $ahab_interval; ?>,
			'ahab_mobile': '<?php echo $ahab_mobile; ?>',
			'ahab_arrow': '<?php echo $ahab_arrow; ?>',
			'ahab_arrow_pos': '<?php echo $ahab_arrow_pos; ?>',
			'ahab_keyboard_ctrl': <?php echo \array_key_exists( 'Ctrl', $ahab_keyboard_shortcut_keys ) ? '\'' . $ahab_keyboard_shortcut_keys['Ctrl'] . '\'' : 0; ?>,
			'ahab_keyboard_alt': <?php echo \array_key_exists( 'Alt', $ahab_keyboard_shortcut_keys ) ? '\'' . $ahab_keyboard_shortcut_keys['Alt'] . '\'' : 0; ?>,
			'ahab_keyboard_shift': <?php echo \array_key_exists( 'Shift', $ahab_keyboard_shortcut_keys ) ? '\'' . $ahab_keyboard_shortcut_keys['Shift'] . '\'' : 0; ?>,
			'ahab_keyboard_char': <?php echo \array_key_exists( 'char', $ahab_keyboard_shortcut_keys ) ? '\'' . $ahab_keyboard_shortcut_keys['char'] . '\'' : '\'\''; ?>
		};
	</script>
	<?php
}

/**
 * Add jQuery and jQuery hoverIntent (No tents were harmed in the process)
 *
 * @param None
 *
 * @return None
 * @author Marcel Bootsman
 */

\add_action( 'wp_footer', 'ahab_add_jquery_stuff' );
function ahab_add_jquery_stuff() {

	if ( \is_user_logged_in() && ( ! \is_ahab_disabled() ) ) {
		\wp_enqueue_script( 'jquery' );

		\wp_register_script( 'jquery-hoverintent', \plugins_url( 'js/jquery.hoverIntent.minified.js', __FILE__ ) );
		\wp_enqueue_script( 'jquery-hoverintent' );

		\wp_enqueue_script( 'jquery-hotkeys' );

		\wp_register_script( 'ahab', \plugins_url( 'js/ahab.js', __FILE__ ) );
		\wp_enqueue_script( 'ahab' );
	}
}

/**
 * Hook main function for logged in users
 *
 * @author Marcel Bootsman
 */
\add_action( 'wp_footer', 'ahab_add_my_hide_stuff' );
function ahab_add_my_hide_stuff() {
	if ( \is_user_logged_in() && ( ! \is_ahab_disabled() ) ) {
		\auto_hide_admin_bar();
	}
}

