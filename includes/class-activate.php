<?php
/**
 * Plugin activation class.
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
 * Plugin activation class.
 *
 * @since  1.0.0
 * @access public
 */
final class SC_Res_Activate {

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

			// Activation function
			$instance->activate( $networkwide );

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
	 * Fired during plugin activation.
	 *
	 * @since  1.0.0
	 * @access public
	 * @return void
	 */
	public function activate( $networkwide ) {

		global $wpdb;

        // Check if it is a network activation. If so, run the activation function for each blog ID.
        if ( function_exists( 'is_multisite' ) && is_multisite() ) {

            if ( $networkwide ) {

                $old_blog = $wpdb->blogid;
                $blogids  = $wpdb->get_col( "SELECT blog_id FROM $wpdb->blogs" );

                foreach ( $blogids as $blog_id ) {
                    switch_to_blog( $blog_id );
                    _sc_res_install();
                }

                switch_to_blog( $old_blog );

                return;
            }
        }

        $this::install();

	}

	public static function install() {

        global $wpdb;

        $charset_collate = $wpdb->get_charset_collate();

        $sql = "CREATE TABLE ".$wpdb->prefix.DEX_BCCF_DISCOUNT_CODES_TABLE_NAME_NO_PREFIX." (
            id mediumint(9) NOT NULL AUTO_INCREMENT,
            cal_id mediumint(9) NOT NULL DEFAULT 1,
            code VARCHAR(250) DEFAULT '' NOT NULL,
            discount VARCHAR(250) DEFAULT '' NOT NULL,
            expires datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
            availability int(10) unsigned NOT NULL DEFAULT 0,
            used int(10) unsigned NOT NULL DEFAULT 0,
            UNIQUE KEY id (id)
            )".$charset_collate.";";
        $wpdb->query($sql);

