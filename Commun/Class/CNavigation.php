<?php
class CNavigation
{
	public static function titre()
	{
		global $ACTIONS, $FONCTION_CTRL, $TITRE_PAGE;
		
		if (isset($TITRE_PAGE))
		{
			return $TITRE_PAGE;
		}
		
		return $ACTIONS[$FONCTION_CTRL][1];
	}
	
	public static function nommer($titre)
	{
		global $TITRE_PAGE;
		$TITRE_PAGE = $titre;
	}
	
	public static function afficher()
	{
		global $LISTE_CTRLS, $NOM_CTRL, $ACTIONS, $FONCTION_CTRL;
		
		echo "<ul>\n";

		$boite = (isset($GLOBALS['boite'])) ? rawurlencode($GLOBALS['boite']): 'INBOX';

		foreach ($LISTE_CTRLS as $clef => $nom)
		{
			if ($clef === $NOM_CTRL)
			{
				echo "\t<li><h4><strong>$nom</strong></h4>\n\t<ul>\n";
	
				foreach ($ACTIONS as $action => $infos)
				{
					echo "\t\t<li>";
					$strong = false;
					
					if ($action === $FONCTION_CTRL)
					{
						$strong = true;
						echo '<strong>';
					}

					echo '<a href="?EX=',$action,'&amp;boite=',$boite,'" title="',htmlspecialchars($infos[1]),'">',$infos[0],'</a>';
					
					if ($strong)
						echo '</strong>';
					
					echo "</li>\n";
					
				}
				echo "\t</ul>\n\t</li>\n";
			}
			else
			{
				echo "\t<li><h4><a href=\"",$clef,'.php?boite=',$boite,'">',$nom,"</a></h4></li>\n";
			}
		}
		echo "</ul>\n";
	}

	public static function gestionNomBoite()
	{
		global $NOM_BOITE, $boite;

		switch ($boite)
		{
			case 'INBOX':
				$NOM_BOITE = 'livraison';
				break;
			case 'INBOX.Interesting':
				$NOM_BOITE = 'interessant';
				break;
			case 'INBOX.Normal':
				$NOM_BOITE = 'normal';
				break;
			case 'INBOX.Unexciting':
				$NOM_BOITE = 'ininteressant';
				break;
			case 'INBOX.Trash':
				$NOM_BOITE = 'poubelle';
				break;
			default:
				$NOM_BOITE = 'archives';
		}
	}

	# Pageur fait maison car celui de pear est vilain (très vilain)
	public static function pagination($nb_elements = 0, $page = 0, $nb_par_page = 12, $sauts = 3)
	{
		$directions = array();
		
		$nb_pages = ceil($nb_elements / $nb_par_page);

		if ($nb_pages <= 1)
		{
			return false;
		}

		if ($page >= $nb_pages)
		{
			$page = 0;
		}

		if ($page === 0)
		{
			$directions['precedent'] = false;
		} else {
			$directions['precedent'] = $page - 1 ;
		}

		if ($page < $nb_pages-1)
		{
			$directions['suivant'] = $page + 1;
		} else {
			$directions['suivant'] = false;
		}

		$pages	= array();

		$fin	= min($sauts, $page - $sauts + 1);
		$fin	= ($fin < 0) ? 0 : $fin;		
		for ($i = 0; $i < $fin; $i++) {
			$pages[] = $i;	
		}

		$debut	= max($fin,	$page - $sauts + 1);
		$fin	= min($nb_pages,	$page + $sauts);

		for ($i = $debut; $i < $fin; $i++) {
			$pages[] = $i;	
		}


		$debut = max($fin + 1,	$nb_pages - $sauts + 1);
		 
		for ($i = $debut; $i < $nb_pages; $i++) {
			$pages[] = $i;	
		}
		
		return array("pages"		=> $pages,
					 "directions"	=> $directions
					);
	}
}
?>
