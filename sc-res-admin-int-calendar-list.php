<?php

if ( !is_admin() ) 
{
    echo 'Direct access not allowed.';
    exit;
}

$current_user = wp_get_current_user();

global $wpdb, $dexbccf_addons_active_list, $dexbccf_addons_objs_list;

$message = "";

if (isset($_POST["bccf_fileimport"]) && $_POST["bccf_fileimport"] == 1)
{    
    $filename = $_FILES['cp_filename']['tmp_name'];
    $handle = fopen($filename, "r");
    $contents = fread($handle, filesize($filename));
    fclose($handle);
    $params = unserialize($contents);
    $wpdb->query( $wpdb->prepare( 'DELETE FROM `'.DEX_BCCF_CONFIG_TABLE_NAME.'` WHERE id=%d', $params['id'] ) );    
    unset($params["form_name"]);
    $wpdb->insert( DEX_BCCF_CONFIG_TABLE_NAME, $params);
    @unlink($filename);
    $message = "Backup loaded.";
}
else if( isset( $_GET[ 'b' ] ) && $_GET[ 'b' ] == 1 )
{
	// Save the option for active addons
	delete_option( 'dexbccf_addons_active_list' );
	if( !empty( $_GET[ 'dexbccf_addons_active_list' ] ) && is_array( $_GET[ 'dexbccf_addons_active_list' ] ) ) 
	{
		update_option( 'dexbccf_addons_active_list', $_GET[ 'dexbccf_addons_active_list' ] );
	}	
	
	// Get the list of active addons
	$dexbccf_addons_active_list = get_option( 'dexbccf_addons_active_list', array() );
}

if (isset($_GET['a']) && $_GET['a'] == '1')
{
    $sql .= 'INSERT INTO `'.$wpdb->prefix ."bccf_reservation_calendars".'` (`'.TDE_BCCFCONFIG_TITLE.'`,`'.TDE_BCCFCONFIG_USER.'`,`'.TDE_BCCFCONFIG_PASS.'`,`'.TDE_BCCFCONFIG_LANG.'`,`'.TDE_BCCFCONFIG_CPAGES.'`,`'.TDE_BCCFCONFIG_MSG.'`,`'.TDE_BCCFCALDELETED_FIELD.'`,calendar_mode) '.
            ' VALUES("","'.$_GET["name"].'","","ENG","1","Please, select your reservation.","0","true");';

    $wpdb->query($sql);   

    $results = $wpdb->get_results('SELECT `'.TDE_BCCFCONFIG_ID.'` FROM `'.DEX_BCCF_CONFIG_TABLE_NAME.'` ORDER BY `'.TDE_BCCFCONFIG_ID.'` DESC LIMIT 0,1');        
    $wpdb->query('UPDATE `'.DEX_BCCF_CONFIG_TABLE_NAME.'` SET `'.TDE_BCCFCONFIG_TITLE.'`="cal'.$results[0]->id.'" WHERE `'.TDE_BCCFCONFIG_ID.'`='.$results[0]->id);           
    $message = "Item added";
} 
else if (isset($_GET['u']) && $_GET['u'] != '')
{
    $wpdb->query('UPDATE `'.DEX_BCCF_CONFIG_TABLE_NAME.'` SET conwer='.intval($_GET["owner"]).',`'.TDE_BCCFCALDELETED_FIELD.'`='.intval($_GET["public"]).',`'.TDE_BCCFCONFIG_USER.'`="'.$_GET["name"].'" WHERE `'.TDE_BCCFCONFIG_ID.'`='.intval($_GET['u']));           
    $message = "Item updated";        
}
else if (isset($_GET['d']) && $_GET['d'] != '')
{
    $wpdb->query('DELETE FROM `'.DEX_BCCF_CONFIG_TABLE_NAME.'` WHERE `'.TDE_BCCFCONFIG_ID.'`='.intval($_GET['d']));       
    $message = "Item deleted";
}  
else if (isset($_GET['c']) && $_GET['c'] != '')
{
    $myrows = $wpdb->get_row( "SELECT * FROM ".DEX_BCCF_CONFIG_TABLE_NAME." WHERE `".TDE_BCCFCONFIG_ID."`=".intval($_GET['c']), ARRAY_A);    
    unset($myrows[TDE_BCCFCONFIG_ID]);
    $myrows[TDE_BCCFCONFIG_USER] = 'Cloned: '.$myrows[TDE_BCCFCONFIG_USER];
    $wpdb->insert( DEX_BCCF_CONFIG_TABLE_NAME, $myrows);
    $message = "Item duplicated/cloned";
}
else if (isset($_GET['ac']) && $_GET['ac'] == 'st')
{   
    update_option( 'CP_BCCF_LOAD_SCRIPTS', ($_GET["scr"]=="1"?"0":"1") );   
    if ($_GET["chs"] != '')
    {
        $target_charset = esc_sql($_GET["chs"]);
        $tables = array( $wpdb->prefix.DEX_BCCF_TABLE_NAME_NO_PREFIX, $wpdb->prefix.DEX_BCCF_CALENDARS_TABLE_NAME_NO_PREFIX, $wpdb->prefix.DEX_BCCF_CONFIG_TABLE_NAME_NO_PREFIX );                
        foreach ($tables as $tab)
        {  
            $myrows = $wpdb->get_results( "DESCRIBE {$tab}" );                                                                                 
            foreach ($myrows as $item)
	        {
	            $name = $item->Field;
		        $type = $item->Type;
		        if (preg_match("/^varchar\((\d+)\)$/i", $type, $mat) || !strcasecmp($type, "CHAR") || !strcasecmp($type, "TEXT") || !strcasecmp($type, "MEDIUMTEXT"))
		        {
	                $wpdb->query("ALTER TABLE {$tab} CHANGE {$name} {$name} {$type} COLLATE {$target_charset}");	            
	            }
	        }
        }
    }
    $message = "Troubleshoot settings updated";
}


