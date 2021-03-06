<?php
/*
....
*/
require_once dirname( __FILE__ ).'/base.addon.php';

if( !class_exists( 'DEXBCCF_reCAPTCHA' ) )
{
    class DEXBCCF_reCAPTCHA extends DEXBCCF_BaseAddon
    {
        /************* ADDON SYSTEM - ATTRIBUTES AND METHODS *************/
		protected $addonID = "addon-recaptcha-20151106";
		protected $name = "reCAPTCHA";
		protected $description;
		
		public function get_addon_settings()
		{
			if( isset( $_REQUEST[ 'dexbccf_recaptcha' ] ) )
			{	
				check_admin_referer( 'session_id_'.session_id(), '_dexbccf_nonce' );
				update_option( 'dexbccf_recaptcha_sitekey', trim( $_REQUEST[ 'dexbccf_recaptcha_sitekey' ] ) );
				update_option( 'dexbccf_recaptcha_secretkey', trim( $_REQUEST[ 'dexbccf_recaptcha_secretkey' ] ) );
			}	
			?>
			<form method="post">
				<div id="metabox_basic_settings" class="postbox" >
					<h3 class='hndle' style="padding:5px;"><span><?php print $this->name; ?></span></h3>
					<div class="inside"> 
						<table cellspacing="0" style="width:100%;">
							<tr>
								<td style="white-space:nowrap;width:200px;"><?php _e('Site Key', 'bccf');?>:</td>
								<td>
									<input type="text" name="dexbccf_recaptcha_sitekey" value="<?php echo ( ( $key = get_option( 'dexbccf_recaptcha_sitekey' ) ) !== false ) ? $key : ''; ?>"  style="width:80%;" />
								</td>
							</tr>
							<tr>
								<td style="white-space:nowrap;width:200px;"><?php _e('Secret Key', 'bccf');?>:</td>
								<td>
									<input type="text" name="dexbccf_recaptcha_secretkey" value="<?php echo ( ( $key = get_option( 'dexbccf_recaptcha_secretkey' ) ) !== false ) ? $key : ''; ?>" style="width:80%;" />
								</td>
							</tr>
						</table>
						<input type="submit" name="Save settings" />
					</div>
					<input type="hidden" name="dexbccf_recaptcha" value="1" />
					<input type="hidden" name="_dexbccf_nonce" value="<?php echo wp_create_nonce( 'session_id_'.session_id() ); ?>" />
				</div>
			</form>
			<?php
		}
		
		/************************ ADDON CODE *****************************/
        /************************ ATTRIBUTES *****************************/
        
		private $_recaptcha_inserted = false;
		private $_recaptcha_callback = false;
		private $im_flag    = false; // I'm
		private $_sitekey 	= '';
		private $_secretkey = '';
		
        /************************ CONSTRUCT *****************************/
		
        function __construct()
        {
			$this->description = __("The add-on allows to protect the forms with reCAPTCHA service of Google", 'bccf');
			
            // Check if the plugin is active
			if( !$this->addon_is_active() ) return;
			
			// TO-DO
			// Insert action or filter for checking the captcha in the submitted information
			
			// If reCAPTCHA is enabled do not include the common captcha in the form
			add_filter( 'dexbccf_get_option', array( &$this, 'get_form_options' ), 10, 3 );
			
			if( !is_admin() )
			{	
				if( $this->apply_addon() !== false )
				{	
					
					if( isset( $_REQUEST[ 'dexbccf_recaptcha_response' ] ) )
					{
						if( $this->validate_form( trim( $_REQUEST[ 'dexbccf_recaptcha_response' ] ) ) )
						{
							print 'ok';
						}	
						else
						{
							print 'captchafailed';
						}	
						exit;
					}	
					// Inserts the SCRIPT tag to import the reCAPTCHA on webpage
					add_action( 'wp_footer', array( &$this, 'insert_script' ), 99 );
					
					// Inserts the reCAPTCHA field in the form
					add_filter( 'dexbccf_the_form', array( &$this, 'insert_recaptcha'), 99, 2 );
					
					// Validate the form's submission
					add_filter( 'dexbccf_valid_submission', array( &$this, 'validate_form' ) );
					
					// Insert the JS code to validate the recaptcha code through AJAX
					add_action( 'dexbccf_script_after_validation', array( &$this, 'validate_form_script'), 1, 2 );
				}	
			}	
        } // End __construct
        
        /************************ PRIVATE METHODS *****************************/
		
		/**
		 * Check if the API keys have been defined and return the pair of keys or false 
		 */
        private function apply_addon()
		{
			if( 
				( $sitekey   = get_option( 'dexbccf_recaptcha_sitekey' ) ) !== false && !empty( $sitekey ) &&
				( $secretkey = get_option( 'dexbccf_recaptcha_secretkey' ) ) !== false && !empty( $secretkey )
			)
			{
				$this->_sitekey   = $sitekey;
				$this->_secretkey = $secretkey;
				
				return true;
			}
			return false;
		
		} // End apply_addon
		
		/************************ PUBLIC METHODS  *****************************/
        
		/**
         * Check if the reCAPTCHA is used in the form, and inserts the SCRIPT tag that includes its code
         */ 
        public function	insert_script( $params )
		{
			if( $this->_recaptcha_inserted )
			{
				if( !$this->_recaptcha_callback )
				{
					print '
					<script type="text/javascript">
						var cff_reCAPTCHA_callback = function(){
							jQuery( ".g-recaptcha" ).each(
								function()
								{
									grecaptcha.render( this, {"sitekey" : "'.$this->_sitekey.'"});
								}
							);	
						};
					</script>';
					$this->_recaptcha_callback = true;
				}	
				print '<script src="//www.google.com/recaptcha/api.js?onload=cff_reCAPTCHA_callback&render=explicit" async defer></script>';	
			}	
		} // End insert_script
		
		/**
         * Check if the reCAPTCHA is used in the form, and inserts the reCAPTCHA tag
         */ 
        public function	insert_recaptcha( $form_code, $id )
		{

			$this->im_flag = true;
			$is_captcha_enabled = dex_bccf_get_option('cv_enable_captcha', true, $id );
			$this->im_flag = false;

			if( $is_captcha_enabled == false || $is_captcha_enabled == 'false' )
			{	
				return $form_code;
			}
			$this->_recaptcha_inserted = true;
			return preg_replace( '/<\/form>/i', '<div style="margin-top:20px;" class="g-recaptcha" data-sitekey="'.$this->_sitekey.'"></div></form>', $form_code );	
		} // End insert_recaptcha
		
		/**
         * Insert the JS code into the doValidate function for checking the reCAPTCHA code with AJAX
         */ 
        public function validate_form_script( $sequence, $formid )
		{
			$this->im_flag = true;
			$is_captcha_enabled = dex_bccf_get_option('cv_enable_captcha', true, $formid );
			$this->im_flag = false;
			if( $is_captcha_enabled == false || $is_captcha_enabled == 'false' )
			{
				return;
			}
			
			global $dexbccf_default_texts_array;
			$dexbccf_texts_array = dex_bccf_get_option( 'vs_all_texts', $dexbccf_default_texts_array, $formid );
			$dexbccf_texts_array = array_replace_recursive( 
				$dexbccf_default_texts_array, 
				is_string( $dexbccf_texts_array ) ? unserialize( $dexbccf_texts_array ) : $dexbccf_texts_array
			);
			
		?>
			var recaptcha = $dexQuery( '[name="dex_bccf_pform<?php /** print $sequence; */ ?>"] [name="g-recaptcha-response"]' );
			if( 
				recaptcha.length == 0 ||
				/^\s*$/.test( recaptcha.val() )
			)
			{
				alert('<?php echo( _e('Captcha verification is missing.') ); ?>');
				return false;
			}
			else
			{
				var result = $dexQuery.ajax({
					type: "GET",
					url:  "<?php echo cp_bccf_get_site_url(); ?>",
					data: {
						ps: "<?php echo $sequence; ?>",
						dexbccf_recaptcha_response: recaptcha.val()
					},
					async: false
				}).responseText;
				if (result.indexOf("captchafailed") != -1)
				{
					alert('<?php echo( _e('Captcha verification failed. Please try again.') ); ?>');
					return false;
				}	
			}	
		<?php
			
		} // End validate_form_script
		
		/**
         * Check if the reCAPTCHA is valid and return a boolean
         */ 
        public function	validate_form( $recaptcha_response = '' )
		{
			
			if( session_id() == "" ) @session_start();
			
			$this->im_flag = true;
			$is_captcha_enabled = dex_bccf_get_option('cv_enable_captcha', true, $id );
			$this->im_flag = false;
			if( $is_captcha_enabled == false || $is_captcha_enabled == 'false' )
			{
				return true;
			}
			
			if( !empty( $_SESSION[ 'dexbccf_recaptcha_i_am_human' ]) ) return true;
			
			if( isset( $_POST[ 'g-recaptcha-response' ] ) )
			{
				$recaptcha_response = $_POST[ 'g-recaptcha-response' ];
			}	

			if( !empty( $recaptcha_response ) )
			{	
				$response = wp_remote_post( 
					'https://www.google.com/recaptcha/api/siteverify', 
					array(
						'body' => array(
							'secret' 	=> $this->_secretkey,
							'response' 	=> $recaptcha_response
						)	
					) 
				);

				if( !is_wp_error( $response ) )
				{
					$response = json_decode( $response[ 'body' ] );
					if( !is_null( $response ) && isset( $response->success ) && $response->success )
					{
						$_SESSION[ 'dexbccf_recaptcha_i_am_human' ] = 1;
						return true;
					}	
						
				}	
					
			}
			return false;	
			
		} // End dexbccf_valid_submission
		
		/**
         * Corrects the form options
         */
        public function get_form_options( $value, $field, $id )
        {
	
			if( !$this->im_flag && $field == 'cv_enable_captcha' && $this->apply_addon() !== false ){
				return 0;
			}	
            return $value;    
		} // End get_form_options
		
    } // End Class
    
    // Main add-on code
    $dexbccf_recaptcha_obj = new DEXBCCF_reCAPTCHA();
    
	// Add addon object to the objects list
	global $dexbccf_addons_objs_list;
	$dexbccf_addons_objs_list[ $dexbccf_recaptcha_obj->get_addon_id() ] = $dexbccf_recaptcha_obj;
}
?>