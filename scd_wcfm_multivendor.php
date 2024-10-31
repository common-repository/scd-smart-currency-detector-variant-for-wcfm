<?php
/* -------------------------------------------------------
  This module contains functions used only for the SCM multivendor functionality.
  It is included by the index.php file.
  ------------------------------------------------------- */

//    add_action('dokan_get_all_cap','scd_dokan_capability');
//    function scd_dokan_capability($capabilities) {
//        $capabilities['menu']['dokan_view_scd_currency_menu']=__( 'View scd currency menu', 'dokan-lite' );
//        return $capabilities;
//    }
//  
add_action( 'init', function () {
    
    global $WCFM, $WCFMmp;

remove_action( 'after_wcfm_ajax_controller', array( $WCFMmp->wcfmmp_ledger, 'wcfm_ledger_ajax_controller' ) );
remove_action( 'after_wcfm_ajax_controller', array( $WCFM->wcfm_withdrawal, 'wcfm_withdrawal_ajax_controller' ) );
	
}, 999 );
	
	add_action( 'after_wcfm_ajax_controller', 'scd_wcfm_withdrawal_ajax_controller' );
function scd_wcfm_withdrawal_ajax_controller() {
  	global $WCFM;
  	
  	if ( ! check_ajax_referer( 'wcfm_ajax_nonce', 'wcfm_ajax_nonce', false ) ) {
  		wp_send_json_error( esc_html__( 'Invalid nonce! Refresh your page and try again.', 'scd_wcfm_marketplace' ) );
  		wp_die();
  	}
  	
  	$controllers_path = $WCFM->plugin_path . 'controllers/withdrawal/';
  	
  	$controller = '';
  	if( isset( $_POST['controller'] ) ) {
  		$controller = wc_clean( $_POST['controller'] );
  		switch( $controller ) {
  			case 'wcfm-payments':
  				if ( !current_user_can( 'manage_woocommerce' ) && !current_user_can( 'wcfm_vendor' ) && !current_user_can( 'seller' ) && !current_user_can( 'vendor' ) && !current_user_can( 'shop_staff' ) ) {
						wp_send_json_error( esc_html__( 'You don&#8217;t have permission to do this.', 'scd_wcfm_marketplace' ) );
						wp_die();
					}
		
  				if( $WCFM->is_marketplace == 'wcmarketplace' ) {
						include_once( $controllers_path . 'wcmp/wcfm-controller-payments.php' );
						new WCFM_Payments_Controller();
					} elseif( $WCFM->is_marketplace == 'wcfmmarketplace' ) {
						include_once( $controllers_path . 'wcfm/wcfm-controller-payments.php' );
						new WCFM_Payments_Controller();
					} elseif( $WCFM->is_marketplace == 'dokan' ) {
						include_once( $controllers_path . 'dokan/wcfm-controller-payments.php' );
						new WCFM_Payments_Controller();
					}
  			break;
  			
  			case 'wcfm-withdrawal':
  				if ( !current_user_can( 'manage_woocommerce' ) && !current_user_can( 'wcfm_vendor' ) && !current_user_can( 'seller' ) && !current_user_can( 'vendor' ) && !current_user_can( 'shop_staff' ) ) {
						wp_send_json_error( esc_html__( 'You don&#8217;t have permission to do this.', 'scd_wcfm_marketplace' ) );
						wp_die();
					}
		
  				if( $WCFM->is_marketplace == 'wcmarketplace' ) {
						include_once( $controllers_path . 'wcmp/wcfm-controller-withdrawal.php' );
						new WCFM_Withdrawal_Controller();
					} elseif( $WCFM->is_marketplace == 'wcfmmarketplace' ) {
						include_once( SCDS_PLUGIN_DIR_PATH . 'wcfm-controller-withdrawal.php' );
						new SCD_WCFM_Withdrawal_Controller();
					} elseif( $WCFM->is_marketplace == 'wcfmmarketplace' ) {
						include_once( SCDS_PLUGIN_DIR_PATH . 'wcfm-controller-withdrawal.php' );
						new SCD_WCFM_Withdrawal_Controller();
					}
  			break;
  			
  			case 'wcfm-withdrawal-request':
  				if ( !current_user_can( 'manage_woocommerce' ) && !current_user_can( 'wcfm_vendor' ) && !current_user_can( 'seller' ) && !current_user_can( 'vendor' ) && !current_user_can( 'shop_staff' ) ) {
						wp_send_json_error( esc_html__( 'You don&#8217;t have permission to do this.', 'scd_wcfm_marketplace' ) );
						wp_die();
					}
		
  				if( $WCFM->is_marketplace == 'wcmarketplace' ) {
						include_once( $controllers_path . 'wcmp/wcfm-controller-withdrawal-request.php' );
						new WCFM_Withdrawal_Request_Controller();
					} elseif( $WCFM->is_marketplace == 'wcfmmarketplace' ) {
						include_once( $controllers_path . 'wcfm/wcfm-controller-withdrawal-request.php' );
						new WCFM_Withdrawal_Request_Controller();
					} elseif( $WCFM->is_marketplace == 'dokan' ) {
						include_once( $controllers_path . 'dokan/wcfm-controller-withdrawal-request.php' );
						new WCFM_Withdrawal_Request_Controller();
					}
  			break;
  			
  			case 'wcfm-withdrawal-requests':
  				if ( !current_user_can( 'manage_woocommerce' ) && !current_user_can( 'wcfm_vendor' ) && !current_user_can( 'seller' ) && !current_user_can( 'vendor' ) && !current_user_can( 'shop_staff' ) ) {
						wp_send_json_error( esc_html__( 'You don&#8217;t have permission to do this.', 'scd_wcfm_marketplace' ) );
						wp_die();
					}
		
  				if( $WCFM->is_marketplace == 'wcmarketplace' ) {
						//include_once( $controllers_path . 'wcmp/wcfm-controller-withdrawal-request.php' );
						//new WCFM_Withdrawal_Request_Controller();
					} elseif( $WCFM->is_marketplace == 'wcfmmarketplace' ) {
						include_once( $controllers_path . 'wcfm/wcfm-controller-withdrawal-requests.php' );
						new WCFM_Withdrawal_Requests_Controller();
					} elseif( $WCFM->is_marketplace == 'dokan' ) {
						include_once( $controllers_path . 'dokan/wcfm-controller-withdrawal-requests.php' );
						new WCFM_Withdrawal_Requests_Controller();
					}
  			break;
  			
  			case 'wcfm-withdrawal-requests-approve':
  				if ( !current_user_can( 'manage_woocommerce' ) && !current_user_can( 'shop_staff' ) ) {
						wp_send_json_error( esc_html__( 'You don&#8217;t have permission to do this.', 'scd_wcfm_marketplace' ) );
						wp_die();
					}
		
  				if( $WCFM->is_marketplace == 'wcmarketplace' ) {
						//include_once( $controllers_path . 'wcmp/wcfm-controller-withdrawal-request.php' );
						//new WCFM_Withdrawal_Request_Controller();
					} elseif( $WCFM->is_marketplace == 'wcfmmarketplace' ) {
						include_once( $controllers_path . 'wcfm/wcfm-controller-withdrawal-requests-actions.php' );
						new WCFM_Withdrawal_Requests_Approve_Controller();
					} elseif( $WCFM->is_marketplace == 'dokan' ) {
						include_once( $controllers_path . 'dokan/wcfm-controller-withdrawal-requests-actions.php' );
						new WCFM_Withdrawal_Requests_Approve_Controller();
					}
  			break;
  			
  			case 'wcfm-withdrawal-requests-cancel':
  				if ( !current_user_can( 'manage_woocommerce' ) && !current_user_can( 'shop_staff' ) ) {
						wp_send_json_error( esc_html__( 'You don&#8217;t have permission to do this.', 'scd_wcfm_marketplace' ) );
						wp_die();
					}
		
  				if( $WCFM->is_marketplace == 'wcmarketplace' ) {
						//include_once( $controllers_path . 'wcmp/wcfm-controller-withdrawal-request.php' );
						//new WCFM_Withdrawal_Request_Controller();
					} elseif( $WCFM->is_marketplace == 'wcfmmarketplace' ) {
						include_once( $controllers_path . 'wcfm/wcfm-controller-withdrawal-requests-actions.php' );
						new WCFM_Withdrawal_Requests_Cancel_Controller();
					} elseif( $WCFM->is_marketplace == 'dokan' ) {
						include_once( $controllers_path . 'dokan/wcfm-controller-withdrawal-requests-actions.php' );
						new WCFM_Withdrawal_Requests_Cancel_Controller();
					}
  			break;
  			
  			case 'wcfm-withdrawal-reverse':
  				if ( !current_user_can( 'manage_woocommerce' ) && !current_user_can( 'wcfm_vendor' ) && !current_user_can( 'seller' ) && !current_user_can( 'vendor' ) && !current_user_can( 'shop_staff' ) ) {
						wp_send_json_error( esc_html__( 'You don&#8217;t have permission to do this.', 'scd_wcfm_marketplace' ) );
						wp_die();
					}
		
					if( $WCFM->is_marketplace == 'wcfmmarketplace' ) {
						include_once( $controllers_path . 'wcfm/wcfm-controller-withdrawal-reverse.php' );
						new WCFM_Withdrawal_Reverse_Controller();
					}
  			break;
  			
  			case 'wcfm-withdrawal-reverse-approve':
  				if ( !current_user_can( 'manage_woocommerce' ) && !current_user_can( 'shop_staff' ) ) {
						wp_send_json_error( esc_html__( 'You don&#8217;t have permission to do this.', 'scd_wcfm_marketplace' ) );
						wp_die();
					}
		
					if( $WCFM->is_marketplace == 'wcfmmarketplace' ) {
						include_once( $controllers_path . 'wcfm/wcfm-controller-withdrawal-reverse-actions.php' );
						new WCFM_Withdrawal_Reverse_Approve_Controller();
					}
  			break;
  		}
  	}
  }
if( apply_filters( 'wcfm_is_pref_ledger_book', true ) && apply_filters( 'wcfm_is_allow_ledger', true ) && ( !is_admin() || defined('DOING_AJAX') ) ) {
	
	add_action('after_wcfm_ajax_controller', 'scd_wcfm_ledger_ajax_controller');
	function scd_wcfm_ledger_ajax_controller(){
    global $WCFM, $WCFMmp;

if ( ! check_ajax_referer( 'wcfm_ajax_nonce', 'wcfm_ajax_nonce', false ) ) {
    wp_send_json_error( esc_html__( 'Invalid nonce! Refresh your page and try again.', 'scd_wcfm_marketplace' ) );
    wp_die();
}

// $controllers_path = SCDS_PLUGIN_DIR_PATH;

$controller = '';
if( isset( $_POST['controller'] ) ) {
    $controller = sanitize_text_field( $_POST['controller'] );
    switch( $controller ) {
        case 'wcfm-ledger':
            if ( !current_user_can( 'manage_woocommerce' ) && !current_user_can( 'wcfm_vendor' ) && !current_user_can( 'shop_staff' ) ) {
                  wp_send_json_error( esc_html__( 'You don&#8217;t have permission to do this.', 'scd_wcfm_marketplace' ) );
                  wp_die();
              }
  
              include_once( SCDS_PLUGIN_DIR_PATH . 'wcfmmp-controller-ledger.php' );
              new SCD_WCFMmp_Ledger_Controller();
        break;
    }
}
}
}


add_action( 'wcfm_product_quick_edit_save','scd_quick_save_product_prices',10,3);
function scd_quick_save_product_prices($product_id, $product, $wcfm_quick_edit_form_data) {
    $user_curr = scd_get_user_currency();
    update_post_meta($product_id, '_meta_sale_price', $wcfm_quick_edit_form_data['wcfm_quick_edit_sale_price']);
    update_post_meta($product_id, '_meta_regular_price', $wcfm_quick_edit_form_data['wcfm_quick_edit_regular_price']);
    update_post_meta($product_id, '_meta_currency', $user_curr);
    //save the equivalent price entered by user in base currency
    $converted = scd_function_convert_subtotal($wcfm_quick_edit_form_data['wcfm_quick_edit_regular_price'], get_option('wocommerce_currency'), $user_curr, 2, TRUE);
    $product->set_regular_price( wc_format_decimal($converted) );

    update_post_meta($product_id, '_regular_price', $converted);
    if ($wcfm_quick_edit_form_data['wcfm_quick_edit_sale_price'] !== '') {
        $converted = scd_function_convert_subtotal($wcfm_quick_edit_form_data['wcfm_quick_edit_sale_price'], get_option('wocommerce_currency'), $user_curr, 2, TRUE);
        update_post_meta($product_id, '_sale_price', $converted);
        $product->set_sale_price( wc_format_decimal($converted) );
        update_post_meta($product_id, '_price', $converted);
    } else {
        update_post_meta($product_id, '_price', $converted);
    }
    $product->save();
}