if ($message) echo "<div id='setting-error-settings_updated' class='updated settings-error'><p><strong>".$message."</strong></p></div>";

?>
<div class="wrap">
<h2>Booking Calendar Contact Form</h2>

<script type="text/javascript">
 function cp_activateAddons()
 {
    var dexbccf_addons = document.getElementsByName("dexbccf_addons"),
		dexbccf_addons_active_list = [];
	for( var i = 0, h = dexbccf_addons.length; i < h; i++ )
	{
		if( dexbccf_addons[ i ].checked ) dexbccf_addons_active_list.push( 'dexbccf_addons_active_list[]='+encodeURIComponent( dexbccf_addons[ i ].value ) );
	}	
	document.location = 'options-general.php?page=dex_bccf&b=1&r='+Math.random()+( ( dexbccf_addons_active_list.length ) ? '&'+dexbccf_addons_active_list.join( '&' ) : '' )+'&_dexbccf_nonce=<?php echo wp_create_nonce( 'session_id_'.session_id() ); ?>#addons-section';       
 }    
     
 function cp_addItem()
 {
    var calname = document.getElementById("cp_itemname").value;
    document.location = 'admin.php?page=dex_bccf&a=1&r='+Math.random()+'&name='+encodeURIComponent(calname);       
 }
 
 function cp_updateItem(id)
 {
    var calname = document.getElementById("calname_"+id).value;
    var owner = document.getElementById("calowner_"+id).options[document.getElementById("calowner_"+id).options.selectedIndex].value;    
    if (owner == '')
        owner = 0;
    var is_public = (document.getElementById("calpublic_"+id).checked?"0":"1");
    document.location = 'admin.php?page=dex_bccf&u='+id+'&r='+Math.random()+'&public='+is_public+'&owner='+owner+'&name='+encodeURIComponent(calname);    
 }
 
 function cp_cloneItem(id)
 {
    document.location = 'admin.php?page=dex_bccf&c='+id+'&r='+Math.random();  
 }  
 
 function cp_manageSettings(id)
 {
    document.location = 'admin.php?page=dex_bccf&cal='+id+'&r='+Math.random();
 }
 
 function cp_BookingsList(id)
 {
    document.location = 'admin.php?page=dex_bccf&cal='+id+'&list=1&r='+Math.random();
 }
 
 function cp_deleteItem(id)
 {
    if (confirm('Are you sure that you want to delete this item?'))
    {        
        document.location = 'admin.php?page=dex_bccf&d='+id+'&r='+Math.random();
    }
 }
 
 function cp_updateConfig()
 {
    if (confirm('Are you sure that you want to update these settings?'))
    {        
        var scr = document.getElementById("ccscriptload").value;    
        var chs = document.getElementById("cccharsets").value;    
        document.location = 'admin.php?page=dex_bccf&ac=st&scr='+scr+'&chs='+chs+'&r='+Math.random();
    }    
 }
 
 function cp_exportItem()
 {
    var calname = document.getElementById("exportid").options[document.getElementById("exportid").options.selectedIndex].value;
    document.location = 'admin.php?page=dex_bccf&bccf_export=1&r='+Math.random()+'&name='+encodeURIComponent(calname);       
 }
 
</script>


<div id="normal-sortables" class="meta-box-sortables">


 <div id="metabox_basic_settings" class="postbox" >
  <h3 class='hndle' style="padding:5px;"><span>Calendar List / Items List</span></h3>
  <div class="inside">
  
  
  <table cellspacing="1"> 
   <tr>
    <th align="left">ID</th><th align="left">Item Name</th><th align="left">Owner</th><th align="left">Public</th><th align="left">Feed</th><th align="left">&nbsp; &nbsp; Options</th><th align="left">Shorttag for Pages and Posts</th>
   </tr> 
