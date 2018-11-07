/* WPtouch MobileStore Theme JS File */
var page_wrapper = jQuery('.page-wrapper');
var isCheckoutPage = jQuery('body').hasClass('woocommerce-checkout');
var isCartPage = jQuery('body').hasClass('woocommerce-cart');
var isAccountPage = jQuery('body').hasClass('woocommerce-account');
var currentDomain = location.hostname.split('.').join('-');

// WooCommerce load more products link
function mobilestoreLoadMore() {
    if (jQuery('body').hasClass('ajax-pagination')) {
        var loadMoreLink = 'a.load-more-products-link';
        page_wrapper.on('click', loadMoreLink, function (e) {

            var loadMoreURL = jQuery(this).attr('rel');
            nextPage = loadMoreURL.split('/');
            jQuery.bbq.pushState({page: nextPage[ nextPage.length - 2 ]});

            var targetDiv = setupLoadMoreElements(jQuery(this));

            targetDiv.hide().load(loadMoreURL + ' #content ul.products.main li, a.load-more-products-link', function () {
                var newProducts = jQuery(this).children('li');
                newProducts.css('visibility', 'hidden').addClass('tappable');
                if (wptouchReadCookie(currentDomain + '-mobilestore-layout') == 'vertical') {
                    newProducts.addClass('vertical');
                }
                revealProducts(targetDiv, newProducts, false);
            });

            e.preventDefault();
        });
    }
}

// Load more products link handling
function setupLoadMoreElements(loadMoreLink) {
    loadMoreLink.addClass('ajaxing').text(wptouchFdn.ajaxLoading).prepend('<span class="spinner"></span>');
    jQuery('.spinner').spin('tiny');
    loadMoreLink.after("<span class='ajax-target'></span>");
    return jQuery('.ajax-target');
}

// Appending new products with isotope()
function revealProducts(targetDiv, newProducts, doScroll) {
    var itemsToGet = jQuery('#content').find('ul.products.main');
    itemsToGet.append(newProducts).imagesLoaded(function () {
        newProducts.css('visibility', 'visible');
        itemsToGet.isotope('insert', newProducts);

        if (doScroll) {
            jQuery('body').scrollTop(jQuery.bbq.getState('scroll'));
        }
    });

    jQuery('.ajaxing').animate({height: 'toggle'}, 200, 'linear', function () {
        jQuery(this).remove();
        jQuery('a.load-more-products-link', targetDiv).first().insertAfter('#content');
    });

    targetDiv.remove();
}

// Figure out how many pages need to be added to the current view if using back in the browser (really helpful in Web-App Mode)
function mobilestoreLoadStateElements(targetDiv, urlParts, startPage, maxPage, newProducts) {
    urlParts[ urlParts.length - 2 ] = startPage;
    loadMoreURL = urlParts.join('/');

    targetDiv.hide().load(loadMoreURL + ' #content ul.products.main li, a.load-more-products-link', function () {

        jQuery(this).children('li').addClass('page' + startPage);

        lastProducts = jQuery(newProducts);
        newProducts = jQuery(this).children('li');
        newProducts.addClass('tappable');
        newProducts = jQuery.merge(lastProducts, newProducts);

        if (startPage == maxPage) {
            revealProducts(targetDiv, newProducts, true);
        } else {
            startPage++;
            mobilestoreLoadStateElements(targetDiv, urlParts, startPage, maxPage, newProducts);
        }
    });
}

// Load the previous state now
function mobilestoreRestoreState() {
    // Load extra content
    var lastPageLoaded = jQuery.bbq.getState('page');

    if (lastPageLoaded && lastPageLoaded > 1) {
        var loadMoreLink = jQuery('a.load-more-products-link');
        var loadMoreURL = loadMoreLink.attr('rel');
        var loadMoreURLParts = loadMoreURL.split('/');
        var targetDiv = setupLoadMoreElements(loadMoreLink);

        mobilestoreLoadStateElements(targetDiv, loadMoreURLParts, 2, lastPageLoaded, jQuery());
    }
}

// The main isotope() gravy
function mobilestoreIsotope() {

    jQuery('ul.products').each(function () {
        var node = jQuery(this);
        products = node.find('> *').detach();
        node.html(products);
    });

    // don't proceed if container has not been selected
    var container = jQuery('#content ul.products.main');
    var firstLi = container.find('li').first();
    if (!container) {
        return;
    } else {
        container.imagesLoaded(function () {
            if (wptouchReadCookie(currentDomain + '-mobilestore-layout') == 'vertical') {
                var userLayout = 'vertical';
                jQuery('li', container).addClass('vertical');
            } else {
                var userLayout = 'masonry';
            }
            // init Isotope
            jQuery(this).find('li').css('visibility', 'visible');

            container.isotope({
                itemSelector: 'li',
                layoutMode: userLayout
            });

            // Now restore the page state
            if (jQuery('body').hasClass('ajax-pagination')) {
                mobilestoreRestoreState();
            }
        });
    }
}

// Bind the tappable module explicitly to these elements dynamically
function mobilestoreBindTappableLinks() {
    // Off-Canvas menu items, etc
    jQuery('.pushit li, button, a.button, .products li, .sort-filter-buttons i').not('li.empty').each(function () {
        jQuery(this).addClass('tappable');
    });
}

