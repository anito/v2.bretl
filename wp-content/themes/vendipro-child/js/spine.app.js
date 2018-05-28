(function ($, exports) {
    'use strict';
    
    
    var initApp = function() {
        var App = require("index");
        exports.app = new App({el: $("body")});
    }
        
    initApp();

})(jQuery, this)
