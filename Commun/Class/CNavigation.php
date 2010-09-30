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
						
					echo '<a href="?EX=',$action,'" title="',htmlspecialchars($infos[1]),'">',$infos[0],'</a>';
					
					if ($strong)
						echo '</strong>';
					
					echo "</li>\n";
					
				}
				echo "\t</ul>\n\t</li>\n";
			}
			else
			{
				echo "\t<li><h4><a href=\"",$clef,'.php">',$nom,"</a></h4></li>\n";
			}
		}
		echo "</ul>\n";
	}
}
?>
