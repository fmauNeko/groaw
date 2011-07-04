var ScrollBar_instances = null;
var ScrollBarDrag_singleton = null;

var ScrollBar = function(container, id) {

	var firstScrollBar = ScrollBar_instances == null;

	if (firstScrollBar) {
		ScrollBar_instances = [];
	}


	ScrollBar_instances[container.id] = this;

	this.container = container;

	var scrollBar = newDom('div');
	this.scrollBar = scrollBar;
	scrollBar.ondragstart = noNo;
	
	scrollBar.className = 'scrollbar';
	scrollBar.id = id;

	addEventFunction('mousedown', scrollBar, this.dragStart);

	this.setScrollBarHeight();

	byId('body').appendChild(scrollBar);

	if (firstScrollBar) {
		this.scrollbar_mask = newDom('div');
		this.scrollbar_mask.id = 'scrollbar_mask';
		this.onselectstart = noNo;

		document.body.appendChild(this.scrollbar_mask);
	} else {
		this.scrollbar_mask = byId('scrollbar_mask');
	}
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

		var h = Math.round((obj.container.offsetHeight * obj.container.offsetHeight) / obj.container.scrollHeight);

		obj.scrollBar.style.height = h+'px';

		// update position if necesarry
		if (obj.scrollBarHeight && h != obj.scrollBarHeight) {
			var rapport = h/obj.scrollBarHeight;
			var newPos = Math.round(obj.scrollBar.offsetTop*rapport);
			obj.scrollBar.style.top = newPos+'px';
		}
		
		obj.scrollBarHeight = h;
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

ScrollBar.prototype.dragStart = function(e) {
	var obj = ScrollBarDrag_singleton = new Object();

	var dom = e.target ? e.target : e.srcElement;
	obj.dom = dom;

	// start position of mouse
	obj.posDepY = dom.offsetTop;
	obj.posY = obj.posDepY;
	
	obj.mouseY = e.clientY;
	obj.depMouseY = obj.mouseY;

	canard = dom;

	for (var key in ScrollBar_instances) {
		var i = ScrollBar_instances[key];

		if (i.scrollBar == dom) {
			obj.instance = i;
			break;
		}
	}

	addEventFunction('mouseup', document, obj.instance.dragEnd);
	addEventFunction('mousemove', document, obj.instance.cat);
	
	obj.intervalId = window.setInterval(obj.instance.dragPaint, 60, obj);

	obj.instance.scrollbar_mask.style.display = 'block';
}

ScrollBar.prototype.dragEnd = function(e) {
	var obj = ScrollBarDrag_singleton;

	// Disable paint function
	window.clearInterval(obj.intervalId);
	obj.intervalId = null;

	// Disable event functions
	removeEventFunction('mouseup', document, obj.instance.dragEnd);
	removeEventFunction('mousemove', document, obj.instance.cat);

	// Paint one time again
	obj.instance.dragPaint();
	
	obj.instance.scrollbar_mask.style.display = 'none';
	
}

ScrollBar.prototype.dragPaint = function(e) {
	var obj = ScrollBarDrag_singleton;

	var newPosY = obj.mouseY-obj.depMouseY+obj.posDepY;

	if (newPosY < 0) {
		newPosY = 0;
	} else if (newPosY > obj.instance.container.offsetHeight - obj.instance.scrollBarHeight) {
		newPosY = obj.instance.container.offsetHeight - obj.instance.scrollBarHeight;
	}

	if (newPosY != obj.posY) {
		log(newPosY);
		obj.instance.scrollBar.style.top = newPosY+'px';
		obj.posY = newPosY;
	}

	
	/*if (newPosY != obj.posOldY) {
		log(newPosY);
		obj.instance.scrollBar.style.top = newPosY+'px';
		obj.posOldY = newPosY;
	}*/
}

ScrollBar.prototype.cat = function(e) {

	var obj = ScrollBarDrag_singleton;
	
	obj.mouseY = e.clientY;
}

window.onload = function() {

	new ScrollBar(document.getElementsByClassName('messages')[0],
			'scrollbar_messages');

	new ScrollBar(document.getElementsByClassName('boxes')[0],
			'scrollbar_boxes');
};
