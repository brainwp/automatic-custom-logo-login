<?php
/**
 * Plugin Name: Automatic Custom Login Logo
 * Plugin URI: https://github.com/brasadesign/automatic-custom-logo-login
 * Description: WordPress plugin that changes out the default logo on the login screen with the custom logo.
 * Version: 0.1.2
 * Author: Everaldo Matias
 * Author URI: http://everaldomatias.github.io
 * Text Domain: acll
 * Domain Path: /languages/
 * License: GPLv2 or later
 */

// If this file is called directly, abort
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'Automatic_Custom_Login_Logo' ) ) :

	/**
	 * Automatic Custom Login Logo plugin.
	 */
	class Automatic_Custom_Login_Logo {

		/**
		 * Initialize the plugin.
		 */
		function __construct() {

			// Load plugin text domain.
			add_action( 'init', array( $this, 'acll_load_plugin_textdomain' ) );

			// Verify version WP.
			global $wp_version;

			if ( version_compare( $wp_version, '4.5', '>=' ) ) {

				// Verify and add support to custom-logo.
				if ( ! get_theme_support( 'custom-logo' ) ) {
					add_theme_support( 'custom-logo' );
				}
				
				// Add Login Logo URL 
				add_filter( 'login_headerurl', array( $this, 'acll_login_logo_url' ) );

				// Add Login Logo Title
				add_filter( 'login_headertext', array( $this, 'acll_login_logo_title' ) );

				// Add Custom Logo Image in Login Page
				add_action( 'login_enqueue_scripts', array( $this, 'acll_login_logo' ) );

			} else {
				add_action( 'admin_notices', array( $this, 'acll_is_missing_notice' ) );
			}

		}

		/**
		 * Instance of this class.
		 *
		 * @var object.
		 */
		protected static $instance = null;

		/**
		 * Return an instance of this class.
		 *
		 * @return object a single instance of this class.
		 */
		public static function get_instance() {
			// If the single instance hasn't been set, set it now.
			if ( null == self::$instance ) {
				self::$instance = new self;
			}
			return self::$instance;
		}

		/**
		 * Load the plugin text domain for translation.
		 */
		public function acll_load_plugin_textdomain() {
			load_plugin_textdomain( 'acll', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
		}

		/**
		 * Automatic Custom Login Logo missing notice.
		 *
		 * @return string admin notice.
		 */
		public function acll_is_missing_notice() {
			echo '<div class="error"><p><strong>' . __( 'Automatic Custom Login Logo', 'acll' ) . '</strong> ' . sprintf( __( 'works only with the version 4.5 or later of the WordPress, please %s!', 'acll' ), '<a href="' . esc_url( admin_url( 'update-core.php' ) ) . '">' . __( 'upgrade your installation', 'acll' ) . '</a>' ) . '</p></div>';
		}

		/**
		 * Return home url in custom logo.
		 *
		 * @return home url.
		 */
		public function acll_login_logo_url() {
			return esc_url( get_home_url() );
		}

		/**
		 * Return name site in custom logo.
		 *
		 * @return string name site.
		 */
		public function acll_login_logo_title() {
			return get_bloginfo( 'name' );
		}

		/**
		 * Return the custom logo in login page.
		 *
		 * @return css with the custom login.
		 */

		public function acll_login_logo() {
			if ( has_custom_logo() ) {
				$logo = wp_get_attachment_image_src( get_theme_mod( 'custom_logo' ), 'full' ); ?>
			    <style type="text/css">
			    	/* Automatic Custom Login Logo Styles */
			        body.login div#login h1 a {
			            background: url( '<?php echo esc_url( $logo[0] ); ?>' ) center no-repeat !important;
			            background-size: 80% !important;
			            height: <?php echo esc_html( $logo[2] ); ?>px !important;
			            max-width: 320px;
			            width: <?php echo esc_html( $logo[1] ); ?>px !important;
			        }
			    </style>
			<?php
			}
		}
	}

	/**
	 * Initialize the plugin actions.
	 */
	add_action( 'plugins_loaded', array( 'Automatic_Custom_Login_Logo', 'get_instance' ) );

endif;