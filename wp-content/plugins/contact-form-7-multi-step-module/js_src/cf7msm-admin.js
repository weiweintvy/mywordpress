import '../scss/cf7msm-admin.scss';

(function($) {
    var upgrade_div = $("#upgradediv");
    $("#postbox-container-1").append( upgrade_div.removeClass("hide") );

    var handler = FS.Checkout.configure({
        plugin_id:  '1614',
        plan_id:    '2334',
        public_key: 'pk_b445061ad8b540f6a89c2c4f4df19',
        image:      cf7msm_admin_panel.url + '/resources/logo-square-100-white.png'
    });
    
    $('.cf7msm-freemius-purchase').on('click', function (e) {
        handler.open({
            name     : 'Contact Form 7 Multi-Step Forms',
            licenses : 1,
            // You can consume the response for after purchase logic.
            success  : function (response) {
                // alert(response.user.email);
            }
        });
        e.preventDefault();
    });    
})(jQuery);