/*
$Id: openWebX.js 236 2009-09-10 06:03:14Z jens $
*/
window.addEvent('domready',function(){
	Clientcide.setAssetLocation('/share/images/icons/Assets');
	var myObj = new openWebX();
	
	// Make PNGs looking better in IE<7
	myObj.fixPNGs();
	
	// Make Forms "verbose"
	myObj.formHints();

	// Add Datepickers
	myObj.formDatepickers();

});

window.addEvent('load',function(){
	
});

var openWebX = new Class({});

openWebX.implement({
//###############################################################################################
    fixPNGs: function() {
    	Browser.scanForPngs('body');
    	$$('img').each(function(item){
    		alert(item);
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
	}
});

