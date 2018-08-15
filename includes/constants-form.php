<?php
/**
 * Form and calendar constants.
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

define( 'DEX_BCCF_DEFAULT_form_structure', '[[{"name":"email","index":0,"title":"Email","ftype":"femail","userhelp":"","csslayout":"","required":true,"predefined":"","size":"medium"},{"name":"subject","index":1,"title":"Subject","required":true,"ftype":"ftext","userhelp":"","csslayout":"","predefined":"","size":"medium"},{"name":"message","index":2,"size":"large","required":true,"title":"Message","ftype":"ftextarea","userhelp":"","csslayout":"","predefined":""}],[{"title":"","description":"","formlayout":"top_aligned"}]]' );

define( 'DEX_BCCF_DEFAULT_DEFER_SCRIPTS_LOADING', ( get_option( 'CP_BCCF_LOAD_SCRIPTS', '1' ) == '1' ? true : false ) );

define( 'DEX_BCCF_DEFAULT_SERVICES_FIELDS', 6 );

define( 'DEX_BCCF_DEFAULT_COLOR_STARTRES', '#CAFFCA' );
define( 'DEX_BCCF_DEFAULT_COLOR_HOLIDAY', '#FF8080' );

define( 'DEX_BCCF_DEFAULT_SERVICES_FIELDS_ON_TOP', false );

define( 'DEX_BCCF_DEFAULT_CALENDAR_LANGUAGE', '' );
define( 'DEX_BCCF_DEFAULT_CALENDAR_DATEFORMAT', 'false' );
define( 'DEX_BCCF_DEFAULT_CALENDAR_WEEKDAY', '0' );
define( 'DEX_BCCF_DEFAULT_CALENDAR_MINDATE', 'today' );
define( 'DEX_BCCF_DEFAULT_CALENDAR_MAXDATE', '' );
define( 'DEX_BCCF_DEFAULT_CALENDAR_PAGES', 2 );
define( 'DEX_BCCF_DEFAULT_CALENDAR_OVERLAPPED', 'false' );
define( 'DEX_BCCF_DEFAULT_CALENDAR_ENABLED', 'true' );

define( 'DEX_BCCF_DEFAULT_cu_user_email_field', 'email' );
define( 'TDE_BCCFCALENDAR_DEFAULT_COLOR', '6FF' );

define( 'DEX_BCCF_DEFAULT_ENABLE_PAYPAL', 1);
define( 'DEX_BCCF_DEFAULT_PAYPAL_EMAIL', 'email@example.com' );

define( 'DEX_BCCF_DEFAULT_PRODUCT_NAME', 'Reservation' );
define( 'DEX_BCCF_DEFAULT_COST', '25' );
define( 'DEX_BCCF_DEFAULT_OK_URL', get_site_url() );
define( 'DEX_BCCF_DEFAULT_CANCEL_URL', get_site_url() );
define( 'DEX_BCCF_DEFAULT_CURRENCY', 'USD' );
define( 'DEX_BCCF_DEFAULT_PAYPAL_LANGUAGE', 'EN' );

define( 'DEX_BCCF_DEFAULT_PAYPAL_OPTION_YES', 'Pay with PayPal.' );
define( 'DEX_BCCF_DEFAULT_PAYPAL_OPTION_NO', 'Pay later.' );

define( 'DEX_BCCF_DEFAULT_vs_text_is_required', 'This field is required.' );
define( 'DEX_BCCF_DEFAULT_vs_text_is_email', 'Please enter a valid email address.' );

define( 'DEX_BCCF_DEFAULT_vs_text_datemmddyyyy', 'Please enter a valid date with this format ( mm/dd/yyyy )' );
define( 'DEX_BCCF_DEFAULT_vs_text_dateddmmyyyy', 'Please enter a valid date with this format ( dd/mm/yyyy )' );
define( 'DEX_BCCF_DEFAULT_vs_text_number', 'Please enter a valid number.' );
define( 'DEX_BCCF_DEFAULT_vs_text_digits', 'Please enter only digits.' );
define( 'DEX_BCCF_DEFAULT_vs_text_max', 'Please enter a value less than or equal to {0}.' );
define( 'DEX_BCCF_DEFAULT_vs_text_min', 'Please enter a value greater than or equal to {0}.' );

define( 'DEX_BCCF_DEFAULT_SUBJECT_CONFIRMATION_EMAIL', 'Thank you for your request' );
define( 'DEX_BCCF_DEFAULT_CONFIRMATION_EMAIL', "We have received your request with the following information:\n\n%INFORMATION%\n\nThank you.\n\nBest regards." );
define( 'DEX_BCCF_DEFAULT_SUBJECT_NOTIFICATION_EMAIL', 'New reservation requested' );
define( 'DEX_BCCF_DEFAULT_NOTIFICATION_EMAIL', "New reservation made with the following information:\n\n%INFORMATION%\n\nBest regards." );

define( 'DEX_BCCF_DEFAULT_REMINDER_CONTENT', "This is a reminder for your booking with the following information:\n\n%INFORMATION%\n\nThank you.\n\nBest regards." );
define( 'DEX_BCCF_DEFAULT_REMINDER_CONTENT_AFTER', "Thank you for your booking. here is the booking information for future reference:\n\n%INFORMATION%\n\nThank you.\n\nBest regards." );

define( 'DEX_BCCF_DEFAULT_CP_CAL_CHECKBOXES', '' );
define( 'DEX_BCCF_DEFAULT_CP_CAL_CHECKBOXES_TYPE', '0' );
define( 'DEX_BCCF_DEFAULT_EXPLAIN_CP_CAL_CHECKBOXES', "80.00 | 2 Guests\n120.00 | 3 Guests\n160.00 | 4 Guests" );

define( 'TDE_BCCFDEFAULT_CALENDAR_ID', '1' );
define( 'TDE_BCCFDEFAULT_CALENDAR_LANGUAGE', 'EN' );
define( 'DEX_BCCF_DEFAULT_CALENDAR_MODE', 'true' );

define( 'TDE_BCCFCAL_PREFIX', 'RCalendar' );
define( 'TDE_BCCFCONFIG', DEX_BCCF_CONFIG_TABLE_NAME );
define( 'TDE_BCCFCONFIG_ID', 'id' );
define( 'TDE_BCCFCONFIG_TITLE', 'title' );
define( 'TDE_BCCFCONFIG_USER', 'uname' );
define( 'TDE_BCCFCONFIG_PASS', 'passwd' );
define( 'TDE_BCCFCONFIG_LANG', 'lang' );
define( 'TDE_BCCFCONFIG_CPAGES', 'cpages' );
define( 'TDE_BCCFCONFIG_MSG', 'msg' );
define( 'TDE_BCCFCALDELETED_FIELD', 'caldeleted' );

define( 'TDE_BCCFCALENDAR_DATA_TABLE', DEX_BCCF_CALENDARS_TABLE_NAME );
define( 'TDE_BCCFDATA_ID', 'id' );
define( 'TDE_BCCFDATA_IDCALENDAR', 'reservation_calendar_id' );
define( 'TDE_BCCFDATA_DATETIME_S', 'datatime_s' );
define( 'TDE_BCCFDATA_DATETIME_E', 'datatime_e' );
define( 'TDE_BCCFDATA_TITLE', 'title' );
define( 'TDE_BCCFDATA_DESCRIPTION', 'description' );

define( 'TDE_BCCFDEFAULT_dexcv_enable_captcha', 'true' );
define( 'TDE_BCCFDEFAULT_dexcv_width', '180' );
define( 'TDE_BCCFDEFAULT_dexcv_height', '60' );
define( 'TDE_BCCFDEFAULT_dexcv_chars', '5' );
define( 'TDE_BCCFDEFAULT_dexcv_font', 'font-1.ttf' );
define( 'TDE_BCCFDEFAULT_dexcv_min_font_size', '25' );
define( 'TDE_BCCFDEFAULT_dexcv_max_font_size', '35' );
define( 'TDE_BCCFDEFAULT_dexcv_noise', '200' );
define( 'TDE_BCCFDEFAULT_dexcv_noise_length', '4' );
define( 'TDE_BCCFDEFAULT_dexcv_background', 'ffffff' );
define( 'TDE_BCCFDEFAULT_dexcv_border', '000000' );
define( 'DEX_BCCF_DEFAULT_dexcv_text_enter_valid_captcha', 'Please enter a valid captcha code.' );