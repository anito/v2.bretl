jQuery( function( $ ) {
	
        //override mobilestoreSetupHammer function in mobilestores.js
        //in order not to break magigtoolbox scripts
        var mobilestoreSetupHammer = function() {
            return false;
        }
        
	//Init Adult Badges
	var add_adult_badge = function() {
		$( '.product_tag-ab-18 .product-top' ).each(function($i) {
			$(this).append('<i class="adult"><i>');
		})
	}
	
	var add_background_image = function() {
		$( '.page-wrapper' ).addClass('camouflage');
	}
	
	//pimp search field
	var add_search_badge_el = function() {
		$( '#searchform' ).prepend('<span class="icon"><i class="wptouch-icon-search"></i></span>');
	}
	
	//add readmore behaviour
	var add_readmore = function() {
            $('#tab-description h2 + p').readmore({
              speed: 75,
              collapsedHeight: 200,
              moreLink: '<a href="#">[mehr lesen]</a>',
              lessLink: '<a href="#">[weniger lesen]</a>'
            });
        }
	
	
	add_adult_badge();
	add_background_image();
	add_search_badge_el();
	add_readmore();
	
});