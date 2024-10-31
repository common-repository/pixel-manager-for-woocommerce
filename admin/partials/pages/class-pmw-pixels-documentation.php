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
if(!class_exists('PMW_PixelsDocumentation')){
  class PMW_PixelsDocumentation extends PMW_AdminHelper{
    public function __construct( ) {
      $this->load_html();
    }
    protected function load_html(){
      $this->page_html();
    }
    /**
     * Page HTML
     **/
    protected function page_html(){
      //echo $this->get_store_id();
      ?>
       <div class="grow-doc-iframe"> 
        <iframe src="<?php echo esc_url_raw("https://growcommerce.io/doc/pixel-manager/");?>" width="100%" frameborder="0"></iframe>
      </div>
      <?php
    }
  }
}