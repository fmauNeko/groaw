<?php

define('CNETTOYEURHTML_ETAT_A_BASE', 0);
define('CNETTOYEURHTML_ETAT_A_DEBUT_NOEUD', 1);
define('CNETTOYEURHTML_ETAT_A_NOM_NOEUD', 2);
define('CNETTOYEURHTML_ETAT_A_ESPACE_NOEUD', 3);
define('CNETTOYEURHTML_ETAT_A_DEBUT_ATTRIBUT', 4);
define('CNETTOYEURHTML_ETAT_A_MILIEU_ATTRIBUT', 5);
define('CNETTOYEURHTML_ETAT_A_SANSQUOTE_ATTRIBUT', 6);
define('CNETTOYEURHTML_ETAT_A_QUOTE_ATTRIBUT', 7);

define('CNETTOYEURHTML_ETAT_B_CANARD', 0);
define('CNETTOYEURHTML_ETAT_B_STYLE', 1);
define('CNETTOYEURHTML_ETAT_B_SRC', 2);
define('CNETTOYEURHTML_ETAT_B_HREF', 3);

class CNettoyeurHtml
{
	// Code html à nettoyer
	public $html;

	// Buffer utilisé entre les différentes méthodes
	protected $buffer = null;

	// C'est une machine à deux états
	protected $etat_a = CNETTOYEURHTML_ETAT_A_BASE;
	protected $etat_b = -1;

	// Liste des balises autorisées
	public $balises_autorisees = array('a', 'abbr', 'acronym', 'address', 'area', 'b', 'basefont', 'bdo', 'big', 'blockquote', 'br', 'caption', 'center', 'cite', 'code', 'col', 'colgroup', 'dd', 'del', 'dfn', 'dir', 'div', 'dl', 'dt', 'em', 'fieldset', 'font', 'h1', 'h2', 'h3', 'h4', 'h5', 'h6', 'hr', 'i', 'ins', 'label', 'legend', 'li', 'map', 'menu', 'nobr', 'ol', 'p', 'pre', 'q', 's', 'samp', 'small', 'span', 'strike', 'strong', 'sub', 'sup', 'table', 'tbody', 'td', 'tfoot', 'th', 'thead', 'tr', 'tt', 'u', 'ul', 'var', 'wbr', 'img');

	// Liste des attributs autorisés
	public $attributs_autorisees = array('name', 'class', 'title', 'alt', 'width', 'height', 'align', 'nowrap', 'col', 'row', 'id', 'rowspan', 'colspan', 'cellspacing', 'cellpadding', 'valign', 'bgcolor', 'color', 'border', 'bordercolorlight', 'bordercolordark', 'face', 'marginwidth', 'marginheight', 'axis', 'border', 'abbr', 'char', 'charoff', 'clear', 'compact', 'coords', 'vspace', 'hspace', 'cellborder', 'size', 'lang', 'dir');
	

	// Attributs dangereux	
	// Les numéros des états et l'ordre dans le tableau sont liés
	protected $attributs_dangereux = array('canard','style', 'src', 'href');

	public function __construct($html)
	{
		$this->html = $html;
	}

	public function nettoyerEtAfficher()
	{
		// Si il y a une balise body, on ne récupère que son contenu
		if (preg_match('/<\w*body.*?>(.+)<\\/\w*body\w*>/s', $this->html, $p_html))
		{
			$this->html = $p_html[1];
		}

		$taille_html = strlen($this->html);
		
		for ($i = 0; $i < $taille_html; $i++)
		{
			$lettre = $this->html[$i];

			// Un switch c'est moche, mais il n'y a pas de pointeurs de fonctions en php
			switch ($this->etat_a)
			{
			case CNETTOYEURHTML_ETAT_A_BASE:
				$this->etat_base($lettre);
				break;
			case CNETTOYEURHTML_ETAT_A_DEBUT_NOEUD:
				$this->etat_debut_noeud($lettre);
				break;
			case CNETTOYEURHTML_ETAT_A_NOM_NOEUD:
				$this->etat_nom_noeud($lettre);
				break;
			case CNETTOYEURHTML_ETAT_A_ESPACE_NOEUD:
				$this->etat_espace_noeud($lettre);
				break;
			case CNETTOYEURHTML_ETAT_A_DEBUT_ATTRIBUT:
				$this->etat_debut_attribut($lettre);
				break;
			case CNETTOYEURHTML_ETAT_A_MILIEU_ATTRIBUT:
				$this->etat_milieu_attribut($lettre);
				break;
			case CNETTOYEURHTML_ETAT_A_SANSQUOTE_ATTRIBUT:
				$this->etat_sansquote_attribut($lettre);
				break;
			case CNETTOYEURHTML_ETAT_A_QUOTE_ATTRIBUT:
				$this->etat_quote_attribut($lettre);
				break;
			}
		}
	}

