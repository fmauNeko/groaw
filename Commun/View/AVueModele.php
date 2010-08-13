<?php

abstract class AVueModele
{
	protected $modele;
	
	public function __construct($modele)
	{
		$this->modele = $modele;
	}
	
	public function creerLien($objet)
	{
		$lien = '';
		foreach ($this->modele->listeAttributsClefs() as $attribut)
		{
			$valeur = $objet->{$attribut->champ};
			if (isset($valeur))
			{
				$lien .= '&amp;'.$attribut->champ.'='.htmlspecialchars($valeur);
			}
		}
		
		return $lien;
	}
	
	public function afficherLigne($ligne)
	{
		// Création du lien
		$lien = $this->creerLien($ligne);
		
		foreach ($this->modele->listeAttributs() as $attribut)
		{
			$valeur = $ligne->{$attribut->champ};
			echo "\t\t<td class=\"",$attribut::$type,"_vue\" >";
			
			if ($attribut->clef_primaire)
			{
				echo "<a href=\"?EX=fiche$lien\" title=\"Voir la fiche\">",$attribut->affichageListeHtml($valeur),"</a>";
			}
			else
			{
				echo $attribut->affichageListeHtml($valeur);
			}
			echo "</td>\n";
		}
	}
	
	public function afficherTableau()
	{
		echo "<table class=\"vue_modele\">\n<thead>\n\t<tr>\n";
		
		foreach($this->modele->listeAttributs() as $attribut)
		{
			echo "\t\t<th>",$attribut->nom,"</th>\n";
		}
		echo "\t</tr>\n</thead>\n<tbody>\n";
		
		$boolean = true; // Sert à colorer une ligne sur deux
		foreach($this->modele->liste_valeurs as $ligne)
		{
			echo "\t<tr>\n";
			if ($boolean)
			{
				echo "\t<tr>\n";
				$boolean = false;
			}
			else
			{
				echo "\t<tr class=\"impaire\">\n";
				$boolean = true;
			}
			$this->afficherLigne($ligne);
			echo "\t</tr>\n";
		}
		echo "<tbody>\n</table>\n";
	}
	
	public function afficherFormulaire()
	{
		echo '<form action="?EX=formulaire" method="post">';
		
		$modele = $this->modele;
		
		foreach ($modele->listeAttributs() as $value)
		{
			$grand = $value->champ;
			$nom = $value->nom;
			
			$message = null;
			$class = '';
			
			if (isset($modele->problemes) && isset($modele->problemes[$grand]))
			{
				$class = ' class="erreur"';
				$message = htmlspecialchars($modele->problemes[$grand]);
			}
			
			echo "\n\t<div$class>\n\t\t<label for=\"input_$grand\">$nom</label><br/>";
			
			if ($message !==null)
			{
				echo "\n\t\t<p>$message</p>";
			}

			if (isset($modele->valeur))
			{
				$valeur = $modele->valeur->$grand;
			}
			else
			{
				$valeur = '';
			}
			echo "\n\t\t", $value->affichageFormulaireHtml($valeur),"\n\t</div>";
		}
		
		$lien = null;
		
		// Si les valeurs sont affichées pour une mise à jour
		if ($this->modele->clefs_maj)
		{
			foreach ($this->modele->clefs_maj as $clef => $valeur)
			{
				echo "\n\t<input type=\"hidden\" name=\"UPDATE_$clef\" value=\"",htmlspecialchars($valeur),'" />';
			}
			
			// Création du lien pour modifier
			$lien = $this->creerLien($this->modele->clefs_maj);
		
		}
		
		echo "\n\t<input type=\"submit\" value=\"Enregistrer\"/>\n</form>\n</div>\n<ul class=\"navigation_fiche\">\n\t";
		
		if ($lien !== null)
		{
			echo "<li><a href=\"?EX=supprimer$lien\" rev=\"foo\">Supprimer</a></li>\n\t",
					"<li><a href=\"?EX=fiche$lien\">Revenir à la fiche</a></li>\n\t";
		}
		
		echo "<li><a href=\"?EX=liste\" rev=\"foo\">Revenir à la liste</a></li>\n</ul>\n";
		
	}
	
	public function afficherFiche()
	{
		echo '<div class="fiche">';
		
		foreach ($this->modele->listeAttributs() as $attribut)
		{
			$valeur = $this->modele->valeur->{$attribut->champ};
			if (isset($valeur))
			{
				echo "\n\t<div class=\"",$attribut::$type,"_vue\">\n\t\t<h3>",
					$attribut->nom,"</h3>\n\t\t",
					$attribut->affichageHtml($valeur), "\n\t</div>";
			}
		}		
		
		// Création du lien pour modifier
		$lien = $this->creerLien($this->modele->valeur);
		
		echo "\n</div>\n<ul class=\"navigation_fiche\">\n\t<li><a href=\"?EX=formulaire$lien\">Modifier</a></li>\n\t",
				"<li><a href=\"?EX=supprimer$lien\" rev=\"foo\">Supprimer</a></li>\n\t",
				"<li><a href=\"?EX=liste\" rev=\"foo\">Revenir à la liste</a></li>\n</ul>\n";
	}
	
	public function afficherConfirmationSuppression()
	{
		echo '<form action="?EX=supprimer" method="post">';
		
		foreach ($this->modele->listeAttributsClefs() as $value)
		{
			$grand = $value->champ;
			$valeur = $this->modele->valeur->$grand;
			if (isset($valeur))
			{
				echo "\n\t<input type=\"hidden\" name=\"$grand\" value=\"",htmlspecialchars($valeur),'" />';
			}
		}
		
		echo "\n\t<input type=\"hidden\" name=\"CONFIRMER_SUPPRESSION\" />",
				"\n\n\t<input type=\"submit\" value=\"Confirmer la suppression\" />",
				"\n</form>\n";
	}
	
}

?>