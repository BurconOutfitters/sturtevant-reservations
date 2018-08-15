<?php
/**
 * The main admin page for reservations.
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

$current_user = wp_get_current_user();

global $wpdb, $dexbccf_addons_active_list, $dexbccf_addons_objs_list;

$message = "";

if ( isset( $_GET['b'] ) && $_GET['b'] == 1 ) {

	// Save the option for active addons.
    delete_option( 'dexbccf_addons_active_list' );

	if ( ! empty( $_GET['dexbccf_addons_active_list'] ) && is_array( $_GET['dexbccf_addons_active_list'] ) ) {
		update_option( 'dexbccf_addons_active_list', $_GET['dexbccf_addons_active_list'] );
	}

	// Get the list of active addons.
    $dexbccf_addons_active_list = get_option( 'dexbccf_addons_active_list', [] );

}

if ( isset( $_GET['a'] ) && $_GET['a'] == '1' ) {

    $sql .= 'INSERT INTO `'.$wpdb->prefix ."bccf_reservation_calendars".'` (`'.TDE_BCCFCONFIG_TITLE.'`,`'.TDE_BCCFCONFIG_USER.'`,`'.TDE_BCCFCONFIG_PASS.'`,`'.TDE_BCCFCONFIG_LANG.'`,`'.TDE_BCCFCONFIG_CPAGES.'`,`'.TDE_BCCFCONFIG_MSG.'`,`'.TDE_BCCFCALDELETED_FIELD.'`,calendar_mode) '.
            ' VALUES("","' . $_GET["name"] . '","","ENG","1","Please, select your reservation.","0","true");';

    $wpdb->query( $sql );

    $results = $wpdb->get_results( 'SELECT `'.TDE_BCCFCONFIG_ID.'` FROM `'.DEX_BCCF_CONFIG_TABLE_NAME.'` ORDER BY `'.TDE_BCCFCONFIG_ID.'` DESC LIMIT 0,1' );

    $wpdb->query( 'UPDATE `'.DEX_BCCF_CONFIG_TABLE_NAME.'` SET `'.TDE_BCCFCONFIG_TITLE.'`="cal' . $results[0]->id . '" WHERE `'.TDE_BCCFCONFIG_ID.'`=' . $results[0]->id );

    $message = __( 'New form has been added.', 'sc-res' );

} elseif ( isset( $_GET['u'] ) && $_GET['u'] != '' ) {

    $wpdb->query( 'UPDATE `'.DEX_BCCF_CONFIG_TABLE_NAME.'` SET conwer=' . intval( $_GET['owner'] ) . ',`'.TDE_BCCFCALDELETED_FIELD.'`=' . intval( $_GET['public'] ) . ',`'.TDE_BCCFCONFIG_USER.'`="' . $_GET['name'] . '" WHERE `'.TDE_BCCFCONFIG_ID.'`=' . intval( $_GET['u'] ) );

    $message =  __( 'The form has been updated.', 'sc-res' );

} elseif ( isset( $_GET['d'] ) && $_GET['d'] != '' ) {

    $wpdb->query( 'DELETE FROM `'.DEX_BCCF_CONFIG_TABLE_NAME.'` WHERE `'.TDE_BCCFCONFIG_ID.'`=' . intval( $_GET['d'] ) );
    $message =  __( 'The form has been deleted.', 'sc-res' );

} elseif ( isset( $_GET['c'] ) && $_GET['c'] != '' ) {

    $myrows = $wpdb->get_row( "SELECT * FROM ".DEX_BCCF_CONFIG_TABLE_NAME." WHERE `".TDE_BCCFCONFIG_ID."`=" . intval( $_GET['c'] ), ARRAY_A );
    unset( $myrows[TDE_BCCFCONFIG_ID] );
    $myrows[TDE_BCCFCONFIG_USER] = __( 'Duplicated: ', 'sc-res' ) . $myrows[TDE_BCCFCONFIG_USER];
    $wpdb->insert( DEX_BCCF_CONFIG_TABLE_NAME, $myrows );

    $message = __( 'The form has been duplicated', 'sc-res' );

} elseif ( isset( $_GET['ac'] ) && $_GET['ac'] == 'st' ) {

    update_option( 'CP_BCCF_LOAD_SCRIPTS', ( $_GET['scr'] == '1' ? '0' : '1' ) );

    if ( $_GET['chs'] != '' ) {

        $target_charset = esc_sql($_GET['chs']);
        $tables         = [ $wpdb->prefix . DEX_BCCF_TABLE_NAME_NO_PREFIX, $wpdb->prefix . DEX_BCCF_CALENDARS_TABLE_NAME_NO_PREFIX, $wpdb->prefix . DEX_BCCF_CONFIG_TABLE_NAME_NO_PREFIX ];

        foreach ( $tables as $tab ) {

            $myrows = $wpdb->get_results( "DESCRIBE {$tab}" );

            foreach ( $myrows as $item ) {

	            $name = $item->Field;
                $type = $item->Type;

		        if ( preg_match("/^varchar\((\d+)\)$/i", $type, $mat ) || ! strcasecmp( $type, 'CHAR' ) || !strcasecmp( $type, 'TEXT' ) || !strcasecmp( $type, 'MEDIUMTEXT' ) ) {
	                $wpdb->query( "ALTER TABLE {$tab} CHANGE {$name} {$name} {$type} COLLATE {$target_charset}" );
	            }
	        }
        }
    }

    $message = __( 'Troubleshoot settings updated', 'sc-res' );

}

if ( $message ) {
    echo sprintf( '<div id="setting-error-settings_updated" class="notice notice-success is-dismissible"><p><strong>%1s</strong></p></div>', $message );
} ?>
<div class="wrap reservations reservations-main">
    <h1><?php _e( 'Camp Reservations', 'sc-res' ); ?></h1>
    <p class="description"><?php _e( 'Manage camp reservation forms and form submissions.', 'sc-res' ); ?></p>
    <hr />

    <script type="text/javascript">
    function cp_activateAddons() {
        var dexbccf_addons = document.getElementsByName("dexbccf_addons"),
            dexbccf_addons_active_list = [];
        for ( var i = 0, h = dexbccf_addons.length; i < h; i++ ) {
            if ( dexbccf_addons[ i ].checked ) dexbccf_addons_active_list.push( 'dexbccf_addons_active_list[]='+encodeURIComponent( dexbccf_addons[ i ].value ) );
        }
        document.location = 'options-general.php?page=dex_bccf&b=1&r='+Math.random()+( ( dexbccf_addons_active_list.length ) ? '&'+dexbccf_addons_active_list.join( '&' ) : '' )+'&_dexbccf_nonce=<?php echo wp_create_nonce( 'session_id_'.session_id() ); ?>#addons-section';
    }

    function cp_addItem() {
        var calname = document.getElementById( 'cp_itemname' ).value;
        document.location = 'admin.php?page=dex_bccf&a=1&r=' + Math.random() + '&name=' + encodeURIComponent( calname );
    }

    function cp_updateItem(id) {
        var calname = document.getElementById( 'calname_' + id ).value;
        var owner = document.getElementById( "calowner_" + id ).options[document.getElementById( 'calowner_' + id ).options.selectedIndex].value;
        if ( owner == '' )
            owner = 0;
        var is_public = ( document.getElementById( 'calpublic_' + id ).checked ? '0' : '1' );
        document.location = 'admin.php?page=dex_bccf&u=' + id + '&r='+Math.random() + '&public=' + is_public + '&owner=' + owner + '&name='+encodeURIComponent( calname );
    }

    function cp_cloneItem(id) {
        document.location = 'admin.php?page=dex_bccf&c=' + id + '&r='+Math.random();
    }

    function cp_manageSettings(id) {
        document.location = 'admin.php?page=dex_bccf&cal=' + id + '&r='+Math.random();
    }

    function cp_BookingsList(id) {
        document.location = 'admin.php?page=dex_bccf&cal=' + id + '&list=1&r='+Math.random();
    }

    function cp_deleteItem(id) {
        if ( confirm( '<?php _e( 'Are you sure that you want to delete this item?', 'sc-res' ); ?>' ) ) {
            document.location = 'admin.php?page=dex_bccf&d=' + id + '&r='+Math.random();
        }
    }

    function cp_updateConfig() {
        if ( confirm( '<?php _e( 'Are you sure that you want to update these settings?', 'sc-res' ); ?>' ) ) {
            var scr = document.getElementById( 'ccscriptload' ).value;
            var chs = document.getElementById( 'cccharsets' ).value;
            document.location = 'admin.php?page=dex_bccf&ac=st&scr=' + scr + '&chs=' + chs + '&r=' + Math.random();
        }
    }
    </script>
    <h2><?php _e( 'Forms & Submissions', 'sc-res' ); ?></h2>
    <section class="reservations-table">
        <div class="reservations-table-head">
            <span><?php _e( 'ID', 'sc-res' ); ?></span>
            <span><?php _e( 'Form Name', 'sc-res' ); ?></span>
            <span><?php _e( 'Creator', 'sc-res' ); ?></span>
            <span><?php _e( 'Public', 'sc-res' ); ?></span>
            <span><?php _e( 'Manage Forms', 'sc-res' ); ?></span>
            <span><?php _e( 'Form Shortcode', 'sc-res' ); ?></span>
        </div>
        <?php
        $users  = $wpdb->get_results( "SELECT user_login,ID FROM " . $wpdb->users . " ORDER BY ID DESC" );
        $myrows = $wpdb->get_results( "SELECT * FROM " . $wpdb->prefix . "bccf_reservation_calendars ORDER BY `ID` ASC" );

        foreach ( $myrows as $item ) :
            if ( cp_bccf_is_administrator() || $current_user->ID == $item->conwer ) : ?>
            <div class="reservations-table-row">
                <span><?php echo $item->id; ?></span>
                <span><input type="text" <?php if ( ! cp_bccf_is_administrator() ) { echo ' readonly '; } ?>name="calname_<?php echo $item->id; ?>" id="calname_<?php echo $item->id; ?>" value="<?php echo esc_attr( $item->uname ); ?>" /></span>

                <?php if ( cp_bccf_is_administrator() ) { ?>
                <span>
                    <select name="calowner_<?php echo $item->id; ?>" id="calowner_<?php echo $item->id; ?>">
                        <option value="0"<?php if ( ! $item->conwer ) { echo ' selected'; } ?>></option>
                        <?php foreach ( $users as $user ) { ?>
                        <option value="<?php echo $user->ID; ?>"<?php if ( $user->ID . '' == $item->conwer ) echo ' selected'; ?>>
                            <?php echo $user->user_login; ?>
                        </option>
                        <?php  } ?>
                    </select>
                </span>
                <?php } else { ?>
                <span>
                    <?php echo $current_user->user_login; ?>
                </span>
                <?php } ?>
                <span>
                    <?php if ( cp_bccf_is_administrator() ) { ?>
                    <input type="checkbox" name="calpublic_<?php echo $item->id; ?>" id="calpublic_<?php echo $item->id; ?>" value="1" <?php if ( ! $item->caldeleted ) echo ' checked '; ?> />
                    <?php } else { ?>
                    <?php if ( ! $item->caldeleted ) _e( 'Yes', 'sc-res' ); else _e( 'No', 'sc-res' ); ?>
                    <?php } ?>
                </span>
                <span>
                    <?php if ( cp_bccf_is_administrator() ) { ?>
                    <input class="button" type="button" name="calupdate_<?php echo $item->id; ?>" value="<?php _e( 'Update', 'sc-res' ); ?>" onclick="cp_updateItem(<?php echo $item->id; ?>);" />
                    <?php } ?>
                    <input class="button" type="button" name="calmanage_<?php echo $item->id; ?>" value="<?php _e( 'Fields & Settings ', 'sc-res' ); ?>" onclick="cp_manageSettings(<?php echo $item->id; ?>);" />
                    <input class="button" type="button" name="calbookings_<?php echo $item->id; ?>" value="<?php _e( 'Submissions', 'sc-res' ); ?>" onclick="cp_BookingsList(<?php echo $item->id; ?>);" />
                    <input class="button" type="button" name="calclone_<?php echo $item->id; ?>" value="<?php _e( 'Duplicate', 'sc-res' ); ?>" onclick="cp_cloneItem(<?php echo $item->id; ?>);" />
                    <?php if ( cp_bccf_is_administrator() ) { ?>
                    <input class="button" type="button" name="caldelete_<?php echo $item->id; ?>" value="<?php _e( 'Delete', 'sc-res' ); ?>" onclick="cp_deleteItem(<?php echo $item->id; ?>);" />
                    <?php } ?>
                </span>
                <span>[CP_BCCF_FORM calendar="<?php echo $item->id; ?>"]</span>
            </div>
            <?php endif; endforeach; ?>
    </section>
    <?php
    /**
     * The following sections are only available/visible to site admins.
     *
     * @since 1.0.0
     */
    if ( cp_bccf_is_administrator() ) { ?>
    <section>
        <h2><?php _e( 'New Form', 'sc-res' ); ?></h2>
        <form name="additem">
            <p>
                <label for="cp_itemname"><?php _e( 'Name:', 'sc-res' ); ?></label><br />
                <input type="text" name="cp_itemname" id="cp_itemname" value="" /> <input class="button" type="button" onclick="cp_addItem();" name="gobtn" value="<?php _e( 'Add', 'sc-res' ); ?>" />
            </p>
        </form>
    </section>
    <section>
        <h2><a name="addons-section"></a><?php _e( 'Additional Functionality', 'sc-res' ); ?></h2>
        <?php foreach( $dexbccf_addons_objs_list as $key => $obj ) {
            print '<div><label for="' . $key . '" style="font-weight:bold;"><input type="checkbox" id="' . $key . '" name="dexbccf_addons" value="' . $key . '" ' . ( ( $obj->addon_is_active() ) ? 'CHECKED' : '' ) . '>' . $obj->get_addon_name() . '</label> <div style="font-style:italic;padding-left:20px;">' . $obj->get_addon_description() . '</div></div>';
        } ?>
        <p><input class="button" type="button" onclick="cp_activateAddons();" name="activateAddon" value="<?php esc_attr_e( 'Activate/Deactivate', 'sc-res' ); ?>" /></p>
        <?php if ( count( $dexbccf_addons_active_list ) ) {
            foreach ( $dexbccf_addons_active_list as $addon_id ) {
                if ( isset( $dexbccf_addons_objs_list[ $addon_id ] ) ) {
                    print $dexbccf_addons_objs_list[ $addon_id ]->get_addon_settings();
                }
            }
        } ?>
    </section>
    <section>
        <h2><?php _e( 'Troubleshooting', 'sc-res' ); ?></h2>
        <?php echo sprintf(
            '<p><strong>%1s</strong> %2s</p>',
            esc_html__( 'Important:', 'sc-res' ),
            esc_html__( 'Use this area only if you are experiencing conflicts with third party plugins, with the theme scripts or with the character encoding.', 'sc-res' )
        ); ?>
        <form name="updatesettings">
            <label for="ccscriptload"><?php _e( 'Script load method', 'sc-res' ); ?></label><br />
            <select id="ccscriptload" name="ccscriptload">
                <option value="0" <?php if ( get_option( 'CP_BCCF_LOAD_SCRIPTS', '1' ) == '1' ) { echo 'selected'; } ?>><?php _e( 'Classic (Recommended)', 'sc-res' ); ?></option>
                <option value="1" <?php if ( get_option( 'CP_BCCF_LOAD_SCRIPTS', '1' ) != '1' ) { echo 'selected'; } ?>><?php _e( 'Direct', 'sc-res' ); ?></option>
            </select>
            </p>
            <p><em><?php _e( '* Change the script load method if the form doesn\'t appear in the public website.', 'sc-res' ); ?></em></p>
            <label for="cccharsets"><?php _e( 'Character encoding', 'sc-res' ); ?></label><br />
            <select id="cccharsets" name="cccharsets">
                <option value=""><?php _e( 'Keep current charset (Recommended)', 'sc-res' ); ?></option>
                <option value="utf8_general_ci">UTF-8 <?php _e( '(try this first)', 'sc-res' ); ?></option>
                <option value="latin1_swedish_ci">latin1_swedish_ci</option>
            </select>
            </p>
            <p><em><?php _e( '* Update the charset if you are getting problems displaying special/non-latin characters. After updated you need to edit the special characters again.', 'sc-res' ); ?></em></p>
            <p><input class="button" type="button" onclick="cp_updateConfig();" name="gobtn" value="<?php _e( 'Update', 'sc-res' ); ?>" /></p>
        </form>
    </section>
<?php } ?>
</div>