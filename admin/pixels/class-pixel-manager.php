<?php
/**
 * The admin-specific functionality of the plugin.
 *
 * @link       
 * @since      1.0.0
 *
 * @package    
 * @package    PMW_Pixel
 * PixelManagerDataLayer, PixelManagerOptions
 */
if (!defined('ABSPATH')) {
  exit; // Exit if accessed directly
}

if(!class_exists('PMW_PixelManager')):
  class PMW_PixelManager extends PMW_Pixel {
    protected $options;
    protected $PixelItemFunction;
    public $PixelManagerDataLayer = array();
    public $PTMEnhanceConversionData;
    protected $version;
    protected $PMW_AdminHelper;
    protected $api_store;
    protected $is_send_sku;
    public function __construct( $options ){
      $this->version = PIXEL_MANAGER_FOR_WOOCOMMERCE_VERSION;
      $this->options = $options;
      $this->req_int();
      $this->PMW_AdminHelper = new PMW_AdminHelper();
      $this->api_store = (object)$this->PMW_AdminHelper->get_pmw_api_store();
      $this->PixelItemFunction = new PMW_PixelItemFunction();
      $this->is_send_sku = $this->is_send_sku();
      add_action( 'wp_head', array( $this, 'init_in_wp_head') , 120);

      if($this->is_woocommerce_active()){
        add_action("wp_footer", array($this, "PMW_create_products_data_object"));        
      }else{
        add_action("wp_footer", array($this, "PMW_JS_Call"));
      }

     // add_filter( 'woocommerce_related_products_args', 'PMW_woocommerce_add_related_to_loop' );
     // add_filter( 'woocommerce_output_related_products_args', 'PMW_woocommerce_add_related_to_loop' );
      
     // add_filter( 'woocommerce_related_products_columns', 'PMW_woocommerce_add_related_to_loop' );
      //add_filter( 'woocommerce_cross_sells_columns', 'PMW_woocommerce_add_cross_sell_to_loop' );
     // add_filter( 'woocommerce_upsells_columns', 'PMW_woocommerce_add_upsells_to_loop' );

      
      add_action( 'wp_enqueue_scripts', array($this,'enqueue_scripts'));

      add_action('wp_ajax_pmw_call_facebook_converstion_api', array($this, 'pmw_call_facebook_converstion_api'));
      add_action('wp_ajax_nopriv_pmw_call_facebook_converstion_api', array($this, 'pmw_call_facebook_converstion_api'));
        
    }

    public function req_int(){
      if (!class_exists('PMW_PixelItemFunction')) {
        require_once('class-pixel-item-function.php');
      }
      if (!class_exists('PMW_AdminHelper')) {
        require_once(PIXEL_MANAGER_FOR_WOOCOMMERCE_DIR . 'admin/helper/class-pmw-admin-helper.php');
      }
    }
    public function enqueue_scripts() {
      wp_enqueue_script("pmw-pixel-manager.js", esc_url_raw(PIXEL_MANAGER_FOR_WOOCOMMERCE_URL . '/admin/pixels/js/pixel-manager.js'), array('jquery'), $this->version, false);
    }

    public function init_in_wp_head(){
      $this->inject_option_data_layer();     
      $this->inject_gtm_data_layer();
      if($this->is_woocommerce_active()){
        $this->PMW_woocommerce_inject_data_layer_product();
      }      
    }

    public function inject_gtm_data_layer(){
      $gtm_container_id = "GTM-MCCBWXSG";
      if(isset($this->options["axeptio"]["project_id"]) && isset($this->options["axeptio"]["is_enable"]) && $this->options["axeptio"]["project_id"] != "" && $this->options["axeptio"]["is_enable"]){
        $gtm_container_id = "GTM-5VKL3CXJ";
      }
      ?><!-- Google Tag Manager -->
<script>let ptm_gtm_container_id = '<?php echo esc_attr($gtm_container_id); ?>'; (function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
})(window,document,'script','dataLayer',ptm_gtm_container_id);
  document.addEventListener('DOMContentLoaded', function () {
    // Create a new noscript element
    var noscriptElement = document.createElement('noscript');
    // Create a new iframe element for the GTM noscript tag
    var iframeElement = document.createElement('iframe');
    iframeElement.src = 'https://www.googletagmanager.com/ns.html?id='+ptm_gtm_container_id;
    iframeElement.height = '0';
    iframeElement.width = '0';
    iframeElement.style.display = 'none';
    // Append the iframe to the noscript element
    noscriptElement.appendChild(iframeElement);
    // Append the noscript element to the body
    document.body.insertBefore(noscriptElement, document.body.firstChild);
  });
</script>
<!-- End Google Tag Manager -->
      <?php
    }
    /*setting DataLayer */
    public function inject_option_data_layer(){
      unset($this->options["privecy_policy"]);
      unset($this->options["user"]);
      $set_options = $this->options;
      if(isset($set_options["fb_conversion_api"]["api_token"])){
        unset($set_options["fb_conversion_api"]["api_token"]);
      }
      $EventOptions = array(
        "time" => strtotime("now")
      );
      ?>
    <script type="text/javascript" data-pagespeed-no-defer data-cfasync="false">
      var pmw_f_ajax_url = '<?php echo esc_url_raw(admin_url( 'admin-ajax.php' )); ?>';
      window.PixelManagerOptions = window.PixelManagerOptions || [];
      window.PixelManagerOptions = <?php echo json_encode($set_options); ?>;
      window.PixelManagerEventOptions = <?php echo json_encode($EventOptions); ?>;
    </script>
      <?php
    }
    /*public function PMW_woocommerce_add_related_to_loop($arg){
      global $woocommerce_loop;
      $woocommerce_loop['listtype'] = __( 'Related Products', 'pixel-manager-for-woocommerce' );
      exit;
      return $arg;
    }
    function PMW_woocommerce_add_cross_sell_to_loop( $arg ) {
      global $woocommerce_loop;
      $woocommerce_loop['listtype'] = __( 'Cross-Sell Products', 'pixel-manager-for-woocommerce' );
      return $arg;
    }
    function PMW_woocommerce_add_upsells_to_loop( $arg ) {
      global $woocommerce_loop;
      $woocommerce_loop['listtype'] = __( 'Upsell Products', 'pixel-manager-for-woocommerce' );
      return $arg;
    }*/
    public function PMW_JS_Call(){
      ?>
      <script type="text/javascript" data-pagespeed-no-defer data-cfasync="false">
        window.addEventListener('load', call_view_wordpress_js,true);
        function call_view_wordpress_js(){              
          var PMW_JS = new PMW_PixelManagerJS("", false, false);
        }        
      </script>
      <?php
    }
    /**
     * print PixelManagerDataLayer
     **/
    public function PMW_create_products_data_object(){
      $this->PixelManagerDataLayer['currency'] = get_woocommerce_currency();
      ?>
      <script type="text/javascript" data-pagespeed-no-defer data-cfasync="false">
        window.PixelManagerDataLayer = window.PixelManagerDataLayer || [];
        window.PixelManagerDataLayer.push({data:<?php echo json_encode($this->PixelManagerDataLayer); ?>});
        window.PTMEnhanceConversionData = <?php echo json_encode($this->PTMEnhanceConversionData); ?>;
        /**
         * start GTM for Google Analytics with GTM
         **/
        window.addEventListener('load', call_ga4_data_layer,true);
        function call_ga4_data_layer(){ 
          var PMW_JS = new PMW_PixelManagerJS();
        }
      </script>
      <?php      
    }
    public function PMW_woocommerce_inject_data_layer_product(){
      if ( is_order_received_page() ) {
        if( $this->PixelItemFunction->get_order_from_order_received_page() ) {
          $order = $this->PixelItemFunction->get_order_from_order_received_page();        
          $order_items = $order->get_items();
          /*if( is_user_logged_in() ) {
            $user = get_current_user_id();
          }else{
            $user = $order->get_billing_email();
          }*/
          if(!empty($order_items)){
            foreach((array)$order_items as $order_item){
              $product_id = $this->PixelItemFunction->get_variation_id_or_product_id($order_item->get_data(), true);
              $product = wc_get_product( $product_id );
              $data = $this->PixelItemFunction->get_product_details_for_datalayer($product, "", $this->is_send_sku);
              
              $data['quantity'] = (int)$order_item['quantity'];
              $this->PixelManagerDataLayer['checkout']['cart_product_list'][$product_id] = $data;
            }
          }
          $coupon = $order->get_coupon_codes();
          $coupon = (is_array($coupon) && !empty($coupon))?$coupon[0]:"";
          $order_data = array(
            "id"              => $order->get_order_number(),
            //"total"         => $order->get_total(),
            "total"           => number_format((float)$this->get_order_total("order_received", $order),2,'.',''),
            "discount"        => number_format((float)$order->get_total_discount(),2,'.',''),
            "tax"             => number_format((float)$order->get_total_tax(),2,'.',''),
            "shipping"        => number_format((float)$order->get_total_shipping(),2,'.',''),
            "coupon"          => $coupon,
            "currency"        => $order->get_currency(),
            "payment_method"  => $order->get_payment_method()
          );
          $this->PixelManagerDataLayer['checkout'] = array_merge( $this->PixelManagerDataLayer['checkout'], $order_data );          
          $this->PTMEnhanceConversionData = $this->get_user_data();          
          ?>
          <script type="text/javascript" data-pagespeed-no-defer data-cfasync="false">
            window.addEventListener('load', call_purchase,true);
            function call_purchase(){
              var PMW_JS = new PMW_PixelManagerJS("", false);
              if( Object.keys(PixelManagerDataLayer[0]["data"]["checkout"]).length >0 ){
                PMW_JS.Purchase();
              }
              PMW_JS.PurchaseFB();
            }        
          </script>
          <?php
        }
      }
    }
    /**
     * AJAX call for Facebook API
     **/
    public function pmw_call_facebook_converstion_api(){
      if(isset($this->options['fb_conversion_api']['is_enable']) && $this->options['fb_conversion_api']['is_enable'] ){
        $event_name = isset($_POST['fb_event'])?sanitize_text_field($_POST['fb_event']):"";        
        $event_id = isset($_POST['event_id'])?sanitize_text_field($_POST['event_id']):"";
        $fb_contents = [];
        if($event_name != ""){       
          if(!empty($_POST['prodct_data']) ){
            foreach($_POST['prodct_data'] as $fb_val){
              $fb_contents[] = array(
                "id" => isset($fb_val['id'])?sanitize_text_field($fb_val['id']):"",
                "quantity" => isset($fb_val['quantity'])?sanitize_text_field($fb_val['quantity']):"1",
                "item_price" => isset($fb_val['price'])?sanitize_text_field($fb_val['price']):(isset($fb_val['item_price'])?sanitize_text_field($fb_val['item_price']):"")
              );
            }
          }

          $value = isset($_POST['custom_data']['value'])?sanitize_text_field($_POST['custom_data']['value']):"0";
          $content_type = isset($_POST['custom_data']['content_type'])?sanitize_text_field($_POST['custom_data']['content_type']):"product";

          $content_ids = [];
          if(!empty($_POST['custom_data']['content_ids']) ){
            foreach($_POST['custom_data']['content_ids'] as $fb_val){
              $content_ids[] = sanitize_text_field($fb_val);
            }
          }
          $args =  array(
            "event_name" => $event_name,
            "event_time" => time(),
            "event_id"   => $event_id,
            "event_source_url" => get_permalink(),
            "action_source" => "website",
            "user_data" => $this->pmw_get_facebook_user_data(),
            "contents" => $fb_contents,
            "custom_data" => [ 
              "value" => $value,
              "currency" => get_woocommerce_currency(),
              "content_type" => $content_type,
              "content_ids" => $content_ids
            ]
          );
          $this->pmw_call_fb_conversions_api_events($args, $this->options);
          echo json_encode(array('status' => 'success', 'message' => $event_name));
          exit;
        }
        
      }
    }// pmw_call_facebook_converstion_api

  }
endif;