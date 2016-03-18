<?php
/**
 * PayFast Payment Gateway
 *
 * Provides a PayFast Payment Gateway.
 *
 * @class 		woocommerce_payfast
 * @package		WooCommerce
 * @category	Payment Gateways
 * @author		WooThemes
 */
class WC_Gateway_PayFast extends WC_Payment_Gateway {

	/**
	 * Version
	 * @var string
	 */
	public $version = '1.2.8';

	/**
	 * Constructor
	 */
	public function __construct() {
        $this->id			        = 'payfast';
        $this->method_title         = __( 'PayFast', 'woocommerce-gateway-payfast' );
		$this->method_description   = sprintf( __( 'PayFast works by sending the user to %sPayFast%s to enter their payment information.', 'woocommerce-gateway-payfast' ), '<a href="http://payfast.co.za/">', '</a>' );
        $this->icon 		        = WP_PLUGIN_URL . "/" . plugin_basename( dirname( dirname( __FILE__ ) ) ) . '/assets/images/icon.png';
        $this->debug_email 	        = get_option( 'admin_email' );

		// Setup available countries.
		$this->available_countries  = array( 'ZA' );

		// Setup available currency codes.
		$this->available_currencies = array( 'ZAR' );

		// Load the form fields.
		$this->init_form_fields();

		// Load the settings.
		$this->init_settings();

		// Setup constants.
		if ( ! is_admin() ) {
			$this->setup_constants();
		}

		// Setup default merchant data.
		$this->merchant_id      = $this->get_option( 'merchant_id' );
		$this->merchant_key     = $this->get_option( 'merchant_key' );
		$this->url              = 'https://www.payfast.co.za/eng/process?aff=woo-free';
		$this->validate_url     = 'https://www.payfast.co.za/eng/query/validate';
		$this->title            = $this->get_option( 'title' );
		$this->response_url	    = add_query_arg( 'wc-api', 'WC_Gateway_PayFast', home_url( '/' ) );
		$this->send_debug_email = 'yes' === $this->get_option( 'send_debug_email' );

		// Setup the test data, if in test mode.
		if ( 'yes' === $this->get_option( 'testmode' ) ) {
			$this->url          = 'https://sandbox.payfast.co.za/eng/process?aff=woo-free';
			$this->validate_url = 'https://sandbox.payfast.co.za/eng/query/validate';
			$this->merchant_id  = '10000100';
	        $this->merchant_key = '46f0cd694581a';
			$this->add_testmode_admin_settings_notice();
		} else {
			$this->send_debug_email = false;
		}

		add_action( 'woocommerce_api_wc_gateway_payfast', array( $this, 'check_itn_response' ) );
		add_action( 'woocommerce_update_options_payment_gateways_' . $this->id, array( $this, 'process_admin_options' ) );
		add_action( 'woocommerce_receipt_payfast', array( $this, 'receipt_page' ) );

		// Check if the base currency supports this gateway.
		if ( ! $this->is_valid_for_use() ) {
			$this->enabled = false;
		}
    }

