<?php
/**
 * WCFM plugin view
 *
 * WCFM Reports - WCfM Marketplace Sales by Date View
 *
 * @author 		WC Lovers
 * @package 	wcfm/view/reports/
 * @version   5.0.0
 */
 
$wcfm_is_allow_reports = apply_filters( 'wcfm_is_allow_reports', true );
if( !$wcfm_is_allow_reports ) {
	wcfm_restriction_message_show( __( 'Reports', 'scd_wcfm_marketplace' ) );
	return;
}

global $wp, $WCFM, $wpdb, $WCFMmp;

if( isset( $wp->query_vars['wcfm-reports-sales-by-date'] ) && !empty( $wp->query_vars['wcfm-reports-sales-by-date'] ) ) {
	$wcfm_report_type = $wp->query_vars['wcfm-reports-sales-by-date'];
}

$sales_by_vendor_mode = false;
$sales_by_no_vendor_mode = false;
if( $WCFM->is_marketplace && ( $WCFM->is_marketplace == 'wcfmmarketplace' ) ) {
	if( isset( $wp->query_vars['wcfm-reports-sales-by-vendor'] ) ) {
		if( !wcfm_is_vendor() ) {
			if( !empty( $wp->query_vars['wcfm-reports-sales-by-vendor'] ) ) {
				$wcfm_vendor = $wp->query_vars['wcfm-reports-sales-by-vendor'];
				if( $wcfm_vendor && wcfm_is_vendor( $wcfm_vendor ) ) {
					$WCFMmp->vendor_id = absint($wcfm_vendor);
					$sales_by_vendor_mode = true;
				} else {
					wcfm_restriction_message_show( __( 'Invalid Vendor', 'scd_wcfm_marketplace' ) );
					return;
				}
			} else {
				$sales_by_no_vendor_mode = true;
			}
		} else {
			wcfm_restriction_message_show( __( 'Sales by Vendor', 'scd_wcfm_marketplace' ) );
			return;
		}
	}
} else {
	wcfm_restriction_message_show( __( 'Sales by Vendor', 'scd_wcfm_marketplace' ) );
	return;
}

include_once( 'class-scd-wcfmmarketplace-report-sales-by-date.php' );

$wcfm_report_sales_by_date = new SCD_WCFM_Marketplace_Report_Sales_By_Date();

$ranges = array(
	//'year'         => __( 'Year', 'wc-frontend-manager' ),
	'year'         => __( 'Year', 'scd_wcfm_marketplace' ),
	'last_month'   => __( 'Last Month', 'scd_wcfm_marketplace' ),
	'month'        => __( 'This Month', 'scd_wcfm_marketplace' ),
	'7day'         => __( 'Last 7 Days', 'scd_wcfm_marketplace' )
);

$wcfm_report_sales_by_date->chart_colors = apply_filters( 'wcfm_vendor_sales_by_date_chart_colors', array(
			'average'            => '#95a5a6',
			'order_count'        => '#f8cb00',
			'item_count'         => '#ffc107',
			'tax_amount'         => '#73818f',
			'shipping_amount'    => '#6f42c1',
			'earned'             => '#20a8d8',
			'commission'         => '#20c997',
			'gross_sales_amount' => '#3498db',
			'refund'             => '#e83e8c',
		) );

$current_range = ! empty( $_GET['range'] ) ? sanitize_text_field( $_GET['range'] ) : '7day';

if ( ! in_array( $current_range, array( 'custom', 'year', 'last_month', 'month', '7day' ) ) ) {
	$current_range = '7day';
}

$wcfm_report_sales_by_date->calculate_current_range( $current_range );

?>

<div class="collapse wcfm-collapse" id="wcfm_report_details" style="width: 100%;">

  	<div class="wcfm-collapse-content" style="width: 100%">
	  
	  <?php if( $sales_by_vendor_mode ) { ?>
	  	
	  <?php } elseif( $sales_by_no_vendor_mode) { ?>
	  	
	  <?php } else { ?>
	  	<br />
	  <?php } ?>
	  
	  <?php if( !$sales_by_no_vendor_mode ) { ?>
			<div class="wcfm-container">
				<div id="wcfm_reports_sales_by_date_expander" class="wcfm-content">
				
					<?php
						include( $WCFM->plugin_path . '/views/reports/wcfm-html-report-sales-by-date.php');
					?>
				
				</div>
			</div>
		<?php } ?>
	</div>
	
	<?php //do_action( 'wcfm_wcfmmarketplace_report_sales_by_date_after' ); ?>
</div>