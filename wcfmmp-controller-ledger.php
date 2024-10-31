<?php
/**
 * WCFM plugin controllers
 *
 * Plugin WCFM Marketplace Ledger Dashboard Controller
 *
 * @author 		WC Lovers
 * @package 	wcfm/ledger/wcfmmp/controllers
 * @version   1.0.0
 */

class SCD_WCFMmp_Ledger_Controller {
	
	public function __construct() {
		global $WCFM;
		
		$this->processing();
	}
	    public function separeNumeric($text) {
        $nums="";
        if ($text!=null&&strlen($text)>0) {
            for ($i=0;$i<strlen($text);$i++) {
                $c=$text[$i];
                if(is_numeric($c)) {
                    $nums.= $c;
            }
        }
        return $nums;
    		}
		}
	public function processing() {
		global $WCFM, $wpdb, $_POST, $WCFMmp, $wp, $woocommerce, $post ;
		
		//var_dump($order);
		
		$vendor_id = $WCFMmp->vendor_id;
		$user_curr= scd_get_user_currency();
		//var_dump($user_curr);

		$length = absint( $_POST['length'] );
		$offset = absint( $_POST['start'] );
		
		$the_orderby = ! empty( $_POST['orderby'] ) ? sanitize_sql_orderby( $_POST['orderby'] ) : 'ID';
		$the_order   = ( ! empty( $_POST['order'] ) && 'asc' === $_POST['order'] ) ? 'ASC' : 'DESC';
		
    $status_filter = '';
    if( isset($_POST['status_type']) && ( $_POST['status_type'] != '' ) ) {
    		$status_filter = $status_filter = ' AND `reference_status` = "' . sanitize_text_field( $_POST['status_type'] ) . '"';
    }
    
    $type_filter = '';
    if( isset($_POST['type']) && ( $_POST['type'] != '' ) ) {
    		$type_filter = ' AND `reference` = "' . sanitize_text_field( $_POST['type'] ) . '"';
    }
    
		$sql = "SELECT COUNT(ID) from {$wpdb->prefix}wcfm_marketplace_vendor_ledger";
		$sql .= " WHERE 1=1";
		$sql .= " AND `vendor_id` = %d";
		$sql .= $status_filter;
		$sql .= $type_filter;
		
  		$wcfm_ledger_items = $wpdb->get_var( $wpdb->prepare($sql, $vendor_id) );
		//var_dump($wcfm_ledger_items);
		if( !$wcfm_ledger_items ) $wcfm_ledger_items = 0;
		
		$sql = "SELECT * from {$wpdb->prefix}wcfm_marketplace_vendor_ledger";
		$sql .= " WHERE 1=1";
		$sql .= " AND `vendor_id` = %d";
		$sql .= $status_filter;
		$sql .= $type_filter;
		$sql .= " ORDER BY `{$the_orderby}` {$the_order}";
		$sql .= " LIMIT {$length}";
		$sql .= " OFFSET {$offset}";
		
		$wcfm_ledger_array = $wpdb->get_results( $wpdb->prepare($sql, $vendor_id) );
		//var_dump($wcfm_ledger_array);
		// Generate Ledger JSON
		$wcfm_ledger_json = '';
		$wcfm_ledger_json = '{
															"draw": ' . absint( $_POST['draw'] ) . ',
															"recordsTotal": ' . $wcfm_ledger_items . ',
															"recordsFiltered": ' . $wcfm_ledger_items . ',
															"data": ';
		if(!empty($wcfm_ledger_array)) {
			$index = 0;
			$wcfm_ledger_json_arr = array();
			foreach( $wcfm_ledger_array as $wcfm_ledger_single ) {
				$args= array(
					'ex_tax_label'       => false,
					'currency'           => '',
					'decimal_separator'  => wc_get_price_decimal_separator(),
					'thousand_separator' => wc_get_price_thousand_separator(),
					'decimals'           => wc_get_price_decimals(),
					'price_format'       => get_woocommerce_price_format(),
				);
				$reference = $wcfm_ledger_single->reference_details;
				$haystack = $reference;
				$needle   = '#';
				$need = ') ';

				$pos      = strripos($haystack, $needle);
				$posi = strripos($haystack, $need);
				$order_id = '';

				// Status
				$wcfm_ledger_json_arr[$index][] =  '<span class="order-status tips wcicon-status-' . sanitize_title( $wcfm_ledger_single->reference_status ) . ' text_tip" data-tip="' . $WCFMmp->wcfmmp_vendor->wcfmmp_vendor_order_status_name( $wcfm_ledger_single->reference_status ) . '"></span>';
				
				// Type
				$wcfm_ledger_json_arr[$index][] = '<div class="wcfmmp-ledger-type wcfmmp-ledger-type-' . $wcfm_ledger_single->reference . '">' . $WCFMmp->wcfmmp_ledger->wcfmmp_vendor_ledger_reference_name( $wcfm_ledger_single->reference ) . '</div>';
				
        // Details
        $wcfm_ledger_json_arr[$index][] = $wcfm_ledger_single->reference_details;
        
        // Credit
        if( $wcfm_ledger_single->credit ) {	
				if ($pos === false) {
					$order_id = '';
				}else{			
					$order_id = substr($haystack, $pos+1, strlen($haystack)-1);
					$order_id = $this->separeNumeric($order_id);
					$order = wc_get_order($order_id);
					$order_currency = $order->get_currency();					
				//var_dump($order_currency);					
					$decimals = scd_options_get_decimal_precision();
					$credit = scd_function_convert_subtotal($wcfm_ledger_single->credit, $order_currency, $user_curr, $decimals);
					$args['currency'] = $user_curr;
					$args['decimals'] = $decimals;
			       	$args['price_format'] = scd_change_currency_display_format ($args['price_format'], $user_curr);
			
        	$wcfm_ledger_json_arr[$index][] = '<div class="wcfmmp-ledger-credit">' . scd_format_converted_price_to_html( $credit, $args ) . '</div>';
			//echo ($wcfm_ledger_single->credit);
       }
				} else {
        	$wcfm_ledger_json_arr[$index][] = '';
        }
        
        // Debit
        if( $wcfm_ledger_single->debit ) {
			if ($posi === false) {
				$order_id = '';
			}else{
					$order_id = substr($haystack, $posi+1, strlen($haystack)-1);
					$order_id = $this->separeNumeric($order_id);
					$order = wc_get_order($order_id);
					$order_currency = $order->get_currency();
					$decimals = scd_options_get_decimal_precision();
					$debit = scd_function_convert_subtotal($wcfm_ledger_single->debit, $order_currency, $user_curr, $decimals);
					$args['currency'] = $user_curr;
					$args['decimals'] = $decimals;
			        $args['price_format'] = scd_change_currency_display_format ($args['price_format'], $user_curr);
        	$wcfm_ledger_json_arr[$index][] = '<div class="wcfmmp-ledger-debit">' . scd_format_converted_price_to_html( $debit, $args ) . '</div>';
			}
        		} else {
        	$wcfm_ledger_json_arr[$index][] = '';
        }
        
        // Dated
        $wcfm_ledger_json_arr[$index][] = date_i18n( wc_date_format() . ' ' . wc_time_format(), strtotime($wcfm_ledger_single->created) );
        
				$index++;
			}												
		}
		if( !empty($wcfm_ledger_json_arr) ) $wcfm_ledger_json .= json_encode($wcfm_ledger_json_arr);
		else $wcfm_ledger_json .= '[]';
		$wcfm_ledger_json .= '
													}';
													
		echo $wcfm_ledger_json;
	}
}