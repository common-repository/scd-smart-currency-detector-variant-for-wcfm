
jQuery(document).ready(function () {
  //--------------------------------------------------------------------------------------------
  // set base currency if vendor select Base currency only 
  jQuery(document).on('change', '#scd-currency-option', function (e) {
    e.preventDefault();
    if(jQuery(this).children("option:selected").val().indexOf('base-currency') !== -1){
        jQuery('#scd-currency-list').val(settings.baseCurrency);
    }
  });
  // use currency on default currency only if vendor select other currency
  jQuery(document).on('change', '#scd-currency-list', function (e) {
    e.preventDefault();
    if(jQuery(this).children("option:selected").val() !== settings.baseCurrency){
      jQuery('#scd-currency-option').val('only-default-currency');
    }
  });
//-----------------------------------------------------------------------------
    jQuery(document).on('click', '#scd-save-curr', function (e) {
        e.preventDefault();
        var user_currency = jQuery('#scd-currency-list').val();
        jQuery.post(
                ajaxurl,
                {
                    'action': 'scd_update_user_currency',
                    'user_currency': user_currency
                },
                function (response) {
                    jQuery('#scd-action-status').html(response);
                }
        );
    });
    
    jQuery(document).on('click', '#scd-save-currency-option', function (e) {
        e.preventDefault();
        var user_currency_option = jQuery('#scd-currency-option').val();
        jQuery.post(
                ajaxurl,
                {
                    'action': 'scd_update_user_currency_option',
                    'user_currency_option': user_currency_option
                },
                function (response) {
                    jQuery('#scd-action-status').html(response);
                }
        );
        // save user currency
        var user_currency = jQuery('#scd-currency-list').val();
        jQuery.post(
                ajaxurl,
                {
                    'action': 'scd_update_user_currency',
                    'user_currency': user_currency
                },
                function (response) {
                    jQuery('#scd-action-status').html(response);
                }
        );
    });

//close modal windows
    jQuery(document).on('click','.close-btn',function (){
        //jQuery(document).find('#scd-show-user-curr').css('visibility','hidden');
        jQuery(document).find('.scd-modal').css('visibility','hidden');
        jQuery('.wcfm_menu_scd-menu-dash a,.scd_dokan_menu a,.wcmp-venrod-dashboard-nav-link--scd_setting,#dashboard-menu-item- a,#dashboard-menu-item-scd a').removeClass('active');
    });

//wc pao compatibibility
jQuery('.wc-pao-addon-checkbox,.wc-pao-addon-image-swatch,.wc-pao-addon-radio').click(function(){
      setTimeout(function (){
             scd_wc_pao_conversion();
           },200);
    });
    
//wc pao compatibibility
jQuery('.wc-pao-addon-select').change(function (){
setTimeout(function (){
scd_wc_pao_conversion();
},300);
});

jQuery('.wc-pao-addon-input-multiplier').focus(function (){
setTimeout(function (){
scd_wc_pao_conversion();
},300);
}).focusout(function (){
setTimeout(function (){
scd_wc_pao_conversion();
},300);
}).dblclick(function (){
setTimeout(function (){
scd_wc_pao_conversion();
},300);
});

//hide custom price input input-text wc-pao-addon-field wc-pao-addon-custom-price
if(jQuery('.wc-pao-addon-custom-price').length>0){
      jQuery('.wc-pao-addon-custom-price').attr('type','hidden');
      var new_inp='<input type="number" step="any" class="input-text scd-wc-pao-addon-custom-price" name="scd-wc-pao-customm-price"  data-price-type="flat_fee" value="" min="58" max="100">';

      jQuery('.wc-pao-addon-custom-price').parent().append(new_inp);
        jQuery('.scd-wc-pao-addon-custom-price').focusout(function(){
            var price=jQuery(this).val();
           if(price!==''){
            var target_currency=scd_getTargetCurrency();
            //convert the value put by cutomer in target currency to basecurrency to pass it to wc pao
           price= scd_convert_value(price,target_currency,settings.baseCurrency);
            jQuery('.wc-pao-addon-custom-price').val(price);
             jQuery('.wc-pao-addon-custom-price').trigger('woocommerce-product-addons-update');;
           
             setTimeout(function (){
             scd_wc_pao_conversion();
           },300);
       
             }
        }).focusin(function (){
           setTimeout(function (){
             scd_wc_pao_conversion();
           },300);
        }).dblclick(function (){
           setTimeout(function (){
             scd_wc_pao_conversion();
           },300);
        });
}

    jQuery('.wc-pao-addon-custom-text,.wc-pao-addon-custom-textarea').keypress(function(){
      setTimeout(function (){
             scd_wc_pao_conversion();
           },200);
    }).focusout(function(){
      setTimeout(function (){
             scd_wc_pao_conversion();
           },200);
    });
    
    jQuery('li.scd-curr-menu').click(function (){
        jQuery(this).find('ul').toggle();
    });
});
