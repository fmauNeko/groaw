var ScrollBar_instances = null;

var ScrollBar = function(container, id) {

	var firstScrollBar = ScrollBar_instances == null;

	if (firstScrollBar) {
		ScrollBar_instances = [];
	}

	ScrollBar_instances[container.id] = this;

	this.container = container;

	var scrollBar = newDom('div');
	this.scrollBar = scrollBar;
	
	scrollBar.className = 'scrollbar';
	scrollBar.id = id;

	var buttonMore = newDom('div');
	var buttonLess = newDom('div');
	this.buttonMore = buttonMore;
	this.buttonLess = buttonLess;

	buttonMore.className = 'buttonMore';
	buttonLess.className = 'buttonLess';
	buttonMore.id = 'buttonMore_'+container.id;
	buttonLess.id = 'buttonLess_'+container.id;

	buttonMore.appendChild(document.createTextNode('+'));
	buttonLess.appendChild(document.createTextNode('-'));

	buttonMore.onclick = this.clickButtonMore;
	buttonMore.onselectstart = noNo;
	buttonLess.onclick = this.clickButtonLess;
	buttonLess.onselectstart = noNo;

	var body = byId('body');

	body.appendChild(buttonMore);
	body.appendChild(buttonLess);


	body.appendChild(scrollBar);

	this.scrollTop = 0;
	
	if (sessionStorage) {
		var cache = sessionStorage[this.container.id+'_scroll'];

		if (cache > 0) {
			this.scrollTop = parseInt(cache);
		}
	}

	this.setScrollBarHeight();
	
	if (this.container.addEventListener){
		// Pour Firefox	
		this.container.addEventListener('MozMousePixelScroll', this.mousewheel, false); 
		this.container.addEventListener('mousewheel', this.mousewheel, false); 

		if (firstScrollBar) {
			window.addEventListener('resize',this.setScrollBarHeight,false);
		}

	// Pour ie…
	} else if (this.container.attachEvent) {
		this.container.attachEvent('onmousewheel', this.mousewheel); 
		
		if (firstScrollBar) {
			window.attachEvent('onresize', this.setScrollBarHeight); 
		}
	}
};

ScrollBar.prototype.setScrollBarHeight = function() {

	for (var key in ScrollBar_instances) {
		var obj = ScrollBar_instances[key];

		if (obj.container.offsetHeight >= obj.container.scrollHeight) {
			obj.scrollBar.style.display = 'none';
			obj.scrollBarHeight = null;

			obj.buttonMore.style.display = 'none';
			obj.buttonLess.style.display = 'none';
		} else {

			var h = Math.round((obj.container.offsetHeight * obj.container.offsetHeight) / obj.container.scrollHeight);

			obj.scrollBar.style.height = h+'px';

			// update position if necesarry
			if (h != obj.scrollBarHeight) {
				if (!obj.scrollBarHeight) {
					obj.scrollBar.style.display = 'block';
					obj.buttonMore.style.display = 'block';
					obj.buttonLess.style.display = 'block';
				}

				var rapport = h/obj.scrollBarHeight;
				var newPos = Math.round(obj.scrollBar.offsetTop*rapport);
				obj.scrollBar.style.top = newPos+'px';
				
				obj.buttonMore.style.top = (obj.container.offsetHeight - obj.buttonMore.offsetHeight)+'px';
				obj.buttonLess.style.top = obj.container.offsetTop+'px';

				obj.buttonMore.style.left = obj.container.offsetLeft+obj.container.offsetWidth-obj.buttonMore.offsetWidth+'px';
				obj.buttonLess.style.left = obj.buttonMore.style.left;
			}
			
			obj.scrollBarHeight = h;

			obj.scroll();
		}
	}
}

ScrollBar.prototype.scroll = function() {

	// If scroll event when the scroll is useless
	if (this.scrollBarHeight == null) return;

	var max_scroll = this.container.scrollHeight-this.container.offsetHeight;
	var margin = 5;

	var hideMore = false;
	var hideLess = false;

	if (this.scrollTop <= margin) {
		hideLess = true;
		this.scrollTop = 0;
	} else if (this.scrollTop > (max_scroll - margin)) {
		hideMore = true;
		this.scrollTop = max_scroll;
	}
	
	if (sessionStorage) {
		sessionStorage[this.container.id+'_scroll'] = this.scrollTop;
	}

	this.buttonMore.style.display = hideMore ? 'none' : 'block';
	this.buttonLess.style.display = hideLess ? 'none' : 'block';

	this.container.scrollTop = this.scrollTop;

	var h = this.container.offsetHeight - this.scrollBar.offsetHeight;
	var r = this.scrollTop/max_scroll;
	var t = Math.round(r*h);

	this.scrollBar.style.top = t+'px';
}

ScrollBar.prototype.mousewheel = function(e) {

	// IE et les autres :-)
	var dom = e.target ? e.target : e.srcElement;
	var obj;

	while (dom != null) {
		if (dom.id && ScrollBar_instances[dom.id]) {
			obj = ScrollBar_instances[dom.id];
			break;
		}
		dom = dom.parentNode;
	}

	obj.scrollTop -= e.wheelDeltaY ? e.wheelDeltaY : -e.detail;

	var max_scroll = obj.container.scrollHeight-obj.container.offsetHeight;
	
	if (obj.scrollTop < 0) {
		obj.scrollTop = 0;
	} else if (obj.scrollTop > max_scroll) {
		obj.scrollTop = max_scroll;
	}

	obj.scroll();

}

ScrollBar.prototype.clickButtonMore = function(e) {
	var obj = ScrollBar_instances[this.id.slice(11)];
	obj.scrollTop += obj.container.offsetHeight - 100;
	obj.scroll();
}

ScrollBar.prototype.clickButtonLess = function(e) {
	var obj = ScrollBar_instances[this.id.slice(11)];
	obj.scrollTop -= obj.container.offsetHeight - 100;
	obj.scroll();
}

var createScrollBar = function() {
	new ScrollBar(document.getElementsByClassName('messages')[0],
			'scrollbar_messages');

	new ScrollBar(document.getElementsByClassName('boxes')[0],
			'scrollbar_boxes');

	// Desactivate this function if it called two times
	// the function can be called two time for callback with browsers
	// that don't support html5 DomContentLoaded
	createScrollBar = noNo;
};

addEventFunction('DOMContentLoaded', window, function() { createScrollBar() });
addEventFunction('load', window, function() { createScrollBar() });
