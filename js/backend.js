(function($){
    $(document).ready(function(){
        $('[name="post_title"], .acf-field[data-name="address_1"] input, .acf-field[data-name="address_2"] input, .acf-field[data-name="city"] input, .acf-field[data-name="state"] select, .acf-field[data-name="zip"] input, .acf-field[data-name="country"] input').on('blur', function(){
            var address = $('[name="post_title"]').val() + ', ' + $('.acf-field[data-name="address_1"] input').val() + ' ' + $('.acf-field[data-name="address_2"] input').val() + ' ' + $('.acf-field[data-name="city"] input').val() + ', ' + $('.acf-field[data-name="state"] select').val() + ' ' + $('.acf-field[data-name="zip"] input').val() + ' ' + $('.acf-field[data-name="country"] input').val();
            console.log('address to look up: ' + address);
            $('.acf-google-map .acf-sprite-remove').trigger('click');
            $('.acf-google-map input.search, .acf-google-map .input-address').val(address);
        });
    });
})(jQuery);
