<?php
class CFormulaire
{
	static function soumis()
	{
		if ($_SERVER['REQUEST_METHOD'] === "POST")
		{
			return true;
		}
		return false;
	}

}
?>
