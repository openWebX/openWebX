var ImageFlip = new Class({
	/**
	 * Initializes the class.
	 * @param {Array} elements		array with div ids
	 * @param {Array} styles		array with div stylesheets
	 * @param {Array} buttons		array with button ids
	 * @param {Array} images		array with image paths
	 * @param {Array} properties	array with properties
	 */
	initialize: function(elements, styles, buttons, images, properties) {
		// check if elements and styles match
		if (elements.length != styles.length) {
			alert('ERROR: Size of elements and styles do not match!');
			return;
		}
		
		// check if there are enough images
		if (images.length < elements.length) {
			alert('ERROR: There are not enough images!')
			return;
		}
		
		// set element names and styles into class context
		this.elements = elements;
		this.styles = styles;
		this.images = images;
		this.imageElements = new Array();
		for (var i = 0; i < images.length; i++) {
			this.imageElements[i] = null;
		}
		
		// hide the elements displayed for later fade in		
		this.hideElements();
		
		// fetch the width and heights of the base containers
		this.fetchContainerDimensions();
		
		// extend Styles with morphing class
		/*Fx.Morph = Fx.Styles.extend( {
			start: function(className) {
				var to = {};
				$each(document.styleSheets, function(style) {
					var rules = style.rules || style.cssRules;
					$each(rules, function(rule) {
						if (!rule.selectorText.test('\.' + className + '$')) {
							return;
						}
						Fx.CSS.Styles.each(function(style) {
							if (!rule.style || !rule.style[style]) {
								return;
							}
							var ruleStyle = rule.style[style];
							to[style] = (style.test(/color/i) && ruleStyle.test(/^rgb/)) ? ruleStyle.rgbToHex() : ruleStyle;
						});
					});
				});
			return this.parent(to);
			}
		});*/
		
		// styles to be morphed
		Fx.CSS.Styles = ["left", "top", "width", "height"];
		
		// block variable
		this.block = false;
		this.imageArrayFinished = false;
		
		// set z-index for elements
		var zIndexes = new Array();
		for (var i = 0; i < this.elements.length; i++) {
			zIndexes[i] = properties['zIndex'].toInt() + i;
		}
		this.elements.each(function(item, index) {
			$(item).setStyle('z-index', zIndexes[index]);
		});
		// set indexes into class
		this.zIndexes = zIndexes;
		
		// add morphing capability for all elements
		var morphers = new Array();
		this.elements.each(function(item, index) {
			morphers[index] = new Fx.Morph(item, {wait: false});
		});
		this.morphers = morphers;
		
		// add handler for buttons
		$(buttons[0]).addEvent('click', this.moveNext.bind(this)); // forward
		$(buttons[1]).addEvent('click', this.movePrevious.bind(this)); // backward
		
		// preload images
		this.preloadImages(properties['zIndex'].toInt(), properties['loadingText']);
	},
	
	/**
	 * Preloads the images that are displayed at the first start as well as the next and the previous image.
	 * @param {int} 	zIndex 		initial zIndex
	 * @param {String} 	loadingText	text to be displayed while loading the images
	 */
	preloadImages: function(zIndex, loadingText) {
		// create the overlay div
		var preloadingDivDimensions = this.calculatePreloadingDivDimension();
		var preloadingDiv = new Element('div', {
			'styles': {
				'z-index': zIndex,
				'position': 'absolute',
				'background-color': '#000',
				'top': preloadingDivDimensions[0],
				'left': preloadingDivDimensions[1],
				'width': preloadingDivDimensions[2],
				'height': preloadingDivDimensions[3]
			}
		}).injectBefore(this.elements[0]);	
		
		// load spinner image
		// TODO pass dimension and then adjust the innerDiv dimensions
		var spinnerImg = new Asset.image('spinner.gif', {
			'align': 'absmiddle'
		});
		// create a div for spinner and text
		var textDiv = new Element('div', {
			'styles': {
				'position': 'relative',
				'top': ((preloadingDivDimensions[3] - 32) / 2).toInt(),
				'left': ((preloadingDivDimensions[2] - 220) / 2).toInt(),
				'width': '220px',
				'height': '32px',
				'background': 'transparent',
				'color': 'white',
				'font': '12px Verdana, Arial, sans-serif'
			}
		}).injectInside(preloadingDiv);
		preloadingDiv.setProperty('id', '_preloadingDiv_');
		// insert spinner image
		spinnerImg.injectInside(textDiv);
		// insert loading text
		loadingText = loadingText.replace('#SIZE#', this.elements.length + 2);
		var innerText = new Element('span', {
			styles: {
				'margin-left': '5px'
			}
		});
		innerText.injectAfter(spinnerImg);
		
		// prepare images to be loaded
		var count = 1;
		var j = 0;
		var imagesToLoad = new Array();
		for (; j < this.elements.length; j++) {
			imagesToLoad.push(this.images[this.elements.length - 1 - j]);
		}
		// check if the next and previous image can be loaded
		if (this.images.length >= this.elements.length + 1) {
			// add the next image
			imagesToLoad.push(this.images[this.elements.length]);
		}
		if (this.images.length >= this.elements.length + 2) {
			// add the previous image
			imagesToLoad.push(this.images[this.images.length - 1]);
		}
		
		// now really load images ^_^
		var self = this;
		new Asset.images(imagesToLoad, {
			onProgress: function(idx) {
				// badass hook, as idx is sometimes bigger than the number of images to load... :-(
				if(idx < imagesToLoad.length) {
					innerText.set('text',loadingText.replace('#IMG#', idx + 1));
				}
				this.setStyles({
					width: '100%',
					height: '100%'
				});
				// as the images are not loaded in order, find the corresponding index
				self.imageElements[self.images.indexOf(this.getProperty('src'))] = this;
			},
			onComplete: this.finalizeImageLoading.bind(this)
		});
	},
	
	/**
	 * Removes the preloading container and initializes the fade in of the elements.
	 */
	finalizeImageLoading: function() {
		// fade out loader div and call chain
		//$('_preloadingDiv_').morph('opacity', {duration: 500, transition: Fx.Transitions.linear}).start(1, 0).chain(this.addImageElements.bind(this));
	},
	
	/**
	 * Adds the preloaded images to the elements.
	 */
	addImageElements: function() {
		$('_preloadingDiv_').remove(); // remove loader div
		// set images as children for the divs and remember the first
		this.firstImageIdx = 0;
		this.lastImageIdx = this.elements.length - 1;
		var length = this.elements.length - 1;
		var i = 0;
		for (; i < this.elements.length; i++) {
			this.imageElements[length - i].clone().injectInside($(this.elements[i]));			
		}
		$(this.elements[0]).morph('opacity', {duration: 700, transition: Fx.Transitions.Expo.easeIn}).start(0, 1).chain(this.fadeInElement.bind(this, [1]));
	},
	
	/**
	 * Fade the element with the given index in.
	 * @param {int} idx	array index
	 */
	fadeInElement: function(idx) {
		if (idx < this.elements.length) {
			$(this.elements[idx]).morph('opacity', {duration: 700, transition: Fx.Transitions.Expo.easeIn}).start(0, 1).chain(this.fadeInElement.bind(this, [++idx]));
		}
	},
	
	/**
	 * Hides the elements that hold the images.
	 */
	hideElements: function() {
		this.elements.each(function(item, index) {
			$(item).setOpacity(0);
		});
	},
	
	/**
	 * Calculates the dimension of the preloading div.
	 * The div will overlay all elments, so the parameters left, top, width and height are resolved by checking the styles of the elements.
	 * @return dimensions as array
	 */
	calculatePreloadingDivDimension: function() {
		var left = 0;
		var top = 0;
		var width = 0;
		var height = 0;
		var leftwidth = 0;
		var topheight = 0;
		// check styles for each element
		this.elements.each(function(item, index) {
			if (index == 0) {
				left = $(item).getStyle('left').toInt();
				top = $(item).getStyle('top').toInt();
				width = left + $(item).getStyle('width').toInt();
				height = top + $(item).getStyle('height').toInt();
			} else {
				if ($(item).getStyle('left').toInt() < left) {
					left = $(item).getStyle('left').toInt();
				}
				if ($(item).getStyle('top').toInt() < top) {
					top = $(item).getStyle('top').toInt();
				}
				if ($(item).getStyle('width').toInt() + $(item).getStyle('left').toInt() > leftwidth) {
					leftwidth = $(item).getStyle('width').toInt() + $(item).getStyle('left').toInt();
				}
				if ($(item).getStyle('height').toInt() + $(item).getStyle('top').toInt() > topheight) {
					topheight = $(item).getStyle('height').toInt() + $(item).getStyle('top').toInt();
				}
			}
		});
		height -= top;		
		return new Array(top, left, leftwidth - left, topheight - top);
	},
	
	/**
	 * Fetch the sizes of the divs.
	 */
	fetchContainerDimensions: function() {
		var widths = new Array();
		var heights = new Array();
		this.elements.each(function(item, index) {
			widths[index] = $(item).getStyle('width');
			heights[index] = $(item).getStyle('height');
		});
		this.widths = widths;
		this.heights = heights;
	},
	
	/**
	 * Preloads the image on the given index if not already loaded.
	 * @param {int} idx image index
	 * @param {int} direction direction of movement, 1 forewards, -1 backwards
	 */
	preloadSingleImage: function(idx, direction) {
		var imageIdx = 0;		
		if (direction == 1) {
			// forward moving
			imageIdx = idx + this.elements.length - 1;
			// check if end of image array is arrived and recalculate the index
			if (imageIdx > this.images.length - 1) {
				imageIdx = imageIdx - this.images.length;
			}
		} else {
			// backward moving
			if (idx < 0) {
				// ...over array borders
				imageIdx = this.images.length + idx;
			} else {
				imageIdx = idx;
			}
		}
		
		if (this.imageElements[imageIdx] == null) {
			var img = new Asset.image(this.images[imageIdx]);
			img.setStyles({
				width: '100%',
				height: '100%'
			});
			this.imageElements[imageIdx] = img;
		}
	},
	
	/**
	 * Step 1 (fwd): moves the elements one step forwards.
	 * @param {Object} e
	 */
	moveNext: function(e) {
		// cancel event
		new Event(e).stop();
		
		if (!this.block) {
			// set blocker to complete the animations before a new one starts
			this.block = true;
			
			// preload the next but one image
			this.preloadSingleImage(this.firstImageIdx + 2, 1);
									
			// fade out last elements
			var fade = $(this.elements[this.elements.length - 1]).tween('opacity', {duration: 1000, transition: Fx.Transitions.Circ.easeInOut});					
			fade.start(1, 0).chain(this.morphForward.bind(this));
		}
	},
	
	/**
	 * Step 2 (fwd): morph all elements, encapsulated for chain call
	 */
	morphForward: function() {
		// morph all but last
		for (var i = 0; i < this.morphers.length - 1; i++) {
			this.morphers[i].start(this.styles[i + 1]);
		}
		
		// set the next image as background for the now first div
		if (this.images.length - 1 > this.firstImageIdx) {
			// there are more images in the array
			this.firstImageIdx++;
		} else {
			// start at the beginning of the images
			this.firstImageIdx = 0;
		}
		
		// calculate the image index
		var imageIdx = this.firstImageIdx + this.elements.length - 1;
		// check if end of image array is arrived and recalculate the index
		if (imageIdx > this.images.length - 1) {
			imageIdx = imageIdx - this.images.length;
		}
		
		// add the image
		$(this.elements[this.elements.length - 1]).empty();
		this.imageElements[imageIdx].clone().injectInside($(this.elements[this.elements.length - 1]));
				
		// morph the last (that actually is invisible) and add chain
		this.morphers[this.morphers.length - 1].start(this.styles[0]).chain(this.fadeInFirst.bind(this));
	},
	
	/**
	 * Step 1 (bwd): moves the elements one step backwards.
	 * @param {Object} e
	 */
	movePrevious: function(e) {
		// cancel event
		new Event(e).stop();
		
		if (!this.block) {
			// set blocker to complete the animations before a new one starts
			this.block = true;
			
			// preload the last but one image
			this.preloadSingleImage(this.firstImageIdx  - 2, -1);
									
			// fade out last elements
			var fade = $(this.elements[0]).morph('opacity', {duration: 1000, transition: Fx.Transitions.Circ.easeInOut});					
			fade.start(1, 0).chain(this.morphBackward.bind(this));
		}
	},
	
	/**
	 * Step 2 (bwd): morph all elements, encapsulated for chain call
	 */
	morphBackward: function() {
		// morph all but first
		for (var i = 1; i < this.morphers.length; i++) {
			this.morphers[i].start(this.styles[i - 1]);
		}
		
		// set the next image as background for the now last div
		if (this.firstImageIdx > 0) {
			// there are more images in the array
			this.firstImageIdx--;
		} else {
			// start at the beginning of the images
			this.firstImageIdx = this.images.length - 1;
		}
		
		// add the image
		$(this.elements[0]).empty();
		this.imageElements[this.firstImageIdx].clone().injectInside($(this.elements[0]));
		
		// morph the last (that actually is invisible) and add chain
		this.morphers[0].start(this.styles[this.styles.length - 1]).chain(this.fadeInLast.bind(this));
	},
	
	
	/**
	 * Fades the first element in.
	 */
	fadeInFirst: function() {
		// increase the z.indexes for the other elements
		this.increaseZIndexes();
		// fade the first element in
		$(this.elements[this.elements.length - 1]).setStyle('z-index', this.zIndexes[0]);
		$(this.elements[this.elements.length - 1]).effect('opacity', {duration: 1000, transition: Fx.Transitions.Circ.easeInOut})
			.start(0, 1).chain(this.reorderArraysForward.bind(this));
	},
	
	/**
	 * Fades the last element in.
	 */
	fadeInLast: function() {
		// decrease the z.indexes for the other elements
		this.decreaseZIndexes();
		// fade the last element in
		$(this.elements[0]).setStyle('z-index', this.zIndexes[this.zIndexes.length - 1]);
		$(this.elements[0]).effect('opacity', {duration: 1000, transition: Fx.Transitions.Circ.easeInOut})
			.start(0, 1).chain(this.reorderArraysBackward.bind(this));
	},
	
	/**
	 * Increases the z-index for all elements but the first.
	 */
	increaseZIndexes: function() {
		for (var i = 0; i < this.elements.length - 1; i++) {
			$(this.elements[i]).setStyle('z-index', this.zIndexes[i + 1]);
		}
	},
	
	/**
	 * Decreases the z-index for all elements but the last.
	 */
	decreaseZIndexes: function() {
		for (var i = 1; i < this.elements.length; i++) {
			$(this.elements[i]).setStyle('z-index', this.zIndexes[i - 1]);
		}
	},
	
	/**
	 * Reorders the array of elements and morphers.
	 * Move every element one position forward.
	 */
	reorderArraysForward: function() {
		var elements = this.elements.copy();
		var morphers = this.morphers.copy();
		var i = elements.length - 2;
		// move all elements one step forward, last element is overwritten as this is moved to first position
		for (; i >= 0; i--) {
			this.elements[i + 1] = elements[i];
			this.morphers[i + 1] = morphers[i];
		}
		// insert the new first element
		this.elements[0] = elements[elements.length - 1];
		this.morphers[0] = morphers[morphers.length - 1];
		// remove block
		this.block = false;
	},
	
	/**
	 * Reorders the array of elements and morphers.
	 * Move every element one position backward.
	 */
	reorderArraysBackward: function() {
		var elements = this.elements.copy();
		var morphers = this.morphers.copy();
		var i = 1;
		// move all elements one step backward, first element is overwritten as this is moved to last position
		for (; i < elements.length; i++) {
			this.elements[i - 1] = elements[i];
			this.morphers[i - 1] = morphers[i];
		}
		// insert the new last element
		this.elements[this.elements.length - 1] = elements[0];
		this.morphers[this.morphers.length - 1] = morphers[0];
		// remove block
		this.block = false;
	}	
});