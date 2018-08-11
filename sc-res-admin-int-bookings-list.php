<?php
/**
 * The admin page for reservation form submissions.
 *
 * @package    Sturtevant_Reservations
 * @subpackage Admin
 *
 * @since      1.0.0
 * @author     Greg Sweet <greg@ccdzine.com>
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

if ( ! defined( 'CP_BCCF_CALENDAR_ID' ) ) {
    define ( 'CP_BCCF_CALENDAR_ID', intval( $_GET['cal'] ) );
}

global $wpdb;

$mycalendarrows = $wpdb->get_results( 'SELECT * FROM '.DEX_BCCF_CONFIG_TABLE_NAME .' WHERE `'.TDE_BCCFCONFIG_ID.'`='.CP_BCCF_CALENDAR_ID );

$message        = '';

if ( isset( $_GET['ld'] ) && $_GET['ld'] != '' ) {

    $wpdb->query('DELETE FROM `'.DEX_BCCF_CALENDARS_TABLE_NAME.'` WHERE id='.intval($_GET['ld']));
    $message = __( 'Form deleted', 'sc-res' );

} elseif ( isset( $_GET['lup'] ) && $_GET['lup'] != '' ) {

    $wpdb->query("UPDATE `".DEX_BCCF_CALENDARS_TABLE_NAME."` SET status='".$_GET["paid"]."' WHERE id=".intval($_GET['lup']));
    $message = __( 'Form paid status updated', 'sc-res' );

}

if ( $message ) {
    echo '<div id="setting-error-settings_updated" class="notice notice-success is-dismissible"> <p><strong>' . $message . '</strong></p></div>';
}

$current_user = wp_get_current_user();

if ( cp_bccf_is_administrator() || $mycalendarrows[0]->conwer == $current_user->ID ) {

    $current_page = intval( $_GET['p'] );

    if ( ! $current_page ) {
        $current_page = 1;
    }

    $records_per_page = 50;
    $cond             = '';

    if ( is_numeric( $_GET["search"] ) ) {
        if ($_GET["search"] != '') $cond .= " AND (title like '%".esc_sql($_GET["search"])."%' OR description LIKE '%".esc_sql($_GET["search"])."%' OR id=".intval($_GET["search"]).")";
    } else {
        if ($_GET["search"] != '') $cond .= " AND (title like '%".esc_sql($_GET["search"])."%' OR description LIKE '%".esc_sql($_GET["search"])."%')";
    }

    if ( $_GET["dfrom"] != '' ) {
        $cond .= " AND (datatime_s >= '".esc_sql($_GET["dfrom"])."')";
    }

    if ( $_GET["dto"] != '' ) {
        $cond .= " AND (datatime_s <= '".esc_sql($_GET["dto"])." 23:59:59')";
    }

    $events_query = "SELECT * FROM ".DEX_BCCF_CALENDARS_TABLE_NAME." WHERE reservation_calendar_id=".CP_BCCF_CALENDAR_ID.$cond." ORDER BY datatime_s DESC";

    /**
     * Allows modify the query of messages, passing the query as parameter
     * returns the new query
     */
    $events_query            = apply_filters( 'dexbccf_messages_query', $events_query );
    $events                  = $wpdb->get_results( $events_query );
    $total_pages             = ceil( count( $events ) / $records_per_page );
    $option_calendar_enabled = dex_bccf_get_option( 'calendar_enabled', DEX_BCCF_DEFAULT_CALENDAR_ENABLED );
    ?>
    <script type="text/javascript">
        function cp_deleteMessageItem(id) {
            if ( confirm( '<?php _e( 'Are you sure that you want to delete this reservation?', 'sc-res' ); ?>' ) ) {
                document.location = 'admin.php?page=dex_bccf&cal=<?php echo $_GET['cal']; ?>&list=1&ld=' + id + '&r=' + Math.random();
            }
        }

        function cp_editItem( id, cal ) {
            document.location = 'admin.php?page=dex_bccf&cal=' + cal + '&edit=' + id + '&r=' + Math.random();
        }

        function cp_updatePaid( id,paid ) {
            document.location = 'admin.php?page=dex_bccf&cal=<?php echo $_GET['cal']; ?>&list=1&paid=' + paid + '&lup=' + id + '&r=' + Math.random();
        }
    </script>