// Product gallery swapping for images changed by the variation select
function mobilestoreHandleWooVariationImages() {
    image = jQuery('.images > img.attachment-shop_single');
    thumb = jQuery('.thumbnails li').first().find('img');
    thumbs = jQuery('.thumbnails img');
    jQuery(document).on('found_variation', function (event, variation) {
        full_image = variation.image_link;

        if (full_image !== '') {
            image.attr('data-large-image', full_image);
            thumb.attr('data-large-image', full_image).attr('src', variation.image_src);
        } else {
            // Restore the original image.
            full = image.attr('data-o_large');
            image.attr('src', image.attr('data-o_src')).attr('data-large-image', full);
            thumb.attr('data-large-image', full).attr('src', image.attr('data-o_src'));
        }
        thumbs.removeClass('active');
        thumbs.first().addClass('active');
    });

    jQuery(document).on('reset_image', function (event) {
        full = image.attr('data-o_large');
        image.attr('src', image.attr('data-o_src')).attr('data-large-image', full);
        thumbs.removeClass('active');
        thumb.addClass('active').attr('data-large-image', full).attr('src', image.attr('data-o_src'));
    });
}

// The main gallery multitouch magic
function mobilestoreSetupHammer() {
    // Process gallery images and get them out of anchors, setup for image swapping
    jQuery(' .images a, .thumbnails a', '#content').each(function () {
        var bigImg = jQuery(this).attr('href');
        jQuery('img', this).unwrap().attr('data-large-image', bigImg).attr('data-o_large', bigImg).addClass('gallery');
    });

    // Process gallery images and get them out of anchors, setup for image swapping
    jQuery(' .images a, .thumbnails a', '#content').each(function () {
        var bigImg = jQuery(this).attr('href');
        jQuery('img', this).unwrap().attr('data-large-image', bigImg).attr('data-o_large', bigImg).addClass('gallery');
    });

    var thumbDiv = jQuery('#content .thumbnails');
    countThumbs = jQuery('li', thumbDiv).length;

    if (countThumbs > 0) {
        main_image = jQuery('#content .images img.wp-post-image');

        // If there's a duplicate for the main image in the gallery, remove its thumbnail.
        thumbDiv.find('img[data-large-image="' + main_image.attr('data-large-image') + '"]').parent().remove();

        var clonedLi = main_image.clone();
        clonedLi.removeClass('wp-post-image').attr('id', 'image-' + countThumbs).prependTo('.thumbnails').wrap('<li></li>');
        clonedLi.parent().addClass('active');

        var thumbDiv = jQuery('#content').find('.thumbnails');
        thumbDiv.css('visibility', 'visible');
        thumbDiv.on('click', 'li', function (e) {
            e.preventDefault();
            var thumbSmall = jQuery('img', this).attr('src');
            var thumbLarge = jQuery('img', this).attr('data-large-image');

            if (!jQuery(this).hasClass('active')) {
                jQuery('li', thumbDiv).removeClass('active');
                jQuery(this).addClass('active');

                jQuery('img.wp-post-image', '#content').first()
                        .attr('src', thumbSmall)
                        .attr('data-large-image', thumbLarge);

                if (jQuery('body').hasClass('can-zoom-images')) {
                    resetHammer();
                }

            }
        });
    }

    if (jQuery('body').hasClass('can-zoom-images')) {

        if (navigator.userAgent.toLowerCase().indexOf('iphone') != -1 || navigator.userAgent.toLowerCase().indexOf('ipad') != -1) {

            jQuery('.can-zoom-images .images img.wp-post-image').before('<span class="zoomer wptouch-icon-resize-full"></span>');
            jQuery('.images').on('click', '.zoomer', function () {
                jQuery('.images > img').trigger('doubletap');
            });


            var productImgObjDiv = jQuery('.product').find('.images');
            var productImgObj = jQuery('img.wp-post-image.gallery', productImgObj).get(0);

            var hammer_options = {
                preventDefault: false,
                transform_always_block: true,
                transform_min_scale: 1,
                drag_block_horizontal: true,
                drag_block_vertical: false,
                drag_min_distance: 0
            };

            var posX = 0, posY = 0, lastPosX = 0, lastPosY = 0, bufferX = 0, bufferY = 0, scale = 1, last_scale, dragReady = 0, position = jQuery('.images img.wp-post-image.gallery').position();

            function enableAnimation() {
                var mainImg = jQuery('.images img.wp-post-image.gallery');
                mainImg.css('-webkit-transition-duration', '220ms');

                mainImg.one('transitionend webkitTransitionEnd otransitionend oTransitionEnd msTransitionEnd', function () {
                    disableAnimation();
                    position = mainImg.position();
                });
            }

            function disableAnimation() {
                jQuery('.images img.wp-post-image.gallery').css('-webkit-transition-duration', '0');
            }

            function swapImage() {
                var mainImg = jQuery('img.wp-post-image.gallery', '.images');
                if (mainImg.attr('src').indexOf('300x300') != -1 && enableImageReplace) {
                    var fullsize = mainImg.attr('data-large-image');
                    if (mainImg.attr('src') != fullsize) {
                        mainImg.attr('src', fullsize);
                    }
                }
            }

            function applyTransform(image, transform) {
                if (image) {
                    if (transform == 'translate3d(0px, 0px, 0) scale3d(1, 1, 0)') {
                        transform = '';
                    }
                    image.style.transform = transform;
                    image.style.oTransform = transform;
                    image.style.msTransform = transform;
                    image.style.mozTransform = transform;
                    image.style.webkitTransform = transform;
                }
            }

            function resetHammer() {
                enableAnimation();
                posX = 0, posY = 0, lastPosX = 0, lastPosY = 0, bufferX = 0, bufferY = 0, scale = 1, last_scale = 1;
                applyTransform(productImgObj, 'translate3d(0px, 0px, 0) scale3d(1, 1, 0)');
            }

            productImgObjDiv.hammer(hammer_options).on('touch touchend doubletap drag transform', function (ev) {

                switch (ev.type) {
                    case 'touch':
                        enableImageReplace = false;
                        last_scale = scale;
                        disableAnimation();
                        break;

                    case 'touchend':
                        enableImageReplace = false;
                        if (scale > 1) {
                            enableAnimation();
                            boxHeight = productImgObjDiv.height();
                            boxWidth = productImgObjDiv.width();

                            position = productImgObjDiv.find('img.wp-post-image.gallery').position();

                            renderedWidth = productImgObj.width * scale;
                            renderedX = position.left;

                            renderedHeight = productImgObj.height * scale;
                            renderedY = position.top;

                            // Left edge snap
                            if (renderedX > 0) {
                                posX = (renderedWidth - productImgObj.width) / 2;
                            }

                            // Right edge snap
                            if (renderedX + renderedWidth < boxWidth) {
                                correction = boxWidth - (renderedX + renderedWidth);
                                posX = correction + renderedX + ((renderedWidth - productImgObj.width) / 2);
                            }

                            // Top edge snap
                            if (renderedY > 0) {
                                posY = (renderedHeight - productImgObj.height) / 2;
                            }

                            // Bottom edge snap
                            if (renderedY + renderedHeight < boxHeight) {
                                correction = boxHeight - (renderedY + renderedHeight);
                                posY = correction + renderedY + ((renderedHeight - productImgObj.height) / 2);
                            }
                        }

                        lastPosX = posX;
                        lastPosY = posY;

                        if (scale > 1) {
                            enableImageReplace = true;
                            swapImage();
                        } else {
                            enableImageReplace = false;
                        }
                        break;

                    case 'doubletap':
                        if (jQuery.inArray(ev, 'gesture') != -1) {
                            ev.gesture.stopPropagation();
                            ev.gesture.preventDefault();
                        }
                        ev.preventDefault();
                        enableImageReplace = false;
                        enableAnimation();
                        if (scale == 1) {
                            scale = 2;
                            enableImageReplace = true;
                            swapImage();
                            jQuery('.zoomer').fadeOut();
                        } else {
                            posX = 0, posY = 0, lastPosX = 0, lastPosY = 0, bufferX = 0, bufferY = 0, scale = 1, last_scale = 1;
                            jQuery('.zoomer').fadeIn();
                        }
                        break;

                    case 'drag':
                        enableImageReplace = false;
                        if (scale > 1) {
                            ev.gesture.stopPropagation();
                            ev.gesture.preventDefault();
                            posX = ev.gesture.deltaX + lastPosX;
                            posY = ev.gesture.deltaY + lastPosY;
                        }
                        break;

                    case 'transform':
                        ev.gesture.stopPropagation();
                        ev.gesture.preventDefault();
                        scale = Math.max(1, Math.min(last_scale * ev.gesture.scale, 10));

                        if (scale > 10) {
                            scale = 10;
                        }

                        if (scale > 1.1 && scale < 10) {
                            startrWidth = productImgObj.width * last_scale;
                            startrHeight = productImgObj.height * last_scale;
                            startposition = productImgObjDiv.find('img.wp-post-image.gallery').position();

                            startrX = position.left;
                            startrY = position.top;

                            boxWidth = productImgObjDiv.width();
                            boxHeight = productImgObjDiv.height();

                            boxCentreX = boxWidth / 2;
                            boxCentreY = boxHeight / 2;

                            imageCentreX = startrX + (startrWidth / 2);
                            imageCentreY = startrY + (startrHeight / 2);

                            startCentredPixelX = (-1 * startrX) + boxCentreX;
                            startCentredPixelY = (-1 * startrY) + boxCentreY;

                            // end
                            endrWidth = productImgObj.width * scale;
                            endrHeight = productImgObj.height * scale;

                            endrX = imageCentreX - (endrWidth / 2);
                            endrY = imageCentreY - (endrHeight / 2);

                            endCentredPixelX = (-1 * endrX) + boxCentreX;
                            endCentredPixelY = (-1 * endrY) + boxCentreY;

                            // scaled point
                            projectedCentredPixelX = (startCentredPixelX * ev.gesture.scale);
                            projectedCentredPixelY = (startCentredPixelY * ev.gesture.scale);

                            // correction
                            correctionX = endCentredPixelX - projectedCentredPixelX;
                            correctionY = endCentredPixelY - projectedCentredPixelY;

                            posX = lastPosX + (correctionX);
                            posY = lastPosY + (correctionY);

                            jQuery('.zoomer').fadeOut();
                        } else if (scale == 1) {
                            posX = 0;
                            posY = 0;
                            lastPosX = 0;
                            lastPosY = 0;
                            scale = 1;
                            jQuery('.zoomer').fadeIn();
                        }
                        break;
                }

                if (scale == 1) {
                    productImgObjDiv.removeClass('zoomed');
                } else {
                    productImgObjDiv.addClass('zoomed');
                }

                var transform = 'translate3d(' + posX + 'px, ' + posY + 'px, 0) ' + 'scale3d(' + scale + ', ' + scale + ', 0)';

                applyTransform(productImgObj, transform);
            });
        }
    } // can zoom
}

