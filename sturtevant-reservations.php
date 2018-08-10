<?php
/**
 * Plugin Name: Sturtevant Resrvations
 * Plugin URI:  https://github.com/BurconOutfitters/sturtevant-reservations
 * Description: Cabin reservation and payment system.
 * Version:     1.0.0
 * Author:      Controlled Chaos Design
 * Author URI:  http://ccdzine.com/
 * License:     GPL-3.0+
 * License URI: https://www.gnu.org/licenses/gpl.txt
 * Text Domain: sc-res
 * Domain Path: /languages
 */

/**
 * Forked from the Booking Calendar and Contact Form
 *
 * @link http://wordpress.dwbooster.com/calendars/booking-calendar-contact-form
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}


/* initialization / install / uninstall functions */

define('DEX_BCCF_DEFAULT_form_structure', '[[{"name":"email","index":0,"title":"Email","ftype":"femail","userhelp":"","csslayout":"","required":true,"predefined":"","size":"medium"},{"name":"subject","index":1,"title":"Subject","required":true,"ftype":"ftext","userhelp":"","csslayout":"","predefined":"","size":"medium"},{"name":"message","index":2,"size":"large","required":true,"title":"Message","ftype":"ftextarea","userhelp":"","csslayout":"","predefined":""}],[{"title":"","description":"","formlayout":"top_aligned"}]]');

define('DEX_BCCF_DEFAULT_DEFER_SCRIPTS_LOADING', (get_option('CP_BCCF_LOAD_SCRIPTS',"1") == "1"?true:false));

define('DEX_BCCF_DEFAULT_SERVICES_FIELDS', 6);

define('DEX_BCCF_DEFAULT_COLOR_STARTRES', '#CAFFCA');
define('DEX_BCCF_DEFAULT_COLOR_HOLIDAY', '#FF8080');

define('DEX_BCCF_DEFAULT_CALENDAR_LANGUAGE', '');
define('DEX_BCCF_DEFAULT_CALENDAR_DATEFORMAT', 'false');
define('DEX_BCCF_DEFAULT_CALENDAR_WEEKDAY', '0');
define('DEX_BCCF_DEFAULT_CALENDAR_MINDATE', 'today');
define('DEX_BCCF_DEFAULT_CALENDAR_MAXDATE', '');
define('DEX_BCCF_DEFAULT_CALENDAR_PAGES', 2);
define('DEX_BCCF_DEFAULT_CALENDAR_OVERLAPPED', 'false');
define('DEX_BCCF_DEFAULT_CALENDAR_ENABLED', 'true');

define('DEX_BCCF_DEFAULT_cu_user_email_field', 'email');
define('TDE_BCCFCALENDAR_DEFAULT_COLOR', '6FF');

define('DEX_BCCF_DEFAULT_ENABLE_PAYPAL', 1);
define('DEX_BCCF_DEFAULT_PAYPAL_EMAIL','put_your@email_here.com');
define('DEX_BCCF_DEFAULT_PRODUCT_NAME','Reservation');
define('DEX_BCCF_DEFAULT_COST','25');
define('DEX_BCCF_DEFAULT_OK_URL',get_site_url());
define('DEX_BCCF_DEFAULT_CANCEL_URL',get_site_url());
define('DEX_BCCF_DEFAULT_CURRENCY','USD');
define('DEX_BCCF_DEFAULT_PAYPAL_LANGUAGE','EN');

define('DEX_BCCF_DEFAULT_PAYPAL_OPTION_YES', 'Pay with PayPal.');
define('DEX_BCCF_DEFAULT_PAYPAL_OPTION_NO', 'Pay later.');

define('DEX_BCCF_DEFAULT_vs_text_is_required', 'This field is required.');
define('DEX_BCCF_DEFAULT_vs_text_is_email', 'Please enter a valid email address.');

define('DEX_BCCF_DEFAULT_vs_text_datemmddyyyy', 'Please enter a valid date with this format(mm/dd/yyyy)');
define('DEX_BCCF_DEFAULT_vs_text_dateddmmyyyy', 'Please enter a valid date with this format(dd/mm/yyyy)');
define('DEX_BCCF_DEFAULT_vs_text_number', 'Please enter a valid number.');
define('DEX_BCCF_DEFAULT_vs_text_digits', 'Please enter only digits.');
define('DEX_BCCF_DEFAULT_vs_text_max', 'Please enter a value less than or equal to {0}.');
define('DEX_BCCF_DEFAULT_vs_text_min', 'Please enter a value greater than or equal to {0}.');


define('DEX_BCCF_DEFAULT_SUBJECT_CONFIRMATION_EMAIL', 'Thank you for your request');
define('DEX_BCCF_DEFAULT_CONFIRMATION_EMAIL', "We have received your request with the following information:\n\n%INFORMATION%\n\nThank you.\n\nBest regards.");
define('DEX_BCCF_DEFAULT_SUBJECT_NOTIFICATION_EMAIL','New reservation requested');
define('DEX_BCCF_DEFAULT_NOTIFICATION_EMAIL', "New reservation made with the following information:\n\n%INFORMATION%\n\nBest regards.");

define('DEX_BCCF_DEFAULT_REMINDER_CONTENT', "This is a reminder for your booking with the following information:\n\n%INFORMATION%\n\nThank you.\n\nBest regards.");
define('DEX_BCCF_DEFAULT_REMINDER_CONTENT_AFTER', "Thank you for your booking. here is the booking information for future reference:\n\n%INFORMATION%\n\nThank you.\n\nBest regards.");

define('DEX_BCCF_DEFAULT_CP_CAL_CHECKBOXES',"");
define('DEX_BCCF_DEFAULT_CP_CAL_CHECKBOXES_TYPE',"0");
define('DEX_BCCF_DEFAULT_EXPLAIN_CP_CAL_CHECKBOXES',"80.00 | 2 Guests\n120.00 | 3 Guests\n160.00 | 4 Guests");


// tables

define('DEX_BCCF_TABLE_NAME_NO_PREFIX', "bccf_dex_bccf_submissions");
define('DEX_BCCF_TABLE_NAME', @$wpdb->prefix . DEX_BCCF_TABLE_NAME_NO_PREFIX);

define('DEX_BCCF_CALENDARS_TABLE_NAME_NO_PREFIX', "bccf_reservation_calendars_data");
define('DEX_BCCF_CALENDARS_TABLE_NAME', @$wpdb->prefix ."bccf_reservation_calendars_data");

define('DEX_BCCF_CONFIG_TABLE_NAME_NO_PREFIX', "bccf_reservation_calendars");
define('DEX_BCCF_CONFIG_TABLE_NAME', @$wpdb->prefix ."bccf_reservation_calendars");

define('DEX_BCCF_DISCOUNT_CODES_TABLE_NAME_NO_PREFIX', "bccf_dex_discount_codes");
define('DEX_BCCF_SEASON_PRICES_TABLE_NAME_NO_PREFIX', "bccf_dex_season_prices");
define('DEX_BCCF_DISCOUNT_CODES_TABLE_NAME', @$wpdb->prefix ."bccf_dex_discount_codes");

// calendar constants

define("TDE_BCCFDEFAULT_CALENDAR_ID","1");
define("TDE_BCCFDEFAULT_CALENDAR_LANGUAGE","EN");
define("DEX_BCCF_DEFAULT_CALENDAR_MODE","true");

define("TDE_BCCFCAL_PREFIX", "RCalendar");
define("TDE_BCCFCONFIG",DEX_BCCF_CONFIG_TABLE_NAME);
define("TDE_BCCFCONFIG_ID","id");
define("TDE_BCCFCONFIG_TITLE","title");
define("TDE_BCCFCONFIG_USER","uname");
define("TDE_BCCFCONFIG_PASS","passwd");
define("TDE_BCCFCONFIG_LANG","lang");
define("TDE_BCCFCONFIG_CPAGES","cpages");
define("TDE_BCCFCONFIG_MSG","msg");
define("TDE_BCCFCALDELETED_FIELD","caldeleted");

define("TDE_BCCFCALENDAR_DATA_TABLE",DEX_BCCF_CALENDARS_TABLE_NAME);
define("TDE_BCCFDATA_ID","id");
define("TDE_BCCFDATA_IDCALENDAR","reservation_calendar_id");
define("TDE_BCCFDATA_DATETIME_S","datatime_s");
define("TDE_BCCFDATA_DATETIME_E","datatime_e");
define("TDE_BCCFDATA_TITLE","title");
define("TDE_BCCFDATA_DESCRIPTION","description");
// end calendar constants

define('TDE_BCCFDEFAULT_dexcv_enable_captcha', 'true');
define('TDE_BCCFDEFAULT_dexcv_width', '180');
define('TDE_BCCFDEFAULT_dexcv_height', '60');
define('TDE_BCCFDEFAULT_dexcv_chars', '5');
define('TDE_BCCFDEFAULT_dexcv_font', 'font-1.ttf');
define('TDE_BCCFDEFAULT_dexcv_min_font_size', '25');
define('TDE_BCCFDEFAULT_dexcv_max_font_size', '35');
define('TDE_BCCFDEFAULT_dexcv_noise', '200');
define('TDE_BCCFDEFAULT_dexcv_noise_length', '4');
define('TDE_BCCFDEFAULT_dexcv_background', 'ffffff');
define('TDE_BCCFDEFAULT_dexcv_border', '000000');
define('DEX_BCCF_DEFAULT_dexcv_text_enter_valid_captcha', 'Please enter a valid captcha code.');


// loading add-ons
// -----------------------------------------
global $dexbccf_addons_active_list, // List of addon IDs
	   $dexbccf_addons_objs_list; // List of addon objects

$dexbccf_addons_active_list = array();
$dexbccf_addons_objs_list	 = array();

function dexbccf_loading_add_ons()
{
	global $dexbccf_addons_active_list, // List of addon IDs
		   $dexbccf_addons_objs_list; // List of addon objects

    // Get the list of active addons
	$dexbccf_addons_active_list = get_option( 'dexbccf_addons_active_list', array() );
	if( !empty( $dexbccf_addons_active_list ) || ( isset( $_GET["page"] ) && $_GET["page"] == "dex_bccf" )  )
	{
		$path = dirname( __FILE__ ).'/addons';
		if( file_exists( $path ) )
		{
			$addons = dir( $path );
			while( false !== ( $entry = $addons->read() ) )
			{
				if( strlen( $entry ) > 3 && strtolower( pathinfo( $entry, PATHINFO_EXTENSION) ) == 'php'  && $entry != 'dex_allcals.inc.php')
				{
					require_once $addons->path.'/'.$entry;
				}
			}
		}
	}
}
dexbccf_loading_add_ons();


// code initialization, hooks
// -----------------------------------------

require_once 'sc-res-source.php';


register_activation_hook(__FILE__,'dex_bccf_install');
register_deactivation_hook( __FILE__, 'dex_bccf_remove' );

function dex_bccf_plugin_init() {
  load_plugin_textdomain( 'bccf', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
}
add_action('plugins_loaded', 'dex_bccf_plugin_init');

function dex_bccf_install($networkwide)  {
	global $wpdb;

	if (function_exists('is_multisite') && is_multisite()) {
		// check if it is a network activation - if so, run the activation function for each blog id
		if ($networkwide) {
	                $old_blog = $wpdb->blogid;
			// Get all blog ids
			$blogids = $wpdb->get_col( "SELECT blog_id FROM $wpdb->blogs" );
			foreach ($blogids as $blog_id) {
				switch_to_blog($blog_id);
				_dex_bccf_install();
			}
			switch_to_blog($old_blog);
			return;
		}
	}
	_dex_bccf_install();
}

function _dex_bccf_install() {
    global $wpdb;

    $sql = "CREATE TABLE ".$wpdb->prefix.DEX_BCCF_DISCOUNT_CODES_TABLE_NAME_NO_PREFIX." (
         id mediumint(9) NOT NULL AUTO_INCREMENT,
         cal_id mediumint(9) NOT NULL DEFAULT 1,
         code VARCHAR(250) DEFAULT '' NOT NULL,
         discount VARCHAR(250) DEFAULT '' NOT NULL,
         expires datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
         availability int(10) unsigned NOT NULL DEFAULT 0,
         used int(10) unsigned NOT NULL DEFAULT 0,
         UNIQUE KEY id (id)
         );";
    $wpdb->query($sql);

    $sql = "CREATE TABLE ".$wpdb->prefix.DEX_BCCF_SEASON_PRICES_TABLE_NAME_NO_PREFIX." (
         id mediumint(9) NOT NULL AUTO_INCREMENT,
         cal_id mediumint(9) NOT NULL DEFAULT 1,
         price VARCHAR(250) DEFAULT '' NOT NULL,
         date_from datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
         date_to datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
         UNIQUE KEY id (id)
         );";
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
         );";
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
                   "PRIMARY KEY (`".TDE_BCCFCONFIG_ID."`)); ";
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
                   "PRIMARY KEY (`".TDE_BCCFDATA_ID."`)) ;";
    $wpdb->query($sql);



    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
}

function dex_bccf_remove() {

}


/* Filter for placing the maps into the contents */

function dex_bccf_filter_content($atts) {
    global $wpdb;
    extract( shortcode_atts( array(
		'calendar' => '',
		'user' => '',
		'pages' => '',
	), $atts ) );

	/**
	 * Filters applied before generate the form,
	 * is passed as parameter an array with the forms attributes, and return the list of attributes
	 */
	$atts = apply_filters( 'dexbccf_pre_form',  $atts );

    if ($calendar != '')
        define ('DEX_BCCF_CALENDAR_FIXED_ID',$calendar);
    else if ($user != '')
    {
        $users = $wpdb->get_results( "SELECT user_login,ID FROM ".$wpdb->users." WHERE user_login='".esc_sql($user)."'" );
        if (isset($users[0]))
            define ('DEX_CALENDAR_USER',$users[0]->ID);
        else
            define ('DEX_CALENDAR_USER',0);
    }
    else
        define ('DEX_CALENDAR_USER',0);
    ob_start();
    dex_bccf_get_public_form($pages);
    $buffered_contents = ob_get_contents();
    ob_end_clean();

	/**
	 * Filters applied after generate the form,
	 * is passed as parameter the HTML code of the form with the corresponding <LINK> and <SCRIPT> tags,
	 * and returns the HTML code to includes in the webpage
	 */
	$buffered_contents = apply_filters( 'dexbccf_the_form', $buffered_contents,  $atts[ 'id' ] );

    return $buffered_contents;
}


