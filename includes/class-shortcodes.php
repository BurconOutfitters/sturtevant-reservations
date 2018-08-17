<?php
/**
 * Form & calendar shortcodes.
 *
 * @package    Sturtevant_Reservations
 * @subpackage Includes
 *
 * @since      1.0.0
 * @author     Greg Sweet <greg@ccdzine.com>
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Form & calendar shortcodes.
 *
 * @since 1.0.0
 */
class SC_Res_Shortcodes {

	/**
	 * Get an instance of the class.
	 *
	 * @since  1.0.0
	 * @access public
	 * @return object Returns the instance.
	 */
	public static function instance() {

		// Varialbe for the instance to be used outside the class.
		static $instance = null;

		if ( is_null( $instance ) ) {

			// Set variable for new instance.
			$instance = new self;

		}

		// Return the instance.
		return $instance;

    }

    /**
	 * Constructor method.
	 *
	 * @since  1.0.0
	 * @access private
	 * @return self
	 */
	private function __construct() {

		add_shortcode( 'CP_BCCF_FORM', [ $this, 'dex_bccf_filter_content' ] );

		add_shortcode( 'CP_BCCF_ALLCALS', [ $this, 'dex_bccf_filter_content_allcalendars' ] );

	}

	/**
	 * Undocumented function
	 *
	 * @since  1.0.0
	 * @access public
	 * @param array $atts
	 * @return mixed[]
	 */
	public function dex_bccf_filter_content( $atts ) {

		global $wpdb;

		extract( shortcode_atts(
			[
				'calendar' => '',
				'user'     => '',
				'pages'    => '',
			],
			$atts
		) );

		/**
		 * Filters applied before generate the form,
		 * is passed as parameter an array with the forms attributes, and return the list of attributes
		 */
		$atts = apply_filters( 'dexbccf_pre_form',  $atts );

		if ( $calendar != '' ) {
			define ( 'DEX_BCCF_CALENDAR_FIXED_ID', intval( $calendar ) );
		} elseif ( $user != '' ) {
			$users = $wpdb->get_results( "SELECT user_login,ID FROM ".$wpdb->users." WHERE user_login='" . esc_sql( $user ) . "'" );
			if ( isset( $users[0] ) ) {
				define ( 'DEX_CALENDAR_USER', $users[0]->ID );
			} else {
				define ( 'DEX_CALENDAR_USER', 0 );
			}
		} else {
			define ( 'DEX_CALENDAR_USER', 0 );
		}

		ob_start();
		dex_bccf_get_public_form( $pages );
		$buffered_contents = ob_get_contents();
		ob_end_clean();

		/**
		 * Filters applied after generate the form,
		 * is passed as parameter the HTML code of the form with the corresponding <LINK> and <SCRIPT> tags,
		 * and returns the HTML code to includes in the webpage
		 */
		$buffered_contents = apply_filters( 'dexbccf_the_form', $buffered_contents,  $atts['id'] );

		return $buffered_contents;

	}

	/**
	 * Undocumented function
	 *
	 * @since  1.0.0
	 * @access public
	 * @param  array $atts
	 * @return mixed[]
	 */
	public function dex_bccf_filter_content_allcalendars( $atts ) {

		global $wpdb;

		extract( shortcode_atts(
			[
				'calendar' => '',
				'id'       => '',
				'pages'    => '',
			],
			$atts
		) );

		if ( $calendar == '' ) {
			$calendar = $id;
		}

		if ( ! defined( 'DEX_CALENDAR_USER' ) ) {
			define ( 'DEX_CALENDAR_USER', 0 );
		}

		ob_start();
		wp_enqueue_script( 'jquery' );
		wp_enqueue_script( 'jquery-ui-core' );
		wp_enqueue_script( 'jquery-ui-datepicker' );
		$myrows = $wpdb->get_results( "SELECT * FROM ".DEX_BCCF_CONFIG_TABLE_NAME.( $calendar!=''?" WHERE id=".$calendar:"" ) );

		if ( ! defined( 'DEX_AUTH_INCLUDE' ) ) {
			define( 'DEX_AUTH_INCLUDE', true );
		}

		@include dirname( __FILE__ ) . '/addons/sc-res-all-calendars.php';

		$buffered_contents = ob_get_contents();
		ob_end_clean();

		return $buffered_contents;

	}

}

/**
 * Put an instance of the plugin class into a function.
 *
 * @since  1.0.0
 * @access public
 * @return object Returns the instance of the `Controlled_Chaos_Plugin` class.
 */
function sc_res_shortcodes() {

	return SC_Res_Shortcodes::instance();

}

// Run an instance of the class.
sc_res_shortcodes();