<div class="wrap reservations reservation-submissions">
    <h1><?php _e( 'Reservation Form Submissions', 'sc-res' ); ?></h1>
    <p class="description"><?php _e( 'List of completed and submitted reservation forms.', 'sc-res' ); ?></p>
    <p>
        <input class="button" type="button" name="backbtn" value="<?php _e( 'Back to Forms', 'sc-res' ); ?>" onclick="document.location='admin.php?page=dex_bccf';">
        <input class="button" type="button" name="noncbtn" value="<?php _e( 'Forms Not Completed', 'sc-res' ); ?>" onclick="document.location='admin.php?page=dex_bccf&cal=<?php echo $_GET["cal"]; ?>&list2=1';">
    </p>
    <hr />
    <h2><?php _e( 'This list applies only to ', 'sc-res' ); ?><?php echo $mycalendarrows[0]->uname; ?></h2>
    <!-- List form -->
    <form action="admin.php" method="get">
        <!-- Hidden -->
        <input type="hidden" name="page" value="dex_bccf" />
        <input type="hidden" name="cal" value="<?php echo CP_BCCF_CALENDAR_ID; ?>" />
        <input type="hidden" name="list" value="1" />
        <!-- Search -->
        <label for="search"><?php _e( 'Search for:', 'sc-res' ); ?></label>
        <input type="text" id="search" name="search" value="<?php echo esc_attr( $_GET['search'] ); ?>" />
        <!-- From -->
        <label for="dfrom"><?php _e( 'From:', 'sc-res' ); ?></label>
        <input type="text" id="dfrom" name="dfrom" value="<?php echo esc_attr($_GET['search']); ?>" />
        <!-- To -->
        <label for="dto"><?php _e( 'To:', 'sc-res' ); ?></label>
        <input type="text" id="dto" name="dto" value="<?php echo esc_attr($_GET['dto']); ?>" />
        <!-- Action buttons -->
        <span class="submit"><input class="button" type="submit" name="ds" value="<?php _e( 'Filter', 'sc-res' ); ?>" /></span>
        <span class="submit"><input class="button" type="submit" name="bccf_appointments_csv" value="<?php _e( 'Export to CSV', 'sc-res' ); ?>" /></span>
    </form>
    <!-- End list form -->
    <br />
    <?php echo paginate_links( [
        'base'      => 'admin.php?page=reservations&cal=' . CP_BCCF_CALENDAR_ID . '&list=1%_%&dfrom=' . urlencode( $_GET['dfrom'] ) . '&dto=' . urlencode( $_GET['dto'] ) . '&search=' . urlencode( $_GET['search'] ),
        'format'    => '&p=%#%',
        'total'     => $total_pages,
        'current'   => $current_page,
        'show_all'  => false,
        'end_size'  => 1,
        'mid_size'  => 2,
        'prev_next' => true,
        'prev_text' => '&laquo; ' . __( 'Previous','sc-res' ),
        'next_text' => __( 'Next','sc-res' ) . ' &raquo;',
        'type'      => 'plain',
        'add_args'  => false
    ] ); ?>
    <div id="dex_printable_contents">
        <table class="wp-list-table widefat fixed pages" cellspacing="0">
            <thead>
                <tr>
                    <th style="padding-left:7px;font-weight:bold;width:70px;"><?php _e( 'ID', 'sc-res' ); ?></th>
                    <th style="padding-left:7px;font-weight:bold;"><?php _e( 'Date', 'sc-res' ); ?></th>
                    <th style="padding-left:7px;font-weight:bold;"><?php _e( 'Title', 'sc-res' ); ?></th>
                    <th style="padding-left:7px;font-weight:bold;"><?php _e( 'Description', 'sc-res' ); ?></th>
                    <th style="padding-left:7px;font-weight:bold;" width="100" nowrap><?php _e( 'Status', 'sc-res' ); ?></th>
                    <th style="padding-left:7px;font-weight:bold;"><?php _e( 'Options', 'sc-res' ); ?></th>
                </tr>
            </thead>
            <tbody id="the-list">
            <?php for ( $i = ( $current_page-1 ) * $records_per_page; $i < $current_page * $records_per_page; $i++ ) if ( isset( $events[$i] ) ) : ?>
                <tr class="<?php if ( ! ( $i%2 ) ) { echo alternate . ' '; } ?>author-self status-draft format-default iedit" valign="top">
                    <td width="1%"><?php echo $events[$i]->id; ?></td>
                    <td><?php echo substr( $events[$i]->datatime_s, 0, 10 ); ?><?php if ( $option_calendar_enabled != 'false' ) { ?> - <?php echo substr( $events[$i]->datatime_e, 0, 10 ); ?><?php } ?></td>
                    <td><?php echo $events[$i]->title; ?></td>
                    <td><?php echo str_replace( '<br /><br />', '<br />', $events[$i]->description ); ?></td>
                    <td><?php if ( $events[$i]->status ) { echo '<span style="color:red;font-weight:bold">Paid</span>'; } ?></td>
                    <td>
                        <input type="button" name="caledit_<?php echo $events[$i]->id; ?>" value="<?php _e( 'Edit', 'sc-res' ); ?>" onclick="cp_editItem(<?php echo $events[$i]->id; ?>,<?php echo $_GET["cal"]; ?>);" />
                        <input type="button" name="calpaid_<?php echo $events[$i]->id; ?>" value="<?php _e( 'Change Paid Status', 'sc-res' ); ?>" onclick="cp_updatePaid(<?php echo $events[$i]->id; ?>,'<?php if ( ! $events[$i]->status) {  echo '1'; } else { echo ''; } ?>');" />
                        <input type="button" name="caldelete_<?php echo $events[$i]->id; ?>" value="<?php _e( 'Delete', 'sc-res' ); ?>" onclick="cp_deleteMessageItem(<?php echo $events[$i]->id; ?>);" />
                    </td>
                </tr>
            <?php endif; ?>
            </tbody>
        </table>
    </div>
    <p class="submit">
        <input class="button button-primary" type="button" name="pbutton" value="<?php _e( 'Print List', 'sc-res' ); ?>" onclick="do_dexapp_print();" />
    </p>
</div><!-- end .wrap -->
<script type="text/javascript">
    function do_dexapp_print() {

        w=window.open();

        w.document.write("<style>table{border:2px solid black;width:100%;}th{border-bottom:2px solid black;text-align:left}td{padding-left:10px;border-bottom:1px solid black;}</style>"+document.getElementById('dex_printable_contents').innerHTML);

        w.print();
        w.close();
    }

    var $j = jQuery.noConflict();

    $j( function() {
        $j( '#dfrom' ).datepicker({
            dateFormat: 'yy-mm-dd'
        });

        $j( '#dto' ).datepicker({
            dateFormat: 'yy-mm-dd'
        });
    });
</script>
<?php } else { ?>
    <br />
    <p><?php _e( 'The current user logged in doesn\'t have enough permissions to edit this calendar. This user can edit only his/her own calendars. Please log in as administrator to get access to all calendars.', 'sc_res' ); ?></p>
<?php }