<?php
/**
 * WebHook addon.
 *
 * @package    Sturtevant_Reservations
 * @subpackage Addons
 *
 * @since      1.0.0
 * @author     Greg Sweet <greg@ccdzine.com>
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

require_once dirname( __FILE__ ).'/base.addon.php';

/**
 * Addon attributes and methods.
 *
 * @since  1.0.0
 * @access public
 */
if ( ! class_exists( 'DEXBCCF_WebHook' ) ) {
    class DEXBCCF_WebHook extends DEXBCCF_BaseAddon {

		protected $addonID = "addon-webhook-20150403";
		protected $name    = "WebHook";
		protected $description;

		/**
		 * Constructor method.
		 *
		 * @since  1.0.0
		 * @access public
		 * @return self
		 */
        function __construct() {

			$this->description = __( 'The add-on allows put the submitted information to a webhook URL, and integrate the forms with the Zapier service', 'sc-res' );

            // Check if the plugin is active
			if ( ! $this->addon_is_active() ) {
				return;
			}

			// Create database tables
			$this->create_tables();

			// Export the lead
			add_action( 'dexbccf_process_data', [ $this, 'put_data' ] );
        }

		public function get_addon_form_settings( $form_id ) {

			global $wpdb;

			// Insertion in database
			if ( isset( $_REQUEST['dexbccf_webhook'] ) ) {

				$wpdb->delete( $wpdb->prefix . $this->form_webhook_table, [ 'formid' => $form_id ], [ '%d' ] );

				if ( isset( $_REQUEST['dexbccf_webhook_url'] ) ) {

					foreach ( $_REQUEST['dexbccf_webhook_url'] as $url ) {
						$attr = trim( $attr );
						$url  = trim( $url );
						if ( ! empty( $url ) ) {
							$wpdb->insert(
								$wpdb->prefix . $this->form_webhook_table,
								[
									'formid' => $form_id,
									'url'	 => $url
								],
								[ '%d', '%s']
							);
						}
					}

				}

			}

			$rows = $wpdb->get_results( $wpdb->prepare( "SELECT url FROM ".$wpdb->prefix.$this->form_webhook_table." WHERE formid=%d", $form_id ) ); ?>
			<section>
				<h3><?php print $this->name; ?></h3>
				<table cellspacing="0" style="width:100%;">
					<tbody>
						<?php
							foreach( $rows as $row ) {
								print '
									<tr>
										<td style="white-space:nowrap;width:200px;">WebHook URL:</td>
										<td><input type="text" name="dexbccf_webhook_url[]" value="' . esc_attr( $row->url ) . '" > <input type="button" value="[ X ]" onclick="dexbccf_webhook_removeURL( this );" /></td>
									</tr>
								';
							}
						?>
						<tr>
							<td style="white-space:nowrap;width:200px;"><?php _e( 'WebHook URL', 'sc-res' );?>:</td>
							<td>
								<input type="text" name="dexbccf_webhook_url[]" value="" >
								<input type="button" value="[ X ]" onclick="dexbccf_webhook_removeURL( this );" />
							</td>
						</tr>
						<tr>
							<td colspan="2">
								<input type="button" value="<?php esc_attr_e( 'Add new URL', 'sc-res' );?>" onclick="dexbccf_webhook_addURL( this );" />
							</td>
						</tr>
					</tbody>
				</table>
				<input type="hidden" name="dexbccf_webhook" value="1" />
				<script>
					function dexbccf_webhook_addURL( e ) {
						try {
							var $ = jQuery;
							e = $( e );
							e.closest( 'tr' )
							 .before(
								'<tr><td style="white-space:nowrap;width:200px;">WebHook URL:</td><td><input name="dexbccf_webhook_url[]" value=""> <input type="button" value="[ X ]" onclick="dexbccf_webhook_removeURL( this );" /></td></tr>'
							 );
						}
						catch( err ){}
					}

					function dexbccf_webhook_removeURL( e ) {
						try {
							var $ = jQuery;
							$( e ).closest( 'tr' ).remove();
						}
						catch( err ){}
					}
				</script>
			</section>
			<?php
		}

		private $form_webhook_table = 'bccf_form_webhook';

		/**
         * Creates the database tables.
		 *
		 * @since  1.0.0
		 * @access private
		 * @return void
         */
        private function create_tables() {

			global $wpdb;

			$sql = "CREATE TABLE IF NOT EXISTS " . $wpdb->prefix.$this->form_webhook_table . " (
					id mediumint(9) NOT NULL AUTO_INCREMENT,
					formid INT NOT NULL,
					url VARCHAR(250) DEFAULT '' NOT NULL,
					UNIQUE KEY id (id)
				);";

			$wpdb->query( $sql );

		}

		/**
         * Put data to webhooks URLs.
		 *
		 * @since  1.0.0
		 * @access public
		 * @return void
         */
        public function	put_data( $params ) {

			global $wpdb;

			$form_id = @intval( $_REQUEST['dex_item'] );

			if ( $form_id ) {
				$rows = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM " . $wpdb->prefix . $this->form_webhook_table . " WHERE formid=%d", $form_id ) );

				foreach( $rows as $row ) {
					$args = [
						'body' 		=> http_build_query( $params ),
						'timeout' 	=> 45,
						'sslverify'	=> false,
					];
					$result = wp_remote_post( $row->url, $args );
				}
			}

		}

    }

    // Main add-on code
    $dexbccf_webhook_obj = new DEXBCCF_WebHook();

	// Add addon object to the objects list
	global $dexbccf_addons_objs_list;
	$dexbccf_addons_objs_list[$dexbccf_webhook_obj->get_addon_id()] = $dexbccf_webhook_obj;

}