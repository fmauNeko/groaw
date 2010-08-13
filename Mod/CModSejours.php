<?php
class CModSejours extends AModele
{
	public static function __static_constructor()
	{
		self::$attributs = array(
			new CAttId('Identifiant', 'ID', True, False),
			new CAttDateTime('Date d\'arrivée', 'DATEARRIVE', False, True),
			new CAttDateTime('Date de départ', 'DATEDEPART', False, True)
		);

		self::enregistrerRequete("supprimer",
			"DELETE FROM SEJOURS WHERE ID = :ID");
		self::enregistrerRequete("insert",
			"INSERT INTO SEJOURS (ID, DATEARRIVE, DATEDEPART) VALUES (:ID, :DATEARRIVE, :DATEDEPART)");
		self::enregistrerRequete("lister",
			"SELECT ID, DATEARRIVE, DATEDEPART FROM SEJOURS");
		self::enregistrerRequete("update",
			"UPDATE SEJOURS SET ID = :ID, DATEARRIVE = :DATEARRIVE, DATEDEPART = :DATEDEPART WHERE ID = :UPDATE_ID");
		self::enregistrerRequete("select",
			"SELECT ID, DATEARRIVE, DATEDEPART FROM SEJOURS WHERE ID = :ID");
	}
}

CModSejours::__static_constructor();
?>