<?php
// GROAW (raving rabbit cry) print the information with print_r in a special html part.
function groaw($info)
{
    if (DEBUG)
    {
        echo "\n<pre class=\"groaw\">";
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
?>
