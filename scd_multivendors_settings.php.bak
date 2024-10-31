<?php

include_once "scd_license_manager_form.php";
include_once 'scd_settings_form.php';

$scd_license_file = "../scd.lic";
$scd_license_duration = 365;

/**
 * Get the default settings values for a setting group
 * 
 * @param int $opgroup  The setting group
 * 
 * @return array The defaulkt settings values
 */


/**
 * Initialize the settings values for a setting group
 * 
 * @param int $opgroup  The setting group
 */


add_filter('scd-settings-groups','scd_premium_settings_groups');
function scd_premium_settings_groups($grps) {
    $grps['scd_license_section']=array('id'=>'four','title'=>'LICENSE INFORMATION','callback'=>'scd_options_callback','page'=>'scd_license_options','group'=>'scd_license_options','sanitize'=>'scd_sanitize_license_settings');

    return $grps;
}

add_filter('scd-options-fields','scd_premium_options_fields');
function scd_premium_options_fields($fields) {
unset($fields['siderbarcurrencies']);
$fields = array_merge($fields);
$fields['scd_license_key'] =array('title'=>'License Key','callback'=>'scd_form_render_license','page'=>'scd_license_options','section'=>'scd_license_section');    
$fields['scd_license_expiry'] =array('title'=>'License Expiry Date','callback'=>'scd_form_render_license_expiry','page'=>'scd_license_options','section'=>'scd_license_section');

$fields['pricesInVendorCurrency'] =array('title'=>'Display product prices in vendor currency','callback'=>'scd_render_pricesInVendorCurrency','page'=>'scd_currency_options','section'=>'scd_currency_section');


return $fields;
}

add_filter('scd_init_currency_options','scd_init_currency_options_func',10,1);
function scd_init_currency_options_func($options) {
	unset($options['siderbarcurrencies']);
    $options = array_merge($options);
    $options['pricesInVendorCurrency']="";
    return $options;
}

function scd_sanitize_license_settings($input) {
      $default_values = scd_get_default_settings_values('scd_license_options');
    return scd_fill_unset_settings ($input, array_keys($default_values));
}

function is_scd_pro() {
    return (file_exists(plugin_dir_path( __FILE__ ).'scd_update_checker_pro.php'));
}
