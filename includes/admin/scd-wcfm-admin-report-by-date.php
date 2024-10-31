<?php


//report by vendor
add_action('wcfm_wcfmmarketplace_report_sales_by_date_after','scd_wcfm_wcfmmarketplace_report_sales_by_date_after');
function scd_wcfm_wcfmmarketplace_report_sales_by_date_after(){
	if (wcfm_is_vendor()) {
		?>
		<script type="text/javascript">
			document.getElementsByClassName('wcfm-container')[1].remove();
		</script>
		<?php
	}else{

		?>
		<script type="text/javascript">
			document.getElementsByClassName('wcfm-container')[2].remove();
		</script>
		<?php
	}
	include_once('scd-wcfm-views-report-sales-by-date.php');
}




add_action('after_wcfm_dashboard_right_col','scd_wcfm_admin_dashboard_commission');
function scd_wcfm_admin_dashboard_commission(){
	global $wpdb;
	$gross_sales 				= 0;
	
	$wc_curr = get_option('woocommerce_currency');


	// 		GET ALL	ORDERS INCLUDING A REFUNDED ORDERS ACCORDING TO RAGE DATE
	$orders_temp =$wpdb->get_results('SELECT ID as commission_id, total_commission as gross_sales FROM '.$wpdb->prefix.'wcfm_marketplace_orders WHERE `order_status` IN ("completed","processing","pending")');

	foreach ($orders_temp as $key => $single_order) {
		//******* for each order determine the rate to convert it in wc currency ******
		//1. looking for the rate, if there is not, then use the order currency to determine the current rate to use
		$rate = scd_wcfm_get_order_rate($data->order_id);
		//********** convert orders to wc currency ************************
		$gross_sales += $single_order->gross_sales*$rate;

		//*********** looking for a potential refund for the commission
		$refunds = $wpdb->get_results('SELECT refunded_amount FROM '.$wpdb->prefix.'wcfm_marketplace_refund_request WHERE `commission_id` = '.$single_order->commission_id.'  AND `refund_status` = "completed"');
		foreach ($refunds as $key => $refund) {
			$gross_sales -= $refund->refunded_amount*$rate;
		}
	}

	?>
	<script type="text/javascript">
		var time = setInterval(function(){
			//document.getElementsByClassName('wcfm_dashboard_stats_block')[1].children[0].children[1].children[0].textContent='';
			//document.getElementsByClassName('wcfm_dashboard_stats_block')[1].children[0].children[1].children[0].innerHTML=<?php //echo json_encode(wc_price($gross_sales));?>;
			clearInterval(time);
		},2000);		
	</script>
	<?php
}


