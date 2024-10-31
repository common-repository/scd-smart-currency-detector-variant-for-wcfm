<?php
/**
*	Determine the rate used during the checkout for each order
*/
function scd_wcfm_get_order_rate($order_id){
	global $wpdb;
	$commission = $wpdb->get_results("SELECT ID as commission_id FROM {$wpdb->prefix}wcfm_marketplace_orders WHERE `order_id` = {$order_id}");
	$wc_curr = get_option('woocommerce_currency');

	if (count($commission)>0) {//il y a une commission associe a l'ordre
	
		$rate_temp = $wpdb->get_results('SELECT value FROM '.$wpdb->prefix.'wcfm_marketplace_orders_meta WHERE `key` = "rate" AND `order_commission_id` = '.$commission[0]->commission_id);
		if (count($rate_temp)>0) {//il y a un rate qui est associe a la commission
			$rate = $rate_temp[0]->value;
		}else{//on recherche le currency associe a la commission s'il y a pas de rate stocke
			$curr = $wpdb->get_results('SELECT value FROM '.$wpdb->prefix.'wcfm_marketplace_orders_meta WHERE `key` = "currency" AND `order_commission_id` = '.$commission[0]->commission_id);
			if (count($curr)>0) {//on determine le rate
				$rate =  scd_get_conversion_rate($curr[0]->value,$wc_curr);
			}else{
				$rate = 1;
			}
		}
	}else{//il y pas de commission associe a l'ordre
			//on rechrche le currency associe a l'ordre
		$curr = $wpdb->get_results('SELECT meta_value FROM '.$wpdb->prefix.'postmeta WHERE `meta_key` = "_order_currency" AND `post_id` = '.$order_id);
		if (count($curr)>0) {//on determine le rate
			# code...
			$rate =  scd_get_conversion_rate($rate_temp[0]->value,$wc_curr);
		}else{
			$rate = 1;
		}
	}
	return $rate;
}


/**
* SCM WCFM Gross sales by Vendor
*/
function scd_wcfm_get_gross_sales_by_vendor( $vendor_id = '', $interval = '7day', $is_paid = false, $order_id = 0, $filter_date_form = '', $filter_date_to = '' ) {
	global $WCFM, $wpdb, $WCMp, $WCFMmp;
	
	if( $vendor_id ) $vendor_id = absint($vendor_id);
	
	$gross_sales = 0;
	
	$marketplece = wcfm_is_marketplace();
	if( $marketplece == 'wcfmmarketplace' ) {
		$sql = "SELECT ID, order_id, item_id, item_total, item_sub_total, refunded_amount, shipping, tax, shipping_tax_amount FROM {$wpdb->prefix}wcfm_marketplace_orders AS commission";
		$sql .= " WHERE 1=1";
		if( $vendor_id ) $sql .= " AND `vendor_id` = {$vendor_id}";
		if( $order_id ) {
			$sql .= " AND `order_id` = {$order_id}";
			//$sql .= " AND `is_refunded` != 1";
		} else {
			$sql .= apply_filters( 'wcfm_order_status_condition', '', 'commission' );
			$sql .= " AND `is_trashed` = 0";
			if( $is_paid ) {
				$sql .= " AND commission.withdraw_status = 'completed'";
				$sql = wcfm_query_time_range_filter( $sql, 'commission_paid_date', $interval, $filter_date_form, $filter_date_to );
			} else {
				$sql = wcfm_query_time_range_filter( $sql, 'created', $interval, $filter_date_form, $filter_date_to );
			}
		}
		
		$gross_sales_whole_week = $wpdb->get_results( $sql );
		$gross_commission_ids = array();
		$gross_total_refund_amount = 0;
		if( !empty( $gross_sales_whole_week ) ) {
			foreach( $gross_sales_whole_week as $net_sale_whole_week ) {

				$rate = scd_wcfm_get_order_rate($net_sale_whole_week->order_id);

				$gross_commission_ids[] = $net_sale_whole_week->ID;
				$gross_total_refund_amount += (float) sanitize_text_field( $net_sale_whole_week->refunded_amount )*$rate;
			}
		
		  	if( !empty( $gross_commission_ids ) ) {
				try {
					if( apply_filters( 'wcfmmmp_gross_sales_respect_setting', true ) ) {
						$gross_sales = (float) scd_wcfmmp_get_commission_meta_sum( $gross_commission_ids, 'gross_total' );
					} else {
						$gross_sales = (float) scd_wcfmmp_get_commission_meta_sum( $gross_commission_ids, 'gross_sales_total' );
					}
					
					/*if( $WCFMmp->wcfmmp_vendor->is_vendor_deduct_discount( $vendor_id, $net_sale_whole_week->order_id ) ) {
						$gross_sales += (float) sanitize_text_field( $net_sale_whole_week->item_total );
					} else {
						$gross_sales += (float) sanitize_text_field( $net_sale_whole_week->item_sub_total );
					}
					if( $is_vendor_get_tax = $WCFMmp->wcfmmp_vendor->is_vendor_get_tax( $vendor_id ) ) {
						//$gross_sales += (float) $net_sale_whole_week->tax;
						
						// WC Refund Support - 3.0.4
						$gross_sales += (float) $WCFMmp->wcfmmp_commission->wcfmmp_get_commission_meta( $net_sale_whole_week->ID, 'gross_tax_cost' );
					}
					if( $WCFMmp->wcfmmp_vendor->is_vendor_get_shipping( $vendor_id ) ) {
						$gross_sales += (float) apply_filters( 'wcfmmmp_gross_sales_shipping_cost', $WCFMmp->wcfmmp_commission->wcfmmp_get_commission_meta( $net_sale_whole_week->ID, 'gross_shipping_cost' ), $vendor_id );
						if( $is_vendor_get_tax ) {
							$gross_sales += (float) $WCFMmp->wcfmmp_commission->wcfmmp_get_commission_meta( $net_sale_whole_week->ID, 'gross_shipping_tax' );
						}
					}*/
					
					
					// Deduct Refunded Amount
					$gross_sales -= (float) $gross_total_refund_amount;
				} catch (Exception $e) {
					//continue;
				}
			}
		}
	}

	if( !$gross_sales ) $gross_sales = 0;
	
	return $gross_sales;
}


	
/**
 * SCM WCFM Get Commission metas SUM
 */
