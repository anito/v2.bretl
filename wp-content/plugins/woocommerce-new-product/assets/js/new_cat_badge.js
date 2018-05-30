(function ($) {

    var add_cat_new_badge = function () {
        var templFn = function() {
            return $('<span class="wc-new-badge badge">' + new_badge_params.label + '</span>');
        }
        $('.product_cat-neu-im-shop').each(function ( i, el) {
            if($(this).is('li')) {
                $(this).find('img').after(templFn().addClass('left'));
            } else {
                if( $('.product-top').length) { // wptouch
                    $('.product-top').prepend(templFn().addClass('right'));
                } else {
                    $(this).find('.summary').before(templFn().addClass('right'));
                }
            }
        })
    }

    add_cat_new_badge();

})(jQuery)
