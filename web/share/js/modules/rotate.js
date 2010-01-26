// Fx.Rotate
(function(){Fx.Rotate=new Class({Extends:Fx,options:{},regex:/rotate\((\-?[\d\.]+)deg\)/i,initialize:function(b,a){this.element=this.subject=document.id(b);this.parent(a)},set:function(a){var b=(Browser.Engine.webkit)?"WebkitTransform":"MozTransform";this.element.setStyle(b,"rotate("+a+"deg)");return this},start:function(b,a){if(arguments.length==1){a=b;b=this.getRotation()}return this.parent(b,a)},getRotation:function(){var a=(Browser.Engine.webkit)?this.element.getStyle("WebkitTransform"):this.element.getStyle("MozTransform");return(!$chk(a)||a=="none")?0:this.regex.exec(a)[1].toInt()},rotate:function(b,a){var c=this.getRotation(),d=90,a=$defined(a)?a:true;switch(b){case"ccw":d=(c%90==0)?c-90:(c/90).floor()*90;break;case"cw":default:d=(c%90==0)?c+90:(c/90).ceil()*90}if(a){this.start(d)}else{this.set(d)}},spin:function(a){this.toggleSpin(a)},startSpin:function(a){this.oldTransition=this.options.transition;this.options.transition=Fx.Transitions.linear;if($chk(a)){this.oldDuration=this.options.duration;this.options.duration=a}this.cycle();this.spinning=true},stopSpin:function(a){this.cancel();this.options.transition=this.oldTransition;if($chk(a)){this.options.duration=this.oldDuration}this.spinning=false},toggleSpin:function(a){if(this.spinning){this.stopSpin(a)}else{this.startSpin(a)}},cycle:function(){this.start(0,360).chain(function(){this.cycle()}.bind(this))}})})();