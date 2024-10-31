<?php
//for each order's we save the rate between the coustumers currency and woocommerce admin currency.
//this is use to assure good conversion on dashboard
function saveRateOrder($order_id){
	global $wpdb;
	$mpOrders =  $wpdb->get_results("SELECT ID FROM {$wpdb->prefix}wcfm_marketplace_orders WHERE `order_id` = {$order_id}");
	
	if (count($mpOrders) > 0) {
		# l'ordre est bien enregistre
		$user_curr = scd_get_target_currency();
		$wc_curr = get_option('woocommerce_currency'); 

		if($user_curr == $wc_curr)  {
			$rate = 1;
		}else{
			$rate =  scd_get_conversion_rate($user_curr,$wc_curr);
		}
		foreach ($mpOrders as $key => $value) {
			$wpdb->insert("{$wpdb->prefix}wcfm_marketplace_orders_meta", array(
			    'order_commission_id' => $value->ID,
			    'key' => "rate",
			    'value' => $rate
			));
		}
		
	}
}
add_action('woocommerce_thankyou','saveRateOrder');