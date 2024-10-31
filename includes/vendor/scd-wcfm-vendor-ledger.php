<?php
/*
* Author URI: http://gajelabs.com
* Description: This methode is include in index.php file for the scd_wcfm_marketplace. it is use to correct the error on the Leeger book page dashboard.
* Version 1.0.0
* Last update: 31 oct 2020.
* Author: GaJeLabs
 */



// the suite apply_filter for vendor commission is use in scd-wcfm-vendor-dashboard.php



add_filter('wcfm_vendor_dashboard_commission_paid','scd_wcfm_ledger_commission_paid',999,1);
function scd_wcfm_ledger_commission_paid($refund){
	global $wpdb;
	$vendor_id = get_current_user_id();
	//the mater is that: wcfm use the same filter to display the total refunds and withdrawal 
	//so i use the global $_SESSION to determine the first and second call of my function
	//first call is for withdrawal and second for total refunds 
	if (isset($_SESSION['scd_wcfm_vendor_ledger_order'])) {
		# code... for first call
		unset($_SESSION['scd_wcfm_vendor_ledger_order']);
		$vendor_id = get_current_user_id();
		$withdrawal 				= 0;
		
		//$user_curr = scd_get_target_currency();
		$wc_curr = get_option('woocommerce_currency');


		// 		GET ALL	ORDERS INCLUDING A REFUNDED ORDERS ACCORDING TO RAGE DATE
		$orders_temp =$wpdb->get_results('SELECT order_id, ID as commission_id, total_commission as earning FROM '.$wpdb->prefix.'wcfm_marketplace_orders WHERE `order_status` IN ("completed","processing","pending") AND `vendor_id` ='.$vendor_id);

		foreach ($orders_temp as $key => $single_order) {
			//******* for each order determine the rate to convert it in wc currency ******
			//1. looking for the rate, if there is not, then use the order currency to determine the current rate to use
			$rate = scd_wcfm_get_order_rate($single_order->order_id);

			$withdrawals = $wpdb->get_results('SELECT balance FROM '.$wpdb->prefix.'wcfm_marketplace_reverse_withdrawal WHERE `commission_id` = '.$single_order->commission_id.' AND `withdraw_status` = "completed"');
			foreach ($withdrawals as $key => $withdrawal_value) {
				$withdrawal += $withdrawal_value->balance*$rate;
			}
		}
		return wc_price($withdrawal);

		
	}else{
		$_SESSION['scd_wcfm_vendor_ledger_order'] = 1;
		$gross_sales 				= 0;
		
		//$user_curr = scd_get_target_currency();
		$wc_curr = get_option('woocommerce_currency');


		// 		GET ALL	ORDERS INCLUDING A REFUNDED ORDERS ACCORDING TO RAGE DATE
		$orders_temp =$wpdb->get_results('SELECT order_id, ID as commission_id, item_total as gross_sales FROM '.$wpdb->prefix.'wcfm_marketplace_orders WHERE `order_status` IN ("completed","processing","pending") AND `vendor_id` ='.$vendor_id);

		foreach ($orders_temp as $key => $single_order) {
			//******* for each order determine the rate to convert it in wc currency ******
			//1. looking for the rate, if there is not, then use the order currency to determine the current rate to use
			$rate = scd_wcfm_get_order_rate($single_order->order_id);

			//********** convert orders to wc currency ************************

			//*********** looking for a potential refund for the commission
			$refunds = $wpdb->get_results('SELECT refunded_amount FROM '.$wpdb->prefix.'wcfm_marketplace_refund_request WHERE `commission_id` = '.$single_order->commission_id.'  AND `refund_status` = "completed"');
			foreach ($refunds as $key => $refund) {
				$gross_sales += $refund->refunded_amount*$rate;
			}
		}
		return wc_price($gross_sales);	
	}
}
