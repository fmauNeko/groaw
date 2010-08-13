<?php
class CModChambres extends AModele
{
	public static function __static_constructor()
	{
		self::$attributs = array(
			new CAttId('Identifiant', 'ID', True, False),
			new CAttInteger('Tarifs', 'TARIF', False, True)
		);

		self::enregistrerRequete("supprimer",
			"DELETE FROM CHAMBRES WHERE ID = :ID");
		self::enregistrerRequete("insert",
			"INSERT INTO CHAMBRES (ID, TARIF) VALUES (:ID, :TARIF)");
		self::enregistrerRequete("lister",
			"SELECT ID, TARIF FROM CHAMBRES");
		self::enregistrerRequete("update",
			"UPDATE CHAMBRES SET ID = :ID, TARIF = :TARIF WHERE ID = :UPDATE_ID");
		self::enregistrerRequete("select",
			"SELECT ID, TARIF FROM CHAMBRES WHERE ID = :ID");
	}
}

CModChambres::__static_constructor();
?>