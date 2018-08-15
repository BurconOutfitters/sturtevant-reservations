<?php

  // Start:: Language constants, translate below:
  // -----------------------------------------------

  $l_calendar        = __("Calendar",'bccf');
  $l_min_nights      = __("The minimum number of nights to book is",'bccf');
  $l_max_nights      = __("The maximum number of nights to book is",'bccf');
  $l_select_dates    = __("Select start and end dates",'bccf');
  $l_p_select        = __("Please select start and end dates",'bccf');
  $l_select_start    = __("Select Start Date",'bccf');
  $l_select_end      = __("Select End Date",'bccf');
  $l_cancel_c        = __("Cancel Selection",'bccf');
  $l_sucess          = __("Successfully",'bccf');
  $l_cost            = __("Cost",'bccf');
  $l_coupon          = __("Coupon code (optional)",'bccf');
  $l_service         = __("Service",'bccf');
  $l_sec_code        = __("Please enter the security code",'bccf');
  $l_sec_code_low    = __("Security Code (lowercase letters)",'bccf');  
  $l_payment_options = __("Payment options",'bccf');
  
  $l_continue        = __($button_label,'bccf');

  // End:: Language constants.
  // -----------------------------------------------

?>
<?php 

if ( !defined('DEX_AUTH_INCLUDE') ) { echo 'Direct access not allowed.'; exit; } 
  
  
$raw_form_str = str_replace("\r"," ",str_replace("\n"," ",dex_bccf_cleanJSON(dex_bccf_get_option('form_structure', DEX_BCCF_DEFAULT_form_structure))));

$form_data = json_decode( $raw_form_str );
if( is_null( $form_data ) ){
	$json = new JSON;
	$form_data = $json->unserialize( $raw_form_str );
}

if( !is_null( $form_data ) )	
{
	if( !empty( $form_data[ 0 ] ) )
	{
		foreach( $form_data[ 0 ] as $key => $object )
		{
			if( isset( $object->isDataSource ) && $object->isDataSource && function_exists( 'mcrypt_encrypt' ) )
			{
				$connection = new stdClass();
				$connection->connection = base64_encode( mcrypt_encrypt( MCRYPT_RIJNDAEL_256, dex_bccf_get_option('form_structure', DEX_BCCF_DEFAULT_form_structure,$id), serialize( $object->list->database->databaseData ), MCRYPT_MODE_ECB ) );
				$connection->form = $id;
				
				$object->list->database->databaseData = $connection;
				$form_data[ 0 ][ $key ] = $object;
				$raw_form_str = json_encode( $form_data );
			}
		}
	}
	
	if( isset( $form_data[ 1 ] ) && isset( $form_data[ 1 ][ 0 ] ) && isset( $form_data[ 1 ][ 0 ]->formtemplate ) )
	{
		$templatelist = dex_bccf_available_templates();
		if( isset( $templatelist[ $form_data[ 1 ][ 0 ]->formtemplate ] ) );
		print '<link href="'.esc_attr( esc_url( $templatelist[ $form_data[ 1 ][ 0 ]->formtemplate ][ 'file' ] ) ).'" type="text/css" rel="stylesheet" />';
	}	
}

$raw_form_str = str_replace('"','&quot;',esc_attr($raw_form_str));

?>
<link href="<?php echo plugins_url('css/stylepublic.css', __FILE__); ?>" type="text/css" rel="stylesheet" /><link href="<?php echo plugins_url('css/cupertino/jquery-ui-1.8.20.custom.css', __FILE__); ?>" type="text/css" rel="stylesheet" /><link href="<?php echo plugins_url('css/calendar.css', __FILE__); ?>" type="text/css" rel="stylesheet" />
<?php 
  $custom_styles = base64_decode(get_option('CP_BCCF_CSS', '')); 
  if ($custom_styles != '')
      echo '<style type="text/css">'.$custom_styles.'</style>';
  $custom_scripts = base64_decode(get_option('CP_BCCF_JS', '')); 
  if ($custom_scripts != '')
      echo '<script type="text/javascript">'.$custom_scripts.'</script>';  
