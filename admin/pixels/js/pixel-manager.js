window.dataLayer = window.dataLayer || [];
class PMW_PixelManagerJS {
  constructor(options = {}, is_Bindings = true, is_woocomerce = true){    
    this.options = {
      esc_tracking: true
    };
    if(options){
      Object.assign(this.options, options);
    }
    if(is_woocomerce){
      this.PixelManagerDataLayer = PixelManagerDataLayer[0]["data"];
      this.PMWEventID = PixelManagerEventOptions.time;
      this.PixelManagerOptions = PixelManagerOptions;
      this.TrackFormSubmissionsBindings();    

      this.LineItemReq = {    
        item_id       : 'item_id',
        item_name     : 'item_name',
        item_category : 'item_category',
        index         : 'index',
        item_brand    : 'item_brand',
        item_variant  : 'item_variant',
        price         : 'price',
        //currency      : "currency",
        //quantity      : 'quantity',
        item_list_name: 'item_list_name'
      };
      this.is_begin_checkout_fired = false;
      this.is_shipping_fired = false;
    }else{
      this.PixelManagerOptions = PixelManagerOptions;
      this.TrackFormSubmissionsBindings();
    }
    /*var variations = jQuery('.variations_form').data( 'product_variations' );
    var variation = this.get_item_form_items_by_key_val(variations, 'variation_id', 132);*/
  }
  TrackFormSubmissionsBindings(){
    if(this.PixelManagerOptions.hasOwnProperty('generate_lead_from') && this.PixelManagerOptions.generate_lead_from != ""){
      let generate_lead_from = this.PixelManagerOptions.generate_lead_from;
      generate_lead_from = generate_lead_from.replace(/,([^ ])/g, ', $1');     
      //var selector_elements = generate_lead_from.split(', ').map(selector => selector.trim());      
      var elements  = document.querySelectorAll(generate_lead_from);
      if(elements.length > 0){ 
        for (var i = 0; i < elements.length; i++) {
          if(elements[i]){
            elements[i].addEventListener("submit", () => this.TrackFormSubmissions(event));
          }
        }
      }
    }
  }
  TrackFormSubmissions(event){
    event.preventDefault();
    var this_var =  event.currentTarget;
    var form_name = this_var.getAttribute('aria-label') || this_var.getAttribute('name') || this_var.getAttribute('id');
    var form_id = this_var.getAttribute('id');
    var action_url = this_var.getAttribute('action');
    var page_url = window.location.href;
    var page_title = document.title;
    var PMWEvent = {
        event: "form_submit",
        page_title : page_title,
        form_id: (form_id)?form_id:"",
        form_name: (form_name)?form_name:"",
        action_url: (action_url)?action_url:"",
        page_url: page_url
      };
      dataLayer.push(PMWEvent);
  }
  Purchase(){
    if(this.PixelManagerDataLayer.hasOwnProperty('checkout') && this.PixelManagerDataLayer.checkout.hasOwnProperty('cart_product_list')){
      this.LineItemReq["quantity"]="quantity";
      var Items = this.GetLineItems(this.PixelManagerDataLayer.checkout.cart_product_list, this.LineItemReq);
      var PMWEvent = {
        event: "purchase",
        ecommerce: {
          transaction_id: this.PixelManagerDataLayer.checkout.id,
          currency: this.PixelManagerDataLayer.currency,
          value: this.PixelManagerDataLayer.checkout.total,
          tax: this.PixelManagerDataLayer.checkout.tax,
          shipping: this.PixelManagerDataLayer.checkout.shipping,
          coupon: this.PixelManagerDataLayer.checkout.coupon,
          items: Items
        }
      };
      dataLayer.push(PMWEvent);
    }
  }
  PurchaseFB(){
    if(this.PixelManagerDataLayer.hasOwnProperty('checkout') && this.PixelManagerDataLayer.checkout.hasOwnProperty('cart_product_list')){
     // var Items = this.GetLineItems(this.PixelManagerDataLayer.checkout.cart_product_list, this.LineItemReq);
      var fb_contents = [];
      var fb_content_ids = [], num_items = 0;
      var Items = this.PixelManagerDataLayer.checkout.cart_product_list;
      for(var i_key in Items){
        num_items+=parseInt(Items[i_key].quantity);
        fb_content_ids.push(Items[i_key].id);
        fb_contents.push({"id":Items[i_key].id, "quantity":Items[i_key].quantity, "item_price":Items[i_key].price});
      }
      //Facebook Conversio API
      if(this.isFacebookConversionAPIEnable()){
        var data = {
          action: "pmw_call_facebook_converstion_api",
          fb_event:"Purchase",
          event_id:this.PMWEventID,
          order_id:this.PixelManagerDataLayer.checkout.id,
          prodct_data:fb_contents,
          custom_data :{
            value: this.PixelManagerDataLayer.checkout.total,
            //currency: this.PixelManagerDataLayer.currency,
            content_ids: fb_content_ids,
            content_type: "product_group"
          }
        };
        jQuery.ajax({
          type: "POST",
          dataType: "json",
          url:  pmw_f_ajax_url,
          data: data,
          success: function (response) {
          }
        });
      }
      //End Facebook Conversio API
    }
  }
  IsEmpty(List){
    if(List != undefined && Object.keys(List).length > 0 ){
      return false;
    }
    return true;
  }
  ConvertArrayToString(value){
    if(Object.keys(value).length > 0){
      return value.join(", ");
    }else{
      return value;
    }
  }
  GetProductFromProductList(product_id){    
     if(this.PixelManagerDataLayer.hasOwnProperty('product_list') && product_id != ""){
      var ProductList = this.PixelManagerDataLayer.product_list;
      if(!this.IsEmpty(ProductList) ){        
        for(var dataLayer_item in ProductList){
          if(ProductList[dataLayer_item].hasOwnProperty('id')){
            var id = ProductList[dataLayer_item].id;
            if(product_id == id){
              return ProductList[dataLayer_item];
            }
          }
        }
      }
    }
  }
  GetProductFromCartProductList(product_id){
    if(this.PixelManagerDataLayer.hasOwnProperty('checkout') && this.PixelManagerDataLayer.checkout.hasOwnProperty('cart_product_list')){
      var ProductList = this.PixelManagerDataLayer.checkout.cart_product_list;
      if(!this.IsEmpty(ProductList) ){        
        for(var dataLayer_item in ProductList){
          if(ProductList[dataLayer_item].hasOwnProperty('id')){
            var id = ProductList[dataLayer_item].id;
            if(product_id == id){
              return ProductList[dataLayer_item];
            }
          }
        }
      }
    }
  }
  /*get_variation_data(obj){
    //console.log(p_attributes);
    var p_v_title = "";
    var variation_data = [];
    if(Object.keys(obj).length >0 ){
      for(var dataLayer_item in obj) {
        if(obj[dataLayer_item].hasOwnProperty("attributes")){
          for(var p_attributes in obj[dataLayer_item]) {
            if(obj[dataLayer_item].hasOwnProperty(p_attributes)){
              p_v_title+=(p_v_title=="")?p_attributes[index]:' | '+p_attributes[index]; 
            }      
          }
          variation_data.push(p_v_title);
        }      
      }
      return variation_data;
    }
  }
  get_item_form_items_by_key_val(obj, key, val){
    if(val != ""){
      if(Object.keys(obj).length >0 ){
        for(var dataLayer_item in obj){
          if(obj[dataLayer_item].hasOwnProperty(key)){
            var map_val = obj[dataLayer_item][key]; 
            if(val == map_val){
              return obj[dataLayer_item];
            }
          }
        }
      }
    }
  }*/

