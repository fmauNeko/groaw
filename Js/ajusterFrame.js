var frame = false;

var ajusterHauteur = function()
{
	if (!frame)
	{
		frame = document.getElementById('apercu_html');
	};
	
	if (frame)
	{
		var hauteur = frame.contentWindow.document.body.scrollHeight + 'px';

		if (frame.style.height != hauteur)
		{
			frame.style.height = hauteur;
		};

		if (frame.contentWindow.document.body.style.overflowY != 'hidden')
		{
			frame.contentWindow.document.body.style.overflowY = 'hidden';
		};
	};
};

ajusterHauteur();

var intervalle = window.setInterval(ajusterHauteur, 200);

window.onload = function()
{
	if (!frame)
	{
		window.clearInterval(intervalle);
	}
	ajusterHauteur();
};
