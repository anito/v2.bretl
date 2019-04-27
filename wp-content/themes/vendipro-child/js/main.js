(function ($) {
    'use strict';
    
    var $overlay = $('<div>', {
        'class': 'js--overlay'
    }).appendTo('body'),
            isOpen = false,
            openClass = 'is--open',
            closableClass = 'is--closable',
            events = ['click', 'touchstart', 'MSPointerDown'].join('.overlay') + '.overlay',
            openOverlay = function (options) {
                if (isOpen) {
                    updateOverlay(options);
                    return;
                }
                isOpen = true;
                $overlay.addClass(openClass);
                if (options && options.closeOnClick !== false) {
                    $overlay.addClass(closableClass);
                }
                $overlay.on(events, $.proxy(onOverlayClick, this, options));
            },
            closeOverlay = function () {
                if (!isOpen) {
                    return;
                }
                isOpen = false;
                $overlay.removeClass(openClass + ' ' + closableClass);
                $overlay.off(events);
            },
            onOverlayClick = function (options) {
                if (options) {
                    if (typeof options.onClick === 'function') {
                        options.onClick.call($overlay);
                    }
                    if (options.closeOnClick === false) {
                        return;
                    }
                    if (typeof options.onClose === 'function' && options.onClose.call($overlay) === false) {
                        return;
                    }
                }
                closeOverlay();
            },
            updateOverlay = function (options) {
//            $overlay.toggleClass(closableClass, options.closeOnClick !== false);
                $overlay.off(events);
//            $overlay.on(events, $.proxy(onOverlayClick, this, options));
            };
    $overlay.on('mousewheel DOMMouseScroll', function (event) {
        event.preventDefault();
    });
    $.overlay = {
        open: openOverlay,
        close: closeOverlay,
        isOpen: function () {
            return isOpen;
        },
        getElement: function () {
            return $overlay;
        }
    };
})(jQuery);