	/**
     * Initialise Gateway Settings Form Fields
     *
     * @since 1.0.0
     */
    public function init_form_fields () {
    	$this->form_fields = array(
			'enabled' => array(
				'title'       => __( 'Enable/Disable', 'woocommerce-gateway-payfast' ),
				'label'       => __( 'Enable PayFast', 'woocommerce-gateway-payfast' ),
				'type'        => 'checkbox',
				'description' => __( 'This controls whether or not this gateway is enabled within WooCommerce.', 'woocommerce-gateway-payfast' ),
				'default'     => 'yes',
				'desc_tip'    => true
			),
			'title' => array(
				'title'       => __( 'Title', 'woocommerce-gateway-payfast' ),
				'type'        => 'text',
				'description' => __( 'This controls the title which the user sees during checkout.', 'woocommerce-gateway-payfast' ),
				'default'     => __( 'PayFast', 'woocommerce-gateway-payfast' ),
				'desc_tip'    => true
			),
			'description' => array(
				'title'       => __( 'Description', 'woocommerce-gateway-payfast' ),
				'type'        => 'text',
				'description' => __( 'This controls the description which the user sees during checkout.', 'woocommerce-gateway-payfast' ),
				'default'     => '',
				'desc_tip'    => true
			),
			'testmode' => array(
				'title'       => __( 'PayFast Sandbox', 'woocommerce-gateway-payfast' ),
				'type'        => 'checkbox',
				'description' => __( 'Place the payment gateway in development mode.', 'woocommerce-gateway-payfast' ),
				'default'     => 'yes'
			),
			'merchant_id' => array(
				'title'       => __( 'Merchant ID', 'woocommerce-gateway-payfast' ),
				'type'        => 'text',
				'description' => __( 'This is the merchant ID, received from PayFast.', 'woocommerce-gateway-payfast' ),
				'default'     => ''
			),
			'merchant_key' => array(
				'title'       => __( 'Merchant Key', 'woocommerce-gateway-payfast' ),
				'type'        => 'text',
				'description' => __( 'This is the merchant key, received from PayFast.', 'woocommerce-gateway-payfast' ),
				'default'     => ''
			),
			'send_debug_email' => array(
				'title'   => __( 'Send Debug Emails', 'woocommerce-gateway-payfast' ),
				'type'    => 'checkbox',
				'label'   => __( 'Send debug e-mails for transactions through the PayFast gateway (sends on successful transaction as well).', 'woocommerce-gateway-payfast' ),
				'default' => 'yes'
			),
			'debug_email' => array(
				'title'       => __( 'Who Receives Debug E-mails?', 'woocommerce-gateway-payfast' ),
				'type'        => 'text',
				'description' => __( 'The e-mail address to which debugging error e-mails are sent when in test mode.', 'woocommerce-gateway-payfast' ),
				'default'     => get_option( 'admin_email' )
			)
		);
    }

    /**
     * add_testmode_admin_settings_notice()
     * Add a notice to the merchant_key and merchant_id fields when in test mode.
     *
     * @since 1.0.0
     */
    public function add_testmode_admin_settings_notice() {
    	$this->form_fields['merchant_id']['description']  .= ' <strong>' . __( 'Sandbox Merchant ID currently in use', 'woocommerce-gateway-payfast' ) . ' ( ' . esc_html( $this->merchant_id ) . ' ).</strong>';
    	$this->form_fields['merchant_key']['description'] .= ' <strong>' . __( 'Sandbox Merchant Key currently in use', 'woocommerce-gateway-payfast' ) . ' ( ' . esc_html( $this->merchant_key ) . ' ).</strong>';
    }

    /**
     * is_valid_for_use()
     *
     * Check if this gateway is enabled and available in the base currency being traded with.
     *
     * @since 1.0.0
     */
	public function is_valid_for_use() {
		$is_available          = false;
        $is_available_currency = in_array( get_woocommerce_currency(), $this->available_currencies );

		if ( $is_available_currency && $this->merchant_id && $this->merchant_key ) {
			$is_available = true;
		}
        return $is_available;
	}

	/**
	 * Admin Panel Options
	 * - Options for bits like 'title' and availability on a country-by-country basis
	 *
	 * @since 1.0.0
	 */
	public function admin_options() {
    	if ( in_array( get_woocommerce_currency(), $this->available_currencies ) ) {
			parent::admin_options();
		} else {
    		?>
			<h3><?php _e( 'PayFast', 'woocommerce-gateway-payfast' ); ?></h3>
			<div class="inline error"><p><strong><?php _e( 'Gateway Disabled', 'woocommerce-gateway-payfast' ); ?></strong> <?php echo sprintf( __( 'Choose South African Rands as your store currency in %1$sPricing Options%2$s to enable the PayFast Gateway.', 'woocommerce-gateway-payfast' ), '<a href="' . esc_url( admin_url( 'admin.php?page=wc-settings&tab=general' ) ) . '">', '</a>' ); ?></p></div>
    		<?php
		}
    }