?>
<form class="cpp_form" name="dex_bccf_pform" id="dex_bccf_pform" action="<?php get_site_url(); ?>" method="post" enctype="multipart/form-data" onsubmit="return doValidate(this);"><input name="dex_bccf_post" type="hidden" value="1" />
<?php if ($option_calendar_enabled != 'false') { ?>
<script>
var pathCalendar = "<?php echo cp_bccf_get_site_url(); ?>/";
var pathCalendar_full = pathCalendar + "wp-content/plugins/<?php echo basename(dirname(__FILE__));?>/css/images/corners";
</script>
<?php
    $option_overlapped = dex_bccf_get_option('calendar_overlapped', DEX_BCCF_DEFAULT_CALENDAR_OVERLAPPED);

    $calendar_dateformat = dex_bccf_get_option('calendar_dateformat',DEX_BCCF_DEFAULT_CALENDAR_DATEFORMAT);
    $dformat = ((dex_bccf_get_option('calendar_dateformat', DEX_BCCF_DEFAULT_CALENDAR_DATEFORMAT)==0)?"mm/dd/yy":"dd/mm/yy");
    $dformat_php = ((dex_bccf_get_option('calendar_dateformat', DEX_BCCF_DEFAULT_CALENDAR_DATEFORMAT)==0)?"m/d/Y":"d/m/Y");
    $calendar_mindate = "";
    $value = dex_bccf_get_option('calendar_mindate',DEX_BCCF_DEFAULT_CALENDAR_MINDATE);
    if ($value != '') $calendar_mindate = date($dformat_php, strtotime($value));
    $calendar_maxdate = "";
    $value = dex_bccf_get_option('calendar_maxdate',DEX_BCCF_DEFAULT_CALENDAR_MAXDATE);
    if ($value != '') $calendar_maxdate = date($dformat_php, strtotime($value));
    $cfmode = dex_bccf_get_option('calendar_holidaysdays', '1111111'); if (strlen($cfmode)!=7) $cfmode = '1111111';
    $workingdates = "[".$cfmode[0].",".$cfmode[1].",".$cfmode[2].",".$cfmode[3].",".$cfmode[4].",".$cfmode[5].",".$cfmode[6]."]";
    $cfmode = dex_bccf_get_option('calendar_startresdays', '1111111'); if (strlen($cfmode)!=7) $cfmode = '1111111';
    $startReservationWeekly = "[".$cfmode[0].",".$cfmode[1].",".$cfmode[2].",".$cfmode[3].",".$cfmode[4].",".$cfmode[5].",".$cfmode[6]."]";

    $h = dex_bccf_get_option('calendar_holidays','');
    $h = explode(";",$h);
    $holidayDates = array();
    for ($i=0;$i<count($h);$i++)
        if ($h[$i]!="")
            $holidayDates[]= '"'.$h[$i].'"';
    $holidayDates = "[".implode(",",$holidayDates)."]";

    $h = dex_bccf_get_option('calendar_startres','');
    $h = explode(";",$h);
    $startReservationDates = array();
    for ($i=0;$i<count($h);$i++)
        if ($h[$i]!="")
            $startReservationDates[]= '"'.$h[$i].'"';
    $startReservationDates = "[".implode(",",$startReservationDates)."]";




?>
<div <?php echo (count($myrows) < 2?'style="display:none"':''); ?>>
<?php
  echo $l_calendar.':<br /><select name="dex_item" id="dex_item" onchange="dex_updateItem()">';
  foreach ($myrows as $item)
      echo '<option value='.$item->id.'>'.__($item->uname,'bccf').'</option>';
  echo '</select>';
  foreach ($myrows as $item)
  {
    
     $value = $item->calendar_mindate; if ($value == '' && $item->calendar_language == '') $value = DEX_BCCF_DEFAULT_CALENDAR_MINDATE;
     if ($value != '') $value = date($dformat_php, strtotime($value));
     echo '<input type="hidden" name="bccf_calendar_mindate'.$item->id.'" id="bccf_calendar_mindate'.$item->id.'" value="'.$value.'" />';
     
     $value = $item->calendar_maxdate;
     if ($value != '') $value = date($dformat_php, strtotime($value));
     echo '<input type="hidden" name="bccf_calendar_maxdate'.$item->id.'" id="bccf_calendar_maxdate'.$item->id.'" value="'.$value.'" />';
     
     $cfmode = $item->calendar_holidaysdays; if (strlen($cfmode)!=7) $cfmode = '1111111';
     $workingdates = "[".$cfmode[0].",".$cfmode[1].",".$cfmode[2].",".$cfmode[3].",".$cfmode[4].",".$cfmode[5].",".$cfmode[6]."]";
     echo '<input type="hidden" name="bccf_workingdates'.$item->id.'" id="bccf_workingdates'.$item->id.'" value="'.esc_attr($workingdates).'" />';    
     
     $cfmode = $item->calendar_startresdays; if (strlen($cfmode)!=7) $cfmode = '1111111';
     $startReservationWeekly = "[".$cfmode[0].",".$cfmode[1].",".$cfmode[2].",".$cfmode[3].",".$cfmode[4].",".$cfmode[5].",".$cfmode[6]."]";
     echo '<input type="hidden" name="bccf_startReservationWeekly'.$item->id.'" id="bccf_startReservationWeekly'.$item->id.'" value="'.esc_attr($startReservationWeekly).'" />';    
    
     $h = $item->calendar_holidays;
     $h = explode(";",$h);
     $holidayDates = array();
     for ($i=0;$i<count($h);$i++)
         if ($h[$i]!="")
             $holidayDates[]= '"'.$h[$i].'"';
     $holidayDates = "[".implode(",",$holidayDates)."]";
     echo '<input type="hidden" name="bccf_holidayDates'.$item->id.'" id="bccf_holidayDates'.$item->id.'" value="'.esc_attr($holidayDates).'" />';    
    
     $fixrd = ($item->calendar_fixedmode=='1'?'true':'false');
     $fixrd_l = $item->calendar_fixedreslength; if ($fixrd_l=='') $fixrd_l = '1';     
     echo '<input type="hidden" name="bccf_fixedReservationDates'.$item->id.'" id="bccf_fixedReservationDates'.$item->id.'" value="'.esc_attr($fixrd).'" />';    
     echo '<input type="hidden" name="bccf_fixedReservationDates_length'.$item->id.'" id="bccf_fixedReservationDates_length'.$item->id.'" value="'.esc_attr($fixrd_l).'" />';    
        
     $h = $item->calendar_startres;
     $h = explode(";",$h);
     $startReservationDates = array();
     for ($i=0;$i<count($h);$i++)
         if ($h[$i]!="")
             $startReservationDates[]= '"'.$h[$i].'"';
     $startReservationDates = "[".implode(",",$startReservationDates)."]";
     echo '<input type="hidden" name="bccf_startReservationDates'.$item->id.'" id="bccf_startReservationDates'.$item->id.'" value="'.esc_attr($startReservationDates).'" />';    
          
     $minn = $item->calendar_minnights; if ($minn == '') $minn = '0';
     echo '<input type="hidden" name="bccf_minn'.$item->id.'" id="bccf_minn'.$item->id.'" value="'.$minn.'" />';    
     $maxn = $item->calendar_maxnights; if ($maxn == '0' || $maxn == '') $maxn = '365';
     echo '<input type="hidden" name="bccf_maxn'.$item->id.'" id="bccf_maxn'.$item->id.'" value="'.$maxn.'" />';
  }
?>
<br /><br />
</div>
<?php
  echo $l_select_dates.":";
?>
<?php
  foreach ($myrows as $item)
      echo '<div id="calarea'.$item->id.'" style="display:none" class="rcalendar"></div>';
?>
<div id="bccf_display_price" <?php if (dex_bccf_get_option('calendar_showcost','1') == '0') echo 'style="display:none"'; ?>>
Price:
</div>
<?php } else { ?><input name="dex_item" id="dex_item" type="hidden" value="<?php echo $myrows[0]->id; ?>" /><?php } ?>
<div id="selddiv" style="font-weight: bold;margin-top:0px;padding-top:0px;padding-right:3px;padding-left:3px;"></div>
<script type="text/javascript"><?php if ($option_calendar_enabled != 'false') { ?>
 var dex_current_calendar_item;
 function dex_updateItem()
 {
    document.getElementById("calarea"+dex_current_calendar_item).style.display = "none";
    var i = document.dex_bccf_pform.dex_item.options.selectedIndex;
    var selecteditem = document.dex_bccf_pform.dex_item.options[i].value;
    dex_do_init(selecteditem);
 }
 function dex_do_init(id)
 {
myjQuery = (typeof myjQuery != 'undefined' ) ? myjQuery : jQuery;
  try{$testjq = myjQuery} catch (e) {}
  if (typeof $testjq == 'undefined')
  {
    setTimeout("dex_do_init("+id+");");
    return;
  }    
  myjQuery(function(){
    (function($) {
        dex_current_calendar_item = id;
        document.getElementById("calarea"+dex_current_calendar_item).style.display = "";
        /** initCalendar(id,'<?php echo dex_bccf_get_option('calendar_language', DEX_BCCF_DEFAULT_CALENDAR_LANGUAGE); ?>',false,,'<?php echo $l_select_start; ?>','<?php echo $l_select_end; ?>','<?php echo $l_cancel_c; ?>','<?php echo $l_sucess; ?>'); */
        $calendarjQuery = myjQuery;
        $calendarjQuery("#servcontbccf").load('<?php echo cp_bccf_get_site_url(); ?>/?dex_bccf=getservices&dex_item='+dex_current_calendar_item);
        $calendarjQuery(function() {
        $calendarjQuery("#calarea"+id).rcalendar({"calendarId":id,
                                                    "partialDate":<?php echo dex_bccf_get_option('calendar_mode',DEX_BCCF_DEFAULT_CALENDAR_MODE); ?>,
                                                    "edition":false,
                                                    "minDate":$calendarjQuery("#bccf_calendar_mindate"+id).val() /**"<?php echo $calendar_mindate;?>"*/,
                                                    "maxDate":$calendarjQuery("#bccf_calendar_maxdate"+id).val() /**"<?php echo $calendar_maxdate;?>"*/,
                                                    "dformat":"<?php echo $dformat;?>",
                                                    "workingDates":JSON.parse($calendarjQuery("#bccf_workingdates"+id).val())<?php /** echo $workingdates; */?>,
	    			                                "holidayDates":JSON.parse($calendarjQuery("#bccf_holidayDates"+id).val())<?php /** echo $holidayDates; */?>,
	    			                                "startReservationWeekly":JSON.parse($calendarjQuery("#bccf_startReservationWeekly"+id).val())<?php /** echo $startReservationWeekly; */ ?>,
	    			                                "startReservationDates":JSON.parse($calendarjQuery("#bccf_startReservationDates"+id).val())<?php /** echo $startReservationDates; */ ?>,
	    			                                "fixedReservationDates": ($calendarjQuery("#bccf_fixedReservationDates"+id).val()=='true'?true:false)/**<?php echo ((dex_bccf_get_option('calendar_fixedmode', '')=='1'?'true':'false'));?>*/,
	    			                                "fixedReservationDates_length":parseInt($calendarjQuery("#bccf_fixedReservationDates_length"+id).val())/**<?php $v=dex_bccf_get_option('calendar_fixedreslength','1'); if ($v=='') echo '1'; else echo $v; ?>*/,
                                                    "language":"<?php echo $calendar_language?>",
                                                    "firstDay":<?php echo dex_bccf_get_option('calendar_weekday', DEX_BCCF_DEFAULT_CALENDAR_WEEKDAY); ?>,
                                                    "numberOfMonths":<?php echo ($pages!=''?$pages:dex_bccf_get_option('calendar_pages',3)); ?>
                                                    });
        });
        document.getElementById("selddiv").innerHTML = "";
    })(myjQuery);
    });
 }
 dex_do_init(<?php echo $myrows[0]->id; ?>);
 var bccf_d1 = "";
 var bccf_d2 = "";
 var bccf_ser = "";
 function updatedate()
 {
    try
    {
        var a = (document.getElementById("selDay_startcalarea"+dex_current_calendar_item ).value != '');
        var b = (document.getElementById("selDay_endcalarea"+dex_current_calendar_item ).value != '');
        var c = false;
        if (a)
          if (b)
            c = true;
            
        if (c)
        {
            var d1 = document.getElementById("selYear_startcalarea"+dex_current_calendar_item ).value+"-"+document.getElementById("selMonth_startcalarea"+dex_current_calendar_item ).value+"-"+document.getElementById("selDay_startcalarea"+dex_current_calendar_item ).value;
            var d2 = document.getElementById("selYear_endcalarea"+dex_current_calendar_item ).value+"-"+document.getElementById("selMonth_endcalarea"+dex_current_calendar_item ).value+"-"+document.getElementById("selDay_endcalarea"+dex_current_calendar_item ).value;
            $dexQuery = (typeof myjQuery != 'undefined' ) ? myjQuery : jQuery;                       
            var ser = "";<?php 
                             for ($k=1;$k<=DEX_BCCF_DEFAULT_SERVICES_FIELDS; $k++) 
                                 if ($dex_buffer[$k] != '') 
                                 {                                     
                                     if ( dex_bccf_get_option('cp_cal_checkboxes_ftype'.$k, 0) == '1')
                                     {   ?>var myckval = '';$dexQuery("input[id=dex_services<?php echo $k; ?>]:checked").each(function() {myckval += "|||"+this.value;});  
                                        ser += String.fromCharCode(38)+"ser<?php echo $k; ?>="+myckval;<?php }
                                     else if ( dex_bccf_get_option('cp_cal_checkboxes_ftype'.$k, 0) == '2')
                                     {   ?>ser += String.fromCharCode(38)+"ser<?php echo $k; ?>="+$dexQuery("input[name=services<?php echo $k; ?>]:checked").val();<?php }
                                     else
                                     {   ?>ser += String.fromCharCode(38)+"ser<?php echo $k; ?>="+$dexQuery("#dex_services<?php echo $k; ?>").val();<?php }
                                 } 
                         ?>
            if (bccf_d1 != d1 || bccf_d2 != d2 || bccf_ser != ser)
            {
                bccf_d1 = d1;
                bccf_d2 = d2;
                bccf_ser = ser;
                $dexQuery.ajax({
                  type: "GET",
                  url: "<?php echo cp_bccf_get_site_url(); ?>/?dex_bccf=getcost"+String.fromCharCode(38)+"inAdmin=1"+String.fromCharCode(38)+"dex_item="+dex_current_calendar_item+""+String.fromCharCode(38)+"from="+d1+""+String.fromCharCode(38)+"to="+d2+""+ser
                }).done(function( html ) {
                    document.getElementById("bccf_display_price").innerHTML = '';
                    $dexQuery("#bccf_display_price").append('<b><?php echo $l_cost; ?>:</b> <?php echo dex_bccf_get_option('currency', DEX_BCCF_DEFAULT_CURRENCY); ?> '+html);
                });
            }
        }
        else
        {
            bccf_d1 = "";
            bccf_d2 = "";
            document.getElementById("bccf_display_price").innerHTML = '';
        }
    } catch (e) {}
 }
 setInterval('updatedate()',200);<?php } else { /**  if ($option_calendar_enabled != 'false')  */ ?>
 function updatedate() {
 }
<?php } /**  if ($option_calendar_enabled != 'false')  */  ?>
 var cp_bccf_ready_to_go = false;
 function doValidate(form)
 {
    if (cp_bccf_ready_to_go) return;
    $dexQuery = (typeof myjQuery != 'undefined' ) ? myjQuery : jQuery;<?php if ($option_calendar_enabled != 'false') { ?>
    var d1 = new Date(document.getElementById("selYear_startcalarea"+dex_current_calendar_item ).value,document.getElementById("selMonth_startcalarea"+dex_current_calendar_item ).value-1,document.getElementById("selDay_startcalarea"+dex_current_calendar_item ).value);
    var d2 = new Date(document.getElementById("selYear_endcalarea"+dex_current_calendar_item ).value,document.getElementById("selMonth_endcalarea"+dex_current_calendar_item ).value-1,document.getElementById("selDay_endcalarea"+dex_current_calendar_item ).value);
    var ONE_DAY = 1000 * 60 * 60 * 24;
    var nights = Math.round(Math.abs(d2.getTime() - d1.getTime()) / ONE_DAY)<?php if (dex_bccf_get_option('calendar_mode',DEX_BCCF_DEFAULT_CALENDAR_MODE) == 'false') echo '+1'; ?>;<?php
    $minn = dex_bccf_get_option('calendar_minnights', '0'); if ($minn == '') $minn = '0';
    $maxn = dex_bccf_get_option('calendar_maxnights', '0'); if ($maxn == '0' || $maxn == '') $maxn = '365';
    ?>
    <?php } ?>
    document.dex_bccf_pform.dex_bccf_ref_page.value = document.location;<?php if ($option_calendar_enabled != 'false') { ?>
    if (document.getElementById("selDay_startcalarea"+dex_current_calendar_item).value == '' || document.getElementById("selDay_endcalarea"+dex_current_calendar_item).value == '')
    {
        alert('<?php echo str_replace("'","\'",$l_p_select); ?>.');
        return false;
    }
    if (nights < parseInt($dexQuery("#bccf_minn"+dex_current_calendar_item).val()) /**<?php echo $minn; ?>*/){
        alert('<?php echo $l_min_nights.' '; ?>'+$dexQuery("#bccf_minn"+dex_current_calendar_item).val());
        return false;
    }
    if (nights > parseInt($dexQuery("#bccf_maxn"+dex_current_calendar_item).val()) /**<?php echo $maxn; ?>*/){
        alert('<?php echo $l_max_nights.' '; ?>'+$dexQuery("#bccf_maxn"+dex_current_calendar_item).val());
        return false;
    }<?php } ?>
    <?php if (dex_bccf_get_option('dexcv_enable_captcha', TDE_BCCFDEFAULT_dexcv_enable_captcha) != 'false') { ?> if ($dexQuery("#hdcaptcha_dex_bccf_post").val() == '')
    {
        setTimeout( "cpbccf_cerror()", 100);
        return false;
    }
    var result = $dexQuery.ajax({
        type: "GET",
        url: "<?php echo cp_bccf_get_site_url(); ?>?inAdmin=1"+String.fromCharCode(38)+"hdcaptcha_dex_bccf_post="+$dexQuery("#hdcaptcha_dex_bccf_post").val(),
        async: false
    }).responseText;
    if (result.indexOf("captchafailed") != -1)
    {
        $dexQuery("#dex_bccf_captchaimg").attr('src', $dexQuery("#dex_bccf_captchaimg").attr('src')+'&'+Date());
        setTimeout( "cpbccf_cerror()", 100);
        return false;
    }
    else <?php } ?>
    {
        var cpefb_error = $dexQuery("#dex_bccf_pform").find(".cpefb_error:visible").length;
			  if (cpefb_error==0)
			  {<?php 
				/**
				 * Action called before insert the data into database. 
				 * To the function are passed two parameters: the array with submitted data, and the number of form in the page.
				 */							
				do_action( 'dexbccf_script_after_validation', '', CP_BCCF_CALENDAR_ID ); 
				?>
                cp_bccf_ready_to_go = true;
                cpbccf_blink(".pbSubmit");                  
			  	$dexQuery("#dex_bccf_pform").find("select").children().each(function(){
			  			if ($dexQuery(this).attr("vt")) $dexQuery(this).val($dexQuery(this).attr("vt"));
			  	});			  	
			  	$dexQuery("#dex_bccf_pform").find("input:checkbox,input:radio").each(function(){	            
			  			$dexQuery(this).val($dexQuery(this).attr("vt"));
			  	});
			  	$dexQuery("#dex_bccf_pform").find( '.ignore' ).closest( '.fields' ).remove();
			  	$dexQuery("#form_structure").remove();
			  	return true;
			  }
			  return false;
    }
 }