function scd_save_product_prices($post_id, $data) {
    $scd_userRole = scd_get_user_role();
    $scd_userID = get_current_user_id();
    $scd_currencyVal = '';
    if (isset($data['scd_currencyVal'])) {
        //if ($_POST['scd_currencyVal'] !== '') {
        $scd_currencyVal = $data['scd_currencyVal'];
        //}
    }
    $priceField = '';
    if (isset($data['priceField'])) {
        if ($data['priceField'] !== '') {
            $priceField = $data['priceField'];
        }
    }
    // save data
    $user_curr = scd_get_user_currency();
    if ($user_curr !== FALSE && isset($data['scd_sale_price'])) {
        $scd_currencyVal = $user_curr;
        $priceField = 'regular_' . $scd_currencyVal . '_' . $data['scd_regular_price'] . '-sale_' . $scd_currencyVal . '_' . $data['scd_sale_price'];
    }

    $curr_opt = scd_get_user_currency_option();
    if ($user_curr !== FALSE && $user_curr !== get_option('woocommerce_currency') && $curr_opt == 'only-default-currency') {
        $scd_currencyVal = $user_curr;
        $priceField = 'regular_' . $scd_currencyVal . '_' . $data['regular_price'] . '-sale_' . $scd_currencyVal . '_' . $data['sale_price'];
        update_post_meta($post_id, '_meta_sale_price', $data['sale_price']);
        update_post_meta($post_id, '_meta_regular_price', $data['regular_price']);
        update_post_meta($post_id, '_meta_currency', $user_curr);
        //save the equivalent price entered by user in base currency
        $converted = scd_function_convert_subtotal($data['regular_price'], get_option('wocommerce_currency'), $scd_currencyVal, 2, TRUE);

        update_post_meta($post_id, '_regular_price', $converted);
        if ($data['sale_price'] !== '') {
            $converted = scd_function_convert_subtotal($data['sale_price'], get_option('wocommerce_currency'), $scd_currencyVal, 2, TRUE);
            update_post_meta($post_id, '_sale_price', $converted);
            update_post_meta($post_id, '_price', $converted);
        } else {
            update_post_meta($post_id, '_price', $converted);
        }
    } elseif ($user_curr !== FALSE) {
        
    }
    if ($priceField !== '')
        update_post_meta($post_id, 'scd_other_options', array(
            "currencyUserID" => $scd_userID,
            "currencyUserRole" => $scd_userRole,
            "currencyVal" => $scd_currencyVal,
            "currencyPrice" => $priceField
        ));
}

//wcfm integration 
//scd menu in wcfm dashboard
//add_action('wcfm_formeted_menus', 'scd_wcfm_menus_dashboard', 15);
add_filter('wcfm_formeted_menus','scd_wcfm_menus_dashboard',10,1);
function scd_wcfm_menus_dashboard($menu) {
    $menu['scd-menu-dash'] = array(
        'label' => __( 'SCM Currency', 'scd_wcfm_marketplace' ),
        'url' => '#',
        'icon' => 'cogs',
        'id' => 'scd-wcfm-menu',
        'priority' => 5
    );
    return $menu;
}

add_filter('wcfm_product_manage_fields_pricing', 'scd_wcfm_fields', 10, 2);

function scd_wcfm_fields($output, $product_id) {
    $currencyVal = '';
    $currencyPrice = '';
    $reg_price = '';
    $sale_price = '';

    if (get_post_meta($product_id, 'scd_other_options')) {
        $regs = get_post_meta($product_id, 'scd_other_options', TRUE);
        $currencyPrice = $regs["currencyPrice"];
        $currencyVal = $regs["currencyVal"];
    }

    if (isset($regs['currencyVal'])) {
        $currencyVal = $regs['currencyVal'];
    }
    $user_curr = scd_get_user_currency();
    $user_curr_opt = scd_get_user_currency_option();
    $user_curr_opt = scd_get_user_currency_option();

    if ($user_curr == FALSE || $user_curr_opt == 'selected-currencies') {
        $curr_tab = explode(',', $currencyVal);
        $first_curr = (count($curr_tab) > 0) ? $curr_tab[0] : ' ';
        list($regprice, $saleprice) = scd_get_product_custom_price_for_currency($product_id, $first_curr);
        $output["scd_currencyVal"] = array(
            'type' => 'hidden',
            'id' => 'scd-bind-select-curr',
            'value' => $currencyVal);

        $output["priceField"] = array(
            'type' => 'hidden',
            'id' => 'priceField',
            'value' => $currencyPrice);

        $option = array('0' => __( 'Select currency', 'scd_wcfm_marketplace' ));

        $output["scd_currencyVal_seleted"] = array(
            'label' => __('Choose currencies', 'scd_wcfm_marketplace'),
            'type' => 'select',
            'options' => scd_get_list_currencies(), //array('instock' => __('In stock', 'scd_wcfm_marketplace'), 'outofstock' => __('Out of stock', 'scd_wcfm_marketplace')), 
            'class' => 'scd_wcfm_select wcfm-select',
            'id' => 'scd-wcv-select-currencies',
            'label_class' => 'wcfm_el'
        );

        $output["scd_regularCurrency"] = array(
            'type' => 'select',
            'options' => array(),
            'class' => 'scd_price_select scd_wcfm_select_field',
            'data-placeholder' => __( 'Regular price', 'scd_wcfm_marketplace'),
            'id' => 'scd_regularCurrency',
            'style' => 'margin-right: 7px;width: 148px;',
        );
        $output["scd_regularPriceCurrency"] = array(
            'type' => 'text',
            'style' => 'width: 57%;border-radius: 3px;border: 1px solid #ccc;',
            'id' => 'scd_regularPriceCurrency',
            'placeholder' => __( 'Set Regular Price. (e.g. 59.34)', 'scd_wcfm_marketplace'),
            'value' => $regprice
        );

        $output["scd_saleCurrency"] = array(
            'type' => 'select',
            'options' => array(),
            'class' => 'scd_price_select scd_wcfm_select_price scd_wcfm_select_field',
            'data-placeholder' => __( 'Sale price', 'scd_wcfm_marketplace'),
            'id' => 'scd_saleCurrency',
            'style' => 'margin-right: 7px;width: 148px;'
        );
        $output["scd_salePriceCurrency"] = array(
            'type' => 'text',
            'style' => 'width: 57%;border-radius: 3px;border: 1px solid #ccc;',
            'id' => 'scd_salePriceCurrency',
            'placeholder' => __( 'Set Sale Price. (e.g. 59.34)', 'scd_wcfm_marketplace'),
            'value' => $saleprice
        );
        //correct prices due to scd ovewrite
        $regprice = get_post_meta($product_id, '_regular_price', TRUE);
        $saleprice = get_post_meta($product_id, '_sale_price', TRUE);
        $output["regular_price"]['value'] = $regprice;
        $output["sale_price"]['value'] = $saleprice;
    } else {
        $curr_symbol = get_woocommerce_currency_symbol($user_curr);
        list($regprice, $saleprice) = scd_get_product_custom_price_for_currency($product_id, $user_curr);
        if (empty($regprice)) {
            $regprice = get_post_meta($product_id, '_regular_price', true);
            $regprice = scd_function_convert_subtotal($regprice, get_option('wocommerce_currency'), $user_curr, 2);
            $saleprice = get_post_meta($product_id, '_sale_price', true);
            $saleprice = scd_function_convert_subtotal($saleprice, get_option('wocommerce_currency'), $user_curr, 2);
        }
        if ($user_curr_opt == 'base-and-default-currency') {
            $output["scd_regular_price"] = array(
                'label' => __('Price', 'scd_wcfm_marketplace') . '(' . get_woocommerce_currency_symbol($user_curr) . ')',
                'type' => 'text',
                'class' => 'wcfm-text wcfm_ele wcfm_half_ele simple',
                'label_class' => 'wcfm_ele wcfm_half_ele_title wcfm_title simple',
                'value' => $regprice
            );
            $output['scd_sale_price'] = array(
                'label' => __('Sale Price', 'scd_wcfm_marketplace') . '(' . get_woocommerce_currency_symbol($user_curr) . ')',
                'type' => 'text',
                'class' => 'wcfm-text wcfm_ele wcfm_half_ele wcfm_half_ele_right simple',
                'label_class' => 'wcfm_ele wcfm_half_ele_title wcfm_title simple',
                'value' => $saleprice,
                'desc_class' => 'wcfm_ele simple external non-variable-subscription non-auction non-redq_rental non-accommodation-booking sales_schedule');
            //correct prices due to scd ovewrite
            $regprice = get_post_meta($product_id, '_regular_price', TRUE);
            $saleprice = get_post_meta($product_id, '_sale_price', TRUE);
            $output["regular_price"]['value'] = $regprice;
            $output["sale_price"]['value'] = $saleprice;
        } elseif ($user_curr_opt == 'only-default-currency') {
            $output["regular_price"]['label'] = __('Price(' . get_woocommerce_currency_symbol($user_curr) . ')');
            $output["sale_price"]['label'] = __('Sale price(' . get_woocommerce_currency_symbol($user_curr) . ')');
			if(!empty($regprice) && ($regprice == $saleprice)){
            $output["regular_price"]['value'] = $regprice;	
            //$output["sale_price"]['value'] = $saleprice;
			}elseif(!empty($regprice) && !empty($saleprice)){
			 $output["regular_price"]['value'] = $regprice;	
			 $output["sale_price"]['value'] = $saleprice;
			}				
        } else { //base currency only
        }
    }
    ?>
    <script type="text/javascript">
        jQuery(document).ready(function () {
            var currencyVal = "<?php echo $currencyVal; ?>";
            var tab = {};
            jQuery(".scd_wcfm_select").prop("selectedIndex", -1);
            if (currencyVal.trim() !== '')
                tab = currencyVal.split(',');
            jQuery(".scd_wcfm_select").attr('multiple', 'TRUE');
            for (var i = 0; i < tab.length; i++) {
                jQuery('.scd_wcfm_select option[value="' + tab[i] + '"]').attr('selected', true);
                var myregselect = '<option id="reg_' + tab[i] + '" value=' + tab[i] + ' >' .  __('Regular price ', 'scd_wcfm_marketplace' ) . '(' + tab[i] + ')</option>';

                var mysalselect = '<option id="sale_' + tab[i] + '" value=' + tab[i] + ' >' .  __('Sale price ', 'scd_wcfm_marketplace' ) . '(' + tab[i] + ')</option>';

                jQuery('#scd_regularCurrency').append(myregselect);
                jQuery('#scd_saleCurrency').append(mysalselect);
            }
            //alert(tab.length);

            //  if(! tab.length > 0) jQuery(".scd_wcfm_select").prop("selectedIndex", -1);
            jQuery(".scd_wcfm_select").select2();

        });
    </script>
    <?php
    return $output;
}

//edit wcfm variation form
add_filter('wcfm_product_manage_fields_variations', 'scd_wcfm_variable_product', 10, 7);

function scd_wcfm_variable_product($output, $variations, $variation_shipping_option_array, $variation_tax_classes_options, $products_array, $product_id, $product_type) {

    $user_curr_opt = scd_get_user_currency_option();
    $user_curr = scd_get_user_currency();

    if ($user_curr == FALSE || $user_curr_opt == 'selected-currencies') {
        //return $output;     
    } else {
        $curr_symbol = get_woocommerce_currency_symbol($user_curr);
        //list($regprice, $saleprice) = scd_get_product_custom_price_for_currency($product_id, $user_curr);

        if ($user_curr_opt == 'base-and-default-currency') {
            $output["scd_regular_price"] = array(
                'label' => __('Regular Price', 'scd_wcfm_marketplace') . '(' . get_woocommerce_currency_symbol($user_curr) . ')',
                'type' => 'number',
                'class' => 'wcfm-text wcfm_ele wcfm_non_negative_input wcfm_half_ele variable',
                'label_class' => 'wcfm_title wcfm_ele wcfm_half_ele_title variable',
                'attributes' => array('min' => '0.1', 'step' => '0.1'));
            $output["scd_sale_price"] = array(
                'label' => __('Sale Price', 'scd_wcfm_marketplace') . '(' . get_woocommerce_currency_symbol($user_curr) . ')',
                'type' => 'number',
                'class' => 'wcfm-text wcfm_ele wcfm_non_negative_input wcfm_half_ele variable variable-subscription',
                'label_class' => 'wcfm_title wcfm_ele    wcfm_half_ele_title variable variable-subscription',
                'attributes' => array('min' => '0.1', 'step' => '0.1'));
        } elseif ($user_curr_opt == 'only-default-currency') {
            $output["regular_price"]["label"] = __('Price(', 'scd_wcfm_marketplace') . get_woocommerce_currency_symbol($user_curr) . ')';
            $output["sale_price"]["label"] = __('Sale price(', 'scd_wcfm_marketplace') . get_woocommerce_currency_symbol($user_curr) . ')';
        } else { //base currency only
        }
    }
    return $output;
}

//change prices in case the default user currency is different to the base currency
add_filter('wcfm_variation_edit_data', 'scd_wcfm_edit_variation', 100, 3);

