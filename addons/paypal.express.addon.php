<?php
/*
Documentation: https://goo.gl/w3kKoH
*/
require_once dirname( __FILE__ ) . '/sc-res-base-addon.php';

if( !class_exists( 'DEXBCCF_PayPalExpress' ) )
{
    class DEXBCCF_PayPalExpress extends DEXBCCF_BaseAddon
    {

        /************* ADDON SYSTEM - ATTRIBUTES AND METHODS *************/
		protected $addonID = "addon-paypalexpress-20160715";
		protected $name = "PayPal Express Checkout";
		protected $description;

		public function get_addon_form_settings( $form_id )
		{
			global $wpdb;
			// Insertion in database
			if(
				isset( $_REQUEST[ 'DEXBCCF_PayPalExpress_id' ] )
			)
			{
			    $wpdb->delete( $wpdb->prefix.$this->form_table, array( 'formid' => $form_id ), array( '%d' ) );
				$wpdb->insert(
								$wpdb->prefix.$this->form_table,
								array(
									'formid' => $form_id,
									'paypalexpress_api_username'	 => $_REQUEST["paypalexpress_api_username"],
									'paypalexpress_api_pass'	 => $_REQUEST["paypalexpress_api_pass"],
									'paypalexpress_api_sig'	 => $_REQUEST["paypalexpress_api_sig"],
									'paypalexpress_mode'	 => $_REQUEST["paypalexpress_mode"],
									'enabled'	 => $_REQUEST["ppec_enabled"]
								),
								array( '%d', '%s', '%s', '%s', '%s')
							);
			}

			$rows = $wpdb->get_results(
						$wpdb->prepare( "SELECT * FROM ".$wpdb->prefix.$this->form_table." WHERE formid=%d", $form_id )
					);
			if (!count($rows))
			{
			    $row["paypalexpress_api_username"] = "";
			    $row["paypalexpress_api_pass"] = "";
			    $row["paypalexpress_api_sig"] = "";
			    $row["paypalexpress_mode"] = "production";
			    $row["enabled"] = "0";
			} else {
			    $row["paypalexpress_api_username"] = $rows[0]->paypalexpress_api_username;
			    $row["paypalexpress_api_pass"] = $rows[0]->paypalexpress_api_pass;
			    $row["paypalexpress_api_sig"] = $rows[0]->paypalexpress_api_sig;
			    $row["paypalexpress_mode"] = $rows[0]->paypalexpress_mode;
			    $row["enabled"] = $rows[0]->enabled;
			}

			?>
			<div id="metabox_basic_settings" class="postbox" >
				<h3 class='hndle' style="padding:5px;"><span><?php print $this->name; ?></span></h3>
				<div class="inside">
				   <input type="hidden" name="DEXBCCF_PayPalExpress_id" value="1" />
                   <table class="form-table">
                    <tr valign="top">
                    <th scope="row"><?php _e('Enable PayPal Express Checkout? (if enabled PayPal Standard is disabled)', 'bccf'); ?></th>
                    <td><select name="ppec_enabled">
                         <option value="0" <?php if (!$row["enabled"]) echo 'selected'; ?>><?php _e('No', 'bccf'); ?></option>
                         <option value="1" <?php if ($row["enabled"]) echo 'selected'; ?>><?php _e('Yes', 'bccf'); ?></option>
                         </select>
                    </td>
                    </tr>
                    <tr valign="top">
                    <th scope="row"><?php _e('PayPal Express mode? (production for real payments, sandbox for testing)', 'bccf'); ?></th>
                    <td><select name="paypalexpress_mode">
                         <option value="production" <?php if ($row["paypalexpress_mode"] == 'production') echo 'selected'; ?>><?php _e('Production', 'bccf'); ?></option>
                         <option value="sandbox" <?php if ($row["paypalexpress_mode"] == 'sandbox') echo 'selected'; ?>><?php _e('Sandbox', 'bccf'); ?></option>
                         </select>
                    </td>
                    </tr>
                    <tr valign="top">
                    <th scope="row"><?php _e('PayPal Express API Key', 'bccf'); ?></th>
                    <td><input type="text" name="paypalexpress_api_username" size="20" value="<?php echo esc_attr($row["paypalexpress_api_username"]); ?>" /></td>
                    </tr>
                    <tr valign="top">
                    <th scope="row"><?php _e('PayPal Express API Password', 'bccf'); ?></th>
                    <td><input type="text" name="paypalexpress_api_pass" size="20" value="<?php echo esc_attr($row["paypalexpress_api_pass"]); ?>" /></td>
                    </tr>
                    <tr valign="top">
                    <th scope="row"><?php _e('PayPal Express API Signature', 'bccf'); ?></th>
                    <td><input type="text" name="paypalexpress_api_sig" size="20" value="<?php echo esc_attr($row["paypalexpress_api_sig"]); ?>" /></td>
                    </tr>
                   </table>
				</div>
			</div>
			<?php
		} // end get_addon_form_settings



		/************************ ADDON CODE *****************************/

        /************************ ATTRIBUTES *****************************/

        private $form_table = 'bccf_dex_form_paypalexpress';
        private $_inserted = false;

        /************************ CONSTRUCT *****************************/

        function __construct()
        {
			$this->description = __("The add-on adds support for PayPal Express Checkout payments", 'bccf' );
            // Check if the plugin is active
			if( !$this->addon_is_active() ) return;

			add_action( 'dexbccf_process_data', array( &$this, 'pp_paypalexpress' ), 1, 2 );

			add_action( 'init', array( &$this, 'pp_paypalexpress_update_status' ), 10, 1 );

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
					paypalexpress_api_username varchar(255) DEFAULT '' NOT NULL ,
					paypalexpress_api_pass varchar(255) DEFAULT '' NOT NULL ,
					paypalexpress_api_sig varchar(255) DEFAULT '' NOT NULL ,
					paypalexpress_mode varchar(255) DEFAULT '' NOT NULL ,
					UNIQUE KEY id (id)
				);";

			$wpdb->query($sql);
		} // end update_database


		/************************ PUBLIC METHODS  *****************************/


		/**
         * process payment
         */
		public function pp_paypalexpress($params)
		{
            global $wpdb;

			// documentation: https://goo.gl/w3kKoH

            $rows = $wpdb->get_results(
						$wpdb->prepare( "SELECT * FROM ".$wpdb->prefix.$this->form_table." WHERE formid=%d", $params["formid"] )
					);
			if (!$rows[0]->enabled || $params["final_price"] == 0)
			    return;

            try
            {
                $ppexp = new DEXBCCF_PayPalEXPC();
                $ppexp->mode = $rows[0]->paypalexpress_mode;
                $ppexp->API_UserName = $rows[0]->paypalexpress_api_username;
                $ppexp->API_Password = $rows[0]->paypalexpress_api_pass;
                $ppexp->API_Signature = $rows[0]->paypalexpress_api_sig;
                $ppexp->currency = dex_bccf_get_option('currency', DEX_BCCF_DEFAULT_CURRENCY);
                $ppexp->lang = dex_bccf_get_option('paypal_language', DEX_BCCF_DEFAULT_PAYPAL_LANGUAGE);

                $order_id = $params["itemnumber"];

                $products = [];
                $products[0]['ItemName'] = dex_bccf_get_option('paypal_product_name', DEX_BCCF_DEFAULT_PRODUCT_NAME); //Item Name
		        $products[0]['ItemPrice'] = $params["final_price"]; //Item Price
		        $products[0]['ItemNumber'] = $order_id; //Item Number
		        $products[0]['ItemDesc'] = dex_bccf_get_option('paypal_product_name', DEX_BCCF_DEFAULT_PRODUCT_NAME); //Item Number
		        $products[0]['ItemQty']	= 1; // Item Quantity

		        $charges = []; //Other important variables like tax, shipping cost
		        $charges['TotalTaxAmount'] = 0;  //Sum of tax for all items in this order.
		        $charges['HandalingCost'] = 0;  //Handling cost for this order.
		        $charges['InsuranceCost'] = 0;  //shipping insurance cost for this order.
		        $charges['ShippinDiscount'] = 0; //Shipping discount for this order. Specify this as negative number.
		        $charges['ShippinCost'] = 0; //Although you may change the value later, try to pass in a shipping amount that is reasonably accurate.

                $okurl = cp_bccf_get_FULL_site_url().'/?cp_paypalexpress_ipncheck=1&itemnumber='.$params[ 'itemnumber' ].'&d='.$params["formid"];
                $err_url = dex_bccf_get_option('url_cancel', DEX_BCCF_DEFAULT_CANCEL_URL);

                if (function_exists ('dex_process_send_non_completed'))
                    dex_process_send_non_completed($order_id, "", $params);

                $ppexp->SetExpressCheckOut($products, $charges, $okurl, $err_url);
                exit;

            } catch (Exception $e) {
                echo "Error: ".$e->getMessage();
            }
            exit;
		} // end pp_paypalexpress


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

		public function pp_paypalexpress_update_status( )
		{
            global $wpdb;
            if ( !isset( $_GET['cp_paypalexpress_ipncheck'] ) || $_GET['cp_paypalexpress_ipncheck'] != '1' || !isset( $_GET["itemnumber"] ) )
                return;

            $itemnumber = intval(@$_GET['itemnumber'] );

            $rowsppec = $wpdb->get_results(
						$wpdb->prepare( "SELECT * FROM ".$wpdb->prefix.$this->form_table." WHERE formid=%d", intval($_GET["d"]) )
					);

            $myrows = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM ".DEX_BCCF_TABLE_NAME." WHERE id=%d", $itemnumber ) );
            $params = unserialize($myrows[0]->buffered_date);

            if (!defined('CP_BCCF_CALENDAR_ID'))
			    define ('CP_BCCF_CALENDAR_ID',$myrows[0]->calendar);

			try
			{

                $order_id = $params["itemnumber"];

                $products = [];
                $products[0]['ItemName'] = dex_bccf_get_option('paypal_product_name', DEX_BCCF_DEFAULT_PRODUCT_NAME); //Item Name
		        $products[0]['ItemPrice'] = $params["final_price"]; //Item Price
		        $products[0]['ItemNumber'] = $order_id; //Item Number
		        $products[0]['ItemDesc'] = dex_bccf_get_option('paypal_product_name', DEX_BCCF_DEFAULT_PRODUCT_NAME); //Item Number
		        $products[0]['ItemQty']	= 1; // Item Quantity

		        $charges = []; //Other important variables like tax, shipping cost
		        $charges['TotalTaxAmount'] = 0;  //Sum of tax for all items in this order.
		        $charges['HandalingCost'] = 0;  //Handling cost for this order.
		        $charges['InsuranceCost'] = 0;  //shipping insurance cost for this order.
		        $charges['ShippinDiscount'] = 0; //Shipping discount for this order. Specify this as negative number.
		        $charges['ShippinCost'] = 0; //Although you may change the value later, try to pass in a shipping amount that is reasonably accurate.

                $ppexp = new DEXBCCF_PayPalEXPC();
                $ppexp->mode = $rowsppec[0]->paypalexpress_mode;
                $ppexp->API_UserName = $rowsppec[0]->paypalexpress_api_username;
                $ppexp->API_Password = $rowsppec[0]->paypalexpress_api_pass;
                $ppexp->API_Signature = $rowsppec[0]->paypalexpress_api_sig;
                $ppexp->currency = dex_bccf_get_option('currency', DEX_BCCF_DEFAULT_CURRENCY);
                $ppexp->lang = dex_bccf_get_option('paypal_language', DEX_BCCF_DEFAULT_PAYPAL_LANGUAGE);
                $ppexp->DoExpressCheckoutPayment();

	        } catch (Exception $e) {
                echo "Error: ".$e->getMessage();
                exit;
            }

			if ($myrows[0]->paid == 0)
			{
				$wpdb->query( $wpdb->prepare( "UPDATE ".DEX_BCCF_TABLE_NAME." SET buffered_date=%s WHERE id=%d", serialize( $params ), $itemnumber ) );
				dex_process_ready_to_go_bccf( $itemnumber, $payer_email, $params );
			}

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
    $DEXBCCF_PayPalExpress_obj = new DEXBCCF_PayPalExpress();

	// Add addon object to the objects list
	global $dexbccf_addons_objs_list;
	$dexbccf_addons_objs_list[ $DEXBCCF_PayPalExpress_obj->get_addon_id() ] = $DEXBCCF_PayPalExpress_obj;
}

?><?php

if( !class_exists( 'DEXBCCF_PayPalEXPC' ) )
{
    	class DEXBCCF_PayPalEXPC {

	    public $mode = 'sandbox';
	    public $API_UserName = '';
		public $API_Password = '';
		public $API_Signature = '';
		public $currency = 'EUR';
		public $lang = 'EN';

		function GetItemTotalPrice($item){

			//(Item Price x Quantity = Total) Get total amount of product;
			return $item['ItemPrice'] * $item['ItemQty'];
		}

		function GetProductsTotalAmount($products){

			$ProductsTotalAmount=0;

			foreach($products as $p => $item){

				$ProductsTotalAmount = $ProductsTotalAmount + $this -> GetItemTotalPrice($item);
			}

			return $ProductsTotalAmount;
		}

		function GetGrandTotal($products, $charges){

			//Grand total including all tax, insurance, shipping cost and discount

			$GrandTotal = $this -> GetProductsTotalAmount($products);

			foreach($charges as $charge){

				$GrandTotal = $GrandTotal + $charge;
			}

			return $GrandTotal;
		}

		function SetExpressCheckout($products, $charges, $okurl, $errurl, $noshipping='1'){

			//Parameters for SetExpressCheckout, which will be sent to PayPal

			$padata  = 	'&METHOD=SetExpressCheckout';

			$padata .= 	'&RETURNURL='.urlencode($okurl);
			$padata .=	'&CANCELURL='.urlencode($errurl);
			$padata .=	'&PAYMENTREQUEST_0_PAYMENTACTION='.urlencode("SALE");

			foreach($products as $p => $item){

				$padata .=	'&L_PAYMENTREQUEST_0_NAME'.$p.'='.urlencode($item['ItemName']);
				$padata .=	'&L_PAYMENTREQUEST_0_NUMBER'.$p.'='.urlencode($item['ItemNumber']);
				$padata .=	'&L_PAYMENTREQUEST_0_DESC'.$p.'='.urlencode($item['ItemDesc']);
				$padata .=	'&L_PAYMENTREQUEST_0_AMT'.$p.'='.urlencode($item['ItemPrice']);
				$padata .=	'&L_PAYMENTREQUEST_0_QTY'.$p.'='. urlencode($item['ItemQty']);
			}

			/*

			//Override the buyer's shipping address stored on PayPal, The buyer cannot edit the overridden address.

			$padata .=	'&ADDROVERRIDE=1';
			$padata .=	'&PAYMENTREQUEST_0_SHIPTONAME=J Smith';
			$padata .=	'&PAYMENTREQUEST_0_SHIPTOSTREET=1 Main St';
			$padata .=	'&PAYMENTREQUEST_0_SHIPTOCITY=San Jose';
			$padata .=	'&PAYMENTREQUEST_0_SHIPTOSTATE=CA';
			$padata .=	'&PAYMENTREQUEST_0_SHIPTOCOUNTRYCODE=US';
			$padata .=	'&PAYMENTREQUEST_0_SHIPTOZIP=95131';
			$padata .=	'&PAYMENTREQUEST_0_SHIPTOPHONENUM=408-967-4444';

			*/

			$padata .=	'&NOSHIPPING='.$noshipping; //set 1 to hide buyer's shipping address, in-case products that does not require shipping

			$padata .=	'&PAYMENTREQUEST_0_ITEMAMT='.urlencode($this -> GetProductsTotalAmount($products));

			$padata .=	'&PAYMENTREQUEST_0_TAXAMT='.urlencode($charges['TotalTaxAmount']);
			$padata .=	'&PAYMENTREQUEST_0_SHIPPINGAMT='.urlencode($charges['ShippinCost']);
			$padata .=	'&PAYMENTREQUEST_0_HANDLINGAMT='.urlencode($charges['HandalingCost']);
			$padata .=	'&PAYMENTREQUEST_0_SHIPDISCAMT='.urlencode($charges['ShippinDiscount']);
			$padata .=	'&PAYMENTREQUEST_0_INSURANCEAMT='.urlencode($charges['InsuranceCost']);
			$padata .=	'&PAYMENTREQUEST_0_AMT='.urlencode($this->GetGrandTotal($products, $charges));
			$padata .=	'&PAYMENTREQUEST_0_CURRENCYCODE='.urlencode($this->currency);

			//paypal custom template

			$padata .=	'&LOCALECODE='.$this->lang; //PayPal pages to match the language on your website;
			// $padata .=	'&LOGOIMG='.PPL_LOGO_IMG; //site logo
			$padata .=	'&CARTBORDERCOLOR=FFFFFF'; //border color of cart
			$padata .=	'&ALLOWNOTE=1';

			############# set session variable we need later for "DoExpressCheckoutPayment" #######

			//$_SESSION['ppl_products'] =  $products;
			//$_SESSION['ppl_charges'] 	=  $charges;

			$httpParsedResponseAr = $this->PPHttpPost('SetExpressCheckout', $padata);

			//Respond according to message we receive from Paypal
			if("SUCCESS" == strtoupper($httpParsedResponseAr["ACK"]) || "SUCCESSWITHWARNING" == strtoupper($httpParsedResponseAr["ACK"])){

				$paypalmode = ($this->mode=='sandbox') ? '.sandbox' : '';

				//Redirect user to PayPal store with Token received.

				$paypalurl ='https://www'.$paypalmode.'.paypal.com/cgi-bin/webscr?cmd=_express-checkout&token='.$httpParsedResponseAr["TOKEN"].'';

				header('Location: '.$paypalurl);
			}
			else{

				//Show error message

				echo '<div style="color:red"><b>Error : </b>'.urldecode($httpParsedResponseAr["L_LONGMESSAGE0"]).'</div>';

				echo '<pre>';

					print_r($httpParsedResponseAr);

				echo '</pre>';
			}
		}


		function DoExpressCheckoutPayment($ppl_products, $ppl_charges){

			if(!empty($ppl_products)&&!empty($ppl_charges)){

				$products=$ppl_products;

				$charges=$ppl_charges;

				$padata  = 	'&TOKEN='.urlencode($_GET['token']);
				$padata .= 	'&PAYERID='.urlencode($_GET['PayerID']);
				$padata .= 	'&PAYMENTREQUEST_0_PAYMENTACTION='.urlencode("SALE");

				//set item info here, otherwise we won't see product details later

				foreach($products as $p => $item){

					$padata .=	'&L_PAYMENTREQUEST_0_NAME'.$p.'='.urlencode($item['ItemName']);
					$padata .=	'&L_PAYMENTREQUEST_0_NUMBER'.$p.'='.urlencode($item['ItemNumber']);
					$padata .=	'&L_PAYMENTREQUEST_0_DESC'.$p.'='.urlencode($item['ItemDesc']);
					$padata .=	'&L_PAYMENTREQUEST_0_AMT'.$p.'='.urlencode($item['ItemPrice']);
					$padata .=	'&L_PAYMENTREQUEST_0_QTY'.$p.'='. urlencode($item['ItemQty']);
				}

				$padata .= 	'&PAYMENTREQUEST_0_ITEMAMT='.urlencode($this -> GetProductsTotalAmount($products));
				$padata .= 	'&PAYMENTREQUEST_0_TAXAMT='.urlencode($charges['TotalTaxAmount']);
				$padata .= 	'&PAYMENTREQUEST_0_SHIPPINGAMT='.urlencode($charges['ShippinCost']);
				$padata .= 	'&PAYMENTREQUEST_0_HANDLINGAMT='.urlencode($charges['HandalingCost']);
				$padata .= 	'&PAYMENTREQUEST_0_SHIPDISCAMT='.urlencode($charges['ShippinDiscount']);
				$padata .= 	'&PAYMENTREQUEST_0_INSURANCEAMT='.urlencode($charges['InsuranceCost']);
				$padata .= 	'&PAYMENTREQUEST_0_AMT='.urlencode($this->GetGrandTotal($products, $charges));
				$padata .= 	'&PAYMENTREQUEST_0_CURRENCYCODE='.urlencode($this->currency);

				//We need to execute the "DoExpressCheckoutPayment" at this point to Receive payment from user.

				$httpParsedResponseAr = $this->PPHttpPost('DoExpressCheckoutPayment', $padata);

				//vdump($httpParsedResponseAr);

				//Check if everything went ok..
				if("SUCCESS" == strtoupper($httpParsedResponseAr["ACK"]) || "SUCCESSWITHWARNING" == strtoupper($httpParsedResponseAr["ACK"])){

					echo '<h2>Success</h2>';
					echo 'Your Transaction ID : '.urldecode($httpParsedResponseAr["PAYMENTINFO_0_TRANSACTIONID"]);

					/*
					//Sometimes Payment are kept pending even when transaction is complete.
					//hence we need to notify user about it and ask him manually approve the transiction
					*/

					if('Completed' == $httpParsedResponseAr["PAYMENTINFO_0_PAYMENTSTATUS"]){

						echo '<div style="color:green">Payment Received! Your product will be sent to you very soon!</div>';
					}
					elseif('Pending' == $httpParsedResponseAr["PAYMENTINFO_0_PAYMENTSTATUS"]){

						echo '<div style="color:red">Transaction Complete, but payment may still be pending! '.
						'If that\'s the case, You can manually authorize this payment in your <a target="_new" href="http://www.paypal.com">Paypal Account</a></div>';
					}

					$this->GetTransactionDetails();
				}
				else{

					echo '<div style="color:red"><b>Error : </b>'.urldecode($httpParsedResponseAr["L_LONGMESSAGE0"]).'</div>';

					echo '<pre>';

						print_r($httpParsedResponseAr);

					echo '</pre>';
					exit;
				}
			}
			else{

				// Request Transaction Details

				$this->GetTransactionDetails();
			}
		}

		function GetTransactionDetails(){

			// we can retrive transection details using either GetTransactionDetails or GetExpressCheckoutDetails
			// GetTransactionDetails requires a Transaction ID, and GetExpressCheckoutDetails requires Token returned by SetExpressCheckOut

			$padata = 	'&TOKEN='.urlencode($_GET['token']);

			$httpParsedResponseAr = $this->PPHttpPost('GetExpressCheckoutDetails', $padata);

			if("SUCCESS" == strtoupper($httpParsedResponseAr["ACK"]) || "SUCCESSWITHWARNING" == strtoupper($httpParsedResponseAr["ACK"])){

				//echo '<br /><b>Stuff to store in database :</b><br /><pre>';
				/*
				#### SAVE BUYER INFORMATION IN DATABASE ###
				//see (http://www.sanwebe.com/2013/03/basic-php-mysqli-usage) for mysqli usage

				$buyerName = $httpParsedResponseAr["FIRSTNAME"].' '.$httpParsedResponseAr["LASTNAME"];
				$buyerEmail = $httpParsedResponseAr["EMAIL"];

				//Open a new connection to the MySQL server
				$mysqli = new mysqli('host','username','password','database_name');

				//Output any connection error
				if ($mysqli->connect_error) {
					die('Error : ('. $mysqli->connect_errno .') '. $mysqli->connect_error);
				}

				$insert_row = $mysqli->query("INSERT INTO BuyerTable
				(BuyerName,BuyerEmail,TransactionID,ItemName,ItemNumber, ItemAmount,ItemQTY)
				VALUES ('$buyerName','$buyerEmail','$transactionID','$products[0]['ItemName']',$products[0]['ItemNumber'], $products[0]['ItemTotalPrice'],$ItemQTY)");

				if($insert_row){
					print 'Success! ID of last inserted record is : ' .$mysqli->insert_id .'<br />';
				}else{
					die('Error : ('. $mysqli->errno .') '. $mysqli->error);
				}

				*/

				return $httpParsedResponseAr;
			}
			else  {

				echo '<div style="color:red"><b>GetTransactionDetails failed:</b>'.urldecode($httpParsedResponseAr["L_LONGMESSAGE0"]).'</div>';

				echo '<pre>';

					print_r($httpParsedResponseAr);

				echo '</pre>';

			}
		}

		function PPHttpPost($methodName_, $nvpStr_) {

				// Set up your API credentials, PayPal end point, and API version.
				$API_UserName = urlencode($this->API_UserName);
				$API_Password = urlencode($this->API_Password);
				$API_Signature = urlencode($this->API_Signature);

				$paypalmode = ($this->mode=='sandbox') ? '.sandbox' : '';

				$API_Endpoint = "https://api-3t".$paypalmode.".paypal.com/nvp";
				$version = urlencode('109.0');

				// Set the curl parameters.
				$ch = curl_init();
				curl_setopt($ch, CURLOPT_URL, $API_Endpoint);
				curl_setopt($ch, CURLOPT_VERBOSE, 1);
				//curl_setopt($ch, CURLOPT_SSL_CIPHER_LIST, 'TLSv1');

				// Turn off the server and peer verification (TrustManager Concept).
				curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
				curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);

				curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
				curl_setopt($ch, CURLOPT_POST, 1);

				// Set the API operation, version, and API signature in the request.
				$nvpreq = "METHOD=$methodName_&VERSION=$version&PWD=$API_Password&USER=$API_UserName&SIGNATURE=$API_Signature$nvpStr_";

				// Set the request as a POST FIELD for curl.
				curl_setopt($ch, CURLOPT_POSTFIELDS, $nvpreq);

				// Get response from the server.
				$httpResponse = curl_exec($ch);

				if(!$httpResponse) {
					exit("$methodName_ failed: ".curl_error($ch).'('.curl_errno($ch).')');
				}

				// Extract the response details.
				$httpResponseAr = explode("&", $httpResponse);

				$httpParsedResponseAr = array();
				foreach ($httpResponseAr as $i => $value) {

					$tmpAr = explode("=", $value);

					if(sizeof($tmpAr) > 1) {

						$httpParsedResponseAr[$tmpAr[0]] = $tmpAr[1];
					}
				}

				if((0 == sizeof($httpParsedResponseAr)) || !array_key_exists('ACK', $httpParsedResponseAr)) {

					exit("Invalid HTTP Response for POST request($nvpreq) to $API_Endpoint.");
				}

			return $httpParsedResponseAr;
		}
	}

}
?>