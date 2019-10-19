<?php
/**
 * Plugin Name:     WooCommerce Sucursales
 * Description:     Plugin de sucursales
 * Author:          Feroz Digital - Jesus Marcano
 * Author URI:      https://ferozdigital.cl
 * Text Domain:     branch
 * Domain Path:     /languages
 * Version:         0.1.0
 *
 * @package         Branch
 */

  if ( ! defined( 'WPINC' ) ) {
      die;
  }
  require(plugin_dir_path(__FILE__).'inc/taxonomy/branch.php');
  /**
  * Check if WooCommerce is active
  */
 if ( in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) {
 	function branch_shipping_method_init() {
    require(plugin_dir_path(__FILE__).'inc/Branch.php');
 		if ( ! class_exists( 'Branch_Shipping_Method' ) ) {
 			class Branch_Shipping_Method extends WC_Shipping_Method {
 				/**
 				 * Constructor for your shipping class
 				 *
 				 * @access public
 				 * @return void
 				 */
 				public function __construct() {
 					$this->id                 = 'branch_shipping_method'; // Id for your shipping method. Should be uunique.
 					$this->method_title       = __( 'Branch Shipping' );  // Title shown in admin
 					$this->method_description = __( 'Branch Activado' ); // Description shown in admin
 					$this->enabled            = "yes"; // This can be added as an setting but for this example its forced enabled
 					$this->title              = "Retiro en sucursal"; // This can be added as an setting but for this example its forced.
           $this->countries = array("CL");
 					$this->init();
 				}
 				/**
 				 * Init your settings
 				 *
 				 * @access public
 				 * @return void
 				 */
 				function init() {
 					// Load the settings API
 					$this->init_form_fields(); // This is part of the settings API. Override the method to add your own settings
 					$this->init_settings(); // This is part of the settings API. Loads settings you previously init.
 					// Save settings in admin if you have any defined
 					add_action( 'woocommerce_update_options_shipping_' . $this->id, array( $this, 'process_admin_options' ) );
 				}
 				/**
 				 * calculate_shipping function.
 				 *
 				 * @access public
 				 * @param mixed $package
 				 * @return void
 				 */
 				public function calculate_shipping( $package = array() ) {
           global $woocommerce;
 					$rate = array(
 						'id' => $this->id,
 						'label' => $this->title,
 						'cost' => 0,
 						'calc_tax' => 'per_item'
 					);
 					// Register the rate
 					$this->add_rate( $rate );
 				}
 			}
 		}
 	}
 	add_action( 'woocommerce_shipping_init', 'branch_shipping_method_init' );
 	function add_branch_shipping_method( $methods ) {
 		$methods['branch_shipping_method'] = 'Branch_Shipping_Method';
 		return $methods;
 	}
 	add_filter( 'woocommerce_shipping_methods', 'add_branch_shipping_method' );

   function branch_validate_order( $posted )   {
     if( WC()->session->get('chosen_shipping_methods')[0] == 'branch_shipping_method') {
       if(false){
         $message = sprintf( __( 'Sorry, debes especificar el envio %s', 'branch' ), '');
         $messageType = "error";
         if( ! wc_has_notice( $message, $messageType ) ) {
           wc_add_notice( $message, $messageType );
         }
       }
     }
   }

   add_action( 'woocommerce_review_order_before_cart_contents', 'branch_validate_order' , 10 );
   add_action( 'woocommerce_after_checkout_validation', 'branch_validate_order' , 10 );
   /**
    * Save the custom field at shipping calculator.
    */
   function branch_shipping_calculator_field_process() {
     $area = isset( $_POST['calc_shipping_branch'] ) ? $_POST['calc_shipping_branch'] : '';
   	if ( $area ) {
   		WC()->session->set( 'shipping_branch', $area );
     	WC()->customer->set_shipping_postcode( $area );
   	}
   }
   add_action( 'woocommerce_calculated_shipping', 'branch_shipping_calculator_field_process' );
 }


 function branch_calculate_form(){
   // Ensure to get the correct value here. This __get( 'shipping_area' ) is based on how the Advanced Checkout Fields plugin would work
   $current_area=0;
   if(WC()->session->get( 'shipping_branch' )!==null){
     $current_area = WC()->session->get( 'shipping_branch' );
   }

   ?>
     <p class="form-row form-row-wide" id="calc_shipping_branch_field">
       <?php
       $branch = new Branch();
       $communes = [];
       $current_commune = 0 ;

       $communes = $branch->branchs();
       if(WC()->session->get( 'shipping_branch' )!==null){
         $current_commune = WC()->session->get( 'shipping_branch' );
       }
       ?>
       <br>
       <label>Sucursal para retiro</label>
       <select name="calc_shipping_branch" id="calc_shipping_branch" class="country_select" rel="calc_shipping_branch">
         <option value="">Selecciona una sucursal</option>
         <?php
         echo $current_commune;
         foreach($communes as $commune):?>
         <option value="<?php echo $commune->id;?>" <?php selected( $current_area, $commune->id ); ?>><?php echo $commune->name;?></option>
         <?php endforeach;?>
       </select>
     </p>
     <?php
 }

 add_action('woocommerce_branch_form','branch_calculate_form');
 add_action('woocommerce_thankyou', 'branch_request', 10, 1);
 function branch_request( $order_id ) {
   if(WC()->session->get('chosen_shipping_methods')[0]=='branch_shipping_method'){
     global $_product;
     if ( ! $order_id )
         return;
     $order = wc_get_order( $order_id );
     if($order->is_paid())
         $paid = 'yes';
     else
         $paid = 'no';

     // Ouptput some data
     $customer = new WC_Customer();
     $shipping = 0;
     $i=0;
    ?>
    <h2>Retiro en sucursal</h2>
    <table class="woocommerce-table shop_table gift_info">
        <tbody>
          <tr>
              <th>Sucursal</th>
              <td><?php echo Branch::get(WC()->session->get( 'shipping_branch')); ?></td>
          </tr>
        </tbody>
    </table>
       <?php
   }
 }


 function branch_locate_template( $template, $template_name, $template_path ) {
  $basename = basename( $template );
  if( $basename == 'cart-shipping.php' ) {
    $template = trailingslashit( plugin_dir_path( __FILE__ ) ) . 'woocommerce/cart/cart-shipping.php';
  }
  if( $basename == 'shipping-calculator.php' ) {
    $template = trailingslashit( plugin_dir_path( __FILE__ ) ) . 'woocommerce/cart/shipping-calculator.php';
  }
  if( $basename == 'cart.php' ) {
    //$template = trailingslashit( plugin_dir_path( __FILE__ ) ) . 'woocommerce/cart/cart.php';
  }
  if( $basename == 'product-image.php' ) {
    $template = trailingslashit( plugin_dir_path( __FILE__ ) ) . 'woocommerce/single-product/product-image.php';
  }
  return $template;
 }