function cpbccf_cerror(){$dexQuery = jQuery.noConflict();$dexQuery("#hdcaptcha_error").css('top',$dexQuery("#hdcaptcha_dex_bccf_post").outerHeight());$dexQuery("#hdcaptcha_error").css("display","inline");} 
function cpbccf_blink(selector){
        $dexQuery = jQuery.noConflict();
        $dexQuery(selector).fadeOut(1000, function(){
            $dexQuery(this).fadeIn(1000, function(){
                try {
                    if (cp_bccf_ready_to_go)
                        cpbccf_blink(this); 
                } catch (e) {}  
            });
        });    
}
</script><input type="hidden" name="dex_bccf_pform_process" value="1" /><input type="hidden" name="dex_bccf_id" value="<?php echo CP_BCCF_CALENDAR_ID; ?>" /><input type="hidden" name="dex_bccf_ref_page" value="<?php esc_attr(cp_bccf_get_FULL_site_url); ?>" /><input type="hidden" name="form_structure" id="form_structure" size="180" value="<?php echo $raw_form_str; ?>" /><input type="hidden" name="form_structure_hidden" id="form_structure_hidden"  value="" />
<?php 
   if (DEX_BCCF_DEFAULT_SERVICES_FIELDS_ON_TOP)
   {
       echo '<div id="servcontbccf">';
       dex_bccf_echo_services($dex_buffer);
       echo '</div>';
   }
