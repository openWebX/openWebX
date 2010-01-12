/*
$Id: openWebX.js 236 2009-09-10 06:03:14Z jens $
*/
window.addEvent('domready',function(){
	Clientcide.setAssetLocation('/share/images/icons/Assets');
	var myObj = new openWebX();
	
	myObj.initLoaders();
	
	myObj.initActions();
	
	// Make PNGs looking better in IE<7
	myObj.fixPNGs();
	
	// Make Forms "verbose"
	myObj.formHints();

	// Add Datepickers
	myObj.formDatepickers();
	
	// Prepare Tabs
	myObj.tabInit();

});

window.addEvent('load',function(){
	
});

var openWebX = new Class({});

openWebX.implement({
//###############################################################################################
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
	initActions: function () {
		
	}
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
	}
});



