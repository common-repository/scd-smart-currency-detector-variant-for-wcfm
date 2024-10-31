<?php

/* ------------------------------------------------------------------------
   This module handles the operations of the SCM License Manager tab
   ------------------------------------------------------------------------ */ 

/**
 * Render setting: License Key
 */ 
function scd_render_pricesInVendorCurrency(){
    $options=  get_option('scd_currency_options',true);
    $checked=  isset($options['pricesInVendorCurrency'])&& !empty($options['pricesInVendorCurrency'])?'checked':false;
    $checked= $checked?"checked":"";  
    //var_dump($options);
    
echo '<input type="checkbox" value="1" name="scd_currency_options[pricesInVendorCurrency]" '.$checked.'  />';
}
   ?>
