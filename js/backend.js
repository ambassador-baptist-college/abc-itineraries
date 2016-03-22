jQuery(document).ready(function(){
    jQuery('[name="post_title"], #acf-field-address_1, #acf-field-address_2, #acf-field-city, #acf-field-state, #acf-field-zip').off('blur');
    jQuery('[name="post_title"], #acf-field-address_1, #acf-field-address_2, #acf-field-city, #acf-field-state, #acf-field-zip').on('blur', function(){
        var address = jQuery('[name="post_title"]').val() + ' ' + jQuery('#acf-field-address_1').val() + ' ' + jQuery('#acf-field-address_2').val() + ' ' + jQuery('#acf-field-city').val() + ', ' + jQuery('#acf-field-state').val() + ' ' + jQuery('#acf-field-zip').val();
        console.log('address to look up: ' + address);
        jQuery('.acf-google-map .acf-sprite-remove').trigger('click');
        jQuery('.acf-google-map input.search, .acf-google-map .input-address').val(address);
    });
});
