<?php
/*
* Author URI: http://gajelabs.com
* Description: This methode is include in index.php file for the scd_wcfm_marketplace. it is use to correct the error on the report by vendor page dashboard.
* Version 1.0.0
* Last update: 31 oct 2020.
* Author: GaJeLabs
 */

//"wcfm_dashboard_item_title"
add_filter('wcfm_vendors_gross_sales_data','scd_wcfm_store_vendor_gross_sales',999,2);
function scd_wcfm_store_vendor_gross_sales($gross_sales,$vendor_id){
	global $wpdb;
	$gross_sales 				= 0;
	
	//$user_curr = scd_get_target_currency();
	$wc_curr = get_option('woocommerce_currency');


	// 		GET ALL	ORDERS INCLUDING A REFUNDED ORDERS ACCORDING TO RAGE DATE
	$orders_temp =$wpdb->get_results("SELECT order_id, ID as commission_id, item_total as gross_sales FROM {$wpdb->prefix}wcfm_marketplace_orders WHERE `order_status` IN ('completed','processing','pending') AND `vendor_id` = {$vendor_id}");

	foreach ($orders_temp as $key => $single_order) {
		//******* for each order determine the rate to convert it in wc currency ******
		//1. looking for the rate, if there is not, then use the order currency to determine the current rate to use
		$rate = scd_wcfm_get_order_rate($single_order->order_id);

		//********** convert orders to wc currency ************************
		$gross_sales += $single_order->gross_sales*$rate;

		//*********** looking for a potential refund for the commission
		$refunds = $wpdb->get_results("SELECT refunded_amount FROM {$wpdb->prefix}wcfm_marketplace_refund_request WHERE `commission_id` = {$single_order->commission_id}  AND `refund_status` = 'completed'");
		foreach ($refunds as $key => $refund) {
			$gross_sales -= $refund->refunded_amount*$rate;
		}
	}
	return wc_price($gross_sales);
}



























add_filter('wcfm_vendors_earned_commission_data','scd_wcfm_store_vendor_earned_commission',10,3);
function scd_wcfm_store_vendor_earned_commission($earned,$vendor_id,$range)
{
	global $wpdb;
	$earning 				= 0;
	
	//$user_curr = scd_get_target_currency();
	$wc_curr = get_option('woocommerce_currency');


	// 		GET ALL	ORDERS INCLUDING A REFUNDED ORDERS ACCORDING TO RAGE DATE
	$orders_temp =$wpdb->get_results('SELECT ID as commission_id, total_commission as earning FROM '.$wpdb->prefix.'wcfm_marketplace_orders WHERE `order_status` IN ("completed","processing","pending") AND `vendor_id` ='.$vendor_id);

	foreach ($orders_temp as $key => $single_order) {
		//******* for each order determine the rate to convert it in wc currency ******
		//1. looking for the rate, if there is not, then use the order currency to determine the current rate to use
		$rate_temp = $wpdb->get_results('SELECT value FROM '.$wpdb->prefix.'wcfm_marketplace_orders_meta WHERE `key` = "rate" AND `order_commission_id` = '.$single_order->commission_id);
		if (count($rate_temp)>0) {
			$rate = $rate_temp[0]->value;
		}else{
			$rate_temp = $wpdb->get_results('SELECT value FROM '.$wpdb->prefix.'wcfm_marketplace_orders_meta WHERE `key` = "currency" AND `order_commission_id` = '.$single_order->commission_id);
			if (count($rate_temp)>0) {
				$rate =  scd_get_conversion_rate($rate_temp[0]->value,$wc_curr);
			}else{
				$rate = 1;
			}
		}
		//********** convert orders to wc currency ************************
		$earning += $single_order->earning*$rate;

		//*********** looking for a potential refund for the commission
		$refunds = $wpdb->get_results('SELECT refunded_amount FROM '.$wpdb->prefix.'wcfm_marketplace_refund_request WHERE `commission_id` = '.$single_order->commission_id.' AND `refund_status` = "completed"');
		foreach ($refunds as $key => $refund) {
			$earning -= $refund->refund_amount*$rate;
		}
	}
	return wc_price($earning);
}










add_filter('wcfm_vendors_received_commission_data', 'scd_wcfm_vendors_received_commission_data',10,5);

function scd_wcfm_vendors_received_commission_data($received_commission,$vendor_id,$from_date,$to_date){

	global $wpdb;
	$withdrawal 				= 0;
	
	//$user_curr = scd_get_target_currency();
	$wc_curr = get_option('woocommerce_currency');


	// 		GET ALL	ORDERS INCLUDING A REFUNDED ORDERS ACCORDING TO RAGE DATE
	$orders_temp =$wpdb->get_results('SELECT ID as commission_id, total_commission as earning FROM '.$wpdb->prefix.'wcfm_marketplace_orders WHERE `order_status` IN ("completed","processing","pending") AND `vendor_id` ='.$vendor_id);

	foreach ($orders_temp as $key => $single_order) {
		//******* for each order determine the rate to convert it in wc currency ******
		//1. looking for the rate, if there is not, then use the order currency to determine the current rate to use
		$rate_temp = $wpdb->get_results('SELECT value FROM '.$wpdb->prefix.'wcfm_marketplace_orders_meta WHERE `key` = "rate" AND `order_commission_id` = '.$single_order->commission_id);
		if (count($rate_temp)>0) {
			$rate = $rate_temp[0]->value;
		}else{
			$rate_temp = $wpdb->get_results('SELECT value FROM '.$wpdb->prefix.'wcfm_marketplace_orders_meta WHERE `key` = "currency" AND `order_commission_id` = '.$single_order->commission_id);
			if (count($rate_temp)>0) {
				$rate =  scd_get_conversion_rate($rate_temp[0]->value,$wc_curr);
			}else{
				$rate = 1;
			}
		}

		$withdrawals = $wpdb->get_results('SELECT balance FROM '.$wpdb->prefix.'wcfm_marketplace_reverse_withdrawal WHERE `commission_id` = '.$single_order->commission_id.' AND `withdraw_status` = "completed"');
		foreach ($withdrawals as $key => $withdrawal_value) {
			$withdrawal += $withdrawal_value->balance*$rate;
		}
	}
	return wc_price($withdrawal);
}