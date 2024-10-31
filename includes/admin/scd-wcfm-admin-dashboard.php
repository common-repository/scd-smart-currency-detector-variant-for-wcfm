<?php
add_action('wcfm_after_dashboard_stats_box', 'scd_wcfm_after_dashboard_stats_box');

function scd_wcfm_after_dashboard_stats_box() {
    if (scd_get_user_role() != 'administrator') {
        return;
    }
    $commission = scd_wcfm_get_commission_by_vendor();
    if (!$commission) $commission = 0;
    ?>
    <script type="text/javascript">
        jQuery(document).ready(function () {
            jQuery(".wcfm_dashboard_stats_block a div strong:eq(1)").html('<?php echo wc_price($commission); ?>');
        })
    </script>
    <?php
}
