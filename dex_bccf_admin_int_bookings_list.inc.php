<?php

if ( !is_admin() )
{
    echo 'Direct access not allowed.';
    exit;
}

if (!defined('CP_BCCF_CALENDAR_ID'))
    define ('CP_BCCF_CALENDAR_ID',intval($_GET["cal"]));

global $wpdb;
$mycalendarrows = $wpdb->get_results( 'SELECT * FROM '.DEX_BCCF_CONFIG_TABLE_NAME .' WHERE `'.TDE_BCCFCONFIG_ID.'`='.CP_BCCF_CALENDAR_ID);

$message = "";

if (isset($_GET['ld']) && $_GET['ld'] != '')
{
    $wpdb->query('DELETE FROM `'.DEX_BCCF_CALENDARS_TABLE_NAME.'` WHERE id='.intval($_GET['ld']));       
    $message = "Item deleted";
} else if (isset($_GET['lup']) && $_GET['lup'] != '')
{
    $wpdb->query("UPDATE `".DEX_BCCF_CALENDARS_TABLE_NAME."` SET status='".$_GET["paid"]."' WHERE id=".intval($_GET['lup']));       
    $message = "Item paid status updated";
}


if ($message) echo "<div id='setting-error-settings_updated' class='updated settings-error'> <p><strong>".$message."</strong></p></div>";

$current_user = wp_get_current_user();

