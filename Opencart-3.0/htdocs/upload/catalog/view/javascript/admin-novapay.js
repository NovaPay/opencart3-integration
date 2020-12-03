jQuery(document).ready(function() {
    var public = jQuery('[name="payment_novapay_publickey"]');
    var private = jQuery('[name="payment_novapay_privatekey"]');
    var pass = jQuery('[name="payment_novapay_passprivate"]');
    if (public.val() !== '' && public.val() !== '******************') {
        jQuery.cookie('public', public.val());
        public.val('******************');
    }
    if (private.val() !== '' && private.val() !== '******************') {
        jQuery.cookie('private', private.val());
        private.val('******************');
    }
    if (pass.val() !== '' && pass.val() !== '******************') {
        jQuery.cookie('pass', pass.val());
        pass.val('******************');
    }

    jQuery('.pull-right').css('position', 'relative');
    jQuery('.pull-right').append('<div class="novapay-temp-blc" style="position: absolute; left: 0; top: 0; width: 39px; height: 36px; cursor: pointer"></div>');

    jQuery('body').on('click', '.novapay-temp-blc', function () {
        if(jQuery('[name="payment_novapay_publickey"]').val() === '******************') {
            jQuery('[name="payment_novapay_publickey"]').val(jQuery.cookie('public'));
            jQuery.cookie('public', '');
        }
        if(jQuery('[name="payment_novapay_privatekey"]').val() === '******************') {
            jQuery('[name="payment_novapay_privatekey"]').val(jQuery.cookie('private'));
            jQuery.cookie('private', '');
        }
        if(jQuery('[name="payment_novapay_passprivate"]').val() === '******************') {
            jQuery('[name="payment_novapay_passprivate"]').val(jQuery.cookie('pass'));
            jQuery.cookie('pass', '');
        }
        jQuery('.pull-right button:first').click();
        jQuery('.panel-body').hide();
    });
});