    /**
	 * There are no payment fields for PayFast, but we want to show the description if set.
	 *
	 * @since 1.0.0
	 */
    public function payment_fields() {
    	if ( ! $this->get_option( 'description' ) ) {
    		echo wpautop( wptexturize( $this->get_option( 'description' ) ) );
    	}
    }

	/**
	 * Generate the PayFast button link.
	 *
	 * @since 1.0.0
	 */
    public function generate_payfast_form( $order_id ) {
		$order         = wc_get_order( $order_id );
		$shipping_name = explode( ' ', $order->shipping_method );

		// Construct variables for post
	    $this->data_to_send = array(
	        // Merchant details
	        'merchant_id'      => $this->merchant_id,
	        'merchant_key'     => $this->merchant_key,
	        'return_url'       => $this->get_return_url( $order ),
	        'cancel_url'       => $order->get_cancel_order_url(),
	        'notify_url'       => $this->response_url,

			// Billing details
			'name_first'       => $order->billing_first_name,
			'name_last'        => $order->billing_last_name,
			'email_address'    => $order->billing_email,

	        // Item details
	        'm_payment_id'     => ltrim( $order->get_order_number(), __( '#', 'hash before order number', 'woocommerce-gateway-payfast' ) ),
	        'amount'           => $order->get_total(),
	    	'item_name'        => get_bloginfo( 'name' ) . ' - ' . $order->get_order_number(),
	    	'item_description' => sprintf( __( 'New order from %s', 'woocommerce-gateway-payfast' ), get_bloginfo( 'name' ) ),

	    	// Custom strings
	    	'custom_str1'      => $order->order_key,
	    	'custom_str2'      => 'WooCommerce/' . WC_VERSION . '; ' . get_site_url(),
	    	'custom_str3'      => $order->id,
	    	'source'           => 'WooCommerce-Free-Plugin'
	   	);

		$payfast_args_array = array();

		foreach( $this->data_to_send as $key => $value) {
			$payfast_args_array[] = '<input type="hidden" name="' . esc_attr( $key ) .'" value="' . esc_attr( $value ) . '" />';
		}

		return '<form action="' . esc_url( $this->url ) . '" method="post" id="payfast_payment_form">
				' . implode( '', $payfast_args_array ) . '
				<input type="submit" class="button-alt" id="submit_payfast_payment_form" value="' . __( 'Pay via PayFast', 'woocommerce-gateway-payfast' ) . '" /> <a class="button cancel" href="' . $order->get_cancel_order_url() . '">' . __( 'Cancel order &amp; restore cart', 'woocommerce-gateway-payfast' ) . '</a>
				<script type="text/javascript">
					jQuery(function(){
						jQuery("body").block(
							{
								message: "' . __( 'Thank you for your order. We are now redirecting you to PayFast to make payment.', 'woocommerce-gateway-payfast' ) . '",
								overlayCSS:
								{
									background: "#fff",
									opacity: 0.6
								},
								css: {
							        padding:        20,
							        textAlign:      "center",
							        color:          "#555",
							        border:         "3px solid #aaa",
							        backgroundColor:"#fff",
							        cursor:         "wait"
							    }
							});
						jQuery( "#submit_payfast_payment_form" ).click();
					});
				</script>
			</form>';
	}

	/**
	 * Process the payment and return the result.
	 *
	 * @since 1.0.0
	 */
	public function process_payment( $order_id ) {
		$order = wc_get_order( $order_id );
		return array(
			'result' 	 => 'success',
			'redirect'	 => $order->get_checkout_payment_url( true )
		);
	}

	/**
	 * Reciept page.
	 *
	 * Display text and a button to direct the user to PayFast.
	 *
	 * @since 1.0.0
	 */
	public function receipt_page( $order ) {
		echo '<p>' . __( 'Thank you for your order, please click the button below to pay with PayFast.', 'woocommerce-gateway-payfast' ) . '</p>';
		echo $this->generate_payfast_form( $order );
	}

