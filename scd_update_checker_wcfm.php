<?php

/* ------------------------------------------------------------------------
   This module handles the verification and uploading and SCM updates
   ------------------------------------------------------------------------ */

require_once 'plugin-update-checker/plugin-update-checker.php';
// Note : To avoid name conflicts (observed on a customer site) , 
//        the class Puc_v4_Factory has been renamed SCD_Puc_v4_Factory 
//        in the plugin-update-checker module

$myUpdateChecker = SCD_Puc_v4_Factory::buildUpdateChecker(
	'https://bitbucket.org/Gonima/scda/',
	plugin_dir_path(__FILE__)."index.php",
	'scd-smart-currency-detector'
);

//Optional: If you're using a private repository, create an OAuth consumer
//and set the authentication credentials like this:
//Note: For now you need to check "This is a private consumer" when
//creating the consumer to work around #134:
// https://github.com/YahnisElsts/plugin-update-checker/issues/134
$myUpdateChecker->setAuthentication(array(
	'consumer_key' => 'eELpeQWrm4Zk3S2Kq3',
	'consumer_secret' => 'BwFezGdy4HWmaEE5m8Rj936XW4yrtGRU',
));

// Set the branch that contains the stable release.
$myUpdateChecker->setBranch('wcfm');

?>