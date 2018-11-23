Spine = require('spine')
$       = Spine.$
Model   = Spine.Model
ModelExtender = require('extensions/model_extender')
require('spine/lib/local')

class Settings extends Spine.Model
  @configure 'Settings', 'gradient', 'logo', 'wpadminbar', 'isAdmin', 'isProduction', 'fontSize', 'showHint'
  
  @extend Model.Local
  @extend ModelExtender
  
  init: (instance) ->
  
  @load: ->
    return false unless first = Settings.first()
    
    atts = Settings.attributes
    
    for att in atts
        unless first[att]?
            @log "Model \"" + att.toString() + "\" not found!"
            Settings.destroyAll()
            return false
    first
  
  @findUserSettings: ->
    @log "User: "
    @log User.first()
    
    Settings.findByAttribute('user_id', User.first().id)
  
module.exports = Model.Settings = Settings