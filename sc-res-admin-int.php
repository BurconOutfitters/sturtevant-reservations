<?php

if ( !is_admin() )
{
    echo 'Direct access not allowed.';
    exit;
}

dex_bccf_add_field_verify(DEX_BCCF_CONFIG_TABLE_NAME, "master", "varchar(50) DEFAULT '0' NOT NULL");

if (!defined('CP_BCCF_CALENDAR_ID'))
    define ('CP_BCCF_CALENDAR_ID',intval($_GET["cal"]));

global $wpdb, $dexbccf_addons_objs_list, $dexbccf_addons_active_list;
$mycalendarrows = $wpdb->get_results( 'SELECT * FROM '.DEX_BCCF_CONFIG_TABLE_NAME .' WHERE `'.TDE_BCCFCONFIG_ID.'`='.CP_BCCF_CALENDAR_ID);


if ( 'POST' == $_SERVER['REQUEST_METHOD'] && isset( $_POST['dex_bccf_post_options'] ) )
    echo "<div id='setting-error-settings_updated' class='updated settings-error'> <p><strong>Settings saved.</strong></p></div>";

$current_user = wp_get_current_user();

if (cp_bccf_is_administrator() || $mycalendarrows[0]->conwer == $current_user->ID) {


$request_costs = explode(";",dex_bccf_get_option('request_cost',DEX_BCCF_DEFAULT_COST));
if (!count($request_costs)) $request_costs[0] = DEX_BCCF_DEFAULT_COST;

$request_costs_exploded = "'".str_replace("'","\'",$request_costs[0])."'";
for ($k=1;$k<100;$k++)
   if (isset($request_costs[$k]))
       $request_costs_exploded .= ",'".str_replace("'","\'",$request_costs[$k])."'";
   else
       $request_costs_exploded .= ",'".str_replace("'","\'",$request_costs[0]*($k))."'";


?>
<link href="<?php echo plugins_url('css/style.css', __FILE__); ?>" type="text/css" rel="stylesheet" />
<link href="<?php echo plugins_url('css/calendar.css', __FILE__); ?>" type="text/css" rel="stylesheet" />
<link href="<?php echo plugins_url('css/admin.css', __FILE__); ?>" type="text/css" rel="stylesheet" />
 
<script type="text/javascript"> 
  if (false)
  {
    document.write ("<"+"script type='text/javascript' src='http://ajax.googleapis.com/ajax/libs/jquery/1.7.2/jquery.min.js'></"+"script>");
    document.write ("<"+"script type='text/javascript' src='http://ajax.googleapis.com/ajax/libs/jqueryui/1.8.20/jquery-ui.min.js'></"+"script>");
  }
</script>


<div class="wrap">
<h1>Reservation Form Settings</h1>

<input type="button" name="backbtn" value="Back to items list..." onclick="document.location='admin.php?page=dex_bccf';">

<form method="post" name="dexconfigofrm" action="">
<input name="dex_bccf_post_options" type="hidden" value="1" />
<input name="dex_item" type="hidden" value="<?php echo intval($_GET["cal"]); ?>" />

<div id="normal-sortables" class="meta-box-sortables">

 <hr />
 <h2>These settings only apply to <?php echo $mycalendarrows[0]->uname; ?></h2>

 <div id="metabox_basic_settings" class="postbox" >
  <h2 class='hndle' style="padding:5px;"><span>Advanced Settings</span></h2>
  <div class="inside">
       <table class="form-table">
        <tr valign="top">        
        <th scope="row">Master Calendar</th>
        <td>
             <?php 
               $value = dex_bccf_get_option('master','0'); 
               $calendars = $wpdb->get_results( 'SELECT * FROM '.DEX_BCCF_CONFIG_TABLE_NAME .' WHERE (master=\'0\' Or master=\'\') AND  `id`<>'.CP_BCCF_CALENDAR_ID); 
             ?>             
             <select name="master" id="masteritem" onchange="shcalarea();">               
               <option value="0" selected="selected" <?php if ($value == '0') echo ' selected="selected"'; ?>> - </option>               
               <?php foreach ($calendars as $item) { ?>
               <option value="<?php echo $item->id; ?>" <?php if (intval($value) == $item->id) echo ' selected="selected"'; ?>> <?php echo $item->uname; ?> </option>
               <?php } ?> 
            </select><br />
            * If selected, the selected calendar will be used as source for the availability, so it will be used as the 
            "master calendar". The bookings for all the calendars with the same assigned "master calendar" will count together for the 
            availability verification.
        </td>
        </tr>        
       </table> 
  </div>  
</div>  

 <div id="metabox_basic_settings" class="postbox" >
  <h2 class='hndle' style="padding:5px;"><span>Calendar Configuration / Administration</span></h2>
  <div class="inside">
     <table class="form-table">
        <tr valign="top">
        <td colspan="5">
          &nbsp; <strong>Display calendar in the booking form?</strong><br />
          <?php $option = dex_bccf_get_option('calendar_enabled', DEX_BCCF_DEFAULT_CALENDAR_ENABLED); ?>
          &nbsp; <select name="calendar_enabled">
           <option value="true"<?php if ($option == 'true') echo ' selected'; ?>>Yes</option>
           <option value="false"<?php if ($option == 'false') echo ' selected'; ?>>No</option>
          </select>
        </td>
        </tr>
     </table>
<div id="metabox_basic_settings_cal1" style=""> 
<?php
    $option_use_calendar = $option;
    $option_overlapped = dex_bccf_get_option('calendar_overlapped', DEX_BCCF_DEFAULT_CALENDAR_OVERLAPPED);
    $calendar_language = dex_bccf_get_option('calendar_language',DEX_BCCF_DEFAULT_CALENDAR_LANGUAGE);
    
    if ($calendar_language == '') $calendar_language = dex_bccf_autodetect_language();  
    
    $calendar_dateformat = dex_bccf_get_option('calendar_dateformat',DEX_BCCF_DEFAULT_CALENDAR_DATEFORMAT);
    $dformat = ((dex_bccf_get_option('calendar_dateformat', DEX_BCCF_DEFAULT_CALENDAR_DATEFORMAT)==0)?"mm/dd/yy":"dd/mm/yy");
    $dformat_php = ((dex_bccf_get_option('calendar_dateformat', DEX_BCCF_DEFAULT_CALENDAR_DATEFORMAT)==0)?"m/d/Y":"d/m/Y");
    $calendar_mindate = "";
    $value = dex_bccf_get_option('calendar_mindate',DEX_BCCF_DEFAULT_CALENDAR_MINDATE);
    if ($value != '') $calendar_mindate = date($dformat_php, strtotime($value));
    $calendar_maxdate = "";
    $value = dex_bccf_get_option('calendar_maxdate',DEX_BCCF_DEFAULT_CALENDAR_MAXDATE);
    if ($value != '') $calendar_maxdate = date($dformat_php, strtotime($value));
    if ($option_use_calendar == 'false')
    {
?>
   <div style="background-color:#bbffff;width:450px;border: 1px solid black;padding:10px;margin:10px;">
    <strong>Note:</strong> Calendar has been disabled in the field above, so there isn't need to display and edit it.
     <strong>To re-enable</strong> the calendar select that option in the field above and <strong>save the settings</strong> to render the calendar again.
   </div>
<?php
  //  } else if ($option_overlapped == 'true') {
?>
   <!-- <div style="background-color:#ffff55;width:450px;border: 1px solid black;padding:10px;margin:10px;">
    <strong>Note:</strong> Overlapped reservations are enabled below, so you cannot use the calendar to block dates and the booking should be checked in the <a href="admin.php?page=dex_bccf&cal=<?php echo CP_BCCF_CALENDAR_ID; ?>&list=1">bookings list area</a>.
   </div>
    -->
<?php } else { ?>


   <script>
   var pathCalendar = "<?php echo cp_bccf_get_site_url(); ?>/";
   var pathCalendar_full = pathCalendar + "wp-content/plugins/<?php echo basename(dirname(__FILE__));?>/css/images/corners";
   </script>

   <div id="cal<?php echo CP_BCCF_CALENDAR_ID; ?>" class="rcalendar"><span style="color:#009900">Loading calendar data...</span></em></div>
<?php if ($calendar_language != '') { ?><script type="text/javascript" src="<?php echo plugins_url('js/languages/jquery.ui.datepicker-'.$calendar_language.'.js', __FILE__); ?>"></script><?php } ?>

   <script type="text/javascript">
    jQuery(function(){
    (function($) {   
        $calendarjQuery = jQuery.noConflict();
        $calendarjQuery(function() {
        $calendarjQuery("#cal<?php echo CP_BCCF_CALENDAR_ID; ?>").rcalendar({"calendarId":<?php echo CP_BCCF_CALENDAR_ID; ?>,
                                            "partialDate":<?php echo dex_bccf_get_option('calendar_mode',DEX_BCCF_DEFAULT_CALENDAR_MODE); ?>,
                                            "edition":true,
                                            //"minDate":"<?php echo $calendar_mindate;?>",
                                            //"maxDate":"<?php echo $calendar_maxdate;?>",
                                            "dformat":"<?php echo $dformat;?>",
                                            "language":"<?php echo $calendar_language?>",
                                            "firstDay":<?php echo dex_bccf_get_option('calendar_weekday', DEX_BCCF_DEFAULT_CALENDAR_WEEKDAY); ?>,
                                            "numberOfMonths":<?php echo dex_bccf_get_option('calendar_pages',DEX_BCCF_DEFAULT_CALENDAR_PAGES); ?>
                                            });
       
        });
    })(jQuery);
    });
   </script>

   <div style="clear:both;height:20px" ></div>

<?php if ($option_overlapped == 'true') { ?>
<div style="background-color:#ffffdd;width:450px;border: 1px solid black;padding:10px;margin:10px;">
    <strong>Note:</strong> Overlapped reservations are enabled below and you can use the calendar for blocking dates, however only the blocked dates are shown in the calendar. The bookings should be checked in the <a href="admin.php?page=dex_bccf&cal=<?php echo CP_BCCF_CALENDAR_ID; ?>&list=1">bookings list area</a>.
   </div>
<?php } ?>

<?php } ?>

</div>
<div id="metabox_basic_settings_cal2" style="display:none;background-color:#bbffff;width:450px;border: 1px solid black;padding:10px;margin:10px;"> 
    The calendar availability is disabled in these settings because are being loaded from another "<strong>Master Calendar</strong>". See the "<strong>Master Calendar</strong>" selected above.
</div> 

<?php if ($option_use_calendar != 'false') { ?>
   <div id="demo" class="yui-navset"></div>
   <div style="clear:both;height:0px" ></div>

   <table class="form-table" style="width:870px;">
        <tr valign="top">
         <td colspan="4" style="padding:0px;background-color:#E2EFF8;color:#666666;font-weight:bold;text-align:center">
           <strong>Settings for both admin and public calendars</strong>
         </td>
        </tr> 
        <tr valign="top">
        <td colspan="4">

          <div style="float:left;width:80px;">
            <strong>Cal. Pages:</strong><br />
             <?php $value = dex_bccf_get_option('calendar_pages',DEX_BCCF_DEFAULT_CALENDAR_PAGES); ?>
             <select name="calendar_pages">
               <option value="1" <?php if ($value == '1') echo ' selected="selected"'; ?>>1</option>
               <option value="2" <?php if ($value == '2') echo ' selected="selected"'; ?>>2</option>
               <option value="3" <?php if ($value == '3') echo ' selected="selected"'; ?>>3</option>
               <option value="4" <?php if ($value == '4') echo ' selected="selected"'; ?>>4</option>
               <option value="5" <?php if ($value == '5') echo ' selected="selected"'; ?>>5</option>
               <option value="6" <?php if ($value == '6') echo ' selected="selected"'; ?>>6</option>
               <option value="7" <?php if ($value == '7') echo ' selected="selected"'; ?>>7</option>
               <option value="8" <?php if ($value == '8') echo ' selected="selected"'; ?>>8</option>
               <option value="9" <?php if ($value == '9') echo ' selected="selected"'; ?>>9</option>
               <option value="10" <?php if ($value == '10') echo ' selected="selected"'; ?>>10</option>
               <option value="11" <?php if ($value == '11') echo ' selected="selected"'; ?>>11</option>
               <option value="12" <?php if ($value == '12') echo ' selected="selected"'; ?>>12</option>
             </select>
          </div>

          <div style="float:left;width:200px;">          
            <strong>Calendar language</strong><br />
<?php $v = dex_bccf_get_option('calendar_language',DEX_BCCF_DEFAULT_CALENDAR_LANGUAGE); ?>            
             <select name="calendar_language" id="calendar_language">
<option <?php if ($v == '') echo 'selected'; ?> value=""> - auto-detect - </option>
<option <?php if ($v == 'af') echo 'selected'; ?> value="af">Afrikaans</option>
<option <?php if ($v == 'sq') echo 'selected'; ?> value="sq">Albanian</option>
<option <?php if ($v == 'ar') echo 'selected'; ?> value="ar">Arabic</option>
<option <?php if ($v == 'ar-DZ') echo 'selected'; ?> value="ar-DZ">Arabic (Algeria)</option>
<option <?php if ($v == 'hy') echo 'selected'; ?> value="hy">Armenian</option>
<option <?php if ($v == 'az') echo 'selected'; ?> value="az">Azerbaijani</option>
<option <?php if ($v == 'eu') echo 'selected'; ?> value="eu">Basque</option>
<option <?php if ($v == 'bs') echo 'selected'; ?> value="bs">Bosnian</option>
<option <?php if ($v == 'bg') echo 'selected'; ?> value="bg">Bulgarian</option>
<option <?php if ($v == 'be') echo 'selected'; ?> value="be">Byelorussian (Belarusian)</option>
<option <?php if ($v == 'km') echo 'selected'; ?> value="km">Cambodian</option>
<option <?php if ($v == 'ca') echo 'selected'; ?> value="ca">Catalan</option>
<option <?php if ($v == 'zh-HK') echo 'selected'; ?> value="zh-HK">Chinese (Hong Kong SAR)</option>
<option <?php if ($v == 'zh-CN') echo 'selected'; ?> value="zh-CN">Chinese (PRC)</option>
<option <?php if ($v == 'zh-TW') echo 'selected'; ?> value="zh-TW">Chinese (Taiwan)</option>
<option <?php if ($v == 'hr') echo 'selected'; ?> value="hr">Croatian</option>
<option <?php if ($v == 'cs') echo 'selected'; ?> value="cs">Czech</option>
<option <?php if ($v == 'da') echo 'selected'; ?> value="da">Danish</option>
<option <?php if ($v == 'nl') echo 'selected'; ?> value="nl_NL">Dutch</option>
<option <?php if ($v == 'nl-BE') echo 'selected'; ?> value="nl-BE">Dutch - Belgium</option>
<option <?php if ($v == 'en-AU') echo 'selected'; ?> value="en-AU">English (Australia)</option>
<option <?php if ($v == 'en-NZ') echo 'selected'; ?> value="en-NZ">English (New Zealand)</option>
<option <?php if ($v == 'en-GB') echo 'selected'; ?> value="en-GB">English (United Kingdom)</option>
<option <?php if ($v == 'eo') echo 'selected'; ?> value="eo">Esperanto</option>
<option <?php if ($v == 'et') echo 'selected'; ?> value="et">Estonian</option>
<option <?php if ($v == 'fo') echo 'selected'; ?> value="fo">Faeroese</option>
<option <?php if ($v == 'fa') echo 'selected'; ?> value="fa">Farsi</option>
<option <?php if ($v == 'fi') echo 'selected'; ?> value="fi">Finnish</option>
<option <?php if ($v == 'fr') echo 'selected'; ?> value="fr">French</option>
<option <?php if ($v == 'fr-CA') echo 'selected'; ?> value="fr-CA">French (Canada)</option>
<option <?php if ($v == 'fr-CH') echo 'selected'; ?> value="fr-CH">French (Switzerland)</option>
<option <?php if ($v == 'gl') echo 'selected'; ?> value="gl">Galician</option>
<option <?php if ($v == 'ka') echo 'selected'; ?> value="ka">Georgian</option>
<option <?php if ($v == 'de') echo 'selected'; ?> value="de">German</option>
<option <?php if ($v == 'el') echo 'selected'; ?> value="el">Greek</option>
<option <?php if ($v == 'he') echo 'selected'; ?> value="he">Hebrew</option>
<option <?php if ($v == 'hi') echo 'selected'; ?> value="hi">Hindi</option>
<option <?php if ($v == 'hu') echo 'selected'; ?> value="hu">Hungarian</option>
<option <?php if ($v == 'is') echo 'selected'; ?> value="is">Icelandic</option>
<option <?php if ($v == 'id') echo 'selected'; ?> value="id">Indonesian</option>
<option <?php if ($v == 'it') echo 'selected'; ?> value="it_IT">Italian</option>
<option <?php if ($v == 'it-CH') echo 'selected'; ?> value="it-CH">Italian (Switzerland)</option>
<option <?php if ($v == 'ja') echo 'selected'; ?> value="ja">Japanese</option>
<option <?php if ($v == 'kk') echo 'selected'; ?> value="kk">Kazakh</option>
<option <?php if ($v == 'ky') echo 'selected'; ?> value="ky">Kirghiz</option>
<option <?php if ($v == 'ko') echo 'selected'; ?> value="ko">Korean</option>
<option <?php if ($v == 'lv') echo 'selected'; ?> value="lv">Latvian (Lettish)</option>
<option <?php if ($v == 'lt') echo 'selected'; ?> value="lt">Lithuanian</option>
<option <?php if ($v == 'lb') echo 'selected'; ?> value="lb">Luxembourgish</option>
<option <?php if ($v == 'mk') echo 'selected'; ?> value="mk">Macedonian</option>
<option <?php if ($v == 'ms') echo 'selected'; ?> value="ms">Malay</option>
<option <?php if ($v == 'ml') echo 'selected'; ?> value="ml">Malayalam</option>
<option <?php if ($v == 'no') echo 'selected'; ?> value="no">Norwegian</option>
<option <?php if ($v == 'nb') echo 'selected'; ?> value="nb">Norwegian (Bokm&aring;l)</option>
<option <?php if ($v == 'nn') echo 'selected'; ?> value="nn">Norwegian Nynorsk</option>
<option <?php if ($v == 'pl') echo 'selected'; ?> value="pl_PL">Polish</option>
<option <?php if ($v == 'pt') echo 'selected'; ?> value="pt">Portuguese</option>
<option <?php if ($v == 'pt-BR') echo 'selected'; ?> value="pt-BR">Portuguese (Brazil)</option>
<option <?php if ($v == 'rm') echo 'selected'; ?> value="rm">Rhaeto-Romance</option>
<option <?php if ($v == 'ro') echo 'selected'; ?> value="ro">Romanian</option>
<option <?php if ($v == 'ru') echo 'selected'; ?> value="ru">Russian</option>
<option <?php if ($v == 'sr-SR') echo 'selected'; ?> value="sr-SR">Serbian</option>
<option <?php if ($v == 'sr') echo 'selected'; ?> value="sr">Serbian</option>
<option <?php if ($v == 'sk') echo 'selected'; ?> value="sk">Slovak</option>
<option <?php if ($v == 'sl') echo 'selected'; ?> value="sl">Slovenian</option>
<option <?php if ($v == 'es') echo 'selected'; ?> value="es">Spanish</option>
<option <?php if ($v == 'sv') echo 'selected'; ?> value="sv">Swedish</option>
<option <?php if ($v == 'tj') echo 'selected'; ?> value="tj">Tajikistan</option>
<option <?php if ($v == 'ta') echo 'selected'; ?> value="ta">Tamil</option>
<option <?php if ($v == 'th') echo 'selected'; ?> value="th">Thai</option>
<option <?php if ($v == 'tr') echo 'selected'; ?> value="tr">Turkish</option>
<option <?php if ($v == 'uk') echo 'selected'; ?> value="uk">Ukrainian</option>
<option <?php if ($v == 'vi') echo 'selected'; ?> value="vi">Vietnamese</option>
<option <?php if ($v == 'cy-GB') echo 'selected'; ?> value="cy-GB">Welsh/UK</option>
            </select>
          </div>

          <div style="float:left;width:120px;">
             <strong>Start weekday</strong><br />
             <?php $value = dex_bccf_get_option('calendar_weekday',DEX_BCCF_DEFAULT_CALENDAR_WEEKDAY); ?>
             <select name="calendar_weekday">
               <option value="0" <?php if ($value == '0') echo ' selected="selected"'; ?>>Sunday</option>
               <option value="1" <?php if ($value == '1') echo ' selected="selected"'; ?>>Monday</option>
               <option value="2" <?php if ($value == '2') echo ' selected="selected"'; ?>>Tuesday</option>
               <option value="3" <?php if ($value == '3') echo ' selected="selected"'; ?>>Wednesday</option>
               <option value="4" <?php if ($value == '4') echo ' selected="selected"'; ?>>Thursday</option>
               <option value="5" <?php if ($value == '5') echo ' selected="selected"'; ?>>Friday</option>
               <option value="6" <?php if ($value == '6') echo ' selected="selected"'; ?>>Saturday</option>
             </select>
          </div>

          <div style="float:left;width:110px;padding-right:3px;">
             <strong>Date format</strong><br />
             <select name="calendar_dateformat">
               <option value="0" <?php if ($calendar_dateformat == '0') echo ' selected="selected"'; ?>>mm/dd/yyyy</option>
               <option value="1" <?php if ($calendar_dateformat == '1') echo ' selected="selected"'; ?>>dd/mm/yyyy</option>
             </select>
          </div>

          <div style="float:left;width:225px;">
             <strong>Accept overlapped reservations?</strong><br />
             <?php $option = dex_bccf_get_option('calendar_overlapped', DEX_BCCF_DEFAULT_CALENDAR_OVERLAPPED); ?>
             <select name="calendar_overlapped">
              <option value="true"<?php if ($option == 'true') echo ' selected'; ?>>Yes</option>
              <option value="false"<?php if ($option == 'false') echo ' selected'; ?>>No</option>
             </select>
          </div>

        </td>
       </tr>
       <tr>
        <td valign="top" colspan="4">
           <div style="width:190px;float:left"> 
          <strong>Show cost below calendar?</strong><br />
             <?php $value = dex_bccf_get_option('calendar_showcost','1'); ?>
             <select name="calendar_showcost">
               <option value="1" <?php if ($value == '1') echo ' selected="selected"'; ?>>Yes</option>
               <option value="0" <?php if ($value == '0') echo ' selected="selected"'; ?>>No</option>
             </select>            
           </div>             
           <div style="width:140px;float:left"> 
             <strong>Reservation Mode</strong><br />
             <?php $value = dex_bccf_get_option('calendar_mode',DEX_BCCF_DEFAULT_CALENDAR_MODE); ?>
             <select name="calendar_mode">
               <option value="true" <?php if ($value == 'true') echo ' selected="selected"'; ?>>Partial Days</option>
               <option value="false" <?php if ($value == 'false') echo ' selected="selected"'; ?>>Complete Days</option>
             </select>
           </div>  
           <div style="width:500px;float:left;padding-top:10px;"> 
             <em style="font-size:11px;">Complete day means that the first and the last days booked are charged as full days;<br />Partial Day means that they are charged as half-days only.</em>
           </div>  
        </td>
       </tr>
       
        <tr valign="top">
         <td colspan="4" style="padding:0px;background-color:#E2EFF8;color:#666666;font-weight:bold;text-align:center">
			<strong>Settings for the public calendar</strong>
         </td>
        </tr> 
               
       <tr>
        <td width="1%" nowrap valign="top" colspan="2">
         <strong>Minimum  available date:</strong><br />
         <input type="text" name="calendar_mindate" size="40" value="<?php echo esc_attr(dex_bccf_get_option('calendar_mindate',DEX_BCCF_DEFAULT_CALENDAR_MINDATE)); ?>" /><br />
         <em style="font-size:11px;">Examples: 2012-10-25, today, today + 3 days</em>
        </td>
        <td valign="top" colspan="2">
         <strong>Maximum  available date:</strong><br />
         <input type="text" name="calendar_maxdate" size="40" value="<?php echo esc_attr(dex_bccf_get_option('calendar_maxdate',DEX_BCCF_DEFAULT_CALENDAR_MAXDATE)); ?>" /><br />
         <em style="font-size:11px;">Examples: 2012-10-25, today, today + 3 days</em>
        </td>
        </tr>

       <tr>
        <td width="1%" nowrap valign="top" colspan="2">
         <strong>Minimum number of nights to be booked:</strong><br />
         <input type="text" name="calendar_minnights" size="40" value="<?php $v = dex_bccf_get_option('calendar_minnights', '0'); echo esc_attr(($v==''?'0':$v)); ?>" /><br />
         <em style="font-size:11px;">The booking form won't accept less than the above nights</em>
        </td>
        <td valign="top" colspan="2">
         <strong>Maximum number of nights to be booked:</strong><br />
         <input type="text" name="calendar_maxnights" size="40" value="<?php $v = dex_bccf_get_option('calendar_maxnights','365'); echo esc_attr(($v==''?'365':$v)); ?>" /><br />
         <em style="font-size:11px;">The booking form won't accept more than the above nights</em>
        </td>
       </tr>

       <tr>
        <td width="1%" nowrap valign="top" colspan="2">
         <strong>Working dates</strong>
         <div id="workingdates">
         <?php $cfmode = dex_bccf_get_option('calendar_holidaysdays', '1111111'); if ($cfmode == '') $cfmode = '1111111'; ?>
         <input type="checkbox" class="wdCheck" value="1" name="wd1" <?php echo ($cfmode[0]=='1'?'checked="checked"':''); ?> /> Su &nbsp; &nbsp; &nbsp;
         <input type="checkbox" class="wdCheck" value="1" name="wd2" <?php echo ($cfmode[1]=='1'?'checked="checked"':''); ?> /> Mo &nbsp; &nbsp; &nbsp;
         <input type="checkbox" class="wdCheck" value="1" name="wd3" <?php echo ($cfmode[2]=='1'?'checked="checked"':''); ?> /> Tu &nbsp; &nbsp; &nbsp;
         <input type="checkbox" class="wdCheck" value="1" name="wd4" <?php echo ($cfmode[3]=='1'?'checked="checked"':''); ?> /> We &nbsp; &nbsp; &nbsp;
         <input type="checkbox" class="wdCheck" value="1" name="wd5" <?php echo ($cfmode[4]=='1'?'checked="checked"':''); ?> /> Th &nbsp; &nbsp; &nbsp;
         <input type="checkbox" class="wdCheck" value="1" name="wd6" <?php echo ($cfmode[5]=='1'?'checked="checked"':''); ?> /> Fr &nbsp; &nbsp; &nbsp;
         <input type="checkbox" class="wdCheck" value="1" name="wd7" <?php echo ($cfmode[6]=='1'?'checked="checked"':''); ?> /> Sa &nbsp; &nbsp; &nbsp;
         <br />
         <em style="font-size:11px;">Working dates are the dates that accept bookings.</em>
         </div>
         <br />
         <div><strong>Start Reservation Date</strong></div>
         <div>
         <?php $cfmode = dex_bccf_get_option('calendar_startresdays', '1111111'); if ($cfmode == '') $cfmode = '1111111'; ?>
         <input type="checkbox" class="srCheck" value="1" name="sd1" id="c0" <?php echo ($cfmode[0]=='1'?'checked="checked"':''); ?> /> Su &nbsp; &nbsp; &nbsp;
         <input type="checkbox" class="srCheck" value="1" name="sd2" id="c1" <?php echo ($cfmode[1]=='1'?'checked="checked"':''); ?> /> Mo &nbsp; &nbsp; &nbsp;
         <input type="checkbox" class="srCheck" value="1" name="sd3" id="c2" <?php echo ($cfmode[2]=='1'?'checked="checked"':''); ?> /> Tu &nbsp; &nbsp; &nbsp;
         <input type="checkbox" class="srCheck" value="1" name="sd4" id="c3" <?php echo ($cfmode[3]=='1'?'checked="checked"':''); ?> /> We &nbsp; &nbsp; &nbsp;
         <input type="checkbox" class="srCheck" value="1" name="sd5" id="c4" <?php echo ($cfmode[4]=='1'?'checked="checked"':''); ?> /> Th &nbsp; &nbsp; &nbsp;
         <input type="checkbox" class="srCheck" value="1" name="sd6" id="c5" <?php echo ($cfmode[5]=='1'?'checked="checked"':''); ?> /> Fr &nbsp; &nbsp; &nbsp;
         <input type="checkbox" class="srCheck" value="1" name="sd7" id="c6" <?php echo ($cfmode[6]=='1'?'checked="checked"':''); ?> /> Sa &nbsp; &nbsp; &nbsp;
         <br /><em style="font-size:11px;">Use this for allowing specific weekdays as start of the reservation.</em>
         </div> 
         <br />
         <div style="background:#E2EFF8;border: 1px dotted #888888;padding:10px;">
             <div><strong><input type="checkbox" value="1" name="calendar_fixedmode" <?php echo esc_attr((dex_bccf_get_option('calendar_fixedmode', '')=='1'?'checked="checked"':'')); ?> id="fixedreservation"> Enable Fixed Reservation Length?</strong>
                 <br />&nbsp;&nbsp;&nbsp;&nbsp; <em style="font-size:11px;">Use this for allowing only bookings of a specific number of days.</em>
             </div>
             <div id="container_fixedreservation" <?php echo (dex_bccf_get_option('calendar_fixedmode', '')=='1'?'':'style="display:none"'); ?>>
                 <br />
                 <?php $v = dex_bccf_get_option('calendar_fixedreslength','1'); ?>
                 Fixed reservation length (days):
                 <select name="calendar_fixedreslength" id="calendar_fixedreslength">
                  <?php for ($k=1;$k<30;$k++) echo '<option value="'.$k.'"'.($k.""==$v?' selected ':'').'>'.$k.'</option>'; ?>
                 </select>
                 <br /><br />
                 

             </div>
         </div>
         <input type="hidden" name="calendar_holidays" id="holidays" value="<?php echo esc_attr(dex_bccf_get_option('calendar_holidays','')); ?>" />
         <input type="hidden" name="calendar_startres" id="startreservation" value="<?php echo esc_attr(dex_bccf_get_option('calendar_startres','')); ?>" />
        </td>
        <td width="1%" nowrap valign="top" colspan="2">
          <strong>Disabled and special dates (see legend below):</strong>
          <div id="calConfig"><em><span style="color:#009900">Loading calendar data...</span></em></div>
          
          <div style="margin-top:5px;margin-left:10px;"><div style="float:left;width:20px;height:20px;margin-right:10px;background-color:#FEA69A;"></div> <strong>Non-available dates:</strong> Click once to mark the date as non-available.</div>
          <div style="clear:both"></div>
          <div id="startreslegend" style="margin-top:5px;margin-left:10px;"><div style="float:left;width:20px;height:20px;margin-right:10px;background-color:#80BF92;"></div> <strong>Start reservation dates:</strong> Click twice to mark the date as start date.</div>          
          <div style="clear:both"></div>
          <div style="margin-left:35px;"><em style="font-size:11px;">Every time a date is cliked it status changes. Click it to mark/unmark it.</em></div>
        </td>
       </tr>

   </table>
<?php } else { ?>
    <input type="hidden" name="calendar_language" value="<?php echo esc_attr(dex_bccf_get_option('calendar_language',DEX_BCCF_DEFAULT_CALENDAR_LANGUAGE)); ?>" />
    <input type="hidden" name="calendar_weekday" value="<?php echo esc_attr(dex_bccf_get_option('calendar_weekday',DEX_BCCF_DEFAULT_CALENDAR_WEEKDAY)); ?>" />
    <input type="hidden" name="calendar_dateformat" value="<?php echo esc_attr(dex_bccf_get_option('calendar_dateformat',DEX_BCCF_DEFAULT_CALENDAR_DATEFORMAT)); ?>" />
    <input type="hidden" name="calendar_overlapped" value="<?php echo esc_attr(dex_bccf_get_option('calendar_overlapped', DEX_BCCF_DEFAULT_CALENDAR_OVERLAPPED)); ?>" />
    <input type="hidden" name="calendar_mode" value="<?php echo esc_attr(dex_bccf_get_option('calendar_mode',DEX_BCCF_DEFAULT_CALENDAR_MODE)); ?>" />
    <input type="hidden" name="calendar_mindate" value="<?php echo esc_attr(dex_bccf_get_option('calendar_mindate',DEX_BCCF_DEFAULT_CALENDAR_MINDATE)); ?>" />
    <input type="hidden" name="calendar_maxdate" value="<?php echo esc_attr(dex_bccf_get_option('calendar_maxdate',DEX_BCCF_DEFAULT_CALENDAR_MAXDATE)); ?>" />
<?php } ?>

  </div>
 </div>


 <div id="metabox_basic_settings" class="postbox" >
  <h2 class='hndle' style="padding:5px;"><span>Form Builder</span></h2>
  <div class="inside">

     <input type="hidden" name="form_structure" id="form_structure" size="180" value="<?php echo str_replace('"','&quot;',str_replace("\r","",str_replace("\n","",esc_attr(dex_bccf_cleanJSON(dex_bccf_get_option('form_structure', DEX_BCCF_DEFAULT_form_structure)))))); ?>" />
     <input type="hidden" name="templates" id="templates" value="<?php echo esc_attr( json_encode( dex_bccf_available_templates() ) ); ?>" />


     <link href="<?php echo plugins_url('css/cupertino/jquery-ui-1.8.20.custom.css', __FILE__); ?>" type="text/css" rel="stylesheet" />

     <script>
         $contactFormPPQuery = jQuery.noConflict();
         $contactFormPPQuery(document).ready(function() {
            var f = $contactFormPPQuery("#fbuilder").fbuilder();
            f.fBuild.loadData("form_structure", "templates");

            $contactFormPPQuery("#saveForm").click(function() {
                f.fBuild.saveData("form_structure");
            });

            $contactFormPPQuery(".itemForm").click(function() {
     	       f.fBuild.addItem($contactFormPPQuery(this).attr("id"));
     	   });

           $contactFormPPQuery( ".itemForm" ).draggable({revert1: "invalid",helper: "clone",cursor: "move"});
     	   $contactFormPPQuery( "#fbuilder" ).droppable({
     	       accept: ".button",
     	       drop: function( event, ui ) {
     	           f.fBuild.addItem(ui.draggable.attr("id"));
     	       }
     	   });

         });

     </script>

     <div style="background:#fafafa;width:780px;" class="form-builder">

         <div class="column width50">
             <div id="tabs">
     			<ul>
     				<li><a href="#tabs-1">Add a Field</a></li>
     				<li><a href="#tabs-2">Field Settings</a></li>
     				<li><a href="#tabs-3">Form Settings</a></li>
     			</ul>
     			<div id="tabs-1">

     			</div>
     			<div id="tabs-2"></div>
     			<div id="tabs-3"></div>
     		</div>
         </div>
         <div class="columnr width50 padding10" id="fbuilder">
             <div id="formheader"></div>
             <div id="fieldlist"></div>
             <!--<div class="button" id="saveForm">Save Form</div>-->
         </div>
         <div class="clearer"></div>

     </div>

  </div>
 </div>
 
 
 <div id="metabox_basic_settings" class="postbox" >
  <h2 class='hndle' style="padding:5px;"><span>Submit Button</span></h2>
  <div class="inside">   
     <table class="form-table">    
        <tr valign="top">
        <th scope="row">Submit button label (text):</th>
        <td><input type="text" name="vs_text_submitbtn" size="40" value="<?php $label = esc_attr(dex_bccf_get_option('vs_text_submitbtn', 'Continue')); echo ($label==''?'Continue':$label); ?>" /></td>
        </tr>
        <tr valign="top">
        <th scope="row">Previous button label (text):</th>
        <td><input type="text" name="vs_text_previousbtn" size="40" value="<?php $label = esc_attr(dex_bccf_get_option('vs_text_previousbtn', 'Previous')); echo ($label==''?'Previous':$label); ?>" /></td>
        </tr>    
        <tr valign="top">
        <th scope="row">Next button label (text):</th>
        <td><input type="text" name="vs_text_nextbtn" size="40" value="<?php $label = esc_attr(dex_bccf_get_option('vs_text_nextbtn', 'Next')); echo ($label==''?'Next':$label); ?>" /></td>
        </tr>  
        <tr valign="top">
        <td colspan="2"> - The  <em>class="pbSubmit"</em> can be used to modify the button styles. <br />
        - The styles can be applied into any of the CSS files of your theme or into the CSS file <em>"booking-calendar-contact-form\css\stylepublic.css"</em>. <br />
        - For further modifications the submit button is located at the end of the file <em>"sc-res-scheduler.php"</em>.<br />
        - For general CSS styles modifications to the form and samples <a href="http://wordpress.dwbooster.com/faq/booking-calendar-contact-form#q100" target="_blank">check this FAQ</a>.
        </tr>
     </table>
  </div>    
 </div> 
  
 

  <div id="metabox_basic_settings" class="postbox" >
  <h2 class='hndle' style="padding:5px;"><span>Validation Texts</span></h2>
  <div class="inside">
     <?php $option = dex_bccf_get_option('vs_use_validation', DEX_BCCF_DEFAULT_vs_use_validation); ?>
     <input type="hidden" name="vs_use_validation" value="<?php echo $option; ?>" />
     <table class="form-table">
        <tr valign="top">
         <td width="1%" nowrap><strong>"is required" text:</strong><br /><input type="text" name="vs_text_is_required" size="40" value="<?php echo esc_attr(dex_bccf_get_option('vs_text_is_required', DEX_BCCF_DEFAULT_vs_text_is_required)); ?>" /></td>
         <td><strong>"is email" text:</strong><br /><input type="text" name="vs_text_is_email" size="70" value="<?php echo esc_attr(dex_bccf_get_option('vs_text_is_email', DEX_BCCF_DEFAULT_vs_text_is_email)); ?>" /></td>
        </tr>
        <tr valign="top">
         <td><strong>"is valid captcha" text:</strong><br /><input type="text" name="cv_text_enter_valid_captcha" size="70" value="<?php echo esc_attr(dex_bccf_get_option('cv_text_enter_valid_captcha', DEX_BCCF_DEFAULT_dexcv_text_enter_valid_captcha)); ?>" /></td>
         <td><strong>"is valid date (mm/dd/yyyy)" text:</strong><br /><input type="text" name="vs_text_datemmddyyyy" size="70" value="<?php echo esc_attr(dex_bccf_get_option('vs_text_datemmddyyyy', DEX_BCCF_DEFAULT_vs_text_datemmddyyyy)); ?>" /></td>
        </tr>
        <tr valign="top">
         <td><strong>"is valid date (dd/mm/yyyy)" text:</strong><br /><input type="text" name="vs_text_dateddmmyyyy" size="70" value="<?php echo esc_attr(dex_bccf_get_option('vs_text_dateddmmyyyy', DEX_BCCF_DEFAULT_vs_text_dateddmmyyyy)); ?>" /></td>
         <td><strong>"is number" text:</strong><br /><input type="text" name="vs_text_number" size="70" value="<?php echo esc_attr(dex_bccf_get_option('vs_text_number', DEX_BCCF_DEFAULT_vs_text_number)); ?>" /></td>
        </tr>
        <tr valign="top">
         <td><strong>"only digits" text:</strong><br /><input type="text" name="vs_text_digits" size="70" value="<?php echo esc_attr(dex_bccf_get_option('vs_text_digits', DEX_BCCF_DEFAULT_vs_text_digits)); ?>" /></td>
         <td><strong>"under maximum" text:</strong><br /><input type="text" name="vs_text_max" size="70" value="<?php echo esc_attr(dex_bccf_get_option('vs_text_max', DEX_BCCF_DEFAULT_vs_text_max)); ?>" /></td>
        </tr>
        <tr valign="top">
         <td><strong>"over minimum" text:</strong><br /><input type="text" name="vs_text_min" size="70" value="<?php echo esc_attr(dex_bccf_get_option('vs_text_min', DEX_BCCF_DEFAULT_vs_text_min)); ?>" /></td>
        </tr>

     </table>
  </div>
 </div>

 <div id="metabox_basic_settings" class="postbox" >
  <h2 class='hndle' style="padding:5px;"><span>Price Configuration</span></h2>
  <div class="inside">

    <table class="form-table">

        <tr valign="top">
        <th scope="row"><strong>Currency</strong></th>
        <td><div style="float:left"><input type="text" name="currency" size="3" value="<?php echo esc_attr(dex_bccf_get_option('currency',DEX_BCCF_DEFAULT_CURRENCY)); ?>" /></div>
           <div id="currencyhelp" style="float:left"> &nbsp; [<a href="javascript:showcurrencies();">?</a>]</div>
           <div id="currencylist" style="display:none;float:left"> &nbsp; <strong>Ex:</strong> USD, EUR, GBP, CAD, AUD, NZD, CHF, MXN, CZK, DKK, NOK, SEK, HKD, SGD, HUF, ILS, JPY, PLN</div>
        </td>
        </tr>


        <tr valign="top">
        <th scope="row"><strong>Default request cost (per day)</strong></th>
        <td><input type="text" size="5" name="request_cost" value="<?php echo esc_attr($request_costs[0]); ?>" /></td>
        </tr>


        <tr valign="top">
        <th scope="row"><nobr><strong>Total request cost for specific # of days</strong></nobr><br />
          <nobr># of days to setup:
          <?php $option = @intval (dex_bccf_get_option('max_slots', '0')); if ($option=='') $option = 0;  ?>
          <select name="max_slots" onchange="dex_updatemaxslots();">
           <?php for ($k=0; $k<=30; $k++) { ?>
           <option value="<?php echo $k; ?>"<?php if ($option == $k) echo ' selected'; ?>><?php echo $k; ?></option>
           <?php } ?>
          </select></nobr>
        </th>
        <td>
           <div id="cpabcslots">Help: Select the number of days to setup if you want to use this configuration option.<br /><br /></div>
           <div style="clear:both"></div>
           <em style="font-size:11px;">Note: Each box should contain the  TOTAL price for a booking of that length. This will overwrite the default price if the booking length matches some of the specified booking lengths.</em>
        </td>
        </tr>

       <tr>
        <td valign="top" colspan="2">
         <strong>Supplement for bookings between</strong>
         <input type="text" size="5" name="calendar_suplementminnight" size="40" value="<?php $v = dex_bccf_get_option('calendar_suplementminnight', '0'); echo esc_attr(($v==''?'0':$v)); ?>" />
         <strong>and</strong>
         <input type="text" size="5" name="calendar_suplementmaxnight" size="40" value="<?php $v = dex_bccf_get_option('calendar_suplementmaxnight', '0'); echo esc_attr(($v==''?'0':$v)); ?>" />
         <strong>nights:</strong>
         <input type="text" size="5" name="calendar_suplement" size="40" value="<?php $v = dex_bccf_get_option('calendar_suplement', '0'); echo esc_attr(($v==''?'0':$v)); ?>" /><br />
         <em style="font-size:11px;">Suplement will be added once for bookings between those nights.</em>
        </td>
       </tr>
       

        <tr valign="top">
         <td colspan="4" style="padding:3px;background-color:#E2EFF8;color:#666666;font-weight:bold;text-align:left">
			<strong>Deposit Payment (optional)</strong>
         </td>
        </tr>        

       <tr>
        <td valign="top" colspan="2">
        
         <?php $v = dex_bccf_get_option('calendar_depositenable', '0'); if ($v=='') $v = '0'; ?>
         <strong>Enable deposit payment?:</strong>
         <select name="calendar_depositenable">
          <option value="0" <?php if ($v=='0') echo ' selected'; ?>>No</option>
          <option value="1" <?php if ($v=='1') echo ' selected'; ?>>Yes</option>
         </select>
         &nbsp;&nbsp;
         <strong>Deposit Amount:</strong>
         <input type="text" size="5" name="calendar_depositamount" size="40" value="<?php $v = dex_bccf_get_option('calendar_depositamount', '0'); echo esc_attr(($v==''?'0':$v)); ?>" />
         &nbsp;&nbsp;
         <?php $v = dex_bccf_get_option('calendar_deposittype', '0'); if ($v=='') $v = '0'; ?>
         <strong>Deposit type:</strong>
         <select name="calendar_deposittype">
          <option value="0" <?php if ($v=='0') echo ' selected'; ?>>Percent</option>
          <option value="1" <?php if ($v=='1') echo ' selected'; ?>>Fixed</option>
         </select>
         <br />
         <em style="font-size:11px;">If enabled, the customer will have to pay at PayPal only the deposit amount.</em>
         <br /> 
         
        </td>
       </tr>
       

        <tr valign="top">
         <td colspan="4" style="padding:3px;background-color:#E2EFF8;color:#666666;font-weight:bold;text-align:left">
			<strong>Seasons Configuration (optional)</strong>
         </td>
        </tr> 
        
        <tr valign="top">
        <td scope="row" colspan="2">
           <!--<strong>Season cost (per day):</strong>-->
           <div id="dex_noseasons_availmsg">Loading...</div>

           <br />
           <div style="background:#EEF5FB;border: 1px dotted #888888;padding:10px;">
             <strong>Add new season:</strong>
             <br />
             Default Cost for this season: <br /> <input type="text" name="dex_dc_price" id="dex_dc_price" value="" /> <br />
             <div id="cpabcslots_season"></div>
             From: <br /> <input type="text"  size="10" name="dex_dc_season_dfrom" id="dex_dc_season_dfrom" value="" />&nbsp; &nbsp; &nbsp; <br />
             To: <br /> <input type="text"  size="10" name="dex_dc_season_dto" id="dex_dc_season_dto" value="" />&nbsp; &nbsp; &nbsp;<br />
             <input type="button" name="dex_dc_subcseasons" id="dex_dc_subcseasons" value="Add Season" />
             <br />
             <em>Note: Season prices override the "Default request cost" specified above.</em>
           </div>  
        </td>
        </tr>
    </table>

  </div>
 </div>

 <div id="metabox_basic_settings" class="postbox" >
  <h2 class='hndle' style="padding:5px;"><span>Paypal Payment Configuration</span></h2>
  <div class="inside">

    <table class="form-table">
        <tr valign="top">
        <th scope="row">Enable Paypal Payments?</th>
        <td><select name="enable_paypal" onchange="bccp_update_pp_payment_selection();">
             <option value="0" <?php if (!dex_bccf_get_option('enable_paypal',DEX_BCCF_DEFAULT_ENABLE_PAYPAL)) echo 'selected'; ?> >No</option>
             <option value="1" <?php if (dex_bccf_get_option('enable_paypal',DEX_BCCF_DEFAULT_ENABLE_PAYPAL) == '1') echo 'selected'; ?> >Yes</option>
             <option value="2" <?php if (dex_bccf_get_option('enable_paypal',DEX_BCCF_DEFAULT_ENABLE_PAYPAL) == '2') echo 'selected'; ?> >Optional</option>
             <option value="3" <?php if (dex_bccf_get_option('enable_paypal',DEX_BCCF_DEFAULT_ENABLE_PAYPAL) == '3') echo 'selected'; ?> >Use BeanStream.com</option>
            </select> 
            <br /><em style="font-size:11px;">Note: If "Optional" is selected, a radiobutton will appear in the form to select if the payment will be made with PayPal or not.</em>
            <div id="bccf_paypal_options_beanstream" style="display:none;margin-top:10px;background:#EEF5FB;border: 1px dotted #888888;padding:10px;width:260px;">
              BeanStream Merchant ID:<br />
              <input type="text" name="enable_beanstream_id" size="40" style="width:250px;" value="<?php echo esc_attr(dex_bccf_get_option('enable_beanstream_id','')); ?>" />
            </div>
            
            <div id="bccf_paypal_options_label" style="display:none;margin-top:10px;background:#EEF5FB;border: 1px dotted #888888;padding:10px;width:260px;">
              Label for the "<strong>Pay with PayPal</strong>" option:<br />
              <input type="text" name="enable_paypal_option_yes" size="40" style="width:250px;" value="<?php echo esc_attr(dex_bccf_get_option('enable_paypal_option_yes',DEX_BCCF_DEFAULT_PAYPAL_OPTION_YES)); ?>" />
              <br />
              Label for the "<strong>Pay later</strong>" option:<br />
              <input type="text" name="enable_paypal_option_no" size="40" style="width:250px;"  value="<?php echo esc_attr(dex_bccf_get_option('enable_paypal_option_no',DEX_BCCF_DEFAULT_PAYPAL_OPTION_NO)); ?>" />
            </div>
         </td>
        </tr>

        <tr valign="top">
        <th scope="row">Paypal email</th>
        <td><input type="text" name="paypal_email" size="40" value="<?php echo esc_attr(dex_bccf_get_option('paypal_email',DEX_BCCF_DEFAULT_PAYPAL_EMAIL)); ?>" /></td>
        </tr>

        <tr valign="top">
        <th scope="row">Paypal product name</th>
        <td><input type="text" name="paypal_product_name" size="50" value="<?php echo esc_attr(dex_bccf_get_option('paypal_product_name',DEX_BCCF_DEFAULT_PRODUCT_NAME)); ?>" /></td>
        </tr>

        <tr valign="top">
        <th scope="row">URL to return after successful  payment</th>
        <td><input type="text" name="url_ok" size="70" value="<?php echo esc_attr(dex_bccf_get_option('url_ok',DEX_BCCF_DEFAULT_OK_URL)); ?>" />
          <br />
          <em>Note: This field is used as the "acknowledgment / thank you message" even if the Paypal feature isn't used.</em>
        </td>
        </tr>

        <tr valign="top">
        <th scope="row">URL to return after an incomplete or cancelled payment</th>
        <td><input type="text" name="url_cancel" size="70" value="<?php echo esc_attr(dex_bccf_get_option('url_cancel',DEX_BCCF_DEFAULT_CANCEL_URL)); ?>" /></td>
        </tr>


        <tr valign="top">
        <th scope="row">Paypal language</th>
        <td><input type="text" name="paypal_language" value="<?php echo esc_attr(dex_bccf_get_option('paypal_language',DEX_BCCF_DEFAULT_PAYPAL_LANGUAGE)); ?>" /></td>
        </tr>

        <tr valign="top">
        <th scope="row">Taxes (applied at Paypal)</th>
        <td><input type="text" name="request_taxes" value="<?php echo esc_attr(dex_bccf_get_option('request_taxes','0')); ?>" /></td>
        </tr>
        
        <tr valign="top">
        <th scope="row">Discount Codes</th>
        <td>
           <div id="dex_nocodes_availmsg">Loading...</div>

           <br />
           <strong>Add new discount code:</strong>
           <br />
           Code: <input type="text" name="dex_dc_code" id="dex_dc_code" value="" /> &nbsp; &nbsp; &nbsp;
           Discount %: <input type="text" size="3" name="dex_dc_discount" id="dex_dc_discount"  value="25" />         &nbsp; &nbsp; &nbsp;
           Valid until: <input type="text"  size="10" name="dex_dc_expires" id="dex_dc_expires" value="" />&nbsp; &nbsp; &nbsp;
           <input type="button" name="dex_dc_subccode" id="dex_dc_subccode" value="Add" />
           <br />
           <em>Note: Expiration date based in server time. Server time now is <?php echo date("Y-m-d H:i"); ?></em>
        </td>
        </tr>

     </table>

  </div>
 </div>

 <div id="metabox_basic_settings" class="postbox" >
  <h2 class='hndle' style="padding:5px;"><span>Cabin Rates & Event Pricing</span></h2>
  <div class="inside">
  
   <div id="bccf_optional_services_fields_link"> 
    <a href="javascript:bccf_toggle_osf()">Click to show cabin rates configuration</a>
    <script type="text/javascript">
     function bccf_toggle_osf() 
     {
        document.getElementById("bccf_optional_services_fields_link").style.display="none";
        document.getElementById("bccf_optional_services_fields").style.display="";
     } 
    </script>
   </div>
   <div id="bccf_optional_services_fields" style="display:none;"> 
   <?php for ($k=1;$k<=DEX_BCCF_DEFAULT_SERVICES_FIELDS; $k++) { ?>
    <fieldset>
     <legend style="font-size: 16px;"><strong>Field #<?php echo $k; ?></strong></legend>
     <table class="form-table">
        <tr valign="top" colspan="2">
        <th scope="row">
         <?php
           $flabel = dex_bccf_get_option('cp_cal_checkboxes_label'.$k, 'Service');
           if ($flabel == '') $flabel = 'Service';
         ?>
        Label: <input type="text" name="cp_cal_checkboxes_label<?php echo $k; ?>" value="<?php echo esc_attr($flabel); ?>" />
        </th>
        </tr>
        <tr valign="top">
        <td colspan="2">
          <strong>If enabled, apply as:</strong><br />
          <?php $option = dex_bccf_get_option('cp_cal_checkboxes_type'.$k, DEX_BCCF_DEFAULT_CP_CAL_CHECKBOXES_TYPE); ?>
          <select name="cp_cal_checkboxes_type<?php echo $k; ?>">
           <option value="0"<?php if ($option == '0') echo ' selected'; ?>>Additional fee. This will be added once to the default rate.</option>
           <option value="4"<?php if ($option == '4') echo ' selected'; ?>>Additional rate per day. The rate will be added to the default rate for each day.</option>
           <option value="1"<?php if ($option == '1') echo ' selected'; ?>>Rate per day. This will overwrite the default rate.</option>
           <option value="2"<?php if ($option == '2') echo ' selected'; ?>>Fixed rate. This will overwrite the default rates.</option>
          </select>
        </td>
        </tr>
     </table>
     <table class="form-table">
        <tr valign="top">
        <th scope="row" style="width:390px;" >Drop-down select, one "rate | title" per line<br />
        <textarea style="width:385px;" wrap="off" rows="4" name="cp_cal_checkboxes<?php echo $k; ?>"><?php echo dex_bccf_get_option('cp_cal_checkboxes'.$k, DEX_BCCF_DEFAULT_CP_CAL_CHECKBOXES); ?></textarea>
        </th>
        <td>
        <em>Appears only if an option is specified.</em>
        <br /><u><strong>Sample Format:</strong></u><br />
        <?php echo str_replace("\n", "<br />", DEX_BCCF_DEFAULT_EXPLAIN_CP_CAL_CHECKBOXES); ?></td>
        </tr>
     </table>
    </fieldset>
   <?php } ?>
   </div>
  </div>
 </div>

 <div id="metabox_basic_settings" class="postbox" >
  <h2 class='hndle' style="padding:5px;"><span>Notification Settings to Admin</span></h2>
  <div class="inside">
     <table class="form-table">
        <tr valign="top">
        <th scope="row">Notification "from" email</th>
        <td><input type="text" name="notification_from_email" size="40" value="<?php echo esc_attr(dex_bccf_get_option('notification_from_email', DEX_BCCF_DEFAULT_PAYPAL_EMAIL)); ?>" /></td>
        </tr>
        <tr valign="top">
        <th scope="row">Send notification to email</th>
        <td><input type="text" name="notification_destination_email" size="40" value="<?php echo esc_attr(dex_bccf_get_option('notification_destination_email', DEX_BCCF_DEFAULT_PAYPAL_EMAIL)); ?>" />
            <br />
          <em>Note: Comma separated list for adding more than one email address<em>
        </td>
        </tr>
        <tr valign="top">
        <th scope="row">Email subject notification to admin</th>
        <td><input type="text" name="email_subject_notification_to_admin" size="70" value="<?php echo esc_attr(dex_bccf_get_option('email_subject_notification_to_admin', DEX_BCCF_DEFAULT_SUBJECT_NOTIFICATION_EMAIL)); ?>" /></td>
        </tr>
        <tr valign="top">
        <th scope="row">Email format?</th>
        <td>
          <?php $option = dex_bccf_get_option('notification_emailformat', 'text'); ?>
          <select name="notification_emailformat">
           <option value="text"<?php if ($option != 'html') echo ' selected'; ?>>Plain Text (default)</option>
           <option value="html"<?php if ($option == 'html') echo ' selected'; ?>>HTML (use html in the textarea below)</option>
          </select>
        </td>
        </tr>         
        <tr valign="top">
        <th scope="row">Email notification to admin</th>
        <td><textarea cols="70" rows="5" name="email_notification_to_admin"><?php echo dex_bccf_get_option('email_notification_to_admin', DEX_BCCF_DEFAULT_NOTIFICATION_EMAIL); ?></textarea></td>
        </tr>
     </table>
  </div>
 </div>


 <div id="metabox_basic_settings" class="postbox" >
  <h2 class='hndle' style="padding:5px;"><span>Email Copy to User</span></h2>
  <div class="inside">
     <table class="form-table">
        <tr valign="top">
        <th scope="row">Email field on the form</th>
        <td><select id="cu_user_email_field" name="cu_user_email_field" def="<?php echo esc_attr(dex_bccf_get_option('cu_user_email_field', DEX_BCCF_DEFAULT_cu_user_email_field)); ?>"></select></td>
        </tr>
        <tr valign="top">
        <th scope="row">Email subject confirmation to user</th>
        <td><input type="text" name="email_subject_confirmation_to_user" size="70" value="<?php echo esc_attr(dex_bccf_get_option('email_subject_confirmation_to_user', DEX_BCCF_DEFAULT_SUBJECT_CONFIRMATION_EMAIL)); ?>" /></td>
        </tr>
        <tr valign="top">
        <th scope="row">Email format?</th>
        <td>
          <?php $option = dex_bccf_get_option('copyuser_emailformat', 'text'); ?>
          <select name="copyuser_emailformat">
           <option value="text"<?php if ($option != 'html') echo ' selected'; ?>>Plain Text (default)</option>
           <option value="html"<?php if ($option == 'html') echo ' selected'; ?>>HTML (use html in the textarea below)</option>
          </select>
        </td>
        </tr>         
        <tr valign="top">
        <th scope="row">Email confirmation to user</th>
        <td><textarea cols="70" rows="5" name="email_confirmation_to_user"><?php echo dex_bccf_get_option('email_confirmation_to_user', DEX_BCCF_DEFAULT_CONFIRMATION_EMAIL); ?></textarea></td>
        </tr>
     </table>
  </div>
 </div>


 
 <div id="metabox_basic_settings" class="postbox" >
  <h2 class='hndle' style="padding:5px;"><span>Reminder Settings</span></h2>
  <div class="inside">  
     <table class="form-table">    
        <tr valign="top">
        <th scope="row">Enable booking reminders?</th>
        <td><input type="checkbox" name="enable_reminder" id="enable_reminder" onclick="bccf_checkreminderstatus();" size="40" value="1" <?php if (dex_bccf_get_option('enable_reminder',0)) echo 'checked'; ?> /></td>
        </tr>  
     </table>          
     <table class="form-table" id="bccf_remindertable">            
        <tr><td colspan="2"><hr /></td></tr>
        <tr valign="top">
        <th scope="row">Send reminder:</th>
        <td><input type="text" name="reminder_hours" size="2" value="<?php echo esc_attr(dex_bccf_get_option('reminder_hours', 72)); ?>" /> hours <strong>BEFORE</strong> the booking
        <br /><em>Note: Hours date based in server time. Server time now is <?php echo date("Y-m-d H:i"); ?></em>
        </td>
        </tr>
        <tr valign="top">
        <th scope="row">Reminder email subject</th>
        <td><input type="text" name="reminder_subject" size="70" value="<?php echo esc_attr(dex_bccf_get_option('reminder_subject', 'Booking reminder...')); ?>" /></td>
        </tr>
        <tr valign="top">
        <th scope="row">Email format?</th>
        <td>
          <?php $option = dex_bccf_get_option('nremind_emailformat', 'text'); ?>
          <select name="nremind_emailformat">
           <option value="text"<?php if ($option != 'html') echo ' selected'; ?>>Plain Text (default)</option>
           <option value="html"<?php if ($option == 'html') echo ' selected'; ?>>HTML (use html in the textarea below)</option>
          </select>
        </td>
        </tr>          
        <tr valign="top">
        <th scope="row">Reminder email message</th>
        <td><textarea cols="70" rows="5" name="reminder_content"><?php echo dex_bccf_get_option('reminder_content', DEX_BCCF_DEFAULT_REMINDER_CONTENT); ?></textarea></td>
        </tr>                                                
     </table>     
     <table class="form-table" id="bccf_remindertable2">            
        <tr><td colspan="2"><hr /></td></tr>
        <tr valign="top">
        <th scope="row">Send reminder:</th>
        <td><input type="text" name="reminder_hours2" size="2" value="<?php echo esc_attr(dex_bccf_get_option('reminder_hours2', 72)); ?>" /> hours <strong>AFTER</strong> the booking
        <br /><em>Note: Hours date based in server time. Server time now is <?php echo date("Y-m-d H:i"); ?></em>
        </td>
        </tr>
        <tr valign="top">
        <th scope="row">Reminder email subject</th>
        <td><input type="text" name="reminder_subject2" size="70" value="<?php echo esc_attr(dex_bccf_get_option('reminder_subject2', 'Thank you for your booking...')); ?>" /></td>
        </tr>
        <tr valign="top">
        <th scope="row">Email format?</th>
        <td>
          <?php $option = dex_bccf_get_option('nremind_emailformat2', 'text'); ?>
          <select name="nremind_emailformat2">
           <option value="text"<?php if ($option != 'html') echo ' selected'; ?>>Plain Text (default)</option>
           <option value="html"<?php if ($option == 'html') echo ' selected'; ?>>HTML (use html in the textarea below)</option>
          </select>
        </td>
        </tr>          
        <tr valign="top">
        <th scope="row">Reminder email message</th>
        <td><textarea cols="70" rows="5" name="reminder_content2"><?php echo dex_bccf_get_option('reminder_content2', DEX_BCCF_DEFAULT_REMINDER_CONTENT_AFTER); ?></textarea></td>
        </tr>                                                
     </table>          
     <script type="text/javascript">
       function bccf_checkreminderstatus() {
            if (document.getElementById("enable_reminder").checked)
            {
                document.getElementById("bccf_remindertable").style.display = '';
                document.getElementById("bccf_remindertable2").style.display = '';
            }    
            else
            {
                document.getElementById("bccf_remindertable").style.display = 'none';    
                document.getElementById("bccf_remindertable2").style.display = 'none';    
            }    
       }
       bccf_checkreminderstatus();
     </script>
  </div>
</div>  


 <div id="metabox_basic_settings" class="postbox" >
  <h2 class='hndle' style="padding:5px;"><span>Captcha Verification</span></h2>
  <div class="inside">
     <table class="form-table">
        <tr valign="top">
        <th scope="row">Use Captcha Verification?</th>
        <td colspan="5">
          <?php $option = dex_bccf_get_option('dexcv_enable_captcha', TDE_BCCFDEFAULT_dexcv_enable_captcha); ?>
          <select name="dexcv_enable_captcha">
           <option value="true"<?php if ($option == 'true') echo ' selected'; ?>>Yes</option>
           <option value="false"<?php if ($option == 'false') echo ' selected'; ?>>No</option>
          </select>
        </td>
        </tr>

        <tr valign="top">
         <th scope="row">Width:</th>
         <td><input type="text" name="dexcv_width" size="10" value="<?php echo esc_attr(dex_bccf_get_option('dexcv_width', TDE_BCCFDEFAULT_dexcv_width)); ?>"  onblur="generateCaptcha();"  /></td>
         <th scope="row">Height:</th>
         <td><input type="text" name="dexcv_height" size="10" value="<?php echo esc_attr(dex_bccf_get_option('dexcv_height', TDE_BCCFDEFAULT_dexcv_height)); ?>" onblur="generateCaptcha();"  /></td>
         <th scope="row">Chars:</th>
         <td><input type="text" name="dexcv_chars" size="10" value="<?php echo esc_attr(dex_bccf_get_option('dexcv_chars', TDE_BCCFDEFAULT_dexcv_chars)); ?>" onblur="generateCaptcha();"  /></td>
        </tr>

        <tr valign="top">
         <th scope="row">Min font size:</th>
         <td><input type="text" name="dexcv_min_font_size" size="10" value="<?php echo esc_attr(dex_bccf_get_option('dexcv_min_font_size', TDE_BCCFDEFAULT_dexcv_min_font_size)); ?>" onblur="generateCaptcha();"  /></td>
         <th scope="row">Max font size:</th>
         <td><input type="text" name="dexcv_max_font_size" size="10" value="<?php echo esc_attr(dex_bccf_get_option('dexcv_max_font_size', TDE_BCCFDEFAULT_dexcv_max_font_size)); ?>" onblur="generateCaptcha();"  /></td>
         <td colspan="2" rowspan="">
           Preview:<br />
             <br />
            <img src="<?php echo plugins_url('/captcha/captcha.php', __FILE__); ?>"  id="captchaimg" alt="security code" border="0"  />
         </td>
        </tr>


        <tr valign="top">
         <th scope="row">Noise:</th>
         <td><input type="text" name="dexcv_noise" size="10" value="<?php echo esc_attr(dex_bccf_get_option('dexcv_noise', TDE_BCCFDEFAULT_dexcv_noise)); ?>" onblur="generateCaptcha();" /></td>
         <th scope="row">Noise Length:</th>
         <td><input type="text" name="dexcv_noise_length" size="10" value="<?php echo esc_attr(dex_bccf_get_option('dexcv_noise_length', TDE_BCCFDEFAULT_dexcv_noise_length)); ?>" onblur="generateCaptcha();" /></td>
        </tr>


        <tr valign="top">
         <th scope="row">Background:</th>
         <td><input type="text" name="dexcv_background" size="10" value="<?php echo esc_attr(dex_bccf_get_option('dexcv_background', TDE_BCCFDEFAULT_dexcv_background)); ?>" onblur="generateCaptcha();" /></td>
         <th scope="row">Border:</th>
         <td><input type="text" name="dexcv_border" size="10" value="<?php echo esc_attr(dex_bccf_get_option('dexcv_border', TDE_BCCFDEFAULT_dexcv_border)); ?>" onblur="generateCaptcha();" /></td>
        </tr>

        <tr valign="top">
         <th scope="row">Font:</th>
         <td>
            <select name="dexcv_font" onchange="generateCaptcha();" >
              <option value="font-1.ttf"<?php if ("font-1.ttf" == dex_bccf_get_option('dexcv_font', TDE_BCCFDEFAULT_dexcv_font)) echo " selected"; ?>>Font 1</option>
              <option value="font-2.ttf"<?php if ("font-2.ttf" == dex_bccf_get_option('dexcv_font', TDE_BCCFDEFAULT_dexcv_font)) echo " selected"; ?>>Font 2</option>
              <option value="font-3.ttf"<?php if ("font-3.ttf" == dex_bccf_get_option('dexcv_font', TDE_BCCFDEFAULT_dexcv_font)) echo " selected"; ?>>Font 3</option>
              <option value="font-4.ttf"<?php if ("font-4.ttf" == dex_bccf_get_option('dexcv_font', TDE_BCCFDEFAULT_dexcv_font)) echo " selected"; ?>>Font 4</option>
            </select>
         </td>
        </tr>


     </table>
  </div>
 </div>
 
 <?php
	global $dexbccf_addons_objs_list, $dexbccf_addons_active_list;
	if( count( $dexbccf_addons_active_list ) )
	{	
		_e( '<h2>Add-Ons Settings:</h2><hr />', 'bccf' );
		foreach( $dexbccf_addons_active_list as $addon_id ) if( isset( $dexbccf_addons_objs_list[ $addon_id ] ) ) print $dexbccf_addons_objs_list[ $addon_id ]->get_addon_form_settings( CP_CONTACTFORMPP_ID );
	}
 ?>
   

  <div id="metabox_basic_settings" class="postbox" >
    <h2 class='hndle' style="padding:5px;"><span>Note</span></h2>
    <div class="inside">
     To insert this form in a post/page, use the dedicated icon
     <?php print '<img hspace="5" src="'.plugins_url('/images/dex_apps.gif', __FILE__).'" alt="'.__('Insert Booking Calendar','bccf').'" />';     ?>
     which has been added to your Upload/Insert Menu, just below the title of your Post/Page.
     <br /><br />
    </div>
  </div>

</div>


<p class="submit"><input type="submit" name="submit" id="submit" class="button-primary" value="Save Changes"  /></p>

[<a href="http://wordpress.dwbooster.com/contact-us" target="_blank">Request Custom Modifications</a>] | [<a href="http://wordpress.dwbooster.com/calendars/booking-calendar-contact-form" target="_blank">Help</a>]
</form>
</div>
<script type="text/javascript">
 function generateCaptcha()
 {
    var d=new Date();
    var f = document.dexconfigofrm;
    var qs = "?width="+f.dexcv_width.value;
    qs += "&height="+f.dexcv_height.value;
    qs += "&letter_count="+f.dexcv_chars.value;
    qs += "&min_size="+f.dexcv_min_font_size.value;
    qs += "&max_size="+f.dexcv_max_font_size.value;
    qs += "&noise="+f.dexcv_noise.value;
    qs += "&noiselength="+f.dexcv_noise_length.value;
    qs += "&bcolor="+f.dexcv_background.value;
    qs += "&border="+f.dexcv_border.value;
    qs += "&font="+f.dexcv_font.options[f.dexcv_font.selectedIndex].value;
    qs += "&rand="+d;
    document.getElementById("captchaimg").src= "<?php echo plugins_url('/captcha/captcha.php', __FILE__); ?>"+qs+"&inAdmin=1";
 }
 generateCaptcha();
 var $j = jQuery.noConflict();
 $j(function() {
 	$j("#dex_dc_expires").datepicker({
                    dateFormat: 'yy-mm-dd'
                 });
    //$j("#calendar_language").val("<?php echo $calendar_language;?>");

 });
 $j('#dex_nocodes_availmsg').load('<?php echo cp_bccf_get_site_url(true); ?>/?dex_bccf=loadcoupons&inAdmin=1&dex_item=<?php echo CP_BCCF_CALENDAR_ID; ?>');
 $j('#dex_dc_subccode').click (function() {
                               var code = $j('#dex_dc_code').val();
                               var discount = $j('#dex_dc_discount').val();
                               var expires = $j('#dex_dc_expires').val();
                               if (code == '') { alert('Please enter a code'); return; }
                               if (parseFloat(discount)+"" != discount) { alert('Please numeric discount percent'); return; }
                               if (expires == '') { alert('Please enter an expiration date for the code'); return; }
                               var params = '&add=1&expires='+encodeURI(expires)+'&discount='+encodeURI(discount)+'&code='+encodeURI(code);
                               $j('#dex_nocodes_availmsg').load('<?php echo cp_bccf_get_site_url(true); ?>/?dex_bccf=loadcoupons&inAdmin=1&dex_item=<?php echo CP_BCCF_CALENDAR_ID; ?>'+params);
                               $j('#dex_dc_code').val();
                             });
  function dex_delete_coupon(id)
  {
     $j('#dex_nocodes_availmsg').load('<?php echo cp_bccf_get_site_url(true); ?>/?dex_bccf=loadcoupons&inAdmin=1&dex_item=<?php echo CP_BCCF_CALENDAR_ID; ?>&delete=1&code='+id);
  }
  $j(function() {
 	$j("#dex_dc_season_dfrom").datepicker({
                    dateFormat: 'yy-mm-dd'
                 });
    $j("#dex_dc_season_dto").datepicker({
                    dateFormat: 'yy-mm-dd'
                 });
  });
  $j('#dex_noseasons_availmsg').load('<?php echo cp_bccf_get_site_url(true); ?>/?dex_bccf=loadseasonprices&inAdmin=1&dex_item=<?php echo CP_BCCF_CALENDAR_ID; ?>');
  $j('#dex_dc_subcseasons').click (function() {
                               var code = $j('#dex_dc_price').val();
                               var dfrom = $j('#dex_dc_season_dfrom').val();
                               var dto = $j('#dex_dc_season_dto').val();
                               if (parseFloat(code)+"" != code && parseFloat(code)+"0" != code && parseFloat(code)+"00" != code) { alert('Please enter a price (valid number).'); return; }
                               var f = document.dexconfigofrm;
                               var slots = f.max_slots.options[f.max_slots.selectedIndex].value;
                               for(var i=1; i<=slots; i++)
                                   code += ";"+ $j('#request_cost_season'+i).val();
                               if (dfrom == '') { alert('Please enter an expiration date for the code'); return; }
                               if (dto == '') { alert('Please enter an expiration date for the code'); return; }
                               var params = '&add=1&dto='+encodeURI(dto)+'&dfrom='+encodeURI(dfrom)+'&price='+encodeURI(code);
                               $j('#dex_noseasons_availmsg').load('<?php echo cp_bccf_get_site_url(true); ?>/?dex_bccf=loadseasonprices&inAdmin=1&dex_item=<?php echo CP_BCCF_CALENDAR_ID; ?>'+params);
                               $j('#dex_dc_price').val();
                             });
  function dex_delete_season_price(id)
  {
     $j('#dex_noseasons_availmsg').load('<?php echo cp_bccf_get_site_url(true); ?>/?dex_bccf=loadseasonprices&inAdmin=1&dex_item=<?php echo CP_BCCF_CALENDAR_ID; ?>&delete=1&code='+id);
  }

  function showcurrencies()
  {
      document.getElementById("currencyhelp").style.display = "none";
      document.getElementById("currencylist").style.display = "";
  }
  function dex_updatemaxslots()
  {
      try
      {
          var default_request_cost = new Array(<?php echo $request_costs_exploded; ?>);
          var f = document.dexconfigofrm;
          var slots = f.max_slots.options[f.max_slots.selectedIndex].value;
          var buffer = "";
          var buffer2 = "";
          for(var i=1; i<=slots; i++)
          {
              buffer += '<div id="cpabccost'+i+'" style="float:left;width:70px;font-size:10px;">'+i+' day'+(i>1?'s':'')+':<br />'+
                         '<input type="text" name="request_cost_'+i+'" style="width:40px;" value="'+default_request_cost[i]+'" /></div>';
              buffer2 += '<div id="cpabccost_season'+i+'" style="float:left;width:70px;font-size:10px;">'+i+' day'+(i>1?'s':'')+':<br />'+
                         '<input type="text" name="request_cost_season'+i+'" id="request_cost_season'+i+'" style="width:40px;" value="" /></div>';           
          }               
          if (slots == '0')
              buffer = "<br />&lt;-<em> Select the number of days to setup if you want to use this configuration option.<br /></em>";
          else
              buffer2 = 'Total request cost for specific # of days:<br />'+buffer2+'<div style="clear:both"></div>';  
          document.getElementById("cpabcslots").innerHTML = buffer;
          document.getElementById("cpabcslots_season").innerHTML = buffer2;          
      }
      catch(e)
      {
      }
  }
  dex_updatemaxslots();
  
  function bccp_update_pp_payment_selection() 
  {
     var f = document.dexconfigofrm;
     var ppoption = f.enable_paypal.options[f.enable_paypal.selectedIndex].value;     
     if (ppoption == '2')
         document.getElementById("bccf_paypal_options_label").style.display = "";         
     else
         document.getElementById("bccf_paypal_options_label").style.display = "none";  
     if (ppoption == '3')    
         document.getElementById("bccf_paypal_options_beanstream").style.display = "";  
     else
         document.getElementById("bccf_paypal_options_beanstream").style.display = "none";      
         
  }   
  
  bccp_update_pp_payment_selection();
  
  function shcalarea() 
  {
    var cal1 = document.getElementById("metabox_basic_settings_cal1");
    var cal2 = document.getElementById("metabox_basic_settings_cal2");
    var sel = document.getElementById("masteritem");
    if (sel.selectedIndex > 0)
    {
        cal1.style.display='none';
        cal2.style.display='';     
    }    
    else 
    {
        cal1.style.display='';     
        cal2.style.display='none';     
    }
  }
  
  shcalarea();
         
</script>


<?php } else { ?>
  <br />
  The current user logged in doesn't have enough permissions to edit this calendar. This user can edit only his/her own calendars. Please log in as administrator to get access to all calendars.
<?php } ?>