function dex_bccf_filter_content_allcalendars($atts) {
    global $wpdb;
    extract( shortcode_atts( array(
		'calendar' => '',
		'id' => '',
		'pages' => '',
	), $atts ) );
	if ($calendar == '') $calendar = $id;
    if (!defined('DEX_CALENDAR_USER')) define ('DEX_CALENDAR_USER',0);
    ob_start();
    wp_enqueue_script( "jquery" );
    wp_enqueue_script( "jquery-ui-core" );
    wp_enqueue_script( "jquery-ui-datepicker" );
    $myrows = $wpdb->get_results( "SELECT * FROM ".DEX_BCCF_CONFIG_TABLE_NAME.($calendar!=''?" WHERE id=".$calendar:"") );
    if (!defined('DEX_AUTH_INCLUDE')) define('DEX_AUTH_INCLUDE', true);
    @include dirname( __FILE__ ) . '/addons/dex_allcals.inc.php';
    $buffered_contents = ob_get_contents();
    ob_end_clean();
    return $buffered_contents;
}

function dex_bccf_get_public_form($pages = '') {
    global $wpdb;

    if (defined('DEX_CALENDAR_USER') && DEX_CALENDAR_USER != 0)
        $myrows = $wpdb->get_results( "SELECT * FROM ".DEX_BCCF_CONFIG_TABLE_NAME." WHERE conwer=".DEX_CALENDAR_USER );
    else if (defined('DEX_BCCF_CALENDAR_FIXED_ID'))
        $myrows = $wpdb->get_results( "SELECT * FROM ".DEX_BCCF_CONFIG_TABLE_NAME." WHERE id=".DEX_BCCF_CALENDAR_FIXED_ID );
    else
        $myrows = $wpdb->get_results( "SELECT * FROM ".DEX_BCCF_CONFIG_TABLE_NAME." WHERE caldeleted<>'1'" );

    if (!count($myrows))
    {
        echo "Calendar isn't marked as 'Public' in the administration area or calendar ID is incorrect.";
        return;
    }
    define ('CP_BCCF_CALENDAR_ID',$myrows[0]->id);

    $previous_label = dex_bccf_get_option('vs_text_previousbtn', 'Previous');
    $previous_label = ($previous_label==''?'Previous':$previous_label);
    $next_label = dex_bccf_get_option('vs_text_nextbtn', 'Next');
    $next_label = ($next_label==''?'Next':$next_label);

    // for the additional services field if needed
    $dex_buffer = dex_bccf_get_services();

    $calendar_language = dex_bccf_get_option('calendar_language',DEX_BCCF_DEFAULT_CALENDAR_LANGUAGE);
    if ($calendar_language == '') $calendar_language = dex_bccf_autodetect_language();

    if (DEX_BCCF_DEFAULT_DEFER_SCRIPTS_LOADING)
    {
        wp_deregister_script('query-stringify');
        wp_register_script('query-stringify', plugins_url('/js/jQuery.stringify.js', __FILE__));

        wp_deregister_script('cp_contactformpp_validate_script');
        wp_register_script('cp_contactformpp_validate_script', plugins_url('/js/jquery.validate.js', __FILE__));

        wp_deregister_script('cp_contactformpp_rcalendar');
        wp_register_script('cp_contactformpp_rcalendar', plugins_url('/js/jquery.rcalendar.js', __FILE__));

        $dependencies = array("jquery","jquery-ui-core","jquery-ui-button","jquery-ui-datepicker","jquery-ui-widget","jquery-ui-position","jquery-ui-tooltip","query-stringify","cp_contactformpp_validate_script", "cp_contactformpp_rcalendar");
        if ($calendar_language != '') {
            wp_deregister_script('cp_contactformpp_rclang');
            wp_register_script('cp_contactformpp_rclang', plugins_url('/js/languages/jquery.ui.datepicker-'.$calendar_language.'.js', __FILE__));
            $dependencies[] = 'cp_contactformpp_rclang';
        }

        wp_enqueue_script( 'dex_bccf_builder_script',
        get_site_url( get_current_blog_id() ).'?bccf_resources=public',$dependencies, false, true );

        // localize script
        wp_localize_script('dex_bccf_builder_script', 'dex_bccf_fbuilder_config', array('obj'  	=>
        '{"pub":true,"messages": {
        	                	"required": "'.str_replace(array('"'),array('\\"'),__(dex_bccf_get_option('vs_text_is_required', DEX_BCCF_DEFAULT_vs_text_is_required),'bccf')).'",
        	                	"email": "'.str_replace(array('"'),array('\\"'),__(dex_bccf_get_option('vs_text_is_email', DEX_BCCF_DEFAULT_vs_text_is_email),'bccf')).'",
        	                	"datemmddyyyy": "'.str_replace(array('"'),array('\\"'),__(dex_bccf_get_option('vs_text_datemmddyyyy', DEX_BCCF_DEFAULT_vs_text_datemmddyyyy),'bccf')).'",
        	                	"dateddmmyyyy": "'.str_replace(array('"'),array('\\"'),__(dex_bccf_get_option('vs_text_dateddmmyyyy', DEX_BCCF_DEFAULT_vs_text_dateddmmyyyy),'bccf')).'",
        	                	"number": "'.str_replace(array('"'),array('\\"'),__(dex_bccf_get_option('vs_text_number', DEX_BCCF_DEFAULT_vs_text_number),'bccf')).'",
        	                	"digits": "'.str_replace(array('"'),array('\\"'),__(dex_bccf_get_option('vs_text_digits', DEX_BCCF_DEFAULT_vs_text_digits),'bccf')).'",
        	                	"max": "'.str_replace(array('"'),array('\\"'),__(dex_bccf_get_option('vs_text_max', DEX_BCCF_DEFAULT_vs_text_max),'bccf')).'",
        	                	"min": "'.str_replace(array('"'),array('\\"'),__(dex_bccf_get_option('vs_text_min', DEX_BCCF_DEFAULT_vs_text_min),'bccf')).'",
    	                    	"previous": "'.str_replace(array('"'),array('\\"'),$previous_label).'",
    	                    	"next": "'.str_replace(array('"'),array('\\"'),$next_label).'"
        	                }}'
        ));
    }
    else
    {
        wp_enqueue_script( "jquery" );
        wp_enqueue_script( "jquery-ui-core" );
        wp_enqueue_script( "jquery-ui-datepicker" );
    }

    $option_calendar_enabled = dex_bccf_get_option('calendar_enabled', DEX_BCCF_DEFAULT_CALENDAR_ENABLED);

    $button_label = dex_bccf_get_option('vs_text_submitbtn', 'Continue');
    $button_label = ($button_label==''?'Continue':$button_label);
    define('DEX_AUTH_INCLUDE', true);

    if (!DEX_BCCF_DEFAULT_DEFER_SCRIPTS_LOADING) {

        $prefix_ui = '';
        if (file_exists(dirname( __FILE__ ).'/../../../wp-includes/js/jquery/ui/jquery.ui.core.min.js'))
            $prefix_ui = 'jquery.ui.';

?>
<?php $plugin_url = plugins_url('', __FILE__); ?>
<script> if( typeof jQuery != 'undefined' ) var jQueryBK = jQuery.noConflict(); </script>
<script type='text/javascript' src='<?php echo $plugin_url.'/../../../wp-includes/js/jquery/jquery.js'; ?>'></script>
<script type='text/javascript' src='<?php echo $plugin_url.'/../../../wp-includes/js/jquery/ui/'.$prefix_ui.'core.min.js'; ?>'></script>
<script type='text/javascript' src='<?php echo $plugin_url.'/../../../wp-includes/js/jquery/ui/'.$prefix_ui.'datepicker.min.js'; ?>'></script>
<script type='text/javascript' src='<?php echo $plugin_url.'/../../../wp-includes/js/jquery/ui/'.$prefix_ui.'widget.min.js'; ?>'></script>
<script type='text/javascript' src='<?php echo $plugin_url.'/../../../wp-includes/js/jquery/ui/'.$prefix_ui.'position.min.js'; ?>'></script>
<script type='text/javascript' src='<?php echo $plugin_url.'/../../../wp-includes/js/jquery/ui/'.$prefix_ui.'tooltip.min.js'; ?>'></script>
<script>
        var myjQuery = jQuery.noConflict( );
        if( typeof jQueryBK != 'undefined' ) {jQuery = jQueryBK;};
</script>
<script type='text/javascript' src='<?php echo plugins_url('js/jQuery.stringify.js', __FILE__); ?>'></script>
<script type='text/javascript' src='<?php echo plugins_url('js/jquery.validate.js', __FILE__); ?>'></script>
<?php if ($calendar_language != '') { ?><script type="text/javascript" src="<?php echo plugins_url('/js/languages/jquery.ui.datepicker-'.$calendar_language.'.js', __FILE__); ?>"></script><?php } ?>
<script type='text/javascript' src='<?php echo plugins_url('js/jquery.rcalendar.js', __FILE__); ?>'></script>
<script type='text/javascript'>
/* <![CDATA[ */
var dex_bccf_fbuilder_config = {"obj":"{\"pub\":true,\"messages\": {\n    \t                \t\"required\": \"This field is required.\",\n    \t                \t\"email\": \"Please enter a valid email address.\",\n    \t                \t\"datemmddyyyy\": \"Please enter a valid date with this format(mm\/dd\/yyyy)\",\n    \t                \t\"dateddmmyyyy\": \"Please enter a valid date with this format(dd\/mm\/yyyy)\",\n    \t                \t\"number\": \"Please enter a valid number.\",\n    \t                \t\"digits\": \"Please enter only digits.\",\n    \t                \t\"max\": \"Please enter a value less than or equal to {0}.\",\n    \t                \t\"min\": \"Please enter a value greater than or equal to {0}.\"\n    \t                }}"};
/* ]]> */
</script>
<?php
    }
    @include dirname( __FILE__ ) . '/sc-res-scheduler.php';
    if (!DEX_BCCF_DEFAULT_DEFER_SCRIPTS_LOADING) {
        ?><script type='text/javascript' src='<?php echo get_site_url( get_current_blog_id() ).'?bccf_resources=public'; ?>'></script><?php
    }
}


function dex_bccf_show_booking_form($id = "")
{
    if ($id != '')
        define ('DEX_BCCF_CALENDAR_FIXED_ID',$id);
    define('DEX_AUTH_INCLUDE', true);
    @include dirname( __FILE__ ) . '/sc-res-scheduler.php';
}


/* Code for the admin area */

if ( is_admin() ) {
    add_action('media_buttons', 'set_dex_bccf_insert_button', 100);
    add_action('admin_enqueue_scripts', 'set_dex_bccf_insert_adminScripts', 1);
    add_action('admin_menu', 'dex_bccf_admin_menu');

    function dex_bccf_admin_menu() {
        add_options_page('Reservation Forms & Lists', 'Reservation Forms & Lists', 'manage_options', 'dex_bccf', 'dex_bccf_html_post_page' );
        add_menu_page( 'Reservation Forms & Lists', 'Reservations', 'edit_pages', 'dex_bccf', 'dex_bccf_html_post_page', 'dashicons-feedback', 3.5 );
    }
}
else
{
    add_shortcode( 'CP_BCCF_FORM', 'dex_bccf_filter_content' );
    add_shortcode( 'CP_BCCF_ALLCALS', 'dex_bccf_filter_content_allcalendars' );
}

function dex_bccf_html_post_page() {
    if (isset($_GET["cal"]) && $_GET["cal"] != '')
    {
        if (isset($_GET["list"]) && $_GET["list"] == '1')
            @include_once dirname( __FILE__ ) . '/sc-res-admin-int-bookings-list.php';
        else if (isset($_GET["edit"]) && $_GET["edit"] != '')
            @include_once dirname( __FILE__ ) . '/sc-res-admin-int-edit-booking.php';
        else if (isset($_GET["list2"]) && $_GET["list2"] == '1')
            @include_once dirname( __FILE__ ) . '/sc-res-admin-int-non-completed-bookings-list.php';
        else
            @include_once dirname( __FILE__ ) . '/sc-res-admin-int.php';
    }
    else
        @include_once dirname( __FILE__ ) . '/sc-res-admin-int-calendar-list.php';

}

function set_dex_bccf_insert_button() {
    print '<a href="javascript:send_to_editor(\'[CP_BCCF_FORM]\')" title="'.__('Insert Booking Calendar','bccf').'"><img hspace="5" src="'.plugins_url('/images/dex_apps.gif', __FILE__).'" alt="'.__('Insert Booking Calendar','bccf').'" /></a>';
}

function set_dex_bccf_insert_adminScripts($hook) {
    if (isset($_GET["page"]) && $_GET["page"] == "dex_bccf")
    {
        wp_deregister_script('query-stringify');
        wp_register_script('query-stringify', plugins_url('/js/jQuery.stringify.js', __FILE__));
        wp_deregister_script('cp_contactformpp_rcalendar');
        wp_register_script('cp_contactformpp_rcalendar', plugins_url('/js/jquery.rcalendar.js', __FILE__));
        wp_deregister_script('cp_contactformpp_rcalendaradmin');
        wp_register_script('cp_contactformpp_rcalendaradmin', plugins_url('/js/jquery.rcalendaradmin.js', __FILE__));
        wp_enqueue_script( 'dex_bccf_builder_script', get_site_url( get_current_blog_id() ).'?bccf_resources=admin',array("jquery","jquery-ui-core","jquery-ui-sortable","jquery-ui-tabs","jquery-ui-dialog","jquery-ui-droppable","jquery-ui-button","jquery-ui-datepicker","query-stringify","cp_contactformpp_rcalendar","cp_contactformpp_rcalendaradmin") );

        wp_enqueue_style('jquery-style', 'http://ajax.googleapis.com/ajax/libs/jqueryui/1.8.2/themes/smoothness/jquery-ui.css');
    }
    if( 'post.php' != $hook  && 'post-new.php' != $hook )
        return;
}

function dex_bccf_get_services() {
    $dex_buffer = array();
    for ($k=1;$k<=DEX_BCCF_DEFAULT_SERVICES_FIELDS; $k++)
    {
        $dex_buffer[$k] = "";
        $services = explode("\n",dex_bccf_get_option('cp_cal_checkboxes'.$k, DEX_BCCF_DEFAULT_CP_CAL_CHECKBOXES));
        foreach ($services as $item)
            if (trim($item) != '')
            {
                $dex_buffer[$k] .= '<option value="'.esc_attr($item).'">'.__(trim(substr($item,strpos($item,"|")+1)),'bccf').'</option>';
            }
    }
    return $dex_buffer;
}

function dex_bccf_echo_services($dex_buffer) {
    for ($k=1;$k<=DEX_BCCF_DEFAULT_SERVICES_FIELDS; $k++)
      if ($dex_buffer[$k] != '')
      {
         echo '<div class="fields" id="field-c1"><label>';
         $flabel = dex_bccf_get_option('cp_cal_checkboxes_label'.$k, 'Service');
         if ($flabel == '') $flabel = 'Service';
         echo __($flabel,'bccf'); //$l_service;
         echo ':</label><div class="dfield"><select name="services'.$k.'"  id="dex_services'.$k.'" onchange="updatedate()">'.$dex_buffer[$k].'</select></div><div class="clearer"></div></div>';
      }
}

