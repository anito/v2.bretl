(function() {
  var $, ModalSimpleView, Spine,
    __hasProp = {}.hasOwnProperty,
    __extends = function(child, parent) { for (var key in parent) { if (__hasProp.call(parent, key)) child[key] = parent[key]; } function ctor() { this.constructor = child; } ctor.prototype = parent.prototype; child.prototype = new ctor(); child.__super__ = parent.prototype; return child; };

  Spine = require("spine");

  $ = Spine.$;

  ModalSimpleView = (function(_super) {
    __extends(ModalSimpleView, _super);

    ModalSimpleView.prototype.elements = {
      '.modal-header': 'header',
      '.modal-body': 'body',
      '.modal-footer': 'footer'
    };

    ModalSimpleView.prototype.events = {
      'click .opt-btn-close': 'close',
      'hidden.bs.modal': 'hiddenmodal',
      'show.bs.modal': 'showmodal',
      'shown.bs.modal': 'shownmodal',
      'keydown': 'keydown'
    };

    function ModalSimpleView() {
      var modalDefaults, modalOptions, options;
      ModalSimpleView.__super__.constructor.apply(this, arguments);
      modalOptions = this.options != null ? this.options : {};
      modalDefaults = {
        keyboard: true,
        show: false
      };
      this.defaults = {
        small: true,
        header: "Default Header Text",
        body: 'Default Body Text',
        callback: function() {}
      };
      options = $.extend(modalDefaults, modalOptions);
      this.setup(options);
    }

    ModalSimpleView.prototype.render = function(options) {
      if (options == null) {
        options = {
          callback: function() {}
        };
      }
      this.log('render');
      options = $.extend(this.defaults, options);
      this.callback = options.callback;
      this.html(require("views/simple_view")(options));
      this.refreshElements();
      return this;
    };

    ModalSimpleView.prototype.setup = function(options) {
      if (options == null) {
        options = {};
      }
      return this.el.modal(options);
    };

    ModalSimpleView.prototype.hiddenmodal = function() {
      return this.log('hiddenmodal...');
    };

    ModalSimpleView.prototype.showmodal = function() {
      return this.log('showmodal...');
    };

    ModalSimpleView.prototype.shownmodal = function() {
      return this.log('shownmodal...');
    };

    ModalSimpleView.prototype.keydown = function(e) {
      var code;
      this.log('keydown');
      code = e.charCode || e.keyCode;
      switch (code) {
        case 32:
          return e.stopPropagation();
        case 9:
          return e.stopPropagation();
        case 27:
          return e.stopPropagation();
        case 13:
          this.close();
          return e.stopPropagation();
      }
    };

    ModalSimpleView.prototype.show = function() {
      return this.el.modal('show');
    };

    ModalSimpleView.prototype.close = function(e) {
      this.el.modal('hide');
      return this.callback() != null;
    };

    return ModalSimpleView;

  })(Spine.Controller);

  if (typeof module !== "undefined" && module !== null) {
    module.exports = ModalSimpleView;
  }

}).call(this);
