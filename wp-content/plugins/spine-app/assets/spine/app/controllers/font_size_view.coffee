Spine           = require('spine')
$               = Spine.$
Settings        = require('models/settings')

class FontSizeView extends Spine.Controller
    
    elements:
        '.opt-font-small'       : 'smallFontEl'
        '.opt-font-medium'      : 'mediumFontEl'
        '.opt-font-large'       : 'largeFontEl'

    events:
        'click [class*="opt-font-"]'    : 'click'

    constructor: ->
        super
        
        Settings.fetch(empty:true)
        
        @FONT_SIZE =
            SMALL:
                name: 'small'
                val: @small,
            MEDIUM:
                name: 'medium'
                val: @medium,
            LARGE:
                name: 'large'
                val: @large
            
    render: (size) ->
        $('body').css 'font-size', @FONT_SIZE[size].val
        data =
            sizes: @FONT_SIZE
            size: size.toLowerCase()
            
        @html require("views/font_size_view") data
        @refreshElements()
        
    getFontSize: ->
        settings = Settings.first()
        settings.fontSize
        
    setFontSize: (size = @getFontSize()) ->
        settings = Settings.first()
        settings.updateAttribute 'fontSize', size
        @render(size)

    click: (e) ->
        e.preventDefault()
        e.stopPropagation()
        
        el = $(e.target)
        data = el.data('font-size-name')
        name = data.toUpperCase()
            
        @setFontSize name
        
module.exports = FontSizeView
