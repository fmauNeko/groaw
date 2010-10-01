<?php
function groaw($info)
{
	echo "\n<pre>";
	if ($info)
	{
		print_r($info);
	}
	else
	{
		var_dump($info);	
	}
	echo "</pre>\n";
}
?>
