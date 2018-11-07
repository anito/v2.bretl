Spine = require("spine")
$      = Spine.$

class ModalSimpleView extends Spine.Controller
  
  elements:
    '.modal-header'       : 'header'
    '.modal-body'         : 'body'
    '.modal-footer'       : 'footer'
  
  events:
    'click .opt-btn-close'     : 'close'
    'hidden.bs.modal'          : 'hiddenmodal'
    'show.bs.modal'            : 'showmodal'
    'shown.bs.modal'           : 'shownmodal'
    'keydown'                  : 'keydown'
  
  constructor: ->
    super
    modalOptions = if @options? then @options else {}
    
    modalDefaults =
      keyboard: true
      show: false
      
    @defaults =
      small     : true
      header    : "Ich bin ein Header"
      body      : 'Default Body Text'
      callback  : ->
        
    options = $.extend modalDefaults, modalOptions
    
    @setup options
    
#    @render()
    
  render: (options = {callback: ->}) ->
    @log 'render'
    options = $.extend @defaults, options
    @callback = options.callback
    @html require("views/simple_view") options
    @refreshElements()
    @
    
  setup: (options = {}) ->
    @el.modal(options)
    
  hiddenmodal: ->
    @log 'hiddenmodal...'
  
  showmodal: ->
    @log 'showmodal...'
    
  shownmodal: ->
    @log 'shownmodal...'
    
  keydown: (e) ->
    @log 'keydown'
    
    code = e.charCode or e.keyCode
        
    switch code
      when 32 # SPACE
        e.stopPropagation() 
      when 9 # TAB
        e.stopPropagation()
      when 27 # ESC
        e.stopPropagation()
      when 13 # RETURN
        @close()
        e.stopPropagation()
      
  show: ->
    @el.modal('show')
    
  close: (e) ->
    @el.modal('hide')
    @callback()
    
module?.exports = ModalSimpleView