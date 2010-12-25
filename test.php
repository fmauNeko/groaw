<?php
$html = file_get_contents("page.html");

// Si il y a une balise body, on ne récupère que son contenu
if (preg_match('/<\w*body.*?>(.+)<\\/\w*body\w*>/s', $html, $p_html))
{
	$html = $p_html[1];
}

$taille_html = strlen($html);

$new_html = '';
$buffer = '';
$etat_courant = 0;
$etat_attribut = '';

$balises_autorisees = array('a', 'abbr', 'acronym', 'address', 'area', 'b', 'basefont', 'bdo', 'big', 'blockquote', 'br', 'caption', 'center', 'cite', 'code', 'col', 'colgroup', 'dd', 'del', 'dfn', 'dir', 'div', 'dl', 'dt', 'em', 'fieldset', 'font', 'h1', 'h2', 'h3', 'h4', 'h5', 'h6', 'hr', 'i', 'ins', 'label', 'legend', 'li', 'map', 'menu', 'nobr', 'ol', 'p', 'pre', 'q', 's', 'samp', 'small', 'span', 'strike', 'strong', 'sub', 'sup', 'table', 'tbody', 'td', 'tfoot', 'th', 'thead', 'tr', 'tt', 'u', 'ul', 'var', 'wbr', 'img');

$attributs_autorisees = array('name', 'class', 'title', 'alt', 'width', 'height', 'align', 'nowrap', 'col', 'row', 'id', 'rowspan', 'colspan', 'cellspacing', 'cellpadding', 'valign', 'bgcolor', 'color', 'border', 'bordercolorlight', 'bordercolordark', 'face', 'marginwidth', 'marginheight', 'axis', 'border', 'abbr', 'char', 'charoff', 'clear', 'compact', 'coords', 'vspace', 'hspace', 'cellborder', 'size', 'lang', 'dir');

$attributs_dangereux = array('canard','style', 'src', 'href');

define('ETAT_A_BASE', 0);
define('ETAT_A_NOEUD_A', 1);
define('ETAT_A_NOEUD_B', 2);
define('ETAT_A_ESPACE_NOEUD', 3);
define('ETAT_A_ATTRIBUT_A', 4);
define('ETAT_A_ATTRIBUT_B', 5);
define('ETAT_A_ATTRIBUT_C', 6);
define('ETAT_A_ATTRIBUT_D', 7);

// Les numéros des états et l'ordre dans le tableau sont liés
$attributs_dangereux = array('canard','style', 'src', 'href');
define('ETAT_B_CANARD', 0);
define('ETAT_B_STYLE', 1);
define('ETAT_B_SRC', 2);
define('ETAT_B_HREF', 3);

function etat_base($lettre)
{
	global $etat_courant, $buffer, $new_html;

	switch ($lettre)
	{
		case '<':
			$etat_courant = ETAT_A_NOEUD_A;
		default:
			$new_html .= $lettre;
	}
}

// Avant d'avoir trouvé le nom du nœud
function etat_noeud_a($lettre)
{
	global $etat_courant, $buffer, $new_html;

	switch ($lettre)
	{
		case '>':
			$etat_courant = ETAT_A_BASE;
		// Un espace entre le < et le nom du nœud
		// et les trucs à la con
		case ' ':
		case "\t":
		case "\n":
		case "\r":
		case '<':
		case '/':
			$new_html .= $lettre;
			break;
		default:
			$etat_courant = ETAT_A_NOEUD_B;
			$buffer = $lettre;
	}
}

// On est en train de trouver le nom du nœud
function etat_noeud_b($lettre)
{
	global $etat_courant, $buffer, $new_html, $balises_autorisees;

	switch ($lettre)
	{
		// Dès que le nom du nœud est terminé
		case ' ':
		case "\t":
		case "\n":
		case "\r":
			$etat_courant = ETAT_A_ESPACE_NOEUD;
			break;
		case '/':
		case '>':
			$etat_courant = ETAT_A_BASE;
			break;

		default:
			$buffer .= $lettre;
			return;
	}
	
	$buffer = strtolower($buffer);

	if (in_array($buffer, $balises_autorisees))
	{	
		$new_html .= $buffer;
	}
	else
	{
		$new_html .= 'span';
	}

	$new_html .= $lettre;
}

// On est dans un noeud, et on a rien à faire
function etat_espace_noeud($lettre)
{
	global $etat_courant, $buffer, $new_html, $balises_autorisees;

	//$new_html .= '|'.$lettre.'|';
	switch ($lettre)
	{
		// C'est la fin du noeud
		case '/':
		case '>':
			$etat_courant = ETAT_A_BASE;

		// Un simple espace
		case ' ':
		case "\t":
		case "\n":
		case "\r":
			$new_html .= $lettre;
			break;
		default:
			$buffer = $lettre;
			$etat_courant = ETAT_A_ATTRIBUT_A;
	}
}