if (cp_bccf_is_administrator() || $mycalendarrows[0]->conwer == $current_user->ID) {

$current_page = intval($_GET["p"]);
if (!$current_page) $current_page = 1;
$records_per_page = 50;                                                                                  

$cond = '';
if (is_numeric($_GET["search"]))
{
    if ($_GET["search"] != '') $cond .= " AND (title like '%".esc_sql($_GET["search"])."%' OR description LIKE '%".esc_sql($_GET["search"])."%' OR id=".intval($_GET["search"]).")";
}
else
{
    if ($_GET["search"] != '') $cond .= " AND (title like '%".esc_sql($_GET["search"])."%' OR description LIKE '%".esc_sql($_GET["search"])."%')";
}    
if ($_GET["dfrom"] != '') $cond .= " AND (datatime_s >= '".esc_sql($_GET["dfrom"])."')";
if ($_GET["dto"] != '') $cond .= " AND (datatime_s <= '".esc_sql($_GET["dto"])." 23:59:59')";



$events_query = "SELECT * FROM ".DEX_BCCF_CALENDARS_TABLE_NAME." WHERE reservation_calendar_id=".CP_BCCF_CALENDAR_ID.$cond." ORDER BY datatime_s DESC";
/**
 * Allows modify the query of messages, passing the query as parameter
 * returns the new query
 */
$events_query = apply_filters( 'dexbccf_messages_query', $events_query );
$events = $wpdb->get_results( $events_query );
$total_pages = ceil(count($events) / $records_per_page);

$option_calendar_enabled = dex_bccf_get_option('calendar_enabled', DEX_BCCF_DEFAULT_CALENDAR_ENABLED);
 
?>
<script type="text/javascript">
 function cp_deleteMessageItem(id)
 {
    if (confirm('Are you sure that you want to delete this item?'))
    {        
        document.location = 'admin.php?page=dex_bccf&cal=<?php echo $_GET["cal"]; ?>&list=1&ld='+id+'&r='+Math.random();
    }
 }
 function cp_editItem(id, cal)
 {
     document.location = 'admin.php?page=dex_bccf&cal='+cal+'&edit='+id+'&r='+Math.random();
 } 
 function cp_updatePaid(id,paid) {
    document.location = 'admin.php?page=dex_bccf&cal=<?php echo $_GET["cal"]; ?>&list=1&paid='+paid+'&lup='+id+'&r='+Math.random();
 }
</script>
<div class="wrap">
<h1>Reservations List</h1>

<input type="button" name="backbtn" value="Back to items list..." onclick="document.location='admin.php?page=dex_bccf';">

<input type="button" name="noncbtn" style="color:#ff0000;" value="Check non-completed bookings list..." onclick="document.location='admin.php?page=dex_bccf&cal=<?php echo $_GET["cal"]; ?>&list2=1';">

<div id="normal-sortables" class="meta-box-sortables">
 <hr />
 <h3>This list only applies to <?php echo $mycalendarrows[0]->uname; ?></h3>
</div>


<form action="admin.php" method="get">
 <input type="hidden" name="page" value="dex_bccf" />
 <input type="hidden" name="cal" value="<?php echo CP_BCCF_CALENDAR_ID; ?>" />
 <input type="hidden" name="list" value="1" />
 Search for: <input type="text" name="search" value="<?php echo esc_attr($_GET["search"]); ?>" /> &nbsp; &nbsp; &nbsp; 
 From: <input type="text" id="dfrom" name="dfrom" value="<?php echo esc_attr($_GET["dfrom"]); ?>" /> &nbsp; &nbsp; &nbsp; 
 To: <input type="text" id="dto" name="dto" value="<?php echo esc_attr($_GET["dto"]); ?>" /> &nbsp; &nbsp; &nbsp; 
 <span class="submit"><input type="submit" name="ds" value="Filter" /></span>
 <span class="submit"><input type="submit" name="bccf_appointments_csv" value="Export to CSV" /></span>
</form>

<br />
                             
<?php


echo paginate_links(  array(
    'base'         => 'admin.php?page=dex_bccf&cal='.CP_BCCF_CALENDAR_ID.'&list=1%_%&dfrom='.urlencode($_GET["dfrom"]).'&dto='.urlencode($_GET["dto"]).'&search='.urlencode($_GET["search"]),
    'format'       => '&p=%#%',
    'total'        => $total_pages,
    'current'      => $current_page,
    'show_all'     => False,
    'end_size'     => 1,
    'mid_size'     => 2,
    'prev_next'    => True,
    'prev_text'    => '&laquo; '.__('Previous','bccf'),
    'next_text'    => __('Next','bccf').' &raquo;',
    'type'         => 'plain',
    'add_args'     => False
    ) );

?>

<div id="dex_printable_contents">
<table class="wp-list-table widefat fixed pages" cellspacing="0">
	<thead>
	<tr>
	  <th style="padding-left:7px;font-weight:bold;width:70px;">ID</th>
	  <th style="padding-left:7px;font-weight:bold;">Date</th>
	  <th style="padding-left:7px;font-weight:bold;">Title</th>
	  <th style="padding-left:7px;font-weight:bold;">Description</th>
	  <th style="padding-left:7px;font-weight:bold;" width="100" nowrap>Status</th>
	  <th style="padding-left:7px;font-weight:bold;">Options</th>
	</tr>
	</thead>
	<tbody id="the-list">
	 <?php for ($i=($current_page-1)*$records_per_page; $i<$current_page*$records_per_page; $i++) if (isset($events[$i])) { ?>
	  <tr class='<?php if (!($i%2)) { ?>alternate <?php } ?>author-self status-draft format-default iedit' valign="top">
	    <td width="1%"><?php echo $events[$i]->id; ?></td>
		<td><?php echo substr($events[$i]->datatime_s,0,10); ?><?php if ($option_calendar_enabled != 'false') { ?> - <?php echo substr($events[$i]->datatime_e,0,10); ?><?php } ?></td>
		<td><?php echo $events[$i]->title; ?></td>
		<td><?php echo str_replace("<br /><br />","<br />",$events[$i]->description); ?></td>
		<td><?php if ($events[$i]->status) echo '<span style="color:red;font-weight:bold">Paid</span>'; ?></td>
		<td>		  
		  <input type="button" name="caledit_<?php echo $events[$i]->id; ?>" value="Edit" onclick="cp_editItem(<?php echo $events[$i]->id; ?>,<?php echo $_GET["cal"]; ?>);" />
		  <input type="button" name="calpaid_<?php echo $events[$i]->id; ?>" value="Change Paid Status" onclick="cp_updatePaid(<?php echo $events[$i]->id; ?>,'<?php if (!$events[$i]->status) echo '1'; else echo ''; ?>');" />                             
		  <input type="button" name="caldelete_<?php echo $events[$i]->id; ?>" value="Delete" onclick="cp_deleteMessageItem(<?php echo $events[$i]->id; ?>);" />                             
		</td>		
      </tr>
     <?php } ?>
	</tbody>
</table>
</div>

<p class="submit"><input type="button" name="pbutton" value="Print" onclick="do_dexapp_print();" /></p>

</div>


<script type="text/javascript">
 function do_dexapp_print()
 {
      w=window.open();
      w.document.write("<style>table{border:2px solid black;width:100%;}th{border-bottom:2px solid black;text-align:left}td{padding-left:10px;border-bottom:1px solid black;}</style>"+document.getElementById('dex_printable_contents').innerHTML);
      w.print();
      w.close();    
 }
 
 var $j = jQuery.noConflict();
 $j(function() {
 	$j("#dfrom").datepicker({     	                
                    dateFormat: 'yy-mm-dd'
                 });
 	$j("#dto").datepicker({     	                
                    dateFormat: 'yy-mm-dd'
                 });
 });
 
</script>




<?php } else { ?>
  <br />
  The current user logged in doesn't have enough permissions to edit this calendar. This user can edit only his/her own calendars. Please log in as administrator to get access to all calendars.

<?php } ?>