function scd_wcfm_edit_variation($variations, $variation_id, $variation_id_key) {
    $user_curr = scd_get_user_currency();
    $curr_opt = scd_get_user_currency_option();
    if ($user_curr !== FALSE && $curr_opt == 'only-default-currency') {
        list($regprice, $saleprice) = scd_get_product_custom_price_for_currency($variation_id, $user_curr);
		if(!empty($regprice) && ($regprice == $saleprice)){
        $variations[$variation_id_key]['regular_price'] = $regprice ?? scd_function_convert_subtotal($variations[$variation_id_key]['regular_price'], $user_curr, get_option('woocommerce_currency'), 1, TRUE);
        //$variations[$variation_id_key]['sale_price'] = $saleprice ?? scd_function_convert_subtotal($variations[$variation_id_key]['sale_price'], $user_curr, get_option('woocommerce_currency'), 1, TRUE);
		}elseif(!empty($regprice) && !empty($saleprice)){
		$variations[$variation_id_key]['regular_price'] = $regprice ?? scd_function_convert_subtotal($variations[$variation_id_key]['regular_price'], $user_curr, get_option('woocommerce_currency'), 1, TRUE);
        $variations[$variation_id_key]['sale_price'] = $saleprice ?? scd_function_convert_subtotal($variations[$variation_id_key]['sale_price'], $user_curr, get_option('woocommerce_currency'), 1, TRUE);
		}	
	} elseif ($user_curr !== FALSE && $curr_opt == 'base-and-default-currency') {
        list($regprice, $saleprice) = scd_get_product_custom_price_for_currency($variation_id, $user_curr);
        $variations[$variation_id_key]['scd_regular_price'] = $regprice ?? scd_function_convert_subtotal($variations[$variation_id_key]['scd_regular_price'], $user_curr, get_option('woocommerce_currency'), 1, TRUE);
        $variations[$variation_id_key]['scd_sale_price'] = $saleprice ?? scd_function_convert_subtotal($variations[$variation_id_key]['scd_sale_price'], $user_curr, get_option('woocommerce_currency'), 1, TRUE);
    }
    return $variations;
}

//after_wcfm_product_variation_meta_save
add_filter('wcfm_product_variation_data_factory', 'scd_wcfm_variation_save', 10, 5);

function scd_wcfm_variation_save($wcfm_variation_data, $new_product_id, $variation_id, $variations, $wcfm_products_manage_form_data) {

    $scd_userRole = scd_get_user_role();
    $scd_userID = get_current_user_id();
    $scd_currencyVal = '';
    $priceField = '';
    $user_curr = scd_get_user_currency();
    $curr_opt = scd_get_user_currency_option();

    if ($user_curr !== FALSE && isset($variations['scd_sale_price'])) {
        $scd_currencyVal = $user_curr;
        $priceField = 'regular_' . $scd_currencyVal . '_' . $variations['scd_regular_price'] . '-sale_' . $scd_currencyVal . '_' . $variations['scd_sale_price'];
    }

    update_post_meta($variation_id, '_meta_sale_price', $variations['sale_price']);
    update_post_meta($variation_id, '_meta_regular_price', $variations['regular_price']);
    update_post_meta($variation_id, '_meta_currency', $user_curr);
            
            
    if ($user_curr !== FALSE && $curr_opt == 'only-default-currency') {
        $scd_currencyVal = $user_curr;
        $priceField = 'regular_' . $scd_currencyVal . '_' . $variations['regular_price'] . '-sale_' . $scd_currencyVal . '_' . $variations['sale_price'];
        //save the equivalent price entered by user in base currency
        $converted = scd_function_convert_subtotal($variations['regular_price'], get_option('wocommerce_currency'), $scd_currencyVal, 2, TRUE);
        //update_post_meta($variation_id, '_regular_price', $converted);
        $wcfm_variation_data['regular_price'] = $converted;
        if ($variations['sale_price'] !== '') {
            $converted = scd_function_convert_subtotal($variations['sale_price'], get_option('wocommerce_currency'), $scd_currencyVal, 2, TRUE);
            //update_post_meta($variation_id, '_sale_price', $converted);
            $wcfm_variation_data['sale_price'] = $converted;
            //update_post_meta($variation_id, '_price', $converted);
            $wcfm_variation_data['_price'] = $converted;
        } else {
            //update_post_meta($variation_id, '_price', $converted);
            $wcfm_variation_data['_price'] = $converted;
        }
    }
    if ($priceField !== '')
        update_post_meta($variation_id, 'scd_other_options', array(
            "currencyUserID" => $scd_userID,
            "currencyUserRole" => $scd_userRole,
            "currencyVal" => $scd_currencyVal,
            "currencyPrice" => $priceField
        ));

    return $wcfm_variation_data;
}

add_filter('wcfmmp_settings_fields_shipping_by_country', 'scd_wcfmmp_settings_fields_shipping_by_country', 11);

function scd_wcfmmp_settings_fields_shipping_by_country($form) {

    $user_id = get_current_user_id();
    $user_curr = scd_get_user_currency();
    $cur_opt = scd_get_user_currency_option();
    if ($user_curr != FALSE && $cur_opt == 'only-default-currency') {
        $curr_symb = get_woocommerce_currency_symbol($user_curr);

        $wcfmmp_shipping_by_country = get_user_meta($user_id, 'scd_wcfmmp_shipping_by_country', true);
        if (!$wcfmmp_shipping_by_country) {
            $wcfmmp_shipping_by_country = get_option('_wcfmmp_shipping_by_country', array());
        }
        $form['wcfmmp_shipping_type_price']['label'] = $form['wcfmmp_shipping_type_price']['label'] . '(' . $curr_symb . ')';
        $form['wcfmmp_shipping_type_price']['value'] = isset($wcfmmp_shipping_by_country['_wcfmmp_shipping_type_price']) ? $wcfmmp_shipping_by_country['_wcfmmp_shipping_type_price'] : '';

        $form['wcfmmp_additional_product']['label'] = $form['wcfmmp_additional_product']['label'] . '(' . $curr_symb . ')';
        $form['wcfmmp_additional_product']['value'] = isset($wcfmmp_shipping_by_country['_wcfmmp_additional_product']) ? $wcfmmp_shipping_by_country['_wcfmmp_additional_product'] : '';

        $form['wcfmmp_additional_qty']['label'] = $form['wcfmmp_additional_qty']['label'] . '(' . $curr_symb . ')';
        $form['wcfmmp_additional_qty']['value'] = isset($wcfmmp_shipping_by_country['_wcfmmp_additional_qty']) ? $wcfmmp_shipping_by_country['_wcfmmp_additional_qty'] : '';

        $form['wcfmmp_byc_free_shipping_amount']['label'] = $form['wcfmmp_byc_free_shipping_amount']['label'] . '(' . $curr_symb . ')';
        $form['wcfmmp_byc_free_shipping_amount']['value'] = isset($wcfmmp_shipping_by_country['_free_shipping_amount']) ? $wcfmmp_shipping_by_country['_free_shipping_amount'] : '';
    }
    return $form;
}

add_filter('wcfmmp_settings_fields_shipping_rates_by_country', 'scd_wcfmmp_settings_fields_shipping_rates_by_country');

function scd_wcfmmp_settings_fields_shipping_rates_by_country($form) {

    $user_id = get_current_user_id();
    $user_curr = scd_get_user_currency();
    $cur_opt = scd_get_user_currency_option();
    if ($user_curr != FALSE && $cur_opt == 'only-default-currency') {
        $curr_symb = get_woocommerce_currency_symbol($user_curr);
        $wcfmmp_shipping = get_user_meta($user_id, 'scd_wcfmmp_shipping', true);

        $wcfmmp_shipping_by_country = get_user_meta($user_id, 'scd_wcfmmp_shipping_by_country', true);
        if (!$wcfmmp_shipping_by_country) {
            $wcfmmp_shipping_by_country = get_option('_wcfmmp_shipping_by_country', array());
        }

        $wcfmmp_country_rates = get_user_meta($user_id, 'scd_wcfmmp_country_rates', true);
        if (!$wcfmmp_country_rates) {
            $wcfmmp_country_rates = get_option('_wcfmmp_country_rates', array());
        }

        $wcfmmp_state_rates = get_user_meta($user_id, 'scd_wcfmmp_state_rates', true);
        if (!$wcfmmp_state_rates) {
            $wcfmmp_state_rates = get_option('_wcfmmp_state_rates', array());
        }

        $wcfmmp_shipping_rates = array();
        $state_options = array();
        if ($wcfmmp_country_rates) {
            foreach ($wcfmmp_country_rates as $country => $country_rate) {
                $wcfmmp_shipping_state_rates = array();
                $state_options = array();
                if (!empty($wcfmmp_state_rates) && isset($wcfmmp_state_rates[$country])) {
                    foreach ($wcfmmp_state_rates[$country] as $state => $state_rate) {
                        $state_options[$state] = $state;
                        $wcfmmp_shipping_state_rates[] = array(
                            'wcfmmp_state_to' => $state,
                            'wcfmmp_state_to_price' => $state_rate,
                            'option_values' => $state_options
                        );
                    }
                }
                $wcfmmp_shipping_rates[] = array(
                    'wcfmmp_country_to' => $country,
                    'wcfmmp_country_to_price' => $country_rate,
                    'wcfmmp_shipping_state_rates' => $wcfmmp_shipping_state_rates
                );
            }
        }

        $form['wcfmmp_shipping_rates']['options']['wcfmmp_country_to_price']['label'] = __('Cost', 'scd_wcfm_marketplace') . '(' . $curr_symb . ')';
        $form['wcfmmp_shipping_rates']['options']['wcfmmp_shipping_state_rates']['options']['wcfmmp_state_to_price']['label'] = __('Cost', 'scd_wcfm_marketplace') . '(' . $curr_symb . ')';

        $form['wcfmmp_shipping_rates']['value'] = $wcfmmp_shipping_rates;
    }
    return $form;
}

add_filter('wcfmmp_settings_fields_shipping_rates_by_weight', 'scd_wcfmmp_settings_fields_shipping_rates_by_weight', 20);

function scd_wcfmmp_settings_fields_shipping_rates_by_weight($form) {
    $user_id = get_current_user_id();
    $user_curr = scd_get_user_currency();
    $cur_opt = scd_get_user_currency_option();
    if ($user_curr != FALSE && $cur_opt == 'only-default-currency') {
        $curr_symb = get_woocommerce_currency_symbol($user_curr);
        $scd_wcfmmp_shipping_data = get_user_meta($user_id, 'scd_wcfmmp_shipping_by_weight', true);

        $weight_unit = strtolower(get_option('woocommerce_weight_unit'));
        $wcfmmp_country_weight_rates = $scd_wcfmmp_shipping_data['_wcfmmp_country_weight_rates'];
        if (!$wcfmmp_country_weight_rates) {
            $wcfmmp_country_weight_rates = get_option('_wcfmmp_country_weight_rates', array());
        }

        $wcfmmp_country_weight_mode = $scd_wcfmmp_shipping_data['_wcfmmp_country_weight_mode'];
        if (!$wcfmmp_country_weight_mode) {
            $wcfmmp_country_weight_mode = get_option('_wcfmmp_country_weight_mode', array());
        }

        $wcfmmp_country_weight_unit_cost = $scd_wcfmmp_shipping_data['_wcfmmp_country_weight_unit_cost'];
        if (!$wcfmmp_country_weight_unit_cost) {
            $wcfmmp_country_weight_unit_cost = get_option('_wcfmmp_country_weight_unit_cost', array());
        }

        $wcfmmp_country_weight_default_costs = $scd_wcfmmp_shipping_data['_wcfmmp_country_weight_default_costs'];
        if (!$wcfmmp_country_weight_default_costs) {
            $wcfmmp_country_weight_default_costs = get_option('_wcfmmp_country_weight_default_costs', array());
        }

        $wcfmmp_country_weight_shipping_value = array();
        if ($wcfmmp_country_weight_rates) {
            foreach ($wcfmmp_country_weight_rates as $country => $each_rate) {
                $wcfmmp_country_weight_shipping_value[] = array(
                    'wcfmmp_weightwise_country_to' => $country,
                    'wcfmmp_weightwise_country_mode' => isset($wcfmmp_country_weight_mode[$country]) ? $wcfmmp_country_weight_mode[$country] : 'by_rule',
                    'wcfmmp_weightwise_country_per_unit_cost' => isset($wcfmmp_country_weight_unit_cost[$country]) ? $wcfmmp_country_weight_unit_cost[$country] : 0,
                    'wcfmmp_weightwise_country_default_cost' => isset($wcfmmp_country_weight_default_costs[$country]) ? $wcfmmp_country_weight_default_costs[$country] : 0,
                    'wcfmmp_shipping_country_weight_settings' => $each_rate
                );
            }
        }

        $form['wcfmmp_shipping_rates_by_weight']['value'] = $wcfmmp_country_weight_shipping_value;

        $form['wcfmmp_shipping_rates_by_weight']['options']['wcfmmp_weightwise_country_per_unit_cost']['label'] = __('Per unit cost', 'scd_wcfm_marketplace') . ' (' . $curr_symb . '/' . get_option('woocommerce_weight_unit', 'kg') . ')';

        $form['wcfmmp_shipping_rates_by_weight']['options']['wcfmmp_weightwise_country_default_cost']['label'] = __('Country default cost if no matching rule', 'scd_wcfm_marketplace') . ' (' . $curr_symb . ')';
//        
        $form['wcfmmp_shipping_rates_by_weight']['options']['wcfmmp_shipping_country_weight_settings']['options']['wcfmmp_weight_price']['label'] = __('Cost', 'scd_wcfm_marketplace') . ' (' . $curr_symb . ')';
    }
    return $form;
}