?>
  <div id="fbuilder">
      <div id="formheader"></div>
      <div id="fieldlist"></div>
  </div>
<div id="cpcaptchalayer">
<?php
     $codes = $wpdb->get_results( 'SELECT * FROM '.DEX_BCCF_DISCOUNT_CODES_TABLE_NAME.' WHERE `cal_id`='.CP_BCCF_CALENDAR_ID);
     if (count($codes))
     {
?>
      <div class="fields" id="field-c0">
         <label><?php echo $l_coupon; ?>:</label>
         <div class="dfield"><input type="text" name="couponcode" value=""></div>
         <div class="clearer"></div>
      </div>
<?php } ?>
<?php
   if (!DEX_BCCF_DEFAULT_SERVICES_FIELDS_ON_TOP)
   {
       echo '<div id="servcontbccf">';
       dex_bccf_echo_services($dex_buffer);
       echo '</div>';
   }
   if (dex_bccf_get_option('enable_paypal',DEX_BCCF_DEFAULT_ENABLE_PAYPAL) == '2')
   {
?>   <div class="fields" id="field-c0">
         <label><?php echo $l_payment_options; ?></label>
         <div class="dfield">
           <input type="radio" name="bccf_payment_option_paypal" value="1" vt="1" checked /> <?php echo __(dex_bccf_get_option('enable_paypal_option_yes',DEX_BCCF_DEFAULT_PAYPAL_OPTION_YES),'bccf'); ?><br />
           <input type="radio" name="bccf_payment_option_paypal" value="0" vt="0" /> <?php echo  __(dex_bccf_get_option('enable_paypal_option_no',DEX_BCCF_DEFAULT_PAYPAL_OPTION_NO),'bccf'); ?>
         </div>
         <div class="clearer"></div>
      </div>
<?php
   }
   if (dex_bccf_get_option('dexcv_enable_captcha', TDE_BCCFDEFAULT_dexcv_enable_captcha) != 'false')
   {
?>
  <div class="fields" id="field-ck2"><label></label><div class="dfield"><?php echo $l_sec_code; ?>:<br /><img src="<?php echo plugins_url('/captcha/captcha.php?width='.dex_bccf_get_option('dexcv_width', TDE_BCCFDEFAULT_dexcv_width).'&inAdmin=1&height='.dex_bccf_get_option('dexcv_height', TDE_BCCFDEFAULT_dexcv_height).'&letter_count='.dex_bccf_get_option('dexcv_chars', TDE_BCCFDEFAULT_dexcv_chars).'&min_size='.dex_bccf_get_option('dexcv_min_font_size', TDE_BCCFDEFAULT_dexcv_min_font_size).'&max_size='.dex_bccf_get_option('dexcv_max_font_size', TDE_BCCFDEFAULT_dexcv_max_font_size).'&noise='.dex_bccf_get_option('dexcv_noise', TDE_BCCFDEFAULT_dexcv_noise).'&noiselength='.dex_bccf_get_option('dexcv_noise_length', TDE_BCCFDEFAULT_dexcv_noise_length).'&bcolor='.dex_bccf_get_option('dexcv_background', TDE_BCCFDEFAULT_dexcv_background).'&border='.dex_bccf_get_option('dexcv_border', TDE_BCCFDEFAULT_dexcv_border).'&font='.dex_bccf_get_option('dexcv_font', TDE_BCCFDEFAULT_dexcv_font), __FILE__); ?>"  id="dex_bccf_captchaimg" alt="security code" border="0"  />
  <div class="clearer"></div></div></div>
  <div class="fields" id="field-c2"><label><?php echo $l_sec_code_low; ?>:</label>
   <div class="dfield">
    <input type="text" size="20" name="hdcaptcha_dex_bccf_post" id="hdcaptcha_dex_bccf_post" value="" />
    <div class="error message" id="hdcaptcha_error" generated="true" style="display:none;position: absolute; left: 0px; top: 25px;"><?php echo dex_bccf_get_option('cv_text_enter_valid_captcha', DEX_BCCF_DEFAULT_dexcv_text_enter_valid_captcha); ?></div>
    <div class="clearer"></div>
   </div>
  </div>
<?php } ?>
</div>
<div id="cp_subbtn" class="cp_subbtn"><?php echo $l_continue; ?></div>
</form>