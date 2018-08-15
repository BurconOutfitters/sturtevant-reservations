<?php
/*
Documentation: https://goo.gl/w3kKoH
*/
require_once dirname( __FILE__ ) . '/sc-res-base-addon.php';

if( !class_exists( 'DEXBCCF_iDealMollie' ) )
{
    class DEXBCCF_iDealMollie extends DEXBCCF_BaseAddon
    {

        /************* ADDON SYSTEM - ATTRIBUTES AND METHODS *************/
		protected $addonID = "addon-idealmollie-20160715";
		protected $name = "iDeal Mollie";
		protected $description;

		public function get_addon_form_settings( $form_id )
		{
			global $wpdb;
			// Insertion in database
			if(
				isset( $_REQUEST[ 'DEXBCCF_iDealMollie_id' ] )
			)
			{
			    $wpdb->delete( $wpdb->prefix.$this->form_table, array( 'formid' => $form_id ), array( '%d' ) );
				$wpdb->insert(
								$wpdb->prefix.$this->form_table,
								array(
									'formid' => $form_id,
									'idealmollie_api_username'	 => $_REQUEST["idealmollie_api_username"],
									'return_error'	 => $_REQUEST["mollie_return_error"],
									'enabled'	 => $_REQUEST["mollie_enabled"]
								),
								array( '%d', '%s', '%s')
							);
			}


			$rows = $wpdb->get_results(
						$wpdb->prepare( "SELECT * FROM ".$wpdb->prefix.$this->form_table." WHERE formid=%d", $form_id )
					);
			if (!count($rows))
			{
			    $row["idealmollie_api_username"] = "";
			    $row["return_error"] = "";
			    $row["enabled"] = "0";
			} else {
			    $row["idealmollie_api_username"] = $rows[0]->idealmollie_api_username;
			    $row["return_error"] = $rows[0]->return_error;
			    $row["enabled"] = $rows[0]->enabled;
			}

			?>
			<div id="metabox_basic_settings" class="postbox" >
				<h3 class='hndle' style="padding:5px;"><span><?php print $this->name; ?></span></h3>
				<div class="inside">
				   <input type="hidden" name="DEXBCCF_iDealMollie_id" value="1" />
                   <table class="form-table">
                    <tr valign="top">
                    <th scope="row"><?php _e('Enable iDeal-Mollie? (if enabled PayPal Standard is disabled)', 'bccf'); ?></th>
                    <td><select name="mollie_enabled">
                         <option value="0" <?php if (!$row["enabled"]) echo 'selected'; ?>><?php _e('No', 'bccf'); ?></option>
                         <option value="1" <?php if ($row["enabled"]) echo 'selected'; ?>><?php _e('Yes', 'bccf'); ?></option>
                         </select>
                    </td>
                    </tr>
                    <tr valign="top">
                    <th scope="row"><?php _e('Mollie API Key', 'bccf'); ?></th>
                    <td><input type="text" name="idealmollie_api_username" size="20" value="<?php echo esc_attr($row["idealmollie_api_username"]); ?>" /></td>
                    </tr>
                    <tr valign="top">
                    <th scope="row"><?php _e('If payment fails return to this page', 'bccf'); ?></th>
                    <td><input type="text" name="mollie_return_error" size="20" value="<?php echo esc_attr($row["return_error"]); ?>" /></td>
                    </tr>
                   </table>
				</div>
			</div>
			<?php
		} // end get_addon_form_settings



		/************************ ADDON CODE *****************************/

        /************************ ATTRIBUTES *****************************/

        private $form_table = 'bccf_dex_form_idealmollie';
        private $_inserted = false;

        /************************ CONSTRUCT *****************************/

        function __construct()
        {
			$this->description = __("The add-on adds support for iDeal via Mollie payments", 'bccf' );
            // Check if the plugin is active
			if( !$this->addon_is_active() ) return;

			add_action( 'dexbccf_process_data', array( &$this, 'pp_idealmollie' ), 1, 2 );

			add_action( 'init', array( &$this, 'pp_idealmollie_update_status' ), 10, 1 );
			add_action( 'init', array( &$this, 'pp_idealmollie_return_page' ), 10, 1 );

			add_filter( 'dexbccf_get_option', array( &$this, 'get_option' ), 10, 3 );

			$this->update_database();


        } // End __construct



        /************************ PRIVATE METHODS *****************************/

		/**
         * Create the database tables
         */
        protected function update_database()
		{
			global $wpdb;
			$sql = "CREATE TABLE IF NOT EXISTS ".$wpdb->prefix.$this->form_table." (
					id mediumint(9) NOT NULL AUTO_INCREMENT,
					formid INT NOT NULL,
					enabled varchar(10) DEFAULT '0' NOT NULL ,
					idealmollie_api_username varchar(255) DEFAULT '' NOT NULL ,
					return_error varchar(255) DEFAULT '' NOT NULL ,
					UNIQUE KEY id (id)
				);";
				//  idealmollie_api_signature varchar(255) DEFAULT '' NOT NULL ,
				//	currency varchar(255) DEFAULT '' NOT NULL ,

			$wpdb->query($sql);
		} // end update_database


		/************************ PUBLIC METHODS  *****************************/


		/**
         * process payment
         */
		public function pp_idealmollie($params)
		{
            global $wpdb;

			// documentation: https://goo.gl/w3kKoH

            $rows = $wpdb->get_results(
						$wpdb->prepare( "SELECT * FROM ".$wpdb->prefix.$this->form_table." WHERE formid=%d", $params["formid"] )
					);

			if (!$rows[0]->enabled)
			    return;

            $key = $rows[0]->idealmollie_api_username;
            try
            {
                require_once dirname(__FILE__) . "/mollie.addon/src/Mollie/API/Autoloader.php";
                $mollie = new Mollie_API_Client;
                $mollie->setApiKey( $key );
                $order_id = $params["itemnumber"];
                $payment = $mollie->payments->create(array(
		            "amount"       => $params["final_price"],
		            "description"  => dex_bccf_get_option('paypal_product_name', DEX_BCCF_DEFAULT_PRODUCT_NAME),
		            "webhookUrl"   => cp_bccf_get_FULL_site_url().'/?cp_idealmollie_ipncheck=1&itemnumber='.$params[ 'itemnumber' ].'&d='.$params["formid"],
		            "redirectUrl"  => cp_bccf_get_FULL_site_url().'/?cp_idealmollie_ipnreturn=1&itemnumber='.$params[ 'itemnumber' ].'&d='.$params["formid"]
		            ,
		            "metadata"     => array(
		        	    "order_id" => $order_id,
		            ),
	            ));
                header("Location: " . $payment->getPaymentUrl());
            } catch (Exception $e) {
                echo "Error: ".$e->getMessage();
            }
            exit;
		} // end pp_idealmollie


		/**
		 * log
		 */
		private function _log($adarray = array())
		{
			$h = fopen( __DIR__.'/logs.txt', 'a' );
			$log = "";
			foreach( $_REQUEST as $KEY => $VAL )
			{
				$log .= $KEY.": ".$VAL."\n";
			}
			foreach( $adarray as $KEY => $VAL )
			{
				$log .= $KEY.": ".$VAL."\n";
			}
			$log .= "================================================\n";
			fwrite( $h, $log );
			fclose( $h );
		}

		public function pp_idealmollie_return_page( )
		{
            global $wpdb;
            if ( !isset( $_GET['cp_idealmollie_ipnreturn'] ) || $_GET['cp_idealmollie_ipnreturn'] != '1' || !isset( $_GET["itemnumber"] ) )
                return;

            $itemnumber = intval(@$_GET['itemnumber'] );

            $rowsmollie = $wpdb->get_results(
						$wpdb->prepare( "SELECT * FROM ".$wpdb->prefix.$this->form_table." WHERE formid=%d", $itemnumber )
					);

            $myrows = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM ".TDE_BCCFCALENDAR_DATA_TABLE." WHERE reference=%d", $itemnumber ) );

			if (count($myrows))
			{
				$location = $rowsmollie[0]->return_error;
	            header( 'Location: '.$location );
			}
			else
			{
                $location = dex_bccf_get_option('url_ok', DEX_BCCF_DEFAULT_OK_URL);
                header("Location: ".$location);
			}

            exit();

		}


		public function pp_idealmollie_update_status( )
		{
            global $wpdb;
            if ( !isset( $_GET['cp_idealmollie_ipncheck'] ) || $_GET['cp_idealmollie_ipncheck'] != '1' || !isset( $_GET["itemnumber"] ) )
                return;

            $itemnumber = intval(@$_GET['itemnumber'] );

            $rowsmollie = $wpdb->get_results(
						$wpdb->prepare( "SELECT * FROM ".$wpdb->prefix.$this->form_table." WHERE formid=%d", intval($_GET["d"]) )
					);

			try
			{
                require_once dirname(__FILE__) . "/mollie.addon/src/Mollie/API/Autoloader.php";
                $mollie = new Mollie_API_Client;
                $mollie->setApiKey( $rowsmollie[0]->idealmollie_api_username );
                $payment  = $mollie->payments->get($_POST["id"]);
	            $order_id = $payment->metadata->order_id;
	            if ($payment->isPaid() != TRUE)
	            {
	                $location = $rowsmollie[0]->return_error;
	                header( 'Location: '.$location );
	                exit;
	            }
	        } catch (Exception $e) {
                echo "Error: ".$e->getMessage();
                exit;
            }


            $myrows = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM ".DEX_BCCF_TABLE_NAME." WHERE id=%d", $itemnumber ) );
            $params = unserialize($myrows[0]->buffered_date);

			//if ($myrows[0]->paid == 0)
			//{
				$wpdb->query( $wpdb->prepare( "UPDATE ".DEX_BCCF_TABLE_NAME." SET buffered_date=%s WHERE id=%d", serialize( $params ), $itemnumber ) );
				if (!defined('CP_BCCF_CALENDAR_ID'))
					define ('CP_BCCF_CALENDAR_ID',$myrows[0]->formid);
				dex_process_ready_to_go_bccf( $itemnumber, $payer_email, $params );
			//}

			$location = dex_bccf_get_option('url_ok', DEX_BCCF_DEFAULT_OK_URL);
            header("Location: ".$location);

            exit();

		}


		/**
		 * Used to deactivate PayPal Standard if PayPal Pro is enabled for the form
		 */
		public function get_option( $value, $field, $id )
		{
			if( $field == 'enable_paypal' )
			{
			    global $wpdb;
			    $rows = $wpdb->get_results(
						$wpdb->prepare( "SELECT enabled FROM ".$wpdb->prefix.$this->form_table." WHERE formid=%d", $id )
					);
			    if ( !empty( $rows ) && $rows[0]->enabled)
				    $value = 0;
			}
			return $value;
		} // End get_option



    } // End Class

    // Main add-on code
    $DEXBCCF_iDealMollie_obj = new DEXBCCF_iDealMollie();

	// Add addon object to the objects list
	global $dexbccf_addons_objs_list;
	$dexbccf_addons_objs_list[ $DEXBCCF_iDealMollie_obj->get_addon_id() ] = $DEXBCCF_iDealMollie_obj;
}


?>