add_action('wp_ajax_scd_wcfmmp_update_shipping_method', 'scd_wcfmmp_update_shipping_method');

function scd_wcfmmp_update_shipping_method() {
    global $wpdb;
    $vendor_curr = scd_get_user_currency();
    $curr_opt = scd_get_user_currency_option();

    if ($vendor_curr != false && $curr_opt == 'only-default-currency') {
        $args = wp_unslash($_POST['args']);
        if (empty($args['settings']['title'])) {
            wp_send_json_error(__('Shipping title must be required', 'scd_wcfm_marketplace'));
        }
        //a:8:{s:5:"title";s:3:"DHL";s:11:"description";s:41:"Lets you charge a rate for shipping (DHL)";s:4:"cost";s:3:"700";
        //s:10:"tax_status";s:4:"none";s:13:"class_cost_49";s:3:"800";s:13:"class_cost_48";s:3:"500";s:24:"class_cost_no_class_cost";s:4:"1000";s:16:"calculation_type";s:5:"order";}
        foreach ($args['settings'] as $key => $value) {
            if (strpos($key, 'class_cost_') === 0) {
                $cost = scd_function_convert_subtotal($value, get_option('wocommerce_currency'), $vendor_curr, 2, TRUE);
                $args['settings'][$key] = $cost;
            }
        }

        //cost
        if (isset($args['settings']['cost'])) {
            $cost = scd_function_convert_subtotal($args['settings']['cost'], get_option('wocommerce_currency'), $vendor_curr, 2, TRUE);
            $args['settings']['cost'] = $cost;
        }
        //min_amount
        if (isset($args['settings']['min_amount'])) {
            $cost = scd_function_convert_subtotal($args['settings']['min_amount'], get_option('wocommerce_currency'), $vendor_curr, 2, TRUE);
            $args['settings']['min_amount'] = $cost;
        }
        $data = array(
            'method_id' => $args['method_id'],
            'zone_id' => $args['zone_id'],
            'vendor_id' => empty($args['user_id']) ? apply_filters('wcfm_current_vendor_id', get_current_user_id()) : $args['user_id'],
            'settings' => maybe_serialize($args['settings'])
        );

        $table_name = $wpdb->prefix . "wcfm_marketplace_shipping_zone_methods";

        $updated = $wpdb->update($table_name, $data, array('instance_id' => $args['instance_id']), array('%s', '%d', '%d', '%s'));
        //save orifinal data 
        $args = wp_unslash($_POST['args']);
        $data = array(
            'method_id' => $args['method_id'],
            'zone_id' => $args['zone_id'],
            'vendor_id' => empty($args['user_id']) ? apply_filters('wcfm_current_vendor_id', get_current_user_id()) : $args['user_id'],
            'settings' => $args['settings']
        );

        update_user_meta(get_current_user_id(), 'instand_id_' . $args['instance_id'], $data);
    }
    wp_send_json_success();
}

add_action('wp_ajax_scd_wcfmmp_get_shipping_settings', 'scd_wcfmmp_get_shipping_settings');

function scd_wcfmmp_get_shipping_settings() {
    $vendor_curr = scd_get_user_currency();
    $curr_opt = scd_get_user_currency_option();

    if ($vendor_curr != false && $curr_opt == 'only-default-currency') {
        if (isset($_POST['instance_id'])) {
            $instance_id = $_POST['instance_id'];
            $data = get_user_meta(get_current_user_id(), 'instand_id_' . $instance_id, true);
            if ($data == NULL || $data == '')
                $data = array();
        }
        $data['currency'] = ' (' . get_woocommerce_currency_symbol($vendor_curr) . ')';
        wp_send_json_success($data);
    }
    wp_send_json_error();
}

/*
 * wcfm function: wcfmmp_vendor_shipping_settings_update
 */
add_action('wcfm_vendor_settings_update', 'scd_wcfm_shipping_prices', 11, 2);

//add_action('wcfm_vendor_shipping_settings_update', 'scd_wcfm_shipping_prices',11,2);
function scd_wcfm_shipping_prices($user_id, $wcfm_settings_form) {
    $vendor_currency = scd_get_user_currency();
    $user_curr = scd_get_user_currency();
    $cur_opt = scd_get_user_currency_option();
    if ($vendor_currency != FALSE && $cur_opt == 'only-default-currency') {

// By Country settings save
        if (!empty($wcfm_settings_form['wcfmmp_shipping']['_wcfmmp_user_shipping_type']) && $wcfm_settings_form['wcfmmp_shipping']['_wcfmmp_user_shipping_type'] == 'by_country') {
            //print_r($wcfm_settings_form);
            if (isset($wcfm_settings_form['wcfmmp_shipping_by_country'])) {
                //scd conversion
                $converted = scd_function_convert_subtotal($wcfm_settings_form['wcfmmp_shipping_by_country']['_wcfmmp_shipping_type_price'], get_option('wocommerce_currency'), $vendor_currency, 2, TRUE);
                $sh_by_ctry['_wcfmmp_shipping_type_price'] = $converted;

                $converted = scd_function_convert_subtotal($wcfm_settings_form['wcfmmp_shipping_by_country']['_wcfmmp_additional_product'], get_option('wocommerce_currency'), $vendor_currency, 2, TRUE);
                $sh_by_ctry['_wcfmmp_additional_product'] = $converted;

                $converted = scd_function_convert_subtotal($wcfm_settings_form['wcfmmp_shipping_by_country']['_wcfmmp_additional_qty'], get_option('wocommerce_currency'), $vendor_currency, 2, TRUE);
                $sh_by_ctry['_wcfmmp_additional_qty'] = $converted;

                $converted = scd_function_convert_subtotal($wcfm_settings_form['wcfmmp_shipping_by_country']['_free_shipping_amount'], get_option('wocommerce_currency'), $vendor_currency, 2, TRUE);
                $sh_by_ctry['_free_shipping_amount'] = $converted;

                $sh_by_ctry['_wcfmmp_form_location'] = $wcfm_settings_form['wcfmmp_shipping_by_country']['_wcfmmp_form_location'];
                //save converted values
                update_user_meta($user_id, '_wcfmmp_shipping_by_country', $sh_by_ctry);
                //original values
                update_user_meta($user_id, 'scd_wcfmmp_shipping_by_country', $wcfm_settings_form['wcfmmp_shipping_by_country']);
            }
//      
            // Shipping Rates
            if (isset($wcfm_settings_form['wcfmmp_shipping_rates']) && !empty($wcfm_settings_form['wcfmmp_shipping_rates'])) {
                $wcfmmp_country_rates = array();
                $wcfmmp_state_rates = array();
                $scd_country_rates = array();
                $scd_state_rates = array();
                foreach ($wcfm_settings_form['wcfmmp_shipping_rates'] as $wcfmmp_shipping_rates) {
                    if ($wcfmmp_shipping_rates['wcfmmp_country_to']) {
                        if ($wcfmmp_shipping_rates['wcfmmp_shipping_state_rates'] && !empty($wcfmmp_shipping_rates['wcfmmp_shipping_state_rates'])) {
                            foreach ($wcfmmp_shipping_rates['wcfmmp_shipping_state_rates'] as $wcfmmp_shipping_state_rates) {
                                if ($wcfmmp_shipping_state_rates['wcfmmp_state_to']) {
                                    $converted = scd_function_convert_subtotal($wcfmmp_shipping_state_rates['wcfmmp_state_to_price'], get_option('wocommerce_currency'), $vendor_currency, 2, TRUE);
                                    $wcfmmp_state_rates[$wcfmmp_shipping_rates['wcfmmp_country_to']][$wcfmmp_shipping_state_rates['wcfmmp_state_to']] = $converted;

                                    $scd_state_rates[$wcfmmp_shipping_rates['wcfmmp_country_to']][$wcfmmp_shipping_state_rates['wcfmmp_state_to']] = $wcfmmp_shipping_state_rates['wcfmmp_state_to_price'];
                                }
                            }
                        }
                        $converted = scd_function_convert_subtotal($wcfmmp_shipping_rates['wcfmmp_country_to_price'], get_option('wocommerce_currency'), $vendor_currency, 2, TRUE);
                        $wcfmmp_country_rates[$wcfmmp_shipping_rates['wcfmmp_country_to']] = $converted;
                        $scd_country_rates[$wcfmmp_shipping_rates['wcfmmp_country_to']] = $wcfmmp_shipping_rates['wcfmmp_country_to_price'];
                    }
                }
                //save converted values
                update_user_meta($user_id, '_wcfmmp_country_rates', $wcfmmp_country_rates);
                update_user_meta($user_id, '_wcfmmp_state_rates', $wcfmmp_state_rates);
                //save original values
                update_user_meta($user_id, 'scd_wcfmmp_country_rates', $scd_country_rates);
                update_user_meta($user_id, 'scd_wcfmmp_state_rates', $scd_state_rates);
            }
        }
        // By weight settings save
        if (!empty($wcfm_settings_form['wcfmmp_shipping']['_wcfmmp_user_shipping_type']) && $wcfm_settings_form['wcfmmp_shipping']['_wcfmmp_user_shipping_type'] == 'by_weight') {
            //print_r($wcfm_settings_form);
            $wcfmmp_country_weight_rates = array();
            $wcfmmp_country_weight_mode = array();
            $wcfmmp_country_weight_unit_cost = array();
            $wcfmmp_country_weight_default_costs = array();

            //scd variable
            $scd_country_weight_rates = array();
            $scd_country_weight_unit_cost = array();
            $scd_country_weight_default_costs = array();
            $scd_wcfmmp = array();
            foreach ($wcfm_settings_form['wcfmmp_shipping_rates_by_weight'] as $wcfmmp_shipping_rates_by_weight) {
                if ($wcfmmp_shipping_rates_by_weight['wcfmmp_weightwise_country_to']) {

                    if ($wcfmmp_shipping_rates_by_weight['wcfmmp_shipping_country_weight_settings'] && !empty($wcfmmp_shipping_rates_by_weight['wcfmmp_shipping_country_weight_settings'])) {

                        $scd_country_weight_rates[$wcfmmp_shipping_rates_by_weight['wcfmmp_weightwise_country_to']] = $wcfmmp_shipping_rates_by_weight['wcfmmp_shipping_country_weight_settings'];

                        $wcfmm_weight_sh = array();
                        foreach ($wcfmmp_shipping_rates_by_weight['wcfmmp_shipping_country_weight_settings'] as $wcfmm_weight_rule) {
                            $converted = scd_function_convert_subtotal($wcfmm_weight_rule['wcfmmp_weight_price'], get_option('wocommerce_currency'), $vendor_currency, 2, TRUE);
                            $wcfmm_weight_rule['wcfmmp_weight_price'] = $converted;
                            $wcfmm_weight_sh[] = $wcfmm_weight_rule;
                        }
                        $wcfmmp_country_weight_rates[$wcfmmp_shipping_rates_by_weight['wcfmmp_weightwise_country_to']] = $wcfmm_weight_sh;

                        $wcfmmp_country_weight_mode[$wcfmmp_shipping_rates_by_weight['wcfmmp_weightwise_country_to']] = ( $wcfmmp_shipping_rates_by_weight['wcfmmp_weightwise_country_mode'] || $wcfmmp_shipping_rates_by_weight['wcfmmp_weightwise_country_mode'] != "" ) ? $wcfmmp_shipping_rates_by_weight['wcfmmp_weightwise_country_mode'] : 'by_rule';

                        $scd_country_weight_unit_cost[$wcfmmp_shipping_rates_by_weight['wcfmmp_weightwise_country_to']] = ( $wcfmmp_shipping_rates_by_weight['wcfmmp_weightwise_country_per_unit_cost'] || $wcfmmp_shipping_rates_by_weight['wcfmmp_weightwise_country_per_unit_cost'] != "" ) ? $wcfmmp_shipping_rates_by_weight['wcfmmp_weightwise_country_per_unit_cost'] : 0;
                        $converted = scd_function_convert_subtotal($scd_country_weight_unit_cost[$wcfmmp_shipping_rates_by_weight['wcfmmp_weightwise_country_to']], get_option('wocommerce_currency'), $vendor_currency, 2, TRUE);
                        $wcfmmp_country_weight_unit_cost[$wcfmmp_shipping_rates_by_weight['wcfmmp_weightwise_country_to']] = $converted;

                        $scd_country_weight_default_costs[$wcfmmp_shipping_rates_by_weight['wcfmmp_weightwise_country_to']] = ( $wcfmmp_shipping_rates_by_weight['wcfmmp_weightwise_country_default_cost'] || $wcfmmp_shipping_rates_by_weight['wcfmmp_weightwise_country_default_cost'] != "" ) ? $wcfmmp_shipping_rates_by_weight['wcfmmp_weightwise_country_default_cost'] : 0;
                        $converted = scd_function_convert_subtotal($scd_country_weight_default_costs[$wcfmmp_shipping_rates_by_weight['wcfmmp_weightwise_country_to']], get_option('wocommerce_currency'), $vendor_currency, 2, TRUE);
                        $wcfmmp_country_weight_default_costs[$wcfmmp_shipping_rates_by_weight['wcfmmp_weightwise_country_to']] = $converted;
                    }
                }
            }

            $scd_wcfmmp = array(
                '_wcfmmp_country_weight_rates' => $scd_country_weight_rates,
                '_wcfmmp_country_weight_mode' => $wcfmmp_country_weight_mode,
                '_wcfmmp_country_weight_unit_cost' => $scd_country_weight_unit_cost,
                '_wcfmmp_country_weight_default_costs' => $scd_country_weight_default_costs
            );
            update_user_meta($user_id, 'scd_wcfmmp_shipping_by_weight', $scd_wcfmmp);

            update_user_meta($user_id, '_wcfmmp_country_weight_rates', $wcfmmp_country_weight_rates);
            update_user_meta($user_id, '_wcfmmp_country_weight_mode', $wcfmmp_country_weight_mode);
            update_user_meta($user_id, '_wcfmmp_country_weight_unit_cost', $wcfmmp_country_weight_unit_cost);
            update_user_meta($user_id, '_wcfmmp_country_weight_default_costs', $wcfmmp_country_weight_default_costs);
            //print_r($wcfmmp_country_rates);
        }
    }
}