	/**
	 * Check PayFast ITN validity.
	 *
	 * @param array $data
	 * @since 1.0.0
	 */
	public function handle_itn_request( $data ) {
		$pfError        = false;
		$pfDone         = false;
		$pfDebugEmail   = $this->get_option( 'debug_email', get_option( 'admin_email' ) );
		$sessionid      = $data['custom_str1'];
        $transaction_id = $data['pf_payment_id'];
        $vendor_name    = get_bloginfo( 'name' );
        $vendor_url     = home_url( '/' );
		$order_id       = absint( $data['custom_str3'] );
		$order_key      = wc_clean( $sessionid );
		$order          = wc_get_order( $order_id );
		$data_string    = '';
		$data_array     = array();

		// Dump the submitted variables and calculate security signature
	    foreach( $data as $key => $val ) {
	    	if ( $key !== 'signature' ) {
	    		$data_string       .= $key . '=' . urlencode( $val ) . '&';
	    		$data_array[ $key ] = $val;
	    	}
	    }

	    // Remove the last '&' from the parameter string
	    $data_string = substr( $data_string, 0, -1 );
	    $signature   = md5( $data_string );

		$this->log( "\n" . '----------' . "\n" . 'PayFast ITN call received' );

		// Notify PayFast that information has been received
        if ( ! $pfError && ! $pfDone ) {
            header( 'HTTP/1.0 200 OK' );
            flush();
        }

        // Get data sent by PayFast
        if ( ! $pfError && ! $pfDone ) {
        	$this->log( 'Get posted data' );
            $this->log( 'PayFast Data: '. print_r( $data, true ) );

            if ( $data === false ) {
                $pfError  = true;
                $pfErrMsg = PF_ERR_BAD_ACCESS;
            }
        }

        // Verify security signature
        if ( ! $pfError && ! $pfDone ) {
            $this->log( 'Verify security signature' );

            // If signature different, log for debugging
            if( ! $this->validate_signature( $data, $signature ) ) {
                $pfError  = true;
                $pfErrMsg = PF_ERR_INVALID_SIGNATURE;
            }
        }

        // Verify source IP (If not in debug mode)
        if ( ! $pfError && ! $pfDone && $this->get_option( 'testmode' ) != 'yes' ) {
            $this->log( 'Verify source IP' );

            if( ! $this->validate_ip( $_SERVER['REMOTE_ADDR'] ) ) {
                $pfError  = true;
                $pfErrMsg = PF_ERR_BAD_SOURCE_IP;
            }
        }

        // Get internal order and verify it hasn't already been processed
        if ( ! $pfError && ! $pfDone ) {
            $this->log( "Purchase:\n". print_r( $order, true ) );

            // Check if order has already been processed
            if( $order->status === 'completed' ) {
                $this->log( 'Order has already been processed' );
                $pfDone = true;
            }
        }

        // Verify data received
        if( ! $pfError ) {
            $this->log( 'Verify data received' );

            $pfValid = $this->validate_response_data( $data_array );

            if( ! $pfValid ) {
                $pfError = true;
                $pfErrMsg = PF_ERR_BAD_ACCESS;
            }
        }

        // Check data against internal order
        if( ! $pfError && ! $pfDone ) {
            $this->log( 'Check data against internal order' );

            // Check order amount
            if ( ! $this->amounts_equal( $data['amount_gross'], $order->order_total ) ) {
                $pfError  = true;
                $pfErrMsg = PF_ERR_AMOUNT_MISMATCH;
            }
            // Check session ID
            elseif( strcasecmp( $data['custom_str1'], $order->order_key ) != 0 ) {
                $pfError  = true;
                $pfErrMsg = PF_ERR_SESSIONID_MISMATCH;
            }
        }

        // If an error occurred
        if ( $pfError ) {
            $this->log( 'Error occurred: '. $pfErrMsg );

            if ( $this->send_debug_email ) {
	            $this->log( 'Sending email notification' );

	             // Send an email
	            $subject = "PayFast ITN error: ". $pfErrMsg;
	            $body =
	                "Hi,\n\n".
	                "An invalid PayFast transaction on your website requires attention\n".
	                "------------------------------------------------------------\n".
	                "Site: ". $vendor_name ." (". $vendor_url .")\n".
	                "Remote IP Address: ".$_SERVER['REMOTE_ADDR']."\n".
	                "Remote host name: ". gethostbyaddr( $_SERVER['REMOTE_ADDR'] ) ."\n".
	                "Purchase ID: ". $order->id ."\n".
	                "User ID: ". $order->user_id ."\n";
	            if( isset( $data['pf_payment_id'] ) )
	                $body .= "PayFast Transaction ID: ". $data['pf_payment_id'] ."\n";
	            if( isset( $data['payment_status'] ) )
	                $body .= "PayFast Payment Status: ". $data['payment_status'] ."\n";
	            $body .=
	                "\nError: ". $pfErrMsg ."\n";

	            switch( $pfErrMsg ) {
	                case PF_ERR_AMOUNT_MISMATCH:
	                    $body .=
	                        "Value received : ". $data['amount_gross'] ."\n".
	                        "Value should be: ". $order->order_total;
	                    break;

	                case PF_ERR_ORDER_ID_MISMATCH:
	                    $body .=
	                        "Value received : ". $data['custom_str3'] ."\n".
	                        "Value should be: ". $order->id;
	                    break;

	                case PF_ERR_SESSION_ID_MISMATCH:
	                    $body .=
	                        "Value received : ". $data['custom_str1'] ."\n".
	                        "Value should be: ". $order->id;
	                    break;

	                // For all other errors there is no need to add additional information
	                default:
	                    break;
	            }

	            wp_mail( $pfDebugEmail, $subject, $body );
            }
        } elseif ( ! $pfDone ) {

			$this->log( 'Check status and update order' );

			if ( $order->order_key !== $order_key ) {
				exit;
			}

    		switch ( strtolower( $data['payment_status'] ) ) {
                case 'complete':
                    $this->log( '- Complete' );
					$order->add_order_note( __( 'ITN payment completed', 'woocommerce-gateway-payfast' ) );
					$order->payment_complete();

                    if ( $this->send_debug_email ) {
                        $subject = "PayFast ITN on your site";
                        $body =
                            "Hi,\n\n".
                            "A PayFast transaction has been completed on your website\n".
                            "------------------------------------------------------------\n".
                            "Site: ". $vendor_name ." (". $vendor_url .")\n".
                            "Purchase ID: ". $data['m_payment_id'] ."\n".
                            "PayFast Transaction ID: ". $data['pf_payment_id'] ."\n".
                            "PayFast Payment Status: ". $data['payment_status'] ."\n".
                            "Order Status Code: ". $order->status;
                        wp_mail( $pfDebugEmail, $subject, $body );
                    }
                break;
    			case 'failed':
                    $this->log( '- Failed' );
                    $order->update_status( 'failed', sprintf( __( 'Payment %s via ITN.', 'woocommerce-gateway-payfast' ), strtolower( sanitize_text_field( $data['payment_status'] ) ) ) );

					if ( $this->send_debug_email ) {
	                    $subject = "PayFast ITN Transaction on your site";
	                    $body =
	                        "Hi,\n\n".
	                        "A failed PayFast transaction on your website requires attention\n".
	                        "------------------------------------------------------------\n".
	                        "Site: ". $vendor_name ." (". $vendor_url .")\n".
	                        "Purchase ID: ". $order->id ."\n".
	                        "User ID: ". $order->user_id ."\n".
	                        "PayFast Transaction ID: ". $data['pf_payment_id'] ."\n".
	                        "PayFast Payment Status: ". $data['payment_status'];
	                    wp_mail( $pfDebugEmail, $subject, $body );
                    }
        			break;
    			case 'pending':
                    $this->log( '- Pending' );
                    // Need to wait for "Completed" before processing
        			$order->update_status( 'on-hold', sprintf( __( 'Payment %s via ITN.', 'woocommerce-gateway-payfast' ), strtolower( sanitize_text_field( $data['payment_status'] ) ) ) );
        			break;
    			default:
                    // If unknown status, do nothing (safest course of action)
    			break;
            }
		}

    	return $pfError;
    }

