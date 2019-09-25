/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

(function ($) {

        let gal_idx = 0;
        function galleryIndex() {
            return gal_idx++;
        }

        function add_widget_galery() {
            jQuery('.widget_media_library').each(function (i) {

                let idx = galleryIndex();
                jQuery('a.stm_fancybox img', $(this)).each(function () {


                    if (1 > $(this).find('a').length) {

                        var src, caption, regex, subst, inner = $(this), outer, capt;

                        src_big = function (me) {
                            src = $(me).data('src') ? $(me).data('src') : me.src;
                            regex = /(.+)(-\d{1,}x\d{1,})(.)(jpg|jpeg|png|gif)/;
                            subst = '$1$3$4';
                            return src.replace(regex, subst);
                        }

                        src = $(this).data('src');
                        caption = (capt = $(this).parents('[class*="slide-"]').find('.caption').length) ? capt.text() : '';
                        outer = '<a href="' + src_big(this) + '" data-fancybox="gallery-' + idx + '" data-caption="' + caption + '">';

                        inner.wrap(outer);

                    }
                    ;

                });

            });
        }
        
        function add_meta_slider() {
            jQuery('.metaslider.fancybox').each(function (i) {
        
                let idx = galleryIndex();
                jQuery('.slides img[class*="slide-"]', $(this)).each(function () {
        
        
                    if (1 > $(this).find('a').length) {
        
                        var src, caption, regex, subst, inner = $(this), outer;
        
                        src_big = function (me) {
                            src = $(me).data('src') ? $(me).data('src') : me.src;
                            regex = /(.+)(-\d{1,}x\d{1,})(.)(jpg|jpeg|png|gif)/;
                            subst = '$1$3$4';
                            return src.replace(regex, subst);
                        }
                        
                        src = $(this).data('src');
                        caption = $(this).parents('[class*="slide-"]').find('.caption').text();
                        outer = '<a href="' + src_big(this) + '" data-fancybox="gallery-' + idx + '" data-caption="' + caption + '">';
        
                        inner.wrap(outer);
        
                    }
                    ;
        
                });
        
            });
        }

        add_widget_galery();
        add_meta_slider();

    })(jQuery)

    
    $('.fancybox a').fancybox({
        helpers : {
            overlay : {
                css : {
                    'background' : '#f00'
                }
            }
        }
    });