function scd_wcfmmp_get_commission_meta_sum( $commission_ids, $key ) {
	global $WCFM, $WCFMmp, $wpdb;
	
	if( empty( $commission_ids ) || !is_array( $commission_ids ) ) return 0;
	
	$commission_meta = 0;
	$commission_metas = $wpdb->get_results( $wpdb->prepare("SELECT value FROM `{$wpdb->prefix}wcfm_marketplace_orders_meta`  WHERE `order_commission_id` in ('" . implode( "','", $commission_ids ) . "') AND `key` = %s ", $key));

	$commission_orders = $wpdb->get_results( $wpdb->prepare("SELECT order_id FROM `{$wpdb->prefix}wcfm_marketplace_orders`  WHERE `ID` in ('" . implode( "','", $commission_ids ) . "')"));

	foreach ($commission_ids as $key => $commission_id) {
		$rate = scd_wcfm_get_order_rate($commission_orders[$key]->order_id);

		$commission_meta += $commission_metas[$key]->value*$rate;
	}

	return $commission_meta;
}


/**
* Total commission paid by Admin
*/
function scd_wcfm_get_commission_by_vendor( $vendor_id = '', $interval = '7day', $is_paid = false, $order_id = 0, $filter_date_form = '', $filter_date_to = '' ) {
	global $WCFM, $wpdb, $WCMp;
	
	if( $vendor_id ) $vendor_id = absint($vendor_id);
	
	$commission = 0;
	
	$marketplece = wcfm_is_marketplace();
	if( $marketplece == 'wcfmmarketplace' ) {
		$commission_table = 'wcfm_marketplace_orders'; 
		$total_due = 'total_commission';
		$total_shipping = 'shipping';
		$tax = 'tax';
		$shipping_tax = 'shipping_tax_amount';
		$status = 'withdraw_status';
		$vendor_handler = 'vendor_id';
		$table_handler = 'commission';
		if( $is_paid )
			$time = 'commission_paid_date';
		else
			$time = 'created';
	}
	
	if( $marketplece != 'dokan' ) {
	  $sql = "SELECT order_id, {$total_due}  AS total_due, commission.{$total_shipping} AS total_shipping, {$tax} AS tax, {$shipping_tax}  AS shipping_tax FROM {$wpdb->prefix}{$commission_table} AS commission";
	}
	
	$sql .= " WHERE 1=1";
	if( $vendor_id ) $sql .= " AND commission.{$vendor_handler} = {$vendor_id}";
	if( $is_paid ) $sql .= " AND (commission.{$status} = 'paid' OR commission.{$status} = 'completed')";
	if( $marketplece == 'wcfmmarketplace' ) { 
		if( $order_id ) {
			$sql .= " AND `order_id` = {$order_id}";
		} else {
			$sql .= apply_filters( 'wcfm_order_status_condition', '', 'commission' );
			$sql .= " AND `is_refunded` = 0 AND `is_trashed` = 0";
		}
	}
	if( !$order_id )
		$sql = wcfm_query_time_range_filter( $sql, $time, $interval, $filter_date_form, $filter_date_to, $table_handler );
	
	$total_commissions = $wpdb->get_results( $sql );
	$commission = 0;
	if( !empty($total_commissions) ) {
		foreach( $total_commissions as $total_commission ) {
			$rate = scd_wcfm_get_order_rate($total_commission->order_id);
			$commission += $total_commission->total_due*$rate;
		}
	}
	if( !$commission ) $commission = 0;
	return $commission;
}
