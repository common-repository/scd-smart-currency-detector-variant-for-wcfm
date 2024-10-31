<?php
/**
 * Shortcode : Vertical Currencies with flag display
 */

		$gt = get_option('scd_currency_options');
		$ts = $gt['targetSession'];
	
?>
		<form method="POST">
			<!--<div class="scd_error" style="background-color: #E12D2D;color: #fff; font-weight: 600;"></div>-->
		
			<input type="hidden" name='targetSessionName' id="targetSessionName" value="<?php echo $ts;?>"/>
			<!--<div id="endwid" style="border: solid 1px #CCC;width: 100%;margin-bottom: 10px;">
				<div id="dvFlag" class=""></div>
				<select id='ch_scd_woo_widget_select' name='ch_scd_woo_widget_select_name' style="max-width: 100%;width: 92.5%;height: 20px;border: none;" >

				</select>
			</div>-->
			<div id="endwid">
				<input id="scd_widget_selector" type="text" readonly="readonly">
				<label for="scd_widget_selector" style="display:none;"> <?php  __( 'Select a country here...', 'scd_wcfm_marketplace' )  ?></label>
			</div>
			<!--<input type="submit" value="Change" id="scd_widget_upd" style="float: right;width: 100%;">-->
			<!--<button id="scd_widget_upd"><?php //_e('Change', 'ch_scd_woo');?></button>-->
			
		</form>
	