<?php

/**
 * Runs only when the scd free not exist.
 *
 * @since 4.7.15
 */


if (!defined('ABSPATH')) {
    exit;
}



 add_action('admin_init', 'scd_free_dashboard_setup');

function scd_free_dashboard_setup()
{
    $pathscdfree = ABSPATH.'wp-content/plugins/scd-smart-currency-detector/index.php'; 
    if (!file_exists($pathscdfree)) {
		
    	if ( isset($_POST['scd_free_install']) ) {
			install_scd_free();
		}
		
	    if ( isset($_POST['scd_free_retry']) ) {
			install_scd_free_view();
			exit();
		}
		
		if ( isset($_POST['scd_free_cancel']) ) {
						
			cancel_scd_free();
			
		}
			install_scd_free_view();
			exit();
	
    }
}

    /**
     * Content for install scd_free view.
     */


    function install_scd_free_view()
    {
        global $SCD;
        $scd_free_icon = plugins_url('images/scd_free_icon.jpg', __FILE__);
        set_current_screen(); ?>
            <!DOCTYPE html>
            <html <?php language_attributes(); ?>>
                    <head>
                            <meta name="viewport" content="width=device-width" />
                            <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
                            <title><?php esc_html_e('SCM &rsaquo; Setup Wizard', 'scd_wcfm_marketplace'); ?></title>
                            <?php do_action('admin_print_styles'); ?>
                            <?php do_action('admin_head'); ?>
                            <style type="text/css">
                                    body {
                                            margin: 100px auto 24px;
                                            box-shadow: none;
                                            background: #f1f1f1;
                                            padding: 0;
                                            max-width: 700px;
                                    }
                                    #wc-logo {
                                            border: 0;
                                            margin: 0 0 24px;
                                            padding: 0;
                                            text-align: center;
                                    }
                                    #wc-logo a {
                                        color: #00897b;
                                        text-decoration: none;
                                    }

                                    #wc-logo a span {
                                        padding-left: 10px;
                                        padding-top: 23px;
                                        display: inline-block;
                                        vertical-align: top;
                                        font-weight: 700;
                                    }
                                    .scd-install-scdFree {
                                            box-shadow: 0 1px 3px rgba(0,0,0,.13);
                                            padding: 24px 24px 0;
                                            margin: 0 0 20px;
                                            background: #fff;
                                            overflow: hidden;
                                            zoom: 1;
                                    }
                                    .scd-install-scdFree .button-primary{
                                            font-size: 1.25em;
                                            padding: .5em 1em;
                                            line-height: 1em;
                                            margin-right: .5em;
                                            margin-bottom: 2px;
                                            height: auto;
                                    }
                                    .scd-install-scdFree{
                                            font-family: sans-serif;
                                            text-align: center;
                                    }
                                    .scd-install-scdFree form .button-primary{
                                            color: #fff;
                                            background-color: #00798b;
                                            font-size: 16px;
                                            border: 1px solid #00798b;
                                            width: 280px;
                                            padding: 10px;
                                            margin: 25px 0 20px;
                                            cursor: pointer;
                                    }
                                    .scd-install-scdFree form .button-primary:hover{
                                            background-color: #000000;
                                    }
                                    .scd-install-scdFree p{
                                            line-height: 1.6;
                                    }

                            </style>
                    </head>
                    <body class="scd-setup wp-core-ui">
                            <h1 id="wc-logo"><a href="http://gajelabs.com/"><img src="<?php echo $scd_free_icon; ?>" alt="SCM" /><span> <?php __( 'SMART CURRENCY MANAGER', 'scd_wcfm_marketplace' ); ?> </span></a></h1>
                            <div class="scd-install-scdFree">
                                    <p><?php  __( 'This variant need', 'scd_wcfm_marketplace' ) . '<strong style="color:red; font-weight: bold;">' . __( ' Smart Currency Manager ', 'scd_wcfm_marketplace' ) . '</strong>' . __( 'to active.', 'scd_wcfm_marketplace'); ?></p>
                                    <form method="post" action="" name="scd_install_scdFree">
                                            <?php submit_button(__('Install SCM Free', 'scd_wcfm_marketplace'), 'primary', 'scd_free_install'); ?>
                                            <?php wp_nonce_field('scdfree-install'); ?>
                                    </form>
                            </div>
                    </body>
            </html>
            <?php
    }

    /**
     * Install scd free if not exist.
     *
     * @throws Exception
     */


    function install_scd_free()
    {
        check_admin_referer('scdfree-install');
		include_once( ABSPATH . 'wp-admin/includes/file.php' );
		include_once( ABSPATH . 'wp-admin/includes/plugin-install.php' );
		include_once( ABSPATH . 'wp-admin/includes/class-wp-upgrader.php' );
		include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
        // var_dump(array_keys(get_plugins()));
        WP_Filesystem();
        $skin = new Automatic_Upgrader_Skin();
        $upgrader = new WP_Upgrader($skin);
        $installed_plugins = array_map('format_plugin_slug', array_keys(get_plugins()));
        $plugin_slug = 'scd-smart-currency-detector';
        $plugin = 'scd-smart-currency-detector/index.php';
        $installed = false;
        $activate = false;
        // See if the plugin is installed already
        if (in_array($plugin_slug, $installed_plugins)) {
            $installed = true;
            $activate = !is_plugin_active($plugin);
        }
        // Install this thing!
        if (!$installed) {
            // Suppress feedback
            ob_start();

            try {

                $package = "https://downloads.wordpress.org/plugin/scd-smart-currency-detector.zip";
						
				
                $download = $upgrader->download_package($package);

                if (is_wp_error($download)) {
                    throw new Exception($download->get_error_message());
                }
				
               
                $working_dir = $upgrader->unpack_package($download, true);
			

                if (is_wp_error($working_dir)) {
                    throw new Exception($working_dir->get_error_message());
                }

                $result = $upgrader->install_package(array(
                    'source' => $working_dir,
                    'destination' => WP_PLUGIN_DIR,
                    'clear_destination' => false,
                    'abort_if_destination_exists' => false,
                    'clear_working' => true,
                    'hook_extra' => array(
                            'type' => 'plugin',
                            'action' => 'install',
                    ),
            ));

                if (is_wp_error($result)) {
                    throw new Exception($result->get_error_message());
                }

                $activate = true;
				
            } catch (Exception $e) {
        global $SCD;
        $scd_free_icon = plugins_url('images/scd_free_icon.jpg', __FILE__);
        set_current_screen(); ?>
            <!DOCTYPE html>
            <html <?php language_attributes(); ?>>
                    <head>
                            <meta name="viewport" content="width=device-width" />
                            <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
                            <title><?php esc_html_e('SCM &rsaquo; Setup Wizard', 'scd_wcfm_marketplace'); ?></title>
                            <?php do_action('admin_print_styles'); ?>
                            <?php do_action('admin_head'); ?>
                            <style type="text/css">
                                    body {
                                            margin: 100px auto 24px;
                                            box-shadow: none;
                                            background: #f1f1f1;
                                            padding: 0;
                                            max-width: 700px;
                                    }
                                    #wc-logo {
                                            border: 0;
                                            margin: 0 0 24px;
                                            padding: 0;
                                            text-align: center;
                                    }
                                    #wc-logo a {
                                        color: #00897b;
                                        text-decoration: none;
                                    }

                                    #wc-logo a span {
                                        padding-left: 10px;
                                        padding-top: 23px;
                                        display: inline-block;
                                        vertical-align: top;
                                        font-weight: 700;
                                    }
                                    .scd-install-scdFree {
                                            box-shadow: 0 1px 3px rgba(0,0,0,.13);
                                            padding: 24px 24px 0;
                                            margin: 0 0 20px;
                                            background: #fff;
                                            overflow: hidden;
                                            zoom: 1;
                                    }
                                    .scd-install-scdFree .button-primary{
                                            font-size: 1.25em;
                                            padding: .5em 1em;
                                            line-height: 1em;
                                            margin-right: .5em;
                                            margin-bottom: 2px;
                                            height: auto;
                                    }
                                    .scd-install-scdFree{
                                            font-family: sans-serif;
                                            text-align: center;
                                    }
                                    .scd-install-scdFree form .button-primary{
                                            color: #fff;
                                            background-color: #00798b;
                                            font-size: 16px;
                                            border: 1px solid #00798b;
                                            width: 280px;
                                            padding: 10px;
                                            margin: 25px 0 20px;
                                            cursor: pointer;
                                    }
                                    .scd-install-scdFree form .button-primary:hover{
                                            background-color: #000000;
                                    }
                                    .scd-install-scdFree p{
                                            line-height: 1.6;
                                    }

                            </style>
                    </head>
                    <body class="scd-setup wp-core-ui">
                            <h1 id="wc-logo"><a href="http://gajelabs.com/"><img src="<?php echo $scd_free_icon; ?>" alt="SCM" /><span> <?php __( 'SMART CURRENCY MANAGER', 'scd_wcfm_marketplace' ); ?> </span></a></h1>
                            <div class="scd-install-scdFree">
							        <p><?php echo _e('Your installation was not successful, please check that you have a good internet connection. You can retry the installation or cancel. Before canceling the installation you can download the plugin', 'scd_wcfm_marketplace' ) . '<a href="https://downloads.wordpress.org/plugin/scd-smart-currency-detector.zip">' . __( ' by clicking here ', 'scd_wcfm_marketplace' ) . '</a>' .  __( ' and install it manually.', 'scd_wcfm_marketplace' ); ?></p>
									<p><strong style="color:red; font-weight: bold;"><?php echo _e('  Note that!!! it is mandatory to have the SCM Free plugin activated before using this variant of the SCM plugin. ', 'scd_wcfm_marketplace' ); ?></strong>'</p>
                                    <form method="post" action="" name="scd_install_scdFree">
									        <?php submit_button( __('retry to install scm Free', 'scd_wcfm_marketplace'), 'primary', 'scd_free_retry'); ?>
											<?php submit_button( __('Cancel installation', 'scd_wcfm_marketplace'), 'primary', 'scd_free_cancel'); ?>
											<?php wp_nonce_field('scdfree-install'); ?>
                                    </form>
                            </div>
                    </body>
            </html>
            <?php
                exit();
            }

            // Discard feedback
            ob_end_clean();
        }

        wp_clean_plugins_cache();
        // Activate this thing
        if ($activate) {
            try {
                $result = activate_plugin($plugin);

                if (is_wp_error($result)) {
                    throw new Exception($result->get_error_message());
                }
            } catch (Exception $e) {
       
            }
        } 
        wp_safe_redirect(admin_url('plugins.php'));
    }
	
	function cancel_scd_free(){
		
		  check_admin_referer('scdfree-install');
		
		  require_once( ABSPATH . 'wp-admin/includes/plugin.php' );
		  
          deactivate_plugins( '/scd-smart-currency-detector-variant-for-wcfm/index.php', true);
		
		wp_safe_redirect(admin_url('plugins.php'));
	}

    function format_plugin_slug($key)
    {
        $slug = explode('/', $key);
        $slug = explode('.', end($slug));

        return $slug[0];
    }