function dex_bccf_export_iCal() {
    global $wpdb;
    header("Content-type: application/octet-stream");
    header("Content-Disposition: attachment; filename=events".date("Y-M-D_H.i.s").".ics");

    define('DEX_CAL_TIME_ZONE_MODIFY',"");

    echo "BEGIN:VCALENDAR\n";
    echo "PRODID:-//CodePeople//Booking Calendar Contact Form for WordPress//EN\n";
    echo "VERSION:2.0\n";
    echo "CALSCALE:GREGORIAN\n";
    echo "METHOD:PUBLISH\n";
    echo "X-WR-CALNAME:Bookings\n";
    echo "X-WR-TIMEZONE:Europe/London\n";
    echo "BEGIN:VTIMEZONE\n";
    echo "TZID:Europe/Stockholm\n";
    echo "X-LIC-LOCATION:Europe/London\n";
    echo "BEGIN:DAYLIGHT\n";
    echo "TZOFFSETFROM:+0000\n";
    echo "TZOFFSETTO:+0100\n";
    echo "TZNAME:CEST\n";
    echo "DTSTART:19700329T020000\n";
    echo "RRULE:FREQ=YEARLY;BYMONTH=3;BYDAY=-1SU\n";
    echo "END:DAYLIGHT\n";
    echo "BEGIN:STANDARD\n";
    echo "TZOFFSETFROM:+0100\n";
    echo "TZOFFSETTO:+0000\n";
    echo "TZNAME:CET\n";
    echo "DTSTART:19701025T030000\n";
    echo "RRULE:FREQ=YEARLY;BYMONTH=10;BYDAY=-1SU\n";
    echo "END:STANDARD\n";
    echo "END:VTIMEZONE\n";

    $events = $wpdb->get_results( "SELECT * FROM ".DEX_BCCF_CALENDARS_TABLE_NAME." WHERE reservation_calendar_id=".intval($_GET["id"])." ORDER BY datatime_s ASC" );
    foreach ($events as $event)
    {

        echo "BEGIN:VEVENT\n";
        //echo "DTSTART:".date("Ymd",strtotime($event->datatime_s.DEX_CAL_TIME_ZONE_MODIFY))."T".date("His",strtotime($event->datatime_s.DEX_CAL_TIME_ZONE_MODIFY))."Z\n";
        //echo "DTEND:".date("Ymd",strtotime($event->datatime_e.DEX_CAL_TIME_ZONE_MODIFY))."T".date("His",strtotime($event->datatime_e.DEX_CAL_TIME_ZONE_MODIFY." +15 minutes"))."Z\n";
        echo "DTSTART;VALUE=DATE:".date("Ymd",strtotime($event->datatime_s.DEX_CAL_TIME_ZONE_MODIFY))."\n";
        echo "DTEND;VALUE=DATE:".date("Ymd",strtotime($event->datatime_e.DEX_CAL_TIME_ZONE_MODIFY.(!$mode?"":" +1 day")))."\n";
        echo "DTSTAMP:".date("Ymd",strtotime($event->datatime_s.DEX_CAL_TIME_ZONE_MODIFY))."T".date("His",strtotime($event->datatime_s.DEX_CAL_TIME_ZONE_MODIFY))."Z\n";
        echo "UID:uid".$event->id."@".$_SERVER["SERVER_NAME"]."\n";
        echo "CREATED:".date("Ymd",strtotime($event->datatime_s.DEX_CAL_TIME_ZONE_MODIFY))."T".date("His",strtotime($event->datatime_s.DEX_CAL_TIME_ZONE_MODIFY))."Z\n";
        echo "DESCRIPTION:".str_replace("<br>",'\n',str_replace("<br />",'\n',str_replace("\r",'',str_replace("\n",'\n',$event->description)) ))."\n";
        echo "LAST-MODIFIED:".date("Ymd",strtotime($event->datatime_s.DEX_CAL_TIME_ZONE_MODIFY))."T".date("His",strtotime($event->datatime_s.DEX_CAL_TIME_ZONE_MODIFY))."Z\n";
        echo "LOCATION:\n";
        echo "SEQUENCE:0\n";
        echo "STATUS:CONFIRMED\n";
        echo "SUMMARY:Booking from ".str_replace("\n",'\n',$event->title)."\n";
        echo "TRANSP:OPAQUE\n";
        echo "END:VEVENT\n";


    }
    echo 'END:VCALENDAR';
    exit;
}


/* hook for checking posted data for the admin area */


add_action( 'init', 'dex_bccf_check_posted_data', 11 );

function dex_bccf_check_posted_data()
{
    global $wpdb;

	if( isset( $_REQUEST[ 'bccf_resources' ] ) )
	{
		if( $_REQUEST[ 'bccf_resources' ] == 'admin' )
		{
			require_once dirname( __FILE__ ).'/js/fbuilder-loader-admin.php';
		}
		else
		{
			require_once dirname( __FILE__ ).'/js/fbuilder-loader-public.php';
		}
		exit;
	}

    if (isset($_GET["dex_item"]) && $_GET["dex_item"] != '')
        $_POST["dex_item"] = $_GET["dex_item"];
    if (!defined('CP_BCCF_CALENDAR_ID') && isset($_POST["dex_item"]) && $_POST["dex_item"] != '')
        define ('CP_BCCF_CALENDAR_ID',intval($_POST["dex_item"]));

    dex_bccf_check_reminders();

    // define which action is being requested
    //-------------------------------------------------
    if (isset($_GET["dex_bccf"]) && $_GET["dex_bccf"] == 'getcost')
    {
        $default_price = dex_bccf_get_option('request_cost', DEX_BCCF_DEFAULT_COST);
        $services_formatted = array();
        for ($k=1;$k<=DEX_BCCF_DEFAULT_SERVICES_FIELDS; $k++)
        {
            $services_formatted[$k] = array();
            if (isset($_GET["ser".$k]))
                $services_formatted[$k] = explode("|",$_GET["ser".$k]);
        }
        echo number_format (dex_bccf_caculate_price_overall(strtotime($_GET["from"]), strtotime($_GET["to"]), $_POST["dex_item"], $default_price, $services_formatted), 2, ".", ",");
        // echo "<br /><b>Selected period:</b > ".date("Y/m/d",strtotime($_GET["from"]))." - ".date("Y/m/d",strtotime($_GET["to"]));
        exit;
    }

    if (isset($_GET["dex_bccf"]) && $_GET["dex_bccf"] == 'getservices')
    {
        dex_bccf_echo_services(dex_bccf_get_services());
        exit;
    }

    if (isset($_GET["dex_bccf"]) && $_GET["dex_bccf"] == 'calfeed')
        dex_bccf_export_iCal();

    if (isset($_GET["dex_bccf"]) && $_GET["dex_bccf"] == 'loadcoupons')
        dex_bccf_load_discount_codes();

    if (isset( $_GET['bccf_appointments_csv'] ) && is_admin() )
    {
        dex_bccf_appointments_export_csv();
        return;
    }

    if (isset($_GET["dex_bccf"]) && $_GET["dex_bccf"] == 'loadseasonprices')
        dex_bccf_load_season_prices();

    if ( 'POST' == $_SERVER['REQUEST_METHOD'] && isset( $_POST['dex_bccf_post_options'] ) && is_admin() )
    {
        dex_bccf_save_options();
        return;
    }

    // if this isn't the expected post and isn't the captcha verification then nothing to do
	if ( 'POST' != $_SERVER['REQUEST_METHOD'] || ! isset( $_POST['dex_bccf_post'] ) )
		if ( 'GET' != $_SERVER['REQUEST_METHOD'] || !isset( $_GET['hdcaptcha_dex_bccf_post'] ) )
		    return;

    // captcha verification
    //-------------------------------------------------
    @session_start();
    if (!isset($_GET['hdcaptcha_dex_bccf_post']) ||$_GET['hdcaptcha_dex_bccf_post'] == '') $_GET['hdcaptcha_dex_bccf_post'] = @$_POST['hdcaptcha_dex_bccf_post'];
    if (
           !apply_filters( 'dexbccf_valid_submission', true) ||
           (
               (dex_bccf_get_option('dexcv_enable_captcha', TDE_BCCFDEFAULT_dexcv_enable_captcha) != 'false') &&
               ( (strtolower($_GET['hdcaptcha_dex_bccf_post']) != strtolower($_SESSION['rand_code'])) ||
                 ($_SESSION['rand_code'] == '')
               )
           )
       )
    {
        $_SESSION['rand_code'] = '';
        echo 'captchafailed';
        exit;
    }

	// if this isn't the real post (it was the captcha verification) then echo ok and exit
    if ( 'POST' != $_SERVER['REQUEST_METHOD'] || ! isset( $_POST['dex_bccf_post'] ) )
	{
	    echo 'ok';
        exit;
	}

    $_SESSION['rand_code'] = '';



    // get calendar and selected date
    //-------------------------------------------------
    $selectedCalendar = $_POST["dex_item"];
    $selectedCalendar_sfx = 'calarea'.$selectedCalendar;
    $option_calendar_enabled = dex_bccf_get_option('calendar_enabled', DEX_BCCF_DEFAULT_CALENDAR_ENABLED);
    if ($option_calendar_enabled != 'false')
    {
        $_POST["dateAndTime_s"] =  $_POST["selYear_start".$selectedCalendar_sfx]."-".$_POST["selMonth_start".$selectedCalendar_sfx]."-".$_POST["selDay_start".$selectedCalendar_sfx];
        $_POST["dateAndTime_e"] =  $_POST["selYear_end".$selectedCalendar_sfx]."-".$_POST["selMonth_end".$selectedCalendar_sfx]."-".$_POST["selDay_end".$selectedCalendar_sfx];

        if (dex_bccf_get_option('calendar_dateformat', DEX_BCCF_DEFAULT_CALENDAR_DATEFORMAT))
        {
            $_POST["Date_s"] = date("d/m/Y",strtotime($_POST["dateAndTime_s"]));
            $_POST["Date_e"] = date("d/m/Y",strtotime($_POST["dateAndTime_e"]));
        }
        else
        {
            $_POST["Date_s"] = date("m/d/Y",strtotime($_POST["dateAndTime_s"]));
            $_POST["Date_e"] = date("m/d/Y",strtotime($_POST["dateAndTime_e"]));
        }

        // calculate days
        $days = round(
                       (strtotime($_POST["dateAndTime_e"]) - strtotime($_POST["dateAndTime_s"])) / (24 * 60 * 60)
                     );
        if (dex_bccf_get_option('calendar_mode',DEX_BCCF_DEFAULT_CALENDAR_MODE) == 'false')
            $days++;
    }
    else
    {
        $days = 1;
        $_POST["dateAndTime_s"] = date("Y-m-d", time());
        $_POST["dateAndTime_e"] = date("Y-m-d", time());
        $_POST["Date_s"] = date("m/d/Y",strtotime($_POST["dateAndTime_s"]));
        $_POST["Date_e"] = date("m/d/Y",strtotime($_POST["dateAndTime_e"]));
    }

    $params = array();

    $services_formatted = array();
    $services_text = "";
    for ($k=1;$k<=DEX_BCCF_DEFAULT_SERVICES_FIELDS; $k++)
    {
        $services_formatted[$k] = array();
        if (isset($_POST["services".$k]))
        {
            $services_formatted[$k] = explode("|",$_POST["services".$k]);
            $services_text .= dex_bccf_get_option('cp_cal_checkboxes_label'.$k, 'Service').": ".trim($services_formatted[$k][1])."\n\n";
            $params["service".$k] = dex_bccf_get_option('cp_cal_checkboxes_label'.$k, 'Service').": ".trim($services_formatted[$k][1]);
        }
    }
    $services_text = trim($services_text);


    // calculate price from services field or dates
    //-------------------------------------------------
    $price = dex_bccf_caculate_price_overall(strtotime($_POST["dateAndTime_s"]), strtotime($_POST["dateAndTime_e"]), CP_BCCF_CALENDAR_ID, @$default_price, $services_formatted);

    $taxes = trim(str_replace("%","",dex_bccf_get_option('request_taxes', '0')));

    // check discount codes
    //-------------------------------------------------
    $discount_note = "";
    $coupon = false;
    $codes = $wpdb->get_results( "SELECT * FROM ".DEX_BCCF_DISCOUNT_CODES_TABLE_NAME." WHERE code='".esc_sql(@$_POST["couponcode"])."' AND expires>='".date("Y-m-d")." 00:00:00' AND `cal_id`=".CP_BCCF_CALENDAR_ID);
    if (count($codes))
    {
        $coupon = $codes[0];
        $price = number_format (floatval ($price) - $price*$coupon->discount/100,2);
        $discount_note = " (".$coupon->discount."% discount applied)";
    }


    // get form info
    //---------------------------
    require_once(ABSPATH . "wp-admin" . '/includes/file.php');
    $form_data = json_decode(dex_bccf_cleanJSON(dex_bccf_get_option('form_structure', DEX_BCCF_DEFAULT_form_structure)));
    $fields = array();
    foreach ($form_data[0] as $item)
    {
        $fields[$item->name] = $item->title;
        if ($item->ftype == 'fPhone') // join fields for phone fields
        {
            $_POST[$item->name] = '';
            for($i=0; $i<=substr_count($item->dformat," "); $i++)
                $_POST[$item->name] .= ($_POST[$item->name."_".$i]!=''?($i==0?'':'-').$_POST[$item->name."_".$i]:'');
        }
    }

    // grab posted data
    //---------------------------
    $buffer = "";
    $params["days"] = $days;
    $params["startdate"] = $_POST["Date_s"];
    $params["enddate"] = $_POST["Date_e"];
    $params["discount"] = @$discount_note;
    $params["coupon"] = ($coupon?$coupon->code:"");
    $params["service"] = $services_text;
    $params["totalcost"] = dex_bccf_get_option('currency', DEX_BCCF_DEFAULT_CURRENCY).' '.$price;

    foreach ($_POST as $item => $value)
        if (isset($fields[$item]))
        {
            $buffer .= $fields[$item] . ": ". (is_array($value)?(implode(", ",$value)):($value)) . "\n\n";
            $params[$item] = $value;
        }

    foreach ($_FILES as $item => $value)
    {
        //$item = str_replace( $sequence,'',$item );
		if ( isset( $fields[ $item ] ) )
        {
			$files_names_arr = array();
			$files_links_arr = array();
			$files_urls_arr  = array();
			for( $f = 0; $f < count( $value[ 'name' ] ); $f++ )
			{
				if( !empty( $value[ 'name' ][ $f ] ) )
				{
					$uploaded_file = array(
						'name' 		=> $value[ 'name' ][ $f ],
						'type' 		=> $value[ 'type' ][ $f ],
						'tmp_name' 	=> $value[ 'tmp_name' ][ $f ],
						'error' 	=> $value[ 'error' ][ $f ],
						'size' 		=> $value[ 'size' ][ $f ],
					);
					if( dex_bccf_check_upload( $uploaded_file ) )
					{
						$movefile = wp_handle_upload( $uploaded_file, array( 'test_form' => false ) );
						if ( empty( $movefile[ 'error' ] ) )
						{
							$files_links_arr[] = $params[ $item."_link" ][ $f ] = $movefile["file"];
							$files_urls_arr[]  = $params[ $item."_url" ][ $f ] = $movefile["url"];
							$files_names_arr[] = $uploaded_file[ 'name' ];

							/**
							 * Action called when the file is uploaded, the file's data is passed as parameter
							 */
							do_action( 'dexbccf_file_uploaded', $movefile );
						} //else echo $movefile[ 'error' ];
					}
				}
			}
			$joinned_files_names = implode( ", ", $files_names_arr );
			$buffer .= $fields[ $item ] . ": ". $joinned_files_names . "\n\n";
			$params[ $item ] = $joinned_files_names;
			//$params[ $item."_links"] = implode( ",",  $files_links_arr );
			//$params[ $item."_urls"] = implode( ",",  $files_urls_arr );
		}
	}

    $buffer_A = trim($buffer)."\n\n";
    $buffer_A .= ($services_text != ''?$services_text."\n\n":"").
                 ($coupon?"\nCoupon code: ".$coupon->code.$discount_note."\n\n":"");

    if ($price != 0) $buffer_A .= 'Total Cost: '.dex_bccf_get_option('currency', DEX_BCCF_DEFAULT_CURRENCY).' '.$price."\n\n";

    $buffer = $_POST["selMonth_start".$selectedCalendar_sfx]."/".$_POST["selDay_start".$selectedCalendar_sfx]."/".$_POST["selYear_start".$selectedCalendar_sfx]."-".
              $_POST["selMonth_end".$selectedCalendar_sfx]."/".$_POST["selDay_end".$selectedCalendar_sfx]."/".$_POST["selYear_end".$selectedCalendar_sfx]."\n".
              $buffer_A."*-*\n";

    $originalprice = $price;
    if (dex_bccf_get_option('calendar_depositenable','0') == '1')
    {
        if (dex_bccf_get_option('calendar_deposittype','0') == '0')
            $price = round($price * dex_bccf_get_option('calendar_depositamount','0')/100,2);
        else
            $price =  dex_bccf_get_option('calendar_depositamount','0');
    }

    $params["initialpayment"] = $price;
    $params["finalpayment"] = $originalprice - $price;

	/**
	 * Action called before insert the data into database.
	 * To the function is passed an array with submitted data.
	 */
	do_action( 'dexbccf_process_data_before_insert', $params );

    // insert into database
    //---------------------------
    $to = dex_bccf_get_option('cu_user_email_field', DEX_BCCF_DEFAULT_cu_user_email_field);
    $rows_affected = $wpdb->insert( DEX_BCCF_TABLE_NAME, array( 'calendar' => $selectedCalendar,
                                                                        'time' => current_time('mysql'),
                                                                        'booked_time_s' => $_POST["Date_s"],
                                                                        'booked_time_e' => $_POST["Date_e"],
                                                                        'booked_time_unformatted_s' => $_POST["dateAndTime_s"],
                                                                        'booked_time_unformatted_e' => $_POST["dateAndTime_e"],
                                                                        'question' => $buffer_A,
                                                                        'notifyto' => "".$_POST[$to],
                                                                        'buffered_date' => serialize($params)
                                                                         ) );
    if (!$rows_affected)
    {
        echo 'Error saving data! Please try again.';
        echo '<br /><br />Error debug information: '.mysql_error();
        exit;
    }


    $myrows = $wpdb->get_results( "SELECT MAX(id) as max_id FROM ".DEX_BCCF_TABLE_NAME );

 	// save data here
    $item_number = $myrows[0]->max_id;

	// Call action for data processing
	//---------------------------------
	$params[ 'itemnumber' ] = $item_number;

	/**
	 * Action called after inserted the data into database.
	 * To the function is passed an array with submitted data.
	 */
	do_action( 'dexbccf_process_data', $params );


    $paypal_optional = (dex_bccf_get_option('enable_paypal',DEX_BCCF_DEFAULT_ENABLE_PAYPAL) == '2');


    if (floatval($price) > 0 && dex_bccf_get_option('enable_paypal',DEX_BCCF_DEFAULT_ENABLE_PAYPAL) == '3')
    {
       header('Location: https://www.beanstream.com/scripts/payment/payment.asp?merchant_id='.dex_bccf_get_option('enable_beanstream_id', '').
                                                                           '&trnOrderNumber='.$item_number.
                                                                           '&trnAmount='.$price.
                                                                           '&approvedPage='.urlencode( cp_bccf_get_FULL_site_url().'/?dex_bccf=ipn&ipncheck=1&itemnumber='.$item_number.'&beanstrean=1').
                                                                           '&declinedPage='.urlencode( dex_bccf_get_option('url_cancel', DEX_BCCF_DEFAULT_CANCEL_URL) )
                                                                           );
       exit;
    }
    else if (floatval($price) > 0 && dex_bccf_get_option('enable_paypal',DEX_BCCF_DEFAULT_ENABLE_PAYPAL) && ( !$paypal_optional || (@$_POST["bccf_payment_option_paypal"] == '1') ))
    {
?>
<html>
<head><title>Redirecting to Paypal...</title></head>
<body>
<form action="https://www.paypal.com/cgi-bin/webscr" name="ppform3" method="post">
<input type="hidden" name="cmd" value="_xclick" />
<input type="hidden" name="business" value="<?php echo dex_bccf_get_option('paypal_email', DEX_BCCF_DEFAULT_PAYPAL_EMAIL); ?>" />
<input type="hidden" name="item_name" value="<?php echo dex_bccf_get_option('paypal_product_name', DEX_BCCF_DEFAULT_PRODUCT_NAME).($services_text!=''?", ".str_replace("\n\n",", ",$services_text).". ":"").$discount_note; ?>" />
<input type="hidden" name="item_number" value="<?php echo $item_number; ?>" />
<input type="hidden" name="amount" value="<?php echo $price; ?>" />
<?php if ($taxes != '0' && $taxes != '') { ?>
<input type="hidden" name="tax_rate"  value="<?php echo $taxes; ?>" />
<?php } ?>
<input type="hidden" name="charset" value="utf-8">
<input type="hidden" name="page_style" value="Primary" />
<input type="hidden" name="no_shipping" value="1" />
<input type="hidden" name="return" value="<?php echo dex_bccf_get_option('url_ok', DEX_BCCF_DEFAULT_OK_URL); ?>">
<input type="hidden" name="cancel_return" value="<?php echo dex_bccf_get_option('url_cancel', DEX_BCCF_DEFAULT_CANCEL_URL); ?>" />
<input type="hidden" name="no_note" value="1" />
<input type="hidden" name="currency_code" value="<?php echo dex_bccf_get_option('currency', DEX_BCCF_DEFAULT_CURRENCY); ?>" />
<input type="hidden" name="lc" value="<?php echo dex_bccf_get_option('paypal_language', DEX_BCCF_DEFAULT_PAYPAL_LANGUAGE); ?>" />
<input type="hidden" name="bn" value="NetFactorSL_SI_Custom" />
<input type="hidden" name="notify_url" value="<?php echo cp_bccf_get_FULL_site_url(); ?>/?dex_bccf=ipn&ipncheck=1&itemnumber=<?php echo $item_number; ?>" />
<input type="hidden" name="ipn_test" value="1" />
<input class="pbutton" type="hidden" value="Buy Now" /></div>
</form>
<script type="text/javascript">
document.ppform3.submit();
</script>
</body>
</html>
<?php
        exit();
    }
    else
    {
        dex_process_ready_to_go_bccf($item_number, "", $params);
        $_SESSION[ 'cp_cff_form_data' ] = $item_number;
        $redirect = true;

		/**
		 * Filters applied to decide if the website should be redirected to the thank you page after submit the form,
		 * pass a boolean as parameter and returns a boolean
		 */
        $redirect = apply_filters( 'dexbccf_redirect', $redirect );

        if( $redirect )
        {
            $location = dex_bccf_get_option('url_ok', DEX_BCCF_DEFAULT_OK_URL);
            header("Location: ".$location);
            exit;
        }
    }

}