<?php  

  $users = $wpdb->get_results( "SELECT user_login,ID FROM ".$wpdb->users." ORDER BY ID DESC" );                                                                     

  $myrows = $wpdb->get_results( "SELECT * FROM ".$wpdb->prefix ."bccf_reservation_calendars" );                                                                     
  foreach ($myrows as $item)   
      if (cp_bccf_is_administrator() || ($current_user->ID == $item->conwer))
      {
?>
   <tr> 
    <td nowrap><?php echo $item->id; ?></td>
    <td nowrap><input type="text" style="width:100px;" <?php if (!cp_bccf_is_administrator()) echo ' readonly '; ?>name="calname_<?php echo $item->id; ?>" id="calname_<?php echo $item->id; ?>" value="<?php echo esc_attr($item->uname); ?>" /></td>
    
    <?php if (cp_bccf_is_administrator()) { ?>
    <td nowrap>
      <select name="calowner_<?php echo $item->id; ?>" id="calowner_<?php echo $item->id; ?>">
       <option value="0"<?php if (!$item->conwer) echo ' selected'; ?>></option>
       <?php foreach ($users as $user) { 
       ?>
          <option value="<?php echo $user->ID; ?>"<?php if ($user->ID."" == $item->conwer) echo ' selected'; ?>><?php echo $user->user_login; ?></option>
       <?php  } ?>
      </select>
    </td>    
    <?php } else { ?>
        <td nowrap>
        <?php echo $current_user->user_login; ?>
        </td>
    <?php } ?>
    
    <td nowrap align="center">
       <?php if (cp_bccf_is_administrator()) { ?> 
         &nbsp; &nbsp; <input type="checkbox" name="calpublic_<?php echo $item->id; ?>" id="calpublic_<?php echo $item->id; ?>" value="1" <?php if (!$item->caldeleted) echo " checked "; ?> />
       <?php } else { ?>  
         <?php if (!$item->caldeleted) echo "Yes"; else echo "No"; ?> 
       <?php } ?>   
    </td>
    <td nowrap><a href="<?php get_site_url(); ?>?dex_bccf=calfeed&id=<?php echo $item->id; ?>">iCal</a></td>
    <td nowrap>&nbsp; &nbsp; 
                             <?php if (cp_bccf_is_administrator()) { ?> 
                               <input type="button" name="calupdate_<?php echo $item->id; ?>" value="Update" onclick="cp_updateItem(<?php echo $item->id; ?>);" /> &nbsp; 
                             <?php } ?>    
                             <input type="button" name="calmanage_<?php echo $item->id; ?>" value="Settings " onclick="cp_manageSettings(<?php echo $item->id; ?>);" /> &nbsp; 
                             <input type="button" name="calbookings_<?php echo $item->id; ?>" value="Bookings / Contacts" onclick="cp_BookingsList(<?php echo $item->id; ?>);" /> &nbsp; 
                             <input type="button" name="calclone_<?php echo $item->id; ?>" value="Clone" onclick="cp_cloneItem(<?php echo $item->id; ?>);" /> &nbsp;                              
                             <?php if (cp_bccf_is_administrator()) { ?> 
                               <input type="button" name="caldelete_<?php echo $item->id; ?>" value="Delete" onclick="cp_deleteItem(<?php echo $item->id; ?>);" />
                             <?php } ?>  
    </td>
    <td style="font-size:11px;" nowrap>[CP_BCCF_FORM calendar="<?php echo $item->id; ?>"]</td> 
   </tr>
<?php  
      } 
?>   
     
  </table> 
    
    
   
  </div>    
 </div> 
 
