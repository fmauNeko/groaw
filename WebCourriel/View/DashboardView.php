<?php

class DashboardView {
	public static function showTools() {

		echo <<<END
<div class="mainview tools">
<ul>
END;

		echo "\t<li><a href=\"", CNavigation::generateMergedUrl('Dashboard', 'allread'), '">',
			 _('Mark all as read'), "</li>\n";

		echo "\t<li><a href=\"", CNavigation::generateUrlToApp('Dashboard', 'allread'),'">',
			 _('Mark all as read and go to next boxe with unread message'), "</li>\n";

		echo <<<END
</ul>
</div>
END;
	}
}

?>