function dex_bccf_check_upload($uploadfiles) {
    $filetmp = $uploadfiles['tmp_name'];
    //clean filename and extract extension
    $filename = $uploadfiles['name'];
    // get file info
    $filetype = wp_check_filetype( basename( $filename ), null );

    if ( in_array ($filetype["ext"],array("php","asp","aspx","cgi","pl","perl","exe")) )
        return false;
    else
        return true;
}


function dex_bccf_caculate_price_overall($startday, $enddate, $calendar, $default_price, $services_formatted)
{
    //if ($service)
    //    $services_formatted = explode("|",$service);
    //else
    //    $services_formatted = array();

    $days = round(
                   ($enddate - $startday) / (24 * 60 * 60)
                  );
    if (dex_bccf_get_option('calendar_mode',DEX_BCCF_DEFAULT_CALENDAR_MODE) == 'false')
        $days++;

    $min_nights = intval(dex_bccf_get_option('calendar_suplementminnight','0'));
    $max_nights = intval(dex_bccf_get_option('calendar_suplementmaxnight','365'));
    $suplement = 0;

    if ($days >= $min_nights && $days <= $max_nights)
        $suplement  = floatval(dex_bccf_get_option('calendar_suplement', 0));

    $default_price = dex_bccf_get_option('request_cost', DEX_BCCF_DEFAULT_COST);
    $price = dex_bccf_caculate_price($startday, $enddate, CP_BCCF_CALENDAR_ID, $default_price);

    for ($k=1;$k<=DEX_BCCF_DEFAULT_SERVICES_FIELDS; $k++)
    {
        $option_services = dex_bccf_get_option('cp_cal_checkboxes_type'.$k, DEX_BCCF_DEFAULT_CP_CAL_CHECKBOXES_TYPE);
        if ($services_formatted[$k] && ($option_services == '1' || $option_services == '2'))
        {
            $price = trim($services_formatted[$k][0]);
            if ($option_services == '1')
                $price = floatval ($price)*$days;
            else
                $price = floatval ($price);
        }
        else
        {
            if ($services_formatted[$k])
            {
                $price_service = trim($services_formatted[$k][0]);
                if ($option_services == '4')
                    $price += floatval ($price_service)*$days;
                else
                    $price += floatval ($price_service);
            }
        }
    }
    return round($price+$suplement,2);
}

function dex_bccf_caculate_price($startday, $enddate, $calendar, $default_price) {
    global $wpdb;

    $default_price_array = explode (';', $default_price);
    $default_price = $default_price_array[0];
    $season_prices = array();
    $days = 0;
    $price = 0;
    $codes = $wpdb->get_results( 'SELECT * FROM '.$wpdb->prefix.DEX_BCCF_SEASON_PRICES_TABLE_NAME_NO_PREFIX.' WHERE `cal_id`='.$calendar);
    $mode = (dex_bccf_get_option('calendar_mode',DEX_BCCF_DEFAULT_CALENDAR_MODE) == 'false');
    while (
           (($enddate>$startday) && !$mode) ||
           (($enddate>=$startday) && $mode)
           )
    {
        $daily_price = $default_price;
        $sprice = array();
        foreach ($codes as $value)
        {
           $sfrom = strtotime($value->date_from);
           $sto = strtotime($value->date_to);
           if ($startday >= $sfrom && $startday <= $sto)
           {
               $sprice = explode (';', $value->price);
               $daily_price = $sprice[0];
           }
        }
        $season_prices[] = $sprice;
        $price += $daily_price;
        $startday = strtotime (date("Y-m-d", $startday)." +1 day"); //60*60*24;
        $days++;
    }

    if (trim(@$default_price_array[$days]))
        $price = trim($default_price_array[$days]);
    if (trim(@$season_prices[0][$days]))
        $price = trim($season_prices[0][$days]);
    if (trim(@$season_prices[count($season_prices)-1][$days])
        &&
        floatval($price) < floatval(trim(@$season_prices[count($season_prices)-1][$days]))) // get higher price if different seasons
        $price = trim($season_prices[count($season_prices)-1][$days]);

    return $price;
}

function dex_bccf_load_discount_codes() {
    global $wpdb;

    if ( ! current_user_can('edit_pages') ) // prevent loading coupons from outside admin area
    {
        echo 'No enough privilegies to load this content.';
        exit;
    }

    if (!defined('CP_BCCF_CALENDAR_ID'))
        define ('CP_BCCF_CALENDAR_ID',$_GET["dex_item"]);

    if (isset($_GET["add"]) && $_GET["add"] == "1")
        $wpdb->insert( DEX_BCCF_DISCOUNT_CODES_TABLE_NAME, array('cal_id' => CP_BCCF_CALENDAR_ID,
                                                                         'code' => $_GET["code"],
                                                                         'discount' => $_GET["discount"],
                                                                         'expires' => $_GET["expires"],
                                                                         ));
    if (isset($_GET["delete"]) && $_GET["delete"] == "1")
        $wpdb->query( $wpdb->prepare( "DELETE FROM ".DEX_BCCF_DISCOUNT_CODES_TABLE_NAME." WHERE id = %d", $_GET["code"] ));

    $codes = $wpdb->get_results( 'SELECT * FROM '.DEX_BCCF_DISCOUNT_CODES_TABLE_NAME.' WHERE `cal_id`='.CP_BCCF_CALENDAR_ID);
    if (count ($codes))
    {
        echo '<table>';
        echo '<tr>';
        echo '  <th style="padding:2px;background-color: #cccccc;font-weight:bold;">Cupon Code</th>';
        echo '  <th style="padding:2px;background-color: #cccccc;font-weight:bold;">Discount %</th>';
        echo '  <th style="padding:2px;background-color: #cccccc;font-weight:bold;">Valid until</th>';
        echo '  <th style="padding:2px;background-color: #cccccc;font-weight:bold;">Options</th>';
        echo '</tr>';
        foreach ($codes as $value)
        {
           echo '<tr>';
           echo '<td>'.$value->code.'</td>';
           echo '<td>'.$value->discount.'</td>';
           echo '<td>'.substr($value->expires,0,10).'</td>';
           echo '<td>[<a href="javascript:dex_delete_coupon('.$value->id.')">Delete</a>]</td>';
           echo '</tr>';
        }
        echo '</table>';
    }
    else
        echo 'No discount codes listed for this calendar yet.';
    exit;
}


