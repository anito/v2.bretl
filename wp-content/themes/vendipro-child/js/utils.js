(function ($) {
    'use strict';

    $.fn.textify = function(bodyClasses, ignoredClasses) {

        var h3, hasMedia, exclude, include, selector;

        exclude = function () {

            var i, exclude, ret = '';

            for (i in bodyClasses) {
                exclude = bodyClasses[i];
                ret += `:not( ${exclude} )`;
            }
            return ret;
        }
        include = function(el, selectors) {
            var ret = [];
            selectors = selectors.replace(/\s/g,'').split(',');
            for(var i in bodyClasses) {
                include = bodyClasses[i];
                for(var j in selectors) {
                    ret.push(`${el}` + `${include}` + ' ' + selectors[j]);
                }
            }

            ret = ret.join();
            return ret;
        }

        selector = this.selector;
        $(selector + exclude() + ' h3:not(.no-textify)').each( function(i) {

            var header      = $('<div class="textbox-header-wrapper textbox-header-wrapper-id' + i +'"></div>'),
                contentId   = 'textbox-content-wrapper-' + i,
                content     = $('<div id="' + contentId + '" class="textbox-content-wrapper"></div>'),
                h5 = null;
                
            h3 = $(this);

            // iterate through all the adjacent siblings no matter what, starting at H3
            $('~ *', h3).each( function(j) {

                var wrapper = $('<div class="woocommerce textbox-wrapper textbox-wrapper-id' + i + '"></div>'),
                    that = $(this);
                    
                if(that.is('h5') && $('+ h5', h3).length) { // found an H5

                    h5 = that;
                    
                } else if( that.is('p') || that.is('div') || that.is('ol') || that.is('ul') ) { // Pees or the Div

                    that.cssData(that).addClass(`textbox-item textbox-id-${i}`);
                    
                    hasMedia = (that.children('iframe.media, img.media, [id*=animation_hype_container]').length);
                    if(hasMedia) {
                        that.addClass('media');
                    }
                    
                    
                    if($('+ p, + div, + ol, + ul', that).length ) { // there are more Pees to follow
                        
                        content.append(that); // keep the Pee / Div
                        
                    } else {
                        
                        h3.addClass('textbox-header textbox-id-' + i);
                        h3.before(wrapper); // remember the position of our H3 in the DOM
                        wrapper.append(h3); // move H3 and add css

                        if(h5) { // move H5
                            h5.addClass('textbox-subline textbox-id-' + i);
                            wrapper.append(h5);
                        }

                        if(!that.hasClass( ignoredClasses ) ) {
                            content.append(that);
                            that.addClass('textbox-item textbox-item-last');
                        }

                        wrapper.append(content); // move content wrapper
                        h3.wrap(header);
                        
                        wrapper.addClass('processing').block({
                            message: 'Bitte warten',
                            overlayCSS: {
                                background: '#00000022',
                                opacity: 0.6,
                            }
                        }).cssData(h3, contentId);
                        
                    }

                } else if (that.is('style') || that.is('blockquote') ) { // preserve style tags from Galleries however leave them alone (put them outside the box)
                    
                    return;
                    
                }
                else { // no box
                    
                    return false;
                
                }

            });

        })

        $( document ).ready(function() {
            $('.textbox-wrapper').each(function() {
                $(this).data('blockUI.static', false); // if not set to false, unblock sets position style to true
                var x = $(this).addClass('initialized').removeClass('processing').unblock();
            })
//            $(include(selector, 'h3 ~ h5, h3 ~ p, h3 ~ div, h3 ~ ol, h3 ~ ul')).css({'opacity': 1, 'visibility': 'visible'});
        });

    }

    $.fn.informify = function() {

        var selector = this.selector, h6, that, wrapper;
        
        $(selector + ' h6').each( function(i) {
            
            wrapper = $(`<div class="effect information-wrapper"></div>`);
            wrapper.addClass( `information-wrapper-id-${i}`);
            
            h6 = $(this);
            h6.before(wrapper);

            // iterate through all the adjacent siblings no matter what, starting at H6
            $('~ *', h6).each( function(j) {

                that = $(this);
                
                if(that.is('p') || that.is('div') || that.is('ol') || that.is('ul')) { // found an Pee / Div / Ol / Ul
                    
                    var hasSiblings = $('+ p, + div, + ol, + ul', that).length; // check before we move it away
                    wrapper.append(that); // move the Pee / Div
                    
                    if( !hasSiblings ) { // the last occurence
                        
                        wrapper.prepend(h6); // move H6
                        wrapper.wrap('<div></div>'); // protect from textboxify
                        
                        return false;
                    }
                    
                } else {
                    
                    return false;
                            
                }
                
            })
            
            wrapper.prepend(h6); // h6 but no sibling found, wrap the single line header
            wrapper.wrap('<div></div>'); // protect from textboxify

        })
    }
    
    $.fn.cssData = function(el, contentId) {
        
        el = $(el);
        if(!el.length)
            return this;
        
        var ABS_POS = ['top', 'right', 'bottom', 'left'], idx, cssAttr, cssAttrFirstChar, cssAttrRestChars, css_obj = {}, content_css_obj = {}, content_css_obj_raw = {};
        
        for(let data in el.data()) {
            // channel the style to proper object | element
            if(-1 === (idx = data.indexOf('content'))) {
                css_obj[data] = el.data(data); // copy styles from h3.data attribute to an object
            } else {
                /*
                 * jQueryfy data for use with css()
                 * e.g. data attribute "data-content-justify-Content should result in justifyContent not  JustifyContent
                 * 
                 */
                cssAttrFirstChar = data.slice(idx+7, idx+8).toLowerCase();
                cssAttrRestChars = data.slice(idx+8);
                cssAttr = cssAttrFirstChar + cssAttrRestChars;
                
                content_css_obj[cssAttr] = el.data(data); // copy styles from h3.data attribute to an object for later use on content element
                content_css_obj_raw[data] = el.data(data); // copy styles from h3.data attribute to an object for later use on content element
            }
                
            for(let name in css_obj) {
                if(ABS_POS.indexOf(name) !== -1) { // fix position from jQuery.blockUI 
                    css_obj['position'] = 'absolute';
                    break;
                }
                css_obj['position'] = 'relative';
            }
        }
        if($('#' + contentId).length) {
            $('#' + contentId).css(content_css_obj);
        }
        this.css(css_obj);
        return this;
        
    }
    
    $.fn.blurBackground = function(src) {
        
        var selector = this.selector, img;
        if (selector === undefined || src === undefined) return;
        
        
        $(selector).prepend('<div class="expand-background"><img/></div>');
        img = $(selector + ' .expand-background img')[0];
        img.onload = function() {
            $(selector + ' .expand-background').addClass('initialized');
        };
        img.src = src;
        
    }
    
})(jQuery)