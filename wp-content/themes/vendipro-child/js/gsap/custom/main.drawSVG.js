// .to(@target, @length, @object)
var controller = new ScrollMagic.Controller();
var as_tween = TweenMax.from('#end', 20, {
  drawSVG:"0",
  ease: Cubic.easeOut
});


//  Scene
var as_scene = new  ScrollMagic.Scene({
  triggerElement: '#trigger', duration: 3000
})
.setTween(as_tween)
.addTo(controller)