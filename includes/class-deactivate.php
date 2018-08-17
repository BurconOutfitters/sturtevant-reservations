<?php
/**
 * Plugin deactivation class.
 *
 * @link       http://ccdzine.com
 * @since      1.0.0
 *
 * @package    Sturtevant_Reservations
 * @subpackage Includes
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Plugin deactivation class.
 *
 * @since  1.0.0
 * @access public
 */
final class SC_Res_Deactivate {

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

			// Deactivation function
			$instance->deactivate();

		}

		// Return the instance.
		return $instance;

	}

	/**
	 * Constructor method.
	 *
	 * @since  1.0.0
	 * @access public
	 * @return void Constructor method is empty.
	 */
	public function __construct() {}

	/**
	 * Fired during plugin deactivation.
	 *
	 * @since  1.0.0
	 * @access public
	 * @return void
	 */
	public function deactivate() {}

}

/**
 * Put an instance of the class into a function.
 *
 * @since  1.0.0
 * @access public
 * @return object Returns an instance of the class.
 */
function sc_res_deactivate() {

	return SC_Res_Deactivate::instance();

}