
/* ------------------------------------------------------------------------------------
   This module contains javascripts functions used only for the SCM multivendor functionality.
   ----------------------------------------------------------------------------------- */

jQuery(document).ready(function () {
	
		var symbol = jQuery( ".wcfm_dashboard_stats_block").find( ".woocommerce-Price-currencySymbol" ).html();
						
		jQuery( ".wcfm_dashboard_stats_block").find( ".wcfmfa.fa-currency" ).html(symbol);

      //wcfm_menu_items wcfm_menu_scd-menu
    jQuery('.wcfm_menu_scd-menu-dash a').click(function (e) {
        jQuery(this).toggleClass('active').parent().siblings().children().removeClass('active');
        e.preventDefault();
		
	    var data = {
					'action': 'scd_show_user_currency'
		}
		jQuery.post(scd_ajax.ajax_url, data, function(response) {
			    jQuery('.wcfm-collapse-content').html(response);
                jQuery('.wcfm-page-heading-text').html('Set user currency');
                if(jQuery(window).width() < 700){
                    jQuery('.wcfm_menu_toggler').eq(0).click();    
                }
		});
       
    });
    
    jQuery('a.vendor_edit_zone__ss').click(function(){
        //data-user-id="2" data-zone-id="2"
        var vendor_id=jQuery(this).attr('data-user-id');
        var zone_id=jQuery(this).attr('data-zone-id');
        console.log('data attr vendor='+vendor_id+' zone=='+zone_id);
        
        var data = {
                action: 'scd-wcfmmp-get-shipping-zone',
                zoneID: zoneID,
                userID: userID
            };
      
        jQuery.post(ajaxurl, data, function (resp) {
            if( resp && resp.success ) {
                $('#vendor_edit_zone').html(resp.data.html).show();
    //          $('.wcfmmp-table.shipping-zone-table, #wcfmmp_settings_form_shipping_expander').hide();
    //          $('.wcfm-container').unblock();
            }
            //$('body').trigger('zone_settings_loaded');
        });
    });
      //wcfm_menu_items wcfm_menu_scd-menu

    jQuery('#scd-wcv-select-currencies').attr('multiple', 'TRUE');
    jQuery('#scd-wcv-select-currencies').val(null).trigger('change');
//jQuery('#scd-wcv-select-currencies').val(null).trigger('change');
    jQuery(".scd_wcv_select").data("placeholder", "Set currency per product...").chosen();

    jQuery('.scd_wcfm_select_price').attr('disabled', 'true');
    jQuery(".scd_wcfm_select, .scd_wcv_select").change(function () {
        var key = '';

        var newKeys, oldKeys;

        oldKeys = jQuery('#scd-bind-select-curr').val().toString().split(',');

        if (!jQuery('#scd-wcv-select-currencies').val() == '')
            newKeys = jQuery('#scd-wcv-select-currencies').val().toString().split(',');
        else {
            newKeys = '';
        }

        if (jQuery('#scd-bind-select-curr').val() !== '') {

            if (newKeys.length >= oldKeys.length) {

                if (newKeys.length > 0) {
                    key = newKeys[newKeys.length - 1];
                    for (var id = 0; id < newKeys.length; id++) {
                        if (oldKeys.includes(newKeys[id]) == false)
                            key = newKeys[id];
                    }
                }

                var myregselect = '<option id="reg_' + key + '" value=' + key + ' >Regular price (' + key + ')</option>';
                var mysalselect = '<option id="reg_' + key + '" value=' + key + ' >Sale price (' + key + ')</option>';
                jQuery('#scd_regularCurrency').append(myregselect);
                jQuery('#scd_saleCurrency').append(mysalselect);
                jQuery('#scd-bind-select-curr').val(jQuery('#scd-wcv-select-currencies').val());

            } else {
                for (var k = 0; k < oldKeys.length; k++) {
                    if (newKeys.indexOf(oldKeys[k]) == -1) {
                        jQuery('#scd_regularCurrency option[value="' + oldKeys[k] + '"]').remove();
                        jQuery('#scd_saleCurrency option[value="' + oldKeys[k] + '"]').remove();
                    }
                }
            }
            jQuery('#scd-bind-select-curr').val(jQuery('#scd-wcv-select-currencies').val());
        } else {
            if (newKeys.length > 0) {
                key = newKeys[newKeys.length - 1];
            }
            var myregselect = '<option id="reg_' + key + '" value=' + key + ' >Regular price (' + key + ')</option>';
            var mysalselect = '<option id="sale_' + key + '" value=' + key + ' >Sale price (' + key + ')</option>';
            jQuery('#scd_regularCurrency').append(myregselect);
            jQuery('#scd_saleCurrency').append(mysalselect);
            jQuery('#scd-bind-select-curr').val(jQuery('#scd-wcv-select-currencies').val());

        }

        if (jQuery(this).val() !== null) {
            var tabCurr = jQuery(this).val().toString().split(',');
            if (tabCurr.length > 0) {
                var regularBloc = '';
                var saleBloc = '';
                var newpriceField = '';
                var priceField = jQuery('#priceField').val();
                var tabC;
                for (var i = 0; i < tabCurr.length; i++) {
                    regularBloc = 'regular_' + tabCurr[i] + '_';
                    saleBloc = '-sale_' + tabCurr[i] + '_';
                    var regularPrice = '', salePrice = '';
                    if (priceField.indexOf(regularBloc) > -1) {
                        regularPrice = priceField.substr(priceField.indexOf(regularBloc) + regularBloc.length,
                                priceField.indexOf(saleBloc) - priceField.indexOf(regularBloc) - regularBloc.length);

                        tabC = priceField.toString().split(',');
                        var pos = -1;
                        for (var j = 0; j < tabC.length; j++) {
                            if (tabC[j].indexOf('sale_' + tabCurr[i]) > -1) {
                                pos = j;
                            }
                        }

                        if (pos > -1) {
                            var tc = tabC[pos].toString().split('_');
                            if (tc.length > 0) {
                                salePrice = tc[tc.length - 1];
                            }
                        }
                    }
                    if (i == 0) {
                        newpriceField = 'regular_' + tabCurr[i] + '_' + regularPrice + '-sale_' + tabCurr[i] + '_' + salePrice;
                    } else {
                        newpriceField = newpriceField + ',regular_' + tabCurr[i] + '_' + regularPrice + '-sale_' + tabCurr[i] + '_' + salePrice;
                    }
                }
                jQuery('#priceField').val(newpriceField);
            }
        }
    });

    // binding '#scd_regularCurrency' and #scd_saleCurrency'
    jQuery('#scd_regularCurrency').change(function () {
        jQuery('#scd_saleCurrency').val(jQuery('#scd_regularCurrency').val()).change();
        //jQuery('#scd_regularPriceCurrency').val( jQuery('#regularField_'+jQuery('#scd_regularCurrency').val()).val());
        //jQuery('#scd_salePriceCurrency').val( jQuery('#saleField_'+jQuery('#scd_saleCurrency').val()).val());
        var priceField = jQuery('#priceField').val();

        var regularBloc = 'regular_' + jQuery('#scd_regularCurrency').val() + '_';
        var saleBloc = '-sale_' + jQuery('#scd_regularCurrency').val() + '_';
        var price = priceField.substr(priceField.indexOf(regularBloc) + regularBloc.length,
                priceField.indexOf(saleBloc) - priceField.indexOf(regularBloc) - regularBloc.length);
        jQuery('#scd_regularPriceCurrency').val(price);

        var tabCurr = priceField.toString().split(',');
        var pos = -1;
        for (var j = 0; j < tabCurr.length; j++) {
            if (tabCurr[j].indexOf('sale_' + jQuery('#scd_saleCurrency').val()) > -1) {
                pos = j;
            }
        }

        if (pos > -1) {
            var tc = tabCurr[pos].toString().split('_');
            if (tc.length > 0) {
                jQuery('#scd_salePriceCurrency').val(tc[tc.length - 1]);

            }
        }

    });
    // end binding

    // start save regular price entered for each currency when hoverout field  
    jQuery('#scd_regularPriceCurrency').focusout(function () {
        // jQuery('#regularField_'+jQuery('#scd_regularCurrency').val()).val(jQuery(this).val());

        var priceField = jQuery('#priceField').val();
        var regularBloc = 'regular_' + jQuery('#scd_regularCurrency').val() + '_';
        var saleBloc = '-sale_' + jQuery('#scd_regularCurrency').val() + '_';

        priceField = priceField.substr(0, priceField.indexOf(regularBloc)) + regularBloc + jQuery(this).val() +
                priceField.substr(priceField.indexOf(saleBloc));
        jQuery('#priceField').val(priceField);

    });
    // end save regular price

    // start save sale price entered for each currency when hoverout field  
    jQuery('#scd_salePriceCurrency').focusout(function () {
        //jQuery('#saleField_'+jQuery('#scd_saleCurrency').val()).val(jQuery(this).val());

        var priceField = jQuery('#priceField').val();
        var tabCurr = priceField.toString().split(',');
        var pos = -1;
        for (var j = 0; j < tabCurr.length; j++) {
            if (tabCurr[j].indexOf('sale_' + jQuery('#scd_saleCurrency').val()) > -1) {
                pos = j;
            }
        }
        if (pos > -1) {
            tabCurr[pos] = tabCurr[pos].substr(0, tabCurr[pos].indexOf('sale')) + 'sale_' + jQuery('#scd_saleCurrency').val() + '_' + jQuery(this).val();
            priceField = tabCurr[0];
            for (var j = 1; j < tabCurr.length; j++) {
                priceField = priceField + ',' + tabCurr[j];
            }

            jQuery('#priceField').val(priceField);
        }
    });
    // end save sale price

// end save sale price
        
    jQuery(document).on('click', '#wcfmmp_shipping_method_edit_button', function (e) {
    e.preventDefault();
    //begin
    var methodID = jQuery('#wcfmmp_shipping_method_edit_container #method_id_selected').val(),
    instanceId = jQuery('#wcfmmp_shipping_method_edit_container #instance_id_selected').val(),
    zoneId = jQuery('#zone_id').val();
    userId = jQuery('#user_vendor_id').val();
    var data = {
    action: 'scd_wcfmmp_update_shipping_method',
    zoneID: zoneId,

    args: {
    instance_id: instanceId,
    zone_id: zoneId,
    user_id: userId,
    method_id: methodID,
    settings: {}
    }
    };
    if( methodID == 'free_shipping' ) {
    data.args.settings.title = jQuery('#free_shipping #method_title_fs').val();
    data.args.settings.description = jQuery('#free_shipping #method_description_fs').val();
    data.args.settings.cost = 0;
    data.args.settings.tax_status = 'none';
    data.args.settings.min_amount = jQuery('#free_shipping #minimum_order_amount_fs').val();
    } else if( methodID == 'local_pickup' ) {
    data.args.settings.title = jQuery('#local_pickup #method_title_lp').val();
    data.args.settings.description = jQuery('#local_pickup #method_description_lp').val();
    data.args.settings.cost = jQuery('#local_pickup #method_cost_lp').val();
    data.args.settings.tax_status = jQuery('#local_pickup #method_tax_status_lp option:selected').val();

    } else if( methodID == 'flat_rate' ) {
    data.args.settings.title = jQuery('#flat_rate #method_title_fr').val();
    data.args.settings.description = jQuery('#flat_rate #method_description_fr').val();
    data.args.settings.cost = jQuery('#flat_rate #method_cost_fr').val();
    data.args.settings.tax_status = jQuery('#flat_rate #method_tax_status_fr option:selected').val();
    jQuery('.sc_vals').each(function(){
    data.args.settings['class_cost_'+ jQuery(this).attr('data-shipping_class_id')] = jQuery(this).val();
    });
    data.args.settings.calculation_type = jQuery('#flat_rate #calculation_type').val();
    } else {
    data.args.settings.title = jQuery('#wcfmmp_csm #method_title_csm').val();
    data.args.settings.cost = jQuery('#wcfmmp_csm #method_cost_csm').val();
    data.args.settings.description = jQuery('#wcfmmp_csm #method_description_cm').val();
    }

      setTimeout(function(){
          jQuery.post(ajaxurl, data, function (resp) {
                    //alert();
        });
      },1000)   
      
    });

    //edit shippind method fro wcfm
    jQuery(document).on('click', '.edit_shipping_method', function(){
    $parents = jQuery(this).parents('.edit_del_actions');

    var instanceId = $parents.attr('data-instance_id');
    zoneId = jQuery('#zone_id').val();
    methodId = $parents.attr('data-method_id');
           
    var data={
    action: 'scd_wcfmmp_get_shipping_settings',
    instance_id: instanceId
    };
     jQuery('.wcfm-collapse-content').hide();
    jQuery.post(ajaxurl,data,function(resp){
                
    if(resp.success){
        if(resp.data.settings!==undefined){
       if(resp.data.settings['cost']!==undefined){
           //falte rate type
      jQuery('#method_cost_fr').val(resp.data.settings['cost']);
      //local pickup type
      jQuery('#method_cost_lp').val(resp.data.settings['cost']);
       }
       //minimum_order_amount_fs
       if(resp.data.settings['min_amount']!==undefined){
           //free shipping type
      jQuery('#minimum_order_amount_fs').val(resp.data.settings['min_amount']);
      }
      jQuery('.sc_vals').each(function(){
            var class_id = jQuery(this).attr('data-shipping_class_id');
            if(resp.data.settings['class_cost_'+ class_id]!==undefined)
           jQuery(this).val(resp.data.settings['class_cost_'+ class_id]);
       });
    //var data=JSON.stringify(resp.data);
    //data=data.toString().replace('[','');
    //data=data.replace(']','');
            if(resp.data.settings['class_cost_no_class_cost']!==undefined)
      jQuery('#no_class_cost').val(resp.data.settings.class_cost_no_class_cost);
      
    //$parents.attr('data-method-settings',data);
    //console.log(data );
    }
      //add  urecy symbol to cost label for flate rate type
      if(jQuery('.method_cost_fr strong').html().toString().indexOf('(')===-1){
          jQuery('.method_cost_fr strong').append(resp.data.currency);
       jQuery('.wcfmmp_shipping_classes p.wcfm_title:not(.calculation_type) strong').append(resp.data.currency);
       //jQuery('.no_class_cost strong').append(resp.data.currency);
      }
      
      //add  urecy symbol to cost label for local pickup type
      if(jQuery('.method_cost_lp strong').html().toString().indexOf('(')===-1){
          jQuery('.method_cost_lp strong').append(resp.data.currency);
     }
      //minimum_order_amount_fs
      if(jQuery('.minimum_order_amount_fs strong').html().toString().indexOf('(')===-1){
          jQuery('.minimum_order_amount_fs strong').append(resp.data.currency);
     }
    }
    jQuery('.wcfm-collapse-content').show();
    });
});
})