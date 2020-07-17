(function( $ ) {
    $( document ).ready(function() {
        $('.quantity-input').val('');

        if($('.php-error').length > 0) {
            $([document.documentElement, document.body]).animate({
                scrollTop: $('.php-error').offset().top
            },300);
        }

    });
    
   
    $(document).on('input','.quantity-input',function(e){
        
        let rowTag = $(this).closest('tr');
        let quantity = $(this).val();
        let price_nds = rowTag.find('.td-price-nds').attr('price');
        let price = rowTag.find('.td-price').attr('price');
        let weight = rowTag.find('.td-weight').attr('weight');
        
        let totalTag = rowTag.find('.td-row-total');

        let totalRow = (price * quantity).toFixed(2);
        if(!(totalRow > 0)) totalRow ='';

        let totalRowNds = (price_nds * quantity).toFixed(2);
        if(!(totalRowNds > 0)) totalRowNds ='';


        totalTag.attr('quantity', quantity);
        totalTag.attr('weight', weight*quantity);
        totalTag.attr('price', totalRow);
        totalTag.attr('price_nds', totalRowNds);
        totalTag.html(totalRowNds);

        recalcOrderTotals();
    });

    function recalcOrderTotals() {
        let orderTotal = 0;
        let orderTotalNds = 0;
        let orderTotalWeight=0;
        $('.td-row-total').each(function(){
            if($(this).attr('quantity') > 0) {
                orderTotal += Number.parseFloat ( $(this).attr('price')) ;
                orderTotalNds += Number.parseFloat ($(this).attr('price_nds'));
                orderTotalWeight += Number.parseFloat ($(this).attr('weight'));
            }
           
        })
        
        $('.order-total').html(orderTotal.toFixed(2));
        $('.order-total-nds').html(orderTotalNds.toFixed(2));
        $('.order-total-weight').html(orderTotalWeight.toFixed(2));
    }
    



    $(document).on('click','.submit-btn',function(e) {
        let form = $('.order-form');
        e.preventDefault();
        $('.error-msg').hide();
        $('.php-error').hide();
        let products = [];
        $('.product-row').each(function(){
            let quantity = $(this).find('.quantity-input').val();
            if(quantity > 0) {
                let id = $(this).find('.td-row-id').attr('row_id');

                let input = document.createElement('input');
                input.setAttribute('type','hidden');
                input.setAttribute('name','products['+id+']');
                input.setAttribute('value', quantity);
                products.push(input);
            }
        });

        if(!validate_form()) {
            $([document.documentElement, document.body]).animate({
                scrollTop: $('.error-msg').offset().top
            }, 500);
            return;
        }

        if(!products.length) {
            $('.error-msg').html('Вы не выбрали продукцию для заявки!').show();
            $([document.documentElement, document.body]).animate({
                scrollTop: $('.error-msg').offset().top 
            }, 500);
            return;
        }

       


        form.append(products);
        form.submit();
        
    });

    $(document).on('input','#company-name, #mail, #phone',function(e) {
        $(this).removeClass('error');
    });

    function validate_form() {
        let error = false;
        let errorMsg = '';
        let company = $('#company-name').val();
        let email = $('#mail').val();
        let phone = $('#phone').val();
        $('#company-name').removeClass('error');
        $('#mail').removeClass('error');
        $('#phone').removeClass('error');

        if(!error & !company) {
            error = true;
            errorMsg = "Вы не ввели 'Наименование предприятия'";
            $('#company-name').addClass('error');
        }
        if(!error & !email ) {
            error = true;
            errorMsg = "Вы не ввели 'E-mail'";
            $('#mail').addClass('error');
        }
        if(!error & !phone) {
            error = true;
            errorMsg = "Вы не указали 'Телефон'";
            $('#phone').addClass('error');
        }

        if(!error & !validateEmail(email)) {
            error = true;
            errorMsg = "Неправильный 'E-mail'";
            $('#mail').addClass('error');
        } 

        if(!error & !validatePhone(phone)) {
            error = true;
            errorMsg = "Неправильный 'телефон'";
            $('#phone').addClass('error');
        } 


        if(error) {
            $('.error-msg').html(errorMsg).show();
        }

        return !error;
    }

    function validateEmail(email) {
        var re = /^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
        return re.test(String(email).toLowerCase());
    }

    function validatePhone(phone) {
        var re = /^[\+]?[(]?[0-9]{3}[)]?[-\s\.]?[0-9]{3}[-\s\.]?[0-9]{4,6}$/im;
        return re.test(String(phone).toLowerCase());
    }

    
})( jQuery );
