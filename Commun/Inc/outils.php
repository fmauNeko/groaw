<?php
function groaw($info)
{
    if (DEBUG)
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
}
	
function utf7_to_utf8($texte)
{
	return utf8_encode(imap_utf7_decode($texte));
}

function utf8_to_utf7($texte)
{
	return imap_utf7_encode(utf8_decode($texte));
}

?>