        $sql = "CREATE TABLE ".$wpdb->prefix.DEX_BCCF_SEASON_PRICES_TABLE_NAME_NO_PREFIX." (
            id mediumint(9) NOT NULL AUTO_INCREMENT,
            cal_id mediumint(9) NOT NULL DEFAULT 1,
            price VARCHAR(250) DEFAULT '' NOT NULL,
            date_from datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
            date_to datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
            UNIQUE KEY id (id)
            )".$charset_collate.";";
        $wpdb->query($sql);


        $sql = "CREATE TABLE ".$wpdb->prefix.DEX_BCCF_TABLE_NAME_NO_PREFIX." (
            id mediumint(9) NOT NULL AUTO_INCREMENT,
            calendar INT NOT NULL,
            time datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
            booked_time_s VARCHAR(250) DEFAULT '' NOT NULL,
            booked_time_e VARCHAR(250) DEFAULT '' NOT NULL,
            booked_time_unformatted_s VARCHAR(250) DEFAULT '' NOT NULL,
            booked_time_unformatted_e VARCHAR(250) DEFAULT '' NOT NULL,
            name VARCHAR(250) DEFAULT '' NOT NULL,
            email VARCHAR(250) DEFAULT '' NOT NULL,
            phone VARCHAR(250) DEFAULT '' NOT NULL,
            notifyto VARCHAR(250) DEFAULT '' NOT NULL,
            question mediumtext,
            buffered_date mediumtext,
            UNIQUE KEY id (id)
            )".$charset_collate.";";
        $wpdb->query($sql);


        $sql = "CREATE TABLE `".$wpdb->prefix.DEX_BCCF_CONFIG_TABLE_NAME."` (".
                    "`".TDE_BCCFCONFIG_ID."` int(10) unsigned NOT NULL auto_increment,".
                    "`".TDE_BCCFCONFIG_TITLE."` varchar(255) NOT NULL default '',".
                    "`".TDE_BCCFCONFIG_USER."` varchar(100) default NULL,".
                    "`".TDE_BCCFCONFIG_PASS."` varchar(100) default NULL,".
                    "`".TDE_BCCFCONFIG_LANG."` varchar(5) default NULL,".
                    "`".TDE_BCCFCONFIG_CPAGES."` tinyint(3) unsigned default NULL,".
                    "`".TDE_BCCFCONFIG_MSG."` varchar(255) NOT NULL default '',".
                    "`".TDE_BCCFCALDELETED_FIELD."` tinyint(3) unsigned default NULL,".
                    "`conwer` INT NOT NULL,".
                    "`form_structure` mediumtext,".
                    "`master` varchar(50) DEFAULT '' NOT NULL,".
                    "`calendar_language` varchar(10) DEFAULT '' NOT NULL,".
                    "`calendar_mode` varchar(10) DEFAULT '' NOT NULL,".
                    "`calendar_dateformat` varchar(10) DEFAULT '',".
                    "`calendar_overlapped` varchar(10) DEFAULT '',".
                    "`calendar_enabled` varchar(10) DEFAULT '',".
                    "`calendar_pages` varchar(10) DEFAULT '' NOT NULL,".
                    "`calendar_weekday` varchar(10) DEFAULT '' NOT NULL,".
                    "`calendar_mindate` varchar(255) DEFAULT '' NOT NULL,".
                    "`calendar_maxdate` varchar(255) DEFAULT '' NOT NULL,".
                    "`calendar_minnights` varchar(255) DEFAULT '0' NOT NULL,".
                    "`calendar_maxnights` varchar(255) DEFAULT '365' NOT NULL,".
                    "`calendar_suplement` varchar(255) DEFAULT '0' NOT NULL,".
                    "`calendar_suplementminnight` varchar(255) DEFAULT '0' NOT NULL,".
                    "`calendar_suplementmaxnight` varchar(255) DEFAULT '0' NOT NULL,".
                    "`calendar_startres` text,".
                    "`calendar_holidays` text,".
                    "`calendar_fixedmode` varchar(10) DEFAULT '0' NOT NULL,".
                    "`calendar_holidaysdays` varchar(20) DEFAULT '1111111' NOT NULL,".
                    "`calendar_startresdays` varchar(20) DEFAULT '1111111' NOT NULL,".
                    "`calendar_fixedreslength` varchar(20) DEFAULT '1' NOT NULL,".
                    "`calendar_showcost` varchar(1) DEFAULT '1' NOT NULL,".
                    // paypal
                    "`enable_paypal` varchar(10) DEFAULT '' NOT NULL,".
                    "`paypal_email` varchar(255) DEFAULT '' NOT NULL ,".
                    "`request_cost` varchar(255) DEFAULT '' NOT NULL ,".
                    "`max_slots` varchar(20) DEFAULT '0' NOT NULL ,".
                    "`paypal_product_name` varchar(255) DEFAULT '' NOT NULL,".
                    "`currency` varchar(10) DEFAULT '' NOT NULL,".
                    "`request_taxes` varchar(20) DEFAULT '' NOT NULL ,".
                    "`url_ok` text,".
                    "`url_cancel` text,".
                    "`paypal_language` varchar(10) DEFAULT '' NOT NULL,".
                    // copy to user
                    "`cu_user_email_field` VARCHAR(250) DEFAULT '' NOT NULL,".
                    "`notification_from_email` text,".
                    "`notification_destination_email` text,".
                    "`email_subject_confirmation_to_user` text,".
                    "`email_confirmation_to_user` text,".
                    "`email_subject_notification_to_admin` text,".
                    "`email_notification_to_admin` text,".
                    // validation
                    "`enable_paypal_option_yes` VARCHAR(250) DEFAULT '' NOT NULL,".
                    "`enable_paypal_option_no` VARCHAR(250) DEFAULT '' NOT NULL,".
                    "`vs_use_validation` VARCHAR(10) DEFAULT '' NOT NULL,".
                    "`vs_text_is_required` VARCHAR(250) DEFAULT '' NOT NULL,".
                    "`vs_text_is_email` VARCHAR(250) DEFAULT '' NOT NULL,".
                    "`vs_text_datemmddyyyy` VARCHAR(250) DEFAULT '' NOT NULL,".
                    "`vs_text_dateddmmyyyy` VARCHAR(250) DEFAULT '' NOT NULL,".
                    "`vs_text_number` VARCHAR(250) DEFAULT '' NOT NULL,".
                    "`vs_text_digits` VARCHAR(250) DEFAULT '' NOT NULL,".
                    "`vs_text_max` VARCHAR(250) DEFAULT '' NOT NULL,".
                    "`vs_text_min` VARCHAR(250) DEFAULT '' NOT NULL,".

                    "`vs_text_submitbtn` VARCHAR(250) DEFAULT '' NOT NULL,".
                    "`vs_text_previousbtn` VARCHAR(250) DEFAULT '' NOT NULL,".
                    "`vs_text_nextbtn` VARCHAR(250) DEFAULT '' NOT NULL,  ".

                    "`calendar_depositenable` VARCHAR(20) DEFAULT '' NOT NULL,  ".
                    "`calendar_depositamount` VARCHAR(20) DEFAULT '' NOT NULL,  ".
                    "`calendar_deposittype` VARCHAR(20) DEFAULT '' NOT NULL,  ".
                    "`enable_beanstream_id` VARCHAR(250) DEFAULT '' NOT NULL,  ".

                    "`enable_reminder` VARCHAR(20) DEFAULT '' NOT NULL, ".

                    "`reminder_hours` VARCHAR(20) DEFAULT '' NOT NULL, ".
                    "`reminder_subject` VARCHAR(250) DEFAULT '' NOT NULL, ".
                    "`reminder_content` text ,".
                    "`nremind_emailformat` VARCHAR(20) DEFAULT '' NOT NULL, ".

                    "`reminder_hours2` VARCHAR(20) DEFAULT '' NOT NULL, ".
                    "`reminder_subject2` VARCHAR(250) DEFAULT '' NOT NULL, ".
                    "`reminder_content2` text ,".
                    "`nremind_emailformat2` VARCHAR(20) DEFAULT '' NOT NULL, ".

                    // captcha
                    "`dexcv_enable_captcha` varchar(10) DEFAULT '' NOT NULL,".
                    "`dexcv_width` varchar(10) DEFAULT '' NOT NULL,".
                    "`dexcv_height` varchar(10) DEFAULT '' NOT NULL,".
                    "`dexcv_chars` varchar(10) DEFAULT '' NOT NULL,".
                    "`dexcv_min_font_size` varchar(10) DEFAULT '' NOT NULL,".
                    "`dexcv_max_font_size` varchar(10) DEFAULT '' NOT NULL,".
                    "`dexcv_noise` varchar(10) DEFAULT '' NOT NULL,".
                    "`dexcv_noise_length` varchar(10) DEFAULT '' NOT NULL,".
                    "`dexcv_background` varchar(10) DEFAULT '' NOT NULL,".
                    "`dexcv_border` varchar(10) DEFAULT '' NOT NULL,".
                    "`dexcv_font` varchar(100) DEFAULT '' NOT NULL,".
                    "`cv_text_enter_valid_captcha` VARCHAR(250) DEFAULT '' NOT NULL,".
                    // services field
                    "`cp_cal_checkboxes` text,".
                    "`cp_cal_checkboxes_type` varchar(10) DEFAULT '' NOT NULL,".
                    "PRIMARY KEY (`".TDE_BCCFCONFIG_ID."`))".$charset_collate.";";
        $wpdb->query($sql);

        $sql = 'INSERT INTO `'.$wpdb->prefix.DEX_BCCF_CONFIG_TABLE_NAME.'` (`'.TDE_BCCFCONFIG_ID.'`,`form_structure`,`'.TDE_BCCFCONFIG_TITLE.'`,`'.TDE_BCCFCONFIG_USER.'`,`'.TDE_BCCFCONFIG_PASS.'`,`'.TDE_BCCFCONFIG_LANG.'`,`'.TDE_BCCFCONFIG_CPAGES.'`,`'.TDE_BCCFCONFIG_MSG.'`,`'.TDE_BCCFCALDELETED_FIELD.'`,calendar_mode) VALUES("1","'.esc_sql(DEX_BCCF_DEFAULT_form_structure).'","cal1","Calendar Item 1","","ENG","1","Please, select your reservation.","0","true");';
        $wpdb->query($sql);

        $sql = "CREATE TABLE `".$wpdb->prefix.DEX_BCCF_CALENDARS_TABLE_NAME."` (".
                    "`".TDE_BCCFDATA_ID."` int(10) unsigned NOT NULL auto_increment,".
                    "`".TDE_BCCFDATA_IDCALENDAR."` int(10) unsigned default NULL,".
                    "`".TDE_BCCFDATA_DATETIME_S."`datetime NOT NULL default '0000-00-00 00:00:00',".
                    "`".TDE_BCCFDATA_DATETIME_E."`datetime NOT NULL default '0000-00-00 00:00:00',".
                    "`".TDE_BCCFDATA_TITLE."` varchar(250) default NULL,".
                    "`".TDE_BCCFDATA_DESCRIPTION."` mediumtext,".
                    "`viadmin` varchar(10) DEFAULT '0' NOT NULL,".
                    "`reference` varchar(20) DEFAULT '' NOT NULL,".
                    "`reminder` varchar(1) DEFAULT '' NOT NULL,".
                    "`color` varchar(10),".
                    "PRIMARY KEY (`".TDE_BCCFDATA_ID."`))".$charset_collate.";";
        $wpdb->query($sql);



        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');

	}

}

/**
 * Put an instance of the class into a function.
 *
 * @since  1.0.0
 * @access public
 * @return object Returns an instance of the class.
 */
function sc_res_activate() {

	return SC_Res_Activate::instance();

}