	public function recupererHtmlNettoye()
	{
		ob_start();
		$this->nettoyerEtAfficher();
		$html = ob_get_contents();
		ob_end_clean();
		return $html;
	}

	// Les différents états
	
	// État de base, contenu en dehors des nœuds
	protected function etat_base($lettre)
	{
		switch ($lettre)
		{
			// Si un nœud commence, il faut changer d'état
			case '<':
				$this->etat_a = CNETTOYEURHTML_ETAT_A_DEBUT_NOEUD;
			default:
				echo $lettre;
		}
	}

	// Avant d'avoir trouvé le nom du nœud
	protected function etat_debut_noeud($lettre)
	{
		switch ($lettre)
		{
			// Si c'est déjà la fin du noeud, inutile d'aller plus loin
			case '>':
				$this->etat_a = CNETTOYEURHTML_ETAT_A_BASE;
			// Un espace entre le < et le nom du nœud
			// et les trucs à la con, ça fait rester sur le même état
			case ' ':
			case "\t":
			case "\n":
			case "\r":
			case '<':
			case '/':
				echo $lettre;
				break;
			
			// Si on rencontre un autre type de caractères, c'est que l'on doit
			// traiter le nom du noeud
			default:
				$this->etat_a = CNETTOYEURHTML_ETAT_A_NOM_NOEUD;
				// Remplissage du buffer pour garder la première lettre du nom
				$this->buffer = $lettre;
		}
	}

	// On est en train de trouver le nom du nœud
	protected function etat_nom_noeud($lettre)
	{
		switch ($lettre)
		{
			// Dès que le nom du nœud est terminé
			// il faut gérer la suite du noeud, et le traiter
			case ' ':
			case "\t":
			case "\n":
			case "\r":
				$this->etat_a = CNETTOYEURHTML_ETAT_A_ESPACE_NOEUD;
				break;
			// Si le nœeud s'arrête là, il faut le traiter
			case '/':
			case '>':
				$this->etat_a = CNETTOYEURHTML_ETAT_A_BASE;
				break;

			// Sinon, on ajoute seulement la lettre au buffer du nom
			default:
				$this->buffer .= $lettre;
				return;
		}

		// Le nom du noeud doit être en minuscules	
		$buffer = strtolower($this->buffer);

		if (in_array($buffer, $this->balises_autorisees))
		{	
			echo $buffer; 
		}
		else
		{
			// Si la balise n'est pas autorisée, c'est une balise canard
			echo 'canard';
		}

		echo $lettre;
	}

	// On est dans un noeud, après avoir trouvé le nom du noeud
	protected function etat_espace_noeud($lettre)
	{
		switch ($lettre)
		{
			// C'est la fin du noeud
			case '/':
			case '>':
				$this->etat_a = CNETTOYEURHTML_ETAT_A_BASE;

			// Un simple espace
			case ' ':
			case "\t":
			case "\n":
			case "\r":
				echo $lettre;
				break;
			// Si c'est une autre lettre, on est tombé sur un attribut
			default:
				$this->buffer = $lettre;
				$this->etat_a = CNETTOYEURHTML_ETAT_A_DEBUT_ATTRIBUT;
		}
	}

