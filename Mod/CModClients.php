<?php
class CModClients extends AModele
{
	public static function __static_constructor()
	{
		self::$attributs = array(
			new CAttId('Identifiant', 'ID', True, False),
			new CAttString('Nom', 'NOM', False, True),
			new CAttString('Téléphone', 'TELEPHONE', False, True),
			new CAttText('Adresse', 'ADRESSE', False, True)
		);

		self::enregistrerRequete("supprimer",
			"DELETE FROM CLIENTS WHERE ID = :ID");
		self::enregistrerRequete("insert",
			"INSERT INTO CLIENTS (ID, NOM, TELEPHONE, ADRESSE) VALUES (:ID, :NOM, :TELEPHONE, :ADRESSE)");
		self::enregistrerRequete("lister",
			"SELECT ID, NOM, TELEPHONE, ADRESSE FROM CLIENTS");
		self::enregistrerRequete("update",
			"UPDATE CLIENTS SET ID = :ID, NOM = :NOM, TELEPHONE = :TELEPHONE, ADRESSE = :ADRESSE WHERE ID = :UPDATE_ID");
		self::enregistrerRequete("select",
			"SELECT ID, NOM, TELEPHONE, ADRESSE FROM CLIENTS WHERE ID = :ID");
	}
}

CModClients::__static_constructor();
?>