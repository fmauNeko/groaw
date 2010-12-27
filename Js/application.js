window.onload = function()
{
	var frame = document.getElementById("apercu_html");
	
	if (frame)
	{
		var hauteur = frame.contentWindow.document.body.scrollHeight;
		frame.style.height = hauteur+'px';
	}
}
