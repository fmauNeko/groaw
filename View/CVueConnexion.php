<?php
class CVueConnexion extends AVueModele
{
	
	public function afficherFormulaire()
	{
		
		$modele = $this->modele;

echo <<< EOD
<form action="?EX=connexion" name="connexion" method="post">
	<p>
		<label for="input_mail">Mail</label>
		<input name="mail" id="input_mail" type="email" autofocus required/>
	</p>
	<p>
		<label for="input_mdp">Mot de passe</label>
		<input name="mdp" id="input_mdp" type="password" required/>
	</p>
	<p>
		<input type="submit" value="Connexion" />
	</p>
</form>
EOD;

	}	
}
?>
