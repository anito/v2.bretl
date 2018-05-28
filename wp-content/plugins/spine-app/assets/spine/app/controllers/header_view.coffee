Spine           = require('spine')
$               = Spine.$
Settings        = require('models/settings')

class HeaderView extends Spine.Controller
    
    elements:
        'input[data-type="logo"]'       : 'logoCb'
        'input[data-type="gradient"]'   : 'gradientCb'
        'input[data-type="wpadminbar"]' : 'wpadminbarCb'

    events:
        'click [class*="opt-"]'    : 'toggleCheckbox'

    constructor: ->
        super
        
        Settings.fetch(empty:true)
        
    render: (settings = Settings.first()) ->
        @html require("views/header_view") settings
        @refreshElements()
        
    toggleCheckbox: (e) ->
        e.stopPropagation()
        e.preventDefault()
        
        target = $(e.target)
        type = target.parent('div').find('input').data("type")
        Spine.trigger('clicked:checkbox', type)

module.exports = HeaderView
