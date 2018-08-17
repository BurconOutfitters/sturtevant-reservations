<?php
/*
Documentation: https://goo.gl/w3kKoH
*/
require_once dirname( __FILE__ ).'/base.addon.php';

if( !class_exists( 'DEXBCCF_SabTPV' ) )
{
    class DEXBCCF_SabTPV extends DEXBCCF_BaseAddon
    {
       
        /************* ADDON SYSTEM - ATTRIBUTES AND METHODS *************/
		protected $addonID = "addon-sabtpv-20160715";
		protected $name = "RedSys TPV";
		protected $description;
		
		public function get_addon_form_settings( $form_id )
		{
			global $wpdb;			
			// Insertion in database
			if( 
				isset( $_REQUEST[ 'dexbccf_sabtpv_id' ] )
			)
			{
			    $wpdb->delete( $wpdb->prefix.$this->form_table, array( 'formid' => $form_id ), array( '%d' ) );
				$wpdb->insert( 	
								$wpdb->prefix.$this->form_table, 
								array( 
									'formid' => $form_id,
									'sabtpv_api_username'	 => $_REQUEST["sabtpv_api_username"],
									'sabtpv_api_password'	 => $_REQUEST["sabtpv_api_password"],																		
									'enabled'	 => $_REQUEST["redsys_enabled"],
									'paypal_mode'	 => $_REQUEST["redsys_paypal_mode"]
								), 
								array( '%d', '%s', '%s','%s', '%s' ) 
							);					
			}		

			
			$rows = $wpdb->get_results( 
						$wpdb->prepare( "SELECT * FROM ".$wpdb->prefix.$this->form_table." WHERE formid=%d", $form_id ) 
					);
			if (!count($rows))
			{
			    $row["sabtpv_api_username"] = "";
			    $row["sabtpv_api_password"] = "";
			    $row["sabtpv_api_signature"] = "";
			    $row["currency"] = "USD";
			    $row["enabled"] = "0";
			    $row["paypal_mode"] = "production";
			} else {
			    $row["sabtpv_api_username"] = $rows[0]->sabtpv_api_username;
			    $row["sabtpv_api_password"] = $rows[0]->sabtpv_api_password;
			    $row["sabtpv_api_signature"] = $rows[0]->sabtpv_api_signature;
			    $row["currency"] = $rows[0]->currency;
			    $row["enabled"] = $rows[0]->enabled;
			    $row["paypal_mode"] = $rows[0]->paypal_mode;
			}   
			
			?>
			<div id="metabox_basic_settings" class="postbox" >			
				<h3 class='hndle' style="padding:5px;"><span><?php print $this->name; ?></span></h3>
				<div class="inside"> 
				   <input type="hidden" name="dexbccf_sabtpv_id" value="1" />
                   <table class="form-table">
                    <tr valign="top">        
                    <th scope="row"><?php _e('Enable TPV? (if enabled PayPal Standard is disabled)', 'bccf'); ?></th>
                    <td><select name="redsys_enabled">
                         <option value="0" <?php if (!$row["enabled"]) echo 'selected'; ?>><?php _e('No', 'bccf'); ?></option>
                         <option value="1" <?php if ($row["enabled"]) echo 'selected'; ?>><?php _e('Yes', 'bccf'); ?></option>
                         </select> 
                    </td>
                    </tr>   
                    <tr valign="top">        
                    <th scope="row"><?php _e('C&Oacute;DIGO COMERCIO', 'bccf'); ?></th>
                    <td><input type="text" name="sabtpv_api_username" size="20" value="<?php echo esc_attr($row["sabtpv_api_username"]); ?>" /></td>
                    </tr>   
                    <tr valign="top">        
                    <th scope="row"><?php _e('CLAVE SECRETA', 'bccf');?></th>
                    <td><input type="text" name="sabtpv_api_password" size="40" value="<?php echo esc_attr($row["sabtpv_api_password"]); ?>" /></td>
                    </tr>                                           
                    <tr valign="top">        
                    <th scope="row"><?php _e('Mode', 'bccf'); ?></th>
                    <td><select name="redsys_paypal_mode">
                         <option value="production" <?php if ($row["paypal_mode"] != 'sandbox') echo 'selected'; ?>><?php _e('Production - real payments processed', 'bccf'); ?></option> 
                         <option value="sandbox" <?php if ($row["paypal_mode"] == 'sandbox') echo 'selected'; ?>><?php _e('SandBox - Testing sandbox area', 'bccf'); ?></option> 
                        </select>
                    </td>
                    </tr>                    
                   </table>  
				</div>
			</div>	
			<?php
		} // end get_addon_form_settings
		

		
		/************************ ADDON CODE *****************************/
		
        /************************ ATTRIBUTES *****************************/    
        
        private $form_table = 'bccf_dex_form_sabtpv';        
        private $_inserted = false;
        
        /************************ CONSTRUCT *****************************/
		
        function __construct()
        {
			$this->description = __("The add-on adds support for RedSys TPV payments", 'bccf' );
            // Check if the plugin is active
			if( !$this->addon_is_active() ) return;
			
			add_action( 'dexbccf_process_data', array( &$this, 'pp_sabtpv' ), 1, 2 );						

			add_action( 'init', array( &$this, 'pp_sabtpv_update_status' ), 10, 1 );
			
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
					sabtpv_api_username varchar(255) DEFAULT '' NOT NULL ,
					sabtpv_api_password varchar(255) DEFAULT '' NOT NULL ,					
					paypal_mode varchar(255) DEFAULT '' NOT NULL ,				
					UNIQUE KEY id (id)
				);";
				//  sabtpv_api_signature varchar(255) DEFAULT '' NOT NULL ,
				//	currency varchar(255) DEFAULT '' NOT NULL ,
				
			$wpdb->query($sql);
		} // end update_database		        	
        
        
		/************************ PUBLIC METHODS  *****************************/                               

               
		/**
         * process payment
         */		
		public function pp_sabtpv($params)
		{               
            global $wpdb;						
					
			// documentation: https://goo.gl/w3kKoH                     
           
            $rows = $wpdb->get_results( 
						$wpdb->prepare( "SELECT * FROM ".$wpdb->prefix.$this->form_table." WHERE formid=%d", $params["formid"] ) 
					);
			if (!$rows[0]->enabled)		
			    return;
			    
            $key = $rows[0]->sabtpv_api_password;
           
            $redsys = new DEXBCCF_SermepaTPV();
            $redsys->setAmount($params["final_price"]);
            $redsys->setOrder('111'.$params["itemnumber"]);
            $redsys->setMerchantcode($rows[0]->sabtpv_api_username); 
            $redsys->setCurrency('978');
            $redsys->setTransactiontype('0');
            $redsys->setTerminal('1');
            $redsys->setMethod('C'); //Solo pago con tarjeta, no mostramos iupay
            $redsys->setNotification( (cp_bccf_get_FULL_site_url().'/?cp_sabtpv_ipncheck=1&itemnumber='.$params["itemnumber"]) ); //Url de notificacion
			$url_ok = dex_bccf_get_option('url_ok', DEX_BCCF_DEFAULT_OK_URL);
            $redsys->setUrlOk( $url_ok ); //Url OK
			$url_ko .= (( strpos( '?', $url_ok ) === false ) ? '?' : '&' ).'payment_canceled=1'; 
            $redsys->setUrlKo( $url_ok ); //Url KO            
            $redsys->setVersion('HMAC_SHA256_V1');
            //$redsys->setTradeName('Tienda S.L');
            //$redsys->setTitular('Pedro Risco');
            $redsys->setProductDescription( dex_bccf_get_option('paypal_product_name', DEX_BCCF_DEFAULT_PRODUCT_NAME).$params[ 'external_id' ] );
           
            if ($rows[0]->paypal_mode == 'sandbox')
                $redsys->setEnviroment('test'); //Entorno test                
            else  
                $redsys->setEnviroment('live'); //Entorno production
           
            $signature = $redsys->generateMerchantSignature($key);
            $redsys->setMerchantSignature($signature);
           
            $form = $redsys->executeRedirection();			    

            exit;   
		} // end pp_sabtpv               
		
		
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
		
		public function pp_sabtpv_update_status( )
		{
            global $wpdb;      
            if ( !isset( $_GET['cp_sabtpv_ipncheck'] ) || $_GET['cp_sabtpv_ipncheck'] != '1' || !isset( $_GET["itemnumber"] ) )
                return;
                
            $redsys = new DEXBCCF_SermepaTPV();
            $redsys_params = $redsys->getMerchantParameters($_REQUEST["Ds_MerchantParameters"]);
			//$this->_log($redsys_params);
			
			if (!isset($redsys_params["Ds_Response"]))
			    return;
			
            $itemnumber = intval(@$_GET['itemnumber'] );    
            $myrows = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM ".DEX_BCCF_TABLE_NAME." WHERE id=%d", $itemnumber ) );
            $params = unserialize($myrows[0]->buffered_date);       

			$paymentok = (intval($redsys_params["Ds_Response"]) < 100);
			if (!$paymentok)
			{				
				echo 'Payment failed';
				exit();
			}    

			//if ($myrows[0]->paid == 0)
			//{			    
				$params[ 'tpv_response_code' ] = $redsys_params["Ds_Response"];
				$wpdb->query( $wpdb->prepare( "UPDATE ".DEX_BCCF_TABLE_NAME." SET buffered_date=%s WHERE id=%d", serialize( $params ), $itemnumber ) );		
				if (!defined('CP_BCCF_CALENDAR_ID'))
					define ('CP_BCCF_CALENDAR_ID',$myrows[0]->formid);
				dex_process_ready_to_go_bccf( $itemnumber, $payer_email, $params );				
				echo 'OK - processed';
			//}
			//else
			//	echo 'OK - already processed';
	
            exit();              
              
		}
               
        /**
         * Translate response codes
         */       
       public function getResponseText($responseCode) 
       {
            switch($responseCode)
            {
            	case '101':
            		$reason =  'Tarjeta caducada';
            	break;
            	case '102':
            		$reason =  'Tarjeta en excepcion transitoria o bajo sospecha de fraude';
            	break;
            	case '104':
            		$reason =  'Operacion no permitida para esa tarjeta o terminal';
            	break;
            	case '106':
            		$reason =  'Intentos de PIN excedidos';
            	break;
            	case '116':
            		$reason =  'Disponible insuficiente';
            	break;
            	case '118':
            		$reason =  'Tarjeta no registrada';
            	break;
            	case '125':
            		$reason =  'Tarjeta no efectiva.';
            	break;
            	case '129':
            		$reason =  'Codigo de seguridad (CVV2/CVC2) incorrecto';
            	break;
            	case '180':
            		$reason =  'Tarjeta ajena al servicio';
            	break;
            	case '184':
            		$reason =  'Error en la autenticacion del titular';
            	break;
            	case '190':
            		$reason =  'Denegacion sin especificar Motivo';
            	break;
            	case '191':
            		$reason =  'Fecha de caducidad erronea';
            	break;
            	case '201':
            		$reason =  'Transacción denegada porque la fecha de caducidad de la tarjeta que se ha informado en el pago, es anterior a la actualmente vigente';
            	break;
            	case '202':
            		$reason =  'Tarjeta en excepcion transitoria o bajo sospecha de fraude con retirada de tarjeta';
            	break;
            	case '204':
            		$reason =  'Operación no permitida para ese tipo de tarjeta';
            	break;
            	case '207':
            		$reason =  'El banco emisor no permite una autorización automática. Es necesario contactar telefónicamente con su centro autorizador para obtener una aprobación manual';
            	break;
            	case '208':
            	case '209':
            		$reason =  'Tarjeta bloqueada por el banco emisor debido a que el titular le ha manifestado que le ha sido robada o perdida';
            	break;
            	case '280':
            		$reason =  'Es erróneo el código CVV2/CVC2 informado por el comprador';
            	break;
            	case '290':
            		$reason =  'Transacción denegada por el banco emisor pero sin que este dé detalles acerca del motivo';
            	break;
            	case '904':
            		$reason =  'Comercio no registrado en FUC.';
            	break;
            	case '909':
            		$reason =  'Error de sistema.';
            	break;
            	case '913':
            		$reason =  'Pedido repetido.';
            	break;
            	case '930':
            		if( !empty( $_REQUEST["Ds_pay_method"] ) && $_REQUEST["Ds_pay_method"] == 'R')
            		{
            			$reason =  'Realizado por Transferencia bancaria';
            		} else
            		{
            			$reason =  'Realizado por Domiciliacion bancaria';
            		}
            	break;
            	case '944':
            		$reason =  'Sesión Incorrecta.';
            	break;
            	case '950':
            		$reason =  'Operación de devolución no permitida.';
            	break;
            	case '9064':
            		$reason =  'Número de posiciones de la tarjeta incorrecto.';
            	break;
            	case '9078':
            		$reason =  'No existe método de pago válido para esa tarjeta.';
            	break;
            	case '9093':
            		$reason =  'Tarjeta no existente.';
            	break;
            	case '9094':
            		$reason =  'Rechazo servidores internacionales.';
            	break;
            	case '9104':
            		$reason =  'Comercio con "titular seguro" y titular sin clave de compra segura.';
            	break;
            	case '9218':
            		$reason =  'El comercio no permite op. seguras por entrada /operaciones.';
            	break;
            	case '9253':
            		$reason =  'Tarjeta no cumple el check-digit.';
            	break;
            	case '9256':
            		$reason =  'El comercio no puede realizar preautorizaciones.';
            	break;
            	case '9257':
            		$reason =  'Esta tarjeta no permite operativa de preautorizaciones.';
            	break;
            	case '9261':
            	case '912':
            	case '9912':
            		$reason =  'Emisor no disponible';
            	break;
            	case '9913':
            		$reason =  'Error en la confirmación que el comercio envía al TPV Virtual (solo aplicable en la opción de sincronización SOAP).';
            	break;
            	case '9914':
            		$reason =  'Confirmación "KO" del comercio (solo aplicable en la opción de sincronización SOAP).';
            	break;
            	case '9915':
            		$reason =  'A petición del usuario se ha cancelado el pago.';
            	break;
            	case '9928':
            		$reason =  'Anulación de autorización en diferido realizada por el SIS (proceso batch).';
            	break;
            	case '9929':
            		$reason =  'Anulación de autorización en diferido realizada por el comercio.';
            	break;
            	case '9997':
            		$reason =  'Se está procesando otra transacción en SIS con la misma tarjeta.';
            	break;
            	case '9998':
            		$reason =  'Operación en proceso de solicitud de datos de tarjeta.';
            	break;
            	case '9999':
            		$reason =  'Operación que ha sido redirigida al emisor a autenticar.';
            	default:
            		$reason =  'Transaccion denegada codigo:'.$_REQUEST["Ds_Response"];
            	break;	
            }
            return $reason;
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
    $dexbccf_sabtpv_obj = new DEXBCCF_SabTPV();
    
	// Add addon object to the objects list
	global $dexbccf_addons_objs_list;
	$dexbccf_addons_objs_list[ $dexbccf_sabtpv_obj->get_addon_id() ] = $dexbccf_sabtpv_obj;
}

if( !class_exists( 'DEXBCCF_SermepaTPV' ) )
{
    
    class DEXBCCF_SermepaTPV{
        private $_setEnviroment;
        private $_setMerchantData;
        private $_setTerminal;
        private $_setTransactionType;
        private $_setMethod;
        private $_setNameForm;
        private $_setIdForm;
        private $_setSubmit;
        private $_setIdioma;
        private $_setParameters;
        private $_setVersion;
        private $_setNameSubmit;
        private $_setIdSubmit;
        private $_setValueSubmit;
        private $_setStyleSubmit;
        private $_setSignature;
        /**
         * Constructor
         */
        public function __construct()
        {
            $this->_setEnviroment='https://sis-t.redsys.es:25443/sis/realizarPago';
            $this->_setTerminal =1;
            $this->_setMerchantData = '';
            $this->_setTransactionType=0;
            $this->_setIdioma = '001';
            $this->_setMethod='T';
            $this->_setSubmit = '';
            $this->_setParameters = array();
            $this->_setVersion = 'HMAC_SHA256_V1';
            $this->_setNameForm = 'redsys_form';
            $this->_setIdForm = 'redsys_form';
            $this->_setNameSubmit = 'btn_submit';
            $this->_setIdSubmit = 'btn_submit';
            $this->_setValueSubmit = 'Send';
            $this->_setStyleSubmit = '';            
            
        }
        /************* NEW METHODS ************* */
        /**
         * Set amount (required)
         * @param $amount
         * @throws Exception
         */
        public function setAmount($amount)
        {
            if($amount > 0) {
                $amount = $this->convertNumber($amount);
                $amount = intval(strval($amount*100));
                $this->_setParameters['DS_MERCHANT_AMOUNT'] = $amount;
            }
            else {
                throw new Exception('Amount must be greater than 0.');
            }
        }
        /**
         * Set Order number - [The firsts 4 digits must be numeric.] (required)
         * @param $order
         * @throws Exception
         */
        public function setOrder($order)
        {
            if(strlen(trim($order)) > 0){
                $this->_setParameters['DS_MERCHANT_ORDER'] = $order;
            }
            else{
                throw new Exception('Add Order');
            }
        }
        /**
         * Get order
         * @return mixed
         */
        public function getOrder()
        {
            return $this->_setParameters['DS_MERCHANT_ORDER'];
        }
        /**
         * Get Ds_Order of Notification
         * @param $paraments Array with parameters
         * @return string
         */
        function getOrderNotification($paraments){
            $order = '';
            foreach($paraments as $key => $value) {
                if(strtolower($key) == 'ds_order' ){
                    $order = $value;
                }
            }
            return $order;
        }
        /**
         * Set code Fuc of trade (required)
         * @param $fuc Fuc
         * @throws Exception
         */
        public function setMerchantcode($fuc)
        {
            if(strlen(trim($fuc)) > 0){
                $this->_setParameters['DS_MERCHANT_MERCHANTCODE'] = $fuc;
            }
            else{
                throw new Exception('Please add Fuc');
            }
        }
        /**
         * Set currency
         * @param int $currency 978 para Euros, 840 para Dólares, 826 para libras esterlinas y 392 para Yenes.
         * @throws Exception
         */
        public function setCurrency($currency=978)
        {
            if($currency == '978' || $currency =='840' || $currency =='826' || $currency =='392' ){
                $this->_setParameters['DS_MERCHANT_CURRENCY'] = $currency;
            }
            else{
                throw new Exception('Currency is not valid');
            }
        }
        /**
         * Set Transaction type
         * @param int $transaction
         * @throws Exception
         */
        public function setTransactiontype($transaction=0)
        {
            if(strlen(trim($transaction)) > 0){
                $this->_setParameters['DS_MERCHANT_TRANSACTIONTYPE'] = $transaction;
            }
            else{
                throw new Exception('Please add transaction type');
            }
        }
        /**
         * Set terminal by default is 1 to  Sadabell(required)
         * @param int $terminal
         * @throws Exception
         */
        public function setTerminal($terminal=1)
        {
            if(intval($terminal) !=0){
                $this->_setParameters['DS_MERCHANT_TERMINAL'] = $terminal;
            }
            else{
                throw new Exception('Terminal is not valid.');
            }
        }
        /**
         * Set url notification
         * @param string $url
         */
        public function setNotification($url='')
        {
            $this->_setParameters['DS_MERCHANT_MERCHANTURL'] = $url;
        }
        /**
         * Set url Ok
         * @param string $url
         */
        public function setUrlOk($url='')
        {
            $this->_setParameters['DS_MERCHANT_URLOK'] = $url;
        }
        /**
         * Set url Ko
         * @param string $url
         */
        public function setUrlKo($url='')
        {
            $this->_setParameters['DS_MERCHANT_URLKO'] = $url;
        }
        /**
         * @param string $version
         */
        public function setVersion($version='')
        {
            $this->_setVersion = $version;
        }
        /**
         * Generate Merchant Parameters
         * @return string
         */
        public function generateMerchantParameters()
        {
            //Convert Array to Json
            $json = $this->arrayToJson($this->_setParameters);
            //Return Json to Base64
            return $this->encodeBase64($json);
        }
        /**
         * Generate Merchant Signature
         * @param $key
         * @return string
         */
        public function generateMerchantSignature($key)
        {
            $key = $this->decodeBase64($key);
            //Generate Merchant Parameters
            $merchant_parameter = $this->generateMerchantParameters();
            // Get key with Order and key
            $key = $this->encrypt_3DES($this->getOrder(), $key);
            // Generated Hmac256 of Merchant Parameter
            $result = $this->hmac256($merchant_parameter, $key);
            // Base64 encoding
            return $this->encodeBase64($result);
        }
        /**
         * Generate Merchant Signature Notification
         * @param $key
         * @param $data
         * @return string
         */
        public function generateMerchantSignatureNotification($key, $data){
            $key = $this->decodeBase64($key);
            // Decode data base64
            $decode = $this->base64_url_decode($data);
            // Los datos decodificados se pasan al array de datos
            $parameters = $this->JsonToArray($decode);
            $order = $this->getOrderNotification($parameters);
            $key = $this->encrypt_3DES($order, $key);
            // Generated Hmac256 of Merchant Parameter
            $result = $this->hmac256($data, $key);
            return $this->base64_url_encode($result);
        }
        /**
         * Set Merchant Signature
         * @param $signature
         * @internal param $value
         */
        public function setMerchantSignature($signature)
        {
            $this->_setSignature = $signature;
        }
        /**
         * Set enviroment
         * @param string $enviroment test or live
         * @throws Exception
         */
        public function setEnviroment($enviroment='test')
        {
            if(trim($enviroment) == 'live'){
                //Live
                $this->_setEnviroment='https://sis.redsys.es/sis/realizarPago';
            }
            elseif(trim($enviroment) == 'test'){
                //Test
                $this->_setEnviroment ='https://sis-t.redsys.es:25443/sis/realizarPago';
            }
            else{
                throw new Exception('Add test or live');
            }
        }
        /**
         * Set language code by default 001 = Spanish
         *
         * @param string $languagecode Language code [Castellano-001, Inglés-002, Catalán-003, Francés-004, Alemán-005, Holandés-006, Italiano-007, Sueco-008, Portugués-009, Valenciano-010, Polaco-011, Gallego-012 y Euskera-013.]
         * @throws Exception
         */
        public function setLanguage($languagecode='001')
        {
            if(strlen(trim($languagecode)) > 0){
                $this->_setParameters['DS_MERCHANT_CONSUMERLANGUAGE'] = trim($languagecode);
            }
            else{
                throw new Exception('Add language code');
            }
        }
        /**
         * Return enviroment
         *
         * @return string Url of enviroment
         */
        public function getEnviroment()
        {
            return $this->_setEnviroment;
        }
        /**
         * Optional field for the trade to be included in the data sent by the "on-line" response to trade if this option has been chosen.
         * @param $merchantdata
         * @throws Exception
         */
        public function setMerchantData($merchantdata)
        {
            if(strlen(trim($merchantdata)) > 0){
                $this->_setParameters['DS_MERCHANT_MERCHANTDATA'] = trim($merchantdata);
            }
            else{
                throw new Exception('Add merchant data');
            }
        }
        /**
         * Set product description (optional)
         *
         * @param string $description
         * @throws Exception
         */
        public function setProductDescription($description='')
        {
            if(strlen(trim($description)) > 0){
                $this->_setParameters['DS_MERCHANT_PRODUCTDESCRIPTION'] = trim($description);
            }
            else{
                throw new Exception('Add product description');
            }
        }
        /**
         * Set name of the user making the purchase (required)
         *
         * @param string $titular name of the user (for example Alonso Cotos)
         * @throws Exception
         */
        public function setTitular($titular='')
        {
            if(strlen(trim($titular)) > 0){
                $this->_setParameters['DS_MERCHANT_TITULAR'] = trim($titular);
            }
            else{
                throw new Exception('Add name for the user');
            }
        }
        /**
         * Set Trade name Trade name will be reflected in the ticket trade (Optional)
         *
         * @param string $tradename trade name
         * @throws Exception
         */
        public function setTradeName($tradename='')
        {
            if(strlen(trim($tradename)) > 0){
                $this->_setParameters['DS_MERCHANT_MERCHANTNAME'] = trim($tradename);
            }
            else{
                throw new Exception('Add name for Trade name');
            }
        }
        /**
         * Payment type
         *
         * @param string $method [T = Pago con Tarjeta + iupay , R = Pago por Transferencia, D = Domiciliacion, C = Sólo Tarjeta (mostrará sólo el formulario para datos de tarjeta)] por defecto es T
         * @throws Exception
         */
        public function setMethod($method='T')
        {
            if(strlen(trim($method)) > 0){
                $this->_setParameters['DS_MERCHANT_PAYMETHODS'] = trim($method);
            }
            else{
                throw new Exception('Add pay method');
            }
        }
        /**
         * Set name to form
         *
         * @param string $name Name for form.
         */
        public function setNameForm($name = 'servired_form')
        {
            $this->_setNameForm = $name;
        }
        /**
         * Set Id to form
         *
         * @param string $id Name for Id
         */
        public function setIdForm($id = 'servired_form')
        {
            $this->_setIdForm = $id;
        }
        /**
         * Set Attributes to submit
         * @param string $name Name submit
         * @param string $id Id submit
         * @param string $value Value submit
         * @param string $style Set Style
         */
        public function setAttributesSubmit($name = 'btn_submit', $id='btn_submit', $value='Send', $style='')
        {
            $this->_setNameSubmit = $name;
            $this->_setIdSubmit = $id;
            $this->_setValueSubmit = $value;
            $this->_setStyleSubmit = $style;
        }
        /**
         * Execute redirection to TPV
         */
        public function executeRedirection()
        {
            echo $this->createForm();
            echo '<script>document.forms["'.$this->_setNameForm.'"].submit();</script>';
        }
        /**
         * Generate form html
         *
         * @return string
         */
        public function createForm()
        {
            $form='
                <form action="'.$this->_setEnviroment.'" method="post" id="'.$this->_setIdForm.'" name="'.$this->_setNameForm.'" >
                    <input type="hidden" name="Ds_MerchantParameters" value="'.$this->generateMerchantParameters().'"/>
                    <input type="hidden" name="Ds_Signature" value="'.$this->_setSignature.'"/>
                    <input type="hidden" name="Ds_SignatureVersion" value="'.$this->_setVersion.'"/>
                    <input type="submit" name="'.$this->_setNameSubmit.'" id="'.$this->_setIdSubmit.'" value="'.$this->_setValueSubmit.'" style="'.$this->_setStyleSubmit.'" >
                </form>
            ';
            return $form;
        }
        /**
         * Check if properly made ??the purchase.
         *
         * @param string $key Key
         * @param array $postData Data received by the bank
         * @return bool
         * @throws Exception
         */
        public function check($key='', $postData)
        {
            if (isset($postData))
            {
                $version = $postData["Ds_SignatureVersion"];
                $parameters = $postData["Ds_MerchantParameters"];
                $signatureReceived = $postData["Ds_Signature"];
                $decodec = $this->decodeParameters($parameters);
                $signature = $this->generateMerchantSignatureNotification($key,$parameters);
                if ($signature === $signatureReceived){
                    return 1;
                } else {
                    return 0;
                }
            } else {
                throw new Exception("Add data return of bank");
            }
        }
        /**
         *  Decode Ds_MerchantParameters, return array with the parameters
         * @param $parameters
         * @return array with parameters of bank
         */
        public function getMerchantParameters($parameters){
            $decodec = $this->decodeParameters($parameters);
            $decodec_array=$this->JsonToArray($decodec);
            return $decodec_array;
        }
        /**
         * Return array with all parameters assigned.
         * @return array
         */
        public function getParameters()
        {
            return $this->_setParameters;
        }
        // ******** UTILS ********
        /**
         * Convert Array to json
         * @param $data Array
         * @return string Json
         */
        private function arrayToJson($data)
        {
            return json_encode($data);
        }
        /**
         * Convert Json to array
         * @param $data
         * @return mixed
         */
        private function JsonToArray($data)
        {
            return json_decode($data, true);
        }
        /**
         * Generate sha256
         * @param $data
         * @param $key
         * @return string
         */
        private function hmac256($data, $key)
        {
            $sha256 = hash_hmac('sha256', $data, $key, true);
            return $sha256;
        }
        /**
         * Encrypt to 3DES
         * @param $data Data for encrypt
         * @param $key Key
         * @return string
         */
        private function encrypt_3DES($data, $key){
            $iv = "\0\0\0\0\0\0\0\0";
            $ciphertext = mcrypt_encrypt(MCRYPT_3DES, $key, $data, MCRYPT_MODE_CBC, $iv);
            return $ciphertext;
        }
        private function decodeParameters($data){
            $decode = base64_decode(strtr($data, '-_', '+/'));
            return $decode;
        }
        //http://stackoverflow.com/a/9111049/444225
        private function priceToSQL($price)
        {
            $price = preg_replace('/[^0-9\.,]*/i', '', $price);
            $price = str_replace(',', '.', $price);
            if(substr($price, -3, 1) == '.')
            {
                $price = explode('.', $price);
                $last = array_pop($price);
                $price = join($price, '').'.'.$last;
            }
            else
            {
                $price = str_replace('.', '', $price);
            }
            return $price;
        }
        private function convertNumber($price)
        {
            $number=number_format(str_replace(',', '.', $price), 2, '.', '');
            return $number;
        }
        /******  Base64 Functions  *****
         * @param $input
         * @return string
         */
        private function base64_url_encode($input)
        {
            return strtr(base64_encode($input), '+/', '-_');
        }
        /**
         * @param $data
         * @return string
         */
        private function encodeBase64($data)
        {
            $data = base64_encode($data);
            return $data;
        }
        /**
         * @param $input
         * @return string
         */
        private function base64_url_decode($input)
        {
            return base64_decode(strtr($input, '-_', '+/'));
        }
        /**
         * @param $data
         * @return string
         */
        private function decodeBase64($data)
        {
            $data = base64_decode($data);
            return $data;
        }
        // ******** END UTILS ********
    }

}

?>