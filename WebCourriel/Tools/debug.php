<?php
// GROAW (raving rabbit cry) print the information with print_r in a special html part.

function groaw($info)
{
    if (DEBUG)
    {
		if (!isset($GLOBALS['groaw_array'])) {
			$GLOBALS['groaw_array'] = array();
		}

		$GLOBALS['groaw_array'][] = $info;
    }
}
?>