add_action('after_wcfm_products_manage_meta_save', 'scd_wcfm_save_meta', 10, 2);

function scd_wcfm_save_meta($post_id, $data) {
    scd_save_product_prices($post_id, $data);
}

add_filter('coupon_manager_fields_general', 'scd_coupon_manager_fields_general', 99, 2);

function scd_coupon_manager_fields_general($fields, $coupon_id) {
    $user_curr = scd_get_user_currency();
    if ($user_curr !== false) {
        if ($fields['coupon_type']['value'] != 'percent') {
            $coupon = get_post_meta($coupon_id, 'scd_coupon_amount', true);
            if (isset($coupon['coupon_amount']))
                $fields['coupon_amount']['value'] = $coupon['coupon_amount'];
        }
        $fields['coupon_amount']['label'] = $fields['coupon_amount']['label'] . '(%/' . get_woocommerce_currency_symbol($user_curr) . ')';
    }

    return $fields;
}

add_filter('coupon_manager_fields_restriction', 'scd_coupon_manager_fields_restriction', 10, 2);

function scd_coupon_manager_fields_restriction($fields, $coupon_id) {
    $user_curr = scd_get_user_currency();
    if ($user_curr !== false) {
        $fields['minimum_amount']['label'] = $fields['minimum_amount']['label'] . ' (' . get_woocommerce_currency_symbol($user_curr) . ')';
        $fields['maximum_amount']['label'] = $fields['maximum_amount']['label'] . ' (' . get_woocommerce_currency_symbol($user_curr) . ')';

        $coupon = get_post_meta($coupon_id, 'scd_coupon_amount', true);
        if (isset($coupon['minimum_amount'])) {
            $fields['maximum_amount']['value'] = $coupon['maximum_amount'];
            $fields['minimum_amount']['value'] = $coupon['minimum_amount'];
        }
    }
    return $fields;
}

add_filter('wcfm_coupon_data_factory', 'scd_wcfm_coupon_data_factory', 99, 3);

function scd_wcfm_coupon_data_factory($data_coupon, $coupon_id, $coupon_manager_form_data) {
    $user_curr = scd_get_user_currency();
    update_option('__coupon', $data_coupon);
    update_option('__coupons', $coupon_manager_form_data);
    if ($user_curr !== false) {
        $min_amount = '';
        if (isset($data_coupon['minimum_amount']) && !empty($data_coupon['minimum_amount'])) {
            $min_amount = $data_coupon['minimum_amount'];
            $data_coupon['minimum_amount'] = scd_function_convert_subtotal($data_coupon['minimum_amount'], get_option('wocommerce_currency'), $user_curr, 8, TRUE);
        }

        $max_amount = '';
        if (isset($data_coupon['maximum_amount']) && !empty($data_coupon['maximum_amount'])) {
            $max_amount = $data_coupon['maximum_amount'];
            $data_coupon['maximum_amount'] = scd_function_convert_subtotal($data_coupon['maximum_amount'], get_option('wocommerce_currency'), $user_curr, 8, TRUE);
        }
        $original_data = array();
        $original_data['minimum_amount'] = $min_amount;
        $original_data['maximum_amount'] = $max_amount;

        if ($data_coupon['discount_type'] != 'percent') {
            $original_data['coupon_amount'] = $data_coupon['amount'];
            $data_coupon['amount'] = scd_function_convert_subtotal($data_coupon['amount'], get_option('wocommerce_currency'), $user_curr, 8, TRUE);
        }
        update_post_meta($coupon_id, 'scd_coupon_amount', $original_data);
    }
    return $data_coupon;
}

//$result = apply_filters('woocommerce_reports_get_order_report_data', $wpdb->$query_type($query), $data);
add_filter('woocommerce_reports_get_order_report_data', 'scd_woocommerce_reports_get_order_report_data', 10, 2);

function scd_woocommerce_reports_get_order_report_data($result, $data) {

    $basecurrency = get_option('woocommerce_currency');
    if (isset($result[0]->total_sales)) {
        if (isset($result[0]->currency) && $result[0]->currency !== $basecurrency) {
//            if($rate=get_post_meta($result[0]->order_id,'_scd_order_rate',true)){
//              $result[0]->total_sales= round($result[0]->total_sales*$rate,2);
//              $result[0]->total_shipping= round($result[0]->total_shipping*$rate,2);
//              $result[0]->total_tax= round($result[0]->total_tax*$rate,2);
//              $result[0]->total_shipping_tax= round($result[0]->total_shipping_tax*$rate,2);
//             //var_dump($rate,$result[0]);
//              }else{
            $result[0]->total_sales = scd_function_convert_subtotal($result[0]->total_sales, $basecurrency, $result[0]->currency, 8, true);
            $result[0]->total_shipping = scd_function_convert_subtotal($result[0]->total_shipping, $basecurrency, $result[0]->currency, 8, true);
            $result[0]->total_tax = scd_function_convert_subtotal($result[0]->total_tax, $basecurrency, $result[0]->currency, 8, true);
            $result[0]->total_shipping_tax = scd_function_convert_subtotal($result[0]->total_shipping_tax, $basecurrency, $result[0]->currency, 8, true);
            //} 
        }
    }

    return $result;
}

