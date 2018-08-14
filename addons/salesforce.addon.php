<?php
/**
 * SalesForce addon.
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

require_once dirname( __FILE__ ) . '/sc-res-base-addon.php';

/**
 * Addon attributes and methods.
 *
 * @since  1.0.0
 * @access public
 */
if ( ! class_exists( 'DEXBCCF_SalesForce' ) ) {
    class DEXBCCF_SalesForce extends DEXBCCF_BaseAddon {

		protected $addonID = 'addon-salesforce-20150311';
		protected $name    = 'SalesForce';
		protected $description;

		private $form_salesforce_table = 'bccf_salesforce';
		private $salesforce_url        = 'https://www.salesforce.com/servlet/servlet.WebToLead?encoding=UTF-8';
		private $lead_attributes       = [
			'salutation' 	=> 'Salutation',
			'title' 		=> 'Title',
			'first_name'	=> 'First Name',
			'last_name'		=> 'Last Name',
			'email'			=> 'Email',
			'phone'			=> 'Phone',
			'mobile'		=> 'Mobile',
			'fax'			=> 'Fax',
			'street'		=> 'Street',
			'city'			=> 'City',
			'state'			=> 'State/Province (text only)',
			'state_code'	=> 'State Code',
			'country'		=> 'Country (text only)',
			'country_code'	=> 'Country Code',
			'zip'			=> 'ZIP',
			'URL'			=> 'URL',
			'description'	=> 'Description',
			'company'		=> 'Company',
			'industry'		=> 'Industry',
			'revenue'		=> 'Annual Revenue',
			'employees'		=> 'Employees',
			'lead_source'	=> 'Lead Source',
			'rating'		=> 'Rating',
			'Campaign_ID'	=> 'Campaign ID',
			'member_status'	=> 'Campaign Member Status',
			'emailOptOut'	=> 'Email Opt Out',
			'faxOptOut'		=> 'Fax Opt Out',
			'doNotCall'		=> 'Do Not Call'
		];

		/**
		 * Constructor method.
		 *
		 * @since  1.0.0
		 * @access public
		 * @return self
		 */
        public function __construct() {

			$this->description = __( 'The add-on allows create SalesForce leads with the submitted information', 'sc-res' );

            // Check if the plugin is active
			if ( ! $this->addon_is_active() ) {
				return;
			}

			// Create database tables
			$this->create_tables();

			// Load resources
            add_action( 'admin_enqueue_scripts', [ $this, 'enqueue_scripts' ], 10 );

			// Export the lead
			add_action( 'dexbccf_process_data', [ $this, 'export_lead' ] );

		}

		/**
		 * Addon form settings
		 *
		 * @since  1.0.0
 		 * @access public
		 * @param  int $form_id The ID of the form.
		 * @return mixed The output of the addon.
		 */
		public function get_addon_form_settings( $form_id ) {

			global $wpdb;

			// Insertion in database/
			if ( isset( $_REQUEST['dexbccf_salesforce_oid'] ) ) {

				$data = [];

				foreach( $_REQUEST['dexbccf_salesforce_attr'] as $key => $attr ) {

					$attr  = trim( $attr );
					$value = trim( $_REQUEST['dexbccf_salesforce_field'][$key] );

					if ( ! empty( $attr ) && ! empty( $value ) ) {
						$data[$attr] = $value;
					}

				}

				$wpdb->delete( $wpdb->prefix.$this->form_salesforce_table, ['formid' => $form_id], ['%d'] );
				$wpdb->insert( 	$wpdb->prefix.$this->form_salesforce_table,
					[
						'formid'     => $form_id,
						'oid'	     => trim( $_REQUEST['dexbccf_salesforce_oid'] ),
						'debug'      => ( isset( $_REQUEST['dexbccf_salesforce_debug'] ) ) ? 1 : 0,
						'debugemail' => trim( $_REQUEST['dexbccf_salesforce_debug_email'] ),
						'data'	     => serialize( $data )
					],
					[ '%d', '%s', '%d', '%s', '%s' ]
				);
			}

			$oid  	    = '';
			$debug 	    = 0;
			$debugemail = '';
			$data    	= [];
			$row 	    = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM ".$wpdb->prefix.$this->form_salesforce_table." WHERE formid=%d", $form_id ) );

			if ( $row ) {
				$oid 	    = $row->oid;
				$debug 	    = $row->debug;
				$debugemail = $row->debugemail;

				if ( ( $tmp = @unserialize( $row->data ) ) != false ) {
					$data = $tmp;
				}
			} ?>
			<section>
				<h3<?php print $this->name; ?></h3>
				<table cellspacing="0">
					<tbody>
						<tr>
							<th style="white-space:nowrap;width:200px;"><?php _e( 'Organizational ID', 'sc-res' );?>:</th>
							<td><input type="text" name="dexbccf_salesforce_oid" value="<?php echo esc_attr( $oid ); ?>" ></td>
						</tr>
						<tr>
							<th style="white-space:nowrap;width:200px;"><?php _e( 'Enabling debug', 'sc-res' );?>:</th>
							<td><input type="checkbox" name="dexbccf_salesforce_debug" <?php echo ( ( $debug ) ? 'CHECKED' : '' ); ?> ></td>
						</tr>
						<tr>
							<th style="white-space:nowrap;width:200px;"><?php _e( 'Debug email', 'sc-res' );?>:</th>
							<td><input type="text" name="dexbccf_salesforce_debug_email" value="<?php echo esc_attr( $debugemail ); ?>" ></td>
						</tr>
						<tr><td colspan="2"><strong><?php _e( 'Lead Attributes', 'sc-res' );?>:</strong></td></tr>
						<tr>
							<td colspan="2">
								<table>
									<?php
									$c = 1;
									$keys_arr = array_keys( $this->lead_attributes );
									foreach ( $data as $attr => $value ) {

										print '<tr><td style="position:relative;width:200px;">';

										$str = 	'<input type="text" name="dexbccf_salesforce_attr[' . $c . ']" value="' . esc_attr( $attr ) . '" placeholder="fieldname#" class="cpcff-salesforce-attribute" />';
										$str .= '<select class="cpcff-autocomplete" style="width:100%;"><option value=""></option>';
										foreach ( $this->lead_attributes as $lead_attr_key => $lead_attr_title ) {
											$str .= '<option value="'.esc_attr( $lead_attr_key ) . '" ' . ( ( $lead_attr_key == $attr ) ? 'SELECTED' : '' ) . '>' . $lead_attr_title.'</option>';
										}
										$str .= '</select>';

										print $str;
										print '</td><td><input type="text" name="dexbccf_salesforce_field[' . $c . ']" value="' . esc_attr( $value ) . '"><input type="button" value="[ X ]" onclick="dexbccf_salesforce_removeAttr( this );" /></td></tr>';
										$c++;
									} ?>
									<tr>
										<td colspan="2">
											<input type="button" value="<?php esc_attr_e( 'Add attribute', 'sc-res' );?>" onclick="dexbccf_salesforce_addAttr( this );" />
										</td>
									</tr>
								</table>
							</td>
						</tr>
					</tbody>
				</table>
				<script>
					var dexbccf_salesforce_attr_counter = <?php print $c; ?>;
					function dexbccf_salesforce_addAttr( e ) {
						try {
							var $   = jQuery,
								str = $( '<tr><td style="width:200px;position:relative;"><select name="dexbccf_salesforce_attr[' + dexbccf_salesforce_attr_counter + ']" style="width:100%;" class="cpcff-autocomplete"><option value=""></option><?php foreach ( $this->lead_attributes as $key => $value ) { print '<option value="' . esc_attr( $key ) . '">' . $value . '</option>'; } ?></select></td><td><input type="text" name="dexbccf_salesforce_field[' + dexbccf_salesforce_attr_counter + ']" value="" placeholder="fieldname#" ><input type="button" value="[ X ]" onclick="dexbccf_salesforce_removeAttr( this );" /></td></tr>' );

							$( e ).closest( 'tr' )
								  .before( str );

							str.find( '.cpcff-autocomplete' ).cpcffautocomplete();

							dexbccf_salesforce_attr_counter++;
						}
						catch( err ){}
					}

					function dexbccf_salesforce_removeAttr( e ) {
						try
						{
							var $   = jQuery;
							$( e ).closest( 'tr' ).remove();
						}
						catch( err ){}
					}

				</script>
			</section>
			<?php
		}

		/**
		 * Creates the database tables.
		 *
		 * @since  1.0.0
 		 * @access private
		 * @return void
		 */
        private function create_tables() {

			global $wpdb;

			$sql = "CREATE TABLE IF NOT EXISTS " . $wpdb->prefix.$this->form_salesforce_table . " (
					id mediumint(9) NOT NULL AUTO_INCREMENT,
					oid VARCHAR(250) DEFAULT '' NOT NULL,
					formid INT NOT NULL,
					data text,
					debug INT DEFAULT 0 NOT NULL,
					debugemail VARCHAR(250) DEFAULT '' NOT NULL,
					UNIQUE KEY id (id)
				);";

			$wpdb->query( $sql );

		}

		/**
         * Enqueue all resources: CSS and JS files, required by the addon.
		 *
		 * @since  1.0.0
		 * @access public
		 * @return void
         */
        public function enqueue_scripts() {

			wp_enqueue_style( 'dexbccf_salesforce_addon_css', plugins_url( '/salesforce.addon/css/styles.min.css', __FILE__ ) );
			wp_enqueue_script( 'dexbccf_salesforce_addon_js', plugins_url( '/salesforce.addon/js/scripts.min.js',  __FILE__ ), [ 'jquery', 'jquery-ui-autocomplete' ] );

        }

		/**
         * Export the leads to the SalesForce account.
		 *
		 * @since  1.0.0
		 * @access public
		 * @return void
         */
        public function	export_lead( $params ) {

			global $wpdb, $wp_version;

			$form_id = @intval( $_REQUEST['dex_item'] );

			if ( $form_id ) {

				$row = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM ".$wpdb->prefix.$this->form_salesforce_table." WHERE formid=%d", $form_id ) );

				if ( $row && !empty( $row->oid ) ) {

					$post = array( 'oid' => $row->oid );

					if ( $row->debug ) {
						$post['debug'] = 1;
					}

					if ( ! empty( $row->debugemail ) ) {
						$post['debugEmail'] = $row->debugemail;
					}

					$attrs = unserialize( $row->data );

					foreach( $attrs as $key => $value ) {
						if ( isset( $params[$value] ) ) {
							$post[$key] = $params[$value];
						} else {
							$post[$key] = $value;
						}
					}

					// Remove php style arrays for array values [1].
					$body = preg_replace('/%5B[0-9]+%5D/simU', '', http_build_query( $post ) );
					$args = [
						'body' 		=> $body,
						'headers' 	=> [
							'Content-Type' => 'application/x-www-form-urlencoded',
							'user-agent'   => __( 'WordPress-to-Lead for booking-calendar-contact-form plugin - WordPress/', 'sc-res' ) . $wp_version . '; ' . get_bloginfo( 'url' ),
						],
						'timeout'   => 45,
						'sslverify'	=> false,
					];

					$result = wp_remote_post( $this->salesforce_url, $args );

				}
			}
		}

    }

    // Main add-on code.
    $dexbccf_salesforce_obj = new DEXBCCF_SalesForce();

	// Add addon object to the objects list.
	global $dexbccf_addons_objs_list;
	$dexbccf_addons_objs_list[$dexbccf_salesforce_obj->get_addon_id()] = $dexbccf_salesforce_obj;

}