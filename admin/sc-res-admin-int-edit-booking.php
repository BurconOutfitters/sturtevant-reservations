<?php
/**
 * The admin page for editing form submissions.
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

$current_user = wp_get_current_user();

if ( true ) {   // (cp_sc_res_is_administrator() || $mycalendarrows[0]->conwer == $current_user->ID) {

    $event = $wpdb->get_results( "SELECT * FROM ".TDE_BCCFCALENDAR_DATA_TABLE." WHERE id=" . esc_sql( $_GET['edit'] ) );
    $event = $event[0];

    if ( $event->reference != '' ) {
        $form_data = json_decode( sc_res_cleanJSON( dex_bccf_get_option( 'form_structure', DEX_BCCF_DEFAULT_form_structure ) ) );

        $org_booking = $wpdb->get_results( "SELECT buffered_date FROM ".DEX_BCCF_TABLE_NAME." WHERE id=" . $event->reference );
        $params = unserialize( $org_booking[0]->buffered_date );
        unset( $params['startdate'] );
        unset( $params['enddate'] );
    } else {
        $params['description'] = $event->description;

    }

    if ( count( $_POST ) > 0 ) {

        $datatime_s = $_POST['datatime_s'];
        $datatime_e = $_POST['datatime_e'];
        $dfoption   = dex_bccf_get_option( 'calendar_dateformat', DEX_BCCF_DEFAULT_CALENDAR_DATEFORMAT );

        if ( $dfoption == '0' ) {
            $format = 'm/d/Y ' . $format;
        } else {
            $format = 'd/m/Y ' . $format;
        }

        if ( dex_bccf_get_option( 'calendar_dateformat', DEX_BCCF_DEFAULT_CALENDAR_DATEFORMAT ) == '0' ) {
            $format_d = 'm/d/Y ';
        } else {
            $format_d = 'd/m/Y ';
        }

        $params_new = [
            'startdate' => date( $format_d, strtotime( $_POST['datatime_s'] ) ),
            'enddate'   => date( $format_d, strtotime( $_POST['datatime_e'] ) )
        ];

        foreach ( $params as $item => $value ) {
            $params_new[$item] = $_POST[$item];
        }

        $description = __( 'Form: ', 'sc-res' ) . dex_bccf_get_option( 'uname', '' ) . '<br />' . __( 'Date From-To: ', 'sc-res' ) . date( $format, strtotime( $datatime_s ) ) . '-' . date( $format, strtotime( $datatime_e ) ) . '<br />';

        foreach ( $params_new as $item => $value) {
            if ( $value != '' && $item != 'startdate' && $item != 'enddate'
                && $item != 'days'
                && $item != 'initialpayment'
                && $item != 'finalpayment'
                ) {
                $name         = dex_bccf_get_field_name( $item, $form_data[0] );
                $description  .= $name . ': ' . $value . '<br />';
            }
        }

        if ( $event->reference == '' ) {
            $description = $_POST['description'];
        }

        $data1 = [
            'datatime_s'  => $datatime_s,
            'datatime_e'  => $datatime_e,
            'title'       => $_POST['title'],
            'description' => $description
        ];

        $data2 = [
            'booked_time_s'             => $datatime_s,
            'booked_time_e'             => $datatime_e,
            'booked_time_unformatted_s' => $_POST['datatime_s'],
            'booked_time_unformatted_e' => $_POST['datatime_e'],
            'buffered_date'             => serialize( $params_new )
        ];

        $wpdb->update ( TDE_BCCFCALENDAR_DATA_TABLE, $data1, [ 'id' => $_GET['edit'] ] );

        if ( $event->reference != '' ) {
            $wpdb->update ( DEX_BCCF_TABLE_NAME, $data2, [ 'id' => $event->reference ] );
        }

        echo '<script type="text/javascript">  document.location = "admin.php?page=dex_bccf&cal=' . $_GET['cal'] . '&list=1&message=Item updated&r="+Math.random();</script>';

        exit;
    }

    $date_s = date( 'Y-m-d', strtotime( $event->datatime_s ) );
    $date_e = date( 'Y-m-d', strtotime( $event->datatime_e ) );

?>
<div class="wrap reservations reservation-submissions-edit">
    <h1><?php _e( 'Edit this Submissions', 'sc-res' ); ?></h1>
    <hr />
    <section>
        <h2><?php _e( 'Submission Data', 'sc-res' ); ?></h2>
        <form method="post" name="dexeditfrm" action="">
            <table class="form-table">
                <tbody>
                    <tr valign="top">
                        <th scope="row"><?php _e( 'Start Date', 'sc-res' ); ?></th>
                        <td><input type="text" name="datatime_s" id="datatime_s" size="40" value="<?php echo $date_s; ?>" /></td>
                    </tr>
                    <tr valign="top">
                        <th scope="row"><?php _e( 'End Date', 'sc-res' ); ?></th>
                        <td><input type="text" name="datatime_e" id="datatime_e" size="40" value="<?php echo $date_e; ?>" /></td>
                    </tr>
                    <tr valign="top">
                        <th scope="row"><?php _e( 'Title', 'sc-res' ); ?></th>
                        <td><input type="text" name="title" size="40" value="<?php echo esc_attr( $event->title ); ?>" /></td>
                    </tr>
                    <?php foreach ( $params as $item => $value ) { ?>
                    <tr valign="top">
                        <th scope="row">
                        <?php
                            $name = dex_bccf_get_field_name( $item,$form_data[0] );
                            echo $name;
                        ?>
                        </th>
                        <td>
                            <?php if ( strpos( $value,"\n" ) > 0 || strlen( $value ) > 80 ) { ?>
                            <textarea cols="85" name="<?php echo $item; ?>"><?php echo ( $value ); ?></textarea>
                            <?php } else { ?>
                            <input type="text" name="<?php echo $item; ?>" value="<?php echo esc_attr( $value ); ?>" />
                            <?php } ?>
                        </td>
                    </tr>
                    <?php } ?>
                </tbody>
            </table>
            <p class="submit">
                <input class="button button-primary" type="submit" name="submit" id="submit" class="button-primary" value="<?php _e( 'Save', 'sc-res' ); ?>"  />
                <input type="button" value="<?php _e( 'Cancel', 'sc-res' ); ?>" onclick="javascript:gobackapp();">
            </p>
        </form>
    </section>
</div><!-- End .wrap -->
<script type="text/javascript">
    var $j = jQuery.noConflict();

    $j( function() {
        $j( '#datatime_s' ).datepicker({
            dateFormat: 'yy-mm-dd'
        });
        $j( '#datatime_e' ).datepicker({
            dateFormat: 'yy-mm-dd'
        });
    });

    function gobackapp() {
        document.location = 'admin.php?page=dex_bccf&cal=<?php echo $_GET["cal"]; ?>&list=1&r='+Math.random();
    }
</script>
<?php } else { ?>
  <br />
    <p><?php _e( 'The current user logged in doesn\'t have enough permissions to edit this item. Please log in as administrator to get full access.', 'sc_res' ); ?></p>
<?php }