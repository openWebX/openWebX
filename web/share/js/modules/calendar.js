var vlaCalendar=new Class({slideDuration:500,fadeDuration:500,transition:Fx.Transitions.Quart.easeOut,startMonday:true,filePath:"/share/inc/",defaultView:"month",style:"",initialize:function(c,a){if(a){$extend(this,a)}this.loading=false;this.container=c=$(c);var b=this;var d="defaultView="+this.defaultView;if(this.picker){if($type(this.prefillDate)=="object"&&this.getInputDate(this.prefillDate)){d+="&pickedDate="+this.getInputDate(this.prefillDate)}if(this.linkWithInput){d+="&gotoPickedDate=1"}}this.u("base",d,function(){b.mainLoader=c.getElement("div[class=loaderA]");b.tempLoader=c.getElement("div[class=loaderB]");b.label=c.getElement("span[class=label]");b.arrowLeft=c.getElement("div[class=arrowLeft]");b.arrowRight=c.getElement("div[class=arrowRight]");b.initializeCalendarFunctions();if(b.picker){if($type(b.prefillDate)=="object"&&b.getInputDate(b.prefillDate)){b.pick(b.prefillDate)}else{if(b.prefillDate==true){b.pick(JSON.decode(b.label.getProperty("date")))}}}},c)},initializeCalendarFunctions:function(){this.resetArrows();var c=JSON.decode(this.mainLoader.getElement("table").getProperty("summary"));var a=this;this.label.removeClass("noHover").set("html",c.label).onclick=c.parent?function(){a.u(c.parent,"ts="+c.ts+"&parent="+c.current,function(){a.fade()})}:null;if(c.hide_left_arrow){this.hideLeftArrow()}else{if(c.hide_right_arrow){this.hideRightArrow()}}this.arrowLeft.onclick=function(){a.u(c.current,"ts="+c.pr_ts,function(){a.slideLeft()})};this.arrowRight.onclick=function(){a.u(c.current,"ts="+c.nx_ts,function(){a.slideRight()})};var b=this.mainLoader.getElements("td");switch(c.current){case"month":if(this.picker){b.each(function(d){d.onclick=function(){a.pick(JSON.decode(d.getProperty("date")));a.mainLoader.getElements("td").each(function(e){e.removeClass("selected")});this.addClass("selected")}})}break;case"year":b.each(function(d){d.onclick=function(){a.u("month","ts="+d.getProperty("ts"),function(){a.fade()})}});break;case"decade":this.label.addClass("noHover");b.each(function(d){d.onclick=function(){a.u("year","ts="+d.getProperty("ts")+"&m_ts="+d.getProperty("m_ts"),function(){a.fade()})}});break}},u:function(f,b,e,d){if(!this.loading&&!this.transitioning){var a=this;this.loading=true;var c=$(d?d:this.tempLoader);b+="&picker="+(this.picker?1:0)+"&startMonday="+(this.startMonday?1:0)+"&style="+this.style;if(this.picker&&this.getInputDate()){b+="&pickedDate="+this.getInputDate()}new Request({method:"post",url:this.filePath+f+".php",onComplete:function(g){c.set("html",g);e();a.loading=false}}).send(b)}},slideLeft:function(){var a=this;this.transitioning=true;this.tempLoader.setStyle("opacity",1).set("tween",{duration:this.slideDuration,transition:this.transition}).tween("margin-left",[-164,0]);this.mainLoader.setStyle("opacity",1).set("tween",{duration:this.slideDuration,transition:this.transition,onComplete:function(){a.transitioning=false}}).tween("margin-left",[0,164]);this.switchLoaders()},slideRight:function(){var a=this;this.transitioning=true;this.mainLoader.setStyle("opacity",1).set("tween",{duration:this.slideDuration,transition:this.transition}).tween("margin-left",[0,-164]);this.tempLoader.setStyle("opacity",1).set("tween",{duration:this.slideDuration,transition:this.transition,onComplete:function(){a.transitioning=false}}).tween("margin-left",[164,0]);this.switchLoaders()},fade:function(b){var a=this;this.transitioning=b?false:true;this.tempLoader.setStyles({opacity:0,"margin-left":0});this.mainLoader.set("tween",{duration:this.fadeDuration,transition:this.transition}).fade("out");this.tempLoader.set("tween",{duration:this.fadeDuration,transition:this.transition,onComplete:function(){a.tempLoader.setStyles({opacity:1,"margin-left":-999});a.transitioning=false}}).fade("in");this.switchLoaders()},switchLoaders:function(){this.mainLoader=this.mainLoader.className=="loaderA"?this.container.getElement("div[class=loaderB]"):this.container.getElement("div[class=loaderA]");this.tempLoader=this.tempLoader.className=="loaderA"?this.container.getElement("div[class=loaderB]"):this.container.getElement("div[class=loaderA]");this.initializeCalendarFunctions()},resetArrows:function(){this.arrowLeft.setStyle("visibility","visible");this.arrowRight.setStyle("visibility","visible")},hideLeftArrow:function(){this.arrowLeft.setStyle("visibility","hidden")},hideRightArrow:function(){this.arrowRight.setStyle("visibility","hidden")}});var vlaDatePicker=new Class({Extends:vlaCalendar,separateInput:false,prefillDate:true,linkWithInput:true,leadingZero:true,twoDigitYear:false,separator:"/",format:"d/m/y",openWith:null,alignX:"right",alignY:"inputTop",offset:{x:0,y:0},style:"",ieTransitionColor:"#ffffff",toggleDuration:350,initialize:function(b,a){if(a){$extend(this,a)}this.element=$(b);if(!this.element){throw"No (existing) element to create a datepicker for specified: new vlaDatePicker(ELEMENT, [options])"}if(this.separateInput){this.element.day=this.element.getElement("input[name="+this.separateInput.day+"]");this.element.month=this.element.getElement("input[name="+this.separateInput.month+"]");this.element.year=this.element.getElement("input[name="+this.separateInput.year+"]")}this.picker=new Element("div",{"class":"vlaCalendarPicker"+(this.style!=""?" "+this.style:"")}).injectTop($(document.body));this.pickerContent=new Element("div",{"class":"pickerBackground"}).injectTop(this.picker);this.parent(this.pickerContent);var c=this;(this.openWith?$(this.openWith):this.element).addEvent("focus",function(){c.show()}).addEvent("click",function(){c.openWith?c.toggle():c.show()}).addEvent("change",function(){c.hide()});document.addEvent("mousedown",function(d){if(c.outsideHide&&c.outsideClick(d,c.picker)){c.hide()}});if(this.linkWithInput){if(this.separateInput){this.element.day.addEvent("keyup",function(){c.linkedUpdate()});this.element.month.addEvent("keyup",function(){c.linkedUpdate()});this.element.year.addEvent("keyup",function(){c.linkedUpdate()})}else{this.element.addEvent("keyup",function(){c.linkedUpdate()})}}this.visible=false;this.outsideHide=false},position:function(){var c,b;switch(this.alignX){case"left":b=this.element.getLeft();break;case"center":var a=this.pickerContent.getStyle("width").toInt()/2;if(a==0){a=83}b=this.element.getLeft()+(this.element.getSize().x/2)-a-((parseInt(this.pickerContent.getStyle("padding-left"))+parseInt(this.pickerContent.getStyle("padding-right")))/2);break;case"right":default:b=this.element.getLeft()+this.element.getSize().x;break}switch(this.alignY){case"bottom":c=this.getPos(this.element).y+this.element.getSize().y;break;case"top":c=this.getPos(this.element).y-parseInt(this.pickerContent.getStyle("height"))-(parseInt(this.pickerContent.getStyle("padding-top"))+parseInt(this.pickerContent.getStyle("padding-bottom")));break;case"inputTop":default:c=this.getPos(this.element).y}if(this.isNumber(this.offset.x)){b+=this.offset.x}if(this.isNumber(this.offset.y)){c+=this.offset.y}this.picker.setStyles({top:c,left:b})},show:function(){this.position();if(!this.visible){this.visible=true;var a=this;this.picker.setStyles({opacity:0,display:"inline"});if(Browser.Engine.trident5){this.picker.setStyle("background-color",this.ieTransitionColor)}this.picker.set("tween",{onComplete:function(){if(Browser.Engine.trident5){a.picker.setStyle("background-color","transparent")}a.outsideHide=true},duration:this.toggleDuration}).fade("in")}},hide:function(){if(this.visible){this.visible=false;var a=this;if(Browser.Engine.trident5){this.picker.setStyle("background-color",this.ieTransitionColor)}this.picker.set("tween",{onComplete:function(){a.picker.setStyle("display","none");a.outsideHide=false},duration:this.toggleDuration}).fade("out")}},toggle:function(){if(this.visible){this.hide()}else{this.show()}},pick:function(a){if(this.leadingZero){if(a.day<10){a.day="0"+a.day}if(a.month<10){a.month="0"+a.month}}if(this.twoDigitYear){a.year=a.year.toString().substring(2,4)}if(this.separateInput){if(this.element.day){this.element.day.set("value",a.day)}if(this.element.month){this.element.month.set("value",a.month)}if(this.element.year){this.element.year.set("value",a.year)}this.hide()}else{switch(this.format){case"m/d/y":this.element.set("value",a.month+this.separator+a.day+this.separator+a.year);break;case"y/m/d":this.element.set("value",a.year+this.separator+a.month+this.separator+a.day);break;case"y/d/m":this.element.set("value",a.year+this.separator+a.day+this.separator+a.month);break;case"d/m/y":default:this.element.set("value",a.day+this.separator+a.month+this.separator+a.year)}this.hide()}},getInputDate:function(a){var b,e,d;if(a){b=a.day;e=a.month;d=a.year}else{if(this.separateInput){b=this.element.day.get("value").toInt();e=this.element.month.get("value").toInt();d=this.element.year.get("value").toInt()}else{var c=this.element.get("value").split(this.separator);if(c.length!=3){return null}switch(this.format){case"m/d/y":b=c[1];e=c[0];d=c[2];break;case"y/m/d":b=c[2];e=c[1];d=c[0];break;case"y/d/m":b=c[1];e=c[2];d=c[0];break;case"d/m/y":default:b=c[0];e=c[1];d=c[2]}}}if(!this.isNumber(b)||!this.isNumber(e)||!this.isNumber(d)||b==0||e==0||d=="0"||(this.twoDigitYear&&d>99)||(!this.twoDigitYear&&d<1979)||(!this.twoDigitYear&&d>2030)||e>12||b>31){return null}if(this.twoDigitYear&&this.isNumber(d)&&d<100){d=d.toInt();if(d<10){d="200"+d}else{if(d<70){d="20"+d}else{if(d>69){d="19"+d}else{d=new Date().getFullYear()}}}}return b+"/"+e+"/"+d},linkedUpdate:function(){var a=this;var b=this.getInputDate();if(b&&this.pickedDate!=b){this.u("month","gotoPickedDate=1",function(){a.fade(true)});this.pickedDate=b}},outsideClick:function(c,b){var a=this.getMousePos(c);var d=b.getCoordinates();return(a.x>d.left&&a.x<(d.left+d.width))&&(a.y>d.top&&a.y<(d.top+d.height))?false:true},getMousePos:function(a){if(document.all){return{x:window.event.clientX+window.getScrollLeft(),y:window.event.clientY+window.getScrollTop()}}else{return{x:a.page.x,y:a.page.y}}},isNumber:function(a){if(a==""){return false}return(a>=0)||(a<0)?true:false},getPos:function(b){var a,c=0;if(b.offsetParent){do{a+=b.offsetLeft;c+=b.offsetTop}while(b=b.offsetParent)}else{if(b.x){a+=b.x;c+=b.y}}return{x:a,y:c}}});