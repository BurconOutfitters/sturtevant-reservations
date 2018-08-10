<?php

if ( !is_admin() )
{
    echo 'Direct access not allowed.';
    exit;
}

if (!defined('CP_BCCF_CALENDAR_ID'))
    define ('CP_BCCF_CALENDAR_ID',intval($_GET["cal"]));

global $wpdb;

$current_user = wp_get_current_user();

if (true) {   // (cp_bccf_is_administrator() || $mycalendarrows[0]->conwer == $current_user->ID) {
    
    $event = $wpdb->get_results( "SELECT * FROM ".TDE_BCCFCALENDAR_DATA_TABLE." WHERE id=".esc_sql($_GET["edit"]) );
    $event = $event[0];
       
    if ($event->reference != '')
    {
        $form_data = json_decode(dex_bccf_cleanJSON(dex_bccf_get_option('form_structure', DEX_BCCF_DEFAULT_form_structure))); 
        
        $org_booking = $wpdb->get_results( "SELECT buffered_date FROM ".DEX_BCCF_TABLE_NAME." WHERE id=".$event->reference );
        $params = unserialize($org_booking[0]->buffered_date);
        unset($params["startdate"]);
        unset($params["enddate"]);        
    }
    else
        $params["description"] = $event->description;
        
    if (count($_POST) > 0) 
    {
       $datatime_s = $_POST["datatime_s"];
       $datatime_e = $_POST["datatime_e"];       
       $dfoption = dex_bccf_get_option('calendar_dateformat', DEX_BCCF_DEFAULT_CALENDAR_DATEFORMAT);
       if ($dfoption == '0') 
          $format = "m/d/Y ".$format;
       else
          $format = "d/m/Y ".$format;
  
       
       // save quantity
       // save title
       // save buffered_date en original table
       // save description in destination table
       // track who editied the item
              
       if (dex_bccf_get_option('calendar_dateformat', DEX_BCCF_DEFAULT_CALENDAR_DATEFORMAT) == '0') $format_d = "m/d/Y "; else $format_d = "d/m/Y ";
    
       $params_new = array(
                   'startdate' => date($format_d,strtotime($_POST["datatime_s"])),
                   'enddate' => date($format_d,strtotime($_POST["datatime_e"]))
                 );
       foreach ($params as $item => $value)
           $params_new[$item] = $_POST[$item];
          
       $description = "Item: ".dex_bccf_get_option('uname','').'<br />Date From-To: '.date($format, strtotime($datatime_s)).'-'.date($format, strtotime($datatime_e)).'<br />';
       foreach ($params_new as $item => $value)
           if ($value != '' && $item != 'startdate' && $item != 'enddate'
                && $item != 'days'
                && $item != 'initialpayment'                  
                && $item != 'finalpayment'                  
                )
           {
               $name = dex_bccf_get_field_name($item,$form_data[0]);                
               $description .= $name.': '.$value.'<br />';
           }    
       
       if ($event->reference == '')  $description = $_POST["description"];
       
       $data1 = array(
                        'datatime_s' => $datatime_s,
                        'datatime_e' => $datatime_e,                        
                        'title' => $_POST["title"],
                        'description' => $description
                     );
       
       $data2 = array(
                        'booked_time_s' => $datatime_s,
                        'booked_time_e' => $datatime_e,
                        'booked_time_unformatted_s' => $_POST["datatime_s"],
                        'booked_time_unformatted_e' => $_POST["datatime_e"],
                        'buffered_date' => serialize($params_new)
                     );
       
       
       $wpdb->update ( TDE_BCCFCALENDAR_DATA_TABLE, $data1, array( 'id' => $_GET["edit"] ));
       if ($event->reference != '') $wpdb->update ( DEX_BCCF_TABLE_NAME, $data2, array( 'id' => $event->reference ));
       
       echo '<script type="text/javascript">  document.location = "admin.php?page=dex_bccf&cal='.$_GET["cal"].'&list=1&message=Item updated&r="+Math.random();</script>';
       exit;
    }    
    
    $date_s = date("Y-m-d", strtotime($event->datatime_s));
    $date_e = date("Y-m-d", strtotime($event->datatime_e));
    
?>

<div class="wrap">
<h1>Edit Booking</h1>

<form method="post" name="dexeditfrm" action=""> 

 <div id="metabox_basic_settings" class="postbox" >
  <h3 class='hndle' style="padding:5px;"><span>Booking Data</span></h3>
  <div class="inside">  
     <table class="form-table">    
        <tr valign="top">
        <th scope="row">Start Date</th>
        <td><input type="text" name="datatime_s" id="datatime_s" size="40" value="<?php echo $date_s; ?>" /></td>
        </tr>
        <tr valign="top">
        <th scope="row">End  Date</th>
        <td><input type="text" name="datatime_e" id="datatime_e" size="40" value="<?php echo $date_e; ?>" /></td>
        </tr>
        <tr valign="top">
        <th scope="row">Booking Title</th>
        <td><input type="text" name="title" size="40" value="<?php echo esc_attr($event->title); ?>" /></td>
        </tr>       
        <?php foreach ($params as $item => $value) { ?>
        <tr valign="top">
        <th scope="row"><?php             
                           $name = dex_bccf_get_field_name($item,$form_data[0]); 
                           echo $name;    
        ?></th>
        <td>
          <?php if (strpos($value,"\n") > 0 || strlen($value) > 80) { ?>
          <textarea cols="85" name="<?php echo $item; ?>"><?php echo ($value); ?></textarea>
          <?php } else { ?>
          <input type="text" name="<?php echo $item; ?>" value="<?php echo esc_attr($value); ?>" />
          <?php } ?>
        </td>
        </tr>
        <?php } ?>
     </table>         
  </div>
 </div>       



<p class="submit"><input type="submit" name="submit" id="submit" class="button-primary" value="Save"  />  &nbsp; <input type="button" value="Cancel" onclick="javascript:gobackapp();"></p>



</form>

</div>

<script type="text/javascript">
 var $j = jQuery.noConflict();
 $j(function() {
 	$j("#datatime_s").datepicker({     	                
                    dateFormat: 'yy-mm-dd'
                 }); 	
    $j("#datatime_e").datepicker({     	                
                    dateFormat: 'yy-mm-dd'
                 }); 	             
 });
 function gobackapp()
 {
     document.location = 'admin.php?page=dex_bccf&cal=<?php echo $_GET["cal"]; ?>&list=1&r='+Math.random();
 }
</script>


<?php } else { ?>
  <br />
  The current user logged in doesn't have enough permissions to edit this item. Please log in as administrator to get full access.

<?php } ?>