function mobilestoreProductSummaryArea() {
    // Change the inputs in the gallery summary to trigger the numeric keyboard
    jQuery('#content input.qty').attr('type', 'number');
    // Remove the text labels, we're using icons instead for horizontal space savings
    jQuery('.smartphone .woocommerce-tabs ul.tabs a').text('');
}

// Make sure Add to Cart gets caught and triggers the mini-cart off-canvas menu
function mobilestoreHandleAddToCart() {
    if (jQuery('body').hasClass('ajax-add-to-cart')) {
        var cartEl = jQuery('form.cart');
        form_action = cartEl.attr('action');
        if (!form_action) {
            form_action = '';
        }

        cartEl.attr('action', form_action + '?wptouch_mobilestore_action=add_to_cart&ajax=true');

        cartEl.on('click', 'button', function (e) {
            e.preventDefault();
            var action = window.location;
            add_to_cart_button = jQuery(this);
            add_to_cart_button.attr('disabled', 'true').css('padding-left', '20px').spin('tiny');
            add_to_cart_button.children('div').css('left', '-5px');
            cartEl.append('<input type="hidden" name="wptouch_ajax_cart" value="true" />');

            // fix for WooCommerce 3.0+
            if (!jQuery('input[name=add-to-cart]').length && jQuery('button[name=add-to-cart]').length) {
                cartEl.append('<input type="hidden" name="add-to-cart" value="' + jQuery('button[name=add-to-cart]').val() + '" />');
            }
            jQuery.post(action, cartEl.serialize(), function (data) {
                jQuery('.cart-btn').addClass('filled animated');

                data = jQuery.parseJSON(data);
                if (data.wptouch_mobilestore_action == 'success') {
                    if (jQuery('#menu-right').length) {
                        if (jQuery('form.cart').hasClass('variations_form')) {
                            product_id = jQuery('input[name=product_id]').val();
                            variation_id = jQuery('input[name=variation_id]').val();
                            product_item = '.product-' + product_id + '.variation-' + variation_id;
                        } else {
                            if (jQuery('button[name=add-to-cart]').length) {
                                product_id = jQuery('button[name=add-to-cart]').val();
                            } else {
                                product_id = jQuery('input[name=add-to-cart]').val();
                            }
                            product_item = '.product-' + product_id;
                        }

                        if (wptouchMain.query_vars[ 'lang' ] != undefined) {
                            append_lang = '&lang=' + wptouchMain.query_vars[ 'lang' ];
                        } else {
                            append_lang = '';
                        }
                        ;

                        jQuery('#menu-right').load('?wptouch_mobilestore_action=refresh_cart' + append_lang, function () {
                            jQuery('#menu-right ' + product_item).css('opacity', '0');
                            jQuery('.cart-btn').click();
                            jQuery('form.cart').each(function () {
                                this.reset();
                            });
                            setTimeout(function () {
                                jQuery('#menu-right ' + product_item).animate({
                                    opacity: 1
                                }, 500);
                                add_to_cart_button.spin(false).css('padding-left', '').removeAttr('disabled');
                            }, 400);
                        });
                    } else {
                        mobilestoreShowNotice('success', data.message);
                        add_to_cart_button.spin(false).css('padding-left', '16px').removeAttr('disabled');
                    }
                } else if (data.wptouch_mobilestore_action == 'error') {
                    mobilestoreShowNotice('fail', data.message);
                    add_to_cart_button.spin(false).css('padding-left', '16px').removeAttr('disabled');
                }
            });
        });
    }
}

