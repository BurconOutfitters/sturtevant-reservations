<?php
/**
 * Load plugin addons.
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
 * Define the core functionality of the plugin.
 *
 * @since  1.0.0
 * @access public
 */
class Load_Addons {

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

			// Load the plugin addons.
			$instance->load_addons();

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
	private function __construct() {}

	/**
	 * Load the plugin addons.
	 *
	 * @return void
	 */
	public function load_addons() {

		global $sc_res_addons_active_list, // List of addon IDs.
			   $sc_res_addons_objs_list; // List of addon objects.

		$sc_res_addons_active_list = [];
		$sc_res_addons_objs_list   = [];

		// Get the list of active addons.
		$sc_res_addons_active_list = get_option( 'sc_res_addons_active_list', [] );

		if ( ! empty( $sc_res_addons_active_list ) || ( isset( $_GET['page'] ) && $_GET['page'] == 'reservations' ) ) {

			$path = dirname( __FILE__ ) . '/addons';

			if ( file_exists( $path ) ) {

				$addons = dir( $path );

				while ( false !== ( $entry = $addons->read() ) ) {

					if ( strlen( $entry ) > 3 && strtolower( pathinfo( $entry, PATHINFO_EXTENSION ) ) == 'php'  && $entry != 'dex_allcals.inc.php' ) {
						require_once $addons->path . '/' . $entry;
					}
				}
			}
		}

	}

}

/**
 * Put an instance of the class into a function.
 *
 * @since  1.0.0
 * @access public
 * @return object Returns an instance of the class.
 */
function sc_res_load_addons() {

	return Load_Addons::instance();

}

// Run an instance of the class.
sc_res_load_addons();