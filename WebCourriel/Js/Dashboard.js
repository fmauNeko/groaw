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

	this.setScrollBarHeight();

	byId('body').appendChild(scrollBar);

	this.scrollTop = 0;
	
	if (sessionStorage) {
		var cache = sessionStorage[this.container.id+'_scroll'];

		if (cache > 0) {
			this.scrollTop = cache;
		}
	}

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


	this.scroll();
};

ScrollBar.prototype.setScrollBarHeight = function() {

	for (var key in ScrollBar_instances) {
		var obj = ScrollBar_instances[key];

		if (obj.container.offsetHeight >= obj.container.scrollHeight) {
			obj.scrollBar.style.display = 'none';
			obj.scrollBarHeight = null;
		} else {

			var h = Math.round((obj.container.offsetHeight * obj.container.offsetHeight) / obj.container.scrollHeight);

			obj.scrollBar.style.height = h+'px';

			// update position if necesarry
			if (h != obj.scrollBarHeight) {
				if (!obj.scrollBarHeight) {
					obj.scrollBar.style.display = 'block';
				}

				var rapport = h/obj.scrollBarHeight;
				var newPos = Math.round(obj.scrollBar.offsetTop*rapport);
				obj.scrollBar.style.top = newPos+'px';
			}
			
			obj.scrollBarHeight = h;
		}
	}
}

ScrollBar.prototype.scroll = function() {

	this.container.scrollTop = this.scrollTop;

	var max_scroll = this.container.scrollHeight-this.container.offsetHeight;
	var h = this.container.offsetHeight - this.scrollBar.offsetHeight;
	var r = this.scrollTop/max_scroll;
	var t = r*h;

	this.scrollBar.style.top = Math.round(t)+'px';
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

	if (sessionStorage) {
		sessionStorage[obj.container.id+'_scroll'] = obj.scrollTop;
	}
}

addEventFunction('load', window, function() {

	new ScrollBar(document.getElementsByClassName('messages')[0],
			'scrollbar_messages');

	new ScrollBar(document.getElementsByClassName('boxes')[0],
			'scrollbar_boxes');
});