function dex_bccf_load_season_prices() {
    global $wpdb;

    if ( ! current_user_can('edit_pages') ) // prevent loading coupons from outside admin area
    {
        echo 'No enough privilegies to load this content.';
        exit;
    }

    if (!defined('CP_BCCF_CALENDAR_ID'))
        define ('CP_BCCF_CALENDAR_ID',$_GET["dex_item"]);

    if (isset($_GET["add"]) && $_GET["add"] == "1")
        $wpdb->insert( $wpdb->prefix.DEX_BCCF_SEASON_PRICES_TABLE_NAME_NO_PREFIX, array('cal_id' => CP_BCCF_CALENDAR_ID,
                                                                         'price' => $_GET["price"],
                                                                         'date_from' => $_GET["dfrom"],
                                                                         'date_to' => $_GET["dto"],
                                                                         ));
    if (isset($_GET["delete"]) && $_GET["delete"] == "1")
        $wpdb->query( $wpdb->prepare( "DELETE FROM ".$wpdb->prefix.DEX_BCCF_SEASON_PRICES_TABLE_NAME_NO_PREFIX." WHERE id = %d", $_GET["code"] ));

    $codes = $wpdb->get_results( 'SELECT * FROM '.$wpdb->prefix.DEX_BCCF_SEASON_PRICES_TABLE_NAME_NO_PREFIX.' WHERE `cal_id`='.CP_BCCF_CALENDAR_ID);
    $maxcosts = 0;
    foreach ($codes as $value)
        if (substr_count($value->price,';') > $maxcosts)
            $maxcosts = substr_count($value->price,';');
    if (count ($codes))
    {
        echo '<table>';
        echo '<tr>';
        echo '  <th style="padding:2px;background-color: #cccccc;font-weight:bold;">Default Cost</th>';
        for ($k=1; $k<=$maxcosts; $k++)
            echo '  <th style="padding:2px;background-color: #cccccc;font-weight:bold;">'.$k.' day'.($k==1?'':'s').'</th>';
        echo '  <th style="padding:2px;background-color: #cccccc;font-weight:bold;">From</th>';
        echo '  <th style="padding:2px;background-color: #cccccc;font-weight:bold;">To</th>';
        echo '  <th style="padding:2px;background-color: #cccccc;font-weight:bold;">Options</th>';
        echo '</tr>';
        foreach ($codes as $value)
        {
           echo '<tr>';
           $price = explode(';',$value->price);
           echo '<td>'.$price[0].'</td>';
           for ($k=1; $k<=$maxcosts; $k++)
               echo '<td>'.@$price[$k].'</td>';
           echo '<td>'.substr($value->date_from,0,10).'</td>';
           echo '<td>'.substr($value->date_to,0,10).'</td>';
           echo '<td>[<a href="javascript:dex_delete_season_price('.$value->id.')">Delete</a>]</td>';
           echo '</tr>';
        }
        echo '</table>';
    }
    else
        echo 'No season prices listed for this calendar yet.';
    exit;
}

add_action( 'init', 'dex_bccf_check_IPN_verification', 11 );

function dex_bccf_check_IPN_verification() {

    global $wpdb;

	if ( ! isset( $_GET['ipncheck'] ) || $_GET['ipncheck'] != '1' ||  ! isset( $_GET["itemnumber"] ) )
		return;

	if ( ! isset( $_GET['dex_bccf'] ) || $_GET['dex_bccf'] != 'ipn' )
		return;

    $item_name = @$_POST['item_name'];
    $item_number = @$_POST['item_number'];
    $payment_status = @$_POST['payment_status'];
    $payment_amount = @$_POST['mc_gross'];
    $payment_currency = @$_POST['mc_currency'];
    $txn_id = @$_POST['txn_id'];
    $receiver_email = @$_POST['receiver_email'];
    $payer_email = @$_POST['payer_email'];
    $payment_type = @$_POST['payment_type'];

    if (@$_GET["beanstrean"] != '1')
    {
	    if ($payment_status != 'Completed' && $payment_type != 'echeck')
	        return;

	    if ($payment_type == 'echeck' && $payment_status != 'Pending')
	        return;
    }

    $itemnumber = intval($_GET["itemnumber"]);
    $myrows = $wpdb->get_results( "SELECT * FROM ".DEX_BCCF_TABLE_NAME." WHERE id=".$itemnumber );
    $params = unserialize($myrows[0]->buffered_date);

    dex_process_ready_to_go_bccf($_GET["itemnumber"], $payer_email, $params);

    if (@$_GET["beanstrean"] != '1')
        echo 'OK';
    else
        header( 'Location: '.dex_bccf_get_option('url_ok', DEX_BCCF_DEFAULT_OK_URL) );
    exit();

}

function dex_process_ready_to_go_bccf($itemnumber, $payer_email = "", $params)
{
   global $wpdb;

   dex_bccf_add_field_verify(TDE_BCCFCALENDAR_DATA_TABLE, "reference", "varchar(20) DEFAULT '' NOT NULL");
   dex_bccf_add_field_verify(TDE_BCCFCALENDAR_DATA_TABLE, "reminder", "VARCHAR(1) DEFAULT '' NOT NULL");
   dex_bccf_add_field_verify(TDE_BCCFCALENDAR_DATA_TABLE, "status", "VARCHAR(10) DEFAULT '' NOT NULL");

   $myrows = $wpdb->get_results( "SELECT * FROM ".DEX_BCCF_TABLE_NAME." WHERE id=".$itemnumber );

   $mycalendarrows = $wpdb->get_results( 'SELECT * FROM '.DEX_BCCF_CONFIG_TABLE_NAME .' WHERE `'.TDE_BCCFCONFIG_ID.'`='.$myrows[0]->calendar);

   if (!defined('CP_BCCF_CALENDAR_ID'))
        define ('CP_BCCF_CALENDAR_ID',$myrows[0]->calendar);

   $SYSTEM_EMAIL = dex_bccf_get_option('notification_from_email', DEX_BCCF_DEFAULT_PAYPAL_EMAIL);
   $SYSTEM_RCPT_EMAIL = dex_bccf_get_option('notification_destination_email', DEX_BCCF_DEFAULT_PAYPAL_EMAIL);


   $email_subject1 = __(dex_bccf_get_option('email_subject_confirmation_to_user', DEX_BCCF_DEFAULT_SUBJECT_CONFIRMATION_EMAIL),'bccf');
   $email_content1 = __(dex_bccf_get_option('email_confirmation_to_user', DEX_BCCF_DEFAULT_CONFIRMATION_EMAIL),'bccf');
   $email_subject2 = __(dex_bccf_get_option('email_subject_notification_to_admin', DEX_BCCF_DEFAULT_SUBJECT_NOTIFICATION_EMAIL),'bccf');
   $email_content2 = __(dex_bccf_get_option('email_notification_to_admin', DEX_BCCF_DEFAULT_NOTIFICATION_EMAIL),'bccf');

   $option_calendar_enabled = dex_bccf_get_option('calendar_enabled', DEX_BCCF_DEFAULT_CALENDAR_ENABLED);
   if ($option_calendar_enabled != 'false')
   {
       $information = "Item: ".$mycalendarrows[0]->uname."\n\n".
                      "Date From-To: ".$myrows[0]->booked_time_s." - ".$myrows[0]->booked_time_e."\n\n".
                      $myrows[0]->question;
   }
   else
   {
       $information = "Item: ".$mycalendarrows[0]->uname."\n\n".
                      $myrows[0]->question;
   }

   $email_content1 = str_replace("%INFORMATION%", $information, $email_content1);
   $email_content2 = str_replace("%INFORMATION%", $information, $email_content2);

   dex_bccf_add_field_verify(TDE_BCCFCALENDAR_DATA_TABLE, "color", "varchar(10)");

   $rows_affected = $wpdb->insert( TDE_BCCFCALENDAR_DATA_TABLE, array( 'reservation_calendar_id' => $myrows[0]->calendar,
                                                                       'datatime_s' => date("Y-m-d H:i:s", strtotime($myrows[0]->booked_time_unformatted_s)),
                                                                       'datatime_e' => date("Y-m-d H:i:s", strtotime($myrows[0]->booked_time_unformatted_e)),
                                                                       'title' => ($_POST[$to]?$_POST[$to]:"Booked"),
                                                                       'description' => str_replace("\n","<br />", $information),
                                                                       'reference' => $itemnumber,
                                                                       'status' => (@$_POST['payment_status']!=''?'1':''),
                                                                       'color' => TDE_BCCFCALENDAR_DEFAULT_COLOR
                                                                        ) );
   $newitemnum = $wpdb->insert_id;
   $email_content1 = str_replace("<%itemnumber%>", $newitemnum, $email_content1);
   $email_content2 = str_replace("<%itemnumber%>", $newitemnum, $email_content2);

   $email_content1 = str_replace("<%item%>", $mycalendarrows[0]->uname, $email_content1);
   $email_content2 = str_replace("<%item%>", $mycalendarrows[0]->uname, $email_content2);

   $attachments = array();
   foreach ($params as $item => $value)
    {
        $email_content1 = str_replace('<%'.$item.'%>',(is_array($value)?(implode(", ",$value)):($value)),$email_content1);
        $email_content2 = str_replace('<%'.$item.'%>',(is_array($value)?(implode(", ",$value)):($value)),$email_content2);

        $email_subject1 = str_replace('<%'.$item.'%>',(is_array($value)?(implode(", ",$value)):($value)),$email_subject1);
        $email_subject2 = str_replace('<%'.$item.'%>',(is_array($value)?(implode(", ",$value)):($value)),$email_subject2);

        if (strpos($item,"_link"))
        {
            foreach ($value as $filevalue)
                $attachments[] = $filevalue;
        }
    }

   // SEND EMAIL TO USER

   if ('html' == $mycalendarrows[0]->copyuser_emailformat) $content_type = "Content-Type: text/html; charset=utf-8\n"; else $content_type = "Content-Type: text/plain; charset=utf-8\n";


   $to = dex_bccf_get_option('cu_user_email_field', DEX_BCCF_DEFAULT_cu_user_email_field);
   $_POST[$to] = $myrows[0]->notifyto;
   if (trim($_POST[$to]) != '')
       wp_mail(trim($_POST[$to]), $email_subject1, $email_content1,
                "From: \"$SYSTEM_EMAIL\" <".$SYSTEM_EMAIL.">\r\n".
                $content_type.
                "X-Mailer: PHP/" . phpversion());

   if ($payer_email && strtolower($payer_email) != strtolower($_POST[$to]))
       wp_mail($payer_email , $email_subject1, $email_content1,
                "From: \"$SYSTEM_EMAIL\" <".$SYSTEM_EMAIL.">\r\n".
                $content_type.
                "X-Mailer: PHP/" . phpversion());


   // SEND EMAIL TO ADMIN
   if ('html' == $mycalendarrows[0]->notification_emailformat) $content_type = "Content-Type: text/html; charset=utf-8\n"; else $content_type = "Content-Type: text/plain; charset=utf-8\n";

   $to = explode(",",$SYSTEM_RCPT_EMAIL);
   foreach ($to as $item)
        if (trim($item) != '')
        {
            wp_mail($item, $email_subject2, $email_content2,
                     "From: \"$SYSTEM_EMAIL\" <".$SYSTEM_EMAIL.">\r\n".
                     $content_type.
                     "X-Mailer: PHP/" . phpversion(), $attachments);
        }

}


function dex_bccf_add_field_verify ($table, $field, $type = "text")
{
    global $wpdb;
    $results = $wpdb->get_results("SHOW columns FROM `".$table."` where field='".$field."'");
    if (!count($results))
    {
        $sql = "ALTER TABLE  `".$table."` ADD `".$field."` ".$type;
        $wpdb->query($sql);
    }
}