(function ($) {

    
    var add_fb_div = function () {
        $('body').prepend('<div id="fb-root"></div>');
    }

    var add_background_image = function () {
        $('body').addClass('');
    }

    //pimp search aws field
    var add_aws_badge_el = function () {
        $('.aws-search-form').prepend('<span class="icon"><i class="fa fa-search"></i></span>');
    };

    //add disclaimer to MagicToolbox Container
    var disclaimer = "Abbildung kann ähnlich sein. Änderungen und Irrtümer vorbehalten";
    var add_image_disclaimer = function () {
        $('.product .images').prepend('<span class="disclaimer">' + disclaimer + '</span>');
    }

    //add cart sidebar behaviour
    var add_mini_cart = function () {
        var sidebar = $('.cart-sidebar'),
            overlay = $('.js--overlay');
    
        $(document).on('click', '.mini-cart', function (e) {
            e.preventDefault();

            sidebar.addClass('is--open');
            $.overlay.open();
            return false;

        });
        $(overlay).on('click', function (e) {
            $.overlay.close();
            sidebar.removeClass('is--open');
            return false;
        });
//            $(document.body).on('added_to_cart', function(e, fragments, cart_hash, button) {
//                    e.preventDefault();
//                    $('.mini-cart').click()
//            });
        $(document.body).on('quantity_updated', function (e) {
            e.preventDefault();
            $('.mini-cart').click();
        });
        $(document.body).on('updated_wc_div', function (e) {
            e.preventDefault();
            $('.mini-cart').click();
        });
        $(document).on('click', '.continue-shopping', function (e) {
            e.preventDefault();

            sidebar.removeClass('is--open');
            $.overlay.close();
            return false;

        });

    };

    //add readmore behaviour
    var add_readmore = function () {
        $('p').readmore({
            speed: 75,
            moreLink: '<a href="#">[mehr lesen]</a>',
            lessLink: '<a href="#">[weniger lesen]</a>'
        });
    };

    // jQuery.blockUI defaults
    var add_jQuery_blocking = function () {
        $.blockUI.defaults.css = {
            message: "Hi"
        };
    };
    
    var add_open_links_in_new_window_icon = function() {
        // for the document-gallery
        var new_window, array=[];
        $('.document-gallery').each( function() {
            new_window = $(this).data('shortcode')['new_window'];
            if( new_window ) array.push($(this));
        })

        $(array).each(function() {
            $(this).find('.document-icon').addClass('new-window');
        })
    }
        
    
    // depends on utils.js in child-theme/js
    var add_boxify = function() {
        
        var excludeClasses = [
            '.woocommerce-checkout',
            '.woocommerce-multistep-checkout',
            '.woocommerce-cart',
            '.blog'
        ],
        
        ignoreClasses = [
            'page-link'
        ]
        
        $('body').informify();
        $('body').textify(excludeClasses, ignoreClasses);
        
    }

    var add_gsap_1 = function() {

        var mainTl          = new TimelineMax({repeat: -1, repeatDelay: 10, paused: false, onStart: init}),
            tractorTiltTl   = new TimelineMax({paused: true}),
            carTiltTl       = new TimelineMax({paused: true}),
            backupAndGoTl   = new TimelineMax({paused: true, autoRemoveChildren: true}),
            buttonPlayTl    = new TimelineMax({paused: true}),
            buttonPauseTl   = new TimelineMax({paused: true}),
            car             = $('#car').data({'origins': {transformOrigin: '50% 130%'}, scaleX: 0.8, scaleY: 0.6}).css({left: '2000px'}),
            racingCar       = $('#racing-car').data({'origins': {transformOrigin: '0% 160%'}, scaleX: 1.4, scaleY: 1.4}).css({left: '2000px'}),
            truck           = $('#truck').data({'origins': {transformOrigin: '100% 100%'}, scaleX: 1.5, scaleY: 1.4}).css({left: '2000px'}),
            tractor         = $('#tractor').data({'origins': {transformOrigin: '50% 30%'}, scaleX: 1.1, scaleY: 1.1}).css({left: '2000px'}),
            btnPause        = $('.btn-gsap-pause').css({fontSize: '2.5em'}),
            btnPlay         = $('.btn-gsap-play').css({fontSize: '2.5em'}),
            btnResume       = $('.btn-gsap-resume').css({display: 'none', fontSize: '2.5em'}),
            playOrPause;
            
        playOrPause();
//        GSDevTools.create();

        function playOrPause() {
            var paused = mainTl.paused();
            if(paused) {
                buttonPlayTl.play(0)
            } else {
                buttonPauseTl.play(0)
            }
        }
        btnPause.on('click', $.proxy(function(e) {
            mainTl.pause();
            backupAndGoTl.pause();
            playOrPause();
        }))
        btnPlay.on('click', $.proxy(function() {
            mainTl.resume();
            backupAndGoTl.resume();
            playOrPause();
        }))
        // TODO restart issues
        btnResume.on('click', function() {
            tractorTiltTl.kill();
            carTiltTl.kill();
            backupAndGoTl.invalidate();
            mainTl.kill().restart();
            playOrPause();
        })
        
        var buttonPlayTw   = new TweenMax( btnPlay, 1,
            {
                scale: 1.2,
                repeat: -1,
                repeatDelay: 0,
                yoyo: true
            }
        )
        var buttonPauseTw   = new TweenMax( btnPause, 1,
            {
                rotationZ: 360,
                repeat: -1,
                repeatDelay: 2,
            }
        )
        var truckTw   = new TweenMax( truck, 0,
            {
                scaleX: 1.5,
                transformOrigin: '100% 50%'
            }
        )
        var bounceTw   = new TweenMax( [car, tractor], .3,
            {
                scaleY: '+=0.1',
                repeat: -1,
                yoyo: true,
                ease: Back.easeOut.config(Math.random()+1)
            }
        )
        
        buttonPauseTl
            .set(btnPlay, {autoAlpha: 0, display: 'none'})
            .set(btnPause, {autoAlpha: 1, display: 'inline-block'})
            .set(btnPause, {
                transformOrigin: '47% 52%',
            })
            .add(buttonPauseTw)
            .fromTo(btnPause, 1, {
                scale: 1.5,
                ease: Power4.easeOut 
            }, {
                scale: 1,
                ease: Power4.easeOut 
            })
            
        buttonPlayTl
            .set(btnPause, {autoAlpha: 0, display: 'none'})
            .set(btnPlay, {autoAlpha: 1, display: 'inline-block'})
            .set(btnPlay, {
                transformOrigin: '47% 52%',
            })
            .add(buttonPlayTw)
            .to(btnPlay, 0.3, {
                rotationZ: 120,
                repeat: -1,
                repeatDelay: 1.7,
            }, 3)

        tractorTiltTl
            .add('start')
            .set(tractor, {transformOrigin: '0% 130%'})
            .to(tractor, .3, {rotationZ: -40})
            .to(tractor, .2, {x: '+=80'}, 0)
            .to(tractor, .5, {rotationZ: 0, ease: Bounce.easeOut })
        
        carTiltTl
            .add('start')
            .set(car, {transformOrigin: '0% 130%'})
            .to(car, .3, {rotationZ: -30})
            .to(car, .5, {rotationZ: 0, ease: Bounce.easeOut })

        mainTl
            .fromTo(car, 1, {x: 2000}, {x: 25, ease: Power4.easeOut }, 0.1)
            .call(carRotate, null, null, 0.5 )
            .fromTo(racingCar, 1, {x: 2000}, {x: 140 }, 0.3)
            .to(racingCar, 0.5, {x: '+=30', ease: Power4.easeOut })
            .fromTo(truck, 1, {x: 2000}, {x: 340 }, 1)
            .to(truck, 0.5, {x: '+=20', ease: Power4.easeOut })
            .fromTo(tractor, 1, {x: 2000}, {x: 450, ease: Power4.easeIn, onComplete: tractorRotate }, 1.5)
            .add('tractor-hit')
            .to(truck, 0.1, {x: 300})
            .to(truck, 0.5, {x: '+=70', ease: Power4.easeOut })
            .to(tractor, 0.5, {x: '-=30', ease: Power4.easeOut })
            .staggerTo([car, racingCar, truck, tractor], 0.4, {
                onStart: backupAndGo
            }, 0.1)
            
        function init() {

            this
                .to([car, racingCar, truck, tractor], 0, {
                    x: 2000,
                    left: 0,
                    rotationZ: 0,
                    opacity: 1,
                }, 0)
                .to(car, 0, {
                    scaleX: car.data('scaleX'),
                    scaleY: car.data('scaleY'),
                    y: 5,
                    transformOrigin: car.data('origins'),
                }, 0)
                .to(racingCar, 0, {
                    scaleX: racingCar.data('scaleX'),
                    scaleY: racingCar.data('scaleY'),
                    y: -10,
                    transformOrigin: racingCar.data('origins'),
                }, 0)
                .to(truck, 0, {
                    scaleX: truck.data('scaleX'),
                    scaleY: truck.data('scaleY'),
                    y: -10,
                    transformOrigin: truck.data('origins'),
                }, 0)
                .to(tractor, 0, {
                    scaleX: tractor.data('scaleX'),
                    scaleY: tractor.data('scaleY'),
                    y: 0,
                    transformOrigin: tractor.data('origins'),
                }, 0)
        }
            
        // auto-cleans up after completion
        function backupAndGo() {

            var label = this.target.selector,
                isTractor = label === '#tractor' ? true : false,
                duration = isTractor ? 3 : 1,
                ease = isTractor ? Power0.easeIn : Power3.easeIn;
                
            backupAndGoTl
                .to(this.target, 0.4, {x: '+=20'})
                .add(label)
                .to(this.target, duration, {
                    x: -350,
                    ease: ease,
                })
                if (this.target.selector === '#tractor') {
                    backupAndGoTl
                    .to(this.target, 0.15, {
                        y: '-=30',
                        ease: Power3.easeIn,
                    }, label+'+=0.5')
                    .to(this.target, 0.15, {
                        y: 0,
                        ease: Bounce.easeOut,
                    }, label+'+=0.65')
                    .to(this.target, 0.8, {
                        transformOrigin: '80% 100%',
                        rotationZ: 40
                    }, label+'+=0.3')
                    .to(this.target, 1, {
                        rotationZ: 0,
                        transformOrigin: '70% 130%',
                        ease: Bounce.easeOut
                    }, label+'+=1')
                }
            backupAndGoTl.play();
        }

        function carRotate() {
            carTiltTl.play('start', )
        }

        function tractorRotate() {
            tractorTiltTl.play('start')
        }

        function tractorJump() {
            tractorTiltTl.play('start')
        }
    }
    
    var add_gsap_2 = function() {
        
//        var morph = 
        
    }

    add_fb_div();
    add_background_image();
    add_aws_badge_el();
    add_mini_cart();
    add_image_disclaimer();
    add_jQuery_blocking();
    add_open_links_in_new_window_icon();
    add_boxify();
    add_gsap_1();
    add_gsap_2();
    // add_readmore();
    

})(jQuery)
