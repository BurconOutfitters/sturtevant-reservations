<?php
/**
 * WooCommerce addon.
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

require_once dirname( __FILE__ ) . '/base.addon.php';

/**
 * Addon attributes and methods.
 *
 * @since  1.0.0
 * @access public
 */
if ( ! class_exists( 'DEXBCCF_WooCommerce' ) ) {
    class DEXBCCF_WooCommerce extends DEXBCCF_BaseAddon {

		protected $addonID = "addon-woocommerce-20150309";
		protected $name = "WooCommerce";
		protected $description;

        // Form data.
        private $form  = [];

        // Control attribute to avoid read multiple times the form associated to the product.
        private $first_time = true;

        /**
		 * Constructor method.
		 *
		 * @since  1.0.0
		 * @access public
		 * @return self
		 */
        function __construct() {

			$this->description = __( 'The add-on allows integrate the forms with WooCommerce products', 'sc-res' );

            // Check if the plugin is active
			if ( ! $this->addon_is_active() ) {
                return;
            }

			// Check if WooCommerce is active in the website
            $active_plugins = (array) get_option( 'active_plugins', [] );

            if ( is_multisite() ) {
                $active_plugins = array_merge( $active_plugins, get_site_option( 'active_sitewide_plugins', [] ) );
            }

            // Bail if WooCommerce is not active.
            if ( ! in_array( 'woocommerce/woocommerce.php', $active_plugins ) || array_key_exists( 'woocommerce/woocommerce.php', $active_plugins ) ) {
                return;
            }

            // Load resources, css and js
            add_action( 'woocommerce_before_single_product', [ $this, 'enqueue_scripts' ], 10 );

			// Addon display
            add_action('woocommerce_before_add_to_cart_button', [ $this, 'display_form' ], 10);

            // Corrects the form options
            add_filter( 'dexbccf_get_option', [ $this, 'get_form_options' ], 10, 3 );

            // Filters for cart actions
			add_filter( 'woocommerce_add_cart_item_data', [ $this, 'add_cart_item_data' ], 10, 2);
			add_filter( 'woocommerce_get_item_data', [ $this, 'get_cart_item_data' ], 10, 2);
			add_filter( 'woocommerce_get_cart_item_from_session', [ $this, 'get_cart_item_from_session' ], 10, 2);
            add_filter( 'woocommerce_add_cart_item', [ $this, 'add_cart_item' ], 10, 1);
			add_action( 'woocommerce_add_order_item_meta', [ $this, 'add_order_item_meta' ], 10, 3);

            // Filters for the CP Contact Form with PayPal
            add_action( 'dexbccf_redirect', [ $this, 'dexbccf_redirect' ], 10 );

			// The init hook
			add_action( 'admin_init', [ $this, 'init_hook' ], 1 );

        }

        /**
         * Check if the add-on can be applied to the product.
         *
         * @since  1.0.0
		 * @access private
         * @param  int $id
         * @return mixed
         */
        private function apply_addon( $id = false ) {

            global $post;

            $this->form = array();

            if ( $id ) {
                $post_id = $id;
            } elseif ( isset( $_REQUEST['woocommerce_dexbccf_product'] ) ) {
                $post_id = $_REQUEST['woocommerce_dexbccf_product'];
            } elseif ( isset( $post ) ) {
                $post_id = $post->ID;
            }

            if ( isset( $post_id ) ) {

                $tmp = get_post_meta( $post_id, 'woocommerce_dexbccf_form', true );

                if ( ! empty( $tmp ) ) {
                    $this->form['id'] = $tmp;
                }
            }

            return ! empty( $this->form );

        }

        /**
         * Add to cart data.
         *
         * @since  1.0.0
		 * @access public
         * @param  array $cart_item_meta
         * @param  int $product_id
         * @return array
         */
		public function add_cart_item_data( $cart_item_meta, $product_id ) {

			if ( ! isset( $cart_item_meta['cp_cff_form_data'] ) && isset( $_SESSION['cp_cff_form_data'] ) ) {
                $cart_item_meta['cp_cff_form_data'] = $_SESSION['cp_cff_form_data'];
            }

            return $cart_item_meta;

        }

        /**
         * Get cart item from session.
         *
         * @since  1.0.0
		 * @access public
         * @param  array $cart_item
         * @param  array $values
         * @return array
         */
        public function get_cart_item_from_session( $cart_item, $values ) {

			if ( isset( $values['cp_cff_form_data'] ) ) {
				$cart_item['cp_cff_form_data'] = $values['cp_cff_form_data'];
                $this->add_cart_item( $cart_item );
            }

			return $cart_item;

		}

        /**
         * Get cart item data.
         *
         * @since  1.0.0
		 * @access public
         * @param  array $values
         * @param  array $cart_item
         * @return array
         */
		public function get_cart_item_data( $values, $cart_item ) {

			global $wpdb;

			// Adjust price if required based in the dexbccf_data.
			if ( isset( $cart_item['cp_cff_form_data'] ) ) {

                $data_id = $cart_item['cp_cff_form_data'];

                if ( ! empty( $data_id ) ) {

					$data             = $wpdb->get_var( $wpdb->prepare( "SELECT question as data FROM ".DEX_BCCF_TABLE_NAME." WHERE id=%d", $data_id ) );
                    $activate_summary = get_post_meta( $cart_item['product_id'], 'woocommerce_dexbccf_activate_summary', true );

					if ( ! empty( $activate_summary ) && function_exists( 'dex_bccf_form_result' ) ) {

						$summary_title = get_post_meta( $cart_item['product_id'], 'woocommerce_dexbccf_summary_title', true );
						if ( empty( $summary_title ) ) {
                            $summary_title = '';
                        }

						$summary = get_post_meta( $cart_item['product_id'], 'woocommerce_dexbccf_summary', true );
						if ( empty( $summary ) ) {
                            $summary = '<%INFO%>';
                        }

						$result   = ( dex_bccf_form_result( [], $summary, $data_id ) );
						$values[] = [ 'name' => ( ( ! empty( $summary_title ) ) ? $summary_title : '' ) , 'value' => $result ];

					} else {

						$data     = preg_replace( [ "/\n+/", "/:+\s*/" ], [ "\n", ":" ], $data );
                        $data_arr = explode( "\n", $data );

						foreach( $data_arr as $data_item ) {

							if ( ! empty( $data_item ) ) {

                                $data_item = explode( ':', $data_item );

								if ( count( $data_item) == 2 ) {
									$values[] = array(
                                        'name' 	=> stripcslashes( $data_item[0] ),
                                        'value' => stripcslashes( $data_item[1] )
                                    );
                                }

                            }

                        }

                    }

                }

            }
			unset( $_SESSION['cp_cff_form_data'] );
            return $values;

        }

        /**
         * Helper function, used when an item is added to the cart as well as when an item is restored from session.
         *
         * @since  1.0.0
		 * @access public
         * @param  array $cart_item
         * @return array
         */
		function add_cart_item( $cart_item ) {

			global $wpdb;

			// Adjust price if required based in the dexbccf_data.
			if ( isset( $cart_item['cp_cff_form_data'] ) ) {

                $tmp = get_post_meta( $cart_item['product_id'], 'woocommerce_dexbccf_calculate_price', true );

                if ( ! empty( $tmp ) ) {

					$minimum_price            = get_post_meta( $cart_item['product_id'], 'woocommerce_dexbccf_minimum_price', true );
                    $data_id                  = $cart_item['cp_cff_form_data'];
                    $data                     = $wpdb->get_var( $wpdb->prepare( "SELECT buffered_date as paypal_post FROM ".DEX_BCCF_TABLE_NAME." WHERE id=%d", $data_id ) );
                    $paypal_data              = unserialize( $data );
                    $price                    = preg_replace( '/[^\d\.\,]/', '', $paypal_data['final_price'] );
                    $cart_item['data']->price = ( ! empty( $minimum_price ) ) ? max( $price, $minimum_price ) : $price;

                }

            }

            return $cart_item;

		}

        /**
         * Avoid redirect the CP Contact Form with PayPal to the thanks page.
         *
         * @since  1.0.0
		 * @access public
         * @return void
         */
        public function dexbccf_redirect() {

			if ( isset( $_REQUEST['product'] ) || isset( $_REQUEST['woocommerce_dexbccf_product'] ) ) {
                return false;
            } else {
                return true;
            }

        }

        /**
         * Add order item meta
         *
         * @since  1.0.0
		 * @access public
         * @param  int $item_id
         * @param  array $values
         * @param  string $cart_item_key
         * @return void
         */
        public function add_order_item_meta( $item_id, $values, $cart_item_key ) {

            global $wpdb;
            $data_id = $values['cp_cff_form_data'];

            if ( $this->apply_addon( $values['data']->id ) ) {

			    $data = $wpdb->get_row( $wpdb->prepare( "SELECT question as data, buffered_date as paypal_post FROM ".DEX_BCCF_TABLE_NAME." WHERE id=%d", $data_id ) );

				if ( ! empty( $data->paypal_post ) && ( $dataArr = @unserialize( $data->paypal_post ) ) !== false ) {

					foreach ( $dataArr as $fieldname => $value ) {

						if ( strpos( $fieldname, '_url' ) !== false ) {

							$_fieldname   = str_replace( '_url', '', $fieldname );
							$_value       = $dataArr[$_fieldname];
							$_values 	  = explode( ',', $_value );
							$_replacement = [];

							if ( count( $_values ) == count( $value ) ) {

								foreach( $_values as $key => $_fileName ) {
									$_fileName      = trim( $_fileName );
									$_replacement[] = '<a href="'.$value[$key].'" target="_blank">'.$_fileName.'</a>';
                                }

                            }

							if ( ! empty( $_replacement ) ) {
								$data->data = str_replace( $_value, implode( ', ', $_replacement ) , $data->data );
							}
						}
					}
				}

                $metadata = preg_replace( "/\n+/", "<br />", $data->data );
                wc_add_order_item_meta( $item_id, __( 'Data', 'sc-res' ), $metadata, true );

            }

        }

        /**
         * Display the form associated to the product.
         *
         * @since  1.0.0
		 * @access public
         * @return mixed
         */
        public function display_form() {

            global $post, $woocommerce;

            if ( $this->apply_addon() ) {

				$product = null;
				if ( function_exists( 'get_product' ) ) {
					$product = get_product( $post->ID );
				} else {
					$product = new WC_Product( $post->ID );
				}

                $form_content = dex_bccf_filter_content( $this->form );

				// Initialize form fields.
				if (
					! empty( $_SESSION['cp_cff_form_data'] ) &&
					! empty( $_REQUEST['dex_item'] ) /** && ! empty( $_REQUEST[ 'cp_pform_psequence' ] )  */
				) {

                    global $wpdb;

					$result = $wpdb->get_row( $wpdb->prepare( "SELECT buffered_date AS paypal_post FROM ".DEX_BCCF_TABLE_NAME." AS form_data WHERE form_data.id=%d AND form_data.formid=%d", $_SESSION['cp_cff_form_data'],  $_REQUEST['dex_item'] ) );

					if ( ! is_null( $result ) ) {

						$arr            = [];
						$submitted_data = unserialize( $result->paypal_post );
						foreach( $submitted_data as $key => $val ) {
							if ( preg_match( '/^fieldname\d+$/', $key ) ) {
								$arr[$key/**.$_REQUEST[ 'cp_pform_psequence' ]*/] = $val;
							}
						} ?>
						<script>
							dexbccf_default  = ( typeof dexbccf_default != 'undefined' ) ? dexbccf_default : {};
							dexbccf_default['form_structure<?php /** echo $_REQUEST[ 'cp_pform_psequence' ]; */ ?>'] = <?php echo json_encode( $arr ); ?>;
						</script>
				    <?php }
                }

                unset( $_SESSION['cp_cff_form_data'] );

                // Remove the form tags.
                if ( preg_match( '/<form[^>]*>/', $form_content, $match ) ) {
                    $form_content = str_replace( $match[0], '', $form_content );
                    $form_content = preg_replace( '/<\/form>/', '', $form_content );
                }

                $tmp          = get_post_meta( $post->ID, 'woocommerce_dexbccf_calculate_price', true );
                $request_cost = ( ! empty( $tmp ) ) ? dex_bccf_get_option( 'request_cost', false, $this->form['id'] ) : false;

                $form = '<div class="cpcff-woocommerce-wrapper">';
                $form .= $form_content;
                $form .= ( ( method_exists( $woocommerce, 'nonce_field' ) ) ? $woocommerce->nonce_field('add_to_cart') : '' );
                $form .= '<input type="hidden" name="woocommerce_dexbccf_product" value="' . $post->ID . '" />';
                $form .= ( ( $request_cost ) ? '<input type="hidden" name="woocommerce_dexbccf_field" value="' . $request_cost . '" /><input type="hidden" name="woocommerce_dexbccf_form" value="' . $this->form['id'].'">' : '' );
                $form .= '</div>';

                echo $form;

                $add_to_cart_value = '';

				if ( $product->is_type( 'variable' ) ) {
                    $add_to_cart_value = 'variation';
                } elseif ( $product->has_child() ) {
                    $add_to_cart_value = 'group';
                } else {
					$add_to_cart_value = $product->id;
				}

                if ( ! function_exists( 'get_product' ) ) {
					if ( method_exists( $woocommerce, 'nonce_field' ) ) {
                        $woocommerce->nonce_field('add_to_cart');
                    }
					echo '<input type="hidden" name="add-to-cart" value="' . $add_to_cart_value . '" />';
				} else {
					echo '<input type="hidden" name="add-to-cart" value="' . $post->ID . '" />';
				}
			}

			echo '<div class="clear"></div>';

        }

        /**
         * Enqueue all resources: CSS and JS files, required by the addon.
         *
         * @since  1.0.0
		 * @access public
         * @return void
         */
        public function enqueue_scripts() {

            if ( $this->apply_addon() ) {
                wp_enqueue_style ( 'dexbccf_wocommerce_addon_css', plugins_url( '/woocommerce.addon/css/styles.min.css', __FILE__ ) );
                wp_enqueue_script( 'dexbccf_wocommerce_addon_js', plugins_url( '/woocommerce.addon/js/scripts.min.js',  __FILE__ ), [ 'jquery' ] );
            }

        }

        /**
         * Corrects the form options.
         *
         * @since  1.0.0
		 * @access public
         * @param  array $value
         * @param  string $field
         * @param  int $id
         * @return void
         */
        public function get_form_options( $value, $field, $id ) {

            if ( $this->apply_addon() ) {

                switch( $field ) {
                    case 'fp_return_page' :
                        return $_SERVER['REQUEST_URI'];
                    case 'cv_enable_captcha' :
                        return 0;
                    break;
                    case 'cache' :
                        return '';
                    case 'enable_paypal' :
                        return 0;
                }
            }

            return $value;

        }

        public function init_hook() {

            add_meta_box( 'dexbccf_woocommerce_metabox', __( 'CP Contact Form with PayPal', 'sc-res' ), [ $this, 'metabox_form' ], 'product', 'normal', 'high' );

            add_action( 'save_post', [ $this, 'save_data ' ] );

        }

        public function metabox_form() {

            global $post;

            $id               = get_post_meta( $post->ID, 'woocommerce_dexbccf_form', true );
            $active           = get_post_meta( $post->ID, 'woocommerce_dexbccf_calculate_price', true );
            $minimum_price    = get_post_meta( $post->ID, 'woocommerce_dexbccf_minimum_price', true );
            $activate_summary = get_post_meta( $post->ID, 'woocommerce_dexbccf_activate_summary', true );
            $summary_title    = get_post_meta( $post->ID, 'woocommerce_dexbccf_summary_title', true );
            $summary          = get_post_meta( $post->ID, 'woocommerce_dexbccf_summary', true );
			?>
            <table class="form-table">
				<tr>
					<td>
						<?php _e( 'Enter the ID of the form', 'bccf' );?>:
					</td>
                    <td>
                        <input type="text" name="woocommerce_dexbccf_form" value="<?php print( esc_attr( ( ! empty( $id ) ) ? $id : '' ) ); ?>" />
                    </td>
                </tr>
                <tr>
					<td style="white-space:nowrap;">
						<?php _e( 'Calculate the product price through the form', 'sc-res' );?>:
					</td>
                    <td style="width:100%;">
                        <input type="checkbox" name="woocommerce_dexbccf_calculate_price" <?php print( ( ! empty( $active ) ) ? 'checked' : '' ); ?> />
					</td>
				</tr>
				<tr>
					<td>
						<?php _e( 'Minimum price allowed (numbers only)', 'sc-res' );?>:
					</td>
					<td>
						<input type="text" name="woocommerce_dexbccf_minimum_price" value="<?php print( esc_attr( ( ! empty( $minimum_price ) ) ? $minimum_price : '' ) ); ?>">
                    </td>
                </tr>
				<tr style="border-top:2px solid #DDD;border-left:2px solid #DDD;border-right:2px solid #DDD;">
					<td colspan="2">
						<?php _e( 'The summary section is optional. It is possible to use the special tags supported by the notification emails.', 'bccf' );?>
					</td>
				</tr>
				<tr style="border-left:2px solid #DDD;border-right:2px solid #DDD;">
					<td>
						<?php _e( 'Activate the summary', 'sc-res' );?>:
					</td>
					<td>
						<input type="checkbox" name="woocommerce_dexbccf_activate_summary" <?php print( ( ! empty( $activate_summary ) ) ? 'CHECKED' : '' ); ?> />
                    </td>
                </tr>
				<tr style="border-left:2px solid #DDD;border-right:2px solid #DDD;">
					<td>
						<?php _e( 'Summary title', 'sc-res' );?>:
					</td>
					<td>
						<input type="text" name="woocommerce_dexbccf_summary_title" value="<?php print( esc_attr( ( ! empty( $summary_title ) ) ? $summary_title : '' ) ); ?>" style="width:100%;">
                    </td>
                </tr>
				<tr style="border-bottom:2px solid #DDD;border-left:2px solid #DDD;border-right:2px solid #DDD;">
					<td>
						<?php _e( 'Summary', 'sc-res' );?>:
					</td>
					<td>
						<textarea name="woocommerce_dexbccf_summary" style="resize: vertical; min-height: 70px; width:100%;"><?php print ( esc_textarea( ( ! empty( $summary ) ) ? $summary : '' ) ); ?></textarea>
					</td>
                </tr>

            </table>
			<?php

        }

        public function save_data() {

            global $post;

            if ( ! empty( $post ) && is_object( $post ) && $post->post_type == 'product' ) {

                delete_post_meta( $post->ID, 'woocommerce_dexbccf_form' );
                delete_post_meta( $post->ID, 'woocommerce_dexbccf_calculate_price' );
                delete_post_meta( $post->ID, 'woocommerce_dexbccf_minimum_price' );
                delete_post_meta( $post->ID, 'woocommerce_dexbccf_activate_summary' );
                delete_post_meta( $post->ID, 'woocommerce_dexbccf_summary' );
                delete_post_meta( $post->ID, 'woocommerce_dexbccf_summary_title' );

                if ( isset( $_REQUEST['woocommerce_dexbccf_form'] ) ) {

                    add_post_meta( $post->ID, 'woocommerce_dexbccf_form', $_REQUEST['woocommerce_dexbccf_form'], true );
                    add_post_meta( $post->ID, 'woocommerce_dexbccf_minimum_price', trim( $_REQUEST['woocommerce_dexbccf_minimum_price'] ), true );
                    add_post_meta(
                        $post->ID,
                        'woocommerce_dexbccf_calculate_price',
                        ( empty( $_REQUEST['woocommerce_dexbccf_calculate_price'] ) ) ? false : true,
                        true
                    );
                    add_post_meta( $post->ID, 'woocommerce_dexbccf_activate_summary', ( ! empty( $_REQUEST['woocommerce_dexbccf_activate_summary'] ) ) ? 1 : 0, true );
                    add_post_meta( $post->ID, 'woocommerce_dexbccf_summary_title', trim( $_REQUEST['woocommerce_dexbccf_summary_title'] ), true );
					add_post_meta( $post->ID, 'woocommerce_dexbccf_summary', trim( $_REQUEST['woocommerce_dexbccf_summary'] ), true );
                }

            }

        }

    }

    // Main add-on code
    $dexbccf_woocommerce_obj = new DEXBCCF_WooCommerce();

	// Add addon object to the objects list
	global $dexbccf_addons_objs_list;
    $dexbccf_addons_objs_list[$dexbccf_woocommerce_obj->get_addon_id()] = $dexbccf_woocommerce_obj;

}