// Mini-cart checkout button
function mobilestoreMiniCartButtons() {
    var edit_button = jQuery('.button.edit_button');
    var checkout_button = jQuery('.button.checkout_button');

    edit_button.on('click', function (e) {
        window.location = jQuery(this).attr('href');
        e.preventDefault();
    });

    checkout_button.on('click', function (e) {
        checkout_url = jQuery(this).attr('href');
        jQuery(this).text('').spin({lines: 8, length: 1, width: 2, radius: 3, top: '-10px', left: '-2px'});
        setTimeout(function () {
            window.location = checkout_url;
        }, 750); // in milliseconds
        e.preventDefault();
    });
}

// error message info when add to cart fails for some reason
function mobilestoreShowNotice(state, message) {
    var noticeEl = jQuery('.notice');
    noticeEl.addClass(state).html(message).fadeIn(function () {
        setTimeout(function () {
            noticeEl.fadeOut();
        }, 4000);
    });
}

// Things to do only in Web App Mode
function mobilestoreWebApp() {
    if (navigator.standalone) {
        jQuery('body').prepend('<span class="fixed-header-fill"></span>');
    }
}

// Magic to split the footer menu into 2 lists
function mobilestoreSplitFooter() {
    var num_cols = 2,
            container = jQuery('.footer-menu ul'),
            listItem = 'li',
            listClass = 'sub-list';
    container.each(function () {
        var items_per_col = new Array(),
                items = jQuery(this).find(listItem),
                min_items_per_col = Math.floor(items.length / num_cols),
                difference = items.length - (min_items_per_col * num_cols);
        for (var i = 0; i < num_cols; i++) {
            if (i < difference) {
                items_per_col[i] = min_items_per_col + 1;
            } else {
                items_per_col[i] = min_items_per_col;
            }
        }
        for (var i = 0; i < num_cols; i++) {
            jQuery(this).append(jQuery('<ul></ul>').addClass(listClass));
            for (var j = 0; j < items_per_col[i]; j++) {
                var pointer = 0;
                for (var k = 0; k < i; k++) {
                    pointer += items_per_col[k];
                }
                jQuery(this).find('.' + listClass).last().append(items[j + pointer]);
            }
        }
    });
}

