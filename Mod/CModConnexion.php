<?php
class CModHotels extends AModele
{
	public static function __static_constructor()
	{
		self::$attributs = array(
			new CAttId('Identifiant', 'ID', True, False),
			new CAttString('Nom', 'NOM', False, True),
			new CAttInteger('Nombre d\'étoiles', 'NBETOILES', False, True),
			new CAttString('Adresse', 'ADRESSE', False, True),
			new CAttString('Téléphone', 'TELEPHONE', False, True)
		);

		self::enregistrerRequete("supprimer",
			"DELETE FROM HOTELS WHERE ID = :ID");
		self::enregistrerRequete("insert",
			"INSERT INTO HOTELS (ID, NOM, NBETOILES, ADRESSE, TELEPHONE) VALUES (:ID, :NOM, :NBETOILES, :ADRESSE, :TELEPHONE)");
		self::enregistrerRequete("lister",
			"SELECT ID, NOM, NBETOILES, ADRESSE, TELEPHONE FROM HOTELS");
		self::enregistrerRequete("update",
			"UPDATE HOTELS SET ID = :ID, NOM = :NOM, NBETOILES = :NBETOILES, ADRESSE = :ADRESSE, TELEPHONE = :TELEPHONE WHERE ID = :UPDATE_ID");
		self::enregistrerRequete("select",
			"SELECT ID, NOM, NBETOILES, ADRESSE, TELEPHONE FROM HOTELS WHERE ID = :ID");
	}
}

CModHotels::__static_constructor();
?>