// add_filter( 'woocommerce_locate_template', 'branch_locate_template', 10, 3 );


 add_filter( 'woocommerce_shipping_calculator_enable_branch', 'branch_enable_fields' );

// add_filter( 'woocommerce_shipping_calculator_enable_state', 'branch_disable_fields' );
// add_filter( 'woocommerce_shipping_calculator_enable_city', 'branch_disable_fields' );
 add_filter( 'woocommerce_shipping_calculator_enable_postcode', 'branch_disable_fields' );
 //add_filter( 'woocommerce_shipping_calculator_enable_country', 'disable_fields' );
 function branch_disable_fields( $true ){
   if(WC()->session->get('chosen_shipping_methods')[0]=='branch_shipping_method'){
     return false;
   }
   return true;
 }

 function branch_enable_fields( $true ){
   if(WC()->session->get('chosen_shipping_methods')[0]=='branch_shipping_method'){
     return true;
   }
   return false;
 }


 // Hook in
 add_filter( 'woocommerce_checkout_fields' , 'branch_override_checkout_fields' );

 // Our hooked in function - $fields is passed via the filter!
 function branch_override_checkout_fields( $fields ) {
      //unset($fields['billing']['billing_city']);
      unset($fields['billing']['billing_state']);
      unset($fields['billing']['billing_postcode']);
      //unset($fields['billing']['billing_company']);
      return $fields;
 }

 add_action( 'woocommerce_after_checkout_billing_form', 'branch_checkout_field' );

 function branch_checkout_field( ) {
   $checkout = WC()->checkout;

   if(WC()->session->get('chosen_shipping_methods')[0]=='branch_shipping_method'){
     echo '<div id="my_custom_checkout_field"><h2>' . __('Sucursal para retiro') . '</h2>';
     $branch = new Branch();
     $communes = $branch->branchs();
     $f = [];
     foreach($communes as $commune){
       $f[$commune->id] = $commune->name;
     }
     $fields['shipping_options']=woocommerce_form_field( 'shipping_branch', array(
        'type' => 'select',
        'label'     => 'Sucursal para retirar ',
        'placeholder'   => 'Sucursal',
        'required'  => true,
        'class' => array('delivery_method form-row-wide'),
        'input_class'=> array('country_select'),
        'clear'     => true,
        'options' => $f
      ), WC()->session->get( 'shipping_branch'));


     //echo '</div>';
   }
 }

 /*
 add_filter('woocommerce_billing_fields', 'custom_woocommerce_billing_fields');

 function custom_woocommerce_billing_fields($fields){

     $fields['billing_branch'] = array(
         'label' => __('RUT', 'woocommerce'), // Add custom field label
         'placeholder' => _x('', 'placeholder', 'woocommerce'), // Add custom field placeholder
         'required' => true, // if field is required or not
         'clear' => false, // add clear or not
         'type' => 'text', // add field type
         'priority' => 1,
         'class' => array('my-css')    // add class name
     );

     return $fields;
 }*/

 add_action( 'wp_footer', 'branch_custom_checkout_script' );
 function branch_custom_checkout_script() {
     // Only on checkout page
     if( ! is_checkout() && is_wc_endpoint_url( 'order-received' ) )
         return;
     wp_register_script( 'branch_ajax', plugins_url( '/assets/js/branch.js', __FILE__ ), array('jquery'), '1.0', true );
     wp_localize_script('branch_ajax', 'wp_branch_vars', array(
         'ajaxurl' => admin_url( 'admin-ajax.php' )
     ));
     wp_enqueue_script( 'branch_ajax' );
 }



 add_action('wp_ajax_nopriv_branch_update_field', 'branch_update_field');
 // Hook para usuarios logueados
 add_action('wp_ajax_branch_update_field', 'branch_update_field');
 // Función que procesa la llamada AJAX
 function branch_update_field(){
     // Check parameters
     WC()->session->set( 'shipping_branch', $_POST['branch_id'] );
     WC()->customer->set_shipping_postcode( $_POST['branch_id'] );
 }

 add_filter('woocommerce_before_checkout_form', 'branch_custom_message_checkout',10, 1 );

 function branch_custom_message_checkout($wccm_autocreate_account){
   $state = false;
   foreach ( WC()->cart->get_cart() as $cart_item ){
     $product = $cart_item['data'];
     $shipclass=$product->get_shipping_class();
     if($shipclass=='retirotienda' && WC()->session->get('chosen_shipping_methods')[0]=='branch_shipping_method'){
       $state=true;
       break;
     }
   }
   if($state):
   ?>
   <style>
   .branch-items-checkout{
     list-style-type:none;
     padding:0px;
     margin:0px;
     overflow:auto;
   }
   .branch-items-checkout li{
     float:left;
     display:block;
     border:1px solid silver;
     margin:10px;
     padding:10px;
     border-radius:5px;
     background:#ddd;
     color:#333;
   }
   .branch-items-checkout li img{
     margin:0 auto;
     display:block;
   }
   </style>
   <div class="woocommerce-message" role="alert">
     Recuerda estos items tienen que ser retirados en tienda
     <ul class="branch-items-checkout">
       <?php foreach ( WC()->cart->get_cart() as $cart_item ):?>
         <?php $product = $cart_item['data'];
         if(!empty($product)){
           $shipclass=$product->get_shipping_class();
           if($shipclass=='retirotienda'){
           ?>
           <li>
             <?php echo $product->get_image([100,100],[],true);?>
             <br>
             <?php echo $product->get_title();?>
           </li>
           <?php
           }
         }
         ?>
       <?php endforeach;?>
     </ul>
   </div>
   <?php
   endif;
 }