// Move the footer below the switch
function mobilestoreMoveFooterDiv() {
    var poweredBy = jQuery('.powered-by-msg');

    if (poweredBy.is('div')) {
        poweredBy.prepend(jQuery('.wptouch-mobile-switch'));
    } else {
        jQuery('.footer').append(jQuery('.wptouch-mobile-switch'));
    }
}

// Custom serach capability (with recent searches)
function mobilestoreHandleSearch() {
    var recentSearch = jQuery('.recent-searches');
    var recentSearchList = jQuery('ul', recentSearch);
    var searchInput = jQuery('input#search-text');
    var searchTitle = searchInput.attr('title');

    recentSearches = JSON.parse(wptouchReadCookie(currentDomain + '-mobilestore-searches'));
    if (Array.isArray(recentSearches)) {
        var searchUrl = jQuery('#searchform').attr('action');
        for (var i = 0; i < recentSearches.length; i++) {
            newSearch = '<li><a href="' + searchUrl + '?s=' + recentSearches[ i ] + '&post_type=product">' + decodeURIComponent(recentSearches[ i ]) + '</a></li>';
            recentSearchList.append(newSearch);
        }

        searchInput.on('focus', function () {
            recentSearch.slideDown();
        }).blur(function () {
            jQuery(window).one('scroll', function () {
                recentSearch.animate({height: 'toggle'}, 300);
            });
        });

    }

    recentSearch.on('click', 'h4 span', function () {
        wptouchEraseCookie(currentDomain + '-mobilestore-searches');
        recentSearch.slideUp(400);
        setTimeout(function () {
            recentSearch.remove()
        }, 500);
    });

    jQuery('#searchform').submit(function (e) {
        if (!searchInput.val()) {
            e.preventDefault();
        } else {
            cookieVal = JSON.parse(wptouchReadCookie(currentDomain + '-mobilestore-searches'));
            searchTerm = encodeURIComponent(searchInput.val());

            if (!Array.isArray(cookieVal)) {
                cookieVal = new Array();
            }

            if (jQuery.inArray(searchTerm, cookieVal) == -1) {
                cookieVal.unshift(searchTerm);
                cookieVal = cookieVal.slice(0, 5);
                wptouchCreateCookie(currentDomain + '-mobilestore-searches', JSON.stringify(cookieVal), 90);
            }
        }
    });
}

// Dynamic header text sizing to fit longer site titles
function mobilestoreHandleHeaderText() {
    var header = jQuery('#header-area h1 span');

    jQuery(window).load(function () {
        header.bigText({maximumFontSize: 26});
    }).on('resize', function () {
        header.bigText({maximumFontSize: 26});
    });
}

function mobilestoreModifyQtyButtons() {
    jQuery('.summary .quantity .minus').parent().addClass('spinner');

    // Don't do buttons. Do links instead that we can happily intercept without WooCommerce events interfering.
    jQuery('input.plus').replaceWith('<a class="button plus"><i class="wptouch-icon-plus"></i></a>');
    jQuery('input.minus').replaceWith('<a class="button minus"><i class="wptouch-icon-minus"></i></a>');
}