	// Récupération du nom de l'attribut
	protected function etat_debut_attribut($lettre)
	{
		switch ($lettre)
		{
			// Dès que le nom du nœud est terminé
			case ' ':
			case "\t":
			case "\n":
			case "\r":
				$this->etat_a = CNETTOYEURHTML_ETAT_A_ESPACE_NOEUD;
				break;
			// Si on a un égal, il faut traiter la suite du noeud
			case '=':
				$this->etat_a = CNETTOYEURHTML_ETAT_A_MILIEU_ATTRIBUT;
				break;
			case '/':
			case '>':
				$this->etat_a = CNETTOYEURHTML_ETAT_A_BASE;
				break;

			// Sinon, récupération du nom de l'attribut
			default:
				$this->buffer .= $lettre;
				return;
		}

		// Si on est là, c'est que c'est la fin du nom de l'attribut
		
		// L'attribut est en miniscules
		$buffer = strtolower($this->buffer);

		if (in_array($buffer, $this->attributs_autorisees))
		{	
			// Pour ce con de php, false == ETAT_B_CANARD (0)
			$this->etat_b = -1;
		}
		else
		{
			$this->etat_b = array_search($buffer, $this->attributs_dangereux);

			// Si l'attribut n'est pas autorisé, et qu'il n'est pas dangereux,
			// c'est un canard (ignoré)
			if (!$this->etat_b)
			{
				$buffer = 'canard';
				$this->etat_b = CNETTOYEURHTML_ETAT_B_CANARD;
			}
		}

		echo $buffer, $lettre;
	}

	// Après le = d'un attribut
	protected function etat_milieu_attribut($lettre)
	{
		switch ($lettre)
		{
			// Si l'on a un espace, on s'en fiche
			case ' ':
			case "\t":
			case "\n":
			case "\r":
				break;
			// Si c'est la fin de la balise, retour à l'état normal
			case '/':
			case '>':
				$this->etat_a = CNETTOYEURHTML_ETAT_A_BASE;
				break;

			// Si on une quote, il faut continuer jusqu'à trouver une autre quote
			case '"':
				$this->buffer = '';
				$this->etat_a = CNETTOYEURHTML_ETAT_A_QUOTE_ATTRIBUT;
				break;

			// Si on a une lettre, c'est que la valeur de l'attribut es
			// sans quotes  
			default:
				$this->buffer = $lettre;
				$this->etat_a = ETAT_A_ATTRIBUT_C;
				return;
		}

		echo $lettre;	
	}

	// Gestion d'un attribut sans quote
	protected function etat_sansquote_attribut($lettre)
	{
		switch ($lettre)
		{
			// Dès que le nom du nœud est terminé
			case ' ':
			case "\t":
			case "\n":
			case "\r":
				$etat_a = CNETTOYEURHTML_ETAT_A_ESPACE_NOEUD;
				break;
			case '/':
			case '>':
				$this->etat_a = CNETTOYEURHTML_ETAT_A_BASE;
				break;

			// Si c'est autre chose, on est encore dans l'attribut
			default:
				$this->buffer .= $lettre;
				return;
		}

		// Gestion du buffer
		$this->etat_b();

		echo $buffer, $lettre;
	}

	// Gestion d'un attribut avec quotes
	protected function etat_quote_attribut($lettre)
	{
		switch ($lettre)
		{
			// Dès que le nom du nœud est terminé
			case '"':
				$this->etat_b();
				echo $this->buffer, '"'; 

				$this->etat_a = CNETTOYEURHTML_ETAT_A_ESPACE_NOEUD;
				return;

			// Si le truc est mal formé, tant pis
			case '>':
				$lettre = "&gt;";
				break;
			case '<':
				$lettre = "&lt;";
				break;
		}
		
		$this->buffer .= $lettre;
	}

	// Gestion du contenu de l'attribut
	// fonction commune à la gestion avec quotes, et la gestion sans quotes
	protected function etat_b()
	{
		switch ($this->etat_b)
		{
			// Si l'attribut est non valide, que faire de sa valeur ?
			case CNETTOYEURHTML_ETAT_B_CANARD:
				// On la remplace par un canard
				$this->buffer = 'canard';
				break;
			case CNETTOYEURHTML_ETAT_B_STYLE:
				$this->buffer = 'color:red;background:red;';
				break;
			case CNETTOYEURHTML_ETAT_B_SRC:
				$this->buffer = '#';
				break;
			/*case CNETTOYEURHTML_ETAT_B_HREF:
				$this->buffer = '#';
				break;*/
		}
	}

}

$html = file_get_contents("page.html");

$lapin = new CNettoyeurHtml($html);

$t_a = microtime();
for ($i = 0; $i < 30; $i++)
{
	$lapin->recupererHtmlNettoye();
}

echo microtime()-$t_a;
?>
