var byId = function(id)
{
	return document.getElementById(id);
};

var _ = function(chaine)
{
	return chaine;
};

var newDom = function(nom)
{
	return document.createElement(nom);
};

var delDom = function(element)
{
	if (element.hasChildNodes())
	{
		element.removeChild(element.firstChild);
	};
};

var emptyDom = function(element)
{
	while(element.hasChildNodes())
	{
		element.removeChild(element.firstChild);
	};
};

var delChar = function(chaine, caractere)
{
	var index = chaine.indexOf(caractere);
	return chaine.substr(0,index)+chaine.substr(index+1);
};

var creerImage = function(src, alt)
{
	var image = newDom("img");
	image.setAttribute("src",src);
	image.setAttribute("alt",_(alt));
	return image;
};

var creerBouton = function(id, titre,rappel)
{
	var bouton = newDom("input");

	if (rappel)
	{
		bouton.onclick = rappel;
	};

	bouton.setAttribute("type","button");
	bouton.setAttribute("name",id);
	bouton.setAttribute("value",_(titre));
				   
	return bouton;
};

var randInt = function(min, max)
{
    return Math.floor(Math.random()*(max-min))+min;
};

var melangerTableau = function(tableau)
{
	tableau.sort(function(a, b)
	{
		return ((2 * Math.round(Math.random())) - 1);
	});

	return tableau;
};


if (!window.console)
{
	window.console = {};
	window.console.info = alert;
	window.console.log = alert;
	window.console.warn = alert;
	window.console.error = alert;
};

var	log = function(element)
{
	window.console.log(element);
};

var nonNon = function()
{
	return false;
};

HTMLElement.prototype.toutOffset = function()
{
	if (this.cacheOffset)
	{
		return this.cacheOffset;
	};

	var o = new Object();

	if (this.offsetParent)
	{
		var oo = this.offsetParent.toutOffset();
		o.haut = oo.haut + this.offsetTop;
		o.gauche = oo.gauche + this.offsetLeft;
	}
	else
	{
		o.haut = 0;
		o.gauche = 0;
	};

	this.cacheOffset = o;

	return o;
};
