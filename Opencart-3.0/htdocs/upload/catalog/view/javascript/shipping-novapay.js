(function ($, window, document) {
    function disableShippingMethodButton() {
        $('#button-shipping-method').css({opacity: '.5', 'pointer-events': 'none'});
    }

    function enableShippingMethodButton() {
        if($('#button-shipping-method').attr('data-error') === undefined) {
            $('#button-shipping-method').removeAttr('style');
        }
    }

    function disableCityInput() {
        $('.novapay-search-city').css({opacity: '.5', 'pointer-events': 'none'});
    }

    function enableCityInput() {
        $('.novapay-search-city').removeAttr('style');
    }

    function disableWarehouseInput() {
        $('.novapay-search-warehouse').css({opacity: '.5', 'pointer-events': 'none'});
    }

    function enableWarehouseInput() {
        $('.novapay-search-warehouse').removeAttr('style');
    }

    function focusCityInput() {
        $('.novapay-search-city').focus();
    }

    function focusWarehouseInput() {
        $('.novapay-search-warehouse').focus();
    }

    $(document).ready(function () {
        window.warehouse = '';
        window.city = '';

        var chooseNov = $('[value="novapay.novapay"]:checked').length > 0;

        if (chooseNov) {
            disableShippingMethodButton();
        }

        // Select a shipping method
        $('#collapse-shipping-method [type="radio"]').on('click', function () {
            if ($('[value="novapay.novapay"]:checked').length > 0) {
                // && window.warehouse == '' && window.city == ''
                // shipping method is Novapay, even with entered city and warehouse
                enableCityInput();
                enableWarehouseInput();
                if ('' == window.warehouse) {
                    disableShippingMethodButton();
                }
                if ('' == window.city) {
                    focusCityInput();
                } else if ('' == window.warehouse) {
                    focusWarehouseInput();
                }
            } else {
                disableCityInput();
                disableWarehouseInput();
                enableShippingMethodButton();
            }
        });

        $('#button-shipping-method').on('click', function () {
            setTimeout(function () {
                if ($('[value="novapay.novapay"]:checked').length > 0) {
                    $('#collapse-payment-method input').not('[value="novapay"]').parents('.radio').css({
                        opacity: '.5',
                        'pointer-events': 'none'
                    });
                    $('#collapse-payment-method [value="novapay"]').attr('checked', 'checked');

                    $.ajax({
                        url: '/index.php?route=extension/payment/novapay/shippingPlace',
                        type: 'post',
                        data: {
                            type: 'shipping',
                        },
                        dataType: 'json',
                        success: function (json) {
                            console.log(json);
                        }
                    });

                } else {
                    $('#collapse-payment-method input').removeAttr('style');
                }
            }, 1000);
        });

        $('[value="novapay.novapay"]').parents('.radio').append(
            '<div class="novapay-search"><label>' +
            $('.novapay-shipping-price').attr('data-city') +
            '</label><input type="text" class="novapay-search-city" autocomplete="none" /></div>'
        );
        if (!chooseNov) {
            disableCityInput();
        }


        $('body').on('keyup', '.novapay-search-city', function () {
            if ($(this).val().length > 1) {
                $.ajax({
                    url: '/index.php?route=extension/payment/novapay/shippingPlace',
                    type: 'post',
                    data: {
                        type: 'cities',
                        city: $(this).val()
                    },
                    dataType: 'json',
                    success: function (json) {
                        var cities = '';
                        for (var i = 0; i < json.length; i++) {
                            cities += '<li data-value="' + json[i]['Ref'] + '">' + json[i]['Description'] + '</li>';
                        }
                        $('.novapay-cities').remove();
                        if (cities.length > 0) {
                            $('.novapay-search').append('<ul class="novapay-cities">' + cities + '</ul>');
                        }
                    }
                });
            }
        });

        $('body').on('click', '.novapay-cities li', function () {
            window.city = $(this).attr('data-value');
            $('.novapay-search-city').val($(this).text());
            $('.novapay-cities').remove();
            if ($('.novapay-search-warehouse').length > 0) {
                $('.novapay-search-warehouse').val('');
            }
            getWareHouses();
        });

        function getWareHouses() {
            $.ajax({
                url: '/index.php?route=extension/payment/novapay/shippingPlace',
                type: 'post',
                data: {
                    type: 'warehouse',
                    city: window.city,
                },
                dataType: 'json',
                success: function (json) {
                    var warehouses = '';
                    window.warehouses = json;
                    for (var i = 0; i < Math.min(json.length, 20); i++) {
                        warehouses += '<li data-value="' + json[i]['ref'] + '">' + $('.novapay-shipping-price').attr('data-depart') + ' №' + json[i]['no'] + ' ' + json[i]['title'] + '</li>';
                    }
                    $('.novapay-search ul').remove();
                    if (warehouses.length > 0) {
                        if ($('.novapay-search-warehouse').length > 0) {
                            $('.novapay-search').append('<ul class="novapay-warehouse">' + warehouses + '</ul>');
                        } else {
                            $('.novapay-search').append(
                                '<label class="novapay-label-war">' +
                                $('.novapay-shipping-price').attr('data-depart') +
                                '</label><input type="text" class="novapay-search-warehouse" autocomplete="none" />'
                            );
                            $('.novapay-search').append('<ul class="novapay-warehouse">' + warehouses + '</ul>');
                        }
                    }
                },
                error: function (err) {
                    console.log(err);
                }
            });
        };

        $('body').on('keyup', '.novapay-search-warehouse', function () {
            $('.novapay-search ul').remove();

            var warehouses = '';
            var val = $(this).val().toUpperCase();
            var count = 0;
            for (var i = 0; i < window.warehouses.length; i++) {
                if ((window.warehouses[i]['title'].toUpperCase().indexOf(val) !== -1 || val.indexOf(window.warehouses[i]['title'].toUpperCase()) !== -1) && count < 20) {
                    count++;
                    warehouses += '<li data-value="' + window.warehouses[i]['ref'] + '">' + $('.novapay-shipping-price').attr('data-depart') + ' №' + window.warehouses[i]['no'] + ' ' + window.warehouses[i]['title'] + '</li>';
                }
            }
            $('.novapay-search').append('<ul class="novapay-warehouse">' + warehouses + '</ul>');
        });

        $('body').on('click', '.novapay-warehouse li', function () {
            window.warehouse = $(this).attr('data-value');
            $('.novapay-search-warehouse').val($(this).text());
            $('.novapay-warehouse li').hide();

            var admin = location.search.indexOf('order_id') !== -1;

            $.ajax({
                url: '/index.php?route=extension/payment/novapay/shippingPlace',
                type: 'post',
                data: {
                    type: 'res',
                    city_ref: window.city,
                    house_ref: window.warehouse,
                    admin: admin ? location.search.split('&')[2].split('=')[1] : ''
                },
                dataType: 'json',
                success: function (json) {
                    if (json === 'empty') {
                        alert($('.novapay-shipping-price').attr('data-absent'));
                        $('[name="shipping_method"]:eq(0)').click();
                        $('[value="novapay.novapay"]').parents('.radio').remove();
                    } else if (json.hasOwnProperty('message')) {
                        alert(json.message);
                        $('#button-shipping-method').attr('data-error', json.message);
                    } else if (json.hasOwnProperty('delivery_price')) {
                        $('#button-shipping-method').removeAttr('data-error');
                        enableShippingMethodButton();
                        if (admin) {
                            location.reload();
                        }
                        window.ship_price = json.delivery_price;
                        $('.novapay-shipping-price').text($('.novapay-shipping-price').text().substring(0, 1) + json.delivery_price);
                    } else {
                        $('#button-shipping-method').removeAttr('data-error');
                        enableShippingMethodButton();
                        //if we didn't get delivery price then session order data isn't init. Session data initing on confirm order
                        if (!$('#button-shipping-method').hasClass('fixed')) {
                            $('#accordion').css('position', 'relative').append('<div class="novapay-loader"><div class="loader"></div></div>');
                            $('#button-shipping-method').click();
                            var intv1 = setInterval(function () {
                                if ($('[href="#collapse-shipping-method"]').attr('aria-expanded') === 'false') {
                                    $('[name="agree"]').click();
                                    $('#button-payment-method').click();
                                    $('[href="#button-payment-method"]').click();
                                    $('#button-shipping-method').addClass('fixed');
                                    clearInterval(intv1);
                                    setTimeout(function () {
                                        var intv2 = setInterval(function () {
                                            if ($('[href="#collapse-shipping-method"]').attr('aria-expanded') === 'false') {
                                                $('[href="#collapse-shipping-method"]').click();
                                            } else {
                                                $('[data-value="' + window.warehouse + '"]').click();
                                                $('.novapay-loader').remove();
                                                clearInterval(intv2);
                                            }
                                        }, 100);
                                    }, 2000);
                                }
                            }, 100);
                        }
                    }
                }
            });
        });


    });
})(jQuery, window, document);