add_filter('woocommerce_admin_report_data','report_data_by_date_for_admin',999,1);
function report_data_by_date_for_admin($report_data){
	
	?>
<script type="text/javascript">
	console.log(<?php echo json_encode($report_data);?>);
</script>
	<?php
	global $wpdb;
	$sort = 'Y-m-d';//variable used to groupe the values by date or month
	//***************** set date range ***************
	if (isset($_GET['range'])) {
		$range 		= $_GET['range'];
	}else{
		$range 		= '7day';//default range
		if (strpos($_SERVER['REQUEST_URI'],'wp-admin') !== false) {
			# code...
			$screen = get_current_screen();
			if ($screen->id == "dashboard") {
				// This is the admin Dashboard screen for wordpress
				$range = 'month';
			}
		}
		if(strpos($_SERVER['REQUEST_URI'],'store-manager') !== false){
			$range = 'month';
		}
		if(strpos($_SERVER['REQUEST_URI'],'reports-sales-by-date') !== false){
			$range = '7day';
		}
	}
	if ($range == 'custom') {
		$from_date  = new DateTime($_GET['wcfm-date_from']);
		$to_date	= new DateTime($_GET['wcfm-date_to']);
	}else{
		if($range == '7day'){
			$to_date	= new DateTime('now');
			$from_date  = $to_date->modify('-6 day');
			$to_date	= new DateTime('now');
		}else if($range == 'month'){
			$to_date	= new DateTime('now');
			$from_date  = new DateTime('first day of this month');
		}else if($range == 'last_month'){
			$from_date	= new DateTime("first day of last month");
			$to_date  = new DateTime("last day of last month");
		}else if($range == 'year'){
			$to_date	= new DateTime('now');
			$from_date  = $to_date->modify('-365 day');
			$to_date	= new DateTime('now');
			$sort = 'Y-m';
		}
	}

	//************ the parameters data to comput *********
	$coupons = array();
	$coupons_key = 0;

	$orders = array();
	$orders_key = 0;

	$full_refunds = array();

	$refund_lines = array();
	$full_refunds = array();
	$refundeds_key = 0;
	$partial_refunds = array();
	$partial_refunds_key = 0;

	$net_sales  				= 0;

	$average_total_sales 		= 0;
	$refunded_order_items 		= 0;
	$total_coupons				= 0;
	//$total_items				= 0;
	//$total_orders 				= 0;​
	$total_refunded_orders 		= 0;
	$total_refunds 				= 0;
	$total_sales 				= 0;
	$total_shipping 			= 0;
	$total_shipping_refunded    = 0;
	$total_shipping_tax 		= 0;
	$total_shipping_tax_refunded= 0;
	$total_tax 					= 0;
	$total_tax_refunded 		= 0;

	//period is use to process the average
	$period = 1 + $to_date->diff($from_date)->format('%a');
	if ($range == 'year') {	
		$period = 12;//number of month
	}
	
	//$user_curr = scd_get_target_currency();
	$wc_curr = get_option('woocommerce_currency');

















	//			GET ALL	COUPONS ACCORDING TO RANGE DATE
	$coupon = $wpdb->get_results('SELECT order_id, coupon_id as order_item_name, discount_amount, date_created as post_date FROM '.$wpdb->prefix.'wc_order_coupon_lookup WHERE `date_created` >= "'.$from_date->format('Y-m-d H:i:s').'" AND `date_created` <= "'.$to_date->format('Y-m-d H:i:s').'"');
	foreach ($coupon as $key => $single_coupon) {
		//******* for each coupon determine the rate ******
		//1. looking for one commission associete to the order and found his rate
		$rate = scd_wcfm_get_order_rate($single_coupon->order_id);
		//for each order we cant have more thant one coupon
		$single_coupon->discount_amount *= $rate;//convert to wc_curr using order rate.
		$single_coupon->order_item_name = $wpdb->get_results('SELECT post_name FROM '.$wpdb->prefix.'posts WHERE `ID` = '.$single_coupon->order_item_name)[0]->post_name;
		unset($single_coupon->order_id);

		if (count($coupons) == 0) {//first coupon
			$coupons[] = $single_coupon;
		}else{
			$flag = false;
			foreach ($coupons as $coupon_key => $coupon_value) {
				$date1 = new \DateTime($coupon_value->post_date);
				$date2 = new \DateTime($single_coupon->post_date);
				if ($coupon_value->order_item_name == $single_coupon->order_item_name && $date2->format($sort) == $date1->format($sort)) {
					# the two coupon have same code and same date
					$flag = true;
					$coupons_key =$coupon_key;
					break;
				}
			}
			if ($flag) {
				$coupons[$coupons_key]->discount_amount += $single_coupon->discount_amount; 
			}else{
				$coupons[] = $single_coupon;
			}	
		}
		$total_coupons += $single_coupon->discount_amount;
	}
	$report_data->coupons = $coupons;
	$report_data->total_coupons = $total_coupons;
















	// 		GET ALL	ORDERS INCLUDING A REFUNDED ORDERS ACCORDING TO RAGE DATE
	$orders_temp =$wpdb->get_results('SELECT order_id, total_sales, shipping_total as total_shipping , tax_total as total_tax , net_total as total_shipping_tax, date_created as post_date FROM '.$wpdb->prefix.'wc_order_stats WHERE `date_created` >= "'.$from_date->format('Y-m-d H:i:s').'" AND `date_created` <= "'.$to_date->format('Y-m-d H:i:s').'" AND `status` IN ("wc-completed","wc-processing","wc-pending","wc-refunded","wc-on-hold") AND `parent_id` = 0 ');

	foreach ($orders_temp as $key => $single_order) {
		//******* for each order determine the rate ******
		//1. looking for one commission associete to the order and found his rate
		$commissions =  $wpdb->get_results('SELECT ID as commission_id, shipping_tax_amount as total_shipping_tax FROM '.$wpdb->prefix.'wcfm_marketplace_orders WHERE `order_id` = '.$single_order->order_id);

		$rate = scd_wcfm_get_order_rate($single_order->order_id);

		unset($single_order->order_id);
		//********** convert orders to wc currency ************************
		$single_order->total_sales *=$rate;
		$single_order->total_shipping *=$rate;
		$single_order->total_tax *=$rate;
		$single_order->total_shipping_tax = 0;
		foreach ($commissions as $commission_value) {
			$single_order->total_shipping_tax +=$commission_value->total_shipping_tax*$rate;
		}
		
		//************ sort by date
		$flag = false;
		foreach ($orders as $order_key => $order_value) {
			$date1 = new \DateTime($order_value->post_date);
			$date2 = new \DateTime($single_order->post_date);
			if ($date2->format($sort) == $date1->format($sort)) {
				$flag = true;
				$orders_key =$order_key;
				break;
			}
		}
		if ($flag) {
			$orders[$orders_key]->total_sales += $single_order->total_sales;
			$orders[$orders_key]->total_shipping += $single_order->total_shipping;
			$orders[$orders_key]->total_tax += $single_order->total_tax;
			$orders[$orders_key]->total_shipping_tax += $single_order->total_shipping_tax;
		}else{
			$orders[] = $single_order;
		}	
		
	}
	$report_data->orders = $orders;














	// 		GET ALL	ORDERS FULL REFUNDESD BY ADMIN ACCORDING TO RAGE DATE
	$full_refunds_temp =$wpdb->get_results('SELECT order_id, total_sales as total_refund, shipping_total as total_shipping , tax_total as total_tax , net_total as total_shipping_tax, date_created as post_date FROM '.$wpdb->prefix.'wc_order_stats WHERE `date_created` >= "'.$from_date->format('Y-m-d H:i:s').'" AND `date_created` <= "'.$to_date->format('Y-m-d H:i:s').'" AND `status` = "wc-refunded" AND `parent_id` = 0 ');

	foreach ($full_refunds_temp as $key => $single_refund) {
		//******* for each order determine the rate ******
		//1. looking for one commission associete to the order and found his rate
		$commissions =  $wpdb->get_results('SELECT ID as commission_id, shipping_tax_amount as total_shipping_tax FROM '.$wpdb->prefix.'wcfm_marketplace_orders WHERE `order_id` = '.$single_refund->order_id);

		$rate = scd_wcfm_get_order_rate($single_refund->order_id);

		unset($single_refund->order_id);
		//********** convert orders to wc currency ************************
		$single_refund->total_refund *=$rate;
		$single_refund->total_shipping *=$rate;
		$single_refund->total_tax *=$rate;
		$single_refund->total_shipping_tax = 0;
		foreach ($commissions as $commission_value) {
			$single_refund->total_shipping_tax +=$commission_value->total_shipping_tax*$rate;
		}
		
		//************ sort by date
		$flag = false;
		foreach ($full_refunds as $refund_key => $refund_value) {
			$date1 = new \DateTime($refund_value->post_date);
			$date2 = new \DateTime($single_refund->post_date);
			if ($date2->format($sort) == $date1->format($sort)) {
				$flag = true;
				$refunds_key =$refund_key;
				break;
			}
		}
		if ($flag) {
			$full_refunds[$refunds_key]->total_sales += $single_refund->total_refund;
			$full_refunds[$refunds_key]->total_shipping += $single_refund->total_shipping;
			$full_refunds[$refunds_key]->total_tax += $single_refund->total_tax;
			$full_refunds[$refunds_key]->total_shipping_tax += $single_refund->total_shipping_tax;
		}else{
			$full_refunds[] = $single_refund;
		}
	}
	$report_data->full_refunds = $full_refunds;








	// 		GET ALL	ORDERS PARTIALY REFUNDED BY ADMIN AND VENDOR ACCORDING TO RAGE DATE
	$partial_refunds_temp =$wpdb->get_results('SELECT order_id as refund_id, total_sales as total_refund, total_sales, shipping_total as total_shipping , tax_total as total_tax , net_total as total_shipping_tax, date_created as post_date, num_items_sold as order_item_count, parent_id, customer_id  as item_type FROM '.$wpdb->prefix.'wc_order_stats WHERE `date_created` >= "'.$from_date->format('Y-m-d H:i:s').'" AND `date_created` <= "'.$to_date->format('Y-m-d H:i:s').'" AND `status` = "wc-completed" AND `parent_id` <> 0');

	foreach ($partial_refunds_temp as $key => $single_refund) {
		//******* for each order determine the rate ******
		//1. looking for one commission associete to the order and found his rate
		if (count($wpdb->get_results('SELECT * FROM '.$wpdb->prefix.'wc_order_stats WHERE `order_id` = '.$single_refund->parent_id.' AND `status` = "wc-refunded"'))<=0) {
			$commissions =  $wpdb->get_results('SELECT order_id, ID as commission_id, shipping_tax_amount as total_shipping_tax , item_type FROM '.$wpdb->prefix.'wcfm_marketplace_orders WHERE `order_id` = '.$single_refund->refund_id);
			$single_refund->item_type = null;
		
			$rate = scd_wcfm_get_order_rate($single_refund->refund_id);

			unset($single_refund->refund_id);
			//********** convert orders to wc currency ************************
			$single_refund->total_refund *= -1*$rate;
			$single_refund->total_sales *= $rate;
			$single_refund->total_shipping *=$rate;
			$single_refund->total_tax *=$rate;
			$single_refund->total_shipping_tax = 0;
			foreach ($commissions as $commission_value) {
				$single_refund->total_shipping_tax +=$commission_value->total_shipping_tax*$rate;
			}
			
			//************ sort by date
			$flag = false;
			
			$partial_refunds[] = $single_refund;
		
		}
		
	}
	$report_data->partial_refunds = $partial_refunds;





















	// 		GET ALL	ORDERS PARTIALY REFUNDED BY ADMIN AND VENDOR ACCORDING TO RAGE DATE
	$partial_refunds_temp =$wpdb->get_results('SELECT order_id as refund_id, total_sales as total_refund, total_sales, shipping_total as total_shipping , tax_total as total_tax , net_total as total_shipping_tax, date_created as post_date, num_items_sold as order_item_count, parent_id, customer_id  as item_type FROM '.$wpdb->prefix.'wc_order_stats WHERE `date_created` >= "'.$from_date->format('Y-m-d H:i:s').'" AND `date_created` <= "'.$to_date->format('Y-m-d H:i:s').'" AND `status` = "wc-completed" AND `parent_id` <> 0');
	$partial_refunds = array();
	foreach ($partial_refunds_temp as $key => $single_refund) {
		//******* for each order determine the rate ******
		//1. looking for one commission associete to the order and found his rate
		
			$commissions =  $wpdb->get_results('SELECT ID as commission_id, shipping_tax_amount as total_shipping_tax , item_type FROM '.$wpdb->prefix.'wcfm_marketplace_orders WHERE `order_id` = '.$single_refund->refund_id);
			$single_refund->item_type = null;
		
			$rate = scd_wcfm_get_order_rate($single_refund->refund_id);

			unset($single_refund->refund_id);
			//********** convert orders to wc currency ************************
			$single_refund->total_refund *= -1*$rate;
			$single_refund->total_sales *= $rate;
			$single_refund->total_shipping *=$rate;
			$single_refund->total_tax *=$rate;
			$single_refund->total_shipping_tax = 0;
			foreach ($commissions as $commission_value) {
				$single_refund->total_shipping_tax +=$commission_value->total_shipping_tax*$rate;
			}
			
			//************ sort by date
			$flag = false;
			
			$partial_refunds[] = $single_refund;

			$total_refunds += $single_refund->total_refund;
			$total_shipping_refunded += $single_refund->total_shipping_refunded;
			$total_shipping_tax_refunded += $single_refund->total_shipping_tax_refunded;

			$total_shipping_tax += $single_refund->total_shipping_tax;
		
		
	}
	$report_data->refund_lines = $partial_refunds;
	$report_data->total_refunds = $total_refunds;
	$report_data->total_shipping_refunded = $total_shipping_refunded;
	$report_data->total_shipping_tax_refunded = $total_shipping_tax_refunded;

	$report_data->total_shipping_tax = $total_shipping_tax;
		









	$report_data->total_tax          = wc_format_decimal( array_sum( wp_list_pluck( $report_data->orders, 'total_tax' ) ) - $report_data->total_tax_refunded, 2 );

	$report_data->total_shipping     = wc_format_decimal( array_sum( wp_list_pluck( $report_data->orders, 'total_shipping' ) ) - $report_data->total_shipping_refunded, 2 );

	$report_data->total_shipping_tax = wc_format_decimal( array_sum( wp_list_pluck( $report_data->orders, 'total_shipping_tax' ) ) - $report_data->total_shipping_tax_refunded, 2 );

	// Total the refunds and sales amounts. Sales subract refunds. Note - total_sales also includes shipping costs.
	$report_data->total_sales = wc_format_decimal( array_sum( wp_list_pluck( $report_data->orders, 'total_sales' ) ) - $report_data->total_refunds, 2 );

	$report_data->net_sales   = wc_format_decimal( $report_data->total_sales - $report_data->total_shipping - max( 0, $report_data->total_tax ) - max( 0, $report_data->total_shipping_tax ), 2 );

	// Calculate average based on net
	$report_data->average_sales       = wc_format_decimal( $report_data->net_sales / ( $period ), 2 );

	$report_data->average_total_sales = wc_format_decimal( $report_data->total_sales / ( $period ), 2 );

	?>
	<script type="text/javascript">
		console.log(<?php echo json_encode($report_data);?>);
	</script>
	<?php
	return $report_data;
}	