	/**
	 * Check PayFast ITN response.
	 *
	 * @since 1.0.0
	 */
	public function check_itn_response() {
		$this->handle_itn_request( stripslashes_deep( $_POST ) );
	}

	/**
	 * Setup constants.
	 *
	 * Setup common values and messages used by the PayFast gateway.
	 *
	 * @since 1.0.0
	 */
	public function setup_constants() {
		//// Create user agent string
		define( 'PF_SOFTWARE_NAME', 'WooCommerce' );
		define( 'PF_SOFTWARE_VER', WC_VERSION );
		define( 'PF_MODULE_NAME', 'WooCommerce-PayFast-Free' );
		define( 'PF_MODULE_VER', $this->version );

		// Features
		// - PHP
		$pfFeatures = 'PHP ' . phpversion() .';';

		// - cURL
		if ( in_array( 'curl', get_loaded_extensions() ) ) {
		    define( 'PF_CURL', '' );
		    $pfVersion = curl_version();
		    $pfFeatures .= ' curl '. $pfVersion['version'] .';';
		} else {
		    $pfFeatures .= ' nocurl;';
		}

		// Create user agrent
		define( 'PF_USER_AGENT', PF_SOFTWARE_NAME .'/'. PF_SOFTWARE_VER .' ('. trim( $pfFeatures ) .') '. PF_MODULE_NAME .'/'. PF_MODULE_VER );

		// General Defines
		define( 'PF_TIMEOUT', 15 );
		define( 'PF_EPSILON', 0.01 );

		// Messages
		// Error
		define( 'PF_ERR_AMOUNT_MISMATCH', __( 'Amount mismatch', 'woocommerce-gateway-payfast' ) );
		define( 'PF_ERR_BAD_ACCESS', __( 'Bad access of page', 'woocommerce-gateway-payfast' ) );
		define( 'PF_ERR_BAD_SOURCE_IP', __( 'Bad source IP address', 'woocommerce-gateway-payfast' ) );
		define( 'PF_ERR_CONNECT_FAILED', __( 'Failed to connect to PayFast', 'woocommerce-gateway-payfast' ) );
		define( 'PF_ERR_INVALID_SIGNATURE', __( 'Security signature mismatch', 'woocommerce-gateway-payfast' ) );
		define( 'PF_ERR_MERCHANT_ID_MISMATCH', __( 'Merchant ID mismatch', 'woocommerce-gateway-payfast' ) );
		define( 'PF_ERR_NO_SESSION', __( 'No saved session found for ITN transaction', 'woocommerce-gateway-payfast' ) );
		define( 'PF_ERR_ORDER_ID_MISSING_URL', __( 'Order ID not present in URL', 'woocommerce-gateway-payfast' ) );
		define( 'PF_ERR_ORDER_ID_MISMATCH', __( 'Order ID mismatch', 'woocommerce-gateway-payfast' ) );
		define( 'PF_ERR_ORDER_INVALID', __( 'This order ID is invalid', 'woocommerce-gateway-payfast' ) );
		define( 'PF_ERR_ORDER_NUMBER_MISMATCH', __( 'Order Number mismatch', 'woocommerce-gateway-payfast' ) );
		define( 'PF_ERR_ORDER_PROCESSED', __( 'This order has already been processed', 'woocommerce-gateway-payfast' ) );
		define( 'PF_ERR_PDT_FAIL', __( 'PDT query failed', 'woocommerce-gateway-payfast' ) );
		define( 'PF_ERR_PDT_TOKEN_MISSING', __( 'PDT token not present in URL', 'woocommerce-gateway-payfast' ) );
		define( 'PF_ERR_SESSIONID_MISMATCH', __( 'Session ID mismatch', 'woocommerce-gateway-payfast' ) );
		define( 'PF_ERR_UNKNOWN', __( 'Unkown error occurred', 'woocommerce-gateway-payfast' ) );

		// General
		define( 'PF_MSG_OK', __( 'Payment was successful', 'woocommerce-gateway-payfast' ) );
		define( 'PF_MSG_FAILED', __( 'Payment has failed', 'woocommerce-gateway-payfast' ) );
		define( 'PF_MSG_PENDING',
		    __( 'The payment is pending. Please note, you will receive another Instant', 'woocommerce-gateway-payfast' ).
		    __( ' Transaction Notification when the payment status changes to', 'woocommerce-gateway-payfast' ).
		    __( ' "Completed", or "Failed"', 'woocommerce-gateway-payfast' ) );

		do_action( 'woocommerce_gateway_payfast_setup_constants' );
	}