// Modifications and enhancements to make a better mobile woo checkout
function mobilestoreSetupCheckout() {

    // Most countries use numeric postal codes. Set the input appropriately with exceptions for countries with alphanumeric codes.
    function mobilestoreTogglePostalCodes(checkField, inputField) {
        alphaNumerics = ['AR', 'BN', 'CA', 'MT', 'ND', 'NL', 'PE', 'SO', 'SZ', 'KR', 'GB', 'VE', 'PT'];
        if (alphaNumerics.indexOf(jQuery(checkField).val()) == -1) {
            jQuery(inputField).attr('type', 'tel');
        } else {
            jQuery(inputField).attr('type', 'text');
        }
    }

    if (isCartPage) {

        if (!jQuery('body').hasClass('wc-2.2')) {
            // WooCommerce 2.3 compatibility
            jQuery('div.quantity')
                    .not('.buttons_added')
                    .prepend('<a class="button minus"><i class="wptouch-icon-minus"></i></button>')
                    .append('<a class="button plus"><i class="wptouch-icon-plus"></i></button>');
        }

        // Fix cross-sell images
        jQuery('.cross-sells').find('img').removeClass('aligncenter');

        jQuery('#pay_with_amazon').remove();

        var minuses = jQuery('.minus');

        // WooCommerce outputs a 'remove' link to each cart item. Relocate it to allow us to swap with the minus control.
        jQuery('.remove').each(function () {
            productQuantity = jQuery(this).siblings('.quantity');
            if (productQuantity.is('div')) {
                jQuery(this).prependTo(productQuantity).hide();
            }
        });

        // The minus control switches to remove when there's just one of the item in the cart.
        minuses.each(function () {
            qty = jQuery(this).siblings('.qty');
            if (parseInt(qty.val()) == 1) {
                jQuery(this).addClass('delete').find('i').removeClass('wptouch-icon-minus').addClass('wptouch-icon-cancel');
            }
        });

        // Make sure we see minus not remove after incrementing.
        jQuery('.plus').click(function () {
            qty = jQuery(this).siblings('.qty');
            qty.val(parseInt(qty.val()) + 1);
            linkURL = jQuery(this).siblings('.minus').removeClass('delete').find('i').addClass('wptouch-icon-minus').removeClass('wptouch-icon-cancel');
        });

        // Decrement item counts when > 1, remove the item when count is 1.
        minuses.click(function (e) {
            qty = jQuery(this).siblings('.qty');
            if (qty.val() == 1) {
                e.stopImmediatePropagation();
                e.preventDefault();
                linkURL = jQuery(this).siblings('.remove').attr('href');
                jQuery(location).attr('href', linkURL);
            } else if (qty.val() == 2) {
                jQuery(this).addClass('delete').find('i').removeClass('wptouch-icon-minus').addClass('wptouch-icon-cancel');
            }
            qty.val(parseInt(qty.val()) - 1);
        });

        // Changed submit from <input> to <button>. But shipping calculator also submits the form.
        // Inject a hidden field with the correct proceed value to submit and head to checkout.
        jQuery('#checkout_button').click(function (e) {
            e.preventDefault();
            jQuery('form.cart').append('<input type="hidden" name="proceed" value="' + jQuery(this).val() + '">').submit();
        });

        // If the site owner opted to use the select control for shipping, stage for CSS adjustments
        jQuery('select.shipping_method').parents('tr.shipping').addClass('select');
    }

    if (isCheckoutPage) {

        // Used for replacing optional elements with checkboxes and then removing them if option is enabled
        function mobilestoreRevealElement(e, targetElement) {
            e.preventDefault();
            targetElement.fadeIn();
            jQuery(e.target).remove();
        }

        // Detect thank you page by checking for payment method display
        var customer_details = jQuery('.customer_details');
        if (customer_details.is('dl')) {
            jQuery('body').addClass('woocommerce-thanks');
            customer_details.wrap('<div id="customer_details"></div>').append(jQuery('.addresses'));
        }

        var amazon_wrapper = jQuery('#pay_with_amazon');
        if (amazon_wrapper.parent().is('div.woocommerce-info')) {
            amazon_wrapper.parent().addClass('amazon-wrapper');
        }

        // Toggle the login area
        var login_toggle = jQuery('.login-toggle');
        login_toggle.click(function (e) {
            var startText = translated_strings.login_toggle_start;
            var closeText = '<i class="wptouch-icon-cancel-circled"></i>' + translated_strings.login_toggle_close;

            jQuery('form.login').slideToggle('fast', function () {
                login_toggle.html(login_toggle.html() == closeText ? startText : closeText);
            });
            e.preventDefault();
        });

        // Add HTML5 input type email to billing and shipping email fields
        jQuery('input#billing_email, input#shipping_email').attr('type', 'email');

        // Move the billing field heading to the top of the form and stage for CSS.
        jQuery('.woocommerce-billing-fields h3').prependTo('form[name=checkout]').attr('id', 'billing_fields_heading');

        // Swap the order of checkbox and label to meet best practices.
        jQuery('#ship-to-different-address-checkbox').prependTo('#ship-to-different-address');

        // Hide order comments and add control to reveal it.
        var comments_field = jQuery('#order_comments_field');
        comments_field.hide().before('<p class="form_row"><input type="checkbox" id="show_notes_field"> <label class="checkbox" for="show_notes_field">' + translated_strings.order_notes_link + '</label></p>');
        jQuery('#show_notes_field').click(function (e) {
            comments_field.toggle();
        });

        var payment_methods = jQuery('.payment_methods');
        // When a payment method is selected, stage its parent for CSS.
        jQuery('input[checked]', payment_methods).parent().addClass('active');

        // Toggle payment method detail display when selecting a method (WooCommerce uses the active class to show/hide).
        jQuery('body').on('click', '.payment_methods li', function (e) {
            jQuery('li', jQuery('.payment_methods')).removeClass('active').children('div').css('display', 'none').end().children('input[type="radio"]').prop('checked', false);
            jQuery(this).addClass('active').children('div').css('display', 'block');
            jQuery(this).children('input[type="radio"]').prop('checked', true);
        });

        // If someone clicks the fake apply coupon button, move the coupon code to the real form and submit it.
        jQuery('#order_review').on('click', '#mobilestore_apply_coupon', function (e) {
            jQuery('#coupon_code').val(jQuery('#mobilestore_coupon').val());
            jQuery('.mobilestore_checkout_coupon .coupon-button').click();
        });

        // Whenever AJAX requests finish – e.g., load, change shipping option, change country, etc.
        jQuery(document).ajaxComplete(function () {
            // Configure numeric fields to use numeric keyboards
            jQuery('input[class*=number], input[class*=cvc], input[name*=phone]').attr('type', 'tel');
            mobilestoreTogglePostalCodes('#billing_country', '#billing_postcode');
            mobilestoreTogglePostalCodes('#shipping_country', '#shipping_postcode');

            // Fancy hide/show field labels won't work for selects. Rename their first options to read the same as the label.
            jQuery('.woocommerce-checkout select').each(function () {
                if (jQuery(this).siblings('label').is('label') && (jQuery('option', this).first().text() == 'Select' || jQuery('option', this).first().text() == '')) {
                    jQuery('option', this).first().text(jQuery(this).siblings('label').text());
                }
            });

            // WooCommerce doesn't reliably configure label-field relationships which we need for the label show/hide.
            // Set them up (and add body-font for extra measure).
            jQuery('p.form-row input').each(function () {
                myId = jQuery(this).attr('id');
                if (!myId) {
                    myLabel = jQuery(this).siblings('label');
                    myLabel.addClass('body-font');
                    myLabelTarget = myLabel.attr('for');
                    jQuery(this).attr('id', myLabelTarget);
                }
            });

            // Now apply our fancy hide/show labels with placeholder values.
            mobilestoreTransformLabels();
            mobilestoreModifyCheckoutSelects();

            jQuery('label img', '.payment_methods').each(function () {
                if (jQuery(this).width() > 100) {
                    jQuery(this).prependTo(jQuery(this).parent().siblings('.payment_box'));
                }
            });

            // Stripe's label is very long. After applying our fancy hide/show labels, override the field's placeholder to be short.
            jQuery('#stripe_card_csc, #authorize-net-cim-cc-cvv').attr('placeholder', translated_strings.cvc + ' *');

            // Icepay uses inline style and oddly positioned images. Correct.
            jQuery('li[class*="ICEPAY"]').each(function () {
                jQuery(this).find('input, select').attr('style', '');
                jQuery(this).find('img').appendTo(jQuery(this).find('label'));
            });

            // Kill amazon checkout borders.
            jQuery('.widget-container').find('iframe').css('border', 'none');
        });
    }
}