function etat_attribut_a($lettre)
{
	global $etat_courant, $buffer, $new_html, $attributs_autorisees, $etat_attribut, $attributs_dangereux;

	switch ($lettre)
	{
		// Dès que le nom du nœud est terminé
		case ' ':
		case "\t":
		case "\n":
		case "\r":
			$etat_courant = ETAT_A_ESPACE_NOEUD;
			break;
		case '=':
			$etat_courant = ETAT_A_ATTRIBUT_B;
			break;
		case '/':
		case '>':
			$etat_courant = ETAT_A_BASE;
			break;

		default:
			$buffer .= $lettre;
			return;
	}

	$buffer = strtolower($buffer);

	if (in_array($buffer, $attributs_autorisees))
	{	
		// Pour ce con de php, false == ETAT_B_CANARD (0)
		$etat_attribut = -1;
	}
	else
	{
		$etat_attribut = array_search($buffer, $attributs_dangereux);

		if (!$etat_attribut)
		{
			$buffer = 'canard';
			$etat_attribut = ETAT_B_CANARD;
		}
	}

	$new_html .= $buffer.$lettre;
}

function etat_attribut_b($lettre)
{
	global $etat_courant, $buffer, $new_html, $attributs_autorisees;

	switch ($lettre)
	{
		case ' ':
		case "\t":
		case "\n":
		case "\r":
			break;
		case '/':
		case '>':
			$etat_courant = ETAT_A_BASE;
			break;

		case '"':
			$buffer = '';
			$etat_courant = ETAT_A_ATTRIBUT_D;
			break;

		default:
			$buffer = '';
			$etat_courant = ETAT_A_ATTRIBUT_C;
			break;
	}
	
	$new_html .= $lettre;
}

function etat_attribut_c($lettre)
{
	global $etat_courant, $buffer, $new_html, $attributs_autorisees;

	switch ($lettre)
	{
		// Dès que le nom du nœud est terminé
		case ' ':
		case "\t":
		case "\n":
		case "\r":
			$etat_courant = ETAT_A_ESPACE_NOEUD;
			break;
		case '/':
		case '>':
			$etat_courant = ETAT_A_BASE;
			break;

		default:
			$etat_courant = ETAT_A_ATTRIBUT_C;
			break;
	}
	
	$new_html .= $lettre;
}

function etat_attribut_d($lettre)
{
	global $etat_courant, $buffer, $new_html, $attributs_autorisees, $etat_attribut;

	switch ($lettre)
	{
		// Dès que le nom du nœud est terminé
		case '"':
			switch ($etat_attribut)
			{
				// Si l'attribut est non valide, que faire de sa valeur ?
				case ETAT_B_CANARD:
					// On la remplace par un canard
					$buffer = 'canard';
					break;
				case ETAT_B_STYLE:
					$buffer = 'canard';
					break;
				case ETAT_B_SRC:
					$buffer = '#';
					break;
				/*case ETAT_B_HREF:
					$buffer = '#';
					break;*/
			}

			$new_html .= $buffer.'"';
			$etat_courant = ETAT_A_ESPACE_NOEUD;
			return;
		// Je préfère :-)
		case '>':
			$lettre = "&gt;";
			break;
		case '<':
			$lettre = "&lt;";
			break;
	}
	
	$buffer .= $lettre;
}

ob_start();

for ($i = 0; $i < $taille_html; $i++)
{
	$lettre = $html[$i];

	// Un switch c'est moche, mais il n'y a pas de pointeurs de fonctions en php
	switch ($etat_courant)
	{
	case ETAT_A_BASE:
		etat_base($lettre);
		break;
	case ETAT_A_NOEUD_A:
		etat_noeud_a($lettre);
		break;
	case ETAT_A_NOEUD_B:
		etat_noeud_b($lettre);
		break;
	case ETAT_A_ESPACE_NOEUD:
		etat_espace_noeud($lettre);
		break;
	case ETAT_A_ATTRIBUT_A:
		etat_attribut_a($lettre);
		break;
	case ETAT_A_ATTRIBUT_B:
		etat_attribut_b($lettre);
		break;
	case ETAT_A_ATTRIBUT_C:
		etat_attribut_c($lettre);
		break;
	case ETAT_A_ATTRIBUT_D:
		etat_attribut_d($lettre);
		break;
	}
}
echo "canard\n";

ob_end_flush();

?>