	/**
	 * Log system processes.
	 * @since 1.0.0
	 */
	public function log( $message ) {
		if ( 'yes' === $this->get_option( 'testmode' ) ) {
			if ( ! $this->logger ) {
				$this->logger = new WC_Logger();
			}
			$this->logger->add( 'payfast', $message );
		}
	}

	/**
	 * validate_signature()
	 *
	 * Validate the signature against the returned data.
	 *
	 * @param array $data
	 * @param string $signature
	 * @since 1.0.0
	 */
	public function validate_signature ( $data, $signature ) {
	    $result = $data['signature'] === $signature;
	    $this->log( 'Signature = '. ( $result ? 'valid' : 'invalid' ) );
	    return $result;
	}

	/**
	 * validate_ip()
	 *
	 * Validate the IP address to make sure it's coming from PayFast.
	 *
	 * @param array $data
	 * @since 1.0.0
	 */
	public function validate_ip( $sourceIP ) {
	    // Variable initialization
	    $validHosts = array(
	        'www.payfast.co.za',
	        'sandbox.payfast.co.za',
	        'w1w.payfast.co.za',
	        'w2w.payfast.co.za',
	    );

	    $validIps = array();

	    foreach( $validHosts as $pfHostname ) {
	        $ips = gethostbynamel( $pfHostname );

	        if ( $ips !== false ) {
	            $validIps = array_merge( $validIps, $ips );
			}
	    }

	    // Remove duplicates
	    $validIps = array_unique( $validIps );

	    $this->log( "Valid IPs:\n". print_r( $validIps, true ) );

	    return in_array( $sourceIP, $validIps );
	}

