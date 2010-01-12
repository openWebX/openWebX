/*
Script: LavishMenu.js
	Add a Lavish menu effect to UL elements.

License:
	MIT-style license.

Copyright:
	Copyright (c) 2009 [Drew Gauderman](http://www.aspinvision.com)

Code & Documentation:
	[ASP Invision]http://www.aspinvision.com).

Inspiration:
	- http://devthought.com/blog/projects-news/2007/01/cssjavascript-true-power-fancy-menu/


Hot to use:

	//simple, but class and type
	$$('.lavishmenu ul').lavishmenu();
		
	//by id
	$('lavishmenu').lavishmenu();
		
	//advanced with options
	$$('.partners ul,.quicklinks ul').lavishmenu({
		'backgroundColor': '#fff',
		'opacity': 1
	});

*/

var LavishMenu = new Class({

	Implements: [Options],

	options: {
		'backgroundColorActive': '#ffffff',
		'backgroundColor': '#ffffff',
		'opacityActive': 0.4,
		'opacity': 0.8
	},

	// do initialize
	initialize: function( el, options) {
		//input custom options
		this.setOptions(options);

		//make sure its an element
		el = $(el);

		//if not an element exit
		if (!el || el.get('tag') != 'ul') return;

		//make sure link is above the background
		el.getElements('li').setStyles({'z-index': 10,'position': 'relative'});

		//get active menu item
		this.active = $pick(el.getElement('li.active'), el.getElement('li'));

		//li element
		this.liHover = new Element('li', {'class': 'liHover', 'html': '<div class="rtop">' +
			'	<b style="height: 1px;margin: 0 5px;"></b>' +
			'	<b style="height: 1px;margin: 0 3px;"></b>' +
			'	<b style="height: 1px;margin: 0 2px;"></b>' +
			'	<b style="height: 2px;margin: 0 1px;"></b>' +
			'</div>' +
			'<div class="middle" style="display:block;height: ' + (this.active.getSize().y-10) + 'px;"></div>' +
			'<div class="rbottom">' +
			'	<b style="height: 2px;margin: 0 1px;"></b>' +
			'	<b style="height: 1px;margin: 0 2px;"></b>' +
			'	<b style="height: 1px;margin: 0 3px;"></b>' +
			'	<b style="height: 1px;margin: 0 5px;"></b>' +
			'</div>'}).setStyles({
				'position': 'absolute',
				'opacity': 0,
				'padding': 0,
				'margin': 0
			});
			
		this.liHover.getElements('b').setStyles({'display': 'block','overflow': 'hidden'});

		//add li slider
		el.adopt(this.liHover).getElements('li').each(function(li) {
			li.addEvents({
				'mouseenter': function() {
					this.goto(li);
				}.bind(this),
				'mouseleave': function() {
					if (active = el.getElement('li.active'))
						this.goto(active);
					else
						this.liHover.morph({'opacity': 0});
				}.bind(this)
			});
		}.bind(this));

		if (active = el.getElement('li.active'))
			this.goto(active, true);
		else
			this.liHover.setStyle('width', el.getElement('li').getSize().x);
	},

	goto: function(el, noMorph) {
		if (el.hasClass('liHover')) return;

		var coord = el.getCoordinates();
		var effect = (noMorph == true ? 'setStyles' : 'morph');

		this.liHover[effect]({
			'top': coord.top - (Browser.Engine.trident ? 2 : 0) - el.getStyle('margin-top').toInt(),
			'left': coord.left - (Browser.Engine.trident ? 2 : 0) - el.getStyle('margin-left').toInt(),
			'height': coord.height,
			'width': coord.width,
			'opacity': (el.hasClass('active') ? this.options.opacityActive : this.options.opacity)
		}).getElement('div.middle')[effect]({
			'background-color': (el.hasClass('active') ? this.options.backgroundColorActive : this.options.backgroundColor),
			'height': (coord.height-10)
		}).getParent('li').getElements('b')[effect]({
			'background-color': (el.hasClass('active') ? this.options.backgroundColorActive : this.options.backgroundColor)
		});
	}
});

Element.implement({
	lavishmenu: function(options) {
		window.addEvent('load', function() {
			new LavishMenu(this, options);
		}.bind(this));
	}
});