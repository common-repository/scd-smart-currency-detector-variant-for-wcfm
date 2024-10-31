<?php



add_filter('wcfm_vendor_dashboard_gross_sales','_scd_wcfm_vendor_dashboard_gross_sales',999,1);
function _scd_wcfm_vendor_dashboard_gross_sales($gross_sales){
	$gross_sales = scd_wcfm_get_gross_sales_by_vendor(get_current_user_id());
	return wc_price($gross_sales);
}



add_filter('wcfm_vendor_dashboard_commission', 'scd_wcfm_vendor_dashboard_commission', 999, 1);

function scd_wcfm_vendor_dashboard_commission($earning){
	global $wpdb;
	$gross_sales 				= 0;
	$vendor_id = get_current_user_id();
	
	$commission = scd_wcfm_get_commission_by_vendor($vendor_id);
    if (!$commission) $commission = 0;
	return wc_price($commission);
}



	