// add_action('woocommerce_checkout_update_order_meta', 'scd_woocommerce_checkout_update_order_meta');
//function scd_woocommerce_checkout_update_order_meta($order_id){
//    
//    $order= wc_get_order($order_id);
//    //_scd_order_rate 
//        $scd_rate = floatval(scd_get_conversion_rate(get_option('woocommerce_currency'), $order->get_currency()));
//        $scd_rate=1/$scd_rate;
//        update_post_meta($order_id,'_scd_order_rate', $scd_rate);
//    }
//    
//calculate the weeckly sales 
function scd_wcfm_get_gross_sales_by_vendors() {
    $vendor_id = '';
    $interval = '7day';
    $is_paid = false;
    $order_id = 0;
    $filter_date_form = '';
    $filter_date_to = '';
    global $WCFM, $wpdb, $WCMp, $WCFMmp;

    if ($vendor_id)
        $vendor_id = absint($vendor_id);

    $gross_sales = 0;

    $marketplece = wcfm_is_marketplace();
    if ($marketplece == 'wcfmmarketplace') {
        $sql = "SELECT ID, order_id, item_id, item_total, item_sub_total, refunded_amount, shipping, tax, shipping_tax_amount FROM {$wpdb->prefix}wcfm_marketplace_orders AS commission";
        $sql .= " WHERE 1=1";
        if ($vendor_id)
            $sql .= " AND `vendor_id` = {$vendor_id}";
        if ($order_id) {
            $sql .= " AND `order_id` = {$order_id}";
            //$sql .= " AND `is_refunded` != 1";
        } else {
            $sql .= apply_filters('wcfm_order_status_condition', '', 'commission');
            $sql .= " AND `is_trashed` = 0";
            if ($is_paid) {
                $sql .= " AND commission.withdraw_status = 'completed'";
                $sql = wcfm_query_time_range_filter($sql, 'commission_paid_date', $interval, $filter_date_form, $filter_date_to);
            } else {
                $sql = wcfm_query_time_range_filter($sql, 'created', $interval, $filter_date_form, $filter_date_to);
            }
        }

        $basecurrency = get_option('woocommerce_currency');
        $gross_sales_whole_week = $wpdb->get_results($sql);
        $gross_commission_ids = array();
        $gross_total_refund_amount = 0;
        if (!empty($gross_sales_whole_week)) {
            foreach ($gross_sales_whole_week as $net_sale_whole_week) {
                $gross_commission_ids[] = $net_sale_whole_week->ID;
                $gross_total_refund_amount += (float) sanitize_text_field($net_sale_whole_week->refunded_amount);
            }

            if (!empty($gross_commission_ids)) {
                try {
//                        if (apply_filters('wcfmmmp_gross_sales_respect_setting', true)) {
//                            $gross_sales = (float) $WCFMmp->wcfmmp_commission->wcfmmp_get_commission_meta_sum($gross_commission_ids, 'gross_total');
//                        } else {
//                            $gross_sales = (float) $WCFMmp->wcfmmp_commission->wcfmmp_get_commission_meta_sum($gross_commission_ids, 'gross_sales_total');
//                        }
                    //scd code
                    if (apply_filters('wcfmmmp_gross_sales_respect_setting', true)) {
                        $commission_meta = $wpdb->get_results("SELECT * "
                                . "FROM `{$wpdb->prefix}wcfm_marketplace_orders_meta` 
		 WHERE `order_commission_id` in ('" . implode("','", $gross_commission_ids) . "') ");

                        $commissions = array();
                        foreach ($commission_meta as $com) {
                            $commissions[$com->order_commission_id][$com->key] = $com->value;
                        }

                        foreach ($commissions as $value) {
                            if ($value['currency'] == $basecurrency) {
                                $gross_sales = $gross_sales + $value['gross_total'];
                            } else {
                                $gross_sales = $gross_sales + scd_function_convert_subtotal($value['gross_total'], $basecurrency, $value['currency'], 2, true);
                            }
                        }
                    } else {
                        $commission_meta = $wpdb->get_results("SELECT * "
                                . "FROM `{$wpdb->prefix}wcfm_marketplace_orders_meta` 
		 WHERE `order_commission_id` in ('" . implode("','", $gross_commission_ids) . "') ");

                        $commissions = array();
                        foreach ($commission_meta as $com) {
                            $commissions[$com->order_commission_id][$com->key] = $com->value;
                        }

                        foreach ($commissions as $value) {
                            if ($value['currency'] == $basecurrency) {
                                $gross_sales = $gross_sales + $value['gross_sales_total'];
                            } else {
                                $gross_sales = $gross_sales + scd_function_convert_subtotal($value['gross_sales_total'], $basecurrency, $value['currency'], 2, true);
                            }
                        }
                    }
                    //end scd

                    $gross_sales -= (float) $gross_total_refund_amount;
                } catch (Exception $e) {
                    //continue;
                }
            }
        }
    }

    if (!$gross_sales)
        $gross_sales = 0;

    return $gross_sales;
}


add_filter('woocommerce_reports_get_order_report_data_args', 'scd_woocommerce_reports_get_order_report_data_args', 10, 1);

function scd_woocommerce_reports_get_order_report_data_args($args) {
    //_order_currency
    $args['data']['_order_currency'] = array(
        'type' => 'meta',
        'function' => '',
        'name' => 'currency',
    );
    $args['data']['ID'] = array(
        'type' => 'post_data',
        'function' => '',
        'name' => 'order_id',
    );
    return $args;
}
/*
add_action('woocommerce_product_filters','testedd',10,1);
function testedd($data){
    ?>
    <script type="text/javascript">
        console.log(<?php echo json_encode($data);?>);
    </script>
    <?php
}*/

//add_action('after_wcfm_products', 'scd_after_wcfm_products');

function scd_after_wcfm_products() {
    $user_currency = scd_get_user_currency();
    $user_currency_opt = scd_get_user_currency_option();
	$int = wc_get_price_decimals();
    $rate = scd_get_conversion_rate(get_option('woocommerce_currency'), $user_currency);
    if ($user_currency === false || $user_currency_opt !== 'only-default-currency') {
        return;
    }
    ?>
    <p  class="scd-wait-content">
        <img  src="<?php echo trailingslashit(plugins_url("", __FILE__)) . "images/load_proess.gif" ?>">
    </p>
    <style type="text/css">
        td .amount, td ins, td del{display: none;}
        .scd-wait-content{display: none;position: fixed;z-index: 9999;left: 37%;bottom: 5%; background-color: white; }
    </style>
    <script type="text/javascript">
    jQuery('document').ready(function(){
        var myVar = setInterval(myTimer, 1000);
        var rate = <?php echo $rate;  ?>;
		var decimal = <?php echo $int;  ?>;
        var currency = '<?php echo scd_get_user_currency(); ?>';
        var symbol = scd_get_currency_symbol(currency).symbol;
        console.log(symbol);
        function myTimer(){
        if(jQuery('td .amount')!==undefined){
          var elements = jQuery('td .amount');
              console.log(elements);
                var len = elements.length;
                if(len > 0)
                {
                    for (var i=0; i<len; i++) {
                        jQuery(elements[i]).html(symbol + (rate * (jQuery(elements[i]).text()).toString().replace(/[^0-9\.-]+/g,"")).toFixed(decimal));
                    }      
                    clearInterval(myVar);  
                    jQuery('td .amount').css('display','block');
                    jQuery('td ins').css('display','block');
                    jQuery('td del').css('display','block');
                }
            }   
        }
    });
    /*function scd_get_prices() {
            setTimeout(function () {
                //var ids = jQuery('td input.wcfm-checkbox');
                var ids = document.getElementsByClassName('wcfm-action-icon wcfm_product_delete');

                var product_ids = [];
                var nbp = ids.length;
                for (var i = 0; i < nbp; i++) {
                    //product_ids[i] = jQuery(ids[i]).val();
                    product_ids[i] = ids[i].getAttribute('data-proid');
                }
                jQuery.post(ajaxurl,
                        {
                            action: 'scd_wcfm_get_product_prices',
                            product_ids: product_ids.toString()
                        },
                function (response) {
                    if (response.success) {
                        for (var ind = 0; ind < response.data.length; ind++) {
                            jQuery('td del:eq(' + ind + ')').html(response.data[ind].regular_price);
                            jQuery('td ins:eq(' + ind + ')').html(response.data[ind].sale_price);
                            jQuery('td .amount:eq(' + ind + ')').html(response.data[ind].regular_price);
                            jQuery('td .amount:eq(' + ind + ')').html(response.data[ind].regular_price);
                        }
                    }
                    jQuery('td ins, td del').css('display', 'block');
                }
                );
            }, 1000);
            jQuery('.scd-wait-content').hide();
        }

        /*function waitUntil() {
            jQuery('.scd-wait-content').show()
            if (jQuery('td input.wcfm-checkbox').size() == 0) {
                window.requestAnimationFrame(waitUntil);
                //setTimeout(waitUntil, 500); // give everything some time to render
            } else {
                
            }
        }*

        var  interval=setInterval(function(){
            if(document.getElementsByClassName("paginate_button current").length > 0){
                scd_get_prices();
                clearInterval(interval);
            }
        },1000);*/
    </script>
    <?php
}

/*add_action('wp_ajax_scd_wcfm_get_product_prices', 'scd_wcfm_get_product_prices');

function scd_wcfm_get_product_prices() {
$prices = array();
    include_once(WP_PLUGIN_DIR.'/scd-smart-currency-detector/scd_renders.php');
    if (isset($_POST['product_ids'])) {
        $ids = explode(',', $_POST['product_ids']);
        $user_currency = scd_get_user_currency();
        $user_currency_opt = scd_get_user_currency_option();
        $basecurrency = get_option('woocommerce_currency');
        $args['decimals'] = scd_options_get_decimal_precision();
        $args['price_format'] = get_woocommerce_price_format();
        $args['currency'] = $user_currency;
        $args['price_format'] = scd_change_currency_display_format($args['price_format'], $user_currency);

        if ($user_currency !== false && $user_currency_opt == 'only-default-currency') {
            foreach ($ids as $id) {

                $product = wc_get_product($id);
                
                if (scd_is_custom_price_defined_for_currency($id, $user_currency)) {

                    list($regprice, $saleprice) = scd_get_product_custom_price_for_currency($id, $user_currency);
                } else {
                    $regprice = scd_function_convert_subtotal($product->regular_price, $basecurrency, $user_currency);
                    $saleprice = scd_function_convert_subtotal($product->sale_price, $basecurrency, $user_currency);
                }

                $prices[] = array(
                    'regular_price' => scd_format_converted_price_to_html($regprice, $args),
                    'sale_price' => scd_format_converted_price_to_html($saleprice, $args)
                );
            }
           wp_send_json_success($prices);
        }
    }
    wp_send_json_error();
}*/

//apply_filters('wcfm_vendor_pending_withdrawal', $pending_withdrawal, $vendor_id);
add_filter('wcfm_vendor_pending_withdrawal','scd_wcfm_vendor_pending_withdrawal',99,2);
function scd_wcfm_vendor_pending_withdrawal($pending_earning, $vendor_id) {
    global $WCFM, $wpdb, $_POST, $WCFMmp;
    $interval = '7day';
    $filter_date_form = '';
    $filter_date_to = '';

    if (!$vendor_id)
        return 0;

    if (!function_exists('wcfmmp_get_store_url')) {
        $earned = $this->wcfm_get_commission_by_vendor($vendor_id, $interval, false, 0, $filter_date_form, $filter_date_to);
        $withdrawal = $this->wcfm_get_withdrawal_by_vendor($vendor_id, $interval, $filter_date_form, $filter_date_to);
        $pending_withdrawal = (float) $earned - (float) $withdrawal;
    } else {
        $pending_withdrawal = 0;

        $withdrawal_thresold = $WCFMmp->wcfmmp_withdraw->get_withdrawal_thresold($vendor_id);

        $sql = 'SELECT order_id, total_commission FROM ' . $wpdb->prefix . 'wcfm_marketplace_orders AS commission';
        $sql .= ' WHERE 1=1';
        $sql .= " AND `vendor_id` = {$vendor_id}";
        $sql .= apply_filters('wcfm_order_status_condition', '', 'commission');
        $sql .= " AND commission.withdraw_status IN ('pending', 'cancelled')";
        $sql .= " AND commission.refund_status != 'requested'";
        $sql .= ' AND `is_withdrawable` = 1 AND `is_auto_withdrawal` = 0 AND `is_refunded` = 0 AND `is_trashed` = 0';
        if ($withdrawal_thresold)
            $sql .= " AND commission.created <= NOW() - INTERVAL {$withdrawal_thresold} DAY";
        if ($filter_date_form && $filter_date_to) {
            $sql .= " AND DATE( commission.created ) BETWEEN '" . $filter_date_form . "' AND '" . $filter_date_to . "'";
        }

        $wcfm_withdrawals_array = $wpdb->get_results($sql);
        $basecurrency = get_option('woocommerce_currency');
        if (!empty($wcfm_withdrawals_array)) {
            foreach ($wcfm_withdrawals_array as $wcfm_withdrawals_single) {
                $order_id = $wcfm_withdrawals_single->order_id;
                $order = wc_get_order($order_id);
                if (!is_a($order, 'WC_Order'))
                    continue;
                if ($order->get_currency() == $basecurrency) {
                    $pending_withdrawal += (float) $wcfm_withdrawals_single->total_commission;
                } else {
                    $pending_withdrawal += scd_function_convert_subtotal($wcfm_withdrawals_single->total_commission, $basecurrency, $order->get_currency(), 8, true);
                }
            }
        }
    }

    return $pending_withdrawal;
}

add_action('after_wcfm_withdrawal', 'scd_after_wcfm_withdrawal',99);
function scd_after_wcfm_withdrawal() {
    
    ?>
    <style type="text/css">
/*        td ins, td del{display: none;}
        .scd-wait-content{display: none;position: fixed;z-index: 9999;left: 37%;bottom: 5%; background-color: white; }*/
    </style>
    <script type="text/javascript">
        function scd_get_withdraw() {
           //return;
            setTimeout(function () {
                var ids = jQuery('td input.select_withdrawal');
                var widr_ids = [];
                var nbp = ids.length;
                
                for (var i = 0; i < nbp; i++) {
                    widr_ids[i] = jQuery(ids[i]).val();
                }
               
                jQuery.post(ajaxurl,
                        {
                            action: 'scd_wcfm_get_withdrwaw_list',
                            commission_ids: widr_ids.toString()
                        },
                function (response) {
                       
                    if (response.success) {
                   
                        for (var ind = 0; ind < response.data.length; ind++) {
                            jQuery('#wcfm-withdrawal tbody tr:eq('+ind+') td:eq(2)').html(response.data[ind].earning);
                            jQuery('#wcfm-withdrawal tbody tr:eq('+ind+') td:eq(3)').html(response.data[ind].charges);
                            jQuery('#wcfm-withdrawal tbody tr:eq('+ind+')').find('td:eq(4)').html(response.data[ind].payment);
                        
                    }
                    }
                    //jQuery('td ins, td del').css('display', 'block');
                }
                );
            }, 500);
            jQuery('.scd-wait-content').hide();
        }

        function scd_withdraw_waitUntil() {
            jQuery('.scd-wait-content').show()
            if (jQuery('td input.select_withdrawal').size() == 0) {
                window.requestAnimationFrame(scd_withdraw_waitUntil);
                //setTimeout(scd_withdraw_waitUntil, 500); // give everything some time to render
            } else {
               scd_get_withdraw();
            }
        }

        jQuery(document).ready(function () {
            scd_withdraw_waitUntil();
        });
    </script>
    
    <?php
}

add_action('wp_ajax_scd_wcfm_get_withdrwaw_list','scd_wcfm_get_withdrwaw_list');
function scd_wcfm_get_withdrwaw_list() {
        global $wpdb;
        
     //$vendor_id=  get_current_user_id();
        if(isset($_POST['commission_ids'])){
            $vendor_currency=  scd_get_user_currency();
            $ids=$_POST['commission_ids'];
    $sql = 'SELECT * FROM ' . $wpdb->prefix . 'wcfm_marketplace_orders AS commission';
    $sql .= ' WHERE 1=1';
    //$sql .= " AND `vendor_id` = {$vendor_id}";
    $sql .= " AND commission.ID IN (".$ids.")";
    $sql .= " ORDER BY commission.created ASC";
     $wcfm_withdrawals_array = $wpdb->get_results($sql);
  $scd_wcfm_withdrawals_arr = array();
  if($vendor_currency==false){
   $vendor_currency=  get_option('woocommerce_currency');
  }
  
    if (!empty($wcfm_withdrawals_array)) {
         $args['decimals'] = scd_options_get_decimal_precision();
        $args['price_format'] = get_woocommerce_price_format();
        $args['currency'] = $vendor_currency;
        $args['price_format'] = scd_change_currency_display_format($args['price_format'], $vendor_currency);
        $basecurrency=  get_option('woocommerce_currency');
        foreach ($wcfm_withdrawals_array as $wcfm_withdrawals_single) {
            $order_id = $wcfm_withdrawals_single->order_id;

            $order = wc_get_order($order_id);
            if (!is_a($order, 'WC_Order'))
                continue;
 
       if($order->get_currency()==$basecurrency){
            // My Earnings
             $earning=  scd_function_convert_subtotal($wcfm_withdrawals_single->total_commission,$basecurrency,$vendor_currency,8);
             $earning_html=scd_format_converted_price_to_html($earning, $args);
            // Charges
             $charges=  scd_function_convert_subtotal($wcfm_withdrawals_single->withdraw_charges,$basecurrency,$vendor_currency,8);
             $charges_html=scd_format_converted_price_to_html($charges, $args);
           
            // Payment
            $payment=$earning - $charges;
             $payment_html=scd_format_converted_price_to_html($payment, $args);
       }else{
    // My Earnings
            $earning=  scd_function_convert_subtotal($wcfm_withdrawals_single->total_commission,$basecurrency,$order->get_currency(),8,true);
             $earning=  scd_function_convert_subtotal($earning,$basecurrency,$vendor_currency,8);
             $earning_html=scd_format_converted_price_to_html($earning, $args);
            // Charges
             $charges=  scd_function_convert_subtotal($wcfm_withdrawals_single->withdraw_charges,$basecurrency,$order->get_currency(),8,true);
             $charges=  scd_function_convert_subtotal($charges,$basecurrency,$vendor_currency,8);
             $charges_html=scd_format_converted_price_to_html($charges, $args);
           
            // Payment
            $payment=$earning - $charges;
             $payment_html=scd_format_converted_price_to_html($payment, $args);
       }
        $scd_wcfm_withdrawals_arr[]=array(
                    'earning'=>$earning_html,
                    'charges'=>$charges_html,
                    'payment'=>$payment_html
                     );
        }
    }
    wp_send_json_success($scd_wcfm_withdrawals_arr);
}else{
    wp_send_json_error();
}
}


add_action('after_wcfm_payments','scd_after_wcfm_payments',99);
function scd_after_wcfm_payments() {
    ?>
    <script type="text/javascript">
        function scd_wcfm_get_payment() {
             setTimeout(function () {
                var ids = jQuery('#wcfm-payments tbody>tr>:nth-child(2) > a.wcfm_dashboard_item_title');
                var widr_ids = [];
                var nbp = ids.length;
                for (var i = 0; i < nbp; i++) {
                    widr_ids[i] = jQuery(ids[i]).attr('href').substring(jQuery(ids[i]).attr('href').lastIndexOf('/') + 1);
                }
                
                jQuery.post(ajaxurl,
                        {
                        action: 'scd_wcfm_get_withdrwaw_requests_list',
                        withdraw_requests_ids: widr_ids.toString()
                        },
                function (response) {
                       
                    if (response.success) {
                       
                        for (var ind = 0; ind < response.data.length; ind++) {
                            jQuery('#wcfm-payments tbody tr:eq('+ind+') td:eq(3)').html(response.data[ind].amount);
                            jQuery('#wcfm-payments tbody tr:eq('+ind+') td:eq(4)').html(response.data[ind].charges);
                            jQuery('#wcfm-payments tbody tr:eq('+ind+') td:eq(5) .amount').replaceWith(response.data[ind].payment);
                        }
                    }
                    //jQuery('td ins, td del').css('display', 'block');
                }
                );
            }, 500);
            jQuery('.scd-wait-content').hide();
        }

        function scd_wcfm_payment_waitUntil(){
            if (jQuery('td .wcfm_dashboard_item_title').size() == 0) {
                window.requestAnimationFrame(scd_wcfm_payment_waitUntil);
                
            } else { 
               scd_wcfm_get_payment();
            }
        }

        jQuery(document).ready(function () { 
            scd_wcfm_payment_waitUntil();
        });
        
    </script>
    <?php  
}

//add_action('wp_ajax_scd_wcfm_get_payment_list','scd_wcfm_get_payment_list',99);
//function scd_wcfm_get_payment_list() {
//   
//}

//admin side
add_action('after_wcfm_withdrawal_requests','scd_after_wcfm_withdrawal_requests',99);
function scd_after_wcfm_withdrawal_requests() {
    ?>
    <script type="text/javascript">
                function scd_get_withdraw_request() {
            
            setTimeout(function () {
                var ids = jQuery('td input.select_withdrawal_requests');
                var widr_ids = [];
                var nbp = ids.length;
                
                for (var i = 0; i < nbp; i++) {
                    widr_ids[i] = jQuery(ids[i]).val();
                }
                
                jQuery.post(ajaxurl,
                        {
                            action: 'scd_wcfm_get_withdrwaw_requests_list',
                            withdraw_requests_ids: widr_ids.toString()
                        },
                function (response) {
                       
                    if (response.success) {
                           
                        for (var ind = 0; ind < response.data.length; ind++) {
                            jQuery('#wcfm-withdrawal-requests tbody tr:eq('+ind+') td:eq(4)').html(response.data[ind].amount);
                            jQuery('#wcfm-withdrawal-requests tbody tr:eq('+ind+') td:eq(5)').html(response.data[ind].charges);
                            jQuery('#wcfm-withdrawal-requests tbody tr:eq('+ind+') td:eq(6) .amount').html(response.data[ind].payment);
                        }
                    }
                    //jQuery('td ins, td del').css('display', 'block');
                }
                );
            }, 500);
            jQuery('.scd-wait-content').hide();
        }

        function scd_withdraw_request_waitUntil() {
            jQuery('.scd-wait-content').show()
            if (jQuery('td input.select_withdrawal_requests').size() == 0) {
                window.requestAnimationFrame(scd_withdraw_request_waitUntil);
                //setTimeout(scd_withdraw_waitUntil, 500); // give everything some time to render
            } else {
               scd_get_withdraw_request();
            }
        }

        jQuery(document).ready(function () { 
            scd_withdraw_request_waitUntil();
           
        });
        
    </script>
    <?php
}

add_action('wp_ajax_scd_wcfm_get_withdrwaw_requests_list','scd_wcfm_get_withdrwaw_requests_list');
function scd_wcfm_get_withdrwaw_requests_list() {
        global $wpdb;
        
     //$vendor_id=  get_current_user_id();
        if(isset($_POST['withdraw_requests_ids'])){
            $admin_currency=  scd_get_user_currency();
            $ids=$_POST['withdraw_requests_ids'];
    $sql = 'SELECT * FROM ' . $wpdb->prefix . 'wcfm_marketplace_withdraw_request AS commission';
    $sql .= ' WHERE 1=1';
    //$sql .= " AND `vendor_id` = {$vendor_id}";
    $sql .= " AND commission.ID IN (".$ids.")";
    $sql .= " ORDER BY commission.created DESC";
     $wcfm_withdrawals_array = $wpdb->get_results($sql);
  $scd_wcfm_withdrawals_arr = array();
  if($admin_currency==false){
     $admin_currency=  get_option('woocommerce_currency');
  }
  
    if(!empty($wcfm_withdrawals_array)) {
         $args['decimals'] = scd_options_get_decimal_precision();
        $args['price_format'] = get_woocommerce_price_format();
        $args['currency'] = $admin_currency;
        $args['price_format'] = scd_change_currency_display_format($args['price_format'], $admin_currency);
        $basecurrency=  get_option('woocommerce_currency');
        foreach ($wcfm_withdrawals_array as $wcfm_withdrawals_single) {
            
            list($amount,$charges,$payment)=scd_wcfm_get_total_commissions($wcfm_withdrawals_single->commission_ids);
       if($admin_currency==$basecurrency){
            // Amount
             $amount_html=scd_format_converted_price_to_html($amount, $args);
            // Charges
             $charges_html=scd_format_converted_price_to_html($charges, $args);
           
            // Payment
             $payment_html=scd_format_converted_price_to_html($payment, $args);
       }else{
    // Amount
             $amount=  scd_function_convert_subtotal($amount,$basecurrency,$admin_currency,8);
             $amount_html=scd_format_converted_price_to_html($amount, $args);
            // Charges
             $charges=  scd_function_convert_subtotal($charges,$basecurrency,$admin_currency,8);
             $charges_html=scd_format_converted_price_to_html($charges, $args);
            // Payment
            $payment=$amount - $charges;
             $payment_html=scd_format_converted_price_to_html($payment, $args);
       }
        $scd_wcfm_withdrawals_arr[]=array(
                    'amount'=>$amount_html,
                    'charges'=>$charges_html,
                    'payment'=>$payment_html
                     );
        }
    }
    wp_send_json_success($scd_wcfm_withdrawals_arr);
}else{
    wp_send_json_error();
}
}
/*
 * get total comision in basecurrency
 * @param: commsion_ids
 */
function scd_wcfm_get_total_commissions($commisssion_ids) {
        global $wpdb;
            //$vendor_currency=  scd_get_user_currency();
        $earning=0;$charges=0;$payment=0;
        $pay=0;
            $ids=$commisssion_ids;
    $sql = 'SELECT * FROM ' . $wpdb->prefix . 'wcfm_marketplace_orders AS commission';
    $sql .= ' WHERE 1=1';
    //$sql .= " AND `vendor_id` = {$vendor_id}";
    $sql .= " AND commission.ID IN (".$ids.")";
    $sql .= " ORDER BY commission.created ASC";
     $wcfm_withdrawals_array = $wpdb->get_results($sql);
  $scd_wcfm_withdrawals_arr = array();
  
    if (!empty($wcfm_withdrawals_array)) {
        $basecurrency=  get_option('woocommerce_currency');
        foreach ($wcfm_withdrawals_array as $wcfm_withdrawals_single) {
            $order_id = $wcfm_withdrawals_single->order_id;

            $order = wc_get_order($order_id);
            if (!is_a($order, 'WC_Order'))
                continue;
 
       if($order->get_currency()==$basecurrency){
            // My Earnings
             $earning =$earning +$wcfm_withdrawals_single->total_commission;
            // Charges
             $charges =$charges + $wcfm_withdrawals_single->withdraw_charges;
           // Payment
            $pay =$earning - $charges;
            $payment=$payment+$pay;
       }else{
       //    Earnings
            $earning =$earning +  scd_function_convert_subtotal($wcfm_withdrawals_single->total_commission,$basecurrency,$order->get_currency(),8,true);
            // Charges
             $charges =$charges + scd_function_convert_subtotal($wcfm_withdrawals_single->withdraw_charges,$basecurrency,$order->get_currency(),8,true);
           
            }
        
        }
        $payment =$earning  - $charges;
    }
 return array($earning,$charges,$payment);
}


add_filter('woocommerce_get_price_html', 'scds_change_woocommerce_get_price_html', 999, 2);	




function scds_change_woocommerce_get_price_html($price_html, $product) {
	
	$target_currency = scd_get_user_currency();
	
	
	$base_currency = get_option( 'woocommerce_currency');
	
	$decimals      = wc_get_price_decimals();
	
	$args= array(
		'ex_tax_label'       => false,
		'currency'           => '',
		'decimal_separator'  => wc_get_price_decimal_separator(),
		'thousand_separator' => wc_get_price_thousand_separator(),
		'decimals'           => wc_get_price_decimals(),
		'price_format'       => get_woocommerce_price_format(),
	);
	

	
	if($product->get_children()){

	    $price = array();
		
		$childrens=$product->get_children();
		
        foreach ($childrens as $variation_id) {
			
			
			$currencyVal = "";
				
			if (get_post_meta($variation_id, 'scd_other_options')) {
				
			$datas = get_post_meta($variation_id, 'scd_other_options');
			
			$currencyVal = $datas[0]['currencyVal'];
				
			}
			
			if($currencyVal != ""){
				
			list($regprice, $saleprice, $currency) = scd_get_price_for_currency_test($variation_id,$currencyVal);
				
				if($currency == $target_currency){
					
				    if($saleprice>0){
					    array_push($price,$saleprice);
					}else{
						array_push($price,$regprice);
					}
					
					asort($price);
		
                    $price1 = $price[array_key_first($price)];
		
                    $price2 = $price[array_key_last($price)];
					
					$decimals = scd_options_get_decimal_precision();
					$args['currency'] = $target_currency;
					$args['decimals'] = $decimals;
					$args['price_format'] = scd_change_currency_display_format ($args['price_format'], $target_currency);
					
					if($price1 == $price2){
							$price1 = scd_format_converted_price_to_html($price1, $args);
							$price2 = scd_format_converted_price_to_html($price2, $args);
							$price_html = sprintf( __( '%1$s', 'woocommerce' ),  $price1 );
					}else{
							$price1 = scd_format_converted_price_to_html($price1, $args);
							$price2 = scd_format_converted_price_to_html($price2, $args);
							$price_html = sprintf( __( '%1$s - %2$s', 'woocommerce' ),  $price1,  $price2 );
					}		
				
				}else{
					
					if($target_currency  == $base_currency){
					
						if(!empty($saleprice))
						$saleprice = scd_function_convert_subtotal($saleprice, $base_currency, $currencyVal, $decimals,$inverse=TRUE );  
						if(!empty($regprice))
						$regprice = scd_function_convert_subtotal($regprice, $base_currency, $currencyVal, $decimals,$inverse=TRUE ); 

						if($saleprice>0){
							array_push($price,$saleprice);
						}else{
							array_push($price,$regprice);
						}
						
						asort($price);
			
						$price1 = $price[array_key_first($price)];
			
						$price2 = $price[array_key_last($price)];
						
						$decimals = scd_options_get_decimal_precision();
						$args['currency'] = $target_currency;
						$args['decimals'] = $decimals;
						$args['price_format'] = scd_change_currency_display_format ($args['price_format'], $target_currency);
						
						if($price1 == $price2 || empty($price2)){
								$price1 = scd_format_converted_price_to_html($price1, $args);
								$price_html = sprintf( __( '%1$s', 'woocommerce' ),  $price1 );
						}else{
								$price1 = scd_format_converted_price_to_html($price1, $args);
								$price2 = scd_format_converted_price_to_html($price2, $args);
								$price_html = sprintf( __( '%1$s - %2$s', 'woocommerce' ),  $price1,  $price2 );
						}					
				
						
					}else{
											
						
					$reg_price = get_post_meta($variation_id, '_regular_price', TRUE);
					
					$sale_price = get_post_meta($variation_id, '_sale_price', TRUE);
					
					
						if(!empty($sale_price))
						$sale_price = scd_function_convert_subtotal($sale_price, $base_currency, $target_currency, $decimals );   
						if(!empty($reg_price))
						$reg_price = scd_function_convert_subtotal($reg_price, $base_currency, $target_currency, $decimals );  

						if($sale_price>0){
							array_push($price,$sale_price);
						}else{
							array_push($price,$reg_price);
						}
						
						asort($price);
			
						$price1 = $price[array_key_first($price)];
			
						$price2 = $price[array_key_last($price)];
						
						$decimals = scd_options_get_decimal_precision();
						$args['currency'] = $target_currency;
						$args['decimals'] = $decimals;
						$args['price_format'] = scd_change_currency_display_format ($args['price_format'], $target_currency);
						
						if($price1 == $price2 || empty($price2)){
								$price1 = scd_format_converted_price_to_html($price1, $args);
								$price_html = sprintf( __( '%1$s', 'woocommerce' ),  $price1 );
						}else{
								$price1 = scd_format_converted_price_to_html($price1, $args);
								$price2 = scd_format_converted_price_to_html($price2, $args);
								$price_html = sprintf( __( '%1$s - %2$s', 'woocommerce' ),  $price1,  $price2 );
						}					
				
					}
					
					
					
				}
				
			}else{
				
				if($target_currency != $base_currency){
				
					$reg_price = get_post_meta($variation_id, '_regular_price', TRUE);
					
					$sale_price = get_post_meta($variation_id, '_sale_price', TRUE);
					
					
						if(!empty($sale_price))
						$sale_price = scd_function_convert_subtotal($sale_price, $base_currency, $target_currency, $decimals );   
						if(!empty($reg_price))
						$reg_price = scd_function_convert_subtotal($reg_price, $base_currency, $target_currency, $decimals );  

						if($sale_price>0){
							array_push($price,$sale_price);
						}else{
							array_push($price,$reg_price);
						}
						
						asort($price);
			
						$price1 = $price[array_key_first($price)];
			
						$price2 = $price[array_key_last($price)];
						
						$decimals = scd_options_get_decimal_precision();
						$args['currency'] = $target_currency;
						$args['decimals'] = $decimals;
						$args['price_format'] = scd_change_currency_display_format ($args['price_format'], $target_currency);
						
						if($price1 == $price2 || empty($price2)){
								$price1 = scd_format_converted_price_to_html($price1, $args);
								$price_html = sprintf( __( '%1$s', 'woocommerce' ),  $price1 );
						}else{
								$price1 = scd_format_converted_price_to_html($price1, $args);
								$price2 = scd_format_converted_price_to_html($price2, $args);
								$price_html = sprintf( __( '%1$s - %2$s', 'woocommerce' ),  $price1,  $price2 );
						}	
					
				
			    }
				
				
			} 
			
		} 

	}else if($product && (!$product->get_children())){

        
        if($product->get_variation_id()){
			
		
			
			$variation_id = $product->get_variation_id() ;
			
			$currencyVal = "";
				
			if (get_post_meta($variation_id, 'scd_other_options')) {
				
			$datas = get_post_meta($variation_id, 'scd_other_options');
			
			$currencyVal = $datas[0]['currencyVal'];
				
			}
			
			
			
			if($currencyVal != ""){
			
				
			list($regprice, $saleprice, $currency) = scd_get_price_for_currency_test($variation_id,$currencyVal);

				
				if($currency == $target_currency){
					
				
					$decimals = scd_options_get_decimal_precision();
					$args['currency'] = $target_currency;
					$args['decimals'] = $decimals;
					$args['price_format'] = scd_change_currency_display_format ($args['price_format'], $target_currency);
					
					if($regprice == $saleprice || empty($saleprice)){
							$regprice = scd_format_converted_price_to_html($regprice, $args);
							$price_html = '<ins>' . $regprice . '</ins>';  
					}else{
							$regprice = scd_format_converted_price_to_html($regprice, $args);
							$saleprice = scd_format_converted_price_to_html($saleprice, $args);
							$price_html = '<del>' . $regprice . '</del> <ins>' . $saleprice . '</ins>';
					}		
				
				}else{
					
					if($target_currency  == $base_currency){
					
						if(!empty($saleprice))
						$saleprice = scd_function_convert_subtotal($saleprice, $base_currency, $currencyVal, $decimals,$inverse=TRUE );  
						if(!empty($regprice))
						$regprice = scd_function_convert_subtotal($regprice, $base_currency, $currencyVal, $decimals,$inverse=TRUE ); 
						
						$decimals = scd_options_get_decimal_precision();
						$args['currency'] = $target_currency;
						$args['decimals'] = $decimals;
						$args['price_format'] = scd_change_currency_display_format ($args['price_format'], $target_currency);
						
						if($regprice == $saleprice || empty($saleprice)){
								$regprice = scd_format_converted_price_to_html($regprice, $args);
								$price_html = '<ins>' . $regprice . '</ins>';  
						}else{
								$regprice = scd_format_converted_price_to_html($regprice, $args);
								$saleprice = scd_format_converted_price_to_html($saleprice, $args);
								$price_html = '<del>' . $regprice . '</del> <ins>' . $saleprice . '</ins>';
						}						
				
						
					}else{
						
						$reg_price = get_post_meta($variation_id, '_regular_price', TRUE);
					
					    $sale_price = get_post_meta($variation_id, '_sale_price', TRUE);
						
						
						if(!empty($reg_price ))
						$reg_price  = scd_function_convert_subtotal($reg_price , $base_currency, $target_currency, $decimals  );  
						if(!empty($sale_price))
						$sale_price = scd_function_convert_subtotal($sale_price, $base_currency, $target_currency, $decimals  ); 

						$decimals = scd_options_get_decimal_precision();
						$args['currency'] = $target_currency;
						$args['decimals'] = $decimals;
						$args['price_format'] = scd_change_currency_display_format ($args['price_format'], $target_currency);
						
						if($reg_price == $sale_price || empty($sale_price)){
								$reg_price = scd_format_converted_price_to_html($reg_price, $args);
								$price_html = '<ins>' . $reg_price . '</ins>';  
						}else{
								$reg_price = scd_format_converted_price_to_html($reg_price, $args);
								$sale_price = scd_format_converted_price_to_html($sale_price, $args);
								$price_html = '<del>' . $reg_price . '</del> <ins>' . $sale_price . '</ins>';
						}					
				
					}
					
					
					
				}
				
			}else{
				
				if($target_currency != $base_currency){
					
							
				
					$reg_price = get_post_meta($variation_id, '_regular_price', TRUE);
					
					$sale_price = get_post_meta($variation_id, '_sale_price', TRUE);
					
					
						if(!empty($sale_price))
						$sale_price = scd_function_convert_subtotal($sale_price, $base_currency, $target_currency, $decimals );   
						if(!empty($reg_price))
						$reg_price = scd_function_convert_subtotal($reg_price, $base_currency, $target_currency, $decimals );  

						$decimals = scd_options_get_decimal_precision();
						$args['currency'] = $target_currency;
						$args['decimals'] = $decimals;
						$args['price_format'] = scd_change_currency_display_format ($args['price_format'], $target_currency);
						
						if($reg_price == $sale_price || empty($sale_price)){
								$reg_price = scd_format_converted_price_to_html($reg_price, $args);
								$price_html = '<ins>' . $reg_price . '</ins>';  
						}else{
							
							
								$reg_price = scd_format_converted_price_to_html($reg_price, $args);
																
								$sale_price = scd_format_converted_price_to_html($sale_price, $args);
																
								$price_html = '<del>' . $reg_price . '</del> <ins>' . $sale_price . '</ins>';
								
						}		
					
				
			    }
				
				
			} 
			
			

               	
        }else{
			
			
			$product_id = $product->get_id();
 			
			$currencyVal = "";
				
			if (get_post_meta($product_id, 'scd_other_options')) {
				
			$datas = get_post_meta($product_id, 'scd_other_options');
			
			$currencyVal = $datas[0]['currencyVal'];
				
			}
			
			if($currencyVal != ""){
				
			list($regprice, $saleprice, $currency) = scd_get_price_for_currency_test($product_id,$currencyVal);
				
				if($currency == $target_currency){
					
				
					$decimals = scd_options_get_decimal_precision();
					$args['currency'] = $target_currency;
					$args['decimals'] = $decimals;
					$args['price_format'] = scd_change_currency_display_format ($args['price_format'], $target_currency);
					
					if($regprice == $saleprice || empty($saleprice)){
							$regprice = scd_format_converted_price_to_html($regprice, $args);
							$price_html = '<ins>' . $regprice . '</ins>';  
					}else{
							$regprice = scd_format_converted_price_to_html($regprice, $args);
							$saleprice = scd_format_converted_price_to_html($saleprice, $args);
							$price_html = '<del>' . $regprice . '</del> <ins>' . $saleprice . '</ins>';
					}		
				
				}else{
					
					if($target_currency  == $base_currency){
						
					
						if(!empty($saleprice))
						$saleprice = scd_function_convert_subtotal($saleprice, $base_currency, $currencyVal, $decimals,$inverse=TRUE );  
						if(!empty($regprice))
						$regprice = scd_function_convert_subtotal($regprice, $base_currency, $currencyVal, $decimals,$inverse=TRUE ); 
						
						$decimals = scd_options_get_decimal_precision();
						$args['currency'] = $target_currency;
						$args['decimals'] = $decimals;
						$args['price_format'] = scd_change_currency_display_format ($args['price_format'], $target_currency);
						
						if($regprice == $saleprice || empty($saleprice)){
								$regprice = scd_format_converted_price_to_html($regprice, $args);
								$price_html = '<ins>' . $regprice . '</ins>';  
						}else{
								$regprice = scd_format_converted_price_to_html($regprice, $args);
								$saleprice = scd_format_converted_price_to_html($saleprice, $args);
								$price_html = '<del>' . $regprice . '</del> <ins>' . $saleprice . '</ins>';
						}						
				
						
					}else{
											
						
					$reg_price = get_post_meta($product_id, '_regular_price', TRUE);
					
					$sale_price = get_post_meta($product_id, '_sale_price', TRUE);
					
					
						if(!empty($sale_price))
						$sale_price = scd_function_convert_subtotal($sale_price, $base_currency, $target_currency, $decimals );   
						if(!empty($reg_price))
						$reg_price = scd_function_convert_subtotal($reg_price, $base_currency, $target_currency, $decimals );  

						$decimals = scd_options_get_decimal_precision();
						$args['currency'] = $target_currency;
						$args['decimals'] = $decimals;
						$args['price_format'] = scd_change_currency_display_format ($args['price_format'], $target_currency);
						
						if($reg_price == $sale_price || empty($sale_price)){
								$reg_price = scd_format_converted_price_to_html($reg_price, $args);
								$price_html = '<ins>' . $reg_price . '</ins>';  
						}else{
								$reg_price = scd_format_converted_price_to_html($reg_price, $args);
								$sale_price = scd_format_converted_price_to_html($sale_price, $args);
								$price_html = '<del>' . $reg_price . '</del> <ins>' . $sale_price . '</ins>';
						}					
				
					}
					
					
					
				}
				
			}else{
				
				if($target_currency != $base_currency){
					
					
				
					$reg_price = get_post_meta($product_id, '_regular_price', TRUE);
					
					$sale_price = get_post_meta($product_id, '_sale_price', TRUE);
					
					
						if(!empty($sale_price))
						$sale_price = scd_function_convert_subtotal($sale_price, $base_currency, $target_currency, $decimals );   
						if(!empty($reg_price))
						$reg_price = scd_function_convert_subtotal($reg_price, $base_currency, $target_currency, $decimals );  

						$decimals = scd_options_get_decimal_precision();
						$args['currency'] = $target_currency;
						$args['decimals'] = $decimals;
						$args['price_format'] = scd_change_currency_display_format ($args['price_format'], $target_currency);
						
						if($reg_price == $sale_price || empty($sale_price)){
								$reg_price = scd_format_converted_price_to_html($reg_price, $args);
								$price_html = '<ins>' . $reg_price . '</ins>';  
						}else{
								$reg_price = scd_format_converted_price_to_html($reg_price, $args);
								$sale_price = scd_format_converted_price_to_html($sale_price, $args);
								$price_html = '<del>' . $reg_price . '</del> <ins>' . $sale_price . '</ins>';
						}		
					
				
			    }
				
				
			}

        
    }	
  
  }
  

       
  return $price_html;

} 

	
function scd_get_price_for_currency_test ($product_id, $target_currency)
{
	

    $regprice = '';
    $saleprice = '';
	$currencyVal = '';

    // Check if a custom price has been specified by currency
    if (get_post_meta($product_id, 'scd_other_options')) {
		

        $datas = get_post_meta($product_id, 'scd_other_options');
        $currencyPrice=$datas[0]['currencyPrice'];
		$currencyVal = $datas[0]['currencyVal'];
		
        $tab= explode('_', $currencyPrice);
           
        $index= array_search($target_currency, $tab);
        if($index !== FALSE)
        {
            $regprice  = explode('-',$tab[$index+1] )[0];
            $saleprice = explode(',',$tab[$index+3] )[0];
        }  
    } 

    if($saleprice == '')
    {
        $saleprice = $regprice;
    }

    if ( ($saleprice == '') && ($regprice == '') )
        return null;
    else
        return array( floatval($regprice), floatval($saleprice), $currencyVal );

}