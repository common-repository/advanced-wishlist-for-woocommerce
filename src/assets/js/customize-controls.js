/* global customizer_info */

(function () {
    "use strict";
    (function ( wp, $ ) {
        $(function() {
            wp.customize.bind( 'ready', function() {
                $('.awl-icon-picker').closest('li.customize-control').
                    next('.customize-control-cropped_image').hide();
                $('.awl-icon-picker #select input:radio[name^=\'icon\']:checked').each(function() {
                    const iconValue = $(this).val();
                    if (iconValue === 'custom') {
                        $(this).closest('li.customize-control').
                        next('.customize-control-cropped_image').show();
                    }
                });
                $('.awl-icon-picker a').on('click', function(e){
                    var a = $(this),
                        href= a.attr('href'),
                        arr = a.find('.arr');
                    const otherA = $('.awl-icon-picker a.open').not(this);
                    otherA.siblings(href).toggle();
                    otherA.find('.arr').toggleClass('dashicons-arrow-down').toggleClass('dashicons-arrow-up');
                    otherA.toggleClass('open');
                    a.siblings(href).toggle();
                    arr.toggleClass('dashicons-arrow-down').toggleClass('dashicons-arrow-up');
                    a.toggleClass('open');
                    e.preventDefault();
                });
                $('.awl-icon-picker #select input').on('change', function(){
                    const iconPicker = $(this).parents('.awl-icon-picker');
                    const def = iconPicker.find('.def'),
                        iconValue = $(this).val();
                    if (iconValue === 'custom') {
                        def.text(algolWishlistCustomizerData.labels.custom);
                    } else if (iconValue === 'none') {
                        def.text(algolWishlistCustomizerData.labels.selectIcon);
                    } else {
                        const span = $('<span class="dashicons dashicons-'+iconValue+'"></span>');
                        def.html(span);
                    }


                    iconPicker.find('a').click();
                    const customIconPicker = iconPicker.closest('li').
                    next('.customize-control-cropped_image');
                    if (iconValue !== 'custom') {
                        customIconPicker.hide();
                        customIconPicker.find('.remove-button').trigger('click');
                    } else {
                        customIconPicker.show();
                    }
                })
            });
        });


    })( window.wp, jQuery );
}).call( this );