function dex_bccf_save_options()
{
    global $wpdb;
    if (!defined('CP_BCCF_CALENDAR_ID'))
        define ('CP_BCCF_CALENDAR_ID',$_POST["dex_item"]);

    dex_bccf_add_field_verify(DEX_BCCF_CONFIG_TABLE_NAME, "master", "varchar(50) DEFAULT '0' NOT NULL");

    dex_bccf_add_field_verify(DEX_BCCF_CONFIG_TABLE_NAME, "calendar_minnights", "varchar(255) DEFAULT '0' NOT NULL");
    dex_bccf_add_field_verify(DEX_BCCF_CONFIG_TABLE_NAME, "calendar_maxnights", "varchar(255) DEFAULT '365' NOT NULL");
    dex_bccf_add_field_verify(DEX_BCCF_CONFIG_TABLE_NAME, "calendar_suplement", "varchar(255) DEFAULT '0' NOT NULL");
    dex_bccf_add_field_verify(DEX_BCCF_CONFIG_TABLE_NAME, "calendar_suplementminnight", "varchar(255) DEFAULT '0' NOT NULL");
    dex_bccf_add_field_verify(DEX_BCCF_CONFIG_TABLE_NAME, "calendar_suplementmaxnight", "varchar(255) DEFAULT '0' NOT NULL");

    dex_bccf_add_field_verify(DEX_BCCF_CONFIG_TABLE_NAME, "calendar_startres");
    dex_bccf_add_field_verify(DEX_BCCF_CONFIG_TABLE_NAME, "calendar_holidays");
    dex_bccf_add_field_verify(DEX_BCCF_CONFIG_TABLE_NAME, "calendar_fixedmode", "varchar(10) DEFAULT '0' NOT NULL");
    dex_bccf_add_field_verify(DEX_BCCF_CONFIG_TABLE_NAME, "calendar_holidaysdays", "varchar(20) DEFAULT '1111111' NOT NULL");
    dex_bccf_add_field_verify(DEX_BCCF_CONFIG_TABLE_NAME, "calendar_startresdays", "varchar(20) DEFAULT '1111111' NOT NULL");
    dex_bccf_add_field_verify(DEX_BCCF_CONFIG_TABLE_NAME, "calendar_fixedreslength", "varchar(20) DEFAULT '1' NOT NULL");

    dex_bccf_add_field_verify(DEX_BCCF_CONFIG_TABLE_NAME, "max_slots", "varchar(20) DEFAULT '0' NOT NULL");
    dex_bccf_add_field_verify(DEX_BCCF_CONFIG_TABLE_NAME, "enable_paypal_option_yes", "varchar(250) DEFAULT '' NOT NULL");
    dex_bccf_add_field_verify(DEX_BCCF_CONFIG_TABLE_NAME, "enable_paypal_option_no", "varchar(250) DEFAULT '' NOT NULL");

    dex_bccf_add_field_verify(DEX_BCCF_CONFIG_TABLE_NAME, "vs_text_submitbtn", "varchar(250) DEFAULT '' NOT NULL");
    dex_bccf_add_field_verify(DEX_BCCF_CONFIG_TABLE_NAME, "vs_text_previousbtn", "varchar(250) DEFAULT '' NOT NULL");
    dex_bccf_add_field_verify(DEX_BCCF_CONFIG_TABLE_NAME, "vs_text_nextbtn", "varchar(250) DEFAULT '' NOT NULL");

    dex_bccf_add_field_verify(DEX_BCCF_CONFIG_TABLE_NAME, "calendar_depositenable", "varchar(20) DEFAULT '' NOT NULL");
    dex_bccf_add_field_verify(DEX_BCCF_CONFIG_TABLE_NAME, "calendar_depositamount", "varchar(20) DEFAULT '' NOT NULL");
    dex_bccf_add_field_verify(DEX_BCCF_CONFIG_TABLE_NAME, "calendar_deposittype", "varchar(20) DEFAULT '' NOT NULL");
    dex_bccf_add_field_verify(DEX_BCCF_CONFIG_TABLE_NAME, "calendar_showcost", "varchar(1) DEFAULT '' NOT NULL");

    dex_bccf_add_field_verify(DEX_BCCF_CONFIG_TABLE_NAME, "enable_reminder", "varchar(10) DEFAULT '' NOT NULL");

    dex_bccf_add_field_verify(DEX_BCCF_CONFIG_TABLE_NAME, "copyuser_emailformat", "varchar(20) DEFAULT '' NOT NULL");
    dex_bccf_add_field_verify(DEX_BCCF_CONFIG_TABLE_NAME, "notification_emailformat", "varchar(20) DEFAULT '' NOT NULL");

    dex_bccf_add_field_verify(DEX_BCCF_CONFIG_TABLE_NAME, "nremind_emailformat", "varchar(20) DEFAULT '' NOT NULL");
    dex_bccf_add_field_verify(DEX_BCCF_CONFIG_TABLE_NAME, "reminder_hours", "varchar(20) DEFAULT '' NOT NULL");
    dex_bccf_add_field_verify(DEX_BCCF_CONFIG_TABLE_NAME, "reminder_subject");
    dex_bccf_add_field_verify(DEX_BCCF_CONFIG_TABLE_NAME, "reminder_content");

    dex_bccf_add_field_verify(DEX_BCCF_CONFIG_TABLE_NAME, "nremind_emailformat2", "varchar(20) DEFAULT '' NOT NULL");
    dex_bccf_add_field_verify(DEX_BCCF_CONFIG_TABLE_NAME, "reminder_hours2", "varchar(20) DEFAULT '' NOT NULL");
    dex_bccf_add_field_verify(DEX_BCCF_CONFIG_TABLE_NAME, "reminder_subject2");
    dex_bccf_add_field_verify(DEX_BCCF_CONFIG_TABLE_NAME, "reminder_content2");

    dex_bccf_add_field_verify(DEX_BCCF_CONFIG_TABLE_NAME, "enable_beanstream_id", "varchar(250) DEFAULT '' NOT NULL");

    dex_bccf_add_field_verify(TDE_BCCFCALENDAR_DATA_TABLE, "reference", "varchar(20) DEFAULT '' NOT NULL");
    dex_bccf_add_field_verify(TDE_BCCFCALENDAR_DATA_TABLE, "reminder", "VARCHAR(1) DEFAULT '' NOT NULL");

    dex_bccf_add_field_verify(DEX_BCCF_CONFIG_TABLE_NAME,'request_taxes'," varchar(20) NOT NULL default ''");

    $calendar_holidaysdays = (@$_POST["wd1"]?"1":"0").(@$_POST["wd2"]?"1":"0").(@$_POST["wd3"]?"1":"0").(@$_POST["wd4"]?"1":"0").(@$_POST["wd5"]?"1":"0").(@$_POST["wd6"]?"1":"0").(@$_POST["wd7"]?"1":"0");
    $calendar_startresdays = (@$_POST["sd1"]?"1":"0").(@$_POST["sd2"]?"1":"0").(@$_POST["sd3"]?"1":"0").(@$_POST["sd4"]?"1":"0").(@$_POST["sd5"]?"1":"0").(@$_POST["sd6"]?"1":"0").(@$_POST["sd7"]?"1":"0");

    for ($k=1;$k<=DEX_BCCF_DEFAULT_SERVICES_FIELDS; $k++)
    {
        dex_bccf_add_field_verify(DEX_BCCF_CONFIG_TABLE_NAME, "cp_cal_checkboxes_label".$k);
        dex_bccf_add_field_verify(DEX_BCCF_CONFIG_TABLE_NAME, "cp_cal_checkboxes_type".$k);
        dex_bccf_add_field_verify(DEX_BCCF_CONFIG_TABLE_NAME, "cp_cal_checkboxes".$k);
    }

    foreach ($_POST as $item => $value)
        $_POST[$item] = stripcslashes($value);

    for ($k=1;$k <= intval($_POST["max_slots"]); $k++)
        $_POST["request_cost"] .= ";".$_POST["request_cost_".$k];

    //echo $_POST['form_structure'];exit;
    $data = array(
         'form_structure' => $_POST['form_structure'],
         'calendar_language' => $_POST["calendar_language"],
         'calendar_dateformat' => $_POST["calendar_dateformat"],
         'calendar_overlapped' => $_POST["calendar_overlapped"],
         'calendar_enabled' => $_POST["calendar_enabled"],
         'calendar_mode' => $_POST["calendar_mode"],
         'calendar_pages' => (isset($_POST["calendar_pages"])?$_POST["calendar_pages"]:1),
         'calendar_weekday' => $_POST["calendar_weekday"],
         'calendar_mindate' => $_POST["calendar_mindate"],
         'calendar_maxdate' => $_POST["calendar_maxdate"],

         'master' => @$_POST["master"],
         'calendar_minnights' => @$_POST["calendar_minnights"],
         'calendar_maxnights' => @$_POST["calendar_maxnights"],
         'calendar_suplement' => @$_POST["calendar_suplement"],
         'calendar_suplementminnight' => @$_POST["calendar_suplementminnight"],
         'calendar_suplementmaxnight' => @$_POST["calendar_suplementmaxnight"],
         'calendar_fixedmode' => (@$_POST["calendar_fixedmode"]?"1":"0"),
         'calendar_holidaysdays' => $calendar_holidaysdays,
         'calendar_startresdays' => $calendar_startresdays,
         'calendar_fixedreslength' => @$_POST["calendar_fixedreslength"],

         'calendar_startres' => @$_POST["calendar_startres"],
         'calendar_holidays' => @$_POST["calendar_holidays"],
         'calendar_showcost' => $_POST["calendar_showcost"],

         'enable_beanstream_id' => @$_POST["enable_beanstream_id"],

         'cu_user_email_field' => $_POST['cu_user_email_field'],

         'enable_paypal' => @$_POST["enable_paypal"],
         'paypal_email' => $_POST["paypal_email"],
         'request_cost' => $_POST["request_cost"],
         'max_slots' => $_POST["max_slots"],
         'paypal_product_name' => $_POST["paypal_product_name"],
         'currency' => $_POST["currency"],
         'url_ok' => $_POST["url_ok"],
         'url_cancel' => $_POST["url_cancel"],
         'paypal_language' => $_POST["paypal_language"],
         'request_taxes' => $_POST["request_taxes"],

         'notification_from_email' => $_POST["notification_from_email"],
         'notification_destination_email' => $_POST["notification_destination_email"],
         'email_subject_confirmation_to_user' => $_POST["email_subject_confirmation_to_user"],
         'email_confirmation_to_user' => $_POST["email_confirmation_to_user"],
         'email_subject_notification_to_admin' => $_POST["email_subject_notification_to_admin"],
         'email_notification_to_admin' => $_POST["email_notification_to_admin"],

         'copyuser_emailformat' => $_POST["copyuser_emailformat"],
         'notification_emailformat' => $_POST["notification_emailformat"],

         'enable_paypal_option_yes' => (@$_POST['enable_paypal_option_yes']?$_POST['enable_paypal_option_yes']:DEX_BCCF_DEFAULT_PAYPAL_OPTION_YES),
         'enable_paypal_option_no' => (@$_POST['enable_paypal_option_no']?$_POST['enable_paypal_option_no']:DEX_BCCF_DEFAULT_PAYPAL_OPTION_NO),

         // 'vs_use_validation' => $_POST['vs_use_validation'],
         'vs_text_is_required' => $_POST['vs_text_is_required'],
         'vs_text_is_email' => $_POST['vs_text_is_email'],
         'vs_text_datemmddyyyy' => $_POST['vs_text_datemmddyyyy'],
         'vs_text_dateddmmyyyy' => $_POST['vs_text_dateddmmyyyy'],
         'vs_text_number' => $_POST['vs_text_number'],
         'vs_text_digits' => $_POST['vs_text_digits'],
         'vs_text_max' => $_POST['vs_text_max'],
         'vs_text_min' => $_POST['vs_text_min'],

         'vs_text_submitbtn' => $_POST['vs_text_submitbtn'],
         'vs_text_previousbtn' => $_POST['vs_text_previousbtn'],
         'vs_text_nextbtn' => $_POST['vs_text_nextbtn'],

         'calendar_depositenable' => $_POST['calendar_depositenable'],
         'calendar_depositamount' => $_POST['calendar_depositamount'],
         'calendar_deposittype' => $_POST['calendar_deposittype'],


         'enable_reminder' => @$_POST["enable_reminder"],

         'nremind_emailformat' => @$_POST["nremind_emailformat"],
         'reminder_hours' => @$_POST["reminder_hours"],
         'reminder_subject' => @$_POST["reminder_subject"],
         'reminder_content' => @$_POST["reminder_content"],

         'nremind_emailformat2' => @$_POST["nremind_emailformat2"],
         'reminder_hours2' => @$_POST["reminder_hours2"],
         'reminder_subject2' => @$_POST["reminder_subject2"],
         'reminder_content2' => @$_POST["reminder_content2"],

         'dexcv_enable_captcha' => $_POST["dexcv_enable_captcha"],
         'dexcv_width' => $_POST["dexcv_width"],
         'dexcv_height' => $_POST["dexcv_height"],
         'dexcv_chars' => $_POST["dexcv_chars"],
         'dexcv_min_font_size' => $_POST["dexcv_min_font_size"],
         'dexcv_max_font_size' => $_POST["dexcv_max_font_size"],
         'dexcv_noise' => $_POST["dexcv_noise"],
         'dexcv_noise_length' => $_POST["dexcv_noise_length"],
         'dexcv_background' => $_POST["dexcv_background"],
         'dexcv_border' => $_POST["dexcv_border"],
         'dexcv_font' => $_POST["dexcv_font"],
         'cv_text_enter_valid_captcha' => $_POST['cv_text_enter_valid_captcha'],

         'cp_cal_checkboxes' => @$_POST["cp_cal_checkboxes"],
         'cp_cal_checkboxes_type' => @$_POST["cp_cal_checkboxes_type"]
	);

    for ($k=1;$k<=DEX_BCCF_DEFAULT_SERVICES_FIELDS; $k++)
    {
        $data["cp_cal_checkboxes_label".$k] = $_POST["cp_cal_checkboxes_label".$k];
        $data["cp_cal_checkboxes_type".$k] = $_POST["cp_cal_checkboxes_type".$k];
        $data["cp_cal_checkboxes".$k] =  $_POST["cp_cal_checkboxes".$k];
    }


    $wpdb->update ( DEX_BCCF_CONFIG_TABLE_NAME, $data, array( 'id' => CP_BCCF_CALENDAR_ID ));

}


function dex_bccf_get_field_name ($fieldid, $form)
{
    if (is_array($form))
        foreach($form as $item)
            if ($item->name == $fieldid)
                return $item->title;
    return $fieldid;
}


function dex_bccf_appointments_export_csv ()
{
    if (!is_admin())
        return;
    global $wpdb;

    if (!defined('CP_BCCF_CALENDAR_ID'))
        define ('CP_BCCF_CALENDAR_ID',intval($_GET["cal"]));

    $form_data = json_decode(dex_bccf_cleanJSON(dex_bccf_get_option('form_structure', DEX_BCCF_DEFAULT_form_structure)));

    $cond = '';
    if ($_GET["search"] != '') $cond .= " AND (buffered_date like '%".esc_sql($_GET["search"])."%')";
    if ($_GET["dfrom"] != '') $cond .= " AND (`booked_time_unformatted` >= '".esc_sql($_GET["dfrom"])."')";
    if ($_GET["dto"] != '') $cond .= " AND (`booked_time_unformatted` <= '".esc_sql($_GET["dto"])." 23:59:59')";

    if (CP_BCCF_CALENDAR_ID != 0) $cond .= " AND calendar=".CP_BCCF_CALENDAR_ID;

    $events_query = "SELECT ".DEX_BCCF_TABLE_NAME.".*,".DEX_BCCF_CONFIG_TABLE_NAME.".uname FROM "
                              .DEX_BCCF_TABLE_NAME." INNER JOIN ".DEX_BCCF_CONFIG_TABLE_NAME." ON ".DEX_BCCF_TABLE_NAME.".calendar=".DEX_BCCF_CONFIG_TABLE_NAME.".id INNER JOIN  ".DEX_BCCF_CALENDARS_TABLE_NAME." on  ".DEX_BCCF_TABLE_NAME.".id=".DEX_BCCF_CALENDARS_TABLE_NAME.".reference  ".
                              " WHERE 1=1 ".$cond." ORDER BY `datatime_s` DESC";
	/**
	 * Allows modify the query of messages, passing the query as parameter
	 * returns the new query
	 */
	$events_query = apply_filters( 'dexbccf_csv_query', $events_query );
	$events = $wpdb->get_results( $events_query );


    $fields = array("Calendar ID","Calendar Name"/**, "Time Start", "Time End"*/);
    $values = array();
    foreach ($events as $item)
    {
        $value = array($item->calendar, $item->uname/**, $item->booked_time_s, $item->booked_time_e*/);

        $data = array();
        $data = unserialize($item->buffered_date);

        $end = count($fields);
        for ($i=0; $i<$end; $i++)
            if (isset($data[$fields[$i]]) ){
                $value[$i] = $data[$fields[$i]];
                unset($data[$fields[$i]]);
            }

        foreach ($data as $k => $d)
        {
           $fields[] = $k;
           $value[] = $d;
        }
        $values[] = $value;
    }


    header("Content-type: application/octet-stream");
    header("Content-Disposition: attachment; filename=export.csv");

    $end = count($fields);
    for ($i=0; $i<$end; $i++)
        echo '"'.str_replace('"','""', dex_bccf_get_field_name($fields[$i],@$form_data[0])).'",';
    echo "\n";
    foreach ($values as $item)
    {
        for ($i=0; $i<$end; $i++)
        {
            if (!isset($item[$i]))
                $item[$i] = '';
            if (is_array($item[$i]))
                $item[$i] = implode($item[$i],',');
            echo '"'.str_replace('"','""', $item[$i]).'",';
        }
        echo "\n";
    }

    exit;
}


