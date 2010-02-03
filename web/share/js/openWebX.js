/*
$Id: openWebX.js 236 2009-09-10 06:03:14Z jens $
*/


var pos, collapsed;

/*
$(document.body).addEvents({
   
    click: function(e){
        
        if (collapsed){
            
            images.each(function(img, i){
                (function(){
                    this.style.WebkitTransform = 'rotate(0)'
                    this.style.MozTransform = 'rotate(0)';
                    this.morph({
                        top: this.retrieve('default:coords').y,
                        left: this.retrieve('default:coords').x
                    });
                }).delay(40 * i, img);
            });
            
            collapsed = false;
        } else {
            var picked = $(e.target).retrieve('default:coords');
            
            images.each(function(img, i){
                (function(){
                    //this.setStyle('-webkit-transform', 'rotate(' + $random(-20, 20) + 'deg)');
                    this.style.WebkitTransform = 'rotate('+$random(-20,20)+'deg)'
                    this.style.MozTransform = 'rotate('+$random(-20,20)+'deg)';
                    this.morph({
                        top: picked.y + $random(-20, 20),
                        left: picked.x + $random(-20, 20)
                    });
                }).delay(40 * i, img);
            });
            
            collapsed = true;
        }
    }
});
*/




var divArray = new Array();

window.addEvent('domready',function(){
	Clientcide.setAssetLocation('/share/images/icons/Assets');
	var myObj = new openWebX();
	
	myObj.getDivs();
	
	myObj.initLoaders();
	
	myObj.initActions();
	
	myObj.initFaders();
	
	// Make PNGs looking better in IE<7
	myObj.fixPNGs();
	
	// Make Forms "verbose"
	myObj.formHints();

	// Add Datepickers
	myObj.formDatepickers();
	
	// Prepare Tabs
	myObj.tabInit();
	
	// Prepare Galleries
	myObj.initGalleries();

});

window.addEvent('load',function(){
	
});

var openWebX = new Class({});

openWebX.implement({
//###############################################################################################
	getDivs: function() {
		$$('div').each(function(item){
			var currentElement = divArray.length;
			var id = item.getProperty('id');
			var size = item.getSize();
			var pos = item.getPosition();
			var classes = item.getProperty('class');

			
			divArray[currentElement] = new Object();
			divArray[currentElement]['id'] = id;
			divArray[currentElement]['width'] = size.x;
			divArray[currentElement]['height'] = size.y;
			divArray[currentElement]['x'] = pos.x;
			divArray[currentElement]['y'] = pos.y;
			divArray[currentElement]['classes'] = classes;
			divArray[currentElement]['index'] = item.getStyle('z-index');
		});
	},
	
	initLoaders: function() {
		$$('div').each(function(item) {
			var myLoad = false;
			if (myLoad = item.getProperty('load')) {
				var myID = item.getProperty('id');
				var itemWaiter = new Waiter($(myID));
				itemWaiter.start();
				item.load('request.php?request='+myLoad).chain(function() {
					alert ('done!');
				});
				//itemWaiter.stop();
			}
		});
	},
	initFaders: function() {
		divArray.each(function(item, index) {
			if (item.classes.contains('content')) {
				var faderIndex = parseInt(item.index) + 1;
				
				var faderTop = new Element(
						'div',
						{
							'id': 'faderTop_'+item.id,
							'class': 'fade_top',
							'styles': {
								'z-index': faderIndex,
								'position': 'absolute',
								'top': item.y,
								'left': item.x,
								'width': item.width
							}
						}
				);
				var faderBottom = new Element(
						'div',
						{
							'id': 'faderBottom_'+item.id,
							'class': 'fade_bottom',
							'styles': {
								'z-index': faderIndex,
								'position': 'absolute',
								'top': parseInt(item.y)+parseInt(item.height)-20,
								'left': item.x,
								'width': item.width
							}
						}
				);

				faderTop.inject(item.id);
				faderBottom.inject(item.id);
			}
			console.log(item);
		});
	},
	
	initActions: function () {
		divArray.each(function(item, index) {
			if (item.classes.contains('rotate')) {
				var fxRotate = new Fx.Rotate(item.id);
				$(item.id).addEvent('mouseenter',function(el){
					
					resizeTo(item.id,0.5,1);
					//fxRotate.spin(2500);
				});
				$(item.id).addEvent('mouseleave',function(el){
					//fxRotate.set(0);
					resizeTo(item.id,0.5,1);
				});
			}
		});
	},
    fixPNGs: function() {
    	Browser.scanForPngs('body');
    	$$('img').each(function(item){
    		if (item.hasClass('fixPNG')) Browser.fixPNG(item);
    	});
	},
	formHints: function() {
		$$('form').each(function(form){
			new OverText($$('textarea'));
		});
	},
	formDatepickers: function() {
		$$('input[id^=datepicker_]').each(function(item){
			var myFormat = item.getProperty('format');
			new DatePicker(item,{
				format: myFormat
			});
		});
	},
	tabInit: function() {
		$$('div[id^=tab_]').each(function(item){
			var myImg = item.getElement('img');
			item.addEvent('mouseenter',function(e){
				if (item.hasClass('passive')) {
					item.removeClass('passive');
					item.addClass('active');
				}
				if (myImg.hasClass('passive')) {
					myImg.removeClass('passive');
					myImg.addClass('active');
				}
			});
			item.addEvent('mouseleave',function(e){
				if (item.hasClass('active')) {
					item.removeClass('active');
					item.addClass('passive');
				}
				if (myImg.hasClass('active')) {
					myImg.removeClass('active');
					myImg.addClass('passive');
				}
			});
		});
	},
	
	initGalleries: function() {
		$$('div[id^=gallery_]').each(function(item){
			posDiv = item.getPosition();
			
			centerX = posDiv.x + 185;
			centerY = posDiv.y + 195;
			
			//alert(item.getProperty('id')+' '+centerX+'/'+centerY);
			
			var images = item.getElements('img');
			images.each(function(img){
				
				if (img.getProperty('class')=='gallery_image') {
					
				    pos = img.getPosition();
				
				    img
				        .store('default:coords', pos)
				        .set('styles', {
				            top: pos.y,
				            left: pos.x
				        })
				        .set('morph', {
				            transition: 'back:out'
				        });
				    
				    // MooTools bug (?)
				    (function(){
				        this.setStyle('position', 'absolute');
				    }).delay(1, img);
				    
				    img.style.WebkitTransform = 'rotate('+$random(-20,20)+'deg)'
		            img.style.MozTransform = 'rotate('+$random(-20,20)+'deg)';
		            img.set('styles', {
		                top: centerY + $random(-20, 20),
		                left: centerX + $random(-20, 20)
		             });
				}
				
				
                
			     
			});
			
		});
	}
});


/**
 * Helpers
 */

function scale(itemID, size) {
	$(itemID).style.MozTransform = "scale(" + size + ")";
	$(itemID).style.WebkitTransform = "scale(" + size + ")";
}
function resizeTo(itemID, startSize, targetSize) {
	var myFx = new Fx.Tween($(itemID));
	myFx.start('MozTransform','size('+startSize+')','size('+targetSize+')');
}