	/**
	 * validate_response_data()
	 *
	 * @param $post_data String Parameter string to send
	 * @param $proxy String Address of proxy to use or NULL if no proxy
	 * @since 1.0.0
	 */
	public function validate_response_data( $post_data, $pfProxy = null ) {
	    $this->log( 'Host = '. $this->validate_url );
	    $this->log( 'Params = '. print_r( $post_data, true ) );

		if ( ! is_array( $post_data ) ) {
			return false;
		}

		$response = wp_remote_post( $this->validate_url, array(
			'body'       => $post_data,
			'timeout'    => 70,
			'user-agent' => PF_USER_AGENT
		));

		if ( is_wp_error( $response ) || empty( $response['body'] ) ) {
			return false;
		}

		parse_str( $response['body'], $parsed_response );

		$response = $parsed_response;

	    $this->log( "Response:\n" . print_r( $response, true ) );

	    // Interpret Response
	    if ( is_array( $response ) && in_array( 'VALID', array_keys( $response ) ) ) {
	    	return true;
	    } else {
	    	return false;
	    }
	}

	/**
	 * amounts_equal()
	 *
	 * Checks to see whether the given amounts are equal using a proper floating
	 * point comparison with an Epsilon which ensures that insignificant decimal
	 * places are ignored in the comparison.
	 *
	 * eg. 100.00 is equal to 100.0001
	 *
	 * @author Jonathan Smit
	 * @param $amount1 Float 1st amount for comparison
	 * @param $amount2 Float 2nd amount for comparison
	 * @since 1.0.0
	 */
	public function amounts_equal ( $amount1, $amount2 ) {
		return ! ( abs( floatval( $amount1 ) - floatval( $amount2 ) ) > PF_EPSILON );
	}
}