function dex_bccf_check_reminders() {
    global $wpdb;

    dex_bccf_add_field_verify(DEX_BCCF_CONFIG_TABLE_NAME, "nremind_emailformat", "varchar(20) DEFAULT '' NOT NULL");
    dex_bccf_add_field_verify(DEX_BCCF_CONFIG_TABLE_NAME, "nremind_emailformat2", "varchar(20) DEFAULT '' NOT NULL");
    dex_bccf_add_field_verify(DEX_BCCF_CONFIG_TABLE_NAME, "reminder_subject");
    dex_bccf_add_field_verify(DEX_BCCF_CONFIG_TABLE_NAME, "reminder_subject2");
    dex_bccf_add_field_verify(DEX_BCCF_CONFIG_TABLE_NAME, "reminder_content");
    dex_bccf_add_field_verify(DEX_BCCF_CONFIG_TABLE_NAME, "reminder_content2");
    dex_bccf_add_field_verify(DEX_BCCF_CONFIG_TABLE_NAME, "enable_reminder", "varchar(10) DEFAULT '' NOT NULL");
    dex_bccf_add_field_verify(TDE_BCCFCALENDAR_DATA_TABLE, "reminder", "varchar(10) DEFAULT '' NOT NULL");
    dex_bccf_add_field_verify(DEX_BCCF_CONFIG_TABLE_NAME, "reminder_hours", "varchar(10) DEFAULT '' NOT NULL");
    dex_bccf_add_field_verify(DEX_BCCF_CONFIG_TABLE_NAME, "reminder_hours2", "varchar(10) DEFAULT '' NOT NULL");


    // first reminder email
    $query = "SELECT nremind_emailformat,notification_from_email,reminder_subject,reminder_content,uname,".TDE_BCCFCALENDAR_DATA_TABLE.".* FROM ".
              TDE_BCCFCALENDAR_DATA_TABLE." INNER JOIN ".DEX_BCCF_CONFIG_TABLE_NAME." ON ".TDE_BCCFCALENDAR_DATA_TABLE.".reservation_calendar_id=".DEX_BCCF_CONFIG_TABLE_NAME.".id ".
              " WHERE enable_reminder=1 AND (reminder='0' OR reminder='') AND datatime_s<DATE_ADD(now(),INTERVAL reminder_hours HOUR) AND datatime_s>'".date("Y-m-d H:i:s")."'";
    $apps = $wpdb->get_results( $query);

    foreach ($apps as $app) {
        // send email
        if ('html' == $app->nremind_emailformat) $content_type = "Content-Type: text/html; charset=utf-8\n"; else $content_type = "Content-Type: text/plain; charset=utf-8\n";
        $email_content = str_replace('%INFORMATION%',str_replace('<br />',"\n",$app->description),$app->reminder_content);

        $app_source = $wpdb->get_results( "SELECT notifyto,buffered_date FROM ".DEX_BCCF_TABLE_NAME." WHERE id=".$app->reference);
        $params = unserialize($app_source[0]->buffered_date);
        foreach ($params as $item => $value)
        {
            $email_content = str_replace('<%'.$item.'%>',(is_array($value)?(implode(", ",$value)):($value)),$email_content);
            $email_content = str_replace('%'.$item.'%',(is_array($value)?(implode(", ",$value)):($value)),$email_content);
        }
        $email_content = str_replace("%CALENDAR%", $app->uname, $email_content);
        wp_mail($app_source[0]->notifyto, $app->reminder_subject, $email_content,
                "From: \"".$app->notification_from_email."\" <".$app->notification_from_email.">\r\n".
                $content_type.
                "X-Mailer: PHP/" . phpversion());
        // mark as sent
        $wpdb->query("UPDATE ".TDE_BCCFCALENDAR_DATA_TABLE." SET reminder='1' WHERE id=".$app->id);
    }


    // second reminder email
    $query = "SELECT nremind_emailformat2,notification_from_email,reminder_subject2,reminder_content2,uname,".TDE_BCCFCALENDAR_DATA_TABLE.".* FROM ".
              TDE_BCCFCALENDAR_DATA_TABLE." INNER JOIN ".DEX_BCCF_CONFIG_TABLE_NAME." ON ".TDE_BCCFCALENDAR_DATA_TABLE.".reservation_calendar_id=".DEX_BCCF_CONFIG_TABLE_NAME.".id ".
              " WHERE enable_reminder=1 AND (reminder='0' OR reminder='' OR reminder='1') AND datatime_e<DATE_SUB(now(),INTERVAL reminder_hours2 HOUR)"; // AND datatime_e>'".date("Y-m-d H:i:s")."'";
    $apps = $wpdb->get_results( $query);

    foreach ($apps as $app) {
        // send email
        if ('html' == $app->nremind_emailformat2) $content_type = "Content-Type: text/html; charset=utf-8\n"; else $content_type = "Content-Type: text/plain; charset=utf-8\n";
        $email_content = str_replace('%INFORMATION%',str_replace('<br />',"\n",$app->description),$app->reminder_content2);

        $app_source = $wpdb->get_results( "SELECT notifyto,buffered_date FROM ".DEX_BCCF_TABLE_NAME." WHERE id=".$app->reference);
        $params = unserialize($app_source[0]->buffered_date);
        foreach ($params as $item => $value)
        {
            $email_content = str_replace('<%'.$item.'%>',(is_array($value)?(implode(", ",$value)):($value)),$email_content);
            $email_content = str_replace('%'.$item.'%',(is_array($value)?(implode(", ",$value)):($value)),$email_content);
        }
        $email_content = str_replace("%CALENDAR%", $app->uname, $email_content);
        wp_mail($app_source[0]->notifyto, $app->reminder_subject2, $email_content,
                "From: \"".$app->notification_from_email."\" <".$app->notification_from_email.">\r\n".
                $content_type.
                "X-Mailer: PHP/" . phpversion());
        // mark as sent
        $wpdb->query("UPDATE ".TDE_BCCFCALENDAR_DATA_TABLE." SET reminder='2' WHERE id=".$app->id);
    }
}


add_action( 'init', 'dex_bccf_calendar_ajaxevent', 11 );

function dex_bccf_calendar_ajaxevent() {
    if ( ! isset( $_GET['dex_bccf_calendar_load2'] ))
		return;

	if (!ini_get("zlib.output_compression"))
	{
	    @ob_clean();
        header("Cache-Control: no-store, no-cache, must-revalidate");
        header("Pragma: no-cache");
    }

    $ret = array();
    $ret['events'] = array();
    $ret['isSuccess'] = true;
    $ret['msg'] = "";

    switch ($_GET['dex_bccf_calendar_load2']) {
        case 'list':
            $ret = dex_bccf_calendar_load2($ret);
            break;
        case 'add':
            if ( ! current_user_can('edit_pages') )
            {
                $ret['isSuccess'] = false;
                $ret['msg'] = "Permissions error: No enough privilegies to add an item.";
            }
            else
                $ret = dex_bccf_calendar_add($ret);
            break;
        case 'edit':
            if ( ! current_user_can('edit_pages') )
            {
                $ret['isSuccess'] = false;
                $ret['msg'] = "Permissions error: No enough privilegies to edit an item.";
            }
            else
                $ret = dex_bccf_calendar_update($ret);
            break;
        case 'delete':
            if ( ! current_user_can('edit_pages') )
            {
                $ret['isSuccess'] = false;
                $ret['msg'] = "Permissions error: No enough privilegies to delete an item.";
            }
            else
                $ret = dex_bccf_calendar_delete($ret);
            break;
        default:
          $ret['isSuccess'] = false;
          $ret['msg'] = "Unknown calendar action: ".$_GET['dex_bccf_calendar_load2'];
    }

    echo json_encode($ret);
    exit();
}

function dex_bccf_calendar_load2($ret) {
    global $wpdb;

    $calid = str_replace  (TDE_BCCFCAL_PREFIX, "",@$_GET["id"]);


    $query = "SELECT * FROM ".DEX_BCCF_CONFIG_TABLE_NAME." where id='".esc_sql($calid)."'";
    $row = $wpdb->get_results($query,ARRAY_A);
    if ($row[0] && $row[0]["master"] != '0' && $row[0]["master"] != '')
    {
        $query = "SELECT * FROM ".DEX_BCCF_CONFIG_TABLE_NAME." where id='".esc_sql($row[0]["master"])."'";
        $row_master = $wpdb->get_results($query,ARRAY_A);
        if ($row_master[0]) $calid = $row[0]["master"];
    }


    if (!defined('CP_BCCF_CALENDAR_ID') && $calid != '-1')
        define ('CP_BCCF_CALENDAR_ID',$calid);

    $option = dex_bccf_get_option('calendar_overlapped', DEX_BCCF_DEFAULT_CALENDAR_OVERLAPPED);

    if ($calid == '-1')
        $query = "SELECT * FROM ".TDE_BCCFCALENDAR_DATA_TABLE." where (1=1)";
    else
        $query = "SELECT * FROM ".TDE_BCCFCALENDAR_DATA_TABLE." where ".TDE_BCCFDATA_IDCALENDAR."='".esc_sql($calid)."'";
    if ($option == 'true')
        $query.= " AND viadmin='1'";

    $result = $wpdb->get_results($query, ARRAY_A);
    foreach ($result as $row)
    {
        if (@$row["color"] == '')
            $row["color"] = TDE_BCCFCALENDAR_DEFAULT_COLOR;
        $ret['events'][] = array(
                                  "id"=>$row["id"],
                                  "dl"=>date("m/d/Y", strtotime($row["datatime_s"])),
                                  "du"=>date("m/d/Y", strtotime($row["datatime_e"])),
                                  "title"=>$row["title"],
                                  "description"=>$row["description"],
                                  "c"=>@$row["color"]    // falta annadir este campo
                                );
    }

    return $ret;
}


function dex_bccf_calendar_add($ret) {
    global $wpdb;

    $calid = str_replace  (TDE_BCCFCAL_PREFIX, "",@$_GET["id"]);
    if (!defined('CP_BCCF_CALENDAR_ID'))
        define ('CP_BCCF_CALENDAR_ID',$calid);

    dex_bccf_add_field_verify(TDE_BCCFCALENDAR_DATA_TABLE, "viadmin", "varchar(10) DEFAULT '0' NOT NULL");
    dex_bccf_add_field_verify(TDE_BCCFCALENDAR_DATA_TABLE, "color", "varchar(10)");

    $wpdb->query("insert into ".TDE_BCCFCALENDAR_DATA_TABLE."(viadmin,reservation_calendar_id,datatime_s,datatime_e,title,description,color) ".
                " values(1,".esc_sql($calid).",'".esc_sql($_POST["startdate"])."','".esc_sql($_POST["enddate"])."','".esc_sql($_POST["title"])."','".esc_sql($_POST["description"])."','".esc_sql($_POST["color"])."')");
    $ret['events'][0] = array("id"=>$wpdb->insert_id,"dl"=>date("m/d/Y", strtotime($_POST["startdate"])),"du"=>date("m/d/Y", strtotime($_POST["enddate"])),"title"=>$_POST["title"],"description"=>$_POST["description"],"c"=>$_POST["color"]);
    return $ret;
}

function dex_bccf_calendar_update($ret) {
    global $wpdb;

    dex_bccf_add_field_verify(TDE_BCCFCALENDAR_DATA_TABLE, "viadmin", "varchar(10) DEFAULT '0' NOT NULL");
    dex_bccf_add_field_verify(TDE_BCCFCALENDAR_DATA_TABLE, "color", "varchar(10)");

    $wpdb->query("update ".TDE_BCCFCALENDAR_DATA_TABLE." set title='".esc_sql($_POST["title"])."',description='".esc_sql($_POST["description"])."',color='".esc_sql($_POST["color"])."' where id=".esc_sql($_POST["id"]) );
    return $ret;
}

function dex_bccf_calendar_delete($ret) {
    global $wpdb;
    $wpdb->query( "delete from ".TDE_BCCFCALENDAR_DATA_TABLE." where id=".esc_sql($_POST["id"]) );
    return $ret;
}


function cp_bccf_get_site_url($admin = false)
{
    $blog = get_current_blog_id();
    if( $admin )
        $url = get_admin_url( $blog );
    else
        $url = get_home_url( $blog );

    $url = parse_url($url);
    $url = rtrim(@$url["path"],"/");
    return $url;
}

function cp_bccf_get_FULL_site_url($admin = false)
{
    $blog = get_current_blog_id();
    if( $admin )
        $url = get_admin_url( $blog );
    else
        $url = get_home_url( $blog );

    $url = parse_url($url);
    $url = rtrim($url["path"],"/");
    $pos = strpos($url, "://");
    if ($pos === false)
        $url = 'http://'.$_SERVER["HTTP_HOST"].$url;
//    if (!empty($_SERVER['HTTPS']))
//        $url = str_replace("http://","https://",$url);
    return $url;
}

function dex_bccf_cleanJSON($str)
{
    $str = str_replace('&qquot;','"',$str);
    $str = str_replace('	',' ',$str);
    $str = str_replace("\n",'\n',$str);
    $str = str_replace("\r",'',$str);
    return $str;
}

function dex_bccf_available_templates()
{
	global $CP_BCCF_global_templates;

	if( empty( $CP_BCCF_global_templates ) )
	{
		// Get available designs
		$tpls_dir = dir( plugin_dir_path( __FILE__ ).'templates' );
		$CP_BCCF_global_templates = array();
		while( false !== ( $entry = $tpls_dir->read() ) )
		{
			if ( $entry != '.' && $entry != '..' && is_dir( $tpls_dir->path.'/'.$entry ) && file_exists( $tpls_dir->path.'/'.$entry.'/config.ini' ) )
			{
				if( ( $ini_array = parse_ini_file( $tpls_dir->path.'/'.$entry.'/config.ini' ) ) !== false )
				{
					if( !empty( $ini_array[ 'file' ] ) ) $ini_array[ 'file' ] = plugins_url( 'templates/'.$entry.'/'.$ini_array[ 'file' ], __FILE__ );
					if( !empty( $ini_array[ 'thumbnail' ] ) ) $ini_array[ 'thumbnail' ] = plugins_url( 'templates/'.$entry.'/'.$ini_array[ 'thumbnail' ], __FILE__ );
					$CP_BCCF_global_templates[ $ini_array[ 'prefix' ] ] = $ini_array;
				}
			}
		}
	}

	return $CP_BCCF_global_templates;
}

function dex_bccf_translate_json($str)
{
    $form_data = json_decode(dex_bccf_cleanJSON($str));

    $form_data[1][0]->title = __($form_data[1][0]->title,'bccf');
    $form_data[1][0]->description = __($form_data[1][0]->description,'bccf');


    for ($i=0; $i < count($form_data[0]); $i++)
    {
        $form_data[0][$i]->title = __($form_data[0][$i]->title,'bccf');
        $form_data[0][$i]->userhelpTooltip = __($form_data[0][$i]->userhelpTooltip,'bccf');
        $form_data[0][$i]->userhelp = __($form_data[0][$i]->userhelp,'bccf');
        if ($form_data[0][$i]->ftype == 'fCommentArea')
            $form_data[0][$i]->userhelp = __($form_data[0][$i]->userhelp,'bccf');
        else
            if ($form_data[0][$i]->ftype == 'fradio' || $form_data[0][$i]->ftype == 'fcheck' || $form_data[0][$i]->ftype == 'fradio')
            {
                for ($j=0; $j < count($form_data[0][$i]->choices); $j++)
                    $form_data[0][$i]->choices[$j] = __($form_data[0][$i]->choices[$j],'bccf');
            }
    }
    $str = json_encode($form_data);
    return $str;
}

