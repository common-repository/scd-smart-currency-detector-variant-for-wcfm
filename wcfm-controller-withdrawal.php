<?php
/**
 * WCFM plugin controllers
 *
 * Plugin WCfM Marketplace Withdrawal Dashboard Controller
 *
 * @author 		WC Lovers
 * @package 	wcfm/controllers/withdrawal/wcfm
 * @version   5.0.0
 */

class SCD_WCFM_Withdrawal_Controller {
	
	private $vendor_id;
	
	public function __construct() {
		global $WCFM, $WCFMmp;
		
		$this->vendor_id  = $WCFMmp->vendor_id;
		
		$this->processing();
	}
	
	public function processing() {
		global $WCFM, $wpdb, $_POST, $WCFMmp;

		$user_curr= scd_get_user_currency();
		
		$generate_auto_withdrawal = isset( $WCFMmp->wcfmmp_withdrawal_options['generate_auto_withdrawal'] ) ? $WCFMmp->wcfmmp_withdrawal_options['generate_auto_withdrawal'] : 'no';
		if( isset( $WCFMmp->wcfmmp_withdrawal_options['withdrawal_mode'] ) ) {
			$withdrawal_mode = isset( $WCFMmp->wcfmmp_withdrawal_options['withdrawal_mode'] ) ? $WCFMmp->wcfmmp_withdrawal_options['withdrawal_mode'] : '';
		} elseif( $generate_auto_withdrawal == 'yes' ) {
			$withdrawal_mode = 'by_order_status';
		} else {
			$withdrawal_mode = 'by_manual';
		}
		
		$length = absint($_POST['length']);
		$offset = absint($_POST['start']);
		
		$start_date = '';
    $end_date = '';
    
    if( isset($_POST['start_date']) && !empty($_POST['start_date']) ) {
    		$start_date = date('Y-m-d', strtotime(wc_clean($_POST['start_date'])) );
    }
    
    if( isset($_POST['end_date']) && !empty($_POST['end_date']) ) {
    		$end_date = date('Y-m-d', strtotime(wc_clean($_POST['end_date'])) );
    }
		
		$the_orderby = ! empty( $_POST['orderby'] ) ? sanitize_sql_orderby( $_POST['orderby'] ) : 'order_id';
		$the_order   = ( ! empty( $_POST['order'] ) && 'asc' === $_POST['order'] ) ? 'ASC' : 'DESC';

		$withdrawal_thresold = $WCFMmp->wcfmmp_withdraw->get_withdrawal_thresold( $this->vendor_id );

		$sql = 'SELECT COUNT(commission.ID) FROM ' . $wpdb->prefix . 'wcfm_marketplace_orders AS commission';
		$sql .= ' WHERE 1=1';
		$sql .= " AND `vendor_id` = %d";
		$sql .= apply_filters( 'wcfm_order_status_condition', '', 'commission' );
		$sql .= " AND commission.withdraw_status IN ('pending', 'cancelled')";
		$sql .= " AND commission.refund_status != 'requested'";
		$sql .= ' AND `is_withdrawable` = 1 AND `is_auto_withdrawal` = 0 AND `is_refunded` = 0 AND `is_trashed` = 0';
		if( $withdrawal_thresold ) $sql .= " AND commission.created <= NOW() - INTERVAL {$withdrawal_thresold} DAY";
		if( $start_date && $end_date ) {
			$sql .= " AND DATE( commission.created ) BETWEEN '" . $start_date . "' AND '" . $end_date . "'";
		}
		
		$filtered_withdrawal_count = $wpdb->get_var( $wpdb->prepare( $sql, $this->vendor_id ) );
		if( !$filtered_withdrawal_count ) $filtered_withdrawal_count = 0;

		$sql = 'SELECT * FROM ' . $wpdb->prefix . 'wcfm_marketplace_orders AS commission';
		$sql .= ' WHERE 1=1';
		$sql .= " AND `vendor_id` = %d";
		$sql .= apply_filters( 'wcfm_order_status_condition', '', 'commission' );
		$sql .= " AND commission.withdraw_status IN ('pending', 'cancelled')";
		$sql .= " AND commission.refund_status != 'requested'";
		$sql .= ' AND `is_withdrawable` = 1 AND `is_auto_withdrawal` = 0 AND `is_refunded` = 0 AND `is_trashed` = 0';
		if( $withdrawal_thresold ) $sql .= " AND commission.created <= NOW() - INTERVAL {$withdrawal_thresold} DAY";
		if( $start_date && $end_date ) {
			$sql .= " AND DATE( commission.created ) BETWEEN '" . $start_date . "' AND '" . $end_date . "'";
		}
		$sql .= " ORDER BY `{$the_orderby}` {$the_order}";
		$sql .= " LIMIT {$length}";
		$sql .= " OFFSET {$offset}";
		
		$wcfm_withdrawals_array = $wpdb->get_results( $wpdb->prepare( $sql, $this->vendor_id ) );
		
		// Generate Withdrawals JSON
		$wcfm_withdrawals_json = '';
		$wcfm_withdrawals_json = '{
															"draw": ' . wc_clean($_POST['draw']) . ',
															"recordsTotal": ' . $filtered_withdrawal_count . ',
															"recordsFiltered": ' . $filtered_withdrawal_count . ',
															"data": ';
		if(!empty($wcfm_withdrawals_array)) {
			$index = 0;
			$wcfm_withdrawals_json_arr = array();
			foreach($wcfm_withdrawals_array as $wcfm_withdrawals_single) {
				$order_id = $wcfm_withdrawals_single->order_id;
				
				$order = wc_get_order( $order_id );
				$args= array(
					'ex_tax_label'       => false,
					'currency'           => '',
					'decimal_separator'  => wc_get_price_decimal_separator(),
					'thousand_separator' => wc_get_price_thousand_separator(),
					'decimals'           => wc_get_price_decimals(),
					'price_format'       => get_woocommerce_price_format(),
				);
				if( !is_a( $order , 'WC_Order' ) ) continue;
				
				try {
				  $line_item = new WC_Order_Item_Product( absint( $wcfm_withdrawals_single->item_id ) );
				  
				  // Refunded Items Skipping
				  if( $refunded_qty = $order->get_qty_refunded_for_item( absint( $wcfm_withdrawals_single->item_id ) ) ) {
				  	$refunded_qty = $refunded_qty * -1;
				  	if( $line_item->get_quantity() == $refunded_qty ) {
				  		continue;
				  	}
				  }
				}  catch (Exception $e) {
					continue;
				}
				
				if( apply_filters( 'wcfm_is_show_commission_restrict_check', false, $order_id, $wcfm_withdrawals_single ) ) continue;
				
				// Status
				if( $withdrawal_mode == 'by_manual' ) {
					$wcfm_withdrawals_json_arr[$index][] =  '<input name="commissions[]" value="' . $wcfm_withdrawals_single->ID . '" class="wcfm-checkbox select_withdrawal" type="checkbox" >';
				} else {
					$wcfm_withdrawals_json_arr[$index][] =  '&ndash;';
				}
				
				// Order ID
				$wcfm_withdrawals_json_arr[$index][] = apply_filters( 'wcfm_commission_order_label_display', '<a class="wcfm_dashboard_item_title withdrawal_order_ids" target="_blank" href="'. get_wcfm_view_order_url( $order_id ) .'"># ' . wcfm_get_order_number( $order_id ) . '</a>', $order_id, $wcfm_withdrawals_single );
				//$order = wc_get_order($order_id);
				$order_currency = $order->get_currency();
				//var_dump($order_currency);
				$decimals = scd_options_get_decimal_precision();
				$args['currency'] = $user_curr;
				$args['decimals'] = $decimals;
		       	$args['price_format'] = scd_change_currency_display_format ($args['price_format'], $user_curr);
				// Commission ID
				$wcfm_withdrawals_json_arr[$index][] = '<span class="wcfm_dashboard_item_title"># ' . $wcfm_withdrawals_single->ID . '</span>'; 
				
				// My Earnings
				$total_commission = scd_function_convert_subtotal($wcfm_withdrawals_single->total_commission, $order_currency, $user_curr, $decimals);
				$wcfm_withdrawals_json_arr[$index][] = scd_format_converted_price_to_html( $total_commission, $args );  
				
				// Charges
				$withdraw_charges = scd_function_convert_subtotal($wcfm_withdrawals_single->withdraw_charges, $order_currency, $user_curr, $decimals);
				$wcfm_withdrawals_json_arr[$index][] = scd_format_converted_price_to_html( $withdraw_charges, $args );  
				
				// Payment
				$payment = $total_commission - $withdraw_charges;
				$wcfm_withdrawals_json_arr[$index][] = scd_format_converted_price_to_html( $payment, $args );   
				
				// Additional Info
				$wcfm_withdrawals_json_arr[$index][] = apply_filters( 'wcfm_withdrawal_additonal_data', '&ndash;', $wcfm_withdrawals_single->ID, $wcfm_withdrawals_single->order_id, $wcfm_withdrawals_single->vendor_id );
				
				// Date
				$wcfm_withdrawals_json_arr[$index][] = apply_filters( 'wcfm_commission_date_display', date_i18n( wc_date_format() . ' ' . wc_time_format(), strtotime( $wcfm_withdrawals_single->created ) ), $order_id, $wcfm_withdrawals_single );
				
				$index++;
			}												
		}
		if( !empty($wcfm_withdrawals_json_arr) ) $wcfm_withdrawals_json .= json_encode($wcfm_withdrawals_json_arr);
		else $wcfm_withdrawals_json .= '[]';
		$wcfm_withdrawals_json .= '
													}';
													
		echo $wcfm_withdrawals_json;
	}
}