<?php if (cp_bccf_is_administrator()) { ?> 
 
 <div id="metabox_basic_settings" class="postbox" >
  <h3 class='hndle' style="padding:5px;"><span>New Calendar / Item</span></h3>
  <div class="inside"> 
   
    <form name="additem">
      Item Name:<br />
      <input type="text" name="cp_itemname" id="cp_itemname"  value="" /> <input type="button" onclick="cp_addItem();" name="gobtn" value="Add" />
      <br /><br />
      
    </form>

  </div>    
 </div>
 

 <div id="metabox_basic_settings" class="postbox" >
  <h3 class='hndle' style="padding:5px;"><span>Troubleshoot Area</span></h3>
  <div class="inside"> 
    <p><strong>Important!</strong>: Use this area <strong>only</strong> if you are experiencing conflicts with third party plugins, with the theme scripts or with the character encoding.</p>
    <form name="updatesettings">
      Script load method:<br />
       <select id="ccscriptload" name="ccscriptload">
        <option value="0" <?php if (get_option('CP_BCCF_LOAD_SCRIPTS',"1") == "1") echo 'selected'; ?>>Classic (Recommended)</option>
        <option value="1" <?php if (get_option('CP_BCCF_LOAD_SCRIPTS',"1") != "1") echo 'selected'; ?>>Direct</option>
       </select><br />
       <em>* Change the script load method if the form doesn't appear in the public website.</em>
      
      <br /><br />
      Character encoding:<br />
       <select id="cccharsets" name="cccharsets">
        <option value="">Keep current charset (Recommended)</option>
        <option value="utf8_general_ci">UTF-8 (try this first)</option>
        <option value="latin1_swedish_ci">latin1_swedish_ci</option>
       </select><br />
       <em>* Update the charset if you are getting problems displaying special/non-latin characters. After updated you need to edit the special characters again.</em>
       <br />
       <input type="button" onclick="cp_updateConfig();" name="gobtn" value="UPDATE" />
      <br /><br />      
    </form>

  </div>    
 </div> 


<a name="addons-section"></a> 
<h2><?php _e( 'Add-Ons Settings', 'dexbccf' ); ?>:</h2><hr />
<div id="metabox_basic_settings" class="postbox" >
	<h3 class='hndle' style="padding:5px;"><span><?php _e( 'Add-ons Area', 'dexbccf' ); ?></span></h3>
	<div class="inside"> 
	<?php
	foreach( $dexbccf_addons_objs_list as $key => $obj )
	{
		print '<div><label for="'.$key.'" style="font-weight:bold;"><input type="checkbox" id="'.$key.'" name="dexbccf_addons" value="'.$key.'" '.( ( $obj->addon_is_active() ) ? 'CHECKED' : '' ).'>'.$obj->get_addon_name().'</label> <div style="font-style:italic;padding-left:20px;">'.$obj->get_addon_description().'</div></div>';
	}
	?>
	<div style="margin-top:20px;"><input type="button" onclick="cp_activateAddons();" name="activateAddon" value="<?php esc_attr_e( 'Activate/Deactivate Addons', 'dexbccf' ); ?>" /></div>
	</div>
</div>

<?php
	if( count( $dexbccf_addons_active_list ) )
	{	
		foreach( $dexbccf_addons_active_list as $addon_id ) if( isset( $dexbccf_addons_objs_list[ $addon_id ] ) ) print $dexbccf_addons_objs_list[ $addon_id ]->get_addon_settings();
	}
?>  


  <script type="text/javascript">
   function cp_editArea(id)
   {       
          document.location = 'admin.php?page=dex_bccf&editk=1&cal=1&item='+id+'&r='+Math.random();
   }
  </script>
  <div id="metabox_basic_settings" class="postbox" >
  <h3 class='hndle' style="padding:5px;"><span>Customization Area</span></h3>
  <div class="inside"> 
      <p>Use this area to add custom CSS styles or custom scripts. These styles and scripts will be keep safe even after updating the plugin.</p>
      <input type="button" onclick="cp_editArea('css');" name="gobtn3" value="Add Custom Styles" />
      &nbsp; &nbsp; &nbsp;      
      <input type="button" onclick="cp_editArea('js');" name="gobtn2" value="Add Custom JavaScript" />
  </div>    
 </div> 
 
 
<?php } ?> 


 <div id="metabox_basic_settings" class="postbox" >
  <h3 class='hndle' style="padding:5px;"><span>Backup / Restore Area</span></h3>
  <div class="inside"> 
    <p>Use this area <strong>only</strong> to <strong>backup/restore calendar settings.</p>
    <hr />
    <form name="exportitem">
      Export this form structure and settings:<br />
      <select id="exportid" name="exportid">
       <?php  
          foreach ($myrows as $item)         
              echo '<option value="'.$item->id.'">'.$item->uname.'</option>';
       ?>   
      </select> 
      <input type="button" onclick="cp_exportItem();" name="gobtn" value="Export" />
      <br /><br />      
    </form>
    <hr />
    <form name="importitem" action="admin.php?page=dex_bccf" method="post" enctype="multipart/form-data">      
      <input type="hidden" name="bccf_fileimport" id="bccf_fileimport"  value="1" />
      Import a form structure and settings (will OVERWRITE the related form. Only <em>.bccf</em> files ):<br />
      <input type="file" name="cp_filename" id="cp_filename"  value="" /> <input type="submit" name="gobtn" value="Import" />
      <br /><br />
    </form>

  </div>    
 </div>
 
  
</div> 


[<a href="http://wordpress.dwbooster.com/contact-us" target="_blank">Request Custom Modifications</a>] | [<a href="http://wordpress.dwbooster.com/calendars/booking-calendar-contact-form" target="_blank">Help</a>]
</form>
</div>














