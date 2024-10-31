<?php
/**
 * The admin-specific functionality of the plugin.
 *
 * @link       
 * @since      1.0.0
 *
 * @package    
 * @package    PMW_PixelHelper
 * 
 */
if (!defined('ABSPATH')) {
  exit; // Exit if accessed directly
}
if(!class_exists('PMW_PixelHelper')):	
	class PMW_PixelHelper{
		protected $options;
		protected $user_data;
		public function __construct(){
			$this->req_int();
			$this->options = $this->get_option();
		}
		public function req_int(){
			if (!function_exists('is_plugin_active')) {
			  include_once(ABSPATH . 'wp-admin/includes/plugin.php');
			}
			if (!class_exists('PMW_PixelItemFunction')) {
        require_once('class-pixel-item-function.php');
      }
		}

		public function get_option(){
			return unserialize( get_option("pmw_pixels_option") );
		}
		/**
		 * ceck pixel active 
		 */
		public function is_google_ads_conversion_enable(){
			if(isset($this->options['google_ads_conversion']) && isset($this->options['google_ads_conversion']['id'])){
				$pixel = $this->options['google_ads_conversion'];
				if(isset($pixel['id']) && isset($pixel['label']) && isset($pixel['is_enable']) && $pixel['id'] && $pixel['label'] && $pixel['is_enable']){
					return true;
				}
			}
			return false;
		}
		public function is_google_ads_enhanced_conversion_enable(){
			if(isset($this->options['google_ads_enhanced_conversion']) ){
				$pixel = $this->options['google_ads_enhanced_conversion'];
				if( isset($pixel['is_enable']) && $pixel['is_enable']){
					return true;
				}
			}
			return false;
		}

		public function is_google_ads_dynamic_remarketing_enable(){
			if(isset($this->options['google_ads_dynamic_remarketing']) && isset($this->options['google_ads_conversion']) ){
				$pixel_google_ads_conversion = $this->options['google_ads_conversion'];
				$pixel = $this->options['google_ads_dynamic_remarketing'];
				if( isset($pixel['is_enable']) && $pixel['is_enable'] && isset($pixel_google_ads_conversion['id']) && $pixel_google_ads_conversion['id']){
					return true;
				}
			}
			return false;
		}

		public function is_pixel_enable($key){
			if(isset($this->options[$key]) && isset($this->options[$key]['pixel_id'])){
				$pixel = $this->options[$key];
				if(isset($pixel['pixel_id']) && isset($pixel['is_enable']) && $pixel['pixel_id'] && $pixel['is_enable']){
					return true;
				}
			}
			return false;
		}

		public function is_send_sku(){
			if(isset($this->options['integration']['send_product_sku']) && $this->options['integration']['send_product_sku'] ){
				return true;
			}
			return false;
		}

		/*check other plugin active */
		public function is_yith_wc_brands_active() {
      return is_plugin_active('yith-woocommerce-brands-add-on-premium/init.php');
    }
    public function is_woocommerce_brands_active() {
      return is_plugin_active('woocommerce-brands/woocommerce-brands.php');
    }
    public function is_wpml_woocommerce_multi_currency_active() {
      global $woocommerce_wpml;
      if (is_plugin_active('woocommerce-multilingual/wpml-woocommerce.php') && is_object($woocommerce_wpml->multi_currency)) {
        return true;
      } else {
        return false;
      }
    }
    public function is_woocommerce_active() {
      return is_plugin_active('woocommerce/woocommerce.php');
    }

    public function get_order_total($page, $order){
    	if($page == "cart"){
    		$order_total = (float) $order->total;
	    	if ( (isset($this->options["integration"]["exclude_tax_ordertotal"]) && $this->options["integration"]["exclude_tax_ordertotal"] == 1) ) {
					$order_total = (float) ( $order_total - $order->get_taxes_total() );
				} 
				if ( (isset($this->options["integration"]["exclude_shipping_ordertotal"]) && $this->options["integration"]["exclude_shipping_ordertotal"] ==1) ) {
					$order_total = (float) ( $order_total - $order->get_shipping_total() );
				}
				return $order_total;
    	}
    	if($page == "order_received"){
	    	$order_total = (float) $order->get_total();
	    	if ( (isset($this->options["integration"]["exclude_tax_ordertotal"]) && $this->options["integration"]["exclude_tax_ordertotal"] ==1) ) {
					$order_total = (float) ( $order_total - $order->get_total_tax() );
				} 
				if ( (isset($this->options["integration"]["exclude_shipping_ordertotal"]) && $this->options["integration"]["exclude_shipping_ordertotal"] ==1) ) {
					$order_total = (float) ( $order_total - $order->get_shipping_total() );
				}
				return $order_total;
			}
			
    }
    public function get_user_data(){
    	if(empty($this->user_data)){
    		$this->user_data = $this->set_user_data();
    	}
    	return $this->user_data;
    }
    public function set_user_data(){
	    $enhanced_conversion = array();
	    if ( is_user_logged_in() ) {
	      global $current_user;      
	      $billing_country = WC()->customer->get_billing_country();
	      $calling_code = WC()->countries->get_country_calling_code($billing_country);
	      $phone = get_user_meta($current_user->ID,'billing_phone',true);
	      if($phone != ""){
	        $phone = str_replace($calling_code,"", $phone);
	        $phone = $calling_code.$phone;
	        $enhanced_conversion["phone_number"] = esc_js($phone);
	      }
	      $email = esc_js($current_user->user_email);
	      if($email != ""){
	        $enhanced_conversion["email"] = esc_js($email);
	      }
	      $first_name         = esc_js($current_user->user_firstname);
	      if($first_name != ""){
	        $enhanced_conversion["address"]["first_name"] = esc_js($first_name);
	      }
	      $last_name          = $current_user->user_lastname;
	      if($last_name != ""){
	        $enhanced_conversion["address"]["last_name"] = esc_js($last_name);
	      }
	      $billing_address_1  = WC()->customer->get_billing_address_1();
	      if($billing_address_1 != ""){
	        $enhanced_conversion["address"]["street"] = esc_js($billing_address_1);
	      }
	      $billing_postcode   = WC()->customer->get_billing_postcode();
	      if($billing_postcode != ""){
	        $enhanced_conversion["address"]["postal_code"] = esc_js($billing_postcode);
	      }
	      $billing_city       = WC()->customer->get_billing_city();
	      if($billing_city != ""){
	        $enhanced_conversion["address"]["city"] = esc_js($billing_city);
	      }
	      $billing_state      = WC()->customer->get_billing_state();
	      if($billing_state != ""){
	        $enhanced_conversion["address"]["region"] = esc_js($billing_state);
	      }
	      $billing_country    = WC()->customer->get_billing_country();
	      if($billing_country != ""){
	        $enhanced_conversion["address"]["country"] = esc_js($billing_country);
	      }
	    }else{ // get user       
	      $order = "";
	      $order_id = "";
	      if( $order_id == null && is_order_received_page() ){
	      	$PixelItemFunction = new PMW_PixelItemFunction();
	        $order = $PixelItemFunction->get_order_from_order_received_page(); 
	        $order_id = $order->get_id();
	      }
	      if($order_id){
	        $billing_country  = $order->get_billing_country();
	        $calling_code = WC()->countries->get_country_calling_code($billing_country);
	        $billing_email  = $order->get_billing_email();
	        if($billing_email != ""){
	          $enhanced_conversion["email"] = esc_js($billing_email);
	        }
	        $billing_phone  = $order->get_billing_phone();
	        if($billing_phone != ""){
	          $billing_phone = str_replace($calling_code,"", $billing_phone);
	          $billing_phone = $calling_code.$billing_phone;
	          $enhanced_conversion["phone_number"] = esc_js($billing_phone);
	        }
	        $billing_first_name = $order->get_billing_first_name();
	        if($billing_first_name != ""){
	          $enhanced_conversion["address"]["first_name"] = esc_js($billing_first_name);
	        }
	        $billing_last_name = $order->get_billing_last_name();
	        if($billing_last_name != ""){
	          $enhanced_conversion["address"]["last_name"] = esc_js($billing_last_name);
	        }          
	        $billing_address_1 = $order->get_billing_address_1();
	        if($billing_address_1 != ""){
	          $enhanced_conversion["address"]["street"] = esc_js($billing_address_1);
	        }
	        $billing_city = $order->get_billing_city();
	        if($billing_city != ""){
	          $enhanced_conversion["address"]["city"] = esc_js($billing_city);
	        }
	        $billing_state = $order->get_billing_state();
	        if($billing_state != ""){
	          $enhanced_conversion["address"]["region"] = esc_js($billing_state);
	        }
	        $billing_postcode = $order->get_billing_postcode();
	        if($billing_postcode != ""){
	          $enhanced_conversion["address"]["postal_code"] = esc_js($billing_postcode);
	        }
	        $billing_country = $order->get_billing_country();
	        if($billing_country != ""){
	          $enhanced_conversion["address"]["country"] = esc_js($billing_country);
	        }
	      }
	    }
	    return $enhanced_conversion;
	  }

	  public function pmw_get_facebook_user_data($enhanced_conversion = array()){
	  	if(empty($enhanced_conversion)){
	  		$enhanced_conversion = $this->get_user_data();
	  	}
	    $user_data = array(
	      "client_ip_address" => $this->get_user_ip(),
	      "client_user_agent" => $_SERVER['HTTP_USER_AGENT']/*,
	      "fbc" => "",
	      "fbp" => ""*/
	    );
	    if(isset($enhanced_conversion["email"]) && $enhanced_conversion["email"] != ""){
	      $user_data["em"] =[hash("sha256", esc_js($enhanced_conversion["email"]))];
	    }
	    if(isset($enhanced_conversion["address"]["first_name"]) && $enhanced_conversion["address"]["first_name"] != ""){
	      $user_data["fn"] =[hash("sha256", esc_js($enhanced_conversion["address"]["first_name"]))];
	    }
	    if(isset($enhanced_conversion["address"]["last_name"]) && $enhanced_conversion["address"]["last_name"] != ""){
	      $user_data["ln"] =[hash("sha256", esc_js($enhanced_conversion["address"]["last_name"]))];
	    }
	    if(isset($enhanced_conversion["phone_number"]) && $enhanced_conversion["phone_number"] != ""){
	      $user_data["ph"] =[hash("sha256", esc_js($enhanced_conversion["phone_number"]))];
	    }
	    if(isset($enhanced_conversion["address"]["city"]) && $enhanced_conversion["address"]["city"] != ""){
	      $user_data["ct"] =[hash("sha256", esc_js($enhanced_conversion["address"]["city"]))];
	    }
	    if(isset($enhanced_conversion["address"]["street"]) && $enhanced_conversion["address"]["street"] != ""){
	      $user_data["st"] =[hash("sha256", esc_js($enhanced_conversion["address"]["street"]))];
	    }
	    if(isset($enhanced_conversion["address"]["region"]) && $enhanced_conversion["address"]["region"] != ""){
	      $user_data["country"] =[hash("sha256", esc_js($enhanced_conversion["address"]["region"]))];
	    }
	    if(isset($enhanced_conversion["address"]["postal_code"]) && $enhanced_conversion["address"]["postal_code"] != ""){
	      $user_data["zp"] =[hash("sha256", esc_js($enhanced_conversion["address"]["postal_code"]))];
	    }
	    return $user_data;
	  }

	  /**
	   * Call Facebook API
	   **/
	  public function pmw_call_fb_conversions_api_events($args, $options){
	    try {
	      if(!empty($args) && isset($options['fb_conversion_api']['is_enable']) && $options['fb_conversion_api']['is_enable'] ){
	      	$fb_api_version = "v16.0";
			
				$fb_pixel_id = (isset($options['facebook_pixel']['pixel_id']))?$options['facebook_pixel']['pixel_id']:"";
				$fb_conversion_api_token = (isset($options['fb_conversion_api']['api_token']))?$options['fb_conversion_api']['api_token']:"";

	        $header = array(
	          "cache-control" => "no-cache",     
	          "Accept" => "application/json"
	        );
	        $url = "https://graph.facebook.com/".$fb_api_version."/".$fb_pixel_id."/events";
	        // Send remote request
	        $args_req = array();
	        $args_req["data"] = json_encode(array($args));
	        $args_req["access_token"] = $fb_conversion_api_token;
	        $args_req["partner_agent"] = "GrowCommerce";        
	        //$args_req["test_event_code"] ="TEST96768";
	        //$args['timeout']= "1000";
	        $args_api = array(
	          'headers' =>$header,
	          'method' => 'POST',
	          'body' => $args_req
	        );
	        wp_remote_post($url, $args_api);
	        // Retrieve information
	        //$request =
	        /*$response_code = wp_remote_retrieve_response_code($request);
	        $response_message = wp_remote_retrieve_response_message($request);
	        $response_body = json_decode(wp_remote_retrieve_body($request));*/       
	      }
	    } catch (Exception $e) {
	        return $e->getMessage();
	    }
	  }
    /**
     * get user IP
     **/
    public function get_user_ip() {
	    $ipaddress = '';
	    if (getenv('HTTP_CLIENT_IP')){
	      $ipaddress = getenv('HTTP_CLIENT_IP');
	    }else if(getenv('HTTP_X_FORWARDED_FOR')){
	      $ipaddress = getenv('HTTP_X_FORWARDED_FOR');
	    }else if(getenv('HTTP_X_FORWARDED')){
	      $ipaddress = getenv('HTTP_X_FORWARDED');
	    }else if(getenv('HTTP_FORWARDED_FOR')){
	      $ipaddress = getenv('HTTP_FORWARDED_FOR');
	    }else if(getenv('HTTP_FORWARDED')){
	      $ipaddress = getenv('HTTP_FORWARDED');
	    }else if(getenv('REMOTE_ADDR')){
	      $ipaddress = getenv('REMOTE_ADDR');
	    }
	    return $ipaddress;
		}
		/*public function get_fb_event_id() {
	    $data = openssl_random_pseudo_bytes(16);
	    $data[6] = chr(ord($data[6]) & 0x0f | 0x40);
	    $data[8] = chr(ord($data[8]) & 0x3f | 0x80);
	    return vsprintf('%s%s%s%s%s%s%s%s', str_split(bin2hex($data), 4));
	  }*/
	}
endif;