function dex_bccf_autodetect_language()
{
    $basename = '/js/languages/jquery.ui.datepicker-';

    $options = array (get_bloginfo('language'),
                      strtolower(get_bloginfo('language')),
                      substr(strtolower(get_bloginfo('language')),0,2)."-".substr(strtoupper(get_bloginfo('language')),strlen(strtoupper(get_bloginfo('language')))-2,2),
                      substr(strtolower(get_bloginfo('language')),0,2),
                      substr(strtolower(get_bloginfo('language')),strlen(strtolower(get_bloginfo('language')))-2,2)
                      );
    foreach ($options as $option)
    {
        if (file_exists(dirname( __FILE__ ).$basename.$option.'.js'))
            return $option;
        $option = str_replace ("-","_", $option);
        if (file_exists(dirname( __FILE__ ).$basename.$option.'.js'))
            return $option;
    }
    return '';
}


// dex_dex_bccf_get_option:
$dex_option_buffered_item = false;
$dex_option_buffered_id = -1;

function dex_bccf_get_option ($field, $default_value, $id = '')
{
    global $wpdb, $dex_option_buffered_item, $dex_option_buffered_id;
    if (!defined("CP_BCCF_CALENDAR_ID"))
        return  $default_value;
    if ($dex_option_buffered_id == CP_BCCF_CALENDAR_ID)
        $value = @$dex_option_buffered_item->$field;
    else
    {
       $myrows = $wpdb->get_results( "SELECT * FROM ".DEX_BCCF_CONFIG_TABLE_NAME." WHERE id=".CP_BCCF_CALENDAR_ID );
       $value = @$myrows[0]->$field;
       $dex_option_buffered_item = $myrows[0];
       $dex_option_buffered_id  = CP_BCCF_CALENDAR_ID;
    }
    if ($value == '' && $dex_option_buffered_item->calendar_language == '')
        $value = $default_value;

    $value = apply_filters( 'dexbccf_get_option', $value, $field, $id );

    return $value;
}

function cp_bccf_is_administrator()
{
    return current_user_can('manage_options');
}


// Auxiliar functions
// ***********************************************************************


function dex_bccf_form_result( $atts, $content = "", $id = 0 )
	{

		global $wpdb;
		if( $id == 0 && !empty( $_SESSION[ 'cp_cff_form_data' ] ) ) $id = $_SESSION[ 'cp_cff_form_data' ];
		if( !empty( $id ) )
		{
			$content = html_entity_decode( $content );
			$result = $wpdb->get_row( $wpdb->prepare( "SELECT form_settings.form_structure AS form_structure, form_data.question AS data, form_data.buffered_date AS paypal_post FROM ".$wpdb->prefix.DEX_BCCF_CONFIG_TABLE_NAME_NO_PREFIX." AS form_settings,".DEX_BCCF_TABLE_NAME." AS form_data WHERE form_data.id=%d AND form_data.formid=form_settings.id", $id ) );

			if( !is_null( $result ) )
			{
				$atts = shortcode_atts( array( 'fields' => '' ), $atts );
				if( !empty( $atts[ 'fields' ] ) || !empty( $content ) )
				{
					$raw_form_str = dex_bccf_cleanJSON( $result->form_structure );
					$form_data = json_decode( $raw_form_str );

					if( is_null( $form_data ) )
					{
						$json = new JSON;
						$form_data = $json->unserialize( $raw_form_str );
					}
				}

				if( empty( $form_data ) )
				{
					return "<p>" . preg_replace( "/\n+/", "<br />", $result->data ) . "</p>";
				}
				else
				{
					$fields = array();
					foreach($form_data[0] as $item)
					{
						$fields[$item->name] = $item;
					}
					$fields[ 'ipaddr' ] = $_SERVER["REMOTE_ADDR"]; //$result->ipaddr;
					$result->paypal_post = unserialize( $result->paypal_post );
					$str = '';
					$atts[ 'fields' ] = explode( ",", str_replace( " ", "", $atts[ 'fields' ] ) );
					foreach( $atts[ 'fields' ] as $field )
					{
                        if( isset( $fields[ $field ] ) )
                        {
                            if( isset( $result->paypal_post[ $field ] ) )
                            {
                                if( is_array( $result->paypal_post[ $field ] ) ) $result->paypal_post[ $field ] = implode( ',', $result->paypal_post[ $field ] );
                                $str .= "<p>{$fields[ $field ]->title} {$result->paypal_post[ $field ]}</p>";
                            }
                            elseif( in_array( $fields[ $field ]->ftype, array( 'fSectionBreak' ) ) )
                            {
                                $str .= "<p><strong>".$fields[ $field ]->title."</strong>".(( !empty($fields[ $field ]->userhelp) ) ? "<br /><pan class='uh'>".$fields[ $field ]->userhelp."</span>" : '' )."</p>";
                            }
						}

					}

                    if( $content != '' )
                    {
	                    $replaced_values = _dex_bccf_replace_vars( $fields, $result->paypal_post, $content, $result->data, 'html', $id );
	                    $str .= $replaced_values[ 'message' ];
                    }

					return $str;
				}
			}
		}

		return '';
	}


function _dex_bccf_extract_tags( $message )
{
	$tags_arr = array();
	if(
		preg_match_all(	"/<%(info|fieldname\d+|fieldname\d+_label|fieldname\d+_shortlabel|fieldname\d+_value|fieldname\d+_url|fieldname\d+_urls|coupon|itemnumber|final_price|payment_option|ipaddress|currentdate_mmddyyyy|currentdate_ddmmyyyy)\b(?:(?!%>).)*%>/i",
			$message,
			$matches
		)
	)
	{
		$tag = array();
		foreach( $matches[ 0 ] as $index => $value )
		{
			$tag[ 'node' ] = $value;
			$tag[ 'tag' ]  = strtolower( $matches[ 1 ][ $index ] );
			$tag[ 'if_not_empty' ] 	= preg_match( "/if_not_empty/i", $value );
			$tag[ 'before' ]    	= ( preg_match( "/before\s*=\s*\{\{((?:(?!\}\}).)*)\}\}/i",  $value, $match ) ) ? $match[ 1 ] : '';
			$tag[ 'after' ]   		= ( preg_match( "/after\s*=\s*\{\{((?:(?!\}\}).)*)\}\}/i", $value, $match ) ) ? $match[ 1 ] : '';
			$tag[ 'separator' ]    	= ( preg_match( "/separator\s*=\s*\{\{((?:(?!\}\}).)*)\}\}/i",  $value, $match ) ) ? $match[ 1 ] : '';

			$baseTag = ( preg_match( "/(fieldname\d+)_(label|value|shortlabel)/i", $tag[ 'tag' ], $match ) ) ? $match[ 1 ] : $tag[ 'tag' ];

			if( empty( $tags_arr[ $baseTag ] ) ) $tags_arr[ $baseTag ] = array();
			$tags_arr[ $baseTag ][] = $tag;
		}
	}
	return $tags_arr;
}

function _dex_bccf_replace_vars( $fields, $params, $message, $buffer = '', $contentType = 'html', $itemnumber = '' )
{
	// Lambda functions
	$arrayReplacementFunction = create_function('&$tags, $tagName, $replacement, &$message', 'if(isset($tags[ $tagName ])){foreach( $tags[ $tagName ] as $tagData ){ $message = str_replace( $tagData[ "node" ], $tagData[ "before" ].$replacement.$tagData[ "after" ], $message );}unset( $tags[ $tagName ] );}');

	$singleReplacementFunction = create_function('$tagData, $value, &$message', '$message = str_replace( $tagData[ "node" ], $tagData[ "before" ].$value.$tagData[ "after" ],$message );');

	$message = str_replace( '< %', '<%', $message );
    $attachments = array();

	// Remove empty blocks
	while( preg_match( "/<%\s*fieldname(\d+)_block\s*%>/", $message, $matches ) )
	{
		if( empty( $params[ 'fieldname'.$matches[ 1 ] ] ) )
		{
			$from = strpos( $message, $matches[ 0 ] );
			if( preg_match( "/<%\s*fieldname(".$matches[ 1 ].")_endblock\s*%>/", $message, $matches_end ) )
			{
				$lenght = strpos( $message, $matches_end[ 0 ] ) + strlen( $matches_end[ 0 ] ) - $from;
			}
			else
			{
				$lenght = strlen( $matches[ 0 ] );
			}
			$message = substr_replace( $message, '', $from, $lenght );
		}
		else
		{
			$message = preg_replace( array( "/<%\s*fieldname".$matches[ 1 ]."_block\s*%>/", "/<%\s*fieldname".$matches[ 1 ]."_endblock\s*%>/"), "", $message );
		}
	}

	$tags = _dex_bccf_extract_tags( $message );

	if ( 'html' == $contentType )
    {
        $message = str_replace( "\n", "", $message );
        $buffer = str_replace( array('&lt;', '&gt;', '\"', "\'"), array('<', '>', '"', "'" ), $buffer );
    }

	// Replace the INFO tags
    if( !empty( $tags[ 'info' ] ) )
	{
		$buffer1 = $buffer;
		do{
			$tmp = $buffer1;
			$buffer1 = preg_replace(
				array(
					"/^[^\n:]*:{1,2}\s*\n/",
					"/\n[^\n:]*:{1,2}\s*\n/",
					"/\n[^\n:]*:{1,2}\s*$/"
				),
				array(
					"",
					"\n",
					""
				),
				$buffer1
			);
		}while( $buffer1 <> $tmp );

		foreach( $tags[ 'info' ] as $tagData )
		{
			$singleReplacementFunction( $tagData, ( ( $tagData[ 'if_not_empty' ] ) ? $buffer1 : $buffer ), $message );
		}
		unset( $tags[ 'info' ] );
	}

	foreach ($params as $item => $value)
    {
		$value_bk = $value;
		if( isset( $tags[ $item ] ) )
		{
			$label 		= ( isset( $fields[ $item ] ) && property_exists( $fields[ $item ], 'title' ) ) ? $fields[ $item ]->title : '';
			$shortlabel = ( isset( $fields[ $item ] ) && property_exists( $fields[ $item ], 'shortlabel' ) ) ? $fields[ $item ]->shortlabel : '';
			$value = ( !empty( $value ) || is_numeric( $value ) && $value == 0 ) ? ( ( is_array( $value ) ) ? implode( ", ", $value ) : $value ) : '';

			if ( 'html' == $contentType )
			{
				$label = str_replace( array('&lt;', '&gt;', '\"', "\'"), array('<', '>', '"', "'" ), $label );
				$shortlabel = str_replace( array('&lt;', '&gt;', '\"', "\'"), array('<', '>', '"', "'" ), $shortlabel );
				$value = str_replace( array('&lt;', '&gt;', '\"', "\'"), array('<', '>', '"', "'" ), $value );
			}

			foreach( $tags[ $item ] as $tagData )
			{
				if( $tagData[ 'if_not_empty' ] == 0 || $value !== '' )
				{
					switch( $tagData[ 'tag' ] )
					{
						case $item:
							$singleReplacementFunction( $tagData, $label.$tagData[ 'separator' ].$value, $message );
						break;
						case $item.'_label':
							$singleReplacementFunction( $tagData, $label, $message );
						break;
						case $item.'_value':
							$singleReplacementFunction( $tagData, $value, $message );
						break;
						case $item.'_shortlabel':
							$singleReplacementFunction( $tagData, $shortlabel, $message );
						break;
					}
				}
				else
				{
					$message = str_replace( $tagData[ 'node' ], '', $message );
				}
			}
			unset( $tags[ $item ] );
		}

        if( preg_match( "/_link\b/i", $item ) )
        {
            $attachments = array_merge( $attachments, $value_bk );
        }
    }

	$arrayReplacementFunction( $tags, 'itemnumber', $itemnumber, $message );
	$arrayReplacementFunction( $tags, 'currentdate_mmddyyyy', date("m/d/Y H:i:s"), $message );
	$arrayReplacementFunction( $tags, 'currentdate_ddmmyyyy', date("d/m/Y H:i:s"), $message );
	$arrayReplacementFunction( $tags, 'ipaddress', $fields[ 'ipaddr' ], $message );

    // Replace coupons code
	if( isset( $_REQUEST[ 'couponcode' ] ) && isset( $tags[ 'couponcode' ] ) )
    {
		$arrayReplacementFunction( $tags, 'couponcode', $_REQUEST[ 'couponcode' ], $message );
    }

	foreach( $tags as $tagArr )
    {
        foreach( $tagArr as $tagData )
		{
			$message = str_replace( $tagData[ 'node' ], '', $message );
		}
	}

    if ( 'html' == $contentType )
    {
        $message = str_replace( "\n", "<br>", $message );
    }
    $message = str_replace( '\\', '', stripslashes( stripcslashes( $message ) ) );

	return array( 'message' => $message, 'attachments' => $attachments );
}


//* WIDGET CODE BELOW

class DEX_Bccf_Widget extends WP_Widget
{
  function __construct()
  {
    $widget_ops = array('classname' => 'DEX_Bccf_Widget', 'description' => 'Displays a booking form' );
    parent::__construct('DEX_Bccf_Widget', 'Booking Calendar Contact Form', $widget_ops);
  }

  function form($instance)
  {
    $instance = wp_parse_args( (array) $instance, array( 'title' => '', 'calendarid' => '' ) );
    $title = $instance['title'];
    $calendarid = $instance['calendarid'];
?>
  <p><label for="<?php echo $this->get_field_id('title'); ?>">Title: <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo esc_attr($title); ?>" /></label>
  <label for="<?php echo $this->get_field_id('calendarid'); ?>">Calendar ID: <input class="widefat" id="<?php echo $this->get_field_id('calendarid'); ?>" name="<?php echo $this->get_field_name('calendarid'); ?>" type="text" value="<?php echo esc_attr($calendarid); ?>" /></label>
  </p>
<?php
  }

  function update($new_instance, $old_instance)
  {
    $instance = $old_instance;
    $instance['title'] = $new_instance['title'];
    $instance['calendarid'] = $new_instance['calendarid'];
    return $instance;
  }

  function widget($args, $instance)
  {
    extract($args, EXTR_SKIP);

    echo $before_widget;
    $title = empty($instance['title']) ? ' ' : apply_filters('widget_title', $instance['title']);
    $calendarid = $instance['calendarid'];

    if (!empty($title))
      echo $before_title . $title . $after_title;

    if ($calendarid != '')
        define ('DEX_BCCF_CALENDAR_FIXED_ID',$calendarid);

    // WIDGET CODE GOES HERE
    dex_bccf_get_public_form();

    echo $after_widget;
  }

}
add_action( 'widgets_init', create_function('', 'return register_widget("DEX_Bccf_Widget");') );


?>