
jQuery(document).ready(function () {
jQuery('#scd_activate_new').click(function (e) {
        e.preventDefault();
        jQuery('#scd-form-license').empty(); 
        var form = '<h2>PLEASE ACTIVATE YOUR SCM PLUGIN LICENSE.</h2>\n\
<p>Please enter the SCM license key to activate it. You were given a license key when you purchased this item.</p><p>If you did not receive a license key in your invoice email or you need help to activate it, feel free to contact us. You can buy a license key on '
        +'<a href="https://gajelabs.com/product/scd/" onclick="window.open(this.href); return false;"> gajelabs.com </a> </p>'
        +'<form action="" method="post">'
        +'<table class="form-table"><tr>'

            +'    <th style="width:100px;"><label for="scd_license_key">License Key</label></th>'
            +'    <td ><input class="regular-text" type="text" id="scd_license_key" name="scd_license_key"  value="" ></td>'
            +'</tr></table>'
        +'<p class="submit">'
            +'<input type="submit" name="activate_license" value="Activate" class="button-primary" />'
        +'</p>'
    +'</form>';
       jQuery('#scd-form-license').html(form);
    });
});

