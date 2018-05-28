require('lib/setup')

Spine               = require('spine')
$                   = Spine.$
Settings            = require('models/settings')
ModalSimpleView     = require("controllers/modal_simple_view")
HeaderView          = require("controllers/header_view")
FontSizeView        = require("controllers/font_size_view")

class App extends Spine.Controller

    elements:
        '#logo > a.logo-img'    : 'logoImgEl'
        '#logo > a.logo-text'   : 'logoTextEl'
        '#logo > a'             : 'logoEls'
        '#wpadminbar'           : 'wpadminbar'
        
    constructor: ->
        super
        
        Settings.fetch()
        @modalView          = new ModalSimpleView
            el: $('#modal-view')
        @fontSizeView       = new FontSizeView
            el: $('.font-sizes')
            small   : @smallFontSize
            medium  : @mediumFontSize
            large   : @largeFontSize
            default : @defaultFontSize
        @headerView         = new HeaderView
            el: $('.header-view')
        
        Spine.bind('clicked:checkbox', @proxy @handleCheckbox)
        
        settings = @loadSettings()
        
        @setLogo settings.logo
        @setGradient settings.gradient
        @setAdminBar settings.wpadminbar
        @setAdmin()
        @setProduction()
        @fontSizeView.setFontSize()
        @notify('
            <h1>Achtung!</h1>
            <p>Dieser Shop ist lediglich eine</p>
            <h4 class="badge-pill badge-warning">Demo</h4>
            <p>Hier angebotene Waren können NICHT käuflich erworben werden.</p>
            <p>Personen, Waren, Anschriften, Email-Adressen sowie Bankverbindungen sind rein fiktiv.</p>') if settings.showHint
        
        @headerView.render()
        
    loadSettings: ->
        
        settings = Settings.load() || Settings.create
            logo        : @hasLogo()
            gradient    : @hasGradient()
            wpadminbar  : @hasAdminBar()
            isAdmin     : @isAdmin
            isProduction: @isProduction
            fontSize    : @defaultFontSize
            showHint    : true
            
        
    notify: (text) ->
        @modalView.render
          small: true
          header: 'Demonstrationsmodus!'
          body: require("views/notify") text: text
          footer:
            footerButtonText: 'Alles klar'
          callback: =>
            @hideHint()
        .show()
        
    handleCheckbox: (type) ->
        switch type
            when 'logo'
                @setLogo()
            when 'gradient'
                @setGradient()
            when 'wpadminbar'
                @setAdminBar()
        @headerView.render()
    
    hasLogo: ->
        @logoImgEl.hasClass('logo-active');
    
    hasGradient: ->
        @el.hasClass('gradient-active');
    
    hasAdminBar: ->
        !@wpadminbar.hasClass('hide');
    
    setLogo: (newState = !@hasLogo()) ->
        @logoImgEl.toggleClass('logo-active', newState)
        @logoTextEl.toggleClass('logo-active', !newState)
        @el.toggleClass('logo-active', newState)
        settings = Settings.first()
        settings.logo = @hasLogo()
        settings.save()
        
    setGradient: (newState = !@hasGradient()) ->
        @el.toggleClass('gradient-active', newState)
        settings = Settings.first()
        settings.gradient = @hasGradient()
        settings.save()
                
    setAdminBar: (newState = !@hasAdminBar()) ->
        @wpadminbar.toggleClass('hide', !newState)
        $('html').toggleClass('nomargin', !newState)
        settings = Settings.first()
        settings.wpadminbar = @hasAdminBar()
        settings.save()
        
    setAdmin: ->
        settings = Settings.first()
        settings.updateAttribute 'isAdmin', @isAdmin
                
    setProduction: ->
        settings = Settings.first()
        settings.updateAttribute 'isProduction', @isProduction
        
    hideHint: ->
        settings = Settings.first()
        settings.updateAttribute 'showHint', false
        
        
                
module.exports = App

