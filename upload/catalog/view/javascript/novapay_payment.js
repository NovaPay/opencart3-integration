// Novapay Payment Module JavaScript
(function($) {
    'use strict';

    var Novapay_payment = {
        init: function() {
            this.bindEvents();
            console.log('Novapay Payment module initialized');
        },

        bindEvents: function() {
            $('.novapay_payment-module').on('click', 'button', this.handleClick.bind(this));
            $('.novapay_payment-module').on('change', 'input, select', this.handleChange.bind(this));
        },

        handleClick: function(e) {
            e.preventDefault();
            var $target = $(e.currentTarget);
            console.log('Novapay Payment button clicked:', $target);
        },

        handleChange: function(e) {
            var $target = $(e.currentTarget);
            var value = $target.val();
            console.log('Novapay Payment input changed:', value);
        },

        ajax: function(url, data, callback) {
            $.ajax({
                url: url,
                type: 'POST',
                data: data,
                dataType: 'json',
                success: function(response) {
                    if (callback && typeof callback === 'function') {
                        callback(response);
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Novapay Payment AJAX error:', error);
                }
            });
        }
    };

    $(document).ready(function() {
        Novapay_payment.init();
    });

    window.Novapay_payment = Novapay_payment;

})(jQuery);