  GetProductFromProductListByURL(prod_obj, key, product_url){
    if(this.IsEmpty(prod_obj)){
      return [];
    }
    if(product_url != ""){      
      for(var dataLayer_item in prod_obj){
        if(prod_obj[dataLayer_item].hasOwnProperty(key)){
          var map_val = prod_obj[dataLayer_item][key]; 
          if(product_url == map_val){
            return prod_obj[dataLayer_item];
          }
        }
      }      
    }
  }
  GetLineItems( ProductList, LineItemReq, PixelName = null ){
    if(this.IsEmpty(ProductList)){
      return [];
    }
    var this_var = this;
    var ProductItems = [];
    if(Object.keys(ProductList).length > 0){
      Object.keys(ProductList).forEach(function(KeyVal, index){        
        if(ProductList.hasOwnProperty(KeyVal)){
          var Item = ProductList[KeyVal];
          var NewItem = this_var.GetLineItem(Item, LineItemReq, PixelName);
          if(Object.keys(NewItem).length > 0){
            ProductItems.push( NewItem );
          }
        }        
      });      
    }
    return ProductItems;
  }
  GetLineItem( Product, LineItemReq, PixelName = null){
    if(this.IsEmpty(Product)){
      return [];
    }
    var ProductItem = {};
    if(Object.keys(LineItemReq).length > 0){
      Object.keys(LineItemReq).forEach(function(KeyVal, index){
        var ItemKey = LineItemReq[KeyVal];
        if(Product.hasOwnProperty(ItemKey)){
          if(KeyVal == "item_category"){
            Object.keys(Product[ItemKey]).forEach(function(c_KeyVal, c_index){
              if(c_KeyVal == 0 ){
                ProductItem[KeyVal] = Product[ItemKey][c_index];
              }else{
                ProductItem[KeyVal+(c_index+1)] = Product[ItemKey][c_index];
              }
            });
          }else if(Product[ItemKey] != '' && Product[ItemKey] != null && Product[ItemKey] != 'undefined' ){
            ProductItem[KeyVal] = Product[ItemKey];
          }
        }
      });
      /*if(PixelName == "Tiktok"){
        ProductItem['currency'] = DataLayer.currency;
      }*/
    }
    return ProductItem;    
  }
  getParameterByName(name, url = window.location.href){
    name = name.replace(/[\[\]]/g, '\\$&');
    var regex = new RegExp('[?&]' + name + '(=([^&#]*)|&|#|$)'),
        results = regex.exec(url);
    if (!results) return null;
    if (!results[2]) return '';
    return decodeURIComponent(results[2].replace(/\+/g, ' '));
  }
  isFacebookConversionAPIEnable(){
    if(this.PixelManagerOptions.hasOwnProperty("fb_conversion_api") && this.PixelManagerOptions.fb_conversion_api.is_enable){
      return true;
    }else{
      return false;
    }
  }
  isGA4Enable(){
    if(this.PixelManagerOptions.hasOwnProperty("google_analytics_4_pixel") && this.PixelManagerOptions.google_analytics_4_pixel.is_enable ){
      return true;
    }else{
      return false;
    }
  }
}