<?php
/**
 * The admin-specific functionality of the plugin.
 *
 * @link       
 * @since      1.0.0
 *
 * @package    Pixel_Manager_For_Woocommerce
 * @package    Pixel_Manager_For_Woocommerce/admin/partials
 * Pixel Tag Manager For Woocommerce
 */

if(!defined('ABSPATH')){
	exit; // Exit if accessed directly
}
if(!class_exists('PMW_Pixels')){
	class PMW_Pixels extends PMW_AdminHelper{
    protected $is_pro_version;
    protected $api_store;
    protected $plan_name;
    public function __construct( ) {
      $this->api_store = (object)$this->get_pmw_api_store();
      $this->is_pro_version = $this->pmw_is_pro_version($this->api_store);
      $this->plan_name = $this->get_plan_name($this->api_store);
      //$this->req_int();
      $this->load_html();
    }
    public function req_int(){
    }
    protected function load_html(){
      $this->page_html();
      $this->page_js();
    }
    /**
     * Page HTML
     **/
    protected function page_html(){
      /**
       * Tabs
       **/
      ?>
      <div class="pmw_side_menu">
        <ul class="pmw_side_menu_list">
          <li class="active" data-id="sec-pmw-pixels"><i class="pmw_icon pmw_icon-setting"></i></li>
          <li data-id="sec-pmw-pixels-integration"><img src="<?php echo esc_url_raw(PIXEL_MANAGER_FOR_WOOCOMMERCE_URL."/admin/images/integration.png"); ?>" alt="integration"></li>
          <li data-id="sec-pmw-pixels-cookies"><i class="pmw_icon pmw_icon-cookies"></i></li>
        </ul>
      </div>
      <?php
      $current_user = wp_get_current_user();
      do_action("pmw_before_pixel_settings", $this->is_pro_version);
      $pixels_option = $this->get_pmw_pixels_option();
      $email_id = isset($pixels_option['user']['email_id'])?$pixels_option['user']['email_id']:$current_user->user_email;
      //Google
      $google_analytics_4_pixel_id = isset($pixels_option['google_analytics_4_pixel']['pixel_id'])?$pixels_option['google_analytics_4_pixel']['pixel_id']:"";
      $google_analytics_4_pixel_is_enable = isset($pixels_option['google_analytics_4_pixel']['is_enable'])?$pixels_option['google_analytics_4_pixel']['is_enable']:"";

      $generate_lead_from = isset($pixels_option['generate_lead_from'])?$pixels_option['generate_lead_from']:"";

      $google_ads_conversion_id = isset($pixels_option['google_ads_conversion']['id'])?$pixels_option['google_ads_conversion']['id']:"";
      $google_ads_conversion_label = isset($pixels_option['google_ads_conversion']['label'])?$pixels_option['google_ads_conversion']['label']:"";
      $google_ads_conversion_is_enable = isset($pixels_option['google_ads_conversion']['is_enable'])?$pixels_option['google_ads_conversion']['is_enable']:"";

      $google_ads_enhanced_conversion_is_enable = isset($pixels_option['google_ads_enhanced_conversion']['is_enable'])?$pixels_option['google_ads_enhanced_conversion']['is_enable']:"";
      $google_ads_dynamic_remarketing_is_enable = isset($pixels_option['google_ads_dynamic_remarketing']['is_enable'])?$pixels_option['google_ads_dynamic_remarketing']['is_enable']:"";

      //Pixels
      $facebook_pixel_id = isset($pixels_option['facebook_pixel']['pixel_id'])?$pixels_option['facebook_pixel']['pixel_id']:"";
      $facebook_pixel_is_enable = isset($pixels_option['facebook_pixel']['is_enable'])?$pixels_option['facebook_pixel']['is_enable']:"";

      $fb_conversion_api_token = isset($pixels_option['fb_conversion_api']['api_token'])?$pixels_option['fb_conversion_api']['api_token']:"";
      $fb_conversion_api_is_enable = isset($pixels_option['fb_conversion_api']['is_enable'])?$pixels_option['fb_conversion_api']['is_enable']:"";

      $pinterest_pixel_id = isset($pixels_option['pinterest_pixel']['pixel_id'])?$pixels_option['pinterest_pixel']['pixel_id']:"";
      $pinterest_pixel_is_enable = isset($pixels_option['pinterest_pixel']['is_enable'])?$pixels_option['pinterest_pixel']['is_enable']:"";

      $snapchat_pixel_id = isset($pixels_option['snapchat_pixel']['pixel_id'])?$pixels_option['snapchat_pixel']['pixel_id']:"";
      $snapchat_pixel_is_enable = isset($pixels_option['snapchat_pixel']['is_enable'])?$pixels_option['snapchat_pixel']['is_enable']:"";

      $bing_pixel_id = isset($pixels_option['bing_pixel']['pixel_id'])?$pixels_option['bing_pixel']['pixel_id']:"";
      $bing_pixel_is_enable = isset($pixels_option['bing_pixel']['is_enable'])?$pixels_option['bing_pixel']['is_enable']:"";

      $twitter_pixel_id = isset($pixels_option['twitter_pixel']['pixel_id'])?$pixels_option['twitter_pixel']['pixel_id']:"";
      $twitter_pixel_is_enable = isset($pixels_option['twitter_pixel']['is_enable'])?$pixels_option['twitter_pixel']['is_enable']:"";

      $tiktok_pixel_id = isset($pixels_option['tiktok_pixel']['pixel_id'])?$pixels_option['tiktok_pixel']['pixel_id']:"";
      $tiktok_pixel_is_enable = isset($pixels_option['tiktok_pixel']['is_enable'])?$pixels_option['tiktok_pixel']['is_enable']:"";
      
      /**
       * Cookies settings
       **/
      //axeptio
      $axeptio_project_id = isset($pixels_option['axeptio']['project_id'])?$pixels_option['axeptio']['project_id']:"";
      $axeptio_is_enable = isset($pixels_option['axeptio']['is_enable'])?$pixels_option['axeptio']['is_enable']:"";
      $axeptio_cookies_version = isset($pixels_option['axeptio']['cookies_version'])?$pixels_option['axeptio']['cookies_version']:"";

      $privecy_policy = isset($pixels_option['privecy_policy']['privecy_policy'])?$pixels_option['privecy_policy']['privecy_policy']:"";
      $is_theme_plugin_list = isset($pixels_option['privecy_policy']['is_theme_plugin_list'])?$pixels_option['privecy_policy']['is_theme_plugin_list']:"0";
      
      /**
       * Advance settings
       **/
      $exclude_tax_ordertotal = isset($pixels_option['integration']['exclude_tax_ordertotal'])?$pixels_option['integration']['exclude_tax_ordertotal']:"";
      $exclude_shipping_ordertotal = isset($pixels_option['integration']['exclude_shipping_ordertotal'])?$pixels_option['integration']['exclude_shipping_ordertotal']:"";
      $send_product_sku = isset($pixels_option['integration']['send_product_sku'])?$pixels_option['integration']['send_product_sku']:"";

      $fields = [
        "tab_pixels" =>[
          "type" => "tab",
          "name" => "pmw-pixels"
        ],
        "section_account" => [    
          [
            "type" => "section",
            "label" => __("Connect Account", "pixel-manager-for-woocommerce"),
            "class" => "google_section_setting",
          ]
        ],
        "user" => [    
          [
            "type" => "text",
            "label" => __("Email ID", "pixel-manager-for-woocommerce"),
            "name" => "email_id",
            "id" => "email_id",
            "value" => $email_id,
            "placeholder" => __("Enter Your Email", "pixel-manager-for-woocommerce"),
            "class" => "email_id",
            "tooltip" =>[
              "title" => __("Enter your email.", "pixel-manager-for-woocommerce")
            ]
          ]
        ],
        "section_freevspro" => [    
          [
            "type" => "section",
            "label" => __("Comparison between free and pro events tracking ", "pixel-manager-for-woocommerce"),
            "class" => "freevspro_section_setting",
          ]
        ],
        "section_freevspro_features" => [    
          [
            "type" => "freevspro_features",
            "class" => "freevspro_features_setting",
          ]
        ],
        "section_google" => [    
          [
            "type" => "section",
            "label" => __("Google settings", "pixel-manager-for-woocommerce"),
            "class" => "google_section_setting",
          ]
        ],
        "sub_section_google_analytics" => [    
          [
            "type" => "sub_section",
            "label" => __("Google Analytics", "pixel-manager-for-woocommerce"),
            "label_img" => "google_analytics.svg",
            "class" => "google_analytics_sub_section_setting",
          ]
        ],
        "google_analytics_4_pixel" => [    
          [
            "type" => "text_with_switch",
            "is_pro_featured" => true,
            "is_pro_text" => "Upgrade to Pro",
            "pro_utm_text"=> "PRO+Enable+Google+Analytics4+Pixel+Settings",
            "label" => __("GA4- Measurement ID", "pixel-manager-for-woocommerce"),            
            "note"  => __("Ex. Measurement ID: G-QCX3G9KSPC", "pixel-manager-for-woocommerce"),
            "name" => "google_analytics_4_pixel_id",
            "id" => "google_analytics_4_pixel_id",
            "value" => $google_analytics_4_pixel_id,
            "placeholder" => __("Measurement ID", "pixel-manager-for-woocommerce"),
            "class" => "google_analytics_4_pixel_id"
          ],[
            "type" => "switch_with_text",
            "name" => "google_analytics_4_pixel_is_enable",
            "id" => "google_analytics_4_pixel_is_enable",
            "value" => $google_analytics_4_pixel_is_enable,
            "class" => "google_analytics_4_pixel_is_enable",
            "tooltip" =>[
              "title" => __("How do I create a Google Analytics 4 Measurement ID?", "pixel-manager-for-woocommerce"),
              "link_title" => __("Installation Manual", "pixel-manager-for-woocommerce"),
              "link" => "https://support.google.com/analytics/answer/9539598?hl=en"
            ]
          ]
        ],
        "generate_lead_from" => [    
          [
            "type" => "text",
            "label" => __("Form Submission Tracking", "pixel-manager-for-woocommerce"),
            "note"  => __("Enter Form IDs or Class - Ex. .user,#registration,.contact_form", "pixel-manager-for-woocommerce"),
            "name" => "generate_lead_from",
            "id" => "generate_lead_from",
            "value" => $generate_lead_from,
            "is_pro_featured" => true,
            "is_pro_text" => "Upgrade to Pro",
            "pro_utm_text"=> "PRO+Enable+Form+Submission+Tracking+Pixel+Settings",
            "placeholder" => __(".user,#registration,.contact_form", "pixel-manager-for-woocommerce"),
            "class" => "generate_lead_from",
            "tooltip" =>[
              "title" => __("Specify the form elements you want to track by entering their IDs or classes. You can track multiple forms by separating their selectors with commas. Ex. .user,#registration,.contact_form", "pixel-manager-for-woocommerce")
            ]
          ]
        ],
        "sub_section_google_ads" => [    
          [
            "type" => "sub_section",
            "label" => __("Google Ads", "pixel-manager-for-woocommerce"),
            "label_img" => "google_ads.png",
            "class" => "google_ads_sub_section_setting",
          ]
        ],
        "google_ads_conversion" => [    
          [
            "type" => "multi_text",
            "text_fields" =>[
              [
                "is_pro_featured" => true,
                "is_pro_text" => "PRO",
                "pro_utm_text"=> "PRO+Enable+Google+Ads+Conversion+Pixel+Settings",
                "label" => __("Google Ads Conversion ID", "pixel-manager-for-woocommerce"),           
                "note"  => __("Ex. Conversion ID: 11074736289", "pixel-manager-for-woocommerce"),
                "name" => "google_ads_conversion_id",
                "id" => "google_ads_conversion_id",
                "value" => $google_ads_conversion_id,
                "placeholder" => __("Conversion ID", "pixel-manager-for-woocommerce"),
                "class" => "google_ads_conversion_id"
              ],
              [ 
                "label" => __("Conversion Label", "pixel-manager-for-woocommerce"),          
                "note"  => __("Ex. Conversion Label: C3znCNLp84gYEKGh7KAp", "pixel-manager-for-woocommerce"),
                "name" => "google_ads_conversion_label",
                "id" => "google_ads_conversion_label",
                "value" => $google_ads_conversion_label,
                "placeholder" => __("Conversion Label", "pixel-manager-for-woocommerce"),
                "class" => "google_ads_conversion_label"
              ]
            ]
          ]
        ],
        "google_ads_conversion_is_enable" => [    
          [
            "type" => "checkbox",
            "is_pro_featured" => true,
            "is_pro_text" => "PRO",
            "pro_utm_text"=> "PRO+Enable+Google+Ads+Conversion+Pixel+Settings",
            "label" => __("Enable Google Ads Conversion tracking", "pixel-manager-for-woocommerce"),
            "name" => "google_ads_conversion_is_enable",
            "id" => "google_ads_conversion_is_enable",
            "value" => $google_ads_conversion_is_enable,
            "class" => "google_ads_conversion_is_enable",
            "tooltip" =>[
              "title" => __("How do I create a Google Ads Conversion?", "pixel-manager-for-woocommerce"),
              "link_title" => __("Installation Manual", "pixel-manager-for-woocommerce"),
              "link" => "https://support.google.com/tagmanager/answer/6105160?hl=en"
            ]
          ]
        ],
        "google_ads_enhanced_conversion_is_enable" => [    
          [
            "type" => "checkbox",
            "is_pro_featured" => true,
            "is_pro_text" => "PRO",
            "pro_utm_text"=> "PRO+Enable+Google+Ads+Enhanced+Conversion+Pixel+Settings",
            "label" => __("Enable Google Ads Enhanced Conversions tracking", "pixel-manager-for-woocommerce"),
            "name" => "google_ads_enhanced_conversion_is_enable",
            "id" => "google_ads_enhanced_conversion_is_enable",
            "value" => $google_ads_enhanced_conversion_is_enable,
            "class" => "google_ads_enhanced_conversion_is_enable",
            "tooltip" =>[
              "title" => __("Enable Google Ads Enhanced Conversions tracking.", "pixel-manager-for-woocommerce")
            ]
          ]
        ],
        "google_ads_dynamic_remarketing_is_enable" => [    
          [
            "type" => "checkbox",
            "is_pro_featured" => true,
            "is_pro_text" => "PRO",
            "pro_utm_text"=> "PRO+Enable+Google+Ads+Dynamic+Remarketing+Pixel+Settings",
            "label" => __("Enable Google Ads dynamic remarketing tracking", "pixel-manager-for-woocommerce"),
            "name" => "google_ads_dynamic_remarketing_is_enable",
            "id" => "google_ads_dynamic_remarketing_is_enable",
            "value" => $google_ads_dynamic_remarketing_is_enable,
            "class" => "google_ads_dynamic_remarketing_is_enable",
            "tooltip" =>[
              "title" => __("Enable Google Ads dynamic remarketing tracking.", "pixel-manager-for-woocommerce")
            ]
          ]
        ],
        "section_pixels" => [    
          [
            "type" => "section",
            "label" => __("Pixel settings", "pixel-manager-for-woocommerce"),
            "class" => "pixel_section_setting",
          ]
        ],
        "sub_section_facebook" => [    
          [
            "type" => "sub_section",
            "label" => __("Facebook", "pixel-manager-for-woocommerce"),
            "label_img" => "facebook_pixel.png",
            "class" => "facebook_sub_section_setting",
          ]
        ],
        "facebook_pixel" => [    
          [
            "type" => "text_with_switch",
            "is_pro_featured" => true,
            "is_pro_text" => "Upgrade to Pro",
            "pro_utm_text"=> "PRO+Facebook+Pixel+Settings",
            "label" => __("Facebook pixel ID(s)", "pixel-manager-for-woocommerce"),
            //"label_img" => "facebook_pixel.png",
            "note"  => __("Ex. Facebook pixel ID(s): 590022289301578,558158472945205", "pixel-manager-for-woocommerce"),
            "name" => "facebook_pixel_id",
            "id" => "facebook_pixel_id",
            "value" => $facebook_pixel_id,
            "placeholder" => __("Facebook Pixel ID(s)", "pixel-manager-for-woocommerce"),
            "class" => "facebook_pixel_id"
          ],[
            "type" => "switch_with_text",
            "name" => "facebook_pixel_is_enable",
            "id" => "facebook_pixel_is_enable",
            "value" => $facebook_pixel_is_enable,
            "class" => "facebook_pixel_is_enable",
            "tooltip" =>[
              "title" => __("How do I create a Facebook pixel id?", "pixel-manager-for-woocommerce"),
              "link_title" => __("Installation Manual", "pixel-manager-for-woocommerce"),
              "link" => "https://www.facebook.com/business/help/952192354843755?id=1205376682832142"
            ]
          ]
        ],
        "fb_conversion_api" => [    
          [
            "type" => "textarea_with_switch",
            "is_pro_featured" => true,
            "is_pro_text" => "Upgrade to Pro",
            "pro_utm_text"=> "PRO+Enable+Facebook+Conversion+API+Settings",
            "label" => __("Meta (Facebook) Conversion API token", "pixel-manager-for-woocommerce"),
            //"label_img" => "facebook_pixel.png",
            "note"  => __("Send events directly from your web server to Facebook through the Conversion API.", "pixel-manager-for-woocommerce"),
            "name" => "fb_conversion_api_token",
            "id" => "fb_conversion_api_token",
            "value" => $fb_conversion_api_token,
            "placeholder" => __("Conversion API token", "pixel-manager-for-woocommerce"),
            "class" => "fb_conversion_api_token"
          ],[
            "type" => "switch_with_text",
            "name" => "fb_conversion_api_is_enable",
            "id" => "fb_conversion_api_is_enable",
            "value" => $fb_conversion_api_is_enable,
            "class" => "fb_conversion_api_is_enable",
            "tooltip" =>[
              "title" => __("How to find Meta (Facebook) Conversion API token?", "pixel-manager-for-woocommerce"),
              "link_title" => __("Installation Manual", "pixel-manager-for-woocommerce"),
              "link" => "https://developers.facebook.com/docs/marketing-api/conversions-api/get-started#access-token"
            ]
          ]
        ],
        "sub_section_other" => [    
          [
            "type" => "sub_section",
            "label" => __("Other Pixels", "pixel-manager-for-woocommerce"),
            "label_img" => "otherpixel.png",
            "class" => "facebook_sub_section_setting",
          ]
        ],
        "tiktok_pixel" => [    
          [
            "type" => "text_with_switch",
            "is_pro_featured" => true,
            "is_pro_text" => "Upgrade to Pro",
            "pro_utm_text"=> "PRO+Tiktok+Pixel+Settings",
            "label" => __("Tiktok pixel ID", "pixel-manager-for-woocommerce"),
            "label_img" => "tiktok_pixel.png",
            "note"  => __("Ex. Tiktok pixel ID: CBEE743C77U5BM7P378G", "pixel-manager-for-woocommerce"),
            "name" => "tiktok_pixel_id",
            "id" => "tiktok_pixel_id",
            "value" => $tiktok_pixel_id,
            "placeholder" => __("Tiktok Pixel ID", "pixel-manager-for-woocommerce"),
            "class" => "twitter_pixel_id"
          ],[
            "type" => "switch_with_text",
            "name" => "tiktok_pixel_is_enable",
            "id" => "tiktok_pixel_is_enable",
            "value" => $tiktok_pixel_is_enable,
            "class" => "tiktok_pixel_is_enable",
            "tooltip" =>[
              "title" => __("How do I create a Tiktok pixel id?", "pixel-manager-for-woocommerce"),
              "link_title" => __("Installation Manual", "pixel-manager-for-woocommerce"),
              "link" => "https://ads.tiktok.com/help/article?aid=10021"
            ]
          ]
        ],
        "bing_pixel" => [    
          [
            "type" => "text_with_switch",
            "is_pro_featured" => true,
            "is_pro_text" => "Upgrade to Pro",
            "pro_utm_text"=> "PRO+Bing+Pixel+Settings",
            "label" => __("Bing Ads pixel ID", "pixel-manager-for-woocommerce"),
            "label_img" => "bing_pixel.png",
            "note"  => __("Ex. Microsoft Ads - The Bing Ads pixel ID (UET tag ID): 136018753", "pixel-manager-for-woocommerce"),
            "name" => "bing_pixel_id",
            "id" => "bing_pixel_id",
            "value" => $bing_pixel_id,
            "placeholder" => __("Bing Ads Pixel ID (UET tag ID)", "pixel-manager-for-woocommerce"),
            "class" => "bing_pixel_id"
          ],[
            "type" => "switch_with_text",
            "name" => "bing_pixel_is_enable",
            "id" => "bing_pixel_is_enable",
            "value" => $bing_pixel_is_enable,
            "class" => "bing_pixel_is_enable",
            "tooltip" =>[
              "title" => __("How do I create a Bing Ads pixel id (UET tag id)?", "pixel-manager-for-woocommerce"),
              "link_title" => __("Installation Manual", "pixel-manager-for-woocommerce"),
              "link" => "https://help.ads.microsoft.com/#apex/ads/en/56682/-1"
            ]
          ]
        ],
        "pinterest_pixel" => [
          [
            "type" => "text_with_switch",
            "is_pro_featured" => true,
            "is_pro_text" => "Upgrade to Pro",
            "pro_utm_text"=> "PRO+Pinterest+Pixel+Settings",
            "label" => __("Pinterest Pixel ID", "pixel-manager-for-woocommerce"),
            "label_img" => "pinterest_pixel.png",
            "note"  => __("Ex. Pinterest pixel ID: 2613257208392", "pixel-manager-for-woocommerce"),
            "name" => "pinterest_pixel_id",
            "id" => "pinterest_pixel_id",
            "value" => $pinterest_pixel_id,
            "placeholder" => __("Pinterest Pixel ID", "pixel-manager-for-woocommerce"),
            "class" => "pinterest_pixel_id"
          ],[
            "type" => "switch_with_text",
            "name" => "pinterest_pixel_is_enable",
            "id" => "pinterest_pixel_enable",
            "value" => $pinterest_pixel_is_enable,
            "class" => "pinterest_pixel_is_enable",
            "tooltip" =>[
              "title" => __("How do I create a Pinterest pixel id?", "pixel-manager-for-woocommerce"),
              "link_title" => __("Installation Manual", "pixel-manager-for-woocommerce"),
              "link" => "https://developers.pinterest.com/docs/tag/conversion/#basecode"
            ]
          ]
        ],
        "snapchat_pixel" => [
          [
            "type" => "text_with_switch",
            "is_pro_featured" => true,
            "is_pro_text" => "Upgrade to Pro",
            "pro_utm_text"=> "PRO+Snapchat+Pixel+Settings",
            "label" => __("Snapchat Pixel ID", "pixel-manager-for-woocommerce"),
            "label_img" => "snapchat_pixel.png",
            "note"  => __("Ex. Snapchat pixel ID: 12e1ec0a-91aa-4267-b1a3-182c355710e7", "pixel-manager-for-woocommerce"),
            "name" => "snapchat_pixel_id",
            "id" => "snapchat_pixel_id",
            "value" => $snapchat_pixel_id,
            "placeholder" => __("Snapchat Pixel ID", "pixel-manager-for-woocommerce"),
            "class" => "snapchat_pixel_id"
          ],[
            "type" => "switch_with_text",
            "name" => "snapchat_pixel_is_enable",
            "id" => "snapchat_pixel_is_enable",
            "value" => $snapchat_pixel_is_enable,
            "class" => "snapchat_pixel_is_enable",
            "tooltip" =>[
              "title" => __("How do I create a Snapchat pixel id?", "pixel-manager-for-woocommerce"),
              "link_title" => __("Installation Manual", "pixel-manager-for-woocommerce"),
              "link" => "https://businesshelp.snapchat.com/s/article/pixel-website-install?language=en_US"
            ]
          ]
        ],
        "twitter_pixel" => [    
          [
            "type" => "text_with_switch",
            "is_pro_featured" => true,
            "is_pro_text" => "Upgrade to Pro",
            "pro_utm_text"=> "PRO+Twitter+Pixel+Settings",
            "label" => __("Twitter pixel ID", "pixel-manager-for-woocommerce"),
            "label_img" => "twitter_pixel.png",
            "note"  => __("Ex. Twitter pixel ID: o9e1c", "pixel-manager-for-woocommerce"),
            "name" => "twitter_pixel_id",
            "id" => "twitter_pixel_id",
            "value" => $twitter_pixel_id,
            "placeholder" => __("Twitter Pixel ID", "pixel-manager-for-woocommerce"),
            "class" => "twitter_pixel_id"
          ],[
            "type" => "switch_with_text",
            "name" => "twitter_pixel_is_enable",
            "id" => "twitter_pixel_is_enable",
            "value" => $twitter_pixel_is_enable,
            "class" => "twitter_pixel_is_enable",
            "tooltip" =>[
              "title" => __("How do I create a Twitter pixel id?", "pixel-manager-for-woocommerce"),
              "link_title" => __("Installation Manual", "pixel-manager-for-woocommerce"),
              "link" => "https://business.twitter.com/en/help/campaign-measurement-and-analytics/conversion-tracking-for-websites.html"
            ]
          ]
        ],
        "hidden" => [
          [
            "type" => "hidden",
            "name" => "privecy_policy",
            "id" => "privecy_policy",
            "value" => $privecy_policy
          ],[
            "type" => "hidden",
            "name" => "is_theme_plugin_list",
            "id" => "is_theme_plugin_list",
            "value" => $is_theme_plugin_list
          ],[
            "type" => "hidden",
            "id" => "pixels_save_action",
            "name" => "action",
            "value" => "pmw_check_privecy_policy"
          ]
        ],
        "tab_end_pixels" => [ 
          "type" => "tab_end"          
        ],
        "tab_integration" => [
          "type" => "tab",
          "name" => "pmw-pixels-integration"
        ],
        "section_pixels_integration" => [    
          [
            "type" => "section",
            "label" => __("Advanced Options", "pixel-manager-for-woocommerce"),
            "class" => "pixel_section_setting",
          ]
        ],
        "send_product_sku" => [    
          [
            "type" => "switch",
            "label" => __("Send Product SKU instead of ID", "pixel-manager-for-woocommerce"),
            "name" => "send_product_sku",
            "id" => "send_product_sku",
            "value" => $send_product_sku,
            "class" => "send_product_sku",
            "tooltip" =>[
              "title" => __("Activate this feature to send product SKU information for remarketing and eCommerce tracking.", "pixel-manager-for-woocommerce")
            ]
          ]
        ],
        "exclude_tax_ordertotal" => [    
          [
            "type" => "switch",
            "label" => __("Exclude tax from order revenue", "pixel-manager-for-woocommerce"),
            "name" => "exclude_tax_ordertotal",
            "id" => "exclude_tax_ordertotal",
            "value" => $exclude_tax_ordertotal,
            "class" => "exclude_tax_ordertotal",
            "tooltip" =>[
              "title" => __("Activate this feature to exclude tax from the order total variable.", "pixel-manager-for-woocommerce")
            ]
          ]
        ],
        "exclude_shipping_ordertotal" => [    
          [
            "type" => "switch",
            "label" => __("Exclude shipping from order revenue", "pixel-manager-for-woocommerce"),
            "name" => "exclude_shipping_ordertotal",
            "id" => "exclude_shipping_ordertotal",
            "value" => $exclude_shipping_ordertotal,
            "class" => "exclude_shipping_ordertotal",
            "tooltip" =>[
              "title" => __("Activate this feature to exclude shipping from the order total variable.", "pixel-manager-for-woocommerce")
            ]
          ]
        ],
        "tab_end_integration" => [
          "type" => "tab_end"
        ],
        "tab_cookies" => [
          "type" => "tab",
          "name" => "pmw-pixels-cookies"
        ],
        "section_axeptio" => [    
          [
            "type" => "section",
            "label" => __("Cookies Consents Settings", "pixel-manager-for-woocommerce"),
            "class" => "consents_section_setting",
          ]
        ],
        "sub_section_axeptio" => [    
          [
            "type" => "sub_section",
            "label" => __("Axeptio", "pixel-manager-for-woocommerce"),
            "label_img" => "axeptio.png",
            "class" => "axeptio_sub_section_setting",
          ]
        ],
        "axeptio" => [    
          [
            "type" => "text_with_switch",
            "is_pro_featured" => true,
            "is_pro_text" => "Upgrade to Pro",
            "pro_utm_text"=> "PRO+Axeptio+Cookies+Pixel+Settings",
            "label" => __("Project ID", "pixel-manager-for-woocommerce"),
            //"label_img" => "facebook_pixel.png",
            "note"  => __("Enable Google Consent Mode v2 and provide Project ID (Ex.: 65ebb7949d4cb03e6e037a17).", "pixel-manager-for-woocommerce"),
            "name" => "axeptio_project_id",
            "id" => "axeptio_project_id",
            "value" => $axeptio_project_id,
            "placeholder" => __("Project ID", "pixel-manager-for-woocommerce"),
            "class" => "axeptio_project_id"
          ],[
            "type" => "switch_with_text",
            "name" => "axeptio_is_enable",
            "id" => "axeptio_is_enable",
            "value" => $axeptio_is_enable,
            "class" => "axeptio_is_enable",
            "tooltip" =>[
              "title" => __("How do I create a Axeptio Project ID?", "pixel-manager-for-woocommerce"),
              "link_title" => __("Installation Manual", "pixel-manager-for-woocommerce"),
              "link" => "https://admin.axeptio.eu/projects"
            ]
          ]
        ],
        "axeptio_cookies_version" => [    
          [
            "type" => "text",
            "label" => __("Cookies Version(optional)", "pixel-manager-for-woocommerce"),
            "note"  => __("Cookies Version", "pixel-manager-for-woocommerce"),
            "name" => "axeptio_cookies_version",
            "id" => "axeptio_cookies_version",
            "value" => $axeptio_cookies_version,
            "is_pro_featured" => true,
            "is_pro_text" => "Upgrade to Pro",
            "pro_utm_text"=> "PRO+Axeptio+Cookies+Version+Pixel+Settings",
            "placeholder" => __(".", "pixel-manager-for-woocommerce"),
            "class" => "axeptio_cookies_version",
            "tooltip" =>[
              "title" => __("String identifier of the version of Cookie configuration that should be loaded. If this parameter is omitted, then it's the \"pages\" property in the configuration that gets parsed in case of multiple cookies configurations.", "pixel-manager-for-woocommerce")
            ]
          ]
        ],
        "axeptio_setting" => [    
          [
            "type" => "axeptio_setting",
            "label" => __("Axeptio setting", "pixel-manager-for-woocommerce"),
            "class" => "axeptio_setting",
            "value" => isset($pixels_option["axeptio"])?$pixels_option["axeptio"]:[]
          ]
        ],
        "tab_end_cookies" => [
          "type" => "tab_end"
        ],
        "button" => [
          [
            "type" => "button",
            "name" => "pixels_save",
            "id" => "pixels_save",
            "class" => "pixels_save"
          ]
        ]
      ];
      ?>
      <div class="pmw-left-wrapper">
        <?php
        $form = array("name" => "pmw-pixels", "id" => "pmw-pixels", "method" => "post", "class" => "pmw-pixels-from");
        $this->add_form_fields($fields, $form);
        
        /**
         * Sidebar
         */
        echo $this->get_sidebar_html($this->is_pro_version, $this->plan_name);
        ?>
      </div>
      <div id="pmw_privacy_popup" class="modal fade">
        <div class="modal-dialog modal-dialog-centered">
          <!-- Modal content -->
          <div class="modal-content">
            <div class="modal-header">
              <span id="close" class="close">&times;</span>
            </div>
            <div class="modal-body">
              <div class="modal-top-area">
                <div class="logo-section">
                  <div class="logo_section-img"><img src="<?php echo esc_url_raw(PIXEL_MANAGER_FOR_WOOCOMMERCE_URL."/admin/images/wp.png"); ?>" alt="img"></div>
                  <div class="logo_section-img"><img src="<?php echo esc_url_raw(PIXEL_MANAGER_FOR_WOOCOMMERCE_URL."/admin/images/pixel-icon.png"); ?>" alt="img"></div>
                </div>
              </div>
              <div class="modal-middle-area">
                <p><strong>Hey <?php echo esc_attr(get_bloginfo()); ?>,</strong></p>
               <p><?php echo esc_attr__('Never miss an important update - opt in to our security and feature updates notifications, and non-sensitive diagnostic tracking with', 'pixel-manager-for-woocommerce'); ?> <a target="_blank" href="<?php echo esc_url_raw("https://growcommerce.io/"); ?>">GrowCommerce</a></p>
                <p><a target="_blank" href="<?php echo esc_url_raw("https://growcommerce.io/privacy-terms/"); ?>"><?php echo esc_attr__('Privacy & Terms', 'pixel-manager-for-woocommerce'); ?></a></p>
                <div class="modal_button-area">
                  <button class="pmw_btn pmw_btn-fill" id="pmw_accept_privecy_policy"><?php echo esc_attr__('Allow & Continue', 'pixel-manager-for-woocommerce'); ?></button>
                  <?php /*<button class="pmw_btn pmw_btn-default">Skip</button>*/ ?>
                </div>
              </div>
              <div class="modal-bottom-area">
                <h2 class="toggle_title-text"><?php echo esc_attr__('What Permissions are being Granted?', 'pixel-manager-for-woocommerce'); ?></h2>
                <div class="pmw_slide-down-area">
                  <ul>
                    <li>
                      <div class="pmw_slide-area-image"><img src="<?php echo esc_url_raw(PIXEL_MANAGER_FOR_WOOCOMMERCE_URL."/admin/images/Icon-profile.png"); ?>" alt="img"></div>
                      <div class="pmw_slide-area-content">
                        <span class="pmw_slide-area-title"><?php echo esc_attr__('Your Profile Overview', 'pixel-manager-for-woocommerce'); ?></span>
                        <p><?php echo esc_attr__('Name and email address', 'pixel-manager-for-woocommerce'); ?></p>
                      </div>
                    </li>
                    <li>
                      <div class="pmw_slide-area-image"><img src="<?php echo esc_url_raw(PIXEL_MANAGER_FOR_WOOCOMMERCE_URL."/admin/images/Icon-site-overview.png"); ?>" alt="img"></div>
                      <div class="pmw_slide-area-content">
                        <span class="pmw_slide-area-title"><?php echo esc_attr__('Your Site Overview', 'pixel-manager-for-woocommerce'); ?></span>
                        <p><?php echo esc_attr__('Site URL, country, currency, WP version, PHP info', 'pixel-manager-for-woocommerce'); ?></p>
                      </div>
                    </li>
                    <li>
                      <div class="pmw_slide-area-image"><img src="<?php echo esc_url_raw(PIXEL_MANAGER_FOR_WOOCOMMERCE_URL."/admin/images/Icon-notice.png"); ?>" alt="img"></div>
                      <div class="pmw_slide-area-content">
                        <span class="pmw_slide-area-title"><?php echo esc_attr__('Admin Notice', 'pixel-manager-for-woocommerce'); ?></span>
                        <p><?php echo esc_attr__('Updates, announcements, marketing, no spam', 'pixel-manager-for-woocommerce'); ?></p>
                      </div>
                    </li>
                    <li>
                      <div class="pmw_slide-area-image"><img src="<?php echo esc_url_raw(PIXEL_MANAGER_FOR_WOOCOMMERCE_URL."/admin/images/Icon-status.png"); ?>" alt="img"></div>
                      <div class="pmw_slide-area-content">
                        <span class="pmw_slide-area-title"><?php echo esc_attr__('Current Plugin Status', 'pixel-manager-for-woocommerce'); ?></span>
                        <p><?php echo esc_attr__('Active, deactivated, or uninstalled, settings', 'pixel-manager-for-woocommerce'); ?></p>
                      </div>
                    </li>
                    <li>
                      <div class="pmw_slide-area-image"><img src="<?php echo esc_url_raw(PIXEL_MANAGER_FOR_WOOCOMMERCE_URL."/admin/images/icon-plugin.png"); ?>" alt="img"></div>
                      <div class="pmw_slide-area-content">
                        <span class="pmw_slide-area-title"><?php echo esc_attr__('Plugins & Themes', 'pixel-manager-for-woocommerce'); ?></span>
                        <p><?php echo esc_attr__('Title, slug, version, and is active', 'pixel-manager-for-woocommerce'); ?></p>
                      </div>
                      <div class="custom-control custom-switch">
                        <input type="checkbox" class="pmw_custom-control-input" id="ch_is_theme_plugin_list" checked>
                        <label class="pmw_custom-control-label" for="ch_is_theme_plugin_list"></label>
                      </div>
                    </li>
                  </ul>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
      <svg version="1.1" class="svg-filters" style="display:none;">
        <defs>
          <filter id="marker-shape">
            <feTurbulence type="fractalNoise" baseFrequency="0 0.15" numOctaves="1" result="warp" />
            <feDisplacementMap xChannelSelector="R" yChannelSelector="G" scale="30" in="SourceGraphic" in2="warp" />
          </filter>
        </defs>
      </svg>
      <?php
    }
    /**
     * Page JS
     **/
    protected function page_js(){
      ?>
      <script type="text/javascript">
        (function($){ 
          jQuery(document).ready(function(){
            var hash = window.location.hash;
            if(hash!= ""){
              jQuery('html, body').animate({
                  scrollTop: jQuery(hash).offset().top-200
              }, 1000);
            }
            jQuery(".pmw_show").on("click", function () {
              jQuery(this).next("#show-all-features").toggleClass("active");
              let line_title = jQuery(this).attr("data-title");
              if(jQuery(this).next("#show-all-features").hasClass('active')){
                jQuery(this).addClass("active");
                jQuery(this).html("Hide "+line_title);
              }else{
                jQuery(this).removeClass("active");
                jQuery(this).html(line_title);
              }
            });
            jQuery(".pmw_side_menu_list li").on("click", function () {
              var id = jQuery(this).attr("data-id");
              jQuery(".pmw_side_menu_list li").removeClass("active");
              jQuery(".pmw_form-wrapper").removeClass("active");              
              jQuery(this).addClass("active");
              document.getElementById(id).classList.add("active");
            });
            jQuery("#sec-pmw-pixels").toggleClass("active");
            jQuery("#pmw-pixels .pmw_form-control").on("focus", function () {
              if( jQuery(this).attr("id") == "google_ads_conversion_id" || jQuery(this).attr("id") == "google_ads_conversion_label"){
                jQuery(this).parent().parent().addClass("active");
              }else{
                jQuery(this).parent().parent().parent().addClass("active");
              }
            });
            jQuery("#pmw-pixels .pmw_form-control").on("focusout", function (event) {
              if(jQuery(this).val() == "" && ( jQuery(this).attr("id") == "google_ads_conversion_id" || jQuery(this).attr("id") == "google_ads_conversion_label")){
                jQuery(this).parent().parent().removeClass("active");
              }else if(jQuery(this).val() == ""){
                jQuery(this).parent().parent().parent().removeClass("active");
              }
            });
            jQuery("#pmw-pixels .pmw_form-control").on("input", function (event) {
              event.preventDefault();
              if(jQuery(this).val() == "" && ( jQuery(this).attr("id") == "google_ads_conversion_id" || jQuery(this).attr("id") == "google_ads_conversion_label")){
                jQuery(this).parent().parent().removeClass("active");
              }else if(jQuery(this).val() == "" && ( jQuery(this).attr("id") == "fb_conversion_api_token")){
                //var id = jQuery(this).attr("id").replace("id","is_enable");
                jQuery("#fb_conversion_api_is_enable").prop('checked', false);                 
              }else if(jQuery(this).val() == ""){
                jQuery(this).parent().parent().parent().removeClass("active");
                var id = jQuery(this).attr("id").replace("project_id","is_enable").replace("id","is_enable");
                jQuery("#"+id).prop('checked', false);
              }else if(jQuery(this).val() != "" && ( jQuery(this).attr("id") == "fb_conversion_api_token")){
                //var id = jQuery(this).attr("id").replace("id","is_enable");
                jQuery("#fb_conversion_api_is_enable").prop('checked', true);                 
              }else if(jQuery(this).val() != ""){
                var id = jQuery(this).attr("id").replace("project_id","is_enable").replace("id","is_enable");
                jQuery("#"+id).prop('checked', true);                 
              }
            });
            jQuery('#pmw-pixels .pmw_form-control').each(function(){
              if(jQuery(this).val() != "" && ( jQuery(this).attr("id") == "google_ads_conversion_id" || jQuery(this).attr("id") == "google_ads_conversion_label")){
                jQuery(this).parent().parent().addClass("active");
              }else if(jQuery(this).val() != ""){
                jQuery(this).parent().parent().parent().addClass("active");
              }
            });
          });
        })( jQuery );
      </script>
      <?php
    }
	}
}