function mobilestoreModifyCheckoutSelects() {
    if (isCartPage || isCheckoutPage || isAccountPage) {
        var subject_fields = ['#billing_country', '#shipping_country', '#billing_state', '#shipping_state'];

        for (var i = 0; i < subject_fields.length; i++) {
            jQuery(subject_fields[ i ]).siblings('i.wptouch-icon-arrow-combo').remove();
            if (jQuery(subject_fields[ i ]).is('select')) {
                jQuery(subject_fields[ i ]).after('<i class="wptouch-icon-arrow-combo"></i>');
            }
        }

        jQuery('select').change(function () {
            mobilestoreModifyCheckoutSelects();
        });
    }
}

// Use placeholders instead of labels above form elements, and then reveal them when you intereact with the field
function mobilestoreTransformLabels() {
    jQuery('label').each(function () {
        //	e.preventDefault();

        fieldTarget = jQuery(this).attr('for');
        if (typeof fieldTarget != 'undefined') {
            if (fieldTarget.indexOf('\%') != -1) {
                fieldTarget = fieldTarget.replace(/\%/g, '\\%');
            }

            var fieldName = jQuery('#' + fieldTarget).attr('name');
            if (typeof fieldName == 'undefined' || fieldName.indexOf('attribute') == -1) {
                myTarget = jQuery(this).attr('for');

                if (jQuery('#' + myTarget).val() == '') {
                    jQuery(this).hide();
                    jQuery('#' + myTarget).addClass('label-hidden');
                }

                if (!jQuery('#' + myTarget).is('select')) {
                    if (jQuery(this).text() != "") {
                        jQuery('#' + myTarget).attr('placeholder', jQuery(this).text())
                    }

                    jQuery('#' + myTarget).keyup(function () {
                        if (jQuery(this).val() != '' && jQuery(this).hasClass('label-hidden')) {
                            jQuery(this).removeClass('label-hidden').siblings('label').animate({'height': 'toggle'}, 400).css('display', 'block');
                        } else if (jQuery(this).val() == '') {
                            jQuery(this).addClass('label-hidden');
                            jQuery(this).siblings('label').hide();
                        }
                    });
                }
            }
        }
    });

    // Change the location of commentform labels to make them work with the above code^
    jQuery('#commentform label').each(function () {
        jQuery(this).insertBefore(jQuery(this).siblings('input'));
    });
}

function mobilestoreRatings() {
    // Summary Rating Average
    var starDiv = jQuery('.summary .star-rating');
    var starNumber = Math.round(jQuery('strong', starDiv).text());

    starDiv.empty().text('Rating: '); // you can use empty() method

    for (stars = 0; stars < starNumber; stars++) {
        starDiv.append('<i class="wptouch-icon-star"></i>');
    }

    // Reviews
    var starRateDiv = jQuery('.comment-text .star-rating span');

    starRateDiv.each(function () {
        var starAmt = jQuery('strong', this).text();
        jQuery(this).empty();
        for (stars = 0; stars < starAmt; stars++) {
            jQuery(this).parent().append('<i class="wptouch-icon-star"></i>');
        }
    });

    var starsAnchors = jQuery('p.stars a');
    starsAnchors.on('click', function () {
        starsAnchors.removeClass('fill');
        jQuery(this).prevAll('a').addClass('fill');
    });
}

