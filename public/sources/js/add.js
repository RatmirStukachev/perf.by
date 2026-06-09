$(document).ready(function() {
    let timeout = null;
    
    // Hide delivery block initially
    $('.delivery-block').hide();
    
    // Check initial delivery selection on page load
    checkDeliveryBlock();
    
    // Update prices on page load
    updateDeliveryPrice();
    
    $(document).on('click', '.btn-to-cart', function () {
        window.location = '/cart'
    });

    $(document).on('click', '._js-add-to-cart', function(e) {
        e.preventDefault();

        let button = $(this);        
        let productId = button.data('product-id');
        let count = button.closest('.cart-block').find('._js-product-count').val();

        $.ajax({
            url: '/cart/add',
            method: 'POST',
            data: {
                product_id: productId,
                count: count,
                _token: $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                $('.cart .count').text(response.cart_count);

                button.addClass('btn-to-cart _active').removeClass('_js-add-to-cart');

                $('.cart-button').text('В корзине');
            },
            error: function(xhr) {
                console.error('Ошибка при добавлении в корзину');
            }
        });
    });

    $(document).on('click', '._js-b-plus', function(e) {
        e.preventDefault();

        let input = $(this).closest('._js-pcscontrolls').find('input');
        let availability = input.data('max');
        let value = parseInt(input.val());

        if (value >= availability) {
            showValidationPopup('Выбрано максимальное количество', 'info');
            setTimeout(function() { $('._js-validation-alert').hide(); }, 3000);
            return;
        }

        value++;
        input.val(value);

        if (window.location.pathname == '/cart') {
            clearTimeout(timeout);
            timeout = setTimeout(function () {
                let productId = input.data('product-id');
                let count = value;
                let method = 'PUT';
                let url = "/cart/update";
                let send = {
                    product_id: productId,
                    count: count,
                    delivery_id: $('._js-delivery:checked').val(),
                    name: $('input[name="name"]').val(),
                    surname: $('input[name="surname"]').val(),
                    middle_name: $('input[name="middle_name"]').val(),
                    phone: $('input[name="phone"]').val(),
                    email: $('input[name="email"]').val(),
                    payment_type_id: $('._js-payment-type:checked').val(),
                };

                updateCart(url, send, method);
            }.bind(this), 1000);
        }

    });

    $(document).on('click', '._js-b-minus', function(e) {
        e.preventDefault();
        let input = $(this).closest('._js-pcscontrolls').find('input');
        let currentValue = parseInt(input.val());

        if (currentValue > 1) {
            input.val(currentValue - 1);
        }

        if (window.location.pathname == '/cart') {
            clearTimeout(timeout);
            timeout = setTimeout(function () {
                let productId = input.data('product-id');
                let count = parseInt(input.val());
                let method = 'PUT';
                let url = "/cart/update";
                let send = {
                    product_id: productId,
                    count: count,
                    delivery_id: $('._js-delivery:checked').val(),
                    name: $('input[name="name"]').val(),
                    surname: $('input[name="surname"]').val(),
                    middle_name: $('input[name="middle_name"]').val(),
                    phone: $('input[name="phone"]').val(),
                    email: $('input[name="email"]').val(),
                    payment_type_id: $('._js-payment-type:checked').val(),
                };

                updateCart(url, send, method);
            }.bind(this), 1000);
        }
    });

    /**
     * Изменение кол-ва вручную
     */
    $(document).on('change', '._js-input-cart', function() {
        let input = $(this);
        let value = parseInt(input.val());
        let availability = input.data('max');

        if (value < 1) {
            value = 1;
            input.val(value);
        }

        if (value > availability) {
            value = availability;
            input.val(value);
        }

        let productId = input.data('product-id');
        let method = 'PUT';
        let url = "/cart/update";
        let send = {
            product_id: productId,
            count: parseInt(input.val()),
            delivery_id: $('._js-delivery:checked').val()
        };

        updateCart(url, send, method);
    });

    $(document).on('input', '._js-input-cart, ._js-product-count', function() {
        // Оставляем только цифры
        this.value = this.value.replace(/\D/g, '');

        // Проверяем минимальное значение
        if (this.value < 1 && this.value !== '') {
            this.value = 1;
        }

        // Проверяем максимальное значение
        let max = $(this).data('max');
        if (parseInt(this.value) > max) {
            this.value = max;
        }
    });

    $(document).on('click', '._js-remove-product-cart', function(e) {
        e.preventDefault();

        let method = 'DELETE';
        let url = "/cart/remove";
        let send = {
            cart_id: $(this).data('cart-id'),
            delivery_id: $('._js-delivery:checked').val(),                               
            name: $('input[name="name"]').val(),
            surname: $('input[name="surname"]').val(),
            middle_name: $('input[name="middle_name"]').val(),
            phone: $('input[name="phone"]').val(),
            email: $('input[name="email"]').val(),
            payment_type_id: $('._js-payment-type:checked').val(),
        };

        updateCart(url, send, method);
    });

    function updateCart(url, send, method) {
        $.ajax({
            type: method,
            url: url,
            data: send,
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function (response) {
                if(response.success) {
                    if (!response.totalSum) {
                        window.location = '/'
                    }

                    if (window.location.pathname == '/cart') {
                        $('._js-cart-form').html(response.cartBlockHtml);
                        // Reinitialize delivery block and prices after cart update
                        checkDeliveryBlock();
                        updateDeliveryPrice();
                    }

                    $('.cart .count').text(response.cart_count);
                }
            },
            error: function(xhr) {
            }
        });
    }

    $(document).off('submit', '.order-form').on('submit', '.order-form', function(e) {
        e.preventDefault();

        let form = $(this);
        let submitButton = form.find('button[type="submit"]');
        
        // Prevent duplicate submissions
        if (form.data('submitting')) {
            return false;
        }
        form.data('submitting', true);
        submitButton.prop('disabled', true);
        
        form.find('.input__default, .textarea__default').removeClass('error');
        form.find('.styled-figure').removeClass('error');
        form.find('._js-payment-block').removeClass('error');
        form.find('._js-delivery-block').removeClass('error');

        // Get form data and add delivery price
        let formData = form.serialize();
        let selectedDelivery = $('._js-delivery:checked');
        let deliveryPrice = selectedDelivery.length > 0 ? selectedDelivery.data('delivery-price') : 0;
        
        // Add delivery price to form data
        formData += '&delivery_price=' + encodeURIComponent(deliveryPrice);

        $.ajax({
            url: '/order/create',
            type: 'POST',
            data: formData,
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                if (response.success && response.redirect) {
                    window.location = response.redirect;
                }
            },
            error: function(xhr) {
                let errors = xhr.responseJSON?.errors;
                let message = xhr.responseJSON?.message;
                let errorMessages = [];
                
                // Prioritize individual errors over summary message
                if (errors && Object.keys(errors).length > 0) {
                    // Show individual field errors (more specific and useful)
                    $.each(errors, function(field, messages) {
                        errorMessages.push(messages[0]);
    
                        // Подсвечиваем поля с ошибками
                        if (field === 'delivery_id') {
                            $('._js-delivery-block').addClass('error');
                        } else if (field === 'payment_type_id') {
                            $('._js-payment-block').addClass('error');
                        } else if (field === 'customer') {
                            $('._js-change-customer').closest('.custom-selector').addClass('error');
                        } else if (field === 'agree') {
                            form.find(`[name="${field}"]`).closest('.custom-selector').find('.styled-figure').addClass('error');
                        } else {
                            form.find(`[name="${field}"]`).addClass('error');
                        }
                    });
                } else if (message) {
                    // Only use summary message if there are no individual field errors
                    // (typically for custom exceptions like inventory checks)
                    errorMessages.push(message);
                }
                
                // Показываем ошибки только если они есть
                if (errorMessages.length > 0) {
                    showValidationPopup(errorMessages, 'error');
                    setTimeout(function() { $('._js-validation-alert').hide(); }, 3000);
                }
                
                // Reset form state on error
                form.data('submitting', false);
                submitButton.prop('disabled', false);
            },
            complete: function() {
                form.data('submitting', false);
                submitButton.prop('disabled', false);
            }
        });        
        
    });

    $(document).on('input', 'input[name="phone"]', function () {
        // Оставляем только цифры и плюс в начале
        this.value = this.value.replace(/[^\d+]/g, '')
            .replace(/(^\+)?([+\d]*)/, '$1$2') // оставляем плюс только в начале
            .replace(/^\+{2,}/, '+'); // убираем лишние плюсы в начале
        
        // Применяем маску +375 (XX) XXX-XX-XX
        let value = this.value;
        
        // Если начинается не с +375, добавляем +375
        if (value && !value.startsWith('+375')) {
            if (value.startsWith('+')) {
                value = '+375' + value.substring(1);
            } else {
                value = '+375' + value;
            }
        }
        
        // Применяем маску
        if (value.startsWith('+375')) {
            let digits = value.replace(/\D/g, '').substring(3); // убираем 375 и нецифровые символы
            let masked = '+375';
            
            if (digits.length > 0) {
                masked += ' (' + digits.substring(0, 2);
                if (digits.length > 2) {
                    masked += ') ' + digits.substring(2, 5);
                    if (digits.length > 5) {
                        masked += '-' + digits.substring(5, 7);
                        if (digits.length > 7) {
                            masked += '-' + digits.substring(7, 9);
                        }
                    }
                } else if (digits.length === 2) {
                    masked += ')';
                }
            }
            
            this.value = masked;
        }
    });
    
    // Обработчик для формы обратной связи
    $(document).on('submit', '.form-callback', function(e) {
        e.preventDefault();
    
        let form = $(this);
        form.find('.input__default').removeClass('error');
        form.find('.styled-figure').removeClass('error');
        
        $.ajax({
            url: '/send-callback',
            method: 'POST',
            data: form.serialize(),
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                $('.s-popup').hide();
                $('.w-popup').hide();
                $('.s-popup__background').hide();
                $('.w-popup').removeClass('animate');
                form.find('.custom-selector.check').removeClass('_checked');
                form.find('input[name="agree"]').prop('checked', false);

                showValidationPopup(response.message, 'success');
                form.find('.input__default').val('');
                setTimeout(function() { $('._js-validation-alert').hide(); }, 3000);
            },
            error: function(xhr) {
                let errors = xhr.responseJSON.errors;
                let errorMessages = [];

                if (errors) {
                    // Проходим по всем ошибкам
                    $.each(errors, function(field, messages) {
                        errorMessages.push(messages[0]);

                        // Проверяем реальное наличие ошибки для delivery_id и payment_type_id
                        if (field === 'delivery_id' && !selectedDelivery.val()) {
                            $('._js-delivery-block').addClass('error');
                        } else if (field === 'payment_type_id' && !selectedPaymentType.val()) {
                            $('._js-payment-block').addClass('error');
                        } else if (field === 'agree') {
                            form.find(`[name="${field}"]`).closest('.custom-selector').find('.styled-figure').addClass('error');
                        } else {
                            form.find(`[name="${field}"]`).addClass('error');
                        }
                    });
                }

                showValidationPopup(errorMessages, 'error');
                setTimeout(function() { $('._js-validation-alert').hide(); }, 3000);
            }
        });
    });

    // Handle delivery option change
    $(document).on('change', '._js-delivery', function() {
        checkDeliveryBlock();
        updateDeliveryPrice();
    });

    function checkDeliveryBlock() {
        let selectedDelivery = $('._js-delivery:checked');
        
        if (selectedDelivery.length > 0 && selectedDelivery.val() != '1') {
            $('.delivery-block').slideDown(300);
        } else {
            $('.delivery-block').slideUp(300);
        }
    }

    function updateDeliveryPrice() {
        let selectedDelivery = $('._js-delivery:checked');
        let basePriceText = $('._js-price').text();
        let basePrice = parseFloat(basePriceText.replace(/[^\d.,]/g, '').replace(',', '.')) || 0;
        
        if (selectedDelivery.length > 0) {
            let deliveryPrice = parseFloat(selectedDelivery.data('delivery-price')) || 0;
            let totalPrice = basePrice + deliveryPrice;
            
            // Update delivery price display
            $('._js-delivery-price').text(formatPrice(deliveryPrice));
            
            // Update total price display
            $('._js-total-price').text(formatPrice(totalPrice));
        } else {
            // No delivery selected
            $('._js-delivery-price').text('0 BYN');
            $('._js-total-price').text(formatPrice(basePrice));
        }
    }

    function formatPrice(price) {
        return price.toFixed(2).replace('.', ',') + ' BYN';
    }

    function showValidationPopup(messages, type) {
        let popup = $('.s-validation');
        let content = popup.find('.w-icon-left .content ul');
        let alertBox = popup.find('.w-validation-alert');

        content.empty();
        if (Array.isArray(messages)) {
            $.each(messages, function(index, message) {
                content.append('<li>' + message + '</li>');
            });
        } else {
            content.append('<li>' + messages + '</li>');
        }

        if (type === 'success') {
            alertBox.removeClass('color002').addClass('color001');
        } else {
            alertBox.removeClass('color001').addClass('color002');
        }

        popup.removeClass('hide').fadeIn(300);

        // setTimeout(function() {
        //     popup.fadeOut(300, function() {
        //         popup.addClass('hide');
        //     });
        // }, 1500);
    }
});
