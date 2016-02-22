<?php

/**
 * Custom Card Gateway Class
 */

class E_Versatile_Custom_Card_Gateway extends WC_Payment_Gateway {

  /**
   * E_Versatile_Custom_Card_Gateway constructor.
   */
  public function __construct() {
    $this->id          = 'e_versatile_custom_card';
    $this->title       = 'E Versatile Custom Card';
    $this->description = 'Gateway description';
    $this->has_fields  = true;
    $this->init_form_fields();
    $this->init_settings();
    add_action( 'woocommerce_update_options_payment_gateways_' . $this->id, array( $this, 'process_admin_options' ) );

    spl_autoload_register( array( $this, 'autoload') );
  }

  /**
   * Autoload Stripe library classes
   *
   * @param string $cls
   */
  function autoload( $cls ) {
    $cls = ltrim( $cls, '\\' );
    if ( strpos( $cls, 'Stripe' ) !== 0 )
      return;

    $path = str_replace( '\\', DIRECTORY_SEPARATOR, $cls ) . '.php';
    require_once( $path );
  }

  /**
   * Initialise Gateway Settings Form Fields
   * Store any personal information here
   */
  public function init_form_fields() {
    $this->form_fields = array(
      'secret_key' => array(
        'title'       => 'Secret Key',
        'type'        => 'text',
        'description' => 'Get your API keys from your stripe account.',
        'default'     => ''
      )
    );
  }

  /**
   * Echo HTML to be displayed in the cart checkout
   */
  public function payment_fields() {

    if ( $this->description ) {
      echo '<p>' . wp_kses_post( $this->description ) . '</p>';
    }

  }

  /**
   * Process the payment
   *
   * @param $order_id
   */
  public function process_payment( $order_id ) {
    $order = wc_get_order( $order_id );

    // set secret key
    \Stripe\Stripe::setApiKey( $this->get_option( 'secret_key' ) );

    // process Stripe payment

  }

}


/**
 * Check current logged in user, conditionally load gateway
 *
 * @param $methods
 * @return array
 */
function add_e_versatile_custom_card_gateway( $methods ) {
  if( get_current_user_id() === 1 ){
    $methods[] = 'E_Versatile_Custom_Card_Gateway';
    return $methods;
  }
}

add_filter( 'woocommerce_payment_gateways', 'add_e_versatile_custom_card_gateway' );