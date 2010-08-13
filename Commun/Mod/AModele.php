<?php

abstract class AModele
{
	public $valeur = null;
	
	public $liste_valeurs = array();
	
	protected static $requetes = array();
	protected static $requetes_preparees = array();
	
	public static $attributs = array();
	
	public $problemes  = null;
	public $valeur_depuis_post = false;
	public $clefs_maj = null;
	
	public function __set($nom, $valeur)
	{
		throw new Exception("Lecture seule uniquement pour les données membres des modèles");
	}
	
	protected static function enregistrerRequete($nom, $requete)
	{
		global $CONNEXION;
		
		self::$requetes[$nom] = $requete;
	}
	
	protected function executerRequete($nom, $arguments = array())
	{
		global $CONNEXION;
		
		if (!array_key_exists($nom,self::$requetes_preparees))
		{
			$requete = $CONNEXION->prepare(self::$requetes[$nom]);
			self::$requetes_preparees[$nom] = $requete;
		}
		else
		{
			$requete = self::$requetes_preparees[$nom];
		}
		
		$requete->execute($arguments);
		
		$valeurs = $requete->fetchAll(PDO::FETCH_CLASS);
		
		if (count($valeurs)===1)
		{
			$this->valeur = $valeurs[0];
		}
		
		$this->liste_valeurs = $valeurs;
		
		return $requete;
	}
	
	public function verifierPresence()
	{
		if ($this->valeur === null)
		{
			throw new CException("Élement non trouvé");
		}
	}
	
	public function verifierNonPresence($e = null)
	{
		if ($this->valeur !== null)
		{
			if ($e === null)
			{			
				$e = new CExceptionFormulaire("Élément déjà existant");
			}
			
			foreach($this->listeAttributsClefs() as $erreur)
			{	
				$e->ajouterProbleme("Identifiant déjà utilisé",$erreur->champ);
			}
			throw $e;
		}
	}
	
	public function listeAttributsClefs()
	{
		$liste = array();
		foreach(self::$attributs as $attribut)
		{
			if ($attribut->clef_primaire)
			{
				array_push($liste, $attribut);
			}
		}
		return $liste;
	}
	
	public function listeAttributs()
	{
		return self::$attributs;
	}
	
	public function construireValeurs($valeurs_base, $attributs = null, $update=false)
	{
		if ($attributs === null)
		{
			$attributs = self::$attributs;
		}
		$valeurs = array();
		
		$exception = null;
		
		foreach ($attributs as $attribut)
		{
			$champ = $attribut->champ;
			$valeur = null;
			
			if (isset($valeurs_base[$champ]))
			{
				try
				{
					$valeur = $attribut->pourSql($valeurs_base[$champ]);
				}
				catch (CException $e)
				{
					if ($exception == null)
					{
						$exception = new CExceptionFormulaire("Erreur de validation du formulaire");
					}
					
					$exception->ajouterProbleme($e->getMessage(),$champ);
				}
			}
			
			// Si les nuls sont présents, alors qu'ils sont interdits, c'est pas bien
			if ($valeur === null && $attribut->null_possible === false && ($update || !$attribut->clef_primaire))
			{
				$nom = $attribut->nom;
				
				if ($exception == null)
				{
					$exception = new CExceptionFormulaire("Erreur de validation du formulaire");
				}
				
				$exception->ajouterProbleme("Une valeur est requise",$champ);
			}
			$valeurs[$champ] = $valeur;
		}
		
		if ($exception != null)
		{
			throw $exception;
		}
		
		return $valeurs;
	}
	
	public function titreValeur($elements = null)
	{
		$titre = '';
		
		if ($elements === null)
		{
			$elements = $this->valeur;
		}
		
		foreach (self::listeAttributsClefs() as $attribut)
		{
			$titre .= htmlspecialchars($elements->{$attribut->champ}) . " - ";
		}
		
		return substr($titre, 0, -3);
	}
	
	
	public function definirValeurDepuisPOST($post)
	{
		$valeur = new stdClass();
		foreach (self::$attributs as $attribut)
		{
			$clef = $attribut->champ;
			if (isset($post[$clef]))
			{
				$valeur->$clef = $post[$clef];
			}
			if ($attribut->clef_primaire)
			{
				$clef_maj = 'UPDATE_'.$clef;
				if (isset($post[$clef_maj]))
				{
					if ($this->clefs_maj === null)
					{
						$this->clefs_maj = new stdClass();
					}
					$this->clefs_maj->$clef = $post[$clef_maj];			
				}	
			}
		}
		
		$this->valeur = $valeur;
		$this->valeur_depuis_post = true;
	}
	
	public function specifierProblemes($problemes)
	{
		$this->problemes = $problemes;
	}
	
	public function construireClefsMaj()
	{
		$this->clefs_maj = new stdClass();
		foreach (self::listeAttributsClefs() as $attribut)
		{
			$clef = $attribut->champ;
			$this->clefs_maj->$clef = $this->valeur->$clef;
		}
	}
	
	// Fonctions de base
	public function supprimer($valeurs)
	{
		$requete = $this->executerRequete("supprimer",
					$this->construireValeurs($valeurs,
						$this->listeAttributsClefs()));
		
		if ($requete->rowCount() < 1)
		{
			throw new CException("Rien n'a été supprimé");
		}
	}

	public function insert($valeurs)
	{
		$this->executerRequete('select',
				$this->construireValeurs($valeurs,
						$this->listeAttributsClefs()));
		
		try
		{
			$valeurs = $this->construireValeurs($valeurs);
		}
		catch (Exception $e)
		{
			$this->verifierNonPresence($e);
		}
		
		$this->verifierNonPresence();
		
		$this->executerRequete('insert', $valeurs);
	}

	public function lister($arguments = array())
	{
		$this->executerRequete('lister', $arguments);
	}

	public function update($valeurs)
	{
		$dico_update = $this->construireValeurs($valeurs,null,true);
		$dico_select = array();
		
		$changement_clefs = false;
		
		foreach ($this->listeAttributsClefs() as $attribut)
		{
			$clef_select = $attribut->champ;
			$clef_update = 'UPDATE_'.$clef_select;
			
			$valeur_update = $attribut->pourSql($valeurs[$clef_update]);
			$valeur_select = $attribut->pourSql($valeurs[$clef_select]);
			
			$dico_select[$clef_select] = $valeur_select;
			$dico_update[$clef_update] = $valeur_update;
			
			if (!$changement_clefs && $valeur_select !== $valeur_update)
			{
				$changement_clefs = true;
			}
		}
	
		if ($changement_clefs)
		{
			$this->executerRequete('select',$dico_select);
			$this->verifierNonPresence();
		}
		
		$requete = $this->executerRequete('update', $dico_update);
				
		if ($requete->rowCount() < 1)
		{
			throw new CException("Rien n'a été mis à jour");
		}
	}

	public function select($valeurs)
	{
		$this->executerRequete('select',
				$this->construireValeurs($valeurs,
						$this->listeAttributsClefs()));
		
		$this->verifierPresence();
	}
	
}
?>