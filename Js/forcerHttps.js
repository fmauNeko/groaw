if (parent.location.protocol != 'https:')
{
	window.location.replace('https'+parent.location.href.slice(4));
};
