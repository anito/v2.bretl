# jQuery.tmpl.js utilities

$ = jQuery ? require("jqueryify")

$.fn.guid = ->
    mask = [8, 4, 4, 4, 12]

    ret = []
    ret = for sub in mask
        res = null
        milli = new Date().getTime();
        back = new Date().setTime(milli*(-200))
        diff = milli - back
        re1 = diff.toString(16).split('')
        re2 = re1.slice(sub*(-1))
        re3 = re2.join('')
        re3

    re4 = ret.join('-')
    re4

$.fn.uuid = ->
    s4 = -> Math.floor((1 + Math.random()) * 0x10000).toString(16).substring(1)
    s4() + s4() + '-' + s4() + '-' + s4() + '-' + s4() + '-' + s4() + s4() + s4()

$.fn.deselect = (sel) ->
    $(@).children(sel).removeClass('active hot')
  
$.extend jQuery.tmpl.tag,
    "for": 
        _default: {$2: "var i=1;i<=1;i++"},
        open: 'for ($2){',
        close: '};'
  
$.fn.isFormElement = (o=[]) ->
    str = Object::toString.call(o[0])
    formElements = ['[object HTMLInputElement]','[object HTMLTextAreaElement]']
    formElements.indexOf(str) isnt -1

$.fn.state = (state) ->
    d = 'disabled'
    @each ->
        $this = $(@)
        $this.html( $this.data()[state] )
        if state is 'loading'
            return $this.addClass(d).attr(d,d)
        else
            return $this.removeClass(d).removeAttr(d)
    
$.fn.unparam = (value) ->
    # Object that holds names => values.
    params = {}
    # Get query string pieces (separated by &)
    pieces = value.split('&')

    for piece in pieces
        pair = piece.split('=', 2)
        params[decodeURIComponent(pair[0])] = if pair.length is 2 then decodeURIComponent(pair[1].replace(/\+|false/g, '')) else true
    params
  
# build textboxes upon occurance of h3 ~ p, h3 ~ div
$.fn.textboxify = (excludeBodyClasses=[], excludeTags=[]) ->
    
    selector = @.selector
    exclude = () ->
            
        ret = ''
        for val in excludeBodyClasses
            ret += ":not( #{val} )"
        ret
            
    include = (el, selectors) ->
        selectors = selectors.replace(/\s/g,'').split(',')
        ret = []
        for val in excludeBodyClasses
            for v, j in selectors
                ret.push("#{el}#{val} #{selectors[j]}")
            
        ret = ret.join()
            
    $(selector + exclude() + ' h3').each (i) ->
    
        header = $("<div class=\"textbox-header-wrapper textbox-header-wrapper-id-#{i} \"></div>")
        content = $('<div class="textbox-content-wrapper"></div>')
        h5 = null
        h3 = $(@)
            
        # iterate through all the adjacent siblings no matter what, starting at H3
        $('~ *', h3).each (j) ->
                
            wrapper = $("<div class=\"woocommerce textbox-wrapper textbox-wrapper-id-#{i}\"></div>")
            that = $(@);
                
            if( that.is('h3') or that.is('h6') ) # stop here

                return false

            else if(that.is('h5') and $('+ h5', h3).length) # found an H5

                h5 = that;

            else if(that.is('p') or that.is('div')) # Pees or the Div

                that.addClass("textbox-id-#{i}")

                hasMedia = that.children('iframe, img').length
                if(hasMedia) then that.addClass('media');

                if $('+ p, + div', that).length # there are more Pees to follow
                    that.addClass('textbox-item')
                    content.append(that) # wrap the Pees

                else # last Pee or the Div found

                    h3.addClass("textbox-header textbox-id-#{i}")
                    h3.before(wrapper) # remember the position of our H3 in the DOM
                    wrapper.append(h3) # move H3

                    if(h5) # move H5
                        h5.addClass("textbox-subline textbox-id-#{i}")
                        wrapper.append(h5)

                    if(!that.hasClass( excludeTags ) )
                        content.append(that)
                        that.addClass('textbox-item textbox-item-last')

                    wrapper.append(content) # move content wrapper
                    h3.wrap(header)

                    wrapper.addClass('processing').block
                        message: 'Bitte warten'
                        overlayCSS:
                            background: '#00000022'
                            opacity: 0.6
                            
    $( document ).ready () ->
        $('.textbox-wrapper').addClass('initialized').removeClass('processing').unblock()
        incl = include(selector, 'h3 ~ h5, h3 ~ p, h3 ~ div')
        $(incl).css( 'opacity': 1, 'visibility': 'visible' )
        
$.fn.informify = () ->

    selector = @.selector
    $(selector + ' h6').each (i) ->
    
        h6 = $(@)
            
        # iterate through all the adjacent siblings no matter what, starting at H3
        $('~ *', h6).each (j) ->
                
            wrapper = $("<div class=\"effect information-wrapper information-wrapper-id-#{i}\"></div>")
            that = $(@);
                
            if( that.is('h6') ) # stop here

                return false
            
            else if( that.is('p') or that.is('div') ) # the Pees or the Div
            
                that.addClass("information-id-#{i}")
                
                h6.before(wrapper) # remember the position of our H6 in the DOM
                wrapper.append(h6) # move H3
                wrapper.append(that) # move the Pee / Div
                
                return false