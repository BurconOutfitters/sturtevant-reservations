<?php
/**
 * Base addon file.
 *
 * @package    Sturtevant_Reservations
 * @subpackage Addons
 *
 * @since      1.0.0
 * @author     Greg Sweet <greg@ccdzine.com>
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Addon system attributes and methods.
 *
 * @since  1.0.0
 * @access public
 */
if ( ! class_exists( 'DEXBCCF_BaseAddon' ) ) {
    class DEXBCCF_BaseAddon {

		/**
		 * Class variables
		 */
		protected $addonID;
		protected $name;
		protected $description;

		/**
		 * Addon ID.
		 *
		 * @return int Returns the ID of the addon.
		 */
		public function get_addon_id() {
			return $this->addonID;
		}

		/**
		 * Addon name.
		 *
		 * @return string Returns the name of the addon.
		 */
		public function get_addon_name() {
			return $this->name;
		}

		/**
		 * Addon description.
		 *
		 * @return string Return sthe description of the addon.
		 */
		public function get_addon_description() {
			return $this->description;
		}

		/**
		 * Addon form settings
		 *
		 * @param  int $form_id The ID of the addon.
		 * @return void
		 */
		public function get_addon_form_settings( $form_id ) {
			return '';
		}

		/**
		 * Addon settings
		 *
		 * @return void
		 */
		public function get_addon_settings() {
			return '';
		}

		/**
		 * Check if an addon is active.
		 *
		 * @return array Returns an array of active addons.
		 */
		public function addon_is_active() {

			global $dexbccf_addons_active_list;

			return in_array( $this->get_addon_id(), $dexbccf_addons_active_list );

		}

	}

}