<?php

/* ------------------------------------------------------------------------
   This module handles the operations of the install scm License Manager tab
   ------------------------------------------------------------------------ */ 

/**
 * Render setting: License Key
 */ 
function scd_form_render_license() {

    $key = get_option('scd_license_key');
    
    ?>
    
    <input id="scd_license" name='scd_license_manager[scd_license_key]' style="margin-left: 20px; margin-top: 3px; width:300px" type='text' value='<?php echo base64_decode($key); ?>' readonly/>

    <?php
}

/**
 * Render setting: License Expiry Date
 */ 
function scd_form_render_license_expiry() {

    $expiry = get_option('scd_license_expiry_date');

    ?>
    
    <input id="scd_license_expiry" name='scd_license_manager[scd_license_expiry]' style="margin-left: 20px; margin-top: 3px; width:300px" type='text' value='<?php echo base64_decode($expiry); ?>' readonly/>

    <?php
    if(!empty($expiry)){
        $todaydate = new DateTime(date('Y-m-d'));
        $enddate = new DateTime(base64_decode($expiry));
        if($todaydate < $enddate) {
            $daysLeft = $todaydate->diff($enddate)->days;
        } else {
            $daysLeft = 0;
        }
    ?>

        <div class="scd-pp" style="margin-left: 20px; margin-top: 10px">
            <p> <?php echo __( 'License validity time left : ', 'scd_wcfm_marketplace' )  ?> <strong><?php echo $daysLeft ?> <?php echo __( ' days ', 'scd_wcfm_marketplace' )  ?> </strong><p>
        </div>

        <?php
            if($daysLeft >= 0 && $daysLeft<31){
                if($daysLeft == 0){
                    $msg = __( 'Your license has expired.', 'scd_wcfm_marketplace' );
                }
                else{
                    $msg = __( 'Your license will expire in ', 'scd_wcfm_marketplace' ) .$daysLeft. __( ' days.', 'scd_wcfm_marketplace' );
                }
                ?>
                <div class="scd-notice" style="margin-left: 20px; margin-top: 10px">
                    <p> <?php echo $msg ?>
                    <?php echo __(' Please ', 'scd_wcfm_marketplace' )  ?> <a href="https://gajelabs.com/product/scd/" target="_blank"> <?php  echo __( ' update your license ', 'scd_wcfm_marketplace' )  ?> </a> <?php echo __( ' to continue to benefit install scm features. ', 'scd_wcfm_marketplace' )  ?> </p>
                </div>
                <?php
            }
        ?>

    <?php
    }
    ?>

    <?php
}




   ?>