function mobileStoreWebAppBackButton() {
    // If we're in Web-App-Mode
    if (window.navigator.standalone) {
        var startPosition = 0;
        var backButton = jQuery('.back-button');

        if (backButton.is('div')) {
            jQuery(window).scroll(function () {
                var newPosition = jQuery(this).scrollTop();
                if (newPosition > startPosition) {
                    backButton.removeClass('visible');
                } else {
                    if (!backButton.hasClass('visible')) {
                        backButton.addClass('visible');
                    }
                }
                startPosition = newPosition;
            });
        }
    }
}

function mobilestoreSetScrollPos() {
    page_wrapper.on('click', '#content ul.products a', function () {
        scrollPos = jQuery('body').scrollTop();
        jQuery.bbq.pushState({scroll: scrollPos});
    });
}

function mobileStoreSwitchLayout() {
    if (!wptouchReadCookie(currentDomain + '-mobilestore-layout')) {
        if (jQuery('body').hasClass('list-view')) {
            wptouchCreateCookie(currentDomain + '-mobilestore-layout', 'vertical', 365);
        } else {
            wptouchCreateCookie(currentDomain + '-mobilestore-layout', 'masonry', 365);
        }
    }

    var layoutButtons = jQuery('.layout-buttons');
    var container = jQuery('.archive .products.main, .page .products.main, .single .products.main, .search .products.main');

    if (wptouchReadCookie(currentDomain + '-mobilestore-layout') == 'vertical') {
        jQuery('i.layout-list', layoutButtons).addClass('active');
    } else {
        jQuery('i.layout-masonry', layoutButtons).addClass('active');
    }

    layoutButtons.on('click', 'i', function () {

        jQuery('i', layoutButtons).removeClass('active');
        jQuery(this).addClass('active');
        if (jQuery(this).hasClass('layout-masonry')) {
            jQuery('li', container).removeClass('vertical');
            container.isotope({layoutMode: 'masonry'});
            wptouchCreateCookie(currentDomain + '-mobilestore-layout', 'masonry', 365);
        } else {
            jQuery('li', container).addClass('vertical');
            container.isotope({layoutMode: 'vertical'});
            wptouchCreateCookie(currentDomain + '-mobilestore-layout', 'vertical', 365);
        }
    });
}

function mobileStoreReplaceCachedMiniCart() {
    if (jQuery('body').hasClass('infinity-cache-active')) {
        if (wptouchMain.query_vars[ 'lang' ] != undefined) {
            append_lang = '&lang=' + wptouchMain.query_vars[ 'lang' ];
        } else {
            append_lang = '';
        }
        ;

        jQuery('#menu-right').load('?wptouch_mobilestore_action=refresh_cart' + append_lang);
    }
}

function mobileStoreTabletChanges() {
    tabletPage = jQuery('body.tablet');
    tabletProductTop = jQuery('.product-top ', tabletPage);
    jQuery('h1', tabletProductTop).prependTo('.summary');
    jQuery('div[itemprop=offers]', tabletProductTop).remove();
    jQuery('.summary', tabletPage).insertBefore(tabletProductTop);
    jQuery('.summary', tabletPage).css('min-height', tabletProductTop.height());
}

function mobilestoreOwlCarousel() {
    if (jQuery().owlCarousel) {
        var use_rtl = false;
        if (jQuery('body').hasClass('rtl')) {
            use_rtl = true;
        }
        jQuery('.product-carousel .owl-carousel').owlCarousel({
            rtl: use_rtl,
            dots: false,
            nav: false,
            items: 5,
            loop: false,
            margin: 10,
            stagePadding: 0,
            responsive: {
                300: {items: 2},
                340: {items: 3},
                450: {items: 5},
                900: {items: 7}
            }
        });

        jQuery('.product-slider .owl-carousel').owlCarousel({
            rtl: use_rtl,
            nav: false,
            items: 1,
            loop: true,
            autoplay: true
        });
    }
}

function mobilestoreReady() {
    mobilestoreHandleHeaderText();
    mobilestoreLoadMore();
    mobilestoreSetScrollPos();
    mobilestoreIsotope();
    mobileStoreSwitchLayout();
    mobilestoreModifyQtyButtons();
//    mobilestoreSetupHammer();
    mobilestoreHandleSearch();
    mobilestoreHandleAddToCart();
    mobilestoreMiniCartButtons();
    mobileStoreReplaceCachedMiniCart();
    mobilestoreProductSummaryArea();
    mobilestoreRatings();
    mobilestoreHandleWooVariationImages();
    mobilestoreModifyCheckoutSelects();
    mobilestoreTransformLabels();
    mobilestoreSetupCheckout();
    mobileStoreWebAppBackButton();
//	mobileStoreTabletChanges();
    mobilestoreWebApp();
    mobilestoreOwlCarousel();

    jQuery('.back-to-top').appendTo('.footer');

    mobilestoreSplitFooter();
    mobilestoreMoveFooterDiv();
    mobilestoreBindTappableLinks();
}

jQuery(document).ready(function () {
    mobilestoreReady();
});
