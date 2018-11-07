Spine                 = require("spine")
$                     = Spine.$
Model                 = Spine.Model
Log                   = Spine.Log

Model.Extender =

  extended: ->

    Extend =
      
      trace: !Spine.isProduction
      logPrefix: '(' + @className + ')'
      
      selectAttributes: []

      isArray: (value) ->
        Object::toString.call(value) is "[object Array]"

      isObject: (value) ->
        Object::toString.call(value) is "[object Object]"
        
      isString: (value) ->
        Object::toString.call(value) is "[object String]"
        
    Include =
      
      trace: !Spine.isProduction
      logPrefix: @className + '::'
      
        
    @include Log
    @extend Log
    @extend Extend
    @include Include

module